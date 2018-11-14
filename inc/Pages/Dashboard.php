<?php 
/**
 * @package  tn_academyPlugin
 */
namespace Inc\Pages;

use Inc\Api\SettingsApi;
use Inc\Base\BaseController;
use Inc\Api\Callbacks\AdminCallbacks;
use Inc\Api\Callbacks\ManagerCallbacks;

class Dashboard extends BaseController
{
	public $settings;

	public $callbacks;

	public $callbacks_mngr;

	public $pages = array();

	public function register()
	{
		$this->settings = new SettingsApi();

		$this->callbacks = new AdminCallbacks();

		$this->callbacks_mngr = new ManagerCallbacks();

		$this->setPages();

		$this->setSettings();
		$this->setSections();
		$this->setFields();

		$this->settings->addPages( $this->pages )->withSubPage( __('Dashboard', TNA_PLUGIN_NAME) )->register();
	}

	public function setPages() 
	{
		$this->pages = array(
			array(
				'page_title' => __('TN Academy Plugin', TNA_PLUGIN_NAME),
				'menu_title' => __('TN Academy', TNA_PLUGIN_NAME),
				'capability' => 'manage_options', 
				'menu_slug' => 'tn_academy_plugin',
				'callback' => array( $this->callbacks, 'adminDashboard' ), 
				'icon_url' => 'dashicons-store', 
				'position' => 9
			)
		);
	}

	public function setSettings()
	{
		$args = array(
			array(
				'option_group' => 'tn_academy_plugin_settings',
				'option_name' => 'tn_academy_plugin',
				'callback' => array( $this->callbacks_mngr, 'checkboxSanitize' )
			),
            array(
                'option_group' => 'tn_academy_plugin_btn_settings',
                'option_name' => 'tn_academy_plugin_btn',
                'callback' => array( $this->callbacks_mngr, 'btnSanitize' )
            ),
            array(
                'option_group' => 'tn_academy_plugin_mail_settings',
                'option_name' => 'tn_academy_plugin_mail',
                'callback' => array( $this->callbacks_mngr, 'mailSanitize' )
            )
		);

		$this->settings->setSettings( $args );
	}

	public function setSections()
	{
		$args = array(
			array(
				'id' => 'tn_academy_admin_index',
				'title' => __('Settings Manager', TNA_PLUGIN_NAME),
				'callback' => array( $this->callbacks_mngr, 'adminSectionManager' ),
				'page' => 'tn_academy_plugin'
			),
            array(
                'id' => 'tn_academy_admin_btn',
                'title' => __('Button Manager', TNA_PLUGIN_NAME),
                'callback' => array( $this->callbacks_mngr, 'adminSectionBtnManager' ),
                'page' => 'tn_academy_plugin_btn'
            ),
            array(
                'id' => 'tn_academy_admin_mail',
                'title' => __('Mail Manager', TNA_PLUGIN_NAME),
                'callback' => array( $this->callbacks_mngr, 'adminSectionMailManager' ),
                'page' => 'tn_academy_plugin_mail'
            )
		);

		$this->settings->setSections( $args );
	}

