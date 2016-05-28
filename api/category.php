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

require_once './providers/category.php';

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
                    $invalidModel = !validate_model('category-create', $model);

                    // if model is valid
                    if (!$invalidModel) {

                        // try to create category
                        $result = category_create($user['userId'], $model);

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

            case "read": {

                // read all categories of the user
                $result = category_read($user['userId']);

                // write content to body
                http_set_body($result['content']);

                // send HTTP 200 status
                http_response_code(200);

                break;
            }

            case "update": {

                // if method is put
                if($method == 'put') {

                    // extract model from body
                    $model = http_get_body();

                    // validate model
                    $invalidModel = !validate_model('category-update', $model);

                    // if model is valid
                    if (!$invalidModel) {

                        // try to update category
                        $result = category_update($user['userId'], $model);

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

                            // Access was denied
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

            case "delete": {

                // if method is delete
                if($method == 'delete') {

                    // get id from url
                    $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;

                    // if id found
                    if(isset($id) && is_numeric($id)) {

                        // try to delete category
                        $result = category_delete($user['userId'], $id);

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

                        // send HTTP 404
                        http_response_code(404);
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