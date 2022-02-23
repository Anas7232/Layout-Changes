<?php // 	print_r('<pre>');// 	print_r($this->session->userdata());// 	exit();?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <meta name="keywords" content='<?= $keywords ?>'>
    <meta name="description" content='<?= $description ?>'>
    <?php echo '<link rel="canonical" href="' . base_url($this->uri->uri_string()) . '" />';
    $cookie_lang = $this->input->cookie('language', TRUE);
    $path = $is_rtl = "";
    if (!empty($cookie_lang)) {
        $language = get_languages(0, $cookie_lang, 0, 1);
        if (!empty($language)) {
            $path = ($language[0]['is_rtl'] == 1) ? 'rtl/' : "";
            $is_rtl =  ($language[0]['is_rtl'] == 1) ? true : false;
        }
    } else {
        /* read the default language */
        $lang = $this->config->item('language');
        $language = get_languages(0, $lang, 0, 1);
        if (!empty($language)) {
            $path = ($language[0]['is_rtl'] == 1) ? 'rtl/' : "";
            $is_rtl =  ($language[0]['is_rtl'] == 1) ? true : false;
        }
    }
    $data['is_rtl'] = $is_rtl;
    $this->load->view('front-end/' . THEME . '/include-css', $data); ?>
</head>

<body id="body" data-is-rtl='<?= $is_rtl ?>'>
    <?php $this->load->view('front-end/' . THEME . '/header'); ?>
    <?php $this->load->view('front-end/' . THEME . '/pages/' . $main_page); ?>
    <?php $this->load->view('front-end/' . THEME . '/footer'); ?>
    <?php $this->load->view('front-end/' . THEME . '/include-script'); ?>
</body>

</html>