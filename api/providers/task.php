<?php
/**
 * Created by PhpStorm.
 * User: D. Manavi
 */

function task_create($userId, $model) {

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

    // if category doesn't exist or access denied
    if(!category_has_access($userId, $model->categoryId)) {

        // set status to not found
        $output['status'] = 1;

        // return output to the caller
        return $output;
    }

    // execute insert query
    $count = db_execute('INSERT INTO task (title, dueDate, categoryId) VALUES (\''.$model->title.'\',\''.$model->dueDate.'\','.$model->categoryId.')');

    // if query executed successfully
    if($count == 1) {

        // get created task
        $task = db_select('SELECT id FROM task WHERE categoryId='.$model->categoryId.' ORDER BY id DESC LIMIT 1')->fetch_assoc();

        $output['content'] = [
            "id" => $task["id"],
            "title" => $model->title,
            "dueDate" => $model->dueDate,
            "categoryId" => $model->categoryId
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

function task_read($userId, $categoryId, $pageSize, $pageIndex) {

    if(!is_numeric($userId)) { $userId = intval($userId); }

    // if category doesn't exist or access denied
    if(!category_has_access($userId, $categoryId)) {

        // set status to forbidden
        $output['status'] = 1;

        // return output to the caller
        return $output;
    }

    $output = [
        "status" => 0
    ];

    // calculated skipped items
    $skip = $pageSize * $pageIndex;

    $query = 'SELECT id, title, dueDate, doneDate FROM task WHERE categoryId='.$categoryId.' LIMIT '.$skip.','.$pageSize;

    // search for categories of the user
    $result = db_select($query);

    $tasks = [];

    while ($row = $result->fetch_assoc()) {
        array_push($tasks, $row);
    }

    $output['content'] = $tasks;

    // return output to the caller
    return $output;
}

function task_update($userId, $model) {

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
    if(task_has_access($userId, $model->id)) {

        $query = 'UPDATE task SET title=\''.$model->title.'\', categoryId='.$model->categoryId.', dueDate=\''.$model->dueDate.'\'';

        // done date
        $doneDate = null;

        // if done date is available
        if(isset($model->doneDate)) {

            // set done date
            $doneDate = $model->doneDate;
        }
        else {

            // set done date to null
            $doneDate = null;
        }

        // concatenate doneDate & where filter
        $query = $query.' , doneDate=\''.$doneDate.'\' WHERE id='.$model->id;

        // update task
        $count = db_execute($query);

        // if affected rows count is 1
        if($count == 1) {

            $output['content'] = [
                "id" => $model->id,
                "title" => $model->title,
                "categoryId" => $model->categoryId,
                "dueDate" => $model->dueDate,
                "doneDate" => $doneDate
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

function task_delete($userId, $taskId) {

    // create output variable
    $output = [
        "status" => 255
    ];

    // check user access
    if(task_has_access($userId, $taskId)) {

        // delete category and get affected rows count
        $count = db_execute('DELETE FROM task WHERE id='.$taskId);

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

function task_has_access($userId, $taskId) {

    if(!is_numeric($userId)) { $userId = intval($userId); }

    // check parent id existence
    $exist = db_select_scalar('SELECT COUNT(*) FROM task INNER JOIN category on task.CategoryId=category.Id WHERE category.UserId='.$userId.' AND task.id='.$taskId);

    return $exist == 1;
}

function category_has_access($userId, $categoryId) {

    if(!is_numeric($userId)) { $userId = intval($userId); }

    // check parent id existence
    $exist = db_select_scalar('SELECT COUNT(*) FROM category WHERE id='.$categoryId.' AND userId='.$userId);

    return $exist == 1;
}