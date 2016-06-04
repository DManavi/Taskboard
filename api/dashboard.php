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

require_once './providers/dashboard.php';

// if user is guest
if($user["loggedIn"]) {

    // if action defined
    if (isset($_REQUEST['action'])) {

        // get HTTP method
        $method = http_get_method();

        // load action and change to lowercase
        $action = strtolower($_REQUEST['action']);

        // check requested action
        switch ($action) {

            case "read": {

                // if requested with GET method
                if($method == 'get') {

                    // read all categories of the user
                    $result = dashboard_read($user['userId']);

                    // write content to body
                    http_set_body($result);

                    // send HTTP 200 status
                    http_response_code(200);
                }
                else {

                    // send HTTP 405 - method not allowed
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