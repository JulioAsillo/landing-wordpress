<?php
/**
 * Contenido de las secciones.
 *
 * NOTA SOBRE EL ALCANCE
 * Los textos aún no están validados por el cliente, así que viven aquí
 * como datos estructurados y no dispersos en las plantillas. Cuando el
 * copy quede aprobado, esta capa se sustituye por campos administrables
 * (ACF o campos nativos) sin tocar el marcado: las plantillas ya leen
 * de estas funciones y no de literales.
 *
 * Cada función pasa por un filtro para poder sobrescribirla sin editar
 * el tema.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Los tres pilares. Común a las tres propuestas.
 */
function gz_pillars() {
	return apply_filters(
		'gz_pillars',
		array(
			array(
				'title' => 'Seguridad',
				'text'  => 'Procesos legales estructurados y trazables desde la constitución hasta la venta.',
			),
			array(
				'title' => 'Eficiencia',
				'text'  => 'Un representante legal dedicado y plataforma en tiempo real reducen los tiempos del proceso.',
			),
			array(
				'title' => 'Recuperación de valor',
				'text'  => 'Incautación con mapa de calor y subasta gestionada para maximizar el precio de venta.',
			),
		)
	);
}

/**
 * Contadores principales. El valor final lo confirma el cliente;
 * 'value' es el número al que anima el contador.
 */
function gz_stats() {
	return apply_filters(
		'gz_stats',
		array(
			array( 'value' => 0, 'suffix' => '', 'label' => 'Garantías constituidas' ),
			array( 'value' => 0, 'suffix' => '', 'label' => 'Garantías ejecutadas' ),
			array( 'value' => 0, 'suffix' => '', 'label' => 'Garantías incautadas' ),
			array( 'value' => 0, 'suffix' => '', 'label' => 'Garantías vendidas' ),
		)
	);
}

/**
 * Capacidad mensual. Cifras iguales en las tres propuestas.
 */
function gz_capacity() {
	return apply_filters(
		'gz_capacity',
		array(
			array( 'value' => 800, 'label' => 'Constitución de garantías / mes' ),
			array( 'value' => 500, 'label' => 'Emisión de orden de captura / mes' ),
			array( 'value' => 400, 'label' => 'Incautación de unidades / mes' ),
			array( 'value' => 400, 'label' => 'Ventas de unidades / mes' ),
		)
	);
}

/**
 * Los cuatro módulos del ciclo. Idénticos en las tres propuestas,
 * cambia solo cómo se presentan.
 */
function gz_services() {
	return apply_filters(
		'gz_services',
		array(
			array(
				'num'   => '01',
				'title' => 'Constitución de garantías mobiliarias',
				'desc'  => 'Estructuramos el contrato, gestionamos firmas y notaría, y llevamos el registro hasta su inscripción en SUNARP.',
				'items' => array(
					'Estructuración legal del contrato',
					'Toma física de firmas de los participantes',
					'Legalización de contratos ante notaría',
					'Seguimiento de emisión de placa',
					'Elaboración del inserto de GM',
					'Registro e inscripción en SUNARP (SIGM)',
				),
			),
			array(
				'num'   => '02',
				'title' => 'Ejecución de garantías mobiliarias',
				'desc'  => 'Iniciamos la vía judicial cuando es necesario, desde la notificación al deudor hasta la orden de captura.',
				'items' => array(
					'Recopilación de información y sustentos',
					'Notificación notarial al deudor',
					'Elaboración del expediente de demanda',
					'Registro de demanda ante el Poder Judicial',
					'Seguimiento de la calificación judicial',
					'Obtención de orden de captura y notificación a la PNP',
				),
			),
			array(
				'num'   => '03',
				'title' => 'Incautación y ejecución extrajudicial',
				'desc'  => 'Ubicamos y recuperamos la unidad, con o sin GPS, con soporte directo de la PNP.',
				'items' => array(
					'Ubicación de la unidad (con y sin GPS)',
					'Recupero de la unidad con soporte de la PNP',
					'Trámite para el retiro de unidad en comisaría',
					'Entrega de la unidad recuperada',
					'Formalización notarial de la ejecución extrajudicial',
					'Soporte legal del proceso de inscripción en SUNARP',
				),
			),
			array(
				'num'   => '04',
				'title' => 'Venta de unidades recuperadas',
				'desc'  => 'Convertimos la unidad recuperada en liquidez: evaluamos, subastamos y entregamos el efectivo a la entidad.',
				'items' => array(
					'Evaluación de gravámenes',
					'Cálculo del precio de venta',
					'Programación y publicación de la subasta',
					'Gestión de contraofertas para maximizar el precio',
					'Gestión operativa con el comprador',
					'Recolección y entrega del efectivo a la entidad',
				),
			),
		)
	);
}

