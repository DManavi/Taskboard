<?php
/**
 * Created by PhpStorm.
 * User: D. Manavi
 */

require_once 'utils.php';

// define encryption key constant
define('AUTH_ENC_KEY', pack('H*', "bce04b7e103a0cd8b54763051cef08bc55afe029fdebae5e1d417e2ffb2300a3"));

function authentication_auto_login(){

    // create output variable
    $user = [
        "loggedIn" => false,
        "userId" => null
    ];

    // if user has auth session
    if(isset($_SESSION['auth'])) {

        // read authentication data from session
        $user = $_SESSION['auth'];
    }
    else {

        // if authentication cookie found on request
        if (isset($_COOKIE['auth'])) {

            // get cookie content
            $cookie = $_COOKIE['auth'];

            // decrypt token
            $data = authentication_decrypt($cookie);

            // extract key/value pairs from string
            $data = extract_key_value_pairs($data);

            // if userId exist in data
            if (isset($data["userId"])) {

                $data['userId'] = intval($data['userId']);

                // check user existence
                $userExist = db_select_scalar('SELECT COUNT(id) FROM user WHERE id='.$data['userId']) == 1;

                // if userId exist in database
                if ($userExist) {

                    // set cookie to expire after two weeks
                    // setcookie('auth', $cookie, time() + 1209600, '/', null, null, true); // 2x7x24x3600

                    // set current user id
                    $user["userId"] = $data['userId'];

                    // set logged-in
                    $user["loggedIn"] = true;

                    // assign user in session
                    $_SESSION['auth'] = $user;
                } else {

                    // log current user out
                    authentication_logout();
                }
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
    $cookie = authentication_encrypt(to_key_value_pair($data));

    // set cookie to expire after two weeks
    setcookie('auth', $cookie, time() + 1209600, '/', null, null, false); // 2x7x24x3600  (name, value, expire, path, domain, secure, httponly)
}

function authentication_logout() {

    // set cookie to expire after two weeks
    setcookie('auth', '', time() - 1209600, '/', null, null, false);

    // remove cookie
    unset($_COOKIE['auth']);

    // remove PHP session id cookie
    setcookie('PHPSESSID', '', time() - 1209600, '/', null, null, false);

    // remove session data
    unset($_SESSION['auth']);
}

function authentication_encrypt($plainText){
    return utils_encrypt($plainText, AUTH_ENC_KEY);
}

function authentication_decrypt($cipherText) {
    return utils_decrypt($cipherText, AUTH_ENC_KEY);
}

// auto login user
$user = authentication_auto_login();