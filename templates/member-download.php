<?php
$posts_type = \Inc\Base\CustomPostTypeController::getPostTypes();
$post_type = array();
$posts = array();

$count_posts = count($posts_type);
$post_titles = "";
$i = 0;
foreach ($posts_type as $row) {
    $i += 1;
    $post_type[] = $row['post_type'];
    if ($i < $count_posts) {
        $post_titles .= $row['singular_name'] . ", ";
    } elseif ($i == $count_posts) {
        $post_titles .= $row['singular_name'];
    }
}

if ($post_type):
    $posts = get_posts(array(
        'post_type' => $post_type,
        'numberposts' => -1
    ));
endif;
?>
<div class="wrap">
    <div id="icon-tools" class="icon32"></div>
    <h2><?php echo __('Download Report', TNA_PLUGIN_NAME); ?></h2>
    <p><?php echo ($post_titles) ?  __('Export Registered People on ' . $post_titles, TNA_PLUGIN_NAME) : __('Export Registered People on Course', TNA_PLUGIN_NAME); ?></p>
    <form method="post" action="edit.php?post_type=member&page=tn-member-download-list">
        <?php
        if ($posts):
            ?>
            <div class="form-group">
                <label for="course_list"><?php echo __('Course', TNA_PLUGIN_NAME); ?>
                    <select name="course_list" id="course_list" class="form-control">
                        <option value="All"><?php echo __('All', TNA_PLUGIN_NAME); ?></option>
                        <?php
                        foreach ($posts as $row):
                            ?>
                            <option value="<?php echo $row->post_title; ?>"><?php echo $row->post_title; ?></option>
                            <?php
                        endforeach;
                        ?>
                    </select>
                </label>
            </div>
            <?php
        endif;
        ?>
        <div class="form-group">
            <label for="export"><?php echo __('Donwload
        Format', TNA_PLUGIN_NAME); ?>
                <select name="export" id="export" class="form-control">
                    <option value="tn-member-download-list-csv"><?php echo __('Donwload
        CSV', TNA_PLUGIN_NAME); ?></option>
                    <option value="tn-member-download-list-xls"><?php echo __('Donwload
        Excel', TNA_PLUGIN_NAME); ?></option>
                </select>
            </label>
        </div>
        <div>
            <button type="stubmit" class="button-primary"><?php echo __('Download',TNA_PLUGIN_NAME);?></button>
        </div>
    </form>
</div>