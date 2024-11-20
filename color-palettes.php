<?php
/**
 * Add color palette option to the WordPress Customizer
 */
function add_color_palette_customizer($wp_customize) {
    // Add a new section for color palettes
    $wp_customize->add_section('color_palette_section', array(
        'title' => 'Color Palette',
        'priority' => 30,
    ));

    // Add the color scheme setting
    $wp_customize->add_setting('color_palette_setting', array(
        'default' => 'theme-intro',
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    // Add the control for selecting color schemes
    $wp_customize->add_control('color_palette_control', array(
        'label' => 'Select Color Scheme',
        'section' => 'color_palette_section',
        'settings' => 'color_palette_setting',
        'type' => 'select',
        'choices' => array(
            'default' => 'Default Theme',
            'dark' => 'Dark Mode',
            'light' => 'Light Mode',
            'nature' => 'Nature Theme',
            'ocean' => 'Ocean Theme',
            'theme-intro' => 'Theme Intro'
        )
    ));
}
add_action('customize_register', 'add_color_palette_customizer');

/**
 * Output the CSS for different color palettes
 */
function output_color_palette_css() {
    $current_palette = get_theme_mod('color_palette_setting', 'theme-intro');
    
    // color palettes with additional shades
    global $palettes;

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
        ),
        'dark' => array(
            'primary' => '#375fd9',
            'primary-light' => '#6a8efc',
            'primary-dark' => '#2a47a6',
            'secondary' => '#6c757d',
            'secondary-light' => '#a6a9ad',
            'secondary-dark' => '#494e52',
            'background' => '#121212',
            'text' => '#ffffff',
            'accent' => '#bb86fc',
            'accent-light' => '#d1a8ff',
            'accent-dark' => '#8e5fcf'
        ),
        'light' => array(
            'primary' => '#0066cc',
            'primary-light' => '#3399ff',
            'primary-dark' => '#004999',
            'secondary' => '#737373',
            'secondary-light' => '#a6a6a6',
            'secondary-dark' => '#4d4d4d',
            'background' => '#f8f9fa',
            'text' => '#212529',
            'accent' => '#198754',
            'accent-light' => '#4caf7a',
            'accent-dark' => '#14643a'
        ),
        'nature' => array(
            'primary' => '#2e7d32',
            'primary-light' => '#60ad5e',
            'primary-dark' => '#005005',
            'secondary' => '#81c784',
            'secondary-light' => '#b2fab4',
            'secondary-dark' => '#519657',
            'background' => '#f1f8e9',
            'text' => '#1b5e20',
            'accent' => '#ff7043',
            'accent-light' => '#ffa270',
            'accent-dark' => '#c63f17'
        ),
        'ocean' => array(
            'primary' => '#0277bd',
            'primary-light' => '#58a5f0',
            'primary-dark' => '#004c8c',
            'secondary' => '#00acc1',
            'secondary-light' => '#5ddef4',
            'secondary-dark' => '#007c91',
            'background' => '#f5f9ff',
            'text' => '#01579b',
            'accent' => '#ffa000',
            'accent-light' => '#ffd149',
            'accent-dark' => '#c67100'
        ),
        'wood' => array(
            'primary' => '#C68642', // golden-oak
            'primary-light' => '#e0a96b',
            'primary-dark' => '#8f4d1e',
            'secondary' => '#5D4037', // walnut-brown
            'secondary-light' => '#8b6b61',
            'secondary-dark' => '#321911',
            'background' => '#FFFFFF',
            'text' => '#4A1C1C', // oxblood
            'accent' => '#D2691E', // maple-syrup
            'accent-light' => '#f29b4c',
            'accent-dark' => '#9a3e0e'
        ),
        'sunset' => array(
            'primary' => '#FF7B54', // coral
            'primary-light' => '#FF9E80',
            'primary-dark' => '#E64A19',
            'secondary' => '#FFB26B', // peach
            'secondary-light' => '#FFD1A3',
            'secondary-dark' => '#FF8A50',
            'background' => '#FFF1E6',
            'text' => '#6E3630', // rust
            'accent' => '#FFD56B', // golden-hour
            'accent-light' => '#FFE082',
            'accent-dark' => '#FFC107'
        ),
        'lavender' => array(
            'primary' => '#9B72AA', // purple-rain
            'primary-light' => '#C8B1E4',
            'primary-dark' => '#6A4C93',
            'secondary' => '#C8B1E4', // light-lavender
            'secondary-light' => '#E6D8F5',
            'secondary-dark' => '#A78BBA',
            'background' => '#F8F7FF',
            'text' => '#523961', // deep-purple
            'accent' => '#E6BBD3', // rose-pink
            'accent-light' => '#F3D1E7',
            'accent-dark' => '#C48BAA'
        ),
        'midnight' => array(
            'primary' => '#2C3E50', // dark-blue
            'primary-light' => '#34495E',
            'primary-dark' => '#1A2634',
            'secondary' => '#34495E', // slate
            'secondary-light' => '#5D6D7E',
            'secondary-dark' => '#2C3E50',
            'background' => '#F3F6F9',
            'text' => '#1A2634', // charcoal
            'accent' => '#5D6D7E', // steel-blue
            'accent-light' => '#85929E',
            'accent-dark' => '#34495E'
        ),
        'desert' => array(
            'primary' => '#CB997E', // sand
            'primary-light' => '#E6B89C',
            'primary-dark' => '#A67C52',
            'secondary' => '#DDBEA9', // dusty-rose
            'secondary-light' => '#F2D7C9',
            'secondary-dark' => '#B28A72',
            'background' => '#FFF9F5',
            'text' => '#6B4423', // terra-cotta
            'accent' => '#E6B89C', // adobe
            'accent-light' => '#F2D7C9',
            'accent-dark' => '#B28A72'
        ),
        'berry' => array(
            'primary' => '#9F2B68', // raspberry
            'primary-light' => '#D77FA1',
            'primary-dark' => '#6E1C4A',
            'secondary' => '#D77FA1', // pink
            'secondary-light' => '#F2B2C4',
            'secondary-dark' => '#A64D6A',
            'background' => '#FFF0F5',
            'text' => '#4A1259', // blackberry
            'accent' => '#FF869E', // strawberry
            'accent-light' => '#FFB3C1',
            'accent-dark' => '#E63946'
        ),
        'vintage' => array(
            'primary' => '#7C9082', // sage-green
            'primary-light' => '#A5A58D',
            'primary-dark' => '#5A6B5A',
            'secondary' => '#A5A58D', // olive
            'secondary-light' => '#C8C8B1',
            'secondary-dark' => '#7C7C5A',
            'background' => '#FAFAF7',
            'text' => '#373D20', // moss
            'accent' => '#B7B7A4', // pewter
            'accent-light' => '#D1D1C1',
            'accent-dark' => '#8A8A72'
        ),
        // New palettes
        'forest' => array(
            'primary' => '#228B22', // forest-green
            'primary-light' => '#32CD32',
            'primary-dark' => '#006400',
            'secondary' => '#8FBC8F', // dark-sea-green
            'secondary-light' => '#98FB98',
            'secondary-dark' => '#2E8B57',
            'background' => '#F0FFF0',
            'text' => '#2F4F4F', // dark-slate-gray
            'accent' => '#556B2F', // dark-olive-green
            'accent-light' => '#6B8E23',
            'accent-dark' => '#3B5323'
        ),
        'sunrise' => array(
            'primary' => '#FF4500', // orange-red
            'primary-light' => '#FF6347',
            'primary-dark' => '#CD3700',
            'secondary' => '#FFD700', // gold
            'secondary-light' => '#FFFACD',
            'secondary-dark' => '#DAA520',
            'background' => '#FFF5EE',
            'text' => '#8B4513', // saddle-brown
            'accent' => '#FF8C00', // dark-orange
            'accent-light' => '#FFA500',
            'accent-dark' => '#FF7F50'
        ),
        'blossom' => array(
            'primary' => '#FF69B4', // hot-pink
            'primary-light' => '#FFB6C1',
            'primary-dark' => '#FF1493',
            'secondary' => '#DB7093', // pale-violet-red
            'secondary-light' => '#FFC0CB',
            'secondary-dark' => '#C71585',
            'background' => '#FFF0F5',
            'text' => '#8B008B', // dark-magenta
            'accent' => '#FF00FF', // magenta
            'accent-light' => '#EE82EE',
            'accent-dark' => '#DA70D6'
        ),
        'autumn' => array(
            'primary' => '#D2691E', // chocolate
            'primary-light' => '#FF7F50',
            'primary-dark' => '#8B4513',
            'secondary' => '#FF4500', // orange-red
            'secondary-light' => '#FF6347',
            'secondary-dark' => '#CD3700',
            'background' => '#FFF8DC',
            'text' => '#8B4513', // saddle-brown
            'accent' => '#FFA07A', // light-salmon
            'accent-light' => '#FA8072',
            'accent-dark' => '#E9967A'
        ),
        'tropical' => array(
            'primary' => '#00CED1', // dark-turquoise
            'primary-light' => '#40E0D0',
            'primary-dark' => '#008B8B',
            'secondary' => '#20B2AA', // light-sea-green
            'secondary-light' => '#48D1CC',
            'secondary-dark' => '#008080',
            'background' => '#E0FFFF',
            'text' => '#2F4F4F', // dark-slate-gray
            'accent' => '#00FA9A', // medium-spring-green
            'accent-light' => '#7FFFD4',
            'accent-dark' => '#00FF7F'
        ),
        'theme-intro' => array(
            'primary' => '#007bff', // bg-primary-700
            'primary-light' => '#0056b3', // hover:bg-primary-800
            'primary-dark' => '#004080', // darker shade for primary
            'secondary' => '#6c757d', // border-gray-300
            'secondary-light' => '#a6a9ad', // hover:bg-gray-100
            'secondary-dark' => '#494e52', // dark:border-gray-600
            'background' => '#111827', // dark:bg-gray-900
            'text' => '#333333', // text-gray-900
            'accent' => '#28a745', // focus:ring-primary-300
            'accent-light' => '#71d88a', // lighter shade for accent
            'accent-dark' => '#1e7e34' // dark:focus:ring-primary-900
        ),
    );

    // Get colors for current palette
    $colors = $palettes[$current_palette];
    ?>
    <style>
        :root {
            --color-primary: <?php echo $colors['primary']; ?>;
            --color-primary-light: <?php echo $colors['primary-light']; ?>;
            --color-primary-dark: <?php echo $colors['primary-dark']; ?>;
            --color-secondary: <?php echo $colors['secondary']; ?>;
            --color-secondary-light: <?php echo $colors['secondary-light']; ?>;
            --color-secondary-dark: <?php echo $colors['secondary-dark']; ?>;
            --color-background: <?php echo $colors['background']; ?>;
            --color-text: <?php echo $colors['text']; ?>;
            --color-accent: <?php echo $colors['accent']; ?>;
            --color-accent-light: <?php echo $colors['accent-light']; ?>;
            --color-accent-dark: <?php echo $colors['accent-dark']; ?>;
        }

        /* Base styles using CSS variables */
        body {
            background-color: var(--color-background);
            color: var(--color-text);
        }

        .primary-color {
            color: var(--color-primary);
        }

        .primary-bg {
            background-color: var(--color-primary);
        }

        .secondary-color {
            color: var(--color-secondary);
        }

        .secondary-bg {
            background-color: var(--color-secondary);
        }

        .accent-color {
            color: var(--color-accent);
        }

        .accent-bg {
            background-color: var(--color-accent);
        }

        /* Common element styles */
        a {
            color: var(--color-primary);
        }

        button, 
        .button {
            background-color: var(--color-primary);
            color: var(--color-background);
        }

        h1, h2, h3, h4, h5, h6 {
            color: var(--color-primary);
        }
    </style>
    <?php
}
add_action('wp_head', 'output_color_palette_css');

