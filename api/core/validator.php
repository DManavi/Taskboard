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

            $output = validate_account_register($model);

            break;
        }

        case "account-login": {

            $output = validate_account_login($model);

            break;
        }


        case "category-create": {

            $output = validate_category_create($model);

            break;
        }

        case "category-update": {

            $output = validate_category_update($model);

            break;
        }

        case "profile-update": {

            $output = validate_profile_update($model);

            break;
        }

        case "task-create": {

            $output = validate_task_create($model);

            break;
        }

        case "task-update": {

            $output = validate_task_update($model);

            break;
        }

        case "account-change-password": {

            $output = validate_account_change_password($model);

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

function validate_account_change_password($model) {

    // create output variable
    $output = false;

    // if model isn't null
    if(isset($model)){

        $output = isset($model->currentPassword) && (isset($model->password) && ($model->password == $model->confirmPassword));
    }

    // return validation result to the caller
    return $output;
}


function validate_category_create($model) {

    // create output variable
    $output = false;

    // if model filled
    if(isset($model)){

        // check if title isn't null &&
        $output = isset($model->title) && strlen($model->title) <= 100 && strlen($model->title) > 0;

        // if parentId is present
        if(isset($model->parentId)) {

            // if parent id is number
            $output &= is_numeric($model->parentId);
        }
    }

    // return output to the caller
    return $output;
}

function validate_category_update($model) {

    // create output variable
    $output = false;

    // if model filled
    if(isset($model)){

        // check if title isn't null &&
        $output = isset($model->id) && isset($model->title) && strlen($model->title) <= 100 && strlen($model->title) > 0;

        // if parentId is present
        if(isset($model->parentId)) {

            // if parent id is number
            $output &= is_numeric($model->parentId);
        }
    }

    // return output to the caller
    return $output;
}


function validate_profile_update($model) {

    // create output variable
    $output = false;

    // if model filled
    if(isset($model)){

        // check if title isn't null &&
        $output = strlen($model->firstName) <= 50 && strlen($model->lastName) <= 50 && isset($model->hasImage) && is_numeric($model->hasImage);
    }

    // return output to the caller
    return $output;
}


function validate_task_create($model) {

    // create output variable
    $output = false;

    // if model filled
    if(isset($model)){

        // check if title isn't null && category id is a number
        $output =
            strlen($model->title) <= 100 &&
            strlen($model->title) > 0 &&

            isset($model->categoryId) &&
            is_numeric($model->categoryId) &&

            isset($model->dueDate) &&
            utils_is_date($model->dueDate);

        // if done date is set
        if(isset($model->doneDate)) {

            // validate done date
            $output &= utils_is_date($model->doneDate);
        }
    }

    // return output to the caller
    return $output;
}

function validate_task_update($model) {

    // create output variable
    $output = false;

    // if model filled
    if(isset($model)){

        // check if title isn't null && category id is a number
        $output =
            strlen($model->title) <= 100 &&
            strlen($model->title) > 0 &&

            isset($model->categoryId) &&
            is_numeric($model->categoryId) &&

            isset($model->dueDate) &&
            utils_is_date($model->dueDate);

        // if done date is set
        if(isset($model->doneDate)) {

            // validate done date
            $output &= utils_is_date($model->doneDate);
        }
    }

    // return output to the caller
    return $output;
}