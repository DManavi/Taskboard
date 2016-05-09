<?php
/**
 * Created by PhpStorm.
 * User: D. Manavi
 */

function validate_model($fileName, $model) {

    // create output variable
    $output = false;

    // check provided file name
    switch(strtolower($fileName)) {

        case "account-register": {

            $output = account_register($model);

            break;
        }

        case "account-login": {

            $output = account_login($model);

            break;
        }

        case "account-forget-password": {

            $output = account_forget_password($model);

            break;
        }

        case "account-reset-password": {

            $output = account_reset_password($model);

            break;
        }
    }

    // return validation result to the caller
    return $output;
}

function validate_account_register($model) {

    // create output variable
    $output = false;

    // if model isn't null
    if(isset($model)){

        $output = (isset($model->email) && filter_var($model->email, FILTER_VALIDATE_EMAIL)) && (isset($model->password) && ($model->password == $model->confirmPassword));
    }

    // return validation result to the caller
    return $output;
}

function validate_account_login($model) {
    // create output variable
    $output = false;

    // if model isn't null
    if(isset($model)){

        $output = (isset($model->email) && filter_var($model->email, FILTER_VALIDATE_EMAIL)) && isset($model->password);
    }

    // return validation result to the caller
    return $output;
}

function validate_account_forget_password($model) {
    // create output variable
    $output = false;

    // if model isn't null
    if(isset($model)){

        $output = (isset($model->email) && filter_var($model->email, FILTER_VALIDATE_EMAIL));
    }

    // return validation result to the caller
    return $output;
}

function validate_account_reset_password($model) {
    // create output variable
    $output = false;

    // if model isn't null
    if(isset($model)){

        $output = (isset($model->email) && filter_var($model->email, FILTER_VALIDATE_EMAIL)) && isset($model->token) && (isset($model->password) && ($model->password == $model->confirmPassword));
    }

    // return validation result to the caller
    return $output;
}