<?php 
/**
  * @package PACMEC
  */

class Conectar {
    private $driver;
    private $adapter;
    private $host, $port, $user, $pass, $database, $charset, $prefix;
  
    public function __construct($pdo=true) {
		$aparter        = null;
		$this->driver   = DB_driver;
		$this->port     = DB_port;
		$this->host     = DB_host;
		$this->user     = DB_user;
		$this->pass     = DB_pass;
		$this->database = DB_database;
		$this->charset  = DB_charset;
		$this->prefix   = DB_prefix;
		$this->adapter  = $this->conexion($pdo);
		return $this;
    }
	
	public function getPrefix(){
		return $this->prefix;
	}
	
	public function getAdapter(){
		return $this->adapter;
	}
    
    public function conexion($pdo=true){
        if($pdo == true){
			try {
				if($this->driver=="mysql" || $this->driver==null){
					# $con=new mysqli($this->host, $this->user, $this->pass, $this->database);
					$pdo = new PDO(
						$this->driver.":host={$this->host};port={$this->port};dbname={$this->database};charset={$this->charset}",
						"{$this->user}",
						"{$this->pass}",
						[
							# PDO:: ATTR_PERSISTENT => true,
							#PDO::MYSQL_ATTR_INIT_COMMAND => "SET lc_time_names='es_CO',NAMES '{$this->charset}'"
							#PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->charset}, lc_time_names='es_CO'"
							#PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->charset}"
						]
						);
					$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				}
				$this->adapter = $pdo;
				return $pdo;
			} catch (PDOException $e) {
				print "Error!: " . $e->getMessage() . "<br/>";
				die();
			}
		} else {
			 if($this->driver=="mysql" || $this->driver==null){
				$con=new mysqli($this->host, $this->user, $this->pass, $this->database, $this->port);
				$con->query("SET NAMES '".$this->charset."'");
				$con->query("SET SESSION sql_warnings=1;");
				$con->query("SET SESSION sql_mode = \"ANSI,TRADITIONAL\";");
			}
				$this->adapter = $con;
			return $con;
		}
    }
	
    public function FetchObject(string $sql, array $params = []){
        try {
            $query = $this->getAdapter()->prepare($sql);
            $result = $query->execute($params);
			
			preg_match('/insert+[\s\g\w]+/i', $sql, $is_insert, PREG_OFFSET_CAPTURE);
			if(isset($is_insert[0])){
				return (int) $this->getAdapter()->lastInsertId();
			} else {
				preg_match('/select+[\s\g\w]+/i', $sql, $is_select, PREG_OFFSET_CAPTURE);
				if(isset($is_select[0])){
					return $query->fetch(PDO::FETCH_OBJ);
				} else {
					$result = $query->execute($params);
					return ($result == true) ? true : false;
				}
			}
			
        }
        catch(Exception $e){
			echo $e->getMessage();
            return false;
        }
    }
	
    public function FetchAllObject($sql, $params = []){
        try {
            $query = $this->getAdapter()->prepare($sql);
            $result = $query->execute($params);
			preg_match('/select+[\s\g\w]+/i', $sql, $is_select, PREG_OFFSET_CAPTURE);
			if(isset($is_select[0])){
				return $query->fetchAll(PDO::FETCH_OBJ);
			} else {
				$result = $query->execute($params);
				return ($result == true) ? true : false;
			}
        }
        catch(Exception $e){
			#echo $e->getMessage();
            return false;
        }
    }
}
?>
