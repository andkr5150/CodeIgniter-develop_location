<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ip_location extends CI_Controller
{
    public function index()
    {
        $this->load->library('user_agent');

        if ($this->agent->is_browser())
        {
            $agent = $this->agent->browser().' '.$this->agent->version();
        }
        elseif ($this->agent->is_robot())
        {
            $agent = $this->agent->robot();
        }
        elseif ($this->agent->is_mobile())
        {
            $agent = $this->agent->mobile();
        }
        else
        {
            $agent = 'Unidentified User Agent';
        }

        $data['agent'] = $agent;
        $data['platform'] = $this->agent->platform();
        $data['agent_str'] = $this->agent->agent_string();

        $data['ip'] = $this->input->ip_address();

        // RU '85.114.175.250'
        // UA '212.90.37.192'
        $ip_clocation = $data['ip'];
        $info = $this->getCountryByIp($ip_clocation);

        $data['country'] = $info['country'];
        $data['city'] = $info['city'];
        $data['region'] = $info['region'];

        $this->load->view("view_location", $data);
    }

    public function getCountryByIp($ipAddress)
    {
        $ipInformation = array();
        $xmlTxt = file_get_contents("http://ipgeobase.ru:7020/geo?ip=".$ipAddress);
        $xml = iconv('CP1251', 'UTF-8', $xmlTxt);

        preg_match( '@<message>(.*?)</message>@si' , $xml , $message);
        if (isset($message[1])) {

            $ipInformation['country'] = $message[1];
            $ipInformation['city'] = $message[1];
            $ipInformation['region'] = $message[1];
            return $ipInformation;

        } else {
            preg_match( '@<country>(.*?)</country>@si' , $xml , $country );
            $ipInformation['country'] = $country[1];

            preg_match( '@<city>(.*?)</city>@si' , $xml , $city );
            $ipInformation['city'] = $city[1];

            preg_match( '@<region>(.*?)</region>@si' , $xml , $region );
            $ipInformation['region'] = $region[1];

            return $ipInformation;
        }
    }

}