<?php
/**
 * Contenido de las secciones.
 *
 * ALCANCE
 * Los textos definitivos aún no llegan del cliente. Viven aquí como datos
 * estructurados y no dispersos en las plantillas: cuando el copy quede
 * aprobado se sustituyen los valores de este archivo y nada más. Cuando
 * el cliente pida editarlos desde el panel, esta capa se cambia por
 * campos administrables sin tocar el marcado, porque las plantillas ya
 * leen de estas funciones y no de literales.
 *
 * Cada función pasa por un filtro para poder sobrescribirla sin editar
 * el tema. Las cifras y textos reproducen la maqueta aprobada por diseño.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Hero.
 */
function gz_hero() {
	return apply_filters(
		'gz_hero',
		array(
			'eyebrow'     => 'Gestión integral de garantías mobiliarias',
			'title_lines' => array(
				array( 'text' => 'Maximizamos el valor de tu garantía mobiliaria,', 'accent' => false ),
				array( 'text' => 'minimizamos los tiempos del proceso.', 'accent' => true ),
			),
			'lead'        => 'Acompañamos a empresas y entidades financieras durante todo el ciclo de una garantía mobiliaria: constitución, ejecución, incautación y venta del activo, bajo un solo proceso.',
			// CTA principal retirado a pedido del cliente (revisión de textos v0.3.0).
			// Para restituirlo: array( 'label' => '…', 'url' => '#contacto' ).
			'cta_primary' => array(),
			'cta_ghost'   => array( 'label' => 'Ver cómo trabajamos', 'url' => '#servicios' ),
			'quote'       => '“Gestionamos integralmente el ciclo de una garantía mobiliaria, de inicio a fin.”',
		)
	);
}

/**
 * Los tres pilares de la franja flotante del hero.
 * 'icon' referencia una clave de gz_icon().
 */
function gz_pillars() {
	return apply_filters(
		'gz_pillars',
		array(
			array( 'icon' => 'shield', 'title' => 'Seguridad' ),
			array( 'icon' => 'bolt',   'title' => 'Eficiencia' ),
			array( 'icon' => 'chart',  'title' => 'Recuperación de valor' ),
		)
	);
}

/**
 * Números principales.
 *
 * 'value' es la cifra a la que anima el contador. La maqueta la muestra
 * con prefijo "+" y separador de miles en formato es-PE; de eso se
 * encarga el JS, aquí solo va el número.
 */
function gz_stats() {
	return apply_filters(
		'gz_stats',
		array(
			'kicker' => 'Nuestros números',
			'title'  => 'Generamos valor a tu garantía mobiliaria',
			'intro'  => 'Hemos gestionado un portafolio de <strong>+S/ 2,000 millones</strong> en más de <strong>44,000 créditos</strong> con garantía mobiliaria.',
			'items'  => array(
				array( 'value' => 44000, 'label' => 'Garantías constituidas' ),
				array( 'value' => 12000, 'label' => 'Garantías ejecutadas' ),
				array( 'value' => 11000, 'label' => 'Garantías incautadas' ),
				array( 'value' => 11000, 'label' => 'Garantías vendidas' ),
			),
		)
	);
}

/**
 * Capacidad mensual.
 *
 * 'width' es el largo de la barra en porcentaje, relativo a la mayor.
 * Se declara y no se calcula para que diseño pueda ajustar la proporción
 * visual sin que quede atada al literal de la cifra.
 */
function gz_capacity() {
	return apply_filters(
		'gz_capacity',
		array(
			'title' => 'Nuestros resultados por mes',
			'items' => array(
				array( 'label' => 'Constitución Garantías', 'value' => '+800 Unidades / Mes', 'width' => 100 ),
				array( 'label' => 'Emisión Orden Captura',  'value' => '+500 Órdenes / Mes', 'width' => 63 ),
				array( 'label' => 'Incautación Unidades',   'value' => '+400 Unidades / Mes', 'width' => 50 ),
				array( 'label' => 'Ventas de Unidades',     'value' => '+400 Unidades / Mes', 'width' => 50 ),
			),
		)
	);
}