/**
 * Comparativa. Cinco filas enfrentadas, iguales en las tres propuestas.
 */
function gz_comparison() {
	return apply_filters(
		'gz_comparison',
		array(
			array(
				'topic' => 'Cobertura del proceso',
				'bad'   => array( 'Gestión fragmentada', 'Procesos aislados con distintas partes involucradas en cada etapa.' ),
				'good'  => array( 'Procesos integrados', 'Un representante legal dedicado gestiona todo el ciclo.' ),
			),
			array(
				'topic' => 'Visibilidad y seguimiento',
				'bad'   => array( 'Procesos manuales', 'Seguimiento manual, sin visibilidad en tiempo real.' ),
				'good'  => array( 'Uso intensivo de tecnología', 'Plataforma con seguimiento en tiempo real, de la constitución a la venta.' ),
			),
			array(
				'topic' => 'Asesoría en cada etapa',
				'bad'   => array( 'Asistencia fraccionada', 'Distintos asesores legales según cada etapa del proceso.' ),
				'good'  => array( 'Soporte legal 360°', 'Gestión integral de documentos, estudios y análisis en cada etapa.' ),
			),
			array(
				'topic' => 'Alcance de la recuperación',
				'bad'   => array( 'Recuperación aislada', 'Foco exclusivo en la unidad que garantiza el crédito.' ),
				'good'  => array( 'Recuperación adicional', 'Recuperación de unidades en paralelo con la búsqueda de bienes para embargo.' ),
			),
			array(
				'topic' => 'Unidades sin GPS',
				'bad'   => array( 'Recuperación limitada', 'La pérdida del GPS al iniciar la incautación reduce la recuperación y el valor.' ),
				'good'  => array( 'Recuperación alta sin GPS', 'Mapa de calor, puntos de ubicación e incautadores propios en Lima y provincias.' ),
			),
		)
	);
}

/**
 * Clientes. En el prototipo 1 se muestran como tiles de texto en grilla
 * de 6 columnas, no como logos. Si el cliente entrega logos con permiso
 * de uso, el tile acepta una imagen en lugar del texto.
 */
function gz_clients() {
	return apply_filters(
		'gz_clients',
		array( 'Santander', 'COFIDE', 'BanBif', 'Caja Trujillo', 'Financiera Confianza' )
	);
}

/**
 * Campos del formulario de contacto.
 *
 * PENDIENTE DE VALIDACIÓN. Esta es la estructura del prototipo 1 y sirve
 * como referencia para construir el formulario en Fluent Forms. El tema
 * no renderiza estos campos: los pinta Fluent Forms. Se dejan aquí para
 * que el formulario real se arme con los mismos nombres y no haya que
 * retocar estilos ni la lógica de notificación cuando se confirme.
 */
function gz_form_fields_reference() {
	return array(
		array( 'name' => 'empresa',  'label' => 'Empresa / entidad',        'type' => 'text',     'required' => true ),
		array( 'name' => 'tipo',     'label' => 'Tipo de entidad',          'type' => 'select',   'required' => true,
			'options' => array( 'Banco', 'Financiera', 'Caja municipal / rural', 'Otro' ) ),
		array( 'name' => 'nombre',   'label' => 'Nombre y cargo',           'type' => 'text',     'required' => true ),
		array( 'name' => 'telefono', 'label' => 'Teléfono',                 'type' => 'tel',      'required' => false ),
		array( 'name' => 'correo',   'label' => 'Correo corporativo',       'type' => 'email',    'required' => true ),
		array( 'name' => 'mensaje',  'label' => 'Cuéntanos sobre tu cartera','type' => 'textarea', 'required' => false ),
	);
}
