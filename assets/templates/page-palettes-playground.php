<?php
error_log('Template loaded: page-palettes-playground.php');
error_log('Current user capabilities: ' . print_r(wp_get_current_user()->allcaps, true));

/**
 * Template Name: Palettes Playground
 * Template Post Type: page
 */

/* Security and function checks
if (!function_exists('get_all_theme_palettes')) {
    error_log('get_all_theme_palettes function not found');
    wp_die('Required functions not available. Please check theme setup.');
}

// Check permissions
if (!current_user_can('edit_theme_options')) {
    wp_die(__('Sorry, you do not have sufficient permissions to access this page.'));
} */

get_header(); 

// Get palette data
$current_palette = get_theme_mod('color_palette_setting', 'default');
$palettes = get_all_theme_palettes();
error_log('Available palettes: ' . print_r($palettes, true));
?>

<div class="palette-playground">
    <!-- Add notification div -->
    <div id="palette-notification" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-y-[-100%] opacity-0 transition-all duration-300 z-50"></div>
    
    <h1 class="text-4xl font-bold mb-8">Theme Color Palettes</h1>
    
    <!-- Current Active Palette Display -->
    <div class="current-palette">
        <h2 class="text-2xl font-semibold mb-4">
            Current Active Palette: <?php echo esc_html(ucfirst($current_palette)); ?>
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <?php 
            foreach ($palettes[$current_palette] as $name => $color) : 
            ?>
                <div class="p-4 rounded shadow text-center" style="background: <?php echo esc_attr($color); ?>">
                    <span style="color: <?php echo $name === 'background' ? '#000' : '#fff'; ?>">
                        <?php echo esc_html(ucfirst($name)); ?>
                    </span>
                    <div class="text-xs" style="color: <?php echo $name === 'background' ? '#000' : '#fff'; ?>">
                        <?php echo esc_html($color); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Palette Previews -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach ($palettes as $key => $colors) : ?>
            <div class="palette-card" data-palette="<?php echo esc_attr($key); ?>">
                <h3 class="text-xl mb-4 <?php echo $current_palette === $key ? 'font-bold' : ''; ?>">
                    <?php echo esc_html(ucfirst($key)); ?> Theme
                    <?php if ($current_palette === $key) : ?>
                        <span class="ml-2 text-sm text-green-600">(Active)</span>
                    <?php endif; ?>
                </h3>
                
                <!-- Color Swatches with Color Pickers -->
                <div class="grid grid-cols-2 gap-2 mb-4">
                    <?php 
                    foreach ($colors as $name => $color) : 
                    ?>
                        <div class="color-swatch" data-color="<?php echo esc_attr($name); ?>">
                            <div class="h-12 rounded relative" style="background-color: <?php echo esc_attr($color); ?>">
                                <span class="absolute bottom-0 right-0 text-xs px-1 py-0.5 bg-white bg-opacity-75 rounded-tl">
                                    <?php echo esc_html($color); ?>
                                </span>
                                <input type="color" class="color-picker absolute inset-0 opacity-0 cursor-pointer" 
                                       value="<?php echo esc_attr($color); ?>" 
                                       data-color="<?php echo esc_attr($name); ?>" 
                                       data-palette="<?php echo esc_attr($key); ?>">
                                <span class="absolute inset-0 flex items-center justify-center text-xs text-white bg-black bg-opacity-50 opacity-0 hover:opacity-100 pointer-events-none">
                                    Change Color
                                </span>
                            </div>
                            <div class="flex justify-between items-center mt-1">
                                <span class="text-sm"><?php echo esc_html(ucfirst($name)); ?></span>
                                <button class="save-color px-2 py-1 text-xs bg-gray-500 text-white rounded hover:bg-gray-600">
                                    Save Color
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Preview Elements -->
                <div class="preview-elements p-4 rounded" style="background-color: <?php echo esc_attr($colors['background']); ?>">
                    <h4 class="heading-preview" style="color: <?php echo esc_attr($colors['primary']); ?>">
                        Sample Heading
                    </h4>
                    <p class="text-preview" style="color: <?php echo esc_attr($colors['text']); ?>">
                        Sample paragraph text
                    </p>
                    <div class="flex gap-2 mt-2">
                        <button class="px-4 py-2 rounded" 
                                style="background-color: <?php echo esc_attr($colors['primary']); ?>; color: #fff;">
                            Primary
                        </button>
                        <button class="px-4 py-2 rounded" 
                                style="background-color: <?php echo esc_attr($colors['accent']); ?>; color: #fff;">
                            Accent
                        </button>
                    </div>
                </div>

                <!-- Apply Button -->
                <div class="flex gap-2 mt-4">
                    <button class="apply-palette px-4 py-2 bg-blue-500 text-white rounded flex-grow hover:bg-blue-600 transition-colors <?php echo $current_palette === $key ? 'opacity-50 cursor-not-allowed' : ''; ?>" 
                            data-palette="<?php echo esc_attr($key); ?>"
                            <?php echo $current_palette === $key ? 'disabled' : ''; ?>>
                        <?php echo $current_palette === $key ? 'Currently Active' : 'Apply This Palette'; ?>
                    </button>
                    <button class="reset-palette px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition-colors"
                            data-palette="<?php echo esc_attr($key); ?>">
                        Reset
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Add notification function
    function showNotification(message, isSuccess = true) {
        const notification = $('#palette-notification');
        notification
            .text(message)
            .removeClass('bg-green-500 bg-red-500')
            .addClass(isSuccess ? 'bg-green-500' : 'bg-red-500')
            .css({
                'transform': 'translateY(0)',
                'opacity': '1'
            });

        setTimeout(() => {
            notification.css({
                'transform': 'translateY(-100%)',
                'opacity': '0'
            });
        }, 3000);
    }

    // Handle apply palette button clicks
    $('.apply-palette').on('click', function() {
        const button = $(this);
        const palette = button.data('palette');
        
        // Disable button and show loading state
        button.prop('disabled', true).text('Applying...');
        
        $.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
            data: {
                action: 'update_color_palette',
                nonce: '<?php echo wp_create_nonce('update_color_palette_nonce'); ?>',
                palette: palette
            },
            success: function(response) {
                if (response.success) {
                    showNotification('Palette applied successfully!');
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    showNotification('Error applying palette', false);
                    button.prop('disabled', false).text('Apply This Palette');
                }
            },
            error: function() {
                showNotification('Error applying palette', false);
                button.prop('disabled', false).text('Apply This Palette');
            }
        });
    });

    // Modify save color handler
    $('.save-color').on('click', function() {
        const swatch = $(this).closest('.color-swatch');
        const paletteCard = $(this).closest('.palette-card');
        const palette = paletteCard.data('palette');
        const colorName = swatch.find('.color-picker').data('color');
        const colorValue = swatch.find('.color-picker').val();
        
        // Show saving state
        const button = $(this);
        button.prop('disabled', true).text('Saving...');
        
        $.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
            data: {
                action: 'save_custom_palette_colors',
                nonce: '<?php echo wp_create_nonce('update_color_palette_nonce'); ?>',
                palette: palette,
                color_name: colorName,
                color_value: colorValue,
                original_colors: getAllPaletteColors(paletteCard)
            },
            success: function(response) {
                if (response.success) {
                    showNotification('Color saved successfully!');
                    // Update the color display
                    swatch.find('.h-12').css('background-color', colorValue);
                    swatch.find('.bg-white.bg-opacity-75 span').text(colorValue);
                } else {
                    showNotification('Error saving color', false);
                }
            },
            error: function() {
                showNotification('Error saving color', false);
            },
            complete: function() {
                button.prop('disabled', false).text('Save Color');
            }
        });
    });

    // Add function to get all colors from a palette
    function getAllPaletteColors(paletteCard) {
        const colors = {};
        paletteCard.find('.color-picker').each(function() {
            colors[$(this).data('color')] = $(this).val();
        });
        return colors;
    }

    // Handle color picker changes (preview only)
    $('.color-picker').on('input', function() {
        const palette = $(this).data('palette');
        const colorName = $(this).data('color');
        const colorValue = $(this).val();
        
        // Update the color in the palette
        $(`.palette-card[data-palette="${palette}"] .color-swatch[data-color="${colorName}"] .h-12`).css('background-color', colorValue);
        
        // Update the preview elements
        if (colorName === 'background') {
            $(`.palette-card[data-palette="${palette}"] .preview-elements`).css('background-color', colorValue);
        } else if (colorName === 'primary') {
            $(`.palette-card[data-palette="${palette}"] .preview-elements h4`).css('color', colorValue);
            $(`.palette-card[data-palette="${palette}"] .preview-elements button:first-child`).css('background-color', colorValue);
        } else if (colorName === 'accent') {
            $(`.palette-card[data-palette="${palette}"] .preview-elements button:last-child`).css('background-color', colorValue);
        } else if (colorName === 'text') {
            $(`.palette-card[data-palette="${palette}"] .preview-elements p`).css('color', colorValue);
        }
    });

    // Add reset palette handler
    $('.reset-palette').on('click', function() {
        if (!confirm('Are you sure you want to reset this palette to default values?')) {
            return;
        }
        
        const button = $(this);
        const palette = button.data('palette');
        
        // Disable button and show loading state
        button.prop('disabled', true).text('Resetting...');
        
        $.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
            data: {
                action: 'reset_palette_data',
                nonce: '<?php echo wp_create_nonce('update_color_palette_nonce'); ?>',
                palette: palette
            },
            success: function(response) {
                if (response.success) {
                    showNotification('Palette reset successfully!');
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    showNotification('Error resetting palette', false);
                    button.prop('disabled', false).text('Reset');
                }
            },
            error: function() {
                showNotification('Error resetting palette', false);
                button.prop('disabled', false).text('Reset');
            }
        });
    });
});
</script>

<style>
.color-picker {
    -webkit-appearance: none;
    padding: 0;
    border: none;
    border-radius: 4px;
    overflow: hidden;
}
.color-picker::-webkit-color-swatch-wrapper {
    padding: 0;
}
.color-picker::-webkit-color-swatch {
    border: none;
    border-radius: 4px;
}
.color-swatch {
    position: relative;
}
.color-swatch .color-picker + span {
    display: none;
}
.color-swatch:hover .color-picker + span {
    display: flex;
}
.color-swatch .color-picker + span.pointer-events-none {
    pointer-events: none;
}
</style>

<?php get_footer(); ?>