<?php
require "config.php";
require "functions/conn.php";
require "functions/functions_api.php";
require "functions/functions_todo.php";
require "functions/functions_user.php";
require "response.php";

$api_key  = isset($_REQUEST['api_key'])  ? $_REQUEST['api_key']  : NULL;
$action   = isset($_REQUEST['action'])   ? $_REQUEST['action']   : NULL;

$check_api = checkApiKey($api_key);

if($check_api === false || !count($check_api) > 0)
{
    echo getStatusJson(2, 7, $action);
    die();
}

$user_id  = isset($_REQUEST['user_id'])  ? $_REQUEST['user_id']  : NULL;

/**
 * Status:
 * 0: Ok
 * 1: Server error
 * 2: Request error
*/

$server_error = getStatusJson(1, 1, $action);

switch ($action) {
    case 'get_user_todo':
        if(empty($user_id))
        {
            echo getStatusJson(2, 13, $action);
            die();
        }
        else
        {
            $ret = readUser(NULL, NULL, NULL, $user_id);

            if($ret === false)
            {
                echo $server_error;
                die();
            }
            else
            {
                if(!count($ret) > 0)
                {
                    echo getStatusJson(2, 14, $action);
                    die();
                }
                else
                {
                    $todo_data = readTodo(NULL, $user_id);

                    if($todo_data === false)
                    {
                        echo $server_error;
                        die();
                    }
                    else
                    {
                        echo json_encode(
                            array(
                                "request_status" => getStatusJson(0, 0, $action, FALSE),
                                "data" => $todo_data
                            ),
                        );
                        die();
                    }
                }
            }
        }
    break;
    
    default:
        echo getStatusJson(2, 2, $action);
        die();
    break;
}
