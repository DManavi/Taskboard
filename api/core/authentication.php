<?php
/**
 * Created by PhpStorm.
 * User: D. Manavi
 */

require_once 'utils.php';

// define encryption key constant
define('ENC_KEY', pack('H*', "bce04b7e103a0cd8b54763051cef08bc55afe029fdebae5e1d417e2ffb2300a3"));

function authentication_auto_login(){

    // create output variable
    $user = [
        "loggedIn" => false,
        "userId" => null
    ];

    // if authentication cookie found on request
    if (isset($_COOKIE['auth'])) {

        // get cookie content
        $cookie = $_COOKIE['auth'];

        // decrypt token
        $data = authentication_decrypt($cookie);

        // extract key/value pairs from string
        $data = extract_key_value_pairs($data);

        // if userId exist in data
        if(isset($data["userId"])) {

            // check user existence
            $userExist = db_select_scalar('SELECT COUNT(Id) FROM Users WHERE Id=\''.$data['userId'].'\'') == 1;

            // if userId exist in database
            if($userExist) {

                // set cookie to expire after two weeks
                setcookie('auth', $cookie, time() + 1209600); // 2x7x24x3600

                // set current user id
                $user["userId"] = $data['userId'];

                // set logged-in
                $user["loggedIn"] = true;
            }
            else {

                // log current user out
                authentication_logout();
            }
        }
    }

    // return loaded user to the caller
    return $user;
}

function authentication_login($userId) {

    // create cookie data
    $data = [
        "userId" => $userId
    ];

    // set cookie value
    $cookie = authentication_encrypt( to_key_value_pair($data) );

    // set cookie to expire after two weeks
    setcookie('auth', $cookie, time() + 1209600); // 2x7x24x3600
}

function authentication_logout() {

    // logout
    unset($_COOKIE['auth']);
}

function authentication_encrypt($plainText){

    $key_size =  strlen(ENC_KEY);

    # create a random IV to use with CBC encoding
    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

    # creates a cipher text compatible with AES (Rijndael block size = 128)
    # to keep the text confidential
    # only suitable for encoded input that never ends with value 00h
    # (because of default zero padding)
    $ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, ENC_KEY, $plainText, MCRYPT_MODE_CBC, $iv);

    # prepend the IV for it to be available for decryption
    $ciphertext = $iv . $ciphertext;

    # encode the resulting cipher text so it can be represented by a string
    $ciphertext_base64 = base64_encode($ciphertext);

    // return encrypted text to the caller
    return $ciphertext_base64;
}

function authentication_decrypt($cipherText) {
    $key_size =  strlen(ENC_KEY);

    # create a random IV to use with CBC encoding
    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

    $ciphertext_dec = base64_decode($cipherText);

    # retrieves the IV, iv_size should be created using mcrypt_get_iv_size()
    $iv_dec = substr($ciphertext_dec, 0, $iv_size);

    # retrieves the cipher text (everything except the $iv_size in the front)
    $ciphertext_dec = substr($ciphertext_dec, $iv_size);

    # may remove 00h valued characters from end of plain text
    $plaintext_dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, ENC_KEY, $ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);

    // return decrypted text to the caller
    return $plaintext_dec;
}

// auto login user
$user = authentication_auto_login();