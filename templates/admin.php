<?php
$base = new \Inc\Base\BaseController();
?>
<div class="wrap">
    <h1><?php echo __('TN Academy Plugin', TNA_PLUGIN_NAME); ?></h1>
    <?php settings_errors(); ?>

    <ul class="nav nav-tabs">
        <li class="active"><a href="#tab-1"><?php echo __('Manage Settings', TNA_PLUGIN_NAME); ?></a></li>
        <?php
        if ($base->activated('event_button')):
        ?>
        <li><a href="#tab-2"><?php echo __('Wysiwyg Button Settings', TNA_PLUGIN_NAME); ?></a></li>
        <?php endif;?>
        <?php
        if ($base->activated('member_manager')):
            ?>
            <li><a href="#tab-3"><?php echo __('Mail Settings', TNA_PLUGIN_NAME); ?></a></li>
        <?php endif;?>
    </ul>

    <div class="tab-content">
        <div id="tab-1" class="tab-pane active">

            <form method="post" action="options.php">
                <?php
                settings_fields('tn_academy_plugin_settings');
                do_settings_sections('tn_academy_plugin');
                submit_button();
                ?>
            </form>
        </div>

        <?php
        if ($base->activated('event_button')):
        ?>
        <div id="tab-2" class="tab-pane">
            <form method="post" action="options.php">
                <?php
                settings_fields('tn_academy_plugin_btn_settings');
                do_settings_sections('tn_academy_plugin_btn');
                submit_button();
                ?>
            </form>
        </div>
        <?php endif;?>

        <?php
        if ($base->activated('member_manager')):
        ?>
        <div id="tab-3" class="tab-pane">
            <form method="post" action="options.php">
                <?php
                settings_fields('tn_academy_plugin_mail_settings');
                do_settings_sections('tn_academy_plugin_mail');
                submit_button();
                ?>
            </form>
        </div>
        <?php endif;?>
    </div>
</div>