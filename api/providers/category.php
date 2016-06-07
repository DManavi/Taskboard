<?php
/**
 * Created by PhpStorm.
 * User: D. Manavi
 */

function category_create($userId, $model) {

    $output = [
        'status' => 255
    ];

    // create parent id to null
    $parentId = null;

    // if user is isn't in numeric format
    if(!is_numeric($userId)){

        // cast userId to integer
        $userId = intval($userId);
    }

    // if model has parent id
    if(isset($model->parentId)) {

        // if category doesn't exist or access denied
        if(!category_has_access($userId, $model->parentId)) {

            // set status to not found
            $output['status'] = 1;

            // return output to the caller
            return $output;
        }
        else {

            // set parent id
            $parentId = $model->parentId;
        }
    }
    else {

        // set parent id to null
        $parentId = 'NULL';
    }

    // execute insert query
    $count = db_execute('INSERT INTO category (title, parentId, userId) VALUES (\''.$model->title.'\','.$parentId.','.$userId.')');

    // if query executed successfully
    if($count == 1) {

        // get created category
        $category = db_select('SELECT id FROM category WHERE userId='.$userId.' ORDER BY id DESC LIMIT 1')->fetch_assoc();

        $output['content'] = [
            "id" => $category["id"],
            "title" => $model->title,
            "parentId" => $parentId
        ];

        // set status to done
        $output['status'] = 0;
    }
    else {

        // failed to insert
        $output['status'] = 255;
    }

    // return output to the caller
    return $output;
}

function category_read_list($userId) {

    if(!is_numeric($userId)) { $userId = intval($userId); }

    $output = [
        "status" => 0
    ];

    // search for categories of the user or the categories that shared with
    $result = db_select('SELECT category.id, category.title, category.parentId, (category.UserId!='.$userId.') AS shared FROM category LEFT OUTER JOIN task on category.Id=task.CategoryId WHERE category.UserId='.$userId.' OR task.AssignedTo='.$userId.' GROUP BY category.Id ORDER BY category.title ASC');

    $categories = [];

    while ($row = $result->fetch_assoc()) {
        array_push($categories, $row);
    }

    // iterate all categories
    for($i = 0; $i < sizeof($categories); $i++) {

        // if category is shared, reset parent id
        if($categories[$i]['shared']) {

            // set parent id to null
            $categories[$i]['parentId'] = null;
        }
    }

    $output['content'] = $categories;

    // return output to the caller
    return $output;
}

function category_read_single($userId, $categoryId) {

    if(!is_numeric($userId)) { $userId = intval($userId); }

    $output = [
        "status" => 255
    ];

    // if user id isn't in numeric format
    if(!is_numeric($userId)){

        // cast userId to integer
        $userId = intval($userId);
    }

    // if category id isn't in numeric format
    if(!is_numeric($categoryId)){

        // cast userId to integer
        $categoryId = intval($categoryId);
    }

    // check user access
    if(category_has_access($userId, $categoryId)) {

        // set forbidden status
        $output['status'] = 0;

        $result = db_select('SELECT id, title, parentId FROM category WHERE id='.$categoryId);

        $output['content'] = $result->fetch_assoc();
    }
    else {

        // set forbidden status
        $output['status'] = 1;
    }

    // return output to the caller
    return $output;
}

function category_update($userId, $model) {

    // create output variable
    $output = [
        "status" => 255
    ];

    // if user is isn't in numeric format
    if(!is_numeric($userId)){

        // cast userId to integer
        $userId = intval($userId);
    }

    // check user access
    if(category_has_access($userId, $model->id)) {

        // create parent id to null
        $parentId = null;

        // if model has parent id
        if(isset($model->parentId)) {

            // if category doesn't exist or access denied
            if(!category_has_access($userId, $model->parentId)) {

                // set status to not found
                $output['status'] = 1;

                // return output to the caller
                return $output;
            }
            else {

                // set parent id
                $parentId = $model->parentId;
            }
        }
        else {

            // set parent id to null
            $parentId = 'NULL';
        }

        // update category
        $count = db_execute('UPDATE category SET title=\''.$model->title.'\', parentId='.$parentId.' WHERE id='.$model->id);

        // if affected rows count is 1
        if($count == 1) {

            $output['content'] = [
                "id" => $model->id,
                "title" => $model->title,
                "parentId" => $parentId
            ];

            // set status to done
            $output['status'] = 0;
        }
    }
    else { // access was denied

        // set status to forbidden
        $output['status'] = 1;
    }

    // return output to caller
    return $output;
}

function category_delete($userId, $categoryId) {

    // create output variable
    $output = [
        "status" => 255
    ];

    // check user access
    if(category_has_access($userId, $categoryId)) {

        // delete category and get affected rows count
        $count = db_execute('DELETE FROM category WHERE id='.$categoryId.' OR parentId='.$categoryId);

        // if more than no row affected
        if($count > 0) {

            // set status to done
            $output['status'] = 0;
        }
    }
    else {

        // set status to 1 // access denied
        $output['status'] = 1;
    }

    // return output to caller
    return $output;
}

function category_has_access($userId, $categoryId) {

    if(!is_numeric($userId)) { $userId = intval($userId); }

    // check parent id existence
    $exist = db_select_scalar('SELECT COUNT(*) FROM category WHERE id='.$categoryId.' AND userId='.$userId);

    return $exist == 1;
}

function category_is_shared($userId, $categoryId) {

    if(!is_numeric($userId)) { $userId = intval($userId); }

    if(!is_numeric($categoryId)) { $categoryId = intval($categoryId); }

    // check parent id existence
    $exist = db_select_scalar('SELECT COUNT(*) FROM category INNER JOIN task on task.CategoryId=category.Id WHERE task.AssignedTo='.$userId.' AND category.Id='.$categoryId);

    return $exist == 1;
}