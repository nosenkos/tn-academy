<?php
get_header();
?>

    <section class="eventsPage">
        <div class="container">
            <?php if (have_posts()) : while (have_posts()) : the_post();
                $title = get_post_meta(get_the_ID(), 'tn-title', true);
                $short_desc = get_post_meta(get_the_ID(), 'tn-desc', true);
                $date = get_post_meta(get_the_ID(), 'tn-date', true);
                $location = get_post_meta(get_the_ID(), 'tn-location', true);
                $c_leader = get_post_meta(get_the_ID(), 'tn-course-leader', true);
                $price = get_post_meta(get_the_ID(), 'tn-price', true);
                $last_date = get_post_meta(get_the_ID(), 'tn-last-date', true);
                $det_desc = get_post_meta(get_the_ID(), 'tn-det-desc', true);
                $show_form = get_post_meta(get_the_ID(), 'tn-show-form', true);
                $base = new \Inc\Base\BaseController();
                ?>
                <?php if (has_post_thumbnail()) { ?>
                    <div class="coverImage">
                        <?php the_post_thumbnail(); ?>
                    </div>
                <?php } ?>
                <div class="tabsWrap">
                    <div class="tabs">
                        <div class="tab-content">
                            <?php
                            if ($title):
                                ?>
                                <h1><?= $title; ?></h1>
                                <?php
                            endif;

                            if ($short_desc):
                                ?>
                                <div class="course_info">
                                    <p><?= $short_desc; ?></p>
                                </div>
                                <?php
                            endif;

                            if ($date):
                                ?>
                                <div class="course_info">
                                    <h3><?php echo __('Date:', TNA_PLUGIN_NAME); ?></h3>
                                    <p><?= $date; ?></p>
                                </div>
                                <?php
                            endif;

                            if ($location):
                                ?>
                                <div class="course_info">
                                    <h3><?php echo __('Location:', TNA_PLUGIN_NAME); ?></h3>
                                    <p><?= $location; ?></p>
                                </div>
                                <?php
                            endif;

                            if ($c_leader):
                                ?>
                                <div class="course_info">
                                    <h3><?php echo __('Course Leader:', TNA_PLUGIN_NAME); ?></h3>
                                    <p><?= $c_leader; ?></p>
                                </div>
                                <?php
                            endif;

                            if ($price):
                                ?>
                                <div class="course_info">
                                    <h3><?php echo __('Price:', TNA_PLUGIN_NAME); ?></h3>
                                    <p><?= $price; ?></p>
                                </div>
                                <?php
                            endif;

                            if ($last_date):
                                ?>
                                <div class="course_info">
                                    <h3><?php echo __('Last day of registration:', TNA_PLUGIN_NAME); ?></h3>
                                    <p><?= $last_date; ?></p>
                                </div>
                                <?php
                            endif;

                            if ($det_desc):
                                ?>
                                <div class="course_info_desc">
                                    <h3><?php echo __('Detailed Description', TNA_PLUGIN_NAME); ?></h3>
                                    <?= wpautop($det_desc); ?>
                                </div>
                                <?php
                            endif;
                            if ($base->activated('member_manager')):
                                if ($show_form):
                                    ?>
                                    <button type="button" class="tn-registration" data-toggle="modal"
                                            data-target="#member-form"><?php echo __('Registration', TNA_PLUGIN_NAME); ?></button>
                                    <?php
                                    require_once(plugin_dir_path(dirname(__FILE__)) . 'templates/contact-form.php');
                                    ?>
                                    <?php
                                endif;
                            endif; ?>
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