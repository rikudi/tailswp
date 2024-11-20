<?php
/* Template Name: Elementor Canvas */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

\Elementor\Plugin::$instance->frontend->add_body_class( 'elementor-template-canvas' );

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>
    <?php
    while ( have_posts() ) : the_post();
        the_content();
    endwhile;
    ?>
    <?php wp_footer(); ?>
</body>
</html>