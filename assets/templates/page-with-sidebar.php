<?php /**
 * Template Name: Page with Sidebar
 * 
 * This is a page template with a right sidebar
 */
?>

<?php get_header(); ?>

<div class="container mx-auto flex flex-wrap">
    <div id="primary" class="content-area w-full md:w-2/3 pr-4">
        <main id="main" class="site-main">
            <?php
            while ( have_posts() ) :
                the_post();
                get_template_part( 'template-parts/content', 'page' );

                // If comments are open or we have at least one comment, load up the comment template.
                if ( comments_open() || get_comments_number() ) :
                    comments_template();
                endif;
            endwhile;
            ?>
        </main>
    </div>

    <div id="secondary" class="widget-area w-full md:w-1/3">
        <?php get_sidebar(); ?>
    </div>
</div>

<?php get_footer(); ?>