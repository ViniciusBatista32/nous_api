<?php
function readTodoTask($id = NULL, $user_id = NULL)
{
    $sql = "
    SELECT
        * 
    FROM 
        `" . DB_NAME . "`.`users_todo`
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

function alterTodoTask($id, $completed = NULL)
{
    $sql = "
    UPDATE
        `" . DB_NAME . "`.`users_todo`
    SET 
    ";

    if(is_numeric($completed))
        $sql .= " completed = :completed ";

    $sql .= " WHERE id = :id ";
    
    $stmt = Connection::getConn()->prepare($sql);

    if(is_numeric($completed))
        $stmt->bindValue(":completed", $completed);

    $stmt->bindValue(":id", $id);

    if($stmt->execute() === false)
    {
        return false;
    }
    else
    {
        return true;
    }
}

// function createTodo($variable)
// {
//     $sql = "
//     INSERT INTO
//         `" . DB_NAME . "`.`users_todo`
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

function deleteTodoTask($id)
{
    $sql = "
    DELETE FROM 
        `" . DB_NAME . "`.`users_todo`
    WHERE 
        id = :id
    ";

    $stmt = Connection::getConn()->prepare($sql);

    $stmt->bindValue(":id", $id);

    if($stmt->execute() === false)
    {
        return false;
    }
    else
    {
        return true;
    }
}