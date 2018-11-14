<div class="wrap">
    <h1><?php echo __('CPT Manager', TNA_PLUGIN_NAME); ?></h1>
    <?php settings_errors(); ?>

    <ul class="nav nav-tabs">
        <li class="<?php echo !isset($_POST["edit_post"]) ? 'active' : '' ?>"><a
                    href="#tab-1"><?php echo __('Your Custom Post Types', TNA_PLUGIN_NAME); ?></a></li>
        <li class="<?php echo isset($_POST["edit_post"]) ? 'active' : '' ?>">
            <a href="#tab-2">
                <?php echo isset($_POST["edit_post"]) ? __('Edit', TNA_PLUGIN_NAME) : __('Add', TNA_PLUGIN_NAME) ?><?php echo __('Custom Post Type', TNA_PLUGIN_NAME); ?>
            </a>
        </li>
        <li><a href="#tab-3"><?php echo __('Export', TNA_PLUGIN_NAME); ?></a></li>
    </ul>

    <div class="tab-content">
        <div id="tab-1" class="tab-pane <?php echo !isset($_POST["edit_post"]) ? 'active' : '' ?>">

            <h3><?php echo __('Manage Your Custom Post Types', TNA_PLUGIN_NAME); ?></h3>

            <?php
            $options = get_option('tn_academy_plugin_cpt') ?: array();

            echo '<table class="cpt-table"><tr><th>' . __('ID', TNA_PLUGIN_NAME) . '</th><th>' . __('Singular Name', TNA_PLUGIN_NAME) . '</th><th>' . __('Plural Name', TNA_PLUGIN_NAME) . '</th><th class="text-center">' . __('Public', TNA_PLUGIN_NAME) . '</th><th class="text-center">' . __('Archive', TNA_PLUGIN_NAME) . '</th><th class="text-center">' . __('Actions', TNA_PLUGIN_NAME) . '</th></tr>';

            foreach ($options as $option) {
                $public = isset($option['public']) ? __("TRUE", TNA_PLUGIN_NAME) : "FALSE";
                $archive = isset($option['has_archive']) ? "TRUE" : __("FALSE", TNA_PLUGIN_NAME);

                echo "<tr><td>{$option['post_type']}</td><td>" . __($option['singular_name'], TNA_PLUGIN_NAME) . "</td><td>" . __($option['plural_name'], TNA_PLUGIN_NAME) . "</td><td class=\"text-center\">" . __($public, TNA_PLUGIN_NAME) . "</td><td class=\"text-center\">" . __($archive, TNA_PLUGIN_NAME) . "</td><td class=\"text-center\">";

                echo '<form method="post" action="" class="inline-block">';
                echo '<input type="hidden" name="edit_post" value="' . $option['post_type'] . '">';
                submit_button(__('Edit', TNA_PLUGIN_NAME), 'primary small', 'submit', false);
                echo '</form> ';

                echo '<form method="post" action="options.php" class="inline-block">';
                settings_fields('tn_academy_plugin_cpt_settings');
                echo '<input type="hidden" name="remove" value="' . $option['post_type'] . '">';
                submit_button(__('Delete', TNA_PLUGIN_NAME), 'delete small', 'submit', false, array(
                    'onclick' => 'return confirm("' . __('Are you sure you want to delete this Custom Post Type? The data associated with it will not be deleted.', TNA_PLUGIN_NAME) . '");'
                ));
                echo '</form></td></tr>';
            }

            echo '</table>';
            ?>

        </div>

        <div id="tab-2" class="tab-pane <?php echo isset($_POST["edit_post"]) ? 'active' : '' ?>">
            <form method="post" action="options.php">
                <?php
                settings_fields('tn_academy_plugin_cpt_settings');
                do_settings_sections('tn_academy_cpt');
                submit_button();
                ?>
            </form>
        </div>

        <div id="tab-3" class="tab-pane">
            <h3><?php echo __('Export Your Custom Post Types', TNA_PLUGIN_NAME); ?></h3>

            <?php foreach ($options as $option) { ?>

                <h3><?php echo __($option['singular_name'], TNA_PLUGIN_NAME); ?></h3>

                <pre class="prettyprint">
// Register Custom Post Type
function custom_post_type() {

	$labels = array(
		'name'                  => _x( 'Post Types', 'Post Type General Name', 'text_domain' ),
		'singular_name'         => _x( '<?php echo $option['singular_name']; ?>', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'             => __( '<?php echo $option['plural_name']; ?>', 'text_domain' ),
		'plural_name'             => __( '<?php echo $option['plural_name']; ?>', 'text_domain' ),
		'name_admin_bar'        => __( 'Post Type', 'text_domain' ),
		'archives'              => __( 'Item Archives', 'text_domain' ),
		'attributes'            => __( 'Item Attributes', 'text_domain' ),
		'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
		'all_items'             => __( 'All Items', 'text_domain' ),
		'add_new_item'          => __( 'Add New Item', 'text_domain' ),
		'add_new'               => __( 'Add New', 'text_domain' ),
		'new_item'              => __( 'New Item', 'text_domain' ),
		'edit_item'             => __( 'Edit Item', 'text_domain' ),
		'update_item'           => __( 'Update Item', 'text_domain' ),
		'view_item'             => __( 'View Item', 'text_domain' ),
		'view_items'            => __( 'View Items', 'text_domain' ),
		'search_items'          => __( 'Search Item', 'text_domain' ),
		'not_found'             => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
		'featured_image'        => __( 'Featured Image', 'text_domain' ),
		'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
		'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
		'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
		'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
		'items_list'            => __( 'Items list', 'text_domain' ),
		'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
		'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
	);
	$args = array(
		'label'                 => __( 'Post Type', 'text_domain' ),
		'description'           => __( 'Post Type Description', 'text_domain' ),
		'labels'                => $labels,
		'supports'              => false,
		'taxonomies'            => array( 'category', 'post_tag' ),
		'hierarchical'          => false,
		'public'                => <?php echo isset($option['public']) ? "true" : "false"; ?>,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => <?php echo isset($option['has_archive']) ? "true" : "false"; ?>,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);
	register_post_type( '<?php echo $option['post_type']; ?>', $args );

}
add_action( 'init', 'custom_post_type', 0 );
			</pre>

            <?php } ?>

        </div>
    </div>
</div>