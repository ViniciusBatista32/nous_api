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

$user_id      = isset($_REQUEST['user_id'])      ? $_REQUEST['user_id']                : NULL;
$task_id      = isset($_REQUEST['task_id'])      ? $_REQUEST['task_id']                : NULL;

$name         = isset($_REQUEST['name'])         ? $_REQUEST['name']                   : NULL;
$description  = isset($_REQUEST['description'])  ? $_REQUEST['description']            : NULL;
$initial_time = isset($_REQUEST['initial_time']) ? $_REQUEST['initial_time']           : NULL;
$final_time   = isset($_REQUEST['final_time'])   ? $_REQUEST['final_time']             : NULL;
$color        = isset($_REQUEST['color'])        ? $_REQUEST['color']                  : NULL;
$icon         = isset($_REQUEST['icon'])         ? $_REQUEST['icon']                   : NULL;
$no_repeat    = isset($_REQUEST['no_repeat'])    ? json_decode($_REQUEST['no_repeat']) : NULL;
$weekdays     = isset($_REQUEST['weekdays'])     ? json_decode($_REQUEST['weekdays'])  : NULL;
$date         = isset($_REQUEST['date'])         ? $_REQUEST['date']                   : NULL;


/**
 * Status:
 * 0: Ok
 * 1: Server error
 * 2: Request error
*/

$server_error = getStatusJson(1, 1, $action);