	public function setFields()
	{
		$args = array();

		// Add Checkboxes to manage Settings
		foreach ( $this->managers as $key => $value ) {
			$args[] = array(
				'id' => $key,
				'title' => $value,
				'callback' => array( $this->callbacks_mngr, 'checkboxField' ),
				'page' => 'tn_academy_plugin',
				'section' => 'tn_academy_admin_index',
				'args' => array(
					'option_name' => 'tn_academy_plugin',
					'label_for' => $key,
					'class' => 'ui-toggle'
				)
			);
		}

		// Add Fields to Button Settings
		$args[] = array(
            'id' => 'btn_name',
            'title' => __('Wysiwyg Button Name', TNA_PLUGIN_NAME),
            'callback' => array( $this->callbacks_mngr, 'textField' ),
            'page' => 'tn_academy_plugin_btn',
            'section' => 'tn_academy_admin_btn',
            'args' => array(
                'option_name' => 'tn_academy_plugin_btn',
                'label_for' => 'btn_name',
                'class' => '',
                'placeholder' => __('Course Button', TNA_PLUGIN_NAME)
            )
        );
        $args[] = array(
            'id' => 'add_btn_title',
            'title' => __('Button Title', TNA_PLUGIN_NAME),
            'callback' => array( $this->callbacks_mngr, 'textField' ),
            'page' => 'tn_academy_plugin_btn',
            'section' => 'tn_academy_admin_btn',
            'args' => array(
                'option_name' => 'tn_academy_plugin_btn',
                'label_for' => 'add_btn_title',
                'class' => '',
                'placeholder' => __('Add Course Button', TNA_PLUGIN_NAME)
            )
        );
        $args[] = array(
            'id' => 'add_btn_name',
            'title' => __('Button Name Field', TNA_PLUGIN_NAME),
            'callback' => array( $this->callbacks_mngr, 'textField' ),
            'page' => 'tn_academy_plugin_btn',
            'section' => 'tn_academy_admin_btn',
            'args' => array(
                'option_name' => 'tn_academy_plugin_btn',
                'label_for' => 'add_btn_name',
                'class' => '',
                'placeholder' => __('Course Name', TNA_PLUGIN_NAME)
            )
        );
        $args[] = array(
            'id' => 'add_btn_desc',
            'title' => __('Button Short Description Field', TNA_PLUGIN_NAME),
            'callback' => array( $this->callbacks_mngr, 'textField' ),
            'page' => 'tn_academy_plugin_btn',
            'section' => 'tn_academy_admin_btn',
            'args' => array(
                'option_name' => 'tn_academy_plugin_btn',
                'label_for' => 'add_btn_desc',
                'class' => '',
                'placeholder' => __('Course Short Description', TNA_PLUGIN_NAME)
            )
        );
        $args[] = array(
            'id' => 'add_text_under_link',
            'title' => __('Text under Link', TNA_PLUGIN_NAME),
            'callback' => array( $this->callbacks_mngr, 'textField' ),
            'page' => 'tn_academy_plugin_btn',
            'section' => 'tn_academy_admin_btn',
            'args' => array(
                'option_name' => 'tn_academy_plugin_btn',
                'label_for' => 'add_text_under_link',
                'class' => '',
                'placeholder' => __('You may create and manage courses', TNA_PLUGIN_NAME)
            )
        );

        // Add Fields to Mail Settings
        $args[] = array(
            'id' => 'email_course_data',
            'title' => __('Include Course data in Email?', TNA_PLUGIN_NAME),
            'callback' => array( $this->callbacks_mngr, 'checkboxField' ),
            'page' => 'tn_academy_plugin_mail',
            'section' => 'tn_academy_admin_mail',
            'args' => array(
                'option_name' => 'tn_academy_plugin_mail',
                'label_for' => 'email_course_data',
                'class' => 'ui-toggle'
            )
        );
        $args[] = array(
            'id' => 'email_participant_data',
            'title' => __('Include Participant data in Email?', TNA_PLUGIN_NAME),
            'callback' => array( $this->callbacks_mngr, 'checkboxField' ),
            'page' => 'tn_academy_plugin_mail',
            'section' => 'tn_academy_admin_mail',
            'args' => array(
                'option_name' => 'tn_academy_plugin_mail',
                'label_for' => 'email_participant_data',
                'class' => 'ui-toggle'
            )
        );
        $args[] = array(
            'id' => 'email_from',
            'title' => __('From:', TNA_PLUGIN_NAME),
            'callback' => array( $this->callbacks_mngr, 'textField' ),
            'page' => 'tn_academy_plugin_mail',
            'section' => 'tn_academy_admin_mail',
            'args' => array(
                'option_name' => 'tn_academy_plugin_mail',
                'label_for' => 'email_from',
                'class' => '',
                'placeholder' => __('Travel News Academy', TNA_PLUGIN_NAME),
                'admin_email' =>  $this->admin_email
            )
        );
        $args[] = array(
            'id' => 'email_subject',
            'title' => __('Subject:', TNA_PLUGIN_NAME),
            'callback' => array( $this->callbacks_mngr, 'textField' ),
            'page' => 'tn_academy_plugin_mail',
            'section' => 'tn_academy_admin_mail',
            'args' => array(
                'option_name' => 'tn_academy_plugin_mail',
                'label_for' => 'email_subject',
                'class' => '',
                'placeholder' => __('Travel News Academy, Course', TNA_PLUGIN_NAME)
            )
        );
        $args[] = array(
            'id' => 'email_academy_link',
            'title' => __('Logo Link on Main Page:', TNA_PLUGIN_NAME),
            'callback' => array( $this->callbacks_mngr, 'selectField' ),
            'page' => 'tn_academy_plugin_mail',
            'section' => 'tn_academy_admin_mail',
            'args' => array(
                'option_name' => 'tn_academy_plugin_mail',
                'label_for' => 'email_academy_link',
                'class' => '',
                'selectFields' => $this->getAllPosts('page')
            )
        );
        $args[] = array(
            'id' => 'email_logo',
            'title' => __('Email Logo:', TNA_PLUGIN_NAME),
            'callback' => array( $this->callbacks_mngr, 'fileUpload' ),
            'page' => 'tn_academy_plugin_mail',
            'section' => 'tn_academy_admin_mail',
            'args' => array(
                'option_name' => 'tn_academy_plugin_mail',
                'label_for' => 'email_logo',
                'class' => ''
            )
        );
        $args[] = array(
            'id' => 'email_confirmation',
            'title' => __('Email Introductory Text :', TNA_PLUGIN_NAME),
            'callback' => array( $this->callbacks_mngr, 'wysiwygField' ),
            'page' => 'tn_academy_plugin_mail',
            'section' => 'tn_academy_admin_mail',
            'args' => array(
                'option_name' => 'tn_academy_plugin_mail',
                'label_for' => 'email_confirmation',
                'class' => ''
            )
        );

		$this->settings->setFields( $args );
	}
}