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

        $output = (isset($model->email) && filter_var($model->email, FILTER_VALIDATE_EMAIL) == $model->email) && (isset($model->password) && ($model->password == $model->confirmPassword));
    }

    // return validation result to the caller
    return $output;
}

function validate_account_login($model) {
    // create output variable
    $output = false;

    // if model isn't null
    if(isset($model)){

        $output = (isset($model->email) && filter_var($model->email, FILTER_VALIDATE_EMAIL) == $model->email) && isset($model->password);
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
        $output =
            isset($model->id) &&
            is_numeric($model->id) &&

            isset($model->title) &&
            strlen($model->title) <= 100 &&
            strlen($model->title) > 0;

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
            isset($model->title) && (
            strlen($model->title) <= 100 &&
            strlen($model->title) > 0) &&

            (isset($model->categoryId) &&
            is_numeric($model->categoryId)) &&

            (isset($model->dueDate) &&
            utils_is_date($model->dueDate));

        // if assigned to provided
        if(isset($model->assignedTo)) {

            // validate assigned to value
            $output &= filter_var($model->assignedTo, FILTER_VALIDATE_EMAIL) == $model->assignedTo;
        }
    }

    // return output to the caller
    return $output;
}

function validate_task_update($model) {

    // create output variable
    $output = false;

    // if model filled
    if(isset($model)) {

        // check if title isn't null && category id is a number
        $output =

            isset($model->id) &&
            is_numeric($model->id) &&

            strlen($model->title) <= 100 &&
            strlen($model->title) > 0 &&

            isset($model->dueDate) &&
            utils_is_date($model->dueDate);

        // if assigned to provided
        if(isset($model->assignedTo)) {

            // validate assigned to value
            $output &= filter_var($model->assignedTo, FILTER_VALIDATE_EMAIL) == $model->assignedTo;
        }
    }

    // return output to the caller
    return $output;
}