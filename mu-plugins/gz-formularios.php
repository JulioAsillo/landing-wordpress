<?php
/**
 * Plugin Name:  Garantiza — ajustes de formularios
 * Description:  Correcciones de QA sobre Fluent Forms que la versión gratuita no permite configurar desde el panel: campo de teléfono con validación real y mensajes de validación en español.
 * Version:      1.0.0
 * Author:       Novaly
 *
 * Por qué está en código y no en el panel:
 *
 *  1) BUG-001. El campo de teléfono se creó como numérico. En HTML eso es
 *     <input type="number">, y ese control tiene dos defectos para un
 *     teléfono: si el visitante escribe un carácter no numérico el valor
 *     enviado es una cadena vacía (validity.badInput), de modo que el
 *     registro llega sin teléfono y sin aviso; y el atributo maxlength no
 *     se aplica, por lo que el límite de 9 cifras tampoco se cumple.
 *     Aquí el campo se emite como texto con teclado numérico, límite real
 *     de 9 cifras y validación en el servidor.
 *
 *  2) BUG-006. Los mensajes de validación por defecto de Fluent Forms
 *     están en inglés y la edición por campo no está disponible en la
 *     versión gratuita. Se traducen al vuelo, tanto en el formulario que
 *     se pinta como en la respuesta del servidor.
 *
 * No toca la base de datos: si algún día se edita el formulario desde el
 * panel, estos ajustes se siguen aplicando encima.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* =========================================================================
   1. Traducción de los mensajes por defecto
   ====================================================================== */

/**
 * Equivalencias inglés → español de los mensajes que genera el plugin.
 * Se aplican por reemplazo de texto, así que también funcionan cuando el
 * mensaje lleva el nombre del campo o una cifra detrás.
 */
function gz_ff_diccionario() {
	return apply_filters(
		'gz_ff_diccionario',
		array(
			'This field is required'                 => 'Este campo es obligatorio',
			'This field is required.'                => 'Este campo es obligatorio',
			'This value is required'                 => 'Este campo es obligatorio',
			'Please fill up this field'              => 'Completa este campo',
			'Please enter a valid email address'     => 'Ingresa un correo electrónico válido',
			'This field must contain a valid email'  => 'Ingresa un correo electrónico válido',
			'The email address is not valid'         => 'Ingresa un correo electrónico válido',
			'This field must be numeric'             => 'Este campo solo admite números',
			'The value must be numeric'              => 'Este campo solo admite números',
			'Please enter a valid number'            => 'Ingresa un número válido',
			'Please select a value'                  => 'Selecciona una opción',
			'Please select an option'                => 'Selecciona una opción',
			'This field must contain a valid URL'    => 'Ingresa una dirección web válida',
			'Maximum length is'                      => 'La longitud máxima es',
			'Minimum length is'                      => 'La longitud mínima es',
			'Maximum value is'                       => 'El valor máximo es',
			'Minimum value is'                       => 'El valor mínimo es',
			'Something went wrong'                   => 'Ocurrió un problema al enviar el formulario',
			'Something is wrong'                     => 'Ocurrió un problema al enviar el formulario',
		)
	);
}

/**
 * Traduce un texto suelto.
 *
 * @param mixed $texto Valor a traducir; se devuelve intacto si no es texto.
 * @return mixed
 */
function gz_ff_traducir( $texto ) {
	if ( ! is_string( $texto ) || '' === $texto ) {
		return $texto;
	}
	return strtr( $texto, gz_ff_diccionario() );
}

/**
 * Traduce todas las cadenas de una estructura anidada.
 *
 * @param mixed $nodo Array, objeto o texto.
 * @return mixed
 */
function gz_ff_traducir_todo( $nodo ) {
	if ( is_string( $nodo ) ) {
		return gz_ff_traducir( $nodo );
	}

	if ( is_array( $nodo ) ) {
		foreach ( $nodo as $clave => $valor ) {
			$nodo[ $clave ] = gz_ff_traducir_todo( $valor );
		}
	}

	return $nodo;
}

/* =========================================================================
   2. Campo de teléfono
   ====================================================================== */

/**
 * ¿El nombre del campo corresponde a un teléfono?
 *
 * @param string $nombre Atributo name del campo.
 * @return bool
 */
function gz_ff_es_telefono( $nombre ) {
	if ( ! is_string( $nombre ) || '' === $nombre ) {
		return false;
	}

	$nombre = strtolower( $nombre );

	foreach ( array( 'telefono', 'teléfono', 'phone', 'celular', 'movil', 'móvil' ) as $pista ) {
		if ( false !== strpos( $nombre, $pista ) ) {
			return true;
		}
	}

	return false;
}

/** Cantidad de dígitos exigida. Perú: 9. */
function gz_ff_digitos() {
	return (int) apply_filters( 'gz_ff_digitos', 9 );
}

