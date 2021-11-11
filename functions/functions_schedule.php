<?php
function readSchedule($id = NULL, $user_id = NULL)
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

    $stmt = Connection::getConn()->prepare($sql);

    if(!empty($id))
        $stmt->bindValue(":id", $id);

    if(!empty($user_id))
        $stmt->bindValue(":user_id", $user_id);

    if($stmt->execute() === false)
    {
        return false;
    }
    else
    {
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}

// function createSchedule($variable)
// {
//     $sql = "
//     INSERT INTO
//         `" . DB_NAME . "`.`users_schedule`
//         (values)
//     VALUES 
//         (:bind variable)
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