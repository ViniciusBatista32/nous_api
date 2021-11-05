<?php
function checkApiKey($api_key)
{
    if(!empty($api_key))
    {
        $sql = "SELECT * FROM `" . DB_NAME . "`.`api` WHERE api_key = :api_key";
        $stmt = Connection::getConn()->prepare($sql);

        $stmt->bindValue(":api_key", $api_key);

        if($stmt->execute() === false)
        {
            return $stmt->errorInfo();
        }
        else
        {
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
    }
    else
    {
        return false;
    }
}

function addApiKey($api_key, $app_name, $disabled)
{
    $sql = "INSERT INTO `" . DB_NAME . "`.`api`(`api_key`, `app_name`, `disabled`) VALUES (:api_key, :app_name, :disabled)";
    $stmt = Connection::getConn()->prepare($sql);

    $stmt->bindValue(":api_key",$api_key);
    $stmt->bindValue(":app_name",$app_name);
    $stmt->bindValue(":disabled",$disabled);

    if($stmt->execute() === false)
    {
        return false;
    }
    else
    {
        return true;
    }
}

function removeApiKey($id)
{
    $sql = "DELETE FROM `" . DB_NAME . "`.`api` WHERE id = :id ";

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

function listApiKeys($api_key = NULL)
{
    $sql = "SELECT * FROM `" . DB_NAME . "`.`api` ";

    if(!empty($api_key))
        $sql .= " WHERE api_key = :api_key ";

    $stmt = Connection::getConn()->prepare($sql);
    
    if(!empty($api_key))
        $stmt->bindValue(":api_key", $api_key);

    if($stmt->execute() === false)
    {
        return false;
    }
    else
    {
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}

function disableEnableApiKey($id, $action)
{
    $sql = "UPDATE `" . DB_NAME . "`.`api` SET disabled = :action WHERE id = :id";

    $stmt = Connection::getConn()->prepare($sql);

    $stmt->bindValue(":action", $action);
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