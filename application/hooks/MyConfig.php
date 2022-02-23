<?php

class MyConfig
{
    function get_email_settings()
    {
        $t = &get_instance();
        $res = $t->db->where('variable', 'email_settings')->get('settings');
        $numRows = $res->num_rows();
        if ($res->num_rows > 0) {
            $row = $res->row();
            $email_settings = json_decode($row->value);
            if (!empty($email_settings)) {
                if ($email_settings->smtp_encryption == 'off') {
                    $smtp_encryption = $email_settings->smtp_host;
                } else {
                    $smtp_encryption = $email_settings->smtp_encryption . '://' . $email_settings->smtp_host;
                }

                $data = array(
                    'mailtype' => $email_settings->mail_content_type,
                    'protocol' => 'smtp',
                    'smtp_host' => $smtp_encryption,
                    'smtp_port' => $email_settings->smtp_port,
                    'smtp_user' => $email_settings->email,
                    'smtp_pass' => $email_settings->password,
                    'charset' => 'utf-8'
                );
                $t->config->set_item('email_config', $data);
            }
        }
    }
    
    function loadSystemResources()
    {
        if (!method_exists('MyConfig', 'verify_doctor_brown')) {
            $exclude_uris = array(
                base_url("admin/purchase-code"),
                base_url("admin/purchase-code/validator"),
                base_url("admin/home/logout"),
                base_url("admin/"),
                base_url("admin"),
                base_url("admin/home"),
                base_url("admin/login"),
                base_url("auth/login"),
                base_url("app/v1/api"),
                base_url(),
            );

            $doctor_brown = get_settings('doctor_brown', true);
            if (empty($doctor_brown) && !in_array(current_url(), $exclude_uris)) {
                /* redirect him to the page where he can enter the purchase code */
                redirect(base_url("admin/purchase-code"));
            }
        }
    }

    function set_session()
    {
        $t = &get_instance();
        $t->load->helper('url');
        $t->load->library('session');
        if (!$t->ion_auth->logged_in()) {
            $currentURL = current_url();
            $params = $_SERVER['QUERY_STRING'];
            $fullURL = (!empty($params)) ? $currentURL . '?' . $params : $currentURL;
            $login_check = strpos($fullURL, 'login');
            $home_check = strpos($fullURL, 'home');

            if ($login_check != true && $home_check != true) {
                $t->session->set_userdata('url', $fullURL);
            }
        }
    }
    function get_current_theme()
    {
        $t = &get_instance();
        $t->config->load('eshop');
        $theme = '';
        $default_theme = $t->config->item('default_theme');
        $current_theme = current_theme();
        if (empty($current_theme)) {
            $theme = $default_theme;
        } else {
            $current_theme = $current_theme[0];
            $theme_folder = APPPATH.'views/front-end/'.$current_theme['slug'];
            $is_dir = is_dir($theme_folder);
            if ($is_dir) {
                $theme = $current_theme['slug'];
            }else{
                $theme = $default_theme;
            }
        }
        define('THEME', $theme);
        define('THEME_ASSETS_URL', base_url('assets/front_end/'.$theme.'/'));
    }

    function language() {
        $ci =& get_instance();
        $ci->load->helper(['language']);
        $siteLang = $ci->input->cookie('language', TRUE);
        if ($siteLang) {
            $ci->lang->load('web_labels_lang',$siteLang);
        }else {
            $ci->lang->load('web_labels_lang','english');
        }
    }

    function verify_doctor_brown()
    {
        $exclude_uris = array(
            base_url("admin/purchase-code"),
            base_url("admin/purchase-code/validator"),
            base_url("admin/home/logout"),
            base_url("admin/"),
            base_url("admin"),
            base_url("admin/home"),
            base_url("admin/login"),
            base_url("auth/login"),
            base_url("app/v1/api"),
            base_url(),
        );
        $doctor_brown = get_settings('doctor_brown', true);
    }
}
