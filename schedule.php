<?php
require "config.php";
require "functions/conn.php";
require "functions/functions_api.php";
require "functions/functions_schedule.php";
require "functions/functions_user.php";
require "response.php";

$api_key      = isset($_REQUEST['api_key'])      ? $_REQUEST['api_key']      : NULL;
$action       = isset($_REQUEST['action'])       ? $_REQUEST['action']       : NULL;

$check_api = checkApiKey($api_key);

if($check_api === false || !count($check_api) > 0)
{
    echo getStatusJson(2, 7, $action);
    die();
}

$user_id      = isset($_REQUEST['user_id'])      ? $_REQUEST['user_id']      : NULL;

$name         = isset($_REQUEST['name'])         ? $_REQUEST['name']         : NULL;
$description  = isset($_REQUEST['description'])  ? $_REQUEST['description']  : NULL;
$initial_time = isset($_REQUEST['initial_time']) ? $_REQUEST['initial_time'] : NULL;
$final_time   = isset($_REQUEST['final_time'])   ? $_REQUEST['final_time']   : NULL;
$color        = isset($_REQUEST['color'])        ? $_REQUEST['color']        : NULL;
$icon         = isset($_REQUEST['icon'])         ? $_REQUEST['icon']         : NULL;
$no_repeat    = isset($_REQUEST['no_repeat'])    ? $_REQUEST['no_repeat']    : NULL;
$weekdays     = isset($_REQUEST['weekdays'])     ? $_REQUEST['weekdays']     : NULL;

/**
 * Status:
 * 0: Ok
 * 1: Server error
 * 2: Request error
*/

$server_error = getStatusJson(1, 1, $action);

switch ($action) {
    case 'get_user_schedule':
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
                    $schedule_data = readSchedule(NULL, $user_id);

                    if($schedule_data === false)
                    {
                        echo $server_error;
                        die();
                    }
                    else
                    {
                        $week = array("mon", "tue", "wed", "thu", "fri", "sat", "sun");

                        $mon = array();
                        $tue = array();
                        $wed = array();
                        $thu = array();
                        $fri = array();
                        $sat = array();
                        $sun = array();
                        
                        foreach($schedule_data as $schedule)
                            ${$week[$schedule["weekday"]]}[] = $schedule;

                        $schedule_data = array();

                        foreach($week as $day)
                        {
                            $sort_rule = array();

                            foreach($$day as $schedule_index => $schedule_values)
                                $sort_rule[$schedule_index] = $schedule_values["initial_time"];

                            array_multisort($sort_rule, SORT_ASC, $$day);
                            $schedule_data[] = $$day;
                        }

                        echo json_encode(
                            array(
                                "request_status" => getStatusJson(0, 0, $action, FALSE),
                                "data" => $schedule_data
                            ),
                        );
                        die();
                    }
                }
            }
        }
    break;

    case 'create_user_schedule':
        
    break;
    
    default:
        echo getStatusJson(2, 2, $action);
        die();
    break;
}
