<?php
if (!defined('ABSPATH')) exit;

function register_code_playground_page() {
    $page = add_menu_page(
        'Code Playground',
        'Code Playground',
        'manage_options',
        'code-playground',
        'render_code_playground',
        'dashicons-editor-code'
    );
    add_action('load-' . $page, 'load_code_playground_scripts');
}
add_action('admin_menu', 'register_code_playground_page');

function load_code_playground_scripts() {
    $version = '5.65.2';

    // Add our custom CSS
    wp_enqueue_style('code-playground-style', get_template_directory_uri() . '/assets/css/style-playground.css', array(), '1.0.0');
    
    // Base CodeMirror
    wp_enqueue_script('codemirror', get_template_directory_uri() . '/codemirror/lib/codemirror.js', array(), $version);
    wp_enqueue_style('codemirror', get_template_directory_uri() . '/codemirror/lib/codemirror.css', array(), $version);
    wp_enqueue_style('cm_theme', get_template_directory_uri() . '/codemirror/theme/blackboard.css', array(), $version);

    // Mode scripts
    wp_enqueue_script('cm_xml', get_template_directory_uri() . '/codemirror/mode/xml/xml.js', array('codemirror'), $version);
    wp_enqueue_script('cm_javascript', get_template_directory_uri() . '/codemirror/mode/javascript/javascript.js', array('codemirror'), $version);
    wp_enqueue_script('cm_css', get_template_directory_uri() . '/codemirror/mode/css/css.js', array('codemirror'), $version);
    wp_enqueue_script('cm_htmlmixed', get_template_directory_uri() . '/codemirror/mode/htmlmixed/htmlmixed.js', array('cm_xml', 'cm_javascript', 'cm_css'), $version);

    // Hint addons (note the dependencies)
    wp_enqueue_script('cm_show_hint', get_template_directory_uri() . '/codemirror/addon/hint/show-hint.js', array('codemirror'), $version);
    wp_enqueue_style('cm_show_hint', get_template_directory_uri() . '/codemirror/addon/hint/show-hint.css', array(), $version);
    wp_enqueue_script('cm_xml_hint', get_template_directory_uri() . '/codemirror/addon/hint/xml-hint.js', array('cm_show_hint', 'cm_xml'), $version);
    wp_enqueue_script('cm_html_hint', get_template_directory_uri() . '/codemirror/addon/hint/html-hint.js', array('cm_show_hint', 'cm_xml_hint'), $version);
    wp_enqueue_script('cm_css_hint', get_template_directory_uri() . '/codemirror/addon/hint/css-hint.js', array('cm_show_hint'), $version);
    wp_enqueue_script('cm_javascript_hint', get_template_directory_uri() . '/codemirror/addon/hint/javascript-hint.js', array('cm_show_hint'), $version);

    // Additional Addons
    wp_enqueue_script('cm_closebrackets', get_template_directory_uri() . '/codemirror/addon/edit/closebrackets.js', array('codemirror'), $version);
    wp_enqueue_script('cm_closetag', get_template_directory_uri() . '/codemirror/addon/edit/closetag.js', array('codemirror'), $version);
    wp_enqueue_script('cm_matchbrackets', get_template_directory_uri() . '/codemirror/addon/edit/matchbrackets.js', array('codemirror'), $version);
    wp_enqueue_script('cm_matchtags', get_template_directory_uri() . '/codemirror/addon/edit/matchtags.js', array('codemirror'), $version);
    wp_enqueue_script('cm_active_line', get_template_directory_uri() . '/codemirror/addon/selection/active-line.js', array('codemirror'), $version);
    wp_enqueue_script('cm_fold', get_template_directory_uri() . '/codemirror/addon/fold/foldcode.js', array('codemirror'), $version);
    wp_enqueue_script('cm_fold_xml', get_template_directory_uri() . '/codemirror/addon/fold/xml-fold.js', array('cm_fold'), $version);
    wp_enqueue_script('cm_search', get_template_directory_uri() . '/codemirror/addon/search/search.js', array('codemirror'), $version);
    wp_enqueue_script('cm_searchcursor', get_template_directory_uri() . '/codemirror/addon/search/searchcursor.js', array('codemirror'), $version);
    wp_enqueue_script('cm_dialog', get_template_directory_uri() . '/codemirror/addon/dialog/dialog.js', array('codemirror'), $version);
    wp_enqueue_style('cm_dialog', get_template_directory_uri() . '/codemirror/addon/dialog/dialog.css', array(), $version);
}

