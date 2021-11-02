<?php
function readUser($email = NULL, $password = NULL, $confirmation_code = NULL)
{
    $sql = "
    SELECT
        * 
    FROM 
        `nous`.`users`
    WHERE 
        1 = 1
    ";

    if(!empty($email))
        $sql .= " AND email = :email ";
    
    if(!empty($password))
        $sql .= " AND password = :password ";

    if(!empty($confirmation_code))
        $sql .= " AND confirmation_code = :confirmation_code ";

    $stmt = Connection::getConn()->prepare($sql);

    if(!empty($email))
        $stmt->bindValue(":email", $email);

    if(!empty($password))
        $stmt->bindValue(":password", $password);

    if(!empty($confirmation_code))
        $stmt->bindValue(":confirmation_code", $confirmation_code);

    if($stmt->execute() === false)
    {
        return false;
    }
    else
    {
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}

function createUser($stmt, $name, $email, $password)
{
    $confirmation_code = base64_encode(md5(uniqid(rand(), true)));

    $sql = "
    INSERT INTO
        `nous`.`users`
        (name, email, password, confirmation_code)
    VALUES 
        (:name, :email, :password, :confirmation_code)
    ";

    $stmt_prepare = $stmt->prepare($sql);

    $stmt_prepare->bindValue(":name", $name);
    $stmt_prepare->bindValue(":email", $email);
    $stmt_prepare->bindValue(":password", $password);
    $stmt_prepare->bindValue(":confirmation_code", $confirmation_code);

    if($stmt_prepare->execute() === false)
    {
        return false;
    }
    else
    {
        return $confirmation_code;
    }
}

function updateUser($confirmation_code)
{
    $sql = "
    UPDATE 
        `nous`.`users`
    SET
        confirmation_code = :new_confirmation_code,
        confirmed = :confirmed
    WHERE 
        confirmation_code = :confirmation_code
    ";

    $stmt = Connection::getConn()->prepare($sql);

    $stmt->bindValue(":new_confirmation_code", NULL);
    $stmt->bindValue(":confirmed", 1);
    $stmt->bindValue(":confirmation_code", $confirmation_code);

    if($stmt->execute() === false)
    {
        return false;
    }
    else
    {
        return true;
    }
}

function deleteUser($confirmation_code)
{
    $sql = "
    DELETE FROM 
        `nous`.`users`
    WHERE 
        confirmation_code = :confirmation_code
    ";

    $stmt = Connection::getConn()->prepare($sql);

    $stmt->bindValue(":confirmation_code", $confirmation_code);

    if($stmt->execute() === false)
    {
        return false;
    }
    else
    {
        return true;
    }
}

function checkConfirmationCode($confirmation_code)
{
    $sql = "
    SELECT
        email 
    FROM 
        `nous`.`users`
    WHERE 
        confirmation_code = :confirmation_code
    ";

    $stmt = Connection::getConn()->prepare($sql);

    $stmt->bindValue(":confirmation_code", $confirmation_code);

    if($stmt->execute() === false)
    {
        return false;
    }
    else
    {
        return $stmt->rowCount();
    }
}