<?php
if (!defined('ABSPATH')) exit;

class Elementor_Custom_Component_Widget extends \Elementor\Widget_Base {
    public function get_name() {
        return 'custom_component';
    }

    public function get_title() {
        return __('Custom Component', 'tailswp');
    }

    public function get_icon() {
        return 'eicon-code';
    }

    public function get_categories() {
        return ['general'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'tailswp'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        // Get all saved components
        $saved_components = get_option('code_playground_components', array());
        $component_options = [''=>'Select Component'];
        foreach ($saved_components as $name => $component) {
            $component_options[$name] = $name;
        }

        // Add component selector with immediate update
        $this->add_control(
            'component_name',
            [
                'label' => __('Select Component', 'tailswp'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '',
                'options' => $component_options,
                'frontend_available' => true,
            ]
        );

        // Remove initial component loading
        $this->add_control(
            'html_content',
            [
                'label' => __('HTML', 'tailswp'),
                'type' => \Elementor\Controls_Manager::CODE,
                'language' => 'html',
                'rows' => 20,
                'default' => '',
            ]
        );

        $this->add_control(
            'css_content',
            [
                'label' => __('CSS', 'tailswp'),
                'type' => \Elementor\Controls_Manager::CODE,
                'language' => 'css',
                'rows' => 20,
                'default' => '',
            ]
        );

        $this->add_control(
            'js_content',
            [
                'label' => __('JavaScript', 'tailswp'),
                'type' => \Elementor\Controls_Manager::CODE,
                'language' => 'javascript',
                'rows' => 20,
                'default' => '',
            ]
        );

        $this->end_controls_section();

        // Improved component selection handling
        add_action('elementor/frontend/after_register_scripts', function() {
            wp_add_inline_script('elementor-frontend', "
                jQuery(window).on('elementor/frontend/init', function() {
                    elementor.hooks.addAction('panel/open_editor/widget/custom_component', function(panel, model, view) {
                        // Store the select element reference
                        var componentSelect = panel.$el.find('[data-setting=\"component_name\"]');
                        
                        function loadComponent(componentName) {
                            if(!componentName) return;
                            
                            jQuery.ajax({
                                url: '" . admin_url('admin-ajax.php') . "',
                                type: 'POST',
                                data: {
                                    action: 'get_component_data',
                                    component: componentName
                                },
                                success: function(response) {
                                    if(response.success && response.data) {
                                        model.setSettings({
                                            html_content: response.data.html || '',
                                            css_content: response.data.css || '',
                                            js_content: response.data.js || ''
                                        });
                                    }
                                }
                            });
                        }

                        // Clear previous event handlers
                        componentSelect.off('change');
                        
                        // Add new change handler
                        componentSelect.on('change', function(e) {
                            loadComponent(this.value);
                        });

                        // Initial load if component is selected
                        if(componentSelect.val()) {
                            loadComponent(componentSelect.val());
                        }
                    });
                });
            ");
        });
    }

    public function on_import($settings) {
        $saved_components = get_option('code_playground_components', array());
        if (!empty($settings['component_name']) && isset($saved_components[$settings['component_name']])) {
            $component = $saved_components[$settings['component_name']];
            $settings['html_content'] = $component['html'];
            $settings['css_content'] = $component['css'];
            $settings['js_content'] = $component['js'];
        }
        return $settings;
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        
        if (empty($settings['component_name'])) {
            return;
        }

        // Get current values or fallback to saved component
        $saved_components = get_option('code_playground_components', array());
        $component = isset($saved_components[$settings['component_name']]) ? $saved_components[$settings['component_name']] : null;

        $html = !empty($settings['html_content']) ? $settings['html_content'] : ($component ? $component['html'] : '');
        $css = !empty($settings['css_content']) ? $settings['css_content'] : ($component ? $component['css'] : '');
        $js = !empty($settings['js_content']) ? $settings['js_content'] : ($component ? $component['js'] : '');

        echo '<style>' . $css . '</style>';
        echo '<div class="custom-component">' . $html . '</div>';
        echo '<script>' . $js . '</script>';
    }
}

// Update AJAX handler with nonce check
add_action('wp_ajax_get_component_data', function() {
    check_ajax_referer('get_component_data');
    $component_name = sanitize_text_field($_POST['component']);
    $saved_components = get_option('code_playground_components', array());
    
    if (isset($saved_components[$component_name])) {
        wp_send_json_success($saved_components[$component_name]);
    } else {
        wp_send_json_error('Component not found');
    }
});