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

require_once './providers/profile.php';

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

            case "read": {

                // check if verb is GET
                if($method == 'get') {

                    // read current user profile
                    $result = profile_read($user['userId']);

                    // write content to body
                    http_set_body($result['content']);

                    // send HTTP 200 status
                    http_response_code(200);
                }
                else {

                    // HTTP 405 - method not allowed
                    http_response_code(405);
                }

                break;
            }

            case "update": {

                // if method is put
                if($method == 'put') {

                    // extract model from body
                    $model = http_get_body();

                    // validate model
                    $invalidModel = !validate_model('profile-update', $model);

                    // if model is valid
                    if (!$invalidModel) {

                        // try to update profile
                        $result = profile_update($user['userId'], $model);

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