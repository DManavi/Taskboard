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

// if action defined
if (isset($_REQUEST['action']) && http_get_method() == 'post') {

    // get action from request and make it lowercase
    $action = strtolower($_REQUEST['action']);

    // invalid model flag
    $invalidModel = false;

    // check requested action
    switch ($action) {

        // register
        case "register": {

            // extract model from body
            $model = http_get_body();

            // validate model
            $invalidModel = !validate_model('account-register', $model);

            // if model is valid
            if (!$invalidModel) {

                // try to register account
                $result = account_register($model->email, $model->password);

                // check register result
                switch ($result["status"]) {

                    // done
                    case 0: {

                        // send HTTP 200
                        http_response_code(200);

                        break;
                    }

                    // email already exist
                    case 1: {

                        // send HTTP 409 (Conflict)
                        http_response_code(409);

                        break;
                    }

                    // unknown error
                    case 255: {

                        // send internal server error
                        http_response_code(500);
                    }
                }
            }

            break;
        }

        // login
        case "login": {

            // extract model from body
            $model = http_get_body();

            // validate model
            $invalidModel = !validate_model('account-login', $model);

            // if model is valid
            if (!$invalidModel) {

                // try to login account
                $result = account_login($model->email, $model->password);

                // check register result
                switch ($result["status"]) {

                    // done
                    case 0: {

                        // write content on the body
                        //http_set_body();

                        // send HTTP 200
                        http_response_code(200);

                        break;
                    }

                    // invalid username/password
                    case 1: {

                        // send HTTP 403 (Invalid username/password)
                        http_response_code(403); // forbidden

                        break;
                    }

                    // unknown error
                    case 255: {

                        // send internal server error
                        http_response_code(500);
                    }
                }
            }

            break;
        }

        // logout
        case "logout": {

            // if user logged in
            if($user["loggedIn"]) {

                // logout
                account_logout();

                // send HTTP 200
                http_response_code(200);
            }
            else { // unauthorized access

                // send HTTP 404
                http_response_code(401);
            }

            break;
        }

        // change password
        case "changepassword": {

            // if user logged in
            if($user["loggedIn"]) {

                // extract model from body
                $model = http_get_body();

                // validate model
                $invalidModel = !validate_model('account-change-password', $model);

                // if model is valid
                if (!$invalidModel) {

                    // try to change account password
                    $result = account_change_password($user['userId'], $model);

                    // check register result
                    switch ($result["status"]) {

                        // done
                        case 0: {

                            // send HTTP 200
                            http_response_code(200);

                            break;
                        }

                        // invalid password
                        case 1: {

                            // send HTTP 403 (Forbidden)
                            http_response_code(403);

                            break;
                        }

                        // unknown error
                        case 255: {

                            // send internal server error
                            http_response_code(500);

                            break;
                        }
                    }
                }
                else {

                    // send HTTP 400 - bad request
                    http_response_code(400);
                }

            }
            else { // unauthorized access

                // send HTTP 404
                http_response_code(401);
            }

            break;
        }

        // invalid action
        default: {

            // send bad-request
            http_response_code(405);
        }
    }

    // send HTTP 400 (Bad request)
    if ($invalidModel) {
        http_response_code(400);
    }
} else {

    // method not allowed
    http_response_code(405);
}