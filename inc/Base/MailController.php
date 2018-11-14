<?php
/**
 * Created by PhpStorm.
 * User: sergeynosenko
 * Date: 25.09.2018
 * Time: 18:04
 */

namespace Inc\Base;

use Braintree\Exception;
use Inc\Base\BaseController;

class MailController extends BaseController
{

    private $header;

    protected $body_css = array();

    public $body;

    public $subject;

    public $mail_settings;

    public $user_data = array();

    public $course_data = array();

    public function register($data)
    {
        // get Mail Settings
        $this->mail_settings = get_option('tn_academy_plugin_mail');

        //set user and course data
        $this->user_data = $data;
        $this->setCourseData();

        $this->setCSS();

        $this->setHeader();

        $this->setBody();

        $this->setSubject();

        //add image
        add_action('phpmailer_init', array($this, 'attachInlineImage'));

        $this->send_mail();
    }

    public function setCourseData()
    {
        $this->course_data['title'] = get_post_meta($this->user_data['course_id'], 'tn-title', true);
        $this->course_data['short_desc'] = get_post_meta($this->user_data['course_id'], 'tn-desc', true);
        $this->course_data['date'] = get_post_meta($this->user_data['course_id'], 'tn-date', true);
        $this->course_data['location'] = get_post_meta($this->user_data['course_id'], 'tn-location', true);
        $this->course_data['c_leader'] = get_post_meta($this->user_data['course_id'], 'tn-course-leader', true);
        $this->course_data['price'] = get_post_meta($this->user_data['course_id'], 'tn-price', true);
        $this->course_data['last_date'] = get_post_meta($this->user_data['course_id'], 'tn-last-date', true);
        $this->course_data['det_desc'] = get_post_meta($this->user_data['course_id'], 'tn-det-desc', true);
    }

    public function setCSS()
    {
        $this->body_css['header'] = 'background: #BE0016;text-align: center;padding: 0;display: flex;width:100%;';

        $this->body_css['dib'] = 'margin: 0 auto;';

        $this->body_css['h1'] = 'padding: 0 0 20px; margin: 0;';

        $this->body_css['bg'] = 'background-color: #be0017;';

        $this->body_css['p20'] = 'padding: 20px;';

        $this->body_css['course_info'] = 'display: table-cell; padding: 0 0 5px;margin: 0;';

        $this->body_css['h3'] = 'color: #be0017;';

        $this->body_css['h3_padding'] = 'padding: 0 0 20px; margin: 0;';

        $this->body_css['course_info_desc'] = 'padding: 0 0 5px; margin: 0;';

        $this->body_css['body'] = 'padding: 25px 30px 15px 30px;background: #fff;box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.25);margin: 0 auto;';

        $this->body_css['btn'] = 'color: #fff !important;padding:0 63px;font-size: 15px;font-weight: 600;line-height: 52px;display: inline-block;text-transform: uppercase;text-align: center;background-color: #be0017;text-decoration: none;';
    }

    public function setHeader()
    {
        $from = ($this->mail_settings['email_from'] && $this->mail_settings['email_from'] != "") ? $this->mail_settings['email_from'] : __('Travel News Academy', TNA_PLUGIN_NAME);

        // write the email content
        $this->header .= "MIME-Version: 1.0\n";
        $this->header .= "Content-Type: text/html; charset=utf-8\n";
        $this->header .= "From: " . $from . " <" . $this->admin_email . ">";
    }

