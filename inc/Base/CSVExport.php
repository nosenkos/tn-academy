<?php
/**
 * Created by PhpStorm.
 * User: sergeynosenko
 * Date: 24.09.2018
 * Time: 4:59
 */

namespace Inc\Base;


class CSVExport
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
        if (isset($_POST['export']) && $_POST['export'] == 'tn-member-download-list-csv') {
            if(isset($_POST['course_list']) && !empty($_POST['course_list']) && $_POST['course_list'] != "") {
                $this->course = str_replace(' ', '_', strtolower($_POST['course_list']));
            }
            $csv = $this->generate_csv();

            $date = date('m/d/Y h:i:s', time());
            $filename = "exprot_csv_" . $this->course . "_" . $date;

            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private", false);
            header("Content-Type: application/octet-stream");
            header("Content-Disposition: attachment; filename=\"" . $filename . ".csv\";");
            header("Content-Transfer-Encoding: binary");

            echo $csv;
            exit;
        }
    }

    /**
     * Converting data to CSV
     */
    public function generate_csv()
    {
        $csv_output = '';
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

            if($this->course == $course){
                $datas[] = array(
                    'First Name' => $first_name,
                    'Last Name' => $last_name,
                    'Address' => $address,
                    'ZIP' => $zip,
                    'City'  => $city,
                    'Phone' => $phone,
                    'Email' => $email,
                    'Message' => $message,
                    'Course' => $course,
                    'Registration Date' => $date,
                );
            } elseif($this->course == "All"){
                $datas[] = array(
                    'First Name' => $first_name,
                    'Last Name' => $last_name,
                    'Address' => $address,
                    'ZIP' => $zip,
                    'City'  => $city,
                    'Phone' => $phone,
                    'Email' => $email,
                    'Message' => $message,
                    'Course' => $course,
                    'Registration Date' => $date,
                );
            }

        }

        $heading = false;
        if (!empty($datas))
            foreach ($datas as $row) {
                if (!$heading) {
                    // display field/column names as a first row
                    $csv_output .= implode(",", array_keys($row)) . "\n";
                    $heading = true;
                }
                $csv_output .= implode(",", array_values($row)) . "\n";
            }

        return $csv_output;
    }
}