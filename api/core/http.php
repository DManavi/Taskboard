<?php
/**
 * Created by PhpStorm.
 * User: D. Manavi
 */

header("Access-Control-Allow-Origin: *");

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