// ...existing code...
function register_code_playground_scripts() {
    $current_palette = get_theme_mod('color_palette_setting', 'default');
    $palettes = get_all_theme_palettes();
    $colors = isset($palettes[$current_palette]) ? $palettes[$current_palette] : $palettes['default'];
    wp_localize_script('jquery', 'wpData', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('code_playground_save'),
        'components' => get_option('code_playground_components', array()),
        'tailwindCssUrl' => get_template_directory_uri() . '/assets/css/tailwind-output.css',
        'currentPalette' => $current_palette,
        'siteUrl' => get_site_url(),
        'paletteColors' => $colors,
        'allPalettes' => $palettes // Add all palettes
    ));
}
// Add AJAX handlers
function save_code_component() {
    check_ajax_referer('code_playground_save', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }

    // Handle delete request
    if (isset($_POST['delete']) && $_POST['delete']) {
        $name = sanitize_title($_POST['name']);
        $saved_components = get_option('code_playground_components', array());
        unset($saved_components[$name]);
        update_option('code_playground_components', $saved_components);
        wp_send_json_success();
        return;
    }

    $name = sanitize_title($_POST['name']);
    
    // Validate component name
    if (empty($name) || $name === 'tabs-2' || preg_match('/^tabs-\d+$/', $name)) {
        wp_send_json_error('Invalid component name. Please choose a different name.');
        return;
    }

    $type = sanitize_text_field($_POST['type']);
    $html = wp_kses_post($_POST['html']);
    $css = sanitize_textarea_field($_POST['css']);
    $js = sanitize_textarea_field($_POST['js']);

    $saved_components = get_option('code_playground_components', array());
    $saved_components[$name] = array(
        'type' => $type,
        'html' => $html,
        'css' => $css,
        'js' => $js
    );

    update_option('code_playground_components', $saved_components);
    
    // Clear Elementor cache to ensure new component is available
    if (class_exists('\Elementor\Plugin')) {
        \Elementor\Plugin::$instance->files_manager->clear_cache();
    }

    wp_send_json_success(array(
        'message' => 'Component saved successfully',
        'elementorMessage' => 'Component is now available in Elementor widgets'
     ));
}
add_action('wp_ajax_save_code_component', 'save_code_component'); // Add this line to register the AJAX action

// Add new AJAX handler for loading component
function load_component() {
    check_ajax_referer('code_playground_save', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }

    $name = sanitize_title($_POST['name']);
    $saved_components = get_option('code_playground_components', array());
    
    if (isset($saved_components[$name])) {
        wp_send_json_success($saved_components[$name]);
    } else {
        wp_send_json_error('Component not found');
    }
}
add_action('wp_ajax_load_component', 'load_component');

