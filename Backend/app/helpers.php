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
        if( strpos( $_SERVER['HTTP_ACCEPT'], 'image/webp' ) !== false ) {
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
