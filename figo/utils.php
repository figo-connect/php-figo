<?php

function parse_api_endpoint($api_endpoint)   {
    $api_endpoint = rtrim($api_endpoint, "/");
    $length = strlen($api_endpoint);
    if (substr($api_endpoint, 0, $length) != "https://")    {
        $api_endpoint = "https://" . $api_endpoint;
    }
    $api_url = parse_url($api_endpoint);
    $api_url["path"] = "";
    return $api_url;
}
