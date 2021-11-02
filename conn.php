<?php
class Connection{

  private static $instance;

  public static function getConn(){
    if(!isset(self::$instance)){
      self::$instance = new \PDO('mysql:host=' . DOMAIN . ':' . DB_PORT . ';dbnme=' . DB_NAME . ';charset=utf8', DB_USER , DB_PASSWORD);
    }
    return self::$instance;
  }
}
