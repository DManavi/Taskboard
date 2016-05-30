<?php
/**
 * Created by PhpStorm.
 * User: D. Manavi
 */

function account_register($email, $password) {

    $output = [
        'status' => 0
    ];

    // set email to lowercase
    $email = strtolower($email);

    // select count of the user with specified email in user table
    $result = db_select_scalar('SELECT COUNT(*) FROM user WHERE email=\''.$email.'\'');

    // if user doesn't exist
    if($result == 0) {

        // hash password
        $password = account_create_password($email, $password);

        // create user in database
        $result = db_execute('INSERT INTO user (email,password) VALUES (\''.$email.'\',\''.$password.'\')');

        // if user inserted
        if($result == 1) {

            // set output
            $output['status'] = 0;
        }
        else { // failed to insert user in database
            $output['status'] = 255;
        }
    }
    else { // user found with this email address

        // set status to already exist
        $output['status'] = 1;
    }

    // return output to the caller
    return $output;
}

function account_login($email, $password) {

    $output = [
        "status" => 255
    ];

    // set email to lowercase
    $email = strtolower($email);

    // search for requested user in database
    $result = account_test_login($email, $password);

    // if result found
    if(isset($result)){

        // set authenticated flag
        authentication_login($result);

        // set status to done
        $output['status'] = 0;
    }
    else { // user not found

        // set status to not found
        $output['status'] = 1;
    }

    // return output to the caller
    return $output;
}

function account_logout(){

    // log current user out
    authentication_logout();
}

function account_change_password($userId, $model) {

    $output = [
        'status' => 255
    ];

    // if user is isn't in numeric format
    if(!is_numeric($userId)){

        // cast userId to integer
        $userId = intval($userId);
    }

    // find email by user id
    $email = account_get_email($userId);

    // if email found
    if(isset($email)){

        // test login information
        $loginResult = account_test_login($email, $model->currentPassword);

        // if login test passed
        if(isset($loginResult)){

            // hash new password
            $password = account_create_password($email, $model->password);

            // update account password
            $result = db_execute('UPDATE user SET password=\''.$password.'\' WHERE id='.$userId);

            // if one row affected
            if(is_numeric($result)) {

                // set status to done
                $output['status'] = 0;
            }
        }
        else {

            // set status to invalid password
            $output['status'] = 1;
        }
    }

    // return output to the caller
    return $output;
}

function account_test_login($email, $password) {

    $password = account_create_password($email, $password);

    return db_select_scalar('SELECT id FROM user WHERE email=\''.$email.'\' AND password=\''.$password.'\' LIMIT 1');
}

function account_get_email($userId) {

    // create output variable
    $output = null;

    // if user is isn't in numeric format
    if(!is_numeric($userId)){

        // cast userId to integer
        $userId = intval($userId);
    }

    // find email by user id
    $output = db_select_scalar('SELECT email FROM user WHERE id='.$userId);

    // return output to caller
    return $output;
}

function account_create_password($email,$password) {

    return md5($email.$password.$email);
}