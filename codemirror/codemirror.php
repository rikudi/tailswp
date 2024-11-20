<?php
if (!defined('ABSPATH')) exit;

add_action('load-theme-editor.php', 'codemirror_register');
add_action('load-plugin-editor.php', 'codemirror_register');

function codemirror_register() {
    $version = '5.65.2'; // Add your CodeMirror version
    wp_register_script('codemirror', get_template_directory_uri()."/codemirror/lib/codemirror.js", array(), $version);
    wp_register_style('codemirror', get_template_directory_uri()."/codemirror/lib/codemirror.css", array(), $version);
    wp_register_style('cm_blackboard', get_template_directory_uri()."/codemirror/theme/blackboard.css", array(), $version);
    wp_register_script('cm_xml', get_template_directory_uri()."/codemirror/xml/xml.js", array('codemirror'), $version);
    wp_register_script('cm_javascript', get_template_directory_uri()."/codemirror/javascript/javascript.js", array('codemirror'), $version);
    wp_register_script('cm_css', get_template_directory_uri()."/codemirror/css/css.js", array('codemirror'), $version);
    wp_register_script('cm_clike', get_template_directory_uri()."/codemirror/clike/clike.js", array('codemirror'), $version);
    wp_register_script('cm_php', get_template_directory_uri()."/codemirror/php/php.js", array('codemirror', 'cm_clike'), $version);
    add_action('admin_enqueue_scripts', 'codemirror_enqueue_scripts');
    add_action('admin_head', 'codemirror_control_js');
}

function codemirror_enqueue_scripts() {
    wp_enqueue_script('codemirror');
    wp_enqueue_style('codemirror');
    wp_enqueue_style('cm_blackboard');
    wp_enqueue_script('cm_xml');
    wp_enqueue_script('cm_javascript');
    wp_enqueue_script('cm_css');
    wp_enqueue_script('cm_php');
    wp_enqueue_script('cm_clike');
}

function codemirror_control_js() {
    if (!current_user_can('edit_themes') && !current_user_can('edit_plugins')) {
        return;
    }
    ?>
    <script>
    jQuery(document).ready(function($) {
        var editor = CodeMirror.fromTextArea(document.getElementById("newcontent"), {
            lineNumbers: true,
            matchBrackets: true,
            mode: "application/x-httpd-php",
            indentUnit: 4,
            indentWithTabs: true,
            theme: "blackboard"
        });
    });
    </script>
    <?php
}
