<?php
function readScheduleTask(
    $id = NULL,
    $user_id = NULL,
    $weekday = NULL,
    $initial_time = NULL,
    $final_time = NULL,
    $type_time = 0,
    $date = NULL,
    $type_date = FALSE,
    $different_id = NULL
)
{
    $sql = "
    SELECT
        * 
    FROM 
        `" . DB_NAME . "`.`users_schedule`
    WHERE 
        1 = 1
    ";

    if(!empty($id))
        $sql .= " AND id = :id ";

    if(!empty($user_id))
        $sql .= " AND user_id = :user_id ";

    if(!empty($weekday))
        $sql .= " AND `weekday` = :weekday ";
    
    if(!empty($initial_time))
    {
        if($type_time == 0)
            $sql .= " AND initial_time < :initial_time ";
        else if($type_time == 1)
            $sql .= " AND initial_time >= :initial_time ";
        else if($type_time == 2)
            $sql .= " AND final_time = :initial_time ";
    }

    if(!empty($final_time))
    {
        if($type_time == 0)
            $sql .= " AND final_time > :final_time ";
        else if($type_time == 1)
            $sql .= " AND final_time <= :final_time ";
        else if($type_time == 2)
            $sql .= " AND initial_time = :final_time ";
    }

    if(!empty($date))
        $sql .= " AND `date` = :date ";

    if($type_date)
        $sql .= " AND `date` = NULL ";

    if(is_numeric($different_id))
        $sql .= " AND id != :different_id ";

    $stmt = Connection::getConn()->prepare($sql);

    if(!empty($id))
        $stmt->bindValue(":id", $id);

    if(!empty($user_id))
        $stmt->bindValue(":user_id", $user_id);

    if(!empty($weekday))
        $stmt->bindValue(":weekday", $weekday);

    if(!empty($initial_time))
        $stmt->bindValue(":initial_time", $initial_time);

    if(!empty($final_time))
        $stmt->bindValue(":final_time", $final_time);

    if(!empty($date))
        $stmt->bindValue(":date", $date);

    if(is_numeric($different_id))
        $stmt->bindValue(":different_id", $different_id);

    if($stmt->execute() === false)
    {
        return false;
    }
    else
    {
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}

function createScheduleTask(
    $stmt,
    $user_id, $name, $description,
    $weekday = NULL, $initial_time, $final_time,
    $color, $icon, $date = NULL
)
{
    $sql = "
    INSERT INTO
        `" . DB_NAME . "`.`users_schedule`
        (
            user_id, description, weekday,
            initial_time, final_time, color,
            icon, date
    ";

    if(!empty($name))
        $sql .= ", name";

    $sql .= "
        )
    VALUES 
        (
            :user_id, :description, :weekday,
            :initial_time, :final_time, :color,
            :icon, :date
    ";

    if(!empty($name))
        $sql .= ", :name";

    $sql .= "
        )
    ";

    $stmt_prepare = $stmt->prepare($sql);

    $stmt_prepare->bindValue(":user_id", $user_id);
    $stmt_prepare->bindValue(":description", $description);
    $stmt_prepare->bindValue(":weekday", $weekday);

    $stmt_prepare->bindValue(":initial_time", $initial_time);
    $stmt_prepare->bindValue(":final_time", $final_time);
    $stmt_prepare->bindValue(":color", $color);

    $stmt_prepare->bindValue(":icon", $icon);
    $stmt_prepare->bindValue(":date", $date);

    if(!empty($name))
        $stmt_prepare->bindValue(":name", $name);

    if($stmt_prepare->execute() === false)
    {
        return false;
    }
    else
    {
        return true;
    }
}

function editScheduleTask(
    $task_id, $user_id, $name,
    $description, $initial_time, $final_time,
    $color, $icon, $date = NULL
)
{
    $sql = "
    UPDATE
        `" . DB_NAME . "`.`users_schedule`
    SET
        description = :description, initial_time = :initial_time, final_time = :final_time,
        color = :color, icon = :icon, date = :date,
        name = :name
    WHERE 
        id = :task_id
    AND
        user_id = :user_id

    ";

    $stmt = Connection::getConn()->prepare($sql);

    $stmt->bindValue(":description", $description);
    $stmt->bindValue(":initial_time", $initial_time);
    $stmt->bindValue(":final_time", $final_time);

    $stmt->bindValue(":color", $color);
    $stmt->bindValue(":icon", $icon);
    $stmt->bindValue(":date", $date);
    
    $stmt->bindValue(":task_id", $task_id);
    $stmt->bindValue(":user_id", $user_id);
    
    if(!empty($name))
        $stmt->bindValue(":name", $name);
    else
        $stmt->bindValue(":name", "[Sem nome]");

    if($stmt->execute() === false)
    {
        return false;
    }
    else
    {
        return true;
    }
}

// function deleteSchedule($variable)
// {
//     $sql = "
//     DELETE FROM 
//         `" . DB_NAME . "`.`users_schedule`
//     WHERE 
//         column = :bind variable
//     ";

//     $stmt = Connection::getConn()->prepare($sql);

//     $stmt->bindValue(":bind variable", $variable);

//     if($stmt->execute() === false)
//     {
//         return false;
//     }
//     else
//     {
//         return true;
//     }
// }