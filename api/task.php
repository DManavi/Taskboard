<?php
/**
 * Created by PhpStorm.
 * User: D. Manavi
 */

// start session
session_start();

require_once './core/db.php';

require_once './core/utils.php';

require_once './core/authentication.php';

require_once './core/http.php';

require_once './core/validator.php';

require_once './providers/account.php';

require_once './providers/task.php';

// if user is guest
if($user["loggedIn"]) {

    // if action defined
    if (isset($_REQUEST['action'])) {

        // get HTTP method
        $method = http_get_method();

        // load action and change to lowercase
        $action = strtolower($_REQUEST['action']);

        // invalid model flag
        $invalidModel = false;

        // check requested action
        switch ($action) {

            case "create": {

                // if method is post
                if($method == 'post') {

                    // extract model from body
                    $model = http_get_body();

                    // validate model
                    $invalidModel = !validate_model('task-create', $model);

                    // if model is valid
                    if (!$invalidModel) {

                        // try to create task
                        $result = task_create($user['userId'], $model);

                        // check create result
                        switch ($result["status"]) {

                            // done
                            case 0: {

                                // write updated content to output
                                http_set_body($result['content']);

                                // send HTTP 200
                                http_response_code(200);

                                break;
                            }

                            // when try to add category to another user's category
                            case 1: {

                                // send HTTP 403 - forbidden
                                http_response_code(403);

                                break;
                            }

                            // unknown error
                            case 255: {

                                // send internal server error
                                http_response_code(500);
                            }
                        }
                    }
                }
                else {

                    // set HTTP 405
                    http_response_code(405);
                }

                break;
            }

            case "list": {

                if($method == 'get') {

                    // get category id from request
                    $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;

                    // get page size
                    $pageSize = isset($_REQUEST['pageSize']) ? intval($_REQUEST['pageSize']) : 5;

                    // validate page size
                    if ($pageSize < 1) {
                        $pageSize = 5;
                    }

                    // get page index
                    $pageIndex = isset($_REQUEST['pageIndex']) ? intval($_REQUEST['pageIndex']) : 0;

                    // validate page index
                    if ($pageIndex < 0) {
                        $pageIndex = 0;
                    }

                    // if id provided
                    if (isset($id) && is_numeric($id)) {

                        // read all categories of the user
                        $result = task_read_list($user['userId'], $id, $pageSize, $pageIndex);

                        // check status
                        switch ($result['status']) {

                            // done
                            case 0: {

                                // write content to body
                                http_set_body($result['content']);

                                // send HTTP 200 status
                                http_response_code(200);

                                break;
                            }

                            // access was denied
                            case 1: {

                                // return HTTP 403
                                http_response_code(403);

                                break;
                            }

                            // failed
                            case 255: {

                                // return HTTP 500
                                http_response_code(500);

                                break;
                            }
                        }
                    } else {

                        // send HTTP 400
                        http_response_code(400);
                    }
                }

                break;
            }

            case "read": {

                if($method == 'get') {

                    // try to get id from uri
                    $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;

                    // if single read requested
                    if (isset($id)) {

                        // read all categories of the user
                        $result = task_read_single($user['userId'], $id);

                        // check result status
                        switch ($result['status']) {

                            // done
                            case 0: {

                                // write content to body
                                http_set_body($result['content']);

                                // send HTTP 200 status
                                http_response_code(200);

                                break;
                            }

                            // forbidden
                            case 1: {

                                // send HTTP 403 - forbidden
                                http_response_code(403);

                                break;
                            }

                            default: {

                                // send HTTP 400 - bad request
                                http_response_code(500);

                                break;
                            }
                        }
                    } else {

                        // send HTTP 400 - bad request
                        http_response_code(405);
                    }
                }

                break;
            }

            case "update": {

                // if method is put
                if($method == 'put') {

                    // extract model from body
                    $model = http_get_body();

                    // validate model
                    $invalidModel = !validate_model('task-update', $model);

                    // if model is valid
                    if (!$invalidModel) {

                        // try to update task
                        $result = task_update($user['userId'], $model);

                        // check register result
                        switch ($result["status"]) {

                            // done
                            case 0: {

                                // write updated content to output
                                http_set_body($result['content']);

                                // send HTTP 200
                                http_response_code(200);

                                break;
                            }

                            // access was denied
                            case 1: {

                                // send HTTP 403 - forbidden
                                http_response_code(403);

                                break;
                            }

                            // unknown error
                            case 255: {

                                // send internal server error
                                http_response_code(500);
                            }
                        }
                    }
                    else {

                        // HTTP 400 - bad request
                        http_response_code(400);
                    }
                }
                else {

                    // set HTTP 405
                    http_send_status(405);
                }

                break;
            }

            case "status": {

                // try to get id from uri
                $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;

                // if id provided
                if(isset($id)) {

                    // try to toggle task status
                    $result = task_status($user['userId'], $id);

                    // check result
                    switch ($result["status"]) {

                        // done
                        case 0: {

                            // write updated content to output
                            http_set_body($result['content']);

                            // send HTTP 200
                            http_response_code(200);

                            break;
                        }

                        // access was denied
                        case 1: {

                            // send HTTP 403 - forbidden
                            http_response_code(403);

                            break;
                        }

                        // unknown error
                        case 255: {

                            // send internal server error
                            http_response_code(500);
                        }
                    }
                }
                else {

                    // send HTTP 400 - bad request
                    http_response_code(400);
                }

                break;
            }

            case "submit": {

                // if method is put
                if($method == 'put') {

                    // try to get id from uri
                    $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;

                    // if id provided
                    if (isset($id)) {

                        // extract model from body
                        $model = http_get_body();

                        // validate model
                        $invalidModel = !validate_model('task-submit', $model);

                        // if model is valid
                        if (!$invalidModel) {

                            // try to update task
                            $result = task_submit($user['userId'], $model);

                            // check register result
                            switch ($result["status"]) {

                                // done
                                case 0: {

                                    // write updated content to output
                                    http_set_body($result['content']);

                                    // send HTTP 200
                                    http_response_code(200);

                                    break;
                                }

                                // access was denied
                                case 1: {

                                    // send HTTP 403 - forbidden
                                    http_response_code(403);

                                    break;
                                }

                                // unknown error
                                case 255: {

                                    // send internal server error
                                    http_response_code(500);
                                }
                            }
                        } else {

                            // HTTP 400 - bad request
                            http_response_code(400);
                        }

                    }
                }
                else {

                    // set HTTP 405
                    http_send_status(405);
                }

                break;
            }

            case "delete": {

                // if method is delete
                if($method == 'delete') {

                    // get id from url
                    $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;

                    // if id found
                    if(isset($id) && is_numeric($id)) {

                        // try to delete category
                        $result = task_delete($user['userId'], $id);

                        // check register result
                        switch ($result["status"]) {

                            // done
                            case 0: {

                                // send HTTP 200
                                http_response_code(200);

                                break;
                            }

                            // not found / forbidden
                            case 1: {

                                // send HTTP 404
                                http_response_code(403);

                                break;
                            }

                            // unknown error
                            case 255: {

                                // send internal server error
                                http_response_code(500);
                            }
                        }
                    }
                    else {

                        // send HTTP 400
                        http_response_code(400);
                    }
                }
                else {

                    // set HTTP 405
                    http_response_code(405);
                }

                break;
            }

            // invalid action
            default: {

                // send method not allowed
                http_response_code(405);

                break;
            }
        }

        // send HTTP 400 (Bad request)
        if ($invalidModel) {
            http_response_code(400);
        }
    }
    else {

        // method not allowed
        http_response_code(405);
    }
}
else { // already logged in

    // unauthorized access
    http_response_code(401);
}