<?php
/**
 * Created by PhpStorm.
 * User: D. Manavi
 */

// include category provider
require_once './providers/category.php';

require_once './providers/profile.php';

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

    // set default assigned to current user
    $assignedTo = $userId;

    if(isset($model->assignedTo)) {

        // get user id by email address
        $assignedTo = account_get_id($model->assignedTo);

        // if requested user not found in database
        if(!isset($assignedTo)) {

            // set assigned to to current user
            $assignedTo = $userId;
        }
    }

    // execute insert query
    $count = db_execute('INSERT INTO task (title, dueDate, categoryId, assignedTo) VALUES (\''.$model->title.'\',\''.$model->dueDate.'\','.$model->categoryId.', '.$assignedTo.')');

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

function task_read_list($userId, $categoryId, $pageSize, $pageIndex) {

    if(!is_numeric($userId)) { $userId = intval($userId); }

    // if category doesn't exist or access denied
    if(!category_has_access($userId, $categoryId) && !category_is_shared($userId, $categoryId)) {

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

    $query = 'SELECT id, title, dueDate, doneDate FROM task WHERE categoryId='.$categoryId;

    $countQuery = 'SELECT COUNT(ID) FROM task WHERE categoryId='.$categoryId;

    $shared = false;

    if(!category_has_access($userId, $categoryId)) {

        $query .= ' AND assignedTo='.$userId;

        $countQuery .= ' AND assignedTo='.$userId;

        $shared = true;
    }
    else {
        $shared = false;
    }

    $query .= ' LIMIT '.$skip.','.$pageSize;

    $count = db_select_scalar($countQuery);

    // search for categories of the user
    $result = db_select($query);

    $tasks = [];

    while ($row = $result->fetch_assoc()) {

        // get is shared flag
        $row["isShared"] = $shared;

        array_push($tasks, $row);
    }

    $output['content'] = [
        'items' => $tasks,
        'total' => $count
    ];

    // return output to the caller
    return $output;
}

function task_read_single($userId, $taskId) {

    // create output variable
    $output = [
        "status" => 255
    ];

    // if provided user id isn't an integer
    if(!is_numeric($userId)) { $userId = intval($userId); }

    // if provided task id isn't an integer
    if(!is_numeric($taskId)) { $taskId = intval($taskId); }

    // if category doesn't exist or access denied
    if(!task_can_read($userId, $taskId)) {

        // set status to forbidden
        $output['status'] = 1;

        // return output to the caller
        return $output;
    }
    else {

        // load task by id
        $task = db_select('SELECT task.id, task.title, task.dueDate, task.assignedTo, category.userId FROM task INNER JOIN category on task.categoryId=category.id WHERE task.id='.$taskId)->fetch_assoc();

        $owner = profile_read($task['userId']);

        // load task comments
        $comments_result = db_select('SELECT userId, date, content FROM comment WHERE taskId='.$taskId);

        $comments = [];

        while($row = $comments_result->fetch_assoc()) {

            // load author profile
            $row['author'] = profile_read($row['userId']);

            // remove user id from array
            unset($row['userId']);

            // push loaded comment to comments list
            array_push($comments, $row);
        }

        // set status to done
        $output['status'] = 0;

        // set task to output content
        $output['content'] = [
            "task" => $task,
            "owner" => $owner,
            "comments" => $comments
        ];
    }

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
    if(task_can_read($userId, $model->id)) {

        // create query
        $query = 'UPDATE task SET title=\''.$model->title.'\', dueDate=\''.$model->dueDate.'\'';

        // if assigned to has been set
        if(isset($model->assignedTo)) {

            // get user id by email address
            $assignedTo = account_get_id($model->assignedTo);

            // if requested user not found in database
            if(!isset($assignedTo)) {

                // set assigned to to current user
                $assignedTo = $userId;
            }

            // create set assigned to statement
            $query .= ', assignedTo='.$assignedTo;
        }

        // concatenate doneDate & where filter
        $query = $query.' WHERE id='.$model->id;

        // update task
        $count = db_execute($query);

        // if affected rows count is 1
        if(is_numeric($count)) {

            $output['content'] = [
                "id" => $model->id,
                "title" => $model->title,
                "dueDate" => $model->dueDate
            ];

            // set status to done
            $output['status'] = 0;
        }
        else {

            // update failed
            $output['status'] = 255;
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
    if(task_is_owner($userId, $taskId)) {

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

function task_status($userId, $taskId) {

    // create output variable
    $output = [
        "status" => 255
    ];

    // if user is isn't in numeric format
    if(!is_numeric($userId)){

        // cast userId to integer
        $userId = intval($userId);
    }

    // if provided task id isn't numeric format
    if(!is_numeric($taskId)) {

        // cast task id to integer
        $taskId = intval($taskId);
    }

    // check user access
    if(task_is_owner($userId, $taskId)) {

        // get task done date
        $doneDate = task_done_date($taskId);

        // if task is already done
        if(!isset($doneDate)) {

            // set done date to null
            $doneDate = date('Y-m-d');

            // toggle task status
            $count = db_execute('UPDATE task SET doneDate=\''.$doneDate.'\' WHERE id='.$taskId);
        }
        else {

            // toggle task status
            $count = db_execute('UPDATE task SET doneDate=NULL WHERE id='.$taskId);

            $doneDate = null;
        }

        // if affected rows count is 1
        if(is_numeric($count)) {

            $output['content'] = [
                "id" => $taskId,
                "doneDate" => $doneDate
            ];

            // set status to done
            $output['status'] = 0;
        }
        else {

            // update failed
            $output['status'] = 255;
        }
    }
    else { // access was denied

        // set status to forbidden
        $output['status'] = 1;
    }

    // return output to caller
    return $output;
}

function task_can_read($userId, $taskId) {

    if(!is_numeric($userId)) { $userId = intval($userId); }

    // check parent id existence
    $hasAccess = db_select_scalar('SELECT COUNT(*) FROM task WHERE assignedTo='.$userId.' AND id='.$taskId) == 1;

    // if task assigned to other
    if(!$hasAccess) {

        // set has access flag depends on ownership
        $hasAccess = task_is_owner($userId, $taskId);
    }

    return $hasAccess;
}

function task_is_owner($userId, $taskId) {

    if(!is_numeric($userId)) { $userId = intval($userId); }

    // check parent id existence
    $exist = db_select_scalar('SELECT COUNT(*) FROM task INNER JOIN category on task.CategoryId=category.Id WHERE category.UserId='.$userId.' AND task.id='.$taskId);

    return $exist == 1;
}

function task_is_shared($userId, $categoryId, $taskId) {

    // get integer value of the user id
    $userId = intval($userId);

    // get integer value of the category id
    $categoryId = intval($categoryId);

    // get integer value of the task id
    $taskId = intval($taskId);

    // check task existence
    $exist = db_select_scalar('SELECT COUNT(ID) FROM task WHERE id='.$taskId.' AND assignedTo='.$userId) == 1;

    // isShared set to true if task assigned to the user and user don't have access to it's category
    $output = $exist && !category_has_access($userId, $categoryId);

    // return output to the caller
    return $output;
}

function task_is_done($taskId) {

    if(!is_numeric($taskId)) { $taskId = intval($taskId); }

    // check parent id existence
    $exist = db_select_scalar('SELECT COUNT(*) FROM task WHERE id='.$taskId.' AND doneDate=NULL');

    return $exist == 1;
}

function task_done_date($taskId) {

    if(!is_numeric($taskId)) { $taskId = intval($taskId); }

    // check parent id existence
    $doneDate = db_select('SELECT doneDate FROM task WHERE id='.$taskId)->fetch_assoc();

    return $doneDate['doneDate'];
}