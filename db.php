<?php
class DatabaseConnection {
    private static $instance = null;

private $conn;
   private $host = '127.0.0.1';
   private $port = '3306';
   private $username = 'root';
   private $password = 'Cl@$$1c105';
   private $database = 'talkzurilocal';

private function __construct() {
    $this->conn = new PDO("mysql:host=$this->host;dbname=$this->database",$this->username,$this->password,
    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
  
    // set the PDO error mode to exception
  $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
public static function getInstance() {
    if(!self::$instance){ //instantiate it only once
        self::$instance = new DatabaseConnection(); //this instantiation will create a new connection from within the constructor
    }
    return self::$instance;
}

/**
 * return the connection for whatever purpose
 */
public function getConnection() {
    return $this->conn;
}

}