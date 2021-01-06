<?php
function getImageCdn($url, $width = 0, $height = 0, $fitIn = true, $webp = true) {
    if (!$url) {
        $url = "https://shopbay.vn/images/logo.png";
        $fitIn = true;
    }
    if (substr($url, -3) == 'svg') {
        return $url;
    }
    $originUrl = $url;
    if (substr($url, 0, 4) == 'http') {
        $url = str_replace('https://', '', $url);
        $url = str_replace('http://', '', $url);
    } else {
        $url = config('app.domain') . $url;
    }
    if ($webp && isset($_SERVER['HTTP_ACCEPT'])) {
        if (strpos($_SERVER['HTTP_ACCEPT'], 'image/webp') !== false) {
            $webp = true;
        } else {
            $webp = false;
        }
    }

    $baseCdnUrl = "https://static.shopbay.vn/unsafe/";
    $fitIn = ($fitIn && $width && $height);
    // $fitIn = false;
    if ($fitIn) {
        $baseCdnUrl .= "fit-in/";
    }
    if ($width || $height) {
        $baseCdnUrl .= $width . "x" . $height . "/";
    }
    if ($fitIn || $webp) {
        $baseCdnUrl .= "filters";
    }
    if ($fitIn) {
        $baseCdnUrl .= "-fill-fff-";
    }
    if ($webp) {
        $baseCdnUrl .= "-format-webp-";
    }
    if ($fitIn || $webp) {
        $baseCdnUrl .= "/";
    }
    $baseCdnUrl .= $url;
    return $baseCdnUrl;
}

function get_client_ip() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if (isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}
