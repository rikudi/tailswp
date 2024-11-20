<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

include('codemirror/codemirror.php');
include('color-palettes.php');

// Theme Setup
function tailswp_theme_setup() {
    // Add theme support
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'customize-selective-refresh-widgets' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ));

    // Register Navigation Menus
    register_nav_menus( array(
        'primary' => __( 'Primary Menu', 'tailswp' ),
        'footer'  => __( 'Footer Menu', 'tailswp' ),
    ));
}
add_action( 'after_setup_theme', 'tailswp_theme_setup' );

// Enqueue scripts and styles
function tailswp_theme_scripts() {
    $theme_version = wp_get_theme()->get('Version');
    $palette_version = get_option('theme_palette_version', '1.0');
    
    // Enqueue jQuery
    wp_enqueue_script('jquery');
    
    // Enqueue main stylesheet with palette version
    wp_enqueue_style( 
        'tailswp-theme-style', 
        get_stylesheet_uri(), 
        array(), 
        $theme_version . '.' . $palette_version 
    );
    
    // Correctly handle Elementor dependencies
    $dependencies = array('jquery');
    if (did_action('elementor/loaded')) {
        $dependencies[] = 'elementor-frontend';
    }
}
add_action( 'wp_enqueue_scripts', 'tailswp_theme_scripts', 20 );

function enqueue_palette_playground_styles() {
    if (is_page_template('assets/templates/page-palettes-playground.php')) {
        wp_enqueue_style(
            'palette-playground', 
            get_template_directory_uri() . '/assets/css/palette-playground.css',
            array(),
            wp_get_theme()->get('Version')
        );
        // Pass all palettes to JavaScript
        $palettes = get_all_theme_palettes();
        wp_localize_script('jquery', 'wpPalettes', array(
            'palettes' => $palettes
        ));
    }
}
add_action('wp_enqueue_scripts', 'enqueue_palette_playground_styles');

function enqueue_ui_showcase_styles() {
    if (is_page_template('assets/templates/page-ui-showcase.php')) {
        wp_enqueue_style(
            'ui-showcase',
            get_template_directory_uri() . '/assets/css/ui-showcase.css',
            array(),
            wp_get_theme()->get('Version')
        );
    }
}
add_action('wp_enqueue_scripts', 'enqueue_ui_showcase_styles');

/**
 * Register custom template directory
 */
function register_custom_template_directory() {
    add_filter('theme_page_templates', function($templates) {
        $custom_templates = array(
            'assets/templates/page-palettes-playground.php' => 'Palettes Playground',
            'assets/templates/page-landing-intro.php' => 'Landing Page Introduction',
            'assets/templates/page-theme-features.php' => 'Theme Features', // Ensure this line is present
        );
        
        error_log('Registered templates: ' . print_r($templates, true));
        return array_merge($templates, $custom_templates);
    });

    add_filter('template_include', function($template) {
        if (is_page()) {
            $custom_template = get_page_template_slug();
            error_log('Selected template: ' . $custom_template);
            
            if ($custom_template && strpos($custom_template, 'assets/templates/') !== false) {
                $file = get_template_directory() . '/' . $custom_template;
                error_log('Looking for template file: ' . $file);
                
                if (file_exists($file)) {
                    error_log('Template file found and loaded');
                    return $file;
                } else {
                    error_log('Template file not found');
                }
            }
            if ($custom_template && strpos($custom_template, 'templates/') !== false) {
                $file = get_template_directory() . '/' . $custom_template;
                if (file_exists($file)) {
                    return $file;
                }
            }
        }
        return $template;
    }, 99);
}
add_action('after_setup_theme', 'register_custom_template_directory');
add_action('init', 'register_custom_template_directory');

// Add this debug function
add_action('admin_notices', function() {
    if (is_admin() && isset($_GET['post'])) {
        $template = get_page_template_slug($_GET['post']);
        error_log('Current page template: ' . $template);
    }
});

// Ensure palette functions are loaded early
require_once get_template_directory() . '/color-palettes.php';

// Add proper Elementor loading
function tailswp_theme_elementor_scripts() {
    if (did_action('elementor/loaded')) {
        wp_add_inline_script('tailswp-theme-header', 'window.elementorFrontend = window.elementorFrontend || {};', 'before');
    }
}
add_action('wp_enqueue_scripts', 'tailswp_theme_elementor_scripts', 21 );