function output_palette_css() {
    $current_palette = get_theme_mod('color_palette_setting', 'default');
    $palettes = get_all_theme_palettes();
    
    if (isset($palettes[$current_palette])) {
        $colors = $palettes[$current_palette];
        $css = ":root {\n";
        foreach ($colors as $name => $color) {
            $css .= "    --color-{$name}: {$color};\n";
        }
        $css .= "}\n";
        
        // Add to wp_head with high priority
        add_action('wp_head', function() use ($css) {
            echo "<style id='theme-palette-css'>\n{$css}</style>\n";
        }, 1);
    }
}
add_action('init', 'output_palette_css');

// Make palettes available early
function init_theme_palettes() {
    global $palettes;
    if (!isset($GLOBALS['theme_palettes'])) {
        $GLOBALS['theme_palettes'] = $palettes;
    }
}
add_action('init', 'init_theme_palettes', 1);

// Update CSS when palette changes
function update_palette_css() {
    if (current_user_can('edit_theme_options')) {
        $cache_buster = time();
        update_option('theme_palette_version', $cache_buster);
    }
}
add_action('wp_ajax_update_color_palette', 'update_palette_css', 5);

// Add version to style.css URL
function add_palette_version_to_style($src) {
    if (strpos($src, 'style.css') !== false) {
        $version = get_option('theme_palette_version', '1.0');
        return add_query_arg('ver', $version, $src);
    }
    return $src;
}
add_filter('style_loader_src', 'add_palette_version_to_style');

