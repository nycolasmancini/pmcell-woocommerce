<?php
/**
 * PMCELL Minimalist Theme Integration
 */
function pmcell_enqueue_minimalist_styles() {
    wp_enqueue_style(
        'pmcell-minimalist',
        get_template_directory_uri() . '/css/minimalist-pmcell.css',
        array(),
        '1.0.0',
        'all'
    );
    
    wp_enqueue_style(
        'inter-font',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap',
        array(),
        null
    );
}
add_action( 'wp_enqueue_scripts', 'pmcell_enqueue_minimalist_styles', 999 );

function pmcell_add_inline_styles() {
    echo '<style>
        button, .button, input[type=submit] { 
            box-shadow: none !important; 
            font-family: Inter, sans-serif !important;
        }
    </style>';
}
add_action( 'wp_head', 'pmcell_add_inline_styles', 100 );
?>
