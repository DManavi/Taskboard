<?php
/**
 * Created by PhpStorm.
 * User: D. Manavi
 */

function profile_read($userId) {

    if(!is_numeric($userId)) { $userId = intval($userId); }

    $output = [
        "status" => 0
    ];

    // search for categories of the user
    $result = db_select('SELECT id,firstName,lastName,hasImage FROM user WHERE id='.$userId)->fetch_assoc();

    $result['hasImage'] = intval($result['hasImage']);

    // set content
    $output['content'] = $result;

    // return output to the caller
    return $output;
}

function profile_update($userId, $model) {

    if(!is_numeric($userId)) { $userId = intval($userId); }

    $output = [
        "status" => 0
    ];

    // read old profile
    $oldProfile = profile_read($userId)['content'];

    // search for categories of the user
    $count = db_execute('UPDATE user SET firstName=\''.$model->firstName.'\', lastName=\''.$model->lastName.'\', hasImage='.$model->hasImage.' WHERE id='.$userId);

    // if 1 row affected
    if(is_numeric($count)) {

        // if user has profile && updated model has no image
        if($oldProfile['hasImage'] && !$model->hasImage) {

            // delete profile image
            unlink('../content/img/'.$userId.'.jpg');
        }
        else if ($model->hasImage && isset($model->image)) {

            // if user uploaded image, save new profile image
            utils_base64_to_jpeg($model->image, '../content/img/'.$userId.'.jpg');
        }

        // set output to done
        $output['status'] = 0;

        // set content
        $output['content'] = profile_read($userId)['content'];
    }

    // return output to the caller
    return $output;
}