function handle_palette_update() {
    check_ajax_referer('update_color_palette_nonce', 'nonce');
    
    if (!current_user_can('edit_theme_options')) {
        wp_send_json_error('Unauthorized');
    }

    $palette = sanitize_text_field($_POST['palette']);
    set_theme_mod('color_palette_setting', $palette);
    
    // Clear any caches
    if (class_exists('\Elementor\Plugin')) {
        \Elementor\Plugin::$instance->files_manager->clear_cache();
    }

    // Update timestamp for cache busting
    update_option('theme_palette_version', time());
    
    // Return the new palette colors for immediate use
    $palettes = get_all_theme_palettes();
    $colors = isset($palettes[$palette]) ? $palettes[$palette] : $palettes['default'];
    
    wp_send_json_success(array(
        'message' => 'Palette updated successfully',
        'colors' => $colors
    ));
}
add_action('wp_ajax_update_color_palette', 'handle_palette_update');

// Add new function to handle custom palette colors
function handle_custom_palette_colors() {
    // Debug logging
    error_log('Received custom palette color request');
    error_log('POST data: ' . print_r($_POST, true));

    if (!check_ajax_referer('update_color_palette_nonce', 'nonce', false)) {
        error_log('Nonce verification failed');
        wp_send_json_error('Invalid nonce');
        return;
    }
    
    if (!current_user_can('edit_theme_options')) {
        error_log('Insufficient permissions');
        wp_send_json_error('Unauthorized');
        return;
    }

    $palette_name = sanitize_text_field($_POST['palette']);
    $color_name = sanitize_text_field($_POST['color_name']);
    $color_value = sanitize_hex_color($_POST['color_value']);

    if (!$palette_name || !$color_name || !$color_value) {
        error_log('Missing required data');
        wp_send_json_error('Missing required data');
        return;
    }

    // Get existing custom palettes
    $custom_palettes = get_option('custom_color_palettes', array());
    
    // If palette doesn't exist in custom palettes, get it from global palettes
    if (!isset($custom_palettes[$palette_name])) {
        global $palettes;
        if (isset($palettes[$palette_name])) {
            $custom_palettes[$palette_name] = $palettes[$palette_name];
        } else {
            $custom_palettes[$palette_name] = array();
        }
    }
    
    // Update only the specific color while preserving others
    $custom_palettes[$palette_name][$color_name] = $color_value;
    
    // Ensure all required colors exist
    $required_colors = array(
        'primary', 'primary-light', 'primary-dark',
        'secondary', 'secondary-light', 'secondary-dark',
        'background', 'text',
        'accent', 'accent-light', 'accent-dark'
    );

    foreach ($required_colors as $color) {
        if (!isset($custom_palettes[$palette_name][$color])) {
            // Get color from original palette if available
            global $palettes;
            if (isset($palettes[$palette_name][$color])) {
                $custom_palettes[$palette_name][$color] = $palettes[$palette_name][$color];
            }
        }
    }
    
    // Save updated palettes
    update_option('custom_color_palettes', $custom_palettes);
    
    wp_send_json_success(array(
        'message' => 'Color saved successfully',
        'palette' => $palette_name,
        'color_name' => $color_name,
        'color_value' => $color_value,
        'all_colors' => $custom_palettes[$palette_name]
    ));
}

// Ensure the action is registered
remove_action('wp_ajax_save_custom_palette_colors', 'handle_custom_palette_colors');
add_action('wp_ajax_save_custom_palette_colors', 'handle_custom_palette_colors');


// Add this near the handle_custom_palette_colors function
function debug_palette_data($palette_name) {
    error_log('Checking palette: ' . $palette_name);
    $custom_palettes = get_option('custom_color_palettes', array());
    error_log('Custom palette data: ' . print_r($custom_palettes[$palette_name] ?? 'not found', true));
    global $palettes;
    error_log('Global palette data: ' . print_r($palettes[$palette_name] ?? 'not found', true));
}