/**
 * Recorre la definición del formulario y reescribe el campo de teléfono.
 *
 * @param mixed $nodo Definición (o parte) del formulario.
 * @return mixed
 */
function gz_ff_ajustar_campos( $nodo ) {
	if ( ! is_array( $nodo ) ) {
		return $nodo;
	}

	if ( isset( $nodo['attributes'] ) && is_array( $nodo['attributes'] )
		&& ! empty( $nodo['attributes']['name'] )
		&& gz_ff_es_telefono( $nodo['attributes']['name'] ) ) {

		$digitos = gz_ff_digitos();

		/* Texto, no number: el valor llega siempre, aunque venga sucio, y
		   así se puede validar y avisar en lugar de perderlo. */
		$nodo['attributes']['type']         = 'text';
		$nodo['attributes']['inputmode']    = 'numeric';
		$nodo['attributes']['pattern']      = '[0-9]{' . $digitos . '}';
		$nodo['attributes']['maxlength']    = $digitos;
		$nodo['attributes']['autocomplete'] = 'tel-national';

		if ( empty( $nodo['attributes']['placeholder'] ) ) {
			$nodo['attributes']['placeholder'] = str_repeat( '9', $digitos );
		}

		/* Título nativo del navegador cuando el patrón no se cumple. */
		$nodo['attributes']['title'] = sprintf(
			/* translators: %d: cantidad de dígitos. */
			__( 'Ingresa %d dígitos, sin espacios ni símbolos.', 'garantiza' ),
			$digitos
		);
	}

	foreach ( $nodo as $clave => $valor ) {
		if ( is_array( $valor ) ) {
			$nodo[ $clave ] = gz_ff_ajustar_campos( $valor );
		}
	}

	return $nodo;
}

/**
 * Aplica traducción y ajustes al formulario antes de pintarlo.
 *
 * Fluent Forms entrega los campos ya decodificados en 6.x y como JSON en
 * versiones anteriores: se contemplan los dos casos.
 *
 * @param object $formulario Formulario a renderizar.
 * @return object
 */
function gz_ff_preparar_formulario( $formulario ) {
	if ( ! is_object( $formulario ) || empty( $formulario->fields ) ) {
		return $formulario;
	}

	$campos   = $formulario->fields;
	$era_json = false;

	if ( is_string( $campos ) ) {
		$decodificado = json_decode( $campos, true );
		if ( ! is_array( $decodificado ) ) {
			return $formulario;
		}
		$campos   = $decodificado;
		$era_json = true;
	}

	if ( ! is_array( $campos ) ) {
		return $formulario;
	}

	$campos = gz_ff_ajustar_campos( $campos );
	$campos = gz_ff_traducir_todo( $campos );

	$formulario->fields = $era_json ? wp_json_encode( $campos ) : $campos;

	return $formulario;
}

add_filter( 'fluentform/rendering_form', 'gz_ff_preparar_formulario', 20 );
add_filter( 'fluentform_rendering_form', 'gz_ff_preparar_formulario', 20 ); // Fluent Forms 4.x.

/* =========================================================================
   3. Validación en el servidor
   ====================================================================== */

/**
 * Traduce los errores que devuelve el plugin y valida el teléfono.
 *
 * Se ejecuta aunque el visitante desactive JavaScript o publique
 * directamente contra el endpoint, que es donde la validación del
 * navegador no existe.
 *
 * @param array $errores  Errores por nombre de campo.
 * @param array $datos    Datos enviados.
 * @return array
 */
function gz_ff_validar( $errores, $datos = array() ) {
	$errores = is_array( $errores ) ? gz_ff_traducir_todo( $errores ) : $errores;

	if ( ! is_array( $errores ) || ! is_array( $datos ) ) {
		return $errores;
	}

	$digitos = gz_ff_digitos();

	foreach ( $datos as $nombre => $valor ) {
		if ( ! gz_ff_es_telefono( $nombre ) || ! is_scalar( $valor ) ) {
			continue;
		}

		$valor = trim( (string) $valor );

		/* Vacío se deja pasar: si el campo es obligatorio, de eso ya se
		   encarga la regla "required" del propio formulario. */
		if ( '' === $valor ) {
			continue;
		}

		if ( ! preg_match( '/^[0-9]{' . $digitos . '}$/', $valor ) ) {
			$errores[ $nombre ] = array(
				sprintf(
					/* translators: %d: cantidad de dígitos. */
					__( 'Ingresa un teléfono de %d dígitos, sin espacios ni símbolos.', 'garantiza' ),
					$digitos
				),
			);
		}
	}

	return $errores;
}

add_filter( 'fluentform/validation_errors', 'gz_ff_validar', 20, 2 );
add_filter( 'fluentform_validation_errors', 'gz_ff_validar', 20, 2 ); // Fluent Forms 4.x.