function getSchedueleData($user_id, $server_error, $action)
{
    $schedule_data = readScheduleTask(NULL, $user_id);

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
                    getSchedueleData($user_id, $server_error, $action);
                }
            }
        }
    break;

    case 'create_schedule_task':
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
                    if(!$no_repeat) // with repeat task
                    {
                        $date = NULL;

                        $stmt = Connection::getConn();
                        $stmt->beginTransaction();

                        foreach($weekdays as $key => $weekday)
                        {
                            if($weekday)
                            {
                                // Prevent from create a task with initial time between another task time
                                $ret1 = readScheduleTask(NULL, $user_id, $key, $initial_time, $initial_time, 0, NULL, TRUE);

                                // Prevent from create a task with final time between another task time
                                $ret2 = readScheduleTask(NULL, $user_id, $key, $final_time, $final_time, 0, NULL, TRUE);

                                // Prevent from create a task with another task inside or with the same duration
                                $ret3 = readScheduleTask(NULL, $user_id, $key, $initial_time, $final_time, 1, NULL, TRUE);

                                // Prevent from create a task with same initial time than another
                                $ret4 = readScheduleTask(NULL, $user_id, $key, $initial_time, NULL, 2, NULL, TRUE);
                                
                                // Prevent from create a task with same final time than another
                                $ret5 = readScheduleTask(NULL, $user_id, $key, NULL, $final_time, 2, NULL, TRUE);

                                if(
                                    count($ret1) > 0 ||
                                    count($ret2) > 0 ||
                                    count($ret3) > 0 ||
                                    count($ret4) > 0 ||
                                    count($ret5) > 0
                                )
                                {
                                    $stmt->rollBack();
                                    echo getStatusJson(2, 17, $action);
                                    die();
                                }
                                else
                                {
                                    $ret = createScheduleTask(
                                        $stmt,
                                        $user_id, $name, $description,
                                        $key, $initial_time, $final_time,
                                        $color, $icon
                                    );
        
                                    if($ret === false)
                                    {
                                        $stmt->rollBack();
                                        echo $server_error;
                                        die();
                                    }
                                }
                            }
                        }

                        $stmt->commit();
                        getSchedueleData($user_id, $server_error, $action);
                        die();
                    } // with repeat task
                    else
                    { // no repeat task
                        $stmt = Connection::getConn();

                        // Prevent from create a task with initial time between another task time
                        $ret1 = readScheduleTask(NULL, $user_id, $weekdays, $initial_time, $initial_time, 0, $date);

                        // Prevent from create a task with final time between another task time
                        $ret2 = readScheduleTask(NULL, $user_id, $weekdays, $final_time, $final_time, 0, $date);

                        // Prevent from create a task with another task inside or with the same duration
                        $ret3 = readScheduleTask(NULL, $user_id, $weekdays, $initial_time, $final_time, 1, $date);

                        // Prevent from create a task with same initial time than another
                        $ret4 = readScheduleTask(NULL, $user_id, $weekdays, $initial_time, NULL, 2, $date);
                        
                        // Prevent from create a task with same final time than another
                        $ret5 = readScheduleTask(NULL, $user_id, $weekdays, NULL, $final_time, 2, $date);

                        if(
                            count($ret1) > 0 ||
                            count($ret2) > 0 ||
                            count($ret3) > 0 ||
                            count($ret4) > 0 ||
                            count($ret5) > 0
                        )
                        {
                            echo getStatusJson(2, 17, $action);
                            die();
                        }
                        else
                        {
                            $ret = createScheduleTask(
                                $stmt,
                                $user_id, $name, $description,
                                $weekdays, $initial_time, $final_time,
                                $color, $icon, $date
                            );

                            if($ret === false)
                            {
                                echo $server_error;
                                die();
                            }
                            else
                            {
                                getSchedueleData($user_id, $server_error, $action);
                                die();
                            }
                        }
                    } // no repeat task
                }
            }
        }
    break;

    case 'edit_schedule_task':
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
                $ret = readScheduleTask($task_id, $user_id);

                if(!count($ret) > 0)
                {
                    echo getStatusJson(2, 16, $action);
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
                        if(!$no_repeat) // with repeat task
                        {
                            $date = NULL;

                            // Prevent from create a task with initial time between another task time
                            $ret1 = readScheduleTask(NULL, $user_id, $weekdays, $initial_time, $initial_time, 0, NULL, TRUE, $task_id);

                            // Prevent from create a task with final time between another task time
                            $ret2 = readScheduleTask(NULL, $user_id, $weekdays, $final_time, $final_time, 0, NULL, TRUE, $task_id);

                            // Prevent from create a task with another task inside or with the same duration
                            $ret3 = readScheduleTask(NULL, $user_id, $weekdays, $initial_time, $final_time, 1, NULL, TRUE, $task_id);

                            // Prevent from create a task with same initial time than another
                            $ret4 = readScheduleTask(NULL, $user_id, $weekdays, $initial_time, NULL, 2, NULL, TRUE, $task_id);
                            
                            // Prevent from create a task with same final time than another
                            $ret5 = readScheduleTask(NULL, $user_id, $weekdays, NULL, $final_time, 2, NULL, TRUE, $task_id);

                            if(
                                count($ret1) > 0 ||
                                count($ret2) > 0 ||
                                count($ret3) > 0 ||
                                count($ret4) > 0 ||
                                count($ret5) > 0
                            )
                            {
                                echo getStatusJson(2, 17, $action);
                                die();
                            }
                            else
                            {
                                $ret = editScheduleTask(
                                    $task_id, $user_id, $name,
                                    $description, $initial_time, $final_time,
                                    $color, $icon
                                );

                                if($ret === false)
                                {
                                    echo $server_error;
                                    die();
                                }
                            }

                            getSchedueleData($user_id, $server_error, $action);
                            die();
                        } // with repeat task
                        else
                        { // no repeat task
                            // Prevent from create a task with initial time between another task time
                            $ret1 = readScheduleTask(NULL, $user_id, $weekdays, $initial_time, $initial_time, 0, $date, FALSE, $task_id);

                            // Prevent from create a task with final time between another task time
                            $ret2 = readScheduleTask(NULL, $user_id, $weekdays, $final_time, $final_time, 0, $date, FALSE, $task_id);

                            // Prevent from create a task with another task inside or with the same duration
                            $ret3 = readScheduleTask(NULL, $user_id, $weekdays, $initial_time, $final_time, 1, $date, FALSE, $task_id);

                            // Prevent from create a task with same initial time than another
                            $ret4 = readScheduleTask(NULL, $user_id, $weekdays, $initial_time, NULL, 2, $date, FALSE, $task_id);
                            
                            // Prevent from create a task with same final time than another
                            $ret5 = readScheduleTask(NULL, $user_id, $weekdays, NULL, $final_time, 2, $date, FALSE, $task_id);
                            
                            if(
                                count($ret1) > 0 ||
                                count($ret2) > 0 ||
                                count($ret3) > 0 ||
                                count($ret4) > 0 ||
                                count($ret5) > 0
                            )
                            {
                                echo getStatusJson(2, 17, $action);
                                die();
                            }
                            else
                            {
                                $ret = editScheduleTask(
                                    $task_id, $user_id, $name,
                                    $description, $initial_time, $final_time,
                                    $color, $icon, $date
                                );

                                if($ret === false)
                                {
                                    echo $server_error;
                                    die();
                                }
                                else
                                {
                                    getSchedueleData($user_id, $server_error, $action);
                                    die();
                                }
                            }
                        } // no repeat task
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