    public function setBody()
    {
        $front_page = ($this->mail_settings['email_academy_link'] && $this->mail_settings['email_academy_link'] != "") ? get_the_permalink($this->mail_settings['email_academy_link']) : get_site_url();

        $this->body .= '<html><head>';
        $this->body .= '</head>';
        $this->body .= '<body style="' . $this->body_css['bg'] . ' ' . $this->body_css['p20'] . '">';
        $this->body .= '<table width="500px" class="container"  cellspacing="0" cellpadding="0" style="' . $this->body_css['body'] . '"><tbody>';
        $this->body .= '<tr class="header" style="' . $this->body_css['header'] . '"><td style="' . $this->body_css['dib'] . '"><a href="' . $front_page . '" target="_blank"><img src="cid:tn_logo" width="440" height="94"></a></td></tr>';
        $this->body .= '</tbody></table>';
        $this->body .= '<table width="500px" class="container"  cellspacing="0" cellpadding="0" style="' . $this->body_css['body'] . '"><tbody>';
        if ($this->mail_settings['email_confirmation'] && $this->mail_settings['email_confirmation'] != ""):
            $this->body .= '<tr>';
            $this->body .= '<td class="course_info_desc">';
            $this->body .= $this->wpautop_with_class(htmlspecialchars_decode(wpautop($this->mail_settings['email_confirmation'])));
            $this->body .= '</td>';
            $this->body .= '</tr>';

            $this->body .= '<tr>';
            $this->body .= '<td class="hr">';
            $this->body .= '<hr>';
            $this->body .= '</td>';
            $this->body .= '</tr>';
        endif;

        if ($this->mail_settings['email_course_data']):
            if ($this->course_data['title']):
                $this->body .= '<tr>';
                $this->body .= '<td>';
                $this->body .= '<h1 style="' . $this->body_css['h1'] . '">' . $this->course_data['title'] . '</h1>';
                $this->body .= '</td>';
                $this->body .= '</tr>';
            endif;

            if ($this->course_data['short_desc']):
                $this->body .= '<tr>';
                $this->body .= '<td class="course_info">';
                $this->body .= '<p style="' . $this->body_css['course_info_desc'] . '">' . $this->course_data['short_desc'] . '</p>';
                $this->body .= '</td>';
                $this->body .= '</tr>';
            endif;

            if ($this->course_data['date']):
                $this->body .= '<tr>';
                $this->body .= '<td class="course_info">';
                $this->body .= '<h3 style="' . $this->body_css['course_info'] . ' ' . $this->body_css['h3'] . '">' . __('Date: ', TNA_PLUGIN_NAME) . '</h3>';
                $this->body .= '<p style="' . $this->body_css['course_info'] . '">' . $this->course_data['date'] . '</p>';
                $this->body .= '</td>';
                $this->body .= '</tr>';
            endif;

            if ($this->course_data['location']):

                $this->body .= '<tr>';
                $this->body .= '<td class="course_info">';
                $this->body .= '<h3 style="' . $this->body_css['course_info'] . ' ' . $this->body_css['h3'] . '">' . __("Location: ", TNA_PLUGIN_NAME) . '</h3>';
                $this->body .= '<p style="' . $this->body_css['course_info'] . '">' . $this->course_data['location'] . '</p>';
                $this->body .= '</td>';
                $this->body .= '</tr>';
            endif;

            if ($this->course_data['c_leader']):

                $this->body .= '<tr>';
                $this->body .= '<td class="course_info">';
                $this->body .= '<h3 style="' . $this->body_css['course_info'] . ' ' . $this->body_css['h3'] . '">' . __('Course Leader: ', TNA_PLUGIN_NAME) . '</h3>';
                $this->body .= '<p style="' . $this->body_css['course_info'] . '">' . $this->course_data['c_leader'] . '</p>';
                $this->body .= '</td>';
                $this->body .= '</tr>';
            endif;

            if ($this->course_data['price']):

                $this->body .= '<tr>';
                $this->body .= '<td class="course_info">';
                $this->body .= '<h3 style="' . $this->body_css['course_info'] . ' ' . $this->body_css['h3'] . '">' . __('Price: ', TNA_PLUGIN_NAME) . '</h3>';
                $this->body .= '<p style="' . $this->body_css['course_info'] . '">' . $this->course_data['price'] . '</p>';
                $this->body .= '</td>';
                $this->body .= '</tr>';
            endif;

            if ($this->course_data['last_date']):
                $this->body .= '<tr>';
                $this->body .= '<td class="course_info">';
                $this->body .= '<h3 style="' . $this->body_css['course_info'] . ' ' . $this->body_css['h3'] . '">' . __('Last day of registration: ', TNA_PLUGIN_NAME) . '</h3>';
                $this->body .= '<p style="' . $this->body_css['course_info'] . '">' . $this->course_data['last_date'] . '</p>';
                $this->body .= '</td>';
                $this->body .= '</tr>';
            endif;

            if ($this->course_data['det_desc']):
                $this->body .= '<tr>';
                $this->body .= '<td class="course_info_desc">';
                $this->body .= '<h3 style="' . $this->body_css['h3'] . ' ' . $this->body_css['h3_padding'] . '">' . __('Detailed Description ', TNA_PLUGIN_NAME) . '</h3>';
                $this->body .= $this->wpautop_with_class(wpautop($this->course_data['det_desc']));
                $this->body .= '</td>';
                $this->body .= '</tr>';
            endif;

            $this->body .= '<tr>';
            $this->body .= '<td class="btn">';
            $this->body .= '<a href="' . get_permalink($this->user_data['course_id']) . '" class="tn-registration" target="_blank" style="' . $this->body_css['btn'] . '">' . __('Check Course\'s Info', TNA_PLUGIN_NAME) . '</a>';
            $this->body .= '</td>';
            $this->body .= '</tr>';

            $this->body .= '<tr>';
            $this->body .= '<td class="hr">';
            $this->body .= '<hr>';
            $this->body .= '</td>';
            $this->body .= '</tr>';
        endif;

        if ($this->mail_settings['email_participant_data']):
            if ($this->user_data['first_name'] || $this->user_data['last_name']):
                $this->body .= '<tr>';
                $this->body .= '<td>';
                $this->body .= '<h1 style="' . $this->body_css['h1'] . '">' . $this->user_data['first_name'] . " " . $this->user_data['last_name'] . '</h1>';
                $this->body .= '</td>';
                $this->body .= '</tr>';
            endif;

            if ($this->user_data['address']):
                $this->body .= '<tr>';
                $this->body .= '<td class="course_info">';
                $this->body .= '<h3 style="' . $this->body_css['h3'] . ' ' . $this->body_css['course_info'] . '">' . __('Address: ', TNA_PLUGIN_NAME) . '</h3>';
                $this->body .= '<p style="' . $this->body_css['course_info'] . '">' . $this->user_data['address'] . '</p>';
                $this->body .= '</td>';
                $this->body .= '</tr>';
            endif;

            if ($this->user_data['zip']):
                $this->body .= '<tr>';
                $this->body .= '<td class="course_info">';
                $this->body .= '<h3 style="' . $this->body_css['h3'] . ' ' . $this->body_css['course_info'] . '">' . __('ZIP: ', TNA_PLUGIN_NAME) . '</h3>';
                $this->body .= '<p style="' . $this->body_css['course_info'] . '">' . $this->user_data['zip'] . '</p>';
                $this->body .= '</td>';
                $this->body .= '</tr>';
            endif;

            if ($this->user_data['city']):
                $this->body .= '<tr>';
                $this->body .= '<td class="course_info">';
                $this->body .= '<h3 style="' . $this->body_css['h3'] . ' ' . $this->body_css['course_info'] . '">' . __("City: ", TNA_PLUGIN_NAME) . '</h3>';
                $this->body .= '<p style="' . $this->body_css['course_info'] . '">' . $this->user_data['city'] . '</p>';
                $this->body .= '</td>';
                $this->body .= '</tr>';
            endif;

            if ($this->user_data['phone']):
                $this->body .= '<tr>';
                $this->body .= '<td class="course_info">';
                $this->body .= '<h3 style="' . $this->body_css['h3'] . ' ' . $this->body_css['course_info'] . '">' . __('Phone: ', TNA_PLUGIN_NAME) . '</h3>';
                $this->body .= '<p style="' . $this->body_css['course_info'] . '">' . $this->user_data['phone'] . '</p>';
                $this->body .= '</td>';
                $this->body .= '</tr>';
            endif;

            if ($this->user_data['email']):
                $this->body .= '<tr>';
                $this->body .= '<td class="course_info">';
                $this->body .= '<h3 style="' . $this->body_css['h3'] . ' ' . $this->body_css['course_info'] . '">' . __('Email: ', TNA_PLUGIN_NAME) . '</h3>';
                $this->body .= '<p style="' . $this->body_css['course_info'] . '">' . $this->user_data['email'] . '</p>';
                $this->body .= '</td>';
                $this->body .= '</tr>';
            endif;

            if ($this->user_data['course']):
                $this->body .= '<tr>';
                $this->body .= '<td class="course_info">';
                $this->body .= '<h3 style="' . $this->body_css['h3'] . ' ' . $this->body_css['course_info'] . '">' . __('Course: ', TNA_PLUGIN_NAME) . '</h3>';
                $this->body .= '<p style="' . $this->body_css['course_info'] . '">' . $this->user_data['course'] . '</p>';
                $this->body .= '</td>';
                $this->body .= '</tr>';
            endif;

            if ($this->user_data['message']):
                $this->body .= '<tr>';
                $this->body .= '<td class="course_info_desc">';
                $this->body .= '<h3 style="' . $this->body_css['h3'] . ' ' . $this->body_css['h3_padding'] . '">' . __('What are your current job description?', TNA_PLUGIN_NAME) . '</h3>';
                $this->body .= '<p style="' . $this->body_css['course_info_desc'] . '">' . $this->user_data['message'] . '</p>';
                $this->body .= '</td>';
                $this->body .= '</tr>';
            endif;
        endif;

        $this->body .= '</tbody></table>';
        $this->body .= '</body></html>';
    }

