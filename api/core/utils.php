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

function utils_renderPhpToString($file, $vars = null)
{
    if (is_array($vars) && !empty($vars)) {
        extract($vars);
    }

    ob_start();

    include $file;

    return ob_get_clean();
}

function utils_encrypt($plainText, $key){

    $key_size =  strlen($key);

    # create a random IV to use with CBC encoding
    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

    # creates a cipher text compatible with AES (Rijndael block size = 128)
    # to keep the text confidential
    # only suitable for encoded input that never ends with value 00h
    # (because of default zero padding)
    $ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $plainText, MCRYPT_MODE_CBC, $iv);

    # prepend the IV for it to be available for decryption
    $ciphertext = $iv . $ciphertext;

    # encode the resulting cipher text so it can be represented by a string
    $ciphertext_base64 = base64_encode($ciphertext);

    // return encrypted text to the caller
    return $ciphertext_base64;
}

function utils_decrypt($cipherText, $key) {
    $key_size =  strlen($key);

    # create a random IV to use with CBC encoding
    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

    $ciphertext_dec = base64_decode($cipherText);

    # retrieves the IV, iv_size should be created using mcrypt_get_iv_size()
    $iv_dec = substr($ciphertext_dec, 0, $iv_size);

    # retrieves the cipher text (everything except the $iv_size in the front)
    $ciphertext_dec = substr($ciphertext_dec, $iv_size);

    # may remove 00h valued characters from end of plain text
    $plaintext_dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);

    // return decrypted text to the caller
    return $plaintext_dec;
}

function utils_base64_to_jpeg($base64_string, $output_file) {

    $ifp = fopen($output_file, "wb");

    $data = explode(',', $base64_string);

    fwrite($ifp, base64_decode($data[1]));

    fclose($ifp);

    return true;
}

function utils_is_date($input){

    $d = DateTime::createFromFormat('Y-m-d', $input);

    return $d && $d->format('Y-m-d') === $input;
}