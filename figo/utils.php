<?php

function parse_api_endpoint($api_endpoint)   {
    $api_endpoint = rtrim($api_endpoint, "/");
    if (substr($api_endpoint, 0, 8) != "https://")    {
        $api_endpoint = "https://" . $api_endpoint;
    }
    $api_url = parse_url($api_endpoint);
    if (array_key_exists("path", $api_url) == false)    {
        $api_url["path"] = "";
    }
    return $api_url;
}