// Register widget areas
function tailswp_theme_widgets_init() {
    register_sidebar( array(
        'name'          => __( 'Sidebar', 'tailswp' ),
        'id'            => 'sidebar-1',
        'description'   => __( 'Add widgets here.', 'tailswp' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));
}
add_action( 'widgets_init', 'tailswp_theme_widgets_init' );

// Add Elementor support
function tailswp_theme_add_elementor_support() {
    // Add support for Elementor
    add_theme_support('elementor');
    
    // Add support for Elementor Pro features (if using Elementor Pro)
    add_theme_support('elementor-pro');

    // Add support for default header and footer
    add_theme_support('elementor-header-footer');
}
add_action('after_setup_theme', 'tailswp_theme_add_elementor_support');

// Add page template support
function tailswp_theme_elementor_templates() {
    update_option('elementor_disable_color_schemes', 'yes');
    update_option('elementor_disable_typography_schemes', 'yes');
    update_option('elementor_container_width', '1140');
}
add_action('after_switch_theme', 'tailswp_theme_elementor_templates');

// Add Canvas template support
function tailswp_theme_add_elementor_canvas_template($templates) {
    $templates['elementor_canvas'] = 'Elementor Canvas';
    $templates['elementor_header_footer'] = 'Elementor Full Width';
    return $templates;
}
add_filter('theme_page_templates', 'tailswp_theme_add_elementor_canvas_template');

function add_ui_showcase_template($templates) {
    $templates['assets/templates/page-ui-showcase.php'] = 'UI Components Showcase';
    return $templates;
}
add_filter('theme_page_templates', 'add_ui_showcase_template');

function set_elementor_default_width() {
    update_option('elementor_container_width', '1200');  // Default container width
    update_option('elementor_stretched_section_container', 'body');  // Stretch container
}
add_action('after_switch_theme', 'set_elementor_default_width');

// Add custom Elementor controls
function custom_elementor_init() {
    // Set default content width
    if (get_option('elementor_container_width') === '') {
        update_option('elementor_container_width', '1200');
    }
}
add_action('elementor/init', 'custom_elementor_init');

// Add this to your functions.php temporarily
add_action('wp_head', function() {
    echo "<!-- Theme Directory: " . get_template_directory() . " -->\n";
    echo "<!-- Stylesheet Directory: " . get_stylesheet_directory() . " -->\n";
});

require_once get_template_directory() . '/inc/code-playground/custom-code-editor.php';

// Add AJAX handler for palette updates
add_action('wp_ajax_update_color_palette', 'handle_update_color_palette');
function handle_update_color_palette() {
    // Verify nonce
    if (!check_ajax_referer('update_color_palette_nonce', 'nonce', false)) {
        wp_send_json_error('Invalid nonce');
    }

    // Verify user permissions
    if (!current_user_can('edit_theme_options')) {
        wp_send_json_error('Insufficient permissions');
    }

    $palette = sanitize_text_field($_POST['palette']);
    
    // Update the theme mod
    set_theme_mod('color_palette_setting', $palette);
    
    wp_send_json_success();
}

// Function to get all theme palettes
function get_all_theme_palettes() {
    global $palettes;
    
    // Ensure we have default palettes
    if (!is_array($palettes)) {
        error_log('Palettes not initialized properly');
        $palettes = array(
            'default' => array(
                'primary' => '#007bff',
                'primary-light' => '#66b2ff',
                'primary-dark' => '#0056b3',
                'secondary' => '#6c757d',
                'secondary-light' => '#a6a9ad',
                'secondary-dark' => '#494e52',
                'background' => '#ffffff',
                'text' => '#333333',
                'accent' => '#28a745',
                'accent-light' => '#71d88a',
                'accent-dark' => '#1e7e34'
            )
        );
    }
    
    // Get custom palettes
    $custom_palettes = get_option('custom_color_palettes', array());
    
    // Debug output
    error_log('Default palettes: ' . print_r($palettes, true));
    error_log('Custom palettes: ' . print_r($custom_palettes, true));
    
    // Merge with default palettes, giving custom palettes precedence
    return array_merge($palettes, $custom_palettes);
}

// Ensure palette file is loaded early
require_once get_template_directory() . '/color-palettes.php';

// Add this function to pass color variables to JavaScript
function add_theme_colors_to_js() {
    if (is_admin() && isset($_GET['page']) && $_GET['page'] === 'code-playground') {
        ?>
        <script>
        window.themeColors = {
            primary: getComputedStyle(document.documentElement).getPropertyValue('--color-primary'),
            secondary: getComputedStyle(document.documentElement).getPropertyValue('--color-secondary'),
            background: getComputedStyle(document.documentElement).getPropertyValue('--color-background'),
            text: getComputedStyle(document.documentElement).getPropertyValue('--color-text'),
            accent: getComputedStyle(document.documentElement).getPropertyValue('--color-accent')
        };
        </script>
        <?php
    }
}
add_action('admin_footer', 'add_theme_colors_to_js');

function enqueue_tailwind_styles() {
    $version = wp_get_theme()->get('Version');
    $is_dev = defined('WP_DEBUG') && WP_DEBUG;
    $version = $is_dev ? time() : $version; // Use timestamp for development
    
    // Check if we're on the code playground page
    $is_code_playground = is_admin() && isset($_GET['page']) && $_GET['page'] === 'code-playground';
    
    // Load Tailwind for frontend and code playground
    if (!is_admin() || $is_code_playground) {
        wp_enqueue_style(
            'tailwind-output',
            get_template_directory_uri() . '/assets/css/tailwind-output.css',
            array(),
            $version,
            'all'
        );
        
        // Add inline script to refresh Tailwind styles
        if ($is_code_playground) {
            wp_add_inline_script('jquery', '
                window.refreshTailwind = function() {
                    var link = document.querySelector(\'link[href*="tailwind-output.css"]\');
                    if (link) {
                        var url = new URL(link.href);
                        url.searchParams.set(\'v\', Date.now());
                        link.href = url.toString();
                    }
                };
            ');
        }
    }
}

// Remove existing actions and add with correct priorities
remove_action('wp_enqueue_scripts', 'enqueue_tailwind_styles');
remove_action('admin_enqueue_scripts', 'enqueue_tailwind_styles');

// Add with higher priority for admin pages
add_action('wp_enqueue_scripts', 'enqueue_tailwind_styles', 15);
add_action('admin_enqueue_scripts', 'enqueue_tailwind_styles', 15);

// Ensure editor page specifically loads Tailwind
add_action('load-toplevel_page_code-playground', callback: function() {
    add_action('admin_enqueue_scripts', 'enqueue_tailwind_styles', 5);
});

// Add admin color palette support
function add_admin_color_palette() {
    $current_palette = get_theme_mod('color_palette_setting', 'default');
    $palettes = get_all_theme_palettes();
    $colors = isset($palettes[$current_palette]) ? $palettes[$current_palette] : $palettes['default'];
    
    echo '<style>
        :root,
        body,
        #wpadminbar,
        #wpwrap {
            --color-primary: ' . esc_attr($colors['primary']) . ';
            --color-secondary: ' . esc_attr($colors['secondary']) . ';
            --color-background: ' . esc_attr($colors['background']) . ';
            --color-text: ' . esc_attr($colors['text']) . ';
            --color-accent: ' . esc_attr($colors['accent']) . ';
        }
        
        /* Optional: Style some admin elements with theme colors */
        .wp-core-ui .button-primary {
            background-color: var(--color-primary);
            border-color: var(--color-primary);
        }
        .wp-core-ui .button-primary:hover {
            background-color: var(--color-accent);
            border-color: var(--color-accent);
        }
        .wp-core-ui .button-secondary {
            color: var(--color-primary);
        }
    </style>';
}
add_action('admin_head', 'add_admin_color_palette');
add_action('customize_controls_print_styles', 'add_admin_color_palette');

// Update admin colors when palette changes
function update_admin_colors() {
    add_action('wp_ajax_update_color_palette', function() {
        add_admin_color_palette();
        wp_die();
    });
}
add_action('admin_init', 'update_admin_colors');

function inject_palette_colors_admin() {
    $current_palette = get_theme_mod('color_palette_setting', 'default');
    $palettes = get_all_theme_palettes();
    
    if (isset($palettes[$current_palette])) {
        $colors = $palettes[$current_palette];
        ?>
        <style id="admin-palette-css">
            :root {
                --color-primary: <?php echo esc_attr($colors['primary']); ?> !important;
                --color-secondary: <?php echo esc_attr($colors['secondary']); ?> !important;
                --color-background: <?php echo esc_attr($colors['background']); ?> !important;
                --color-text: <?php echo esc_attr($colors['text']); ?> !important;
                --color-accent: <?php echo esc_attr($colors['accent']); ?> !important;
            }
        </style>
        <?php
    }
}
add_action('admin_head', 'inject_palette_colors_admin');

function tailswp_enqueue_styles() {
    // Main theme stylesheet
    wp_enqueue_style('tailswp-style', get_stylesheet_uri(), array(), wp_get_theme()->get('Version'));
    
    // Component-specific styles
    wp_enqueue_style('tailswp-components', get_template_directory_uri() . '/assets/css/components.css', array('tailswp-style'), wp_get_theme()->get('Version'));
}

// Handle updating the active color palette
add_action('wp_ajax_update_color_palette', function() {
    check_ajax_referer('update_color_palette_nonce', 'nonce');
    
    if (!current_user_can('edit_theme_options')) {
        wp_send_json_error('Insufficient permissions');
    }
    
    $palette = sanitize_text_field($_POST['palette']);
    
    // Update the theme mod with the new palette
    set_theme_mod('color_palette_setting', $palette);
    
    // Clear any caches
    if (class_exists('\Elementor\Plugin')) {
        \Elementor\Plugin::$instance->files_manager->clear_cache();
    }
    
    wp_send_json_success([
        'message' => 'Palette updated successfully',
        'palette' => $palette
    ]);
});

// Handle saving custom colors for a palette
add_action('wp_ajax_save_custom_palette_colors', function() {
    check_ajax_referer('update_color_palette_nonce', 'nonce');
    
    if (!current_user_can('edit_theme_options')) {
        wp_send_json_error('Insufficient permissions');
    }
    
    $palette = sanitize_text_field($_POST['palette']);
    $color_name = sanitize_text_field($_POST['color_name']);
    $color_value = sanitize_hex_color($_POST['color_value']);
    
    if (!$color_value) {
        wp_send_json_error('Invalid color value');
    }
    
    // Get existing palettes
    $custom_palettes = get_option('custom_color_palettes', array());
    
    // If palette doesn't exist in custom palettes, initialize it
    if (!isset($custom_palettes[$palette])) {
        $all_palettes = get_all_theme_palettes();
        $custom_palettes[$palette] = isset($all_palettes[$palette]) ? $all_palettes[$palette] : array();
    }
    
    // Update the specific color
    $custom_palettes[$palette][$color_name] = $color_value;
    
    // Save updated palettes
    update_option('custom_color_palettes', $custom_palettes);
    
    wp_send_json_success([
        'message' => 'Color saved successfully',
        'palette' => $palette,
        'color_name' => $color_name,
        'color_value' => $color_value
    ]);
});

// Add function to reset palette data
function reset_palette_data() {
    check_ajax_referer('update_color_palette_nonce', 'nonce');
    
    if (!current_user_can('edit_theme_options')) {
        wp_send_json_error('Unauthorized');
    }

    $palette_name = sanitize_text_field($_POST['palette']);
    
    // Get custom palettes
    $custom_palettes = get_option('custom_color_palettes', array());
    
    // Remove the specific palette from custom palettes
    if (isset($custom_palettes[$palette_name])) {
        unset($custom_palettes[$palette_name]);
        update_option('custom_color_palettes', $custom_palettes);
    }
    
    // Force refresh theme mod
    $current_palette = get_theme_mod('color_palette_setting');
    if ($current_palette === $palette_name) {
        remove_theme_mod('color_palette_setting');
    }
    
    wp_send_json_success(array(
        'message' => 'Palette reset successfully'
    ));
}
add_action('wp_ajax_reset_palette_data', 'reset_palette_data');