/**
 * Los cuatro módulos del ciclo.
 */
function gz_services() {
	return apply_filters(
		'gz_services',
		array(
			'kicker' => 'Nuestros servicios',
			'title'  => 'Cuatro etapas, un solo proceso, un solo responsable',
			'lead'   => 'Acompañamos la garantía mobiliaria en cada etapa de su ciclo de vida, desde la constitución del contrato hasta la venta del activo recuperado.',
			'link'   => array( 'label' => 'Ver todos los servicios', 'url' => '#contacto' ),
			'items'  => array(
				array(
					'num'   => '01',
					'title' => 'Constitución de Garantías Mobiliarias',
					'desc'  => 'Estructuramos el contrato hasta su inscripción en el SIGM – SUNARP.',
					'items' => array(
						'Estructuración legal del contrato',
						'Registro e inscripción en SUNARP (SIGM)',
					),
				),
				array(
					'num'   => '02',
					'title' => 'Ejecución de Garantías Mobiliarias',
					'desc'  => 'Gestionamos el proceso judicial de incautación, desde la notificación al deudor hasta la emisión y registro de la orden de captura del vehículo.',
					'items' => array(
						'Recopilación de información y sustentos',
						'Notificación notarial al deudor y/o por formulario del SIGM',
						'Elaboración del expediente de demanda',
						'Registro de demanda ante el Poder Judicial',
						'Seguimiento de la calificación judicial',
						'Obtención de orden de captura y notificación a la PNP',
					),
				),
				array(
					'num'   => '03',
					'title' => 'Incautación y Ejecución Extrajudicial',
					'desc'  => 'Gestionamos la ubicación y recuperamos los vehículos con y sin GPS.',
					'items' => array(
						'Ubicación de la unidad (con y sin GPS)',
						'Trámite para el retiro de unidad en comisaría',
						'Entrega de la unidad recuperada',
						'Formalización notarial de la ejecución extrajudicial',
						'Soporte legal del proceso de inscripción en SUNARP',
					),
				),
				array(
					'num'   => '04',
					'title' => 'Venta de Unidades Recuperadas',
					'desc'  => 'Convertimos la unidad recuperada en liquidez, monitoreamos el proceso de realización o subasta de las unidades recuperadas.',
					'items' => array(
						'Evaluación de gravámenes',
						'Monitoreo en proceso de subastas vehiculares',
						'Gestión operativa con el comprador',
						'Gestión de transferencias vehiculares por ejecución de garantía mobiliaria',
						'Seguimiento del proceso hasta la inscripción de propiedad en el Registro de Propiedad Vehicular',
						'Cancelaciones o levantamientos en SUNARP o en el SIGM por ejecución de garantía mobiliaria',
						'Levantamiento de orden de incautación en el Poder Judicial',
					),
				),
			),
		)
	);
}

/**
 * Franja azul al cierre de servicios.
 */
function gz_services_strip() {
	return apply_filters(
		'gz_services_strip',
		array(
			'text'  => 'Un solo proceso, un solo responsable: así generamos ahorro en costos a lo largo de todo el ciclo de la garantía mobiliaria.',
			'items' => array(
				array( 'verb' => 'Simplificamos', 'object' => 'la gestión' ),
				array( 'verb' => 'Reducimos',     'object' => 'los tiempos' ),
				array( 'verb' => 'Incrementamos', 'object' => 'la recuperación' ),
			),
		)
	);
}

/**
 * Comparativa. Cuatro filas enfrentadas.
 * 'icon' referencia una clave de gz_icon().
 */