    public function setSubject()
    {
        $subject = ($this->mail_settings['email_subject'] && $this->mail_settings['email_subject'] != "") ? $this->mail_settings['email_subject'] : __('Travel News Academy, Course', TNA_PLUGIN_NAME);
        $this->subject = $subject . ": " . $this->user_data['course'];
        $this->subject = "=?utf-8?B?" . base64_encode($this->subject) . "?=";
    }

    public function attachInlineImage()
    {
        global $phpmailer;
        $image = ($this->mail_settings['email_logo'] && $this->mail_settings['email_logo'] != "") ? $this->mail_settings['email_logo'] : get_theme_mod('header_logo', '');
        if ($image) {
            $file_info = explode('/', $image);//phpmailer will load this file
            $file_name = array_slice($file_info, -1, 1);
            $file_path = implode('/', array_slice($file_info, 4));

            $file = dirname(__FILE__, 5) . "/" . $file_path;
            $uid = 'tn_logo'; //will map it to this UID
            $name = $file_name[0]; //this will be the file name for the attachment

            $phpmailer->isSMTP();
            $phpmailer->Host = 'smtp.mailtrap.io';
            $phpmailer->SMTPAuth = true;
            $phpmailer->Port = 2525;
            $phpmailer->Username = 'ef3bb4469b1f4e';
            $phpmailer->Password = '67b76fad914e3c';
            $phpmailer->AddEmbeddedImage($file, $uid, $name);
        }
    }

