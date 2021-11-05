<?php
require "config.php";
require "functions/conn.php";
require "functions/functions_api.php";
require "functions/functions_user.php";
require "functions/email_sender.php";
require "response.php";

$api_key  = isset($_REQUEST['api_key'])  ? $_REQUEST['api_key']  : NULL;
$action   = isset($_REQUEST['action'])   ? $_REQUEST['action']   : NULL;

$check_api = checkApiKey($api_key);

if($check_api === false || !count($check_api) > 0)
{
    echo getStatusJson(2, 7, $action);
    die();
}

$name     = isset($_REQUEST['name'])     ? $_REQUEST['name']     : NULL;
$email    = isset($_REQUEST['email'])    ? $_REQUEST['email']    : NULL;
$password = isset($_REQUEST['password']) ? $_REQUEST['password'] : NULL;

/**
 * Status:
 * 0: Ok
 * 1: Server error
 * 2: Request error
*/

$server_error = getStatusJson(1, 1, $action);

switch ($action) {
    case 'login':
        if(empty($email))
        {
            echo getStatusJson(2, 3, $action);
            die();
        }
        else
        {
            $ret = readUser($email);
    
            if($ret === false)
            {
                echo $server_error;
                die();
            }
            else
            {
                if(!count($ret) > 0)
                {
                    echo getStatusJson(2, 4, $action);
                    die();
                }
                else
                {
                    if(empty($password))
                    {
                        echo getStatusJson(2, 5, $action);
                        die();
                    }
                    else
                    {
                        $ret = readUser($email, $password);
                
                        if($ret === false)
                        {
                            echo $server_error;
                            die();
                        }
                        else
                        {
                            if(!count($ret) > 0)
                            {
                                echo getStatusJson(2, 6, $action);
                                die();
                            }
                            else
                            {
                                if($ret[0]["confirmed"] == 0)
                                {
                                    echo getStatusJson(2, 11, $action);
                                    die();
                                }
                                else
                                {
                                    echo json_encode(
                                        array(
                                            "request_status" => getStatusJson(0, 0, $action, FALSE),
                                            "data" => $ret[0]
                                        )
                                    );
                                    die();
                                }
                            }
                        }
                    }
                }
            }
        }
    break;

    case "signup":
        // check if email is empty
        if(empty($email))
        {
            echo getStatusJson(2, 3, $action);
            die();
        }
        else
        {
            $ret = readUser($email);

            if($ret === false)
            {
                echo $server_error;
                die();
            }
            else
            {
                if(count($ret) > 0)
                {
                    echo getStatusJson(2, 8, $action);
                    die();
                }
                else
                {
                    // check if password is empty
                    if(empty($password))
                    {
                        echo getStatusJson(2, 5, $action);
                        die();
                    }
                    else
                    {
                        // check if name is empty
                        if(empty($name))
                        {
                            echo getStatusJson(2, 9, $action);
                            die();
                        }
                        else
                        {
                            // create user
                            $stmt = Connection::getConn();
                            $stmt->beginTransaction();
                            // returns email confirmation code
                            $ret = createUser($stmt, $name, $email, $password);

                            if($ret === false)
                            {
                                $stmt->rollBack();
                                echo $server_error;
                                die();
                            }
                            else
                            {
                                // send signup confirmation email
                                $ret = signUpConfirmationEmail($email, $ret);

                                if($ret === false)
                                {
                                    $stmt->rollback();
                                    echo getStatusJson(1, 10, $action);
                                    die();
                                }
                                else
                                {
                                    $stmt->commit();
                                    echo getStatusJson(0, 0, $action);
                                    die();
                                }
                            }
                        }
                    }
                }
            }
        }
    break;

    case 'reset_password':
        // check if email is empty
        if(empty($email))
        {
            echo getStatusJson(2, 3, $action);
            die();
        }
        else
        {
            $ret = readUser($email);

            if($ret === false)
            {
                echo $server_error;
                die();
            }
            else
            {
                if(!count($ret) > 0)
                {
                    echo getStatusJson(2, 4, $action);
                    die();
                }
                else
                {
                    $stmt = Connection::getConn();
                    $stmt->beginTransaction();

                    $ret = resetPasswordCode($stmt, $email);

                    if($ret === false)
                    {
                        $stmt->rollBack();
                        echo $server_error;
                        die();
                    }
                    else
                    {
                        $ret = resetPasswordEmail($email, $ret);

                        if($ret === false)
                        {
                            $stmt->rollback();
                            echo getStatusJson(1, 12, $action);
                            die();
                        }
                        else
                        {
                            $stmt->commit();
                            echo getStatusJson(0, 0, $action);
                            die();
                        }
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
