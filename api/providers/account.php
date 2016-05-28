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
        $password = md5($email.$password.$email);

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

    // hash user password
    $password = md5($email.$password.$email);

    // search for requested user in database
    $result = db_select_scalar('SELECT id FROM user WHERE email=\''.$email.'\' AND password=\''.$password.'\' LIMIT 1');

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