function gz_comparison() {
	return apply_filters(
		'gz_comparison',
		array(
			'kicker'    => 'Por qué Garantiza',
			'title'     => 'Gestión tradicional vs. gestión Garantiza',
			'lead'      => 'La fragmentación entre proveedores es lo que más tiempo y valor le cuesta a una garantía mobiliaria. Nosotros lo resolvemos con un solo equipo.',
			'col_bad'   => array( 'tag' => 'Gestión tradicional', 'title' => 'Genera pérdidas' ),
			'col_good'  => array( 'tag' => 'Gestión Garantiza', 'title' => 'Genera ganancias' ),
			'rows'      => array(
				array(
					'icon'     => 'grid',
					'feature'  => 'Gestión integral',
					'sublabel' => 'Cobertura del proceso',
					'bad'      => 'Procesos fragmentados entre distintos proveedores',
					'good'     => 'Un solo equipo gestiona todo el ciclo',
				),
				array(
					'icon'     => 'bolt',
					'feature'  => 'Proceso automatizado',
					'sublabel' => 'Visibilidad y seguimiento',
					'bad'      => 'Seguimiento manual, sin visibilidad en tiempo real',
					'good'     => 'Plataforma con seguimiento en tiempo real',
				),
				array(
					'icon'     => 'shield',
					'feature'  => 'Soporte legal 360°',
					'sublabel' => 'Asesoría en cada etapa',
					'bad'      => 'Asistencia legal fraccionada según cada etapa',
					'good'     => 'Soporte legal integral durante todo el proceso',
				),
				array(
					'icon'     => 'search',
					'feature'  => 'Recuperación sin GPS',
					'sublabel' => 'Alcance en Lima y provincias',
					'bad'      => 'Cobertura limitada, dependiente de terceros',
					'good'     => 'Incautadores propios y mapas de calor en Lima y provincias',
				),
			),
		)
	);
}

/**
 * Clientes.
 *
 * SECCIÓN RETIRADA DE LA PORTADA (v0.3.0) a pedido del cliente. Los datos
 * y la plantilla se conservan para reactivarla: agregar 'clients' en
 * $gz_sections (front-page.php) y el enlace #clientes en el menú.
 *
 * La maqueta los muestra como tiles de texto en grilla de 6 columnas,
 * no como logos. Si el cliente entrega los logos con permiso de uso,
 * cada entrada acepta 'image' con el ID del adjunto y el tile pinta la
 * imagen en lugar del texto.
 */
function gz_clients() {
	return apply_filters(
		'gz_clients',
		array(
			'kicker' => 'Nuestros clientes',
			'title'  => 'Trabajamos con entidades de primer nivel del sector financiero',
			'items'  => array(
				array( 'name' => 'Santander', 'image' => 0 ),
				array( 'name' => 'COFIDE', 'image' => 0 ),
				array( 'name' => 'BanBif', 'image' => 0 ),
				array( 'name' => 'Caja Trujillo', 'image' => 0 ),
				array( 'name' => 'Financiera Confianza', 'image' => 0 ),
				array( 'name' => '+ entidades financieras', 'image' => 0 ),
			),
		)
	);
}

/**
 * Sección de contacto: copy y tarjetas de canal.
 */
function gz_contact() {
	return apply_filters(
		'gz_contact',
		array(
			'kicker' => 'Contacto',
			'title'  => 'Solicita información',
			'lead'   => 'Cuéntanos sobre tu cartera de garantías mobiliarias y te contactamos para revisar cómo podemos gestionar el ciclo completo.',
			'items'  => array(
				array(
					'icon'       => 'phone',
					'title'      => 'Canales directos',
					'text'       => '',
					'link_label' => 'Ingresa aquí para revisar más información',
					'link_url'   => '#',
				),
			),
			'note'   => 'Cobertura a nivel nacional en todo el proceso de incautación.',
		)
	);
}

/**
 * Campos del formulario de contacto.
 *
 * El formulario real lo pinta Fluent Forms. Esta estructura es la
 * referencia con la que se arma allí, para que los nombres de campo
 * coincidan y no haya que retocar estilos ni la lógica de notificación
 * cuando se conecte. Mientras tanto alimenta la vista previa de la
 * sección, para que la maqueta se vea completa.
 */
