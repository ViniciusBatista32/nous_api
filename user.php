<?php
require "conn.php";
require "config.php";
require "api.php";

$api_key  = isset($_REQUEST['api_key'])  ? $_REQUEST['api_key']  : NULL;

$check_api = checkApiKey($api_key);

if($check_api !== false && count($check_api) > 0)
{
    die();
}

$action   = isset($_REQUEST['action'])   ? $_REQUEST['action']   : NULL;
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
            $sql = "
            SELECT
                * 
            FROM 
                `nous`.`users`
            WHERE 
                email = :email
            ";

            $stmt = Connection::getConn()->prepare($sql);
    
            $stmt->bindValue(":email", $email);
    
            if($stmt->execute() === false)
            {
                echo $server_error;
                die();
            }
            else
            {
                $ret = $stmt->fetchAll(\PDO::FETCH_ASSOC);

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
                        $sql = "
                        SELECT
                            id,
                            name,
                            email,
                            birth_date,
                            first_time
                        FROM 
                            `nous`.`users`
                        WHERE
                            password = :password";

                        $stmt = Connection::getConn()->prepare($sql);
                
                        $stmt->bindValue(":password", $password);
                
                        if($stmt->execute() === false)
                        {
                            echo $server_error;
                            die();
                        }
                        else
                        {
                            $ret = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                            if(!count($ret) > 0)
                            {
                                echo getStatusJson(2, 6, $action);
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
    break;

    case "signup":
        // $sql = "INSERT INTO `nous`.`users` VALUES ()";
        // $stmt = Connection::getConn()->prepare($sql);

        // if($stmt->execute() === false)
        // {
        //     false;
        // }
        // else
        // {
        //     true;
        // }
    break;
    
    default:
        echo getStatusJson(2, 2, $action);
        die();
    break;
}
