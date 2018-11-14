<?php
/**
 * Created by PhpStorm.
 * User: sergeynosenko
 * Date: 24.09.2018
 * Time: 4:59
 */

namespace Inc\Base;


class XLSExport
{
    /**
     * Constructor
     */
    /**
     * Constructor
     */

    public $course;

    public function register()
    {
        if (isset($_POST['export']) && $_POST['export'] == 'tn-member-download-list-xls') {
            if (isset($_POST['course_list']) && !empty($_POST['course_list']) && $_POST['course_list'] != "") {
                $this->course = str_replace(' ', '_', strtolower($_POST['course_list']));
            }
            $date = date('m/d/Y-h:i:s', time());
            $filename = "exprot_xls_" . $this->course . "_" . $date;

            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=\"" . $filename . ".xls\";");

            $xls = $this->generate_xls();
            echo $xls;
            exit;
        }
    }

    function cleanData(&$str)
    {
        $str = preg_replace("/\t/", "\\t", $str);
        $str = preg_replace("/\r?\n/", "\\n", $str);
        if (strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
    }

    /**
     * Converting data to XLS
     */
    public function generate_xls()
    {
        $xls_output = '';
        $datas = [];
        $members = get_posts(array('post_type' => 'member', 'numberposts' => -1));
        foreach ($members as $member) {
            $data = get_post_meta($member->ID, '_tn_academy_member_key', true);
            $first_name = isset($data['first_name']) ? $data['first_name'] : '';
            $last_name = isset($data['last_name']) ? $data['last_name'] : '';
            $address = isset($data['address']) ? $data['address'] : '';
            $zip = isset($data['zip']) ? $data['zip'] : '';
            $city = isset($data['city']) ? $data['city'] : '';
            $phone = isset($data['phone']) ? $data['phone'] : '';
            $message = isset($data['message']) ? $data['message'] : '';
            $email = isset($data['email']) ? $data['email'] : '';
            $course = isset($data['course']) ? $data['course'] : '';
            $date = get_post($member->ID)->post_date_gmt;

            if ($this->course == $course) {
                $datas[] = array(
                    'First Name' => $first_name,
                    'Last Name' => $last_name,
                    'Address' => $address,
                    'ZIP' => $zip,
                    'City' => $city,
                    'Phone' => $phone,
                    'Email' => $email,
                    'Message' => $message,
                    'Course' => $course,
                    'Registration Date' => $date,
                );
            } elseif ($this->course == "All") {
                $datas[] = array(
                    'First Name' => $first_name,
                    'Last Name' => $last_name,
                    'Address' => $address,
                    'ZIP' => $zip,
                    'City' => $city,
                    'Phone' => $phone,
                    'Email' => $email,
                    'Message' => $message,
                    'Course' => $course,
                    'Registration Date' => $date,
                );
            }
        }

        $flag = false;
        foreach ($datas as $row) {
            if (!$flag) {
                // display field/column names as a first row
                $xls_output .= implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            array_walk($row, array($this, 'cleanData'));
            $xls_output .= implode("\t", array_values($row)) . "\r\n";
        }

        return $xls_output;
    }
}