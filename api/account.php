<?php
/**
 * Created by PhpStorm.
 * User: D. Manavi
 */

require_once './core/db.php';

require_once './core/authentication.php';

require_once './core/email.php';

require_once './core/http.php';

require_once './core/validator.php';

// if user is guest
if(!$user["loggedIn"]) {

    // if action defined
    if (isset($_REQUEST['action']) && http_get_method() == 'post') {

        // invalid model flag
        $invalidModel = false;

        // check requested action
        switch (strtolower($action)) {

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
                    switch ($result->status) {

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
                    switch ($result->status) {

                        // done
                        case 0: {

                            // write content on the body
                            http_set_body($result["content"]);

                            // send HTTP 200
                            http_response_code(200);

                            break;
                        }

                        // not registered
                        case 1: {

                            // send HTTP 404 (Not found)
                            http_response_code(404);

                            break;
                        }

                        // invalid username/password
                        case 2: {

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

            // forget password
            case "forget-password": {

                // extract model from body
                $model = http_get_body();

                // validate model
                $invalidModel = !validate_model('account-forget-password', $model);

                // if model is valid
                if (!$invalidModel) {

                    // try to forget password
                    $result = account_forgetPassword($model->email);

                    // check forget password result
                    switch ($result->status) {

                        // done
                        case 0: {

                            // send HTTP 200
                            http_response_code(200);

                            break;
                        }

                        // not registered
                        case 1: {

                            // send HTTP 404 (Not found)
                            http_response_code(404);

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

            // reset-password
            case "reset-password": {

                // extract model from body
                $model = http_get_body();

                // validate model
                $invalidModel = !validate_model('account-reset-password', $model);

                // if model is valid
                if (!$invalidModel) {

                    // try to reset password
                    $result = account_reset_password($model->email, $model->token, $model->password);

                    // check reset password result
                    switch ($result->status) {

                        // done
                        case 0: {

                            // send HTTP 200
                            http_response_code(200);

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
}
else { // already logged in

    // unauthorized access
    http_response_code(401);
}