function gz_form_fields() {
	return apply_filters(
		'gz_form_fields',
		array(
			array( 'name' => 'empresa', 'label' => 'Empresa / entidad', 'type' => 'text', 'placeholder' => 'Razón social', 'half' => true, 'required' => true ),
			array(
				'name'     => 'tipo',
				'label'    => 'Tipo de entidad',
				'type'     => 'select',
				'half'     => true,
				'required' => true,
				'options'  => array( 'Banco', 'Financiera', 'Caja municipal / rural', 'Otro' ),
			),
			array( 'name' => 'nombre', 'label' => 'Nombre y cargo', 'type' => 'text', 'placeholder' => 'Nombre completo — cargo', 'half' => true, 'required' => true ),
			array( 'name' => 'telefono', 'label' => 'Teléfono', 'type' => 'tel', 'placeholder' => '+51', 'half' => true, 'required' => false ),
			array( 'name' => 'correo', 'label' => 'Correo corporativo', 'type' => 'email', 'placeholder' => 'nombre@empresa.com', 'half' => false, 'required' => true ),
			array( 'name' => 'mensaje', 'label' => 'Cuéntanos sobre tu cartera', 'type' => 'textarea', 'placeholder' => 'Volumen aproximado, tipo de garantías, urgencia del proceso…', 'half' => false, 'required' => false ),
		)
	);
}

/**
 * Textos del formulario.
 */
function gz_form_copy() {
	return apply_filters(
		'gz_form_copy',
		array(
			'submit' => 'Enviar solicitud',
			'note'   => 'Al enviar este formulario aceptas nuestra política de privacidad.',
		)
	);
}

/**
 * Pie de página.
 *
 * Las columnas de enlaces usan los menús de WordPress cuando están
 * asignados; 'links' es el respaldo mientras no lo estén, para que la
 * maqueta se vea completa desde el primer despliegue.
 */
function gz_footer() {
	return apply_filters(
		'gz_footer',
		array(
			'about'   => 'Gestión integral de garantías mobiliarias: constitución, ejecución, incautación y venta del activo, de inicio a fin.',
			'social'  => array(
				array( 'icon' => 'linkedin', 'label' => 'LinkedIn de Garantiza', 'url' => '#' ),
			),
			'columns' => array(
				array(
					'title' => 'Empresa',
					'menu'  => 'footer',
					'links' => array(
						array( 'label' => 'Nosotros', 'url' => '#nosotros' ),
						array( 'label' => 'Servicios', 'url' => '#servicios' ),
						array( 'label' => 'Por qué Garantiza', 'url' => '#por-que-garantiza' ),
					),
				),
				array(
					'title' => 'Contacto',
					'menu'  => '',
					'links' => array(
						array( 'label' => 'WhatsApp corporativo', 'url' => '#' ),
						array( 'label' => 'contacto@garantiza.pe', 'url' => '#' ),
						array( 'label' => 'Lima, Perú', 'url' => '' ),
						array( 'label' => 'Cobertura a nivel nacional', 'url' => '' ),
					),
				),
				array(
					'title' => 'Legal',
					'menu'  => 'legal',
					'links' => array(
						array( 'label' => 'Política de privacidad', 'url' => '#' ),
						array( 'label' => 'Libro de reclamaciones', 'url' => '#' ),
						array( 'label' => 'Términos y condiciones', 'url' => '#' ),
					),
				),
			),
			'bottom'  => array(
				array( 'label' => 'Política de privacidad', 'url' => '#' ),
				array( 'label' => 'Libro de reclamaciones', 'url' => '#' ),
			),
		)
	);
}

/**
 * Enlaces del menú principal. Respaldo mientras el menú de WordPress no
 * esté asignado en Apariencia > Menús.
 */
function gz_nav_fallback_links() {
	return apply_filters(
		'gz_nav_fallback_links',
		array(
			array( 'label' => 'Nosotros', 'url' => '#nosotros' ),
			array( 'label' => 'Servicios', 'url' => '#servicios' ),
			array( 'label' => 'Por qué Garantiza', 'url' => '#por-que-garantiza' ),
			array( 'label' => 'Contacto', 'url' => '#contacto' ),
		)
	);
}
