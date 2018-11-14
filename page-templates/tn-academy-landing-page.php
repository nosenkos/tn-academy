<?php

/**
 * Template Name: TN Academy Landing Page
 */

get_header();
?>

    <section class="eventsPage">
        <div class="container">
            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                <?php if (has_post_thumbnail()) { ?>
                    <div class="coverImage">
                        <?php the_post_thumbnail(); ?>
                    </div>
                <?php } ?>
                <div class="tabsWrap">
                    <div class="tabs">
                        <div class="tab-content">
                            <?php the_content(); ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
                <style>
                    .eventsPage .tabsWrap {
                        width: 100% !important;
                        padding-right: 0px;
                    }
                </style>
            <?php endif; ?>

        </div>
    </section>

<?php get_footer();