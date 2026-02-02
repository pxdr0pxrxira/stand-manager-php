<?php
return [
    // Navigation
    'nav' => [
        'home' => 'Home',
        'stock' => 'Stock',
        'contact' => 'Contact',
        'view_cars' => 'View Cars',
        'admin_panel' => 'Admin Panel',
    ],

    // Home Page
    'home' => [
        'hero_title_prefix' => 'Find your',
        'hero_title_highlight' => 'next car',
        'hero_title_suffix' => 'with us',
        'hero_subtitle' => 'Over 20 years offering the best vehicles with warranty and total confidence. Visit our showroom and discover the perfect car for you.',
        'view_stock' => 'View Full Stock',
        'contact_us' => 'Contact Us',
        'stats' => [
            'cars_in_stock' => 'Cars in Stock',
            'years_experience' => 'Years of Experience',
            'happy_customers' => 'Happy Customers',
            'warranty_years' => 'Years of Warranty',
        ],
        'featured_title' => 'Recent Highlights',
        'featured_subtitle' => 'Check out our latest arrivals',
        'view_details' => 'View Details',
        'no_featured' => 'No featured cars at the moment.',
        'brands_title' => 'Trusted Brands',
        'testimonials_title' => 'What our clients say',
        'cta_title' => 'Ready to find your car?',
        'cta_subtitle' => 'Visit us today or contact us to schedule a test-drive.',
        'cta_button' => 'Speak with Sales',
    ],

    // Stock Page
    'stock' => [
        'title' => 'Vehicle Stock',
        'subtitle_singular' => 'vehicle',
        'subtitle_plural' => 'vehicles',
        'available' => 'available',
        'available_plural' => 'available',
        'sold' => 'sold',
        'sold_plural' => 'sold',
        'tab_available' => 'Available',
        'tab_sold' => 'Sold',
        'filters' => [
            'brand' => 'Brand',
            'all_brands' => 'All',
            'fuel' => 'Fuel',
            'all_fuels' => 'All',
            'max_price' => 'Max Price',
            'min_year' => 'Min Year',
            'any_year' => 'Any',
            'sort' => 'Sort by',
            'sort_recent' => 'Newest Arrivals',
            'sort_price_low' => 'Price: Low to High',
            'sort_price_high' => 'Price: High to Low',
            'sort_year_new' => 'Year: Newest',
            'sort_km_low' => 'Lowest Mileage',
            'filter_btn' => 'Filter',
            'clear_filters' => 'Clear Filters',
        ],
        'no_results' => 'No vehicles found',
        'no_sold' => 'No sold vehicles',
        'try_filters' => 'Try adjusting your search filters',
        'history_msg' => 'Sales history will appear here',
        'back_to_stock' => 'Back to Stock',
        'sold_label' => 'SOLD',
        'reserved_label' => 'RESERVED',
    ],

    // Details Page
    'details' => [
        'year' => 'Year',
        'km' => 'Mileage',
        'fuel' => 'Fuel',
        'power' => 'Power',
        'transmission' => 'Transmission',
        'warranty' => 'Warranty',
        'warranty_included' => 'Included',
        'warranty_not_included' => 'Not included',
        'description' => 'Description',
        'contact_whatsapp' => 'Contact via WhatsApp',
        'call_now' => 'Call Now',
        'tech_specs' => 'Technical Specifications',
        'general_info' => 'General Information',
        'reference' => 'Reference',
        'brand' => 'Brand',
        'model' => 'Model',
        'version' => 'Version',
        'segment' => 'Segment',
        'engine_performance' => 'Engine & Performance',
        'displacement' => 'Displacement',
        'transmission_section' => 'Transmission',
        'type' => 'Type',
        'traction' => 'Drive Type',
        'configuration' => 'Configuration',
        'doors' => 'Doors',
        'seats' => 'Seats',
        'aesthetics' => 'Aesthetics',
        'exterior_color' => 'Exterior Color',
        'interior_color' => 'Interior Color',
        'efficiency' => 'Efficiency',
        'consumption' => 'Avg. Consumption',
        'avg_consumption' => 'Avg. Consumption',
        'emissions' => 'CO2 Emissions',
        'co2_emissions' => 'CO2 Emissions',
        'related_cars' => 'Other', // ex: Other BMW
        'related_subtitle' => 'See also these options from the same brand',
        'contact_msg_template' => 'Hello! I am interested in the %s (Ref: %s)',
        'img_count' => 'Image %s',
        'new' => 'New',
        'semi_new' => 'Pre-owned',
        'vat_included' => 'VAT included',
        'vat_excluded' => 'excl. VAT',
    ],

    // Contact Page
    'contact' => [
        'title' => 'Get in Touch',
        'subtitle' => 'We are here to help. Send us a message or visit our showroom.',
        'form_title' => 'Send us a Message',
        'form_subtitle' => 'Fill out the form below and we will contact you shortly.',
        'success_title' => 'Message sent successfully!',
        'success_msg' => 'Thank you for contacting us. We will respond soon.',
        'error_title' => 'Error sending message',
        'labels' => [
            'name' => 'Full Name',
            'name_placeholder' => 'Your name',
            'email' => 'Email',
            'email_placeholder' => 'your.email@example.com',
            'phone' => 'Phone (optional)',
            'subject' => 'Subject',
            'subject_placeholder' => 'Reason for contact?',
            'message' => 'Message',
            'message_placeholder' => 'Write your message here...',
            'send_btn' => 'Send Message',
        ],
        'info_address' => 'Address',
        'info_phone' => 'Phone',
        'info_email' => 'Email',
        'info_hours' => 'Opening Hours',
        'location' => 'Location',
        'get_directions' => 'Get Directions',
        'whatsapp_title' => 'Prefer WhatsApp?',
        'whatsapp_subtitle' => 'Chat with us directly',
        'whatsapp_btn' => 'Open Chat',
    ],

    // Footer
    'footer' => [
        'about_us' => 'About Us',
        'about_text' => 'Your trusted dealership with over 20 years of experience in the automotive market. Quality and transparency first.',
        'quick_links' => 'Quick Links',
        'contact_info' => 'Contacts',
        'follow_us' => 'Follow Us',
        'rights_reserved' => 'All rights reserved.',
        'developed_by' => 'Developed by',
    ],
    
    'fuels' => [
        'Gasolina' => 'Petrol',
        'Diesel' => 'Diesel',
        'Híbrido' => 'Hybrid',
        'Elétrico' => 'Electric',
        'GPL' => 'LPG',
    ],

    // Car Descriptions (Dynamic Content Translation)
    'cars' => [
        '1' => [
            'description' => 'Excellent condition, brand service history, one owner. Full equipment including navigation, parking sensors, and rear camera.',
        ],
        '2' => [
            'description' => 'Like new, still with factory warranty. AMG exterior and interior pack, 18" wheels, LED Matrix.',
        ],
        '3' => [
            'description' => 'GTI version with 245hp. Panoramic roof, sports seats, limited slip differential.',
        ],
        '4' => [
            'description' => '2.0 TDI 150hp engine. S-Line pack, Virtual Cockpit, CarPlay/Android Auto.',
        ],
        '5' => [
            'description' => '600km range. Autopilot included, premium white interior, 19" wheels.',
        ],
        '6' => [
            'description' => 'Plug-in hybrid version with 225hp. 3D i-Cockpit, Night Vision, adaptive suspension.',
        ],
    ],
    
    // General
    'general' => [
        'loading' => 'Loading...',
        'error' => 'An error occurred.',
        'close' => 'Close',
    ]
];
?>