function render_code_playground() {
    ?>
    <div class="wrap">
        <h1>Code Playground</h1>
        
        <!-- Modified saved components section -->
        <div class="saved-components">
            <h3>Saved Items</h3>
            <div class="nav-tab-wrapper">
                <a href="#" class="nav-tab nav-tab-active" data-tab="all">All Components</a>
                <a href="#" class="nav-tab" data-tab="shortcodes">Shortcodes</a>
                <a href="#" class="nav-tab" data-tab="sections">Sections</a>
            </div>
            <div id="components-list"></div>
            <div id="shortcode-usage" style="display: none; margin-top: 10px;"></div>
        </div>

        <div class="save-form">
            <h3>Component Settings</h3>
            <input type="text" id="component-name" placeholder="Component name">
            <select id="component-type">
                <option value="shortcode">Shortcode</option>
                <option value="component">Custom Component</option>
                <option value="section">Section</option>
            </select>
            <button id="save-component" class="button button-primary">Save</button>
            <button id="reset-editors" class="button">Reset</button>
            <button id="format-html" class="button button-secondary">Format HTML</button>
        </div>

        <div class="playground-container">
            <div class="editors">
                <h1>Name</h1>
                <div class="editor-tabs">
                    <button class="editor-tab-btn active" data-editor="html">HTML</button>
                    <button class="editor-tab-btn" data-editor="css">CSS</button>
                    <button class="editor-tab-btn" data-editor="js">JavaScript</button>
                </div>
                
                <div class="editor-panels">
                    <div class="editor-panel active" id="html-panel">
                        <div class="editor-wrapper">
                            <textarea id="html-editor"></textarea>
                        </div>
                    </div>
                    
                    <div class="editor-panel" id="css-panel">
                        <div class="editor-wrapper">
                            <textarea id="css-editor"></textarea>
                        </div>
                    </div>
                    
                    <div class="editor-panel" id="js-panel">
                        <div class="editor-wrapper">
                            <textarea id="js-editor"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Add resizer div -->
            <div class="resizer" id="preview-resizer"></div>
            
            <div class="preview-container">
                <div class="preview">
                    <h3>
                        Preview
                        <button id="refresh-preview" class="button button-secondary">
                            <span class="dashicons dashicons-update"></span>
                            Refresh Preview
                        </button>
                    </h3>
                    <!-- Add wrapper for proper Tailwind context -->
                    <div class="preview-wrapper">
                        <div id="preview-content"></div>
                    </div>
                </div>
            </div>
        </div>
        <script>
        jQuery(document).ready(function($) {
            // Common editor configuration
            var commonOptions = {
                theme: "blackboard",
                lineNumbers: true,
                autoCloseBrackets: true,
                autoCloseTags: true,
                styleActiveLine: true,
                matchBrackets: true,
                matchTags: {bothTags: true},
                foldGutter: true,
                gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
                viewportMargin: Infinity,
                height: "100%",
                extraKeys: {
                    "Tab": function(cm) {
                        if (cm.somethingSelected()) {
                            cm.indentSelection("add");
                        } else if (cm.display.input.showHint) {
                            cm.showHint({ completeSingle: false });
                        } else {
                            cm.replaceSelection("    ");
                        }
                    },
                    "Ctrl-Space": "autocomplete",
                    "Ctrl-/": "toggleComment",
                    "Ctrl-F": "findPersistent"
                },
                hintOptions: {
                    completeSingle: false,
                    closeOnUnfocus: false
                }
            };

            // Initialize editors with autocompletion
            function setupEditor(editor) {
                editor.on("inputRead", function(cm, change) {
                    if (change.origin !== "complete" && /^[a-zA-Z_0-9\-\.]$/.test(change.text[0])) {
                        editor.showHint({ completeSingle: false });
                    }
                });
            }

            var htmlEditor = CodeMirror.fromTextArea(document.getElementById("html-editor"), {
                ...commonOptions,
                mode: "htmlmixed",
                autoCloseTags: true
            });
            
            var cssEditor = CodeMirror.fromTextArea(document.getElementById("css-editor"), {
                ...commonOptions,
                mode: "css"
            });
            
            var jsEditor = CodeMirror.fromTextArea(document.getElementById("js-editor"), {
                ...commonOptions,
                mode: "javascript"
            });

            // Setup autocompletion for each editor
            setupEditor(htmlEditor);
            setupEditor(cssEditor);
            setupEditor(jsEditor);

            var hasUnsavedChanges = false;
            
            // Track changes in editors
            function markAsChanged() {
                hasUnsavedChanges = true;
                // Save current state to localStorage
                saveToLocalStorage();
            }

            // Save current state to localStorage
            function saveToLocalStorage() {
                const editorState = {
                    html: htmlEditor.getValue(),
                    css: cssEditor.getValue(),
                    js: jsEditor.getValue(),
                    componentName: $('#component-name').val(),
                    componentType: $('#component-type').val(),
                    timestamp: new Date().getTime()
                };
                localStorage.setItem('codePlaygroundState', JSON.stringify(editorState));
            }

            // Load state from localStorage
            function loadFromLocalStorage() {
                const savedState = localStorage.getItem('codePlaygroundState');
                if (savedState) {
                    const state = JSON.parse(savedState);
                    // Check if state is less than 24 hours old
                    if (new Date().getTime() - state.timestamp < 24 * 60 * 60 * 1000) {
                        htmlEditor.setValue(state.html || '');
                        cssEditor.setValue(state.css || '');
                        jsEditor.setValue(state.js || '');
                        $('#component-name').val(state.componentName || '');
                        $('#component-type').val(state.componentType || 'shortcode');
                        hasUnsavedChanges = false;
                    } else {
                        localStorage.removeItem('codePlaygroundState');
                    }
                }
            }

            // Clear localStorage when component is saved
            function clearLocalStorage() {
                localStorage.removeItem('codePlaygroundState');
                hasUnsavedChanges = false;
            }

            // Modified editor change handlers
            htmlEditor.on("change", function() {
                updatePreview();
                markAsChanged();
            });
            
            cssEditor.on("change", function() {
                updatePreview();
                markAsChanged();
            });
            
            jsEditor.on("change", function() {
                updatePreview();
                markAsChanged();
            });

            // Modified preview update function
            function updatePreview() {
                var html = htmlEditor.getValue();
                var css = cssEditor.getValue();
                var js = jsEditor.getValue();
                
                // Create an isolated preview environment
                var $preview = $('#preview-content');
                $preview.empty();
                
                // Create a container with Tailwind context
                var $container = $('<div>')
                    .addClass('tailwind preview-wrapper')
                    .appendTo($preview);

                // Add current palette CSS variables
                var paletteCSS = `
                    .tailwind {
                        --color-primary: ${wpData.paletteColors.primary};
                        --color-secondary: ${wpData.paletteColors.secondary};
                        --color-background: ${wpData.paletteColors.background};
                        --color-text: ${wpData.paletteColors.text};
                        --color-accent: ${wpData.paletteColors.accent};
                    }
                    /* Base color classes */
                    .tailwind .text-primary { color: var(--color-primary) !important; }
                    .tailwind .bg-primary { background-color: var(--color-primary) !important; }
                    .tailwind .border-primary { border-color: var(--color-primary) !important; }
                    .tailwind .text-secondary { color: var(--color-secondary) !important; }
                    .tailwind .bg-secondary { background-color: var(--color-secondary) !important; }
                    .tailwind .border-secondary { border-color: var(--color-secondary) !important; }
                    .tailwind .text-accent { color: var(--color-accent) !important; }
                    .tailwind .bg-accent { background-color: var(--color-accent) !important; }
                    .tailwind .border-accent { border-color: var(--color-accent) !important; }
                    .tailwind .text-background { color: var(--color-background) !important; }
                    .tailwind .bg-background { background-color: var(--color-background) !important; }
                    
                    /* Hover state classes */
                    .tailwind .hover\\:text-primary:hover { color: var(--color-primary) !important; }
                    .tailwind .hover\\:bg-primary:hover { background-color: var(--color-primary) !important; }
                    .tailwind .hover\\:border-primary:hover { border-color: var(--color-primary) !important; }
                    .tailwind .hover\\:text-secondary:hover { color: var(--color-secondary) !important; }
                    .tailwind .hover\\:bg-secondary:hover { background-color: var(--color-secondary) !important; }
                    .tailwind .hover\\:border-secondary:hover { border-color: var(--color-secondary) !important; }
                    .tailwind .hover\\:text-accent:hover { color: var(--color-accent) !important; }
                    .tailwind .hover\\:bg-accent:hover { background-color: var(--color-accent) !important; }
                    .tailwind .hover\\:border-accent:hover { border-color: var(--color-accent) !important; }
                    .tailwind .hover\\:text-background:hover { color: var(--color-background) !important; }
                    .tailwind .hover\\:bg-background:hover { background-color: var(--color-background) !important; }
                `;

                // Add color palette CSS
                $('<style>')
                    .text(paletteCSS)
                    .appendTo($container);

                // Add Tailwind stylesheet
                $('<link>')
                    .attr({
                        rel: 'stylesheet',
                        href: wpData.tailwindCssUrl + '?v=' + Date.now()
                    })
                    .appendTo($container);

                // Add base URL for images
                $('<base>')
                    .attr('href', wpData.siteUrl + '/')
                    .appendTo($container);

                // Add custom CSS
                if (css) {
                    $('<style>')
                        .text(css)
                        .appendTo($container);
                }

                // Add HTML content
                $('<div>')
                    .addClass('preview-content')
                    .html(html)
                    .appendTo($container);

                // Add and execute JavaScript
                if (js) {
                    try {
                        var $script = $('<script>')
                            .text(js)
                            .appendTo($container);
                        eval(js);
                    } catch (error) {
                        console.error('JavaScript Error:', error);
                    }
                }
            }

            // Add refresh button handler
            $('#refresh-preview').on('click', function() {
                updatePreview();
            });

            // Update preview on changes (optional - remove if you only want manual refresh)
            htmlEditor.on("change", updatePreview);
            cssEditor.on("change", updatePreview);
            jsEditor.on("change", updatePreview);
            
            // Initial preview
            updatePreview();

            // Add format button functionality
            let shouldFormatOnUpdate = false;
            
            // Define color mapping for Tailwind classes
            const colorMap = {
                gray: "primary",
                indigo: "primary",
                white: "",
                violet: "primary",
                blue: "primary",
                green: "primary",
                red: "primary",
                yellow: "primary",
                'primary-700': 'primary',
                'primary-800': 'primary',
                'primary-300': 'primary',
                'primary-900': 'primary',
                'gray-900': 'text',
                'gray-300': 'border',
                'gray-100': 'hover:bg',
                'gray-600': 'dark:border',
                'gray-700': 'dark:hover:bg',
                'gray-400': 'text',
                'gray-500': 'text',
            };

            function formatHtmlContent(content) {
                const formattedContent = content.replace(
                    /\b(text|bg|border|hover:bg|focus:ring|dark:text|dark:border|dark:hover:bg|dark:focus:ring)-(\w+)(?:-(\d{1,3}))?\b(?!-opacity)/g,
                    (match, prefix, color, shade) => {
                        console.log("Matched:", match);
                        // For other color-based classes (bg, border)
                        if (colorMap[color]) {
                            return `${prefix}-${colorMap[color]}`;
                        }
                        return match;
                    }
                );
                return formattedContent;
            }

            $('#format-html').on('click', function() {
                const currentHtml = htmlEditor.getValue();
                const formattedHtml = formatHtmlContent(currentHtml);
                htmlEditor.setValue(formattedHtml);
                updatePreview();
            });

            // Add save functionality
            $('#save-component').on('click', function() {
                var name = $('#component-name').val();
                var type = $('#component-type').val();
                
                if (!name) {
                    alert('Please enter a component name');
                    return;
                }

                $.ajax({
                    url: ajaxurl,  // Changed from wpData.ajaxurl
                    type: 'POST',
                    data: {
                        action: 'save_code_component',
                        nonce: wpData.nonce,  // Changed from codePlayground.nonce
                        name: name,
                        type: type,
                        html: htmlEditor.getValue(),
                        css: cssEditor.getValue(),
                        js: jsEditor.getValue()
                    },
                    success: function(response) {
                        if (response.success) {
                            clearLocalStorage();
                            wpData.components[name] = {  // Changed from codePlayground.components
                                type: type,
                                html: htmlEditor.getValue(),
                                css: cssEditor.getValue(),
                                js: jsEditor.getValue()
                            };
                            displaySavedComponents();
                            alert('Saved successfully! ' + (type === 'shortcode' ? 
                                'Use shortcode [' + name + ']' : 
                                'Component saved as "' + name + '"'));
                        } else {
                            alert('Error saving: ' + response.data);
                        }
                    }
                });
            });

            // Add reset functionality
            function resetEditors() {
                if (hasUnsavedChanges) {
                    if (!confirm('You have unsaved changes. Are you sure you want to reset?')) {
                        return;
                    }
                }
                
                htmlEditor.setValue('');
                cssEditor.setValue('');
                jsEditor.setValue('');
                $('#component-name').val('');
                $('#component-type').val('shortcode');
                
                clearLocalStorage();
                updatePreview();
            }

            $('#reset-editors').on('click', resetEditors);

            // Add function to display saved components
            function displaySavedComponents(filter = 'all') {
                var components = wpData.components;  // Changed from codePlayground.components
                var list = $('#components-list');
                var usageDiv = $('#shortcode-usage');
                list.empty();
                usageDiv.empty();
                
                // Add grid class based on filter
                list.removeClass('grid-view list-view');
                if (filter === 'sections') {
                    list.addClass('grid-view');
                } else {
                    list.addClass('list-view');
                }
                
                Object.keys(components).forEach(function(name) {
                    var component = components[name];
                    if (filter === 'all' || 
                        (filter === 'shortcodes' && component.type === 'shortcode') ||
                        (filter === 'sections' && component.type === 'section')) {
                        
                        var item = $('<div class="component-item"></div>')
                            .addClass(component.type === 'section' ? 'section-item' : '');


                        var controls = $('<div class="component-controls"></div>')
                            .append(
                                $('<span class="component-name"></span>').text(name + ' (' + component.type + ')'),
                                $('<button class="button button-small">Load</button>')
                                    .click(function() {
                                        loadComponent(name);
                                    }),
                                $('<button class="button button-small button-link-delete">Delete</button>')
                                    .click(function() {
                                        if (confirm('Are you sure you want to delete this component?')) {
                                            deleteComponent(name);
                                        }
                                    })
                            );

                        controls.append($('<code></code>').text('[' + name + ']'));
                        item.append(controls);
                        list.append(item);
                    }
                });

                usageDiv.toggle(filter === 'shortcodes');
                if (filter === 'shortcodes') {
                    usageDiv.html(`
                        <div class="shortcode-instructions">
                            <h4>How to use shortcodes:</h4>
                            <ol>
                                <li>Copy the shortcode (e.g., [example-code])</li>
                                <li>Paste it into any post or page content</li>
                                <li>The component will be rendered when the page loads</li>
                            </ol>
                        </div>
                    `);
                }
            }

            // Add this CSS dynamically
            $('<style>')
                .text(`
                    #components-list.grid-view {
                        display: grid;
                        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                        gap: 20px;
                        padding: 20px;
                    }
                    #components-list.list-view .component-item {
                        margin-bottom: 10px;
                    }
                    .section-item {
                        border: 1px solid #ddd;
                        border-radius: 8px;
                        overflow: hidden;
                    }
                    .section-preview {
                        position: relative;
                        min-height: 200px;
                        padding: 20px;
                        background: #f5f5f5;
                        border-bottom: 1px solid #ddd;
                    }
                    .component-controls {
                        padding: 10px;
                        display: flex;
                        gap: 10px;
                        align-items: center;
                        background: #fff;
                    }
                    .component-name {
                        font-weight: bold;
                    }
                `)
                .appendTo('head');

            // Add function to load component
            function loadComponent(name) {
                $.ajax({
                    url: ajaxurl,  // Changed from wpData.ajaxurl
                    type: 'POST',
                    data: {
                        action: 'load_component',
                        nonce: wpData.nonce,  // Changed from codePlayground.nonce
                        name: name
                    },
                    success: function(response) {
                        if (response.success) {
                            htmlEditor.setValue(response.data.html || '');
                            cssEditor.setValue(response.data.css || '');
                            jsEditor.setValue(response.data.js || '');
                            $('#component-name').val(name);
                            $('#component-type').val(response.data.type);
                        } else {
                            alert('Error loading component: ' + response.data);
                        }
                    }
                });
            }

            // Add function to delete component
            function deleteComponent(name) {
                $.ajax({
                    url: ajaxurl,  // Changed from wpData.ajaxurl
                    type: 'POST',
                    data: {
                        action: 'save_code_component',
                        nonce: wpData.nonce,  // Changed from codePlayground.nonce
                        name: name,
                        delete: true
                    },
                    success: function(response) {
                        if (response.success) {
                            delete wpData.components[name];  // Changed from codePlayground.components
                            displaySavedComponents();
                        } else {
                            alert('Error deleting component: ' + response.data);
                        }
                    }
                });
            }

            // Add tab functionality
            $('.nav-tab-wrapper .nav-tab').on('click', function(e) {
                e.preventDefault();
                $('.nav-tab-wrapper .nav-tab').removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active');
                displaySavedComponents($(this).data('tab'));
            });

            // Add beforeunload warning
            $(window).on('beforeunload', function() {
                if (hasUnsavedChanges) {
                    return 'You have unsaved changes. Are you sure you want to leave?';
                }
            });

            // Load last state on page load
            loadFromLocalStorage();

            // Initialize components list
            displaySavedComponents();

            // Add editor tab functionality
            $('.editor-tab-btn').on('click', function() {
                $('.editor-tab-btn').removeClass('active');
                $('.editor-panel').removeClass('active');
                
                $(this).addClass('active');
                $('#' + $(this).data('editor') + '-panel').addClass('active');
                
                // Refresh the active editor to prevent display issues
                switch($(this).data('editor')) {
                    case 'html': htmlEditor.refresh(); break;
                    case 'css': cssEditor.refresh(); break;
                    case 'js': jsEditor.refresh(); break;
                }
            });

            // Add this near the top of your jQuery ready function
            const resizer = document.getElementById('preview-resizer');
            const editors = document.querySelector('.editors');
            const preview = document.querySelector('.preview-container');
            let isResizing = false;
            let lastDownX = 0;

            resizer.addEventListener('mousedown', (e) => {
                isResizing = true;
                lastDownX = e.clientX;
                resizer.classList.add('resizing');
            });

            document.addEventListener('mousemove', (e) => {
                if (!isResizing) return;

                const delta = e.clientX - lastDownX;
                lastDownX = e.clientX;

                // Calculate new widths
                const editorsWidth = editors.getBoundingClientRect().width;
                const previewWidth = preview.getBoundingClientRect().width;
                
                const newEditorsWidth = editorsWidth + delta;
                const newPreviewWidth = previewWidth - delta;

                // Check minimum and maximum widths
                if (newEditorsWidth >= 200 && newEditorsWidth <= (window.innerWidth - 300) &&
                    newPreviewWidth >= 300 && newPreviewWidth <= (window.innerWidth - 200)) {
                    editors.style.width = newEditorsWidth + 'px';
                    preview.style.width = newPreviewWidth + 'px';
                }

                // Refresh CodeMirror editors
                htmlEditor.refresh();
                cssEditor.refresh();
                jsEditor.refresh();
            });

            document.addEventListener('mouseup', () => {
                isResizing = false;
                resizer.classList.remove('resizing');
            });

            // Add this to your window resize handler if you have one
            window.addEventListener('resize', () => {
                // Reset widths on window resize to prevent layout issues
                editors.style.width = '';
                preview.style.width = '';
            });

            // Handle color picker changes
            $('.color-picker').on('input', function() {
                const colorName = $(this).closest('.palette-card').data('color');
                const colorValue = $(this).val();
                wpData.paletteColors[colorName] = colorValue;
                updatePreview();
            });

            // ...existing jQuery ready code...
        });
        </script>

        <style>
        /* Add these styles for better preview rendering */
        .preview-wrapper {
            padding: 20px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-top: 10px;
        }
        
        .preview-content {
            min-height: 100px;
        }

        .tailwind {
            /* Reset common WordPress admin styles that might interfere */
            all: revert;
            font-family: inherit;
            box-sizing: border-box;
        }

        .tailwind * {
            box-sizing: inherit;
        }

        .editors {
            width: 100%;
        }

        .preview-wrapper {
            padding: 20px;
            background: #fff;
            border-radius: 4px;
        }

        .preview-content {
            width: 100%;
            min-height: 400px;
        }

        .editor-wrapper {
            min-height: 300px; /* Adjust editor height */
        }

        /* Responsive adjustments */
        @media (min-width: 1200px) {
            .preview-wrapper {
                min-height: 600px;
            }
            .preview-content {
                min-height: 600px;
            }
        }

        /* Add styles for palette cards */
        .palette-cards {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }
        .palette-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .palette-card input[type="color"] {
            margin-bottom: 10px;
        }
        </style>
    </div>
    <?php
}

