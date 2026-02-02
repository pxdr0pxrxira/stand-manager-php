<?php
return [
    // Navigation
    'nav' => [
        'home' => 'Inicio',
        'stock' => 'Stock',
        'contact' => 'Contacto',
        'view_cars' => 'Ver Coches',
        'admin_panel' => 'Panel Admin',
    ],

    // Home Page
    'home' => [
        'hero_title_prefix' => 'Encuentre su',
        'hero_title_highlight' => 'próximo coche',
        'hero_title_suffix' => 'con nosotros',
        'hero_subtitle' => 'Más de 20 años ofreciendo los mejores vehículos con garantía y total confianza. Visite nuestro concesionario y descubra el coche perfecto para usted.',
        'view_stock' => 'Ver Stock Completo',
        'contact_us' => 'Contáctenos',
        'stats' => [
            'cars_in_stock' => 'Coches en Stock',
            'years_experience' => 'Años de Experiencia',
            'happy_customers' => 'Clientes Satisfechos',
            'warranty_years' => 'Años de Garantía',
        ],
        'featured_title' => 'Destacados Recientes',
        'featured_subtitle' => 'Vea nuestras últimas novedades',
        'view_details' => 'Ver Detalles',
        'no_featured' => 'Ningún coche destacado en este momento.',
        'brands_title' => 'Marcas de Confianza',
        'testimonials_title' => 'Lo que dicen nuestros clientes',
        'cta_title' => '¿Listo para encontrar su coche?',
        'cta_subtitle' => 'Visítenos hoy mismo o contáctenos para programar una prueba de conducción.',
        'cta_button' => 'Hablar con Vendedor',
    ],

    // Stock Page
    'stock' => [
        'title' => 'Stock de Vehículos',
        'subtitle_singular' => 'vehículo',
        'subtitle_plural' => 'vehículos',
        'available' => 'disponible',
        'available_plural' => 'disponibles',
        'sold' => 'vendido',
        'sold_plural' => 'vendidos',
        'tab_available' => 'Disponibles',
        'tab_sold' => 'Vendidos',
        'filters' => [
            'brand' => 'Marca',
            'all_brands' => 'Todas',
            'fuel' => 'Combustible',
            'all_fuels' => 'Todos',
            'max_price' => 'Precio Máximo',
            'min_year' => 'Año Mínimo',
            'any_year' => 'Cualquiera',
            'sort' => 'Ordenar',
            'sort_recent' => 'Más Recientes',
            'sort_price_low' => 'Precio: Menor',
            'sort_price_high' => 'Precio: Mayor',
            'sort_year_new' => 'Año: Más Nuevo',
            'sort_km_low' => 'Menos Km',
            'filter_btn' => 'Filtrar',
            'clear_filters' => 'Limpiar Filtros',
        ],
        'no_results' => 'Ningún vehículo encontrado',
        'no_sold' => 'Ningún vehículo vendido',
        'try_filters' => 'Intente ajustar los filtros de búsqueda',
        'history_msg' => 'El historial de ventas aparecerá aquí',
        'back_to_stock' => 'Ver Todo el Stock',
        'sold_label' => 'VENDIDO',
        'reserved_label' => 'RESERVADO',
    ],

    // Details Page
    'details' => [
        'year' => 'Año',
        'km' => 'Kilómetros',
        'fuel' => 'Combustible',
        'power' => 'Potencia',
        'transmission' => 'Transmisión',
        'warranty' => 'Garantía',
        'warranty_included' => 'Incluida',
        'warranty_not_included' => 'No incluida',
        'description' => 'Descripción',
        'contact_whatsapp' => 'Contactar por WhatsApp',
        'call_now' => 'Llamar Ahora',
        'tech_specs' => 'Especificaciones Técnicas',
        'general_info' => 'Información General',
        'reference' => 'Referencia',
        'brand' => 'Marca',
        'model' => 'Modelo',
        'version' => 'Versión',
        'segment' => 'Segmento',
        'engine_performance' => 'Motor y Rendimiento',
        'displacement' => 'Cilindrada',
        'transmission_section' => 'Transmisión',
        'type' => 'Tipo',
        'traction' => 'Tracción',
        'configuration' => 'Configuración',
        'doors' => 'Puertas',
        'seats' => 'Plazas',
        'aesthetics' => 'Estética',
        'exterior_color' => 'Color Exterior',
        'interior_color' => 'Color Interior',
        'efficiency' => 'Eficiencia',
        'consumption' => 'Consumo Medio',
        'avg_consumption' => 'Consumo Medio',
        'emissions' => 'Emisiones CO2',
        'co2_emissions' => 'Emisiones CO2',
        'related_cars' => 'Otros', // ex: Otros BMW
        'related_subtitle' => 'Vea también estas opciones de la misma marca',
        'contact_msg_template' => '¡Hola! Estoy interesado en el %s (Ref: %s)',
        'img_count' => 'Imagen %s',
        'new' => 'Nuevo',
        'semi_new' => 'Seminuevo',
        'vat_included' => 'IVA incluido',
        'vat_excluded' => 'sin IVA',
    ],

    // Contact Page
    'contact' => [
        'title' => 'Póngase en Contacto',
        'subtitle' => 'Estamos aquí para ayudar. Envíenos un mensaje o visítenos en nuestro concesionario.',
        'form_title' => 'Envíenos un Mensaje',
        'form_subtitle' => 'Complete el formulario a continuación y nos pondremos en contacto con usted en breve.',
        'success_title' => '¡Mensaje enviado con éxito!',
        'success_msg' => 'Gracias por su contacto. Responderemos pronto.',
        'error_title' => 'Error al enviar mensaje',
        'labels' => [
            'name' => 'Nombre Completo',
            'name_placeholder' => 'Su nombre',
            'email' => 'Email',
            'email_placeholder' => 'su.email@ejemplo.com',
            'phone' => 'Teléfono (opcional)',
            'subject' => 'Asunto',
            'subject_placeholder' => '¿Cuál es el motivo de su contacto?',
            'message' => 'Mensaje',
            'message_placeholder' => 'Escriba aquí su mensaje...',
            'send_btn' => 'Enviar Mensaje',
        ],
        'info_address' => 'Dirección',
        'info_phone' => 'Teléfono',
        'info_email' => 'Email',
        'info_hours' => 'Horario',
        'location' => 'Ubicación',
        'get_directions' => 'Obtener Direcciones',
        'whatsapp_title' => '¿Prefiere WhatsApp?',
        'whatsapp_subtitle' => 'Hable con nosotros directamente',
        'whatsapp_btn' => 'Abrir Chat',
    ],

    // Footer
    'footer' => [
        'about_us' => 'Sobre Nosotros',
        'about_text' => 'Su concesionario de confianza con más de 20 años de experiencia en el mercado automotriz. Calidad y transparencia en primer lugar.',
        'quick_links' => 'Enlaces Rápidos',
        'contact_info' => 'Contactos',
        'follow_us' => 'Síganos',
        'rights_reserved' => 'Todos los derechos reservados.',
        'developed_by' => 'Desarrollado por',
    ],
    
    'fuels' => [
        'Gasolina' => 'Gasolina',
        'Diesel' => 'Diésel',
        'Híbrido' => 'Híbrido',
        'Elétrico' => 'Eléctrico',
        'GPL' => 'GLP',
    ],

    // Car Descriptions (Dynamic Content Translation)
    'cars' => [
        '1' => [
            'description' => 'Excelente estado, historial de mantenimiento en la marca, único dueño. Equipamiento completo incluyendo navegación, sensores de aparcamiento y cámara trasera.',
        ],
        '2' => [
            'description' => 'Como nuevo, aún con garantía de fábrica. Pack AMG exterior e interior, llantas 18", LED Matrix.',
        ],
        '3' => [
            'description' => 'Versión GTI con 245cv. Techo panorámico, asientos deportivos, diferencial de deslizamiento limitado.',
        ],
        '4' => [
            'description' => 'Motor 2.0 TDI 150cv. Pack S-Line, Virtual Cockpit, CarPlay/Android Auto.',
        ],
        '5' => [
            'description' => 'Autonomía de 600km. Autopilot incluido, interior premium blanco, llantas 19".',
        ],
        '6' => [
            'description' => 'Versión híbrida enchufable con 225cv. i-Cockpit 3D, Night Vision, suspensión adaptativa.',
        ],
    ],
    
    // General
    'general' => [
        'loading' => 'Cargando...',
        'error' => 'Ocurrió un error.',
        'close' => 'Cerrar',
    ]
];
?>