    public function send_mail()
    {
        if (empty($this->user_data['email']) && $this->user_data['email'] == "") {
            throw new \Exception("The User Email is Empty!");
        }

        if (!wp_mail($this->user_data['email'], $this->subject, $this->body, $this->header)) {
            throw new \Exception("Unexpected error with Sending!!!");
        }
    }

    public function wpautop_with_class($args)
    {
        $page_cont = $args;
        $added_class = str_replace(array('<p style="', '<h1 style="', '<h2 style="', '<h3 style="', '<h4 style="', '<h5 style="', '<h6 style="', '<p>', '<h1>', '<h2>', '<h3>', '<h4>', '<h5>', '<h6>'),
            array('<p style="' . $this->body_css['course_info_desc'],
                '<h1 style="' . $this->body_css['h3_padding'],
                '<h2 style="' . $this->body_css['h3_padding'],
                '<h3 style="' . $this->body_css['h3_padding'],
                '<h4 style="' . $this->body_css['h3_padding'],
                '<h5 style="' . $this->body_css['h3_padding'],
                '<h6 style="' . $this->body_css['h3_padding'],
                '<p style="' . $this->body_css['course_info_desc'] . '">',
                '<h1 style="' . $this->body_css['h3_padding'] . '">',
                '<h2 style="' . $this->body_css['h3_padding'] . '">',
                '<h3 style="' . $this->body_css['h3_padding'] . '">',
                '<h4 style="' . $this->body_css['h3_padding'] . '">',
                '<h5 style="' . $this->body_css['h3_padding'] . '">',
                '<h6 style="' . $this->body_css['h3_padding'] . '">'
            ),
            $page_cont);
        return $added_class;
    }
}