// Load saved shortcodes
function load_saved_shortcodes() {
    $saved_components = get_option('code_playground_components', array());
    foreach ($saved_components as $name => $component) {
        if ($component['type'] === 'shortcode') {
            add_shortcode($name, function() use ($component) {
                $output = "<style>{$component['css']}</style>";
                $output .= $component['html'];
                $output .= "<script>{$component['js']}</script>";
                return $output;
            });
        }
    }
}
add_action('init', 'load_saved_shortcodes');

function add_tailwind_intellisense() {
    ?>
    <script>
    // Wait for CodeMirror to be available
    jQuery(document).ready(function($) {
        if (typeof CodeMirror === 'undefined') return;

        // Add Tailwind class suggestions to CodeMirror
        const tailwindClasses = [
            // Layout
            'container', 'flex', 'grid', 'hidden', 'block', 'inline', 'inline-block',
            // Spacing
            'm-', 'p-', 'mt-', 'mb-', 'ml-', 'mr-', 'px-', 'py-',
            // Colors
            'text-primary', 'bg-primary', 'text-secondary', 'bg-secondary',
            'text-accent', 'bg-accent', 'text-background', 'bg-background',
            // Typography
            'text-sm', 'text-base', 'text-lg', 'text-xl', 'font-bold',
            // Flexbox & Grid
            'flex-row', 'flex-col', 'items-center', 'justify-center', 'gap-',
            // Borders & Shadows
            'rounded', 'border', 'shadow-sm', 'shadow', 'shadow-lg',
            // Transitions
            'transition', 'hover:', 'focus:', 'active:'
        ];

        // Modify CodeMirror's HTML hint function
        const originalHint = CodeMirror.hint.html;
        if (originalHint) {
            CodeMirror.hint.html = function(cm) {
                const cursor = cm.getCursor();
                const token = cm.getTokenAt(cursor);
                
                if (token.string.match(/class(Name)?=["'][^"']*$/)) {
                    const prefix = token.string.match(/["']([^"']*)$/)[1];
                    const matches = tailwindClasses
                        .filter(c => c.startsWith(prefix))
                        .map(c => ({text: c, displayText: c}));
                    
                    return {
                        list: matches,
                        from: {line: cursor.line, ch: token.start + token.string.lastIndexOf(prefix)},
                        to: cursor
                    };
                }
                
                return originalHint(cm);
            };
        }
    });
    </script>
    <?php
}
add_action('admin_footer', 'add_tailwind_intellisense');