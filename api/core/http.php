<?php
/**
 * Created by PhpStorm.
 * User: D. Manavi
 */

// get HTTP request body
function http_get_body(){

    // read content from body
    $content = file_get_contents('php://input');

    // check content encoding
    switch(http_get_content_encoding()){

        // json content encoding
        case "json": {

            // return content as json
            return json_decode($content);
        }

        // xml content encoding
        case "xml": {

            // return content as xml
            return new SimpleXMLElement($content);
        }
    }
}

function http_set_body($model) {

    // check content encoding
    switch(http_get_content_encoding()){

        // json content encoding
        case "json": {

            // return content as json
            echo json_encode($model);

            break;
        }

        // xml content encoding
        case "xml": {

            // return content as xml
            echo $model->asXML();

            break;
        }

        // invalid content-type
        default: {

            die("Invalid content-type");
        }
    }
}

function http_get_content_encoding() {

    $headers = apache_request_headers();

    foreach ($headers as $header => $value) {

        if(strtolower($header) == "content-encoding"){

            switch(strtolower($value)){
                case "application/json": {

                    return "json";
                }

                case "application/xml": {

                    return "xml";
                }
            }
        }
    }

    return "json";
}

function http_get_method() {

    return strtolower($_SERVER['REQUEST_METHOD']);
}

$method = http_get_method();

if(strtolower($method) == 'options') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']) && $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] == 'GET') {

    }

    header_remove();

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Methods: OPTIONS, GET, POST, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type, Depth, User-Agent, X-File-Size, X-Requested-With, If-Modified-Since, X-File-Name, Cache-Control");

    exit;
}