<?php
/**
 * Created by PhpStorm.
 * User: D. Manavi
 */

function extract_key_value_pairs($str) {
    $list = explode('&', $str);

    $result = array();

    for ($i=0 ; $i<count($list) ; $i++) {

        $pair = explode('=', $list[$i]);

        $result[$pair[0]] = $pair[1];
    }

    return $result;
}

function to_key_value_pair($arr) {

    $output = '';

    $isFirst = true;

    foreach($arr as $key => $value) {

        if(!$isFirst) {
            $output = $output . '&';
        }

        $output = $output . $key . '=' . $value;

        $isFirst = false;
    }

    return $output;
}