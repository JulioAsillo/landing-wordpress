<?php
/**
 * Plugin Name: Garantiza — Retención de datos personales
 * Description: Elimina automáticamente los registros de los formularios (Fluent Forms) al cumplirse el plazo de retención.
 * Version:     1.0.0
 *
 * Plazo: variable de entorno GZ_RETENCION_ANIOS (por defecto 5 años).
 * Frecuencia: una vez al día, mediante WP-Cron.
 *
 * Al ser un "must-use plugin" no puede desactivarse desde el panel: la
 * política de retención no depende de que alguien recuerde mantenerla.
 *
 * Verificación manual, sin borrar nada:
 *     wp gz-retencion --dry-run
 * Ejecución inmediata:
 *     wp gz-retencion
 *
 * Alcance: solo la base de datos de WordPress. Las copias que llegan por
 * correo a los buzones de la empresa y los respaldos de la base de datos
 * se rigen por las políticas de quien los administra.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const GZ_RETENCION_HOOK = 'gz_retencion_diaria';
const GZ_RETENCION_LOTE = 500;

/** Plazo en años. Nunca menor a 1: un valor mal escrito no debe vaciar la tabla. */
function gz_retencion_anios() {
	$anios = (int) ( getenv( 'GZ_RETENCION_ANIOS' ) ?: 5 );
	return $anios >= 1 ? $anios : 5;
}

/** Fecha límite: todo registro anterior a ella ya cumplió el plazo. */
function gz_retencion_fecha_limite() {
	$ahora = new DateTimeImmutable( current_time( 'mysql' ) );
	return $ahora->modify( '-' . gz_retencion_anios() . ' years' )->format( 'Y-m-d H:i:s' );
}

function gz_retencion_tabla_existe( $tabla ) {
	global $wpdb;
	return $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $tabla ) ) ) === $tabla;
}

function gz_retencion_columna_existe( $tabla, $columna ) {
	global $wpdb;
	// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- nombre de tabla interno, no entrada de usuario.
	return (bool) $wpdb->get_var( $wpdb->prepare( "SHOW COLUMNS FROM `{$tabla}` LIKE %s", $columna ) );
}

/**
 * Elimina los registros vencidos y todo lo que cuelga de ellos.
 *
 * @param bool $simular true = solo cuenta, no borra.
 * @return int Registros eliminados (o que se eliminarían).
 */
function gz_retencion_purgar( $simular = false ) {
	global $wpdb;

	$envios = $wpdb->prefix . 'fluentform_submissions';
	if ( ! gz_retencion_tabla_existe( $envios ) ) {
		return 0;
	}

	$limite = gz_retencion_fecha_limite();

	if ( $simular ) {
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `{$envios}` WHERE created_at < %s", $limite ) );
	}

	// Tablas dependientes: tabla => columna que apunta al registro.
	$dependientes = array(
		$wpdb->prefix . 'fluentform_submission_meta' => 'response_id',
		$wpdb->prefix . 'fluentform_entry_details'   => 'submission_id',
	);
	$logs = $wpdb->prefix . 'fluentform_logs';

	$total = 0;
	for ( $vuelta = 0; $vuelta < 50; $vuelta++ ) {
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$ids = $wpdb->get_col( $wpdb->prepare( "SELECT id FROM `{$envios}` WHERE created_at < %s ORDER BY id LIMIT %d", $limite, GZ_RETENCION_LOTE ) );
		if ( empty( $ids ) ) {
			break;
		}
		$lista = implode( ',', array_map( 'intval', $ids ) );

		foreach ( $dependientes as $tabla => $columna ) {
			if ( gz_retencion_tabla_existe( $tabla ) && gz_retencion_columna_existe( $tabla, $columna ) ) {
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$wpdb->query( "DELETE FROM `{$tabla}` WHERE `{$columna}` IN ({$lista})" );
			}
		}
		if ( gz_retencion_tabla_existe( $logs ) && gz_retencion_columna_existe( $logs, 'source_id' ) && gz_retencion_columna_existe( $logs, 'source_type' ) ) {
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$wpdb->query( "DELETE FROM `{$logs}` WHERE source_type = 'submission_item' AND source_id IN ({$lista})" );
		}
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$total += (int) $wpdb->query( "DELETE FROM `{$envios}` WHERE id IN ({$lista})" );
	}

	update_option(
		'gz_retencion_ultima',
		array(
			'fecha'      => current_time( 'mysql' ),
			'limite'     => $limite,
			'eliminados' => $total,
		),
		false
	);

	if ( $total > 0 ) {
		error_log( sprintf( '[gz-retencion] %d registro(s) anteriores a %s eliminados.', $total, $limite ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
	}

	return $total;
}

// --- Programación diaria ---
add_action(
	'init',
	function () {
		if ( ! wp_next_scheduled( GZ_RETENCION_HOOK ) ) {
			wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', GZ_RETENCION_HOOK );
		}
	}
);
add_action( GZ_RETENCION_HOOK, 'gz_retencion_purgar' );

// --- Comando de WP-CLI ---
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::add_command(
		'gz-retencion',
		function ( $args, $assoc ) {
			$simular = isset( $assoc['dry-run'] );
			$n       = gz_retencion_purgar( $simular );
			WP_CLI::log( sprintf( 'Plazo: %d años. Fecha límite: %s', gz_retencion_anios(), gz_retencion_fecha_limite() ) );
			WP_CLI::success( sprintf( $simular ? '%d registro(s) cumplen el plazo (no se borró nada).' : '%d registro(s) eliminados.', $n ) );
			$ultima = get_option( 'gz_retencion_ultima' );
			if ( is_array( $ultima ) ) {
				WP_CLI::log( sprintf( 'Última ejecución real: %s (%d eliminados).', $ultima['fecha'], $ultima['eliminados'] ) );
			}
		}
	);
}
