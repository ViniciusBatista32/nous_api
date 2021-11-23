<?php
function readTodoTask($task_id = NULL, $user_id = NULL)
{
    $sql = "
    SELECT
        * 
    FROM 
        `" . DB_NAME . "`.`users_todo`
    WHERE 
        1 = 1
    ";

    if(!empty($task_id))
        $sql .= " AND id = :task_id ";

    if(!empty($user_id))
        $sql .= " AND user_id = :user_id ";

    $stmt = Connection::getConn()->prepare($sql);

    if(!empty($task_id))
        $stmt->bindValue(":task_id", $task_id);

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

function createTodoTask($user_id, $description)
{
    $sql = "
    INSERT INTO
        `" . DB_NAME . "`.`users_todo`
        (user_id, description)
    VALUES 
        (:user_id, :description)
    ";

    $stmt = Connection::getConn()->prepare($sql);

    $stmt->bindValue(":user_id", $user_id);
    $stmt->bindValue(":description", $description);

    if($stmt->execute() === false)
    {
        return false;
    }
    else
    {
        return true;
    }
}

function editTodoTask($task_id, $user_id, $description)
{
    if(empty($description))
        $description = "[Sem nome]";

    $sql = "
    UPDATE
        `" . DB_NAME . "`.`users_todo`
    SET 
        description = :description
    WHERE
        id = :task_id
    AND
        user_id = :user_id
    ";
    
    $stmt = Connection::getConn()->prepare($sql);

    $stmt->bindValue(":description", $description);
    $stmt->bindValue(":task_id", $task_id);
    $stmt->bindValue(":user_id", $user_id);

    if($stmt->execute() === false)
    {
        return false;
    }
    else
    {
        return true;
    }
}

function checkTodoTask($task_id, $completed = NULL)
{
    $sql = "
    UPDATE
        `" . DB_NAME . "`.`users_todo`
    SET 
    ";

    if(is_numeric($completed))
        $sql .= " completed = :completed ";

    $sql .= " WHERE id = :task_id ";
    
    $stmt = Connection::getConn()->prepare($sql);

    if(is_numeric($completed))
        $stmt->bindValue(":completed", $completed);

    $stmt->bindValue(":task_id", $task_id);

    if($stmt->execute() === false)
    {
        return false;
    }
    else
    {
        return true;
    }
}

function deleteTodoTask($task_id)
{
    $sql = "
    DELETE FROM 
        `" . DB_NAME . "`.`users_todo`
    WHERE 
        id = :task_id
    ";

    $stmt = Connection::getConn()->prepare($sql);

    $stmt->bindValue(":task_id", $task_id);

    if($stmt->execute() === false)
    {
        return false;
    }
    else
    {
        return true;
    }
}