<?php
function getErrorMessage($error_code)
{
    switch ($error_code)
    {
        case 0:
            return "Success";
        break;
        
        case 1:
            return "Server error";
        break;

        case 2:
            return "Invalid request action";
        break;

        case 3:
            return "Email not informed";
        break;

        case 4:
            return "Invalid email";
        break;

        case 5:
            return "Password not informed";
        break;

        case 6:
            return "Invalid password";
        break;

        case 7:
            return "Invalid API Key";
        break;

        case 8:
            return "Email already exists";
        break;

        case 9:
            return "Name not informed";
        break;

        case 10:
            return "Confirmation email can't be sented";
        break;

        case 11:
            return "Email not confirmed";
        break;
        
        case 12:
            return "Reset password email can't be sented";
        break;

        case 13:
            return "User not informed";
        break;
        
        case 14:
            return "Invalid user";
        break;

        case 15:
            return "Task not informed";
        break;

        case 16:
            return "Invalid task id";
        break;

        case 17:
            return "Invalid task time";
        break;

        case 18:
            return "Task description not informed";
        break;

        default:
            return "";
        break;
    }
}

/**
 * Get request error
 * @param int $error_code Error code
 * @param String $action Action
 * @param int 0 Success
 * @param int 1 Server error
 * @param int 2 Invalid request action
 * @param int 3 Email not informed
 * @param int 4 Invalid email
 * @param int 5 Password not informed
 * @param int 6 Invalid password
 * @param int 7 Invalid API Key
 * @param int 8 Email already exists
 * @param int 9 Name not informed
 * @param int 10 Confirmation email can't be sented
 * @param int 11 Email not confirmed
 * @param int 12 Reset password email can't be sented
 * @param int 13 User not informed
 * @param int 14 Invalid user
 * @param int 15 Task not informed
 * @param int 16 Invalid task id
 * @param int 17 Invalid task time
 * @param int 18 Task description not informed
 * @return String
*/
function getStatusJson($status, $error_code, $action, $json = TRUE)
{
    $request_status = array(
        "status" => $status,
        "error_code" => $error_code,
        "status_message" => getErrorMessage($error_code),
        "action" => $action
    );

    if($json)
    {
        return json_encode(
            array(
                "request_status" => $request_status,
                "data" => array()
            ),
        );
    }
    else
        return $request_status;
}