<?php 
/**
  * Usuario
  * 
  * 
  * @package    PACMEC
  * @author     FelipheGomez <feliphegomez@gmail.com>
  * 
  **/

class Usuario extends ModeloBase {
	public $permission_group, $permission_items, $comments = [];
	
    public function __construct($atts=[]) {		
		parent::__construct('users');
		$this->searchUser($atts);
    }
	
	public function searchUser($atts, $set=false){
		$atts = (array) $atts;
		if(isset($atts['id']) && $atts['id']>0) {
			$result = $this->getBy('id', $atts['id']);
		};
	}
	
	public function isValid(){
		if(parent::isValid()){
			if(isset($this->status) && $this->status == 1){
				return true;
			}
		}
		return false;
	}
	
	public function setAll($datos = []){
		parent::setAll($datos);
		
	}
	
	public function getBy($column='id', $val=0){
		try {
			$result = parent::getBy($column, $val);
			if($result !== false && $this->id > 0){
				if(isset($this->permissions) && $this->permissions !== null && $this->permissions > 0){
					$sql = "SELECT E.* 
						FROM `{$this->getPrefix()}permissions` D 
						JOIN `{$this->getPrefix()}permissions_items` E 
						ON E.`id` = D.`permission` 
						WHERE D.`group` IN (?)";
					$result = $this->getAdapter()->FetchAllObject($sql, [$this->permissions]);
					
					if($result !== false && count($result) > 0){
						foreach($result as $perm){
							$this->permissions_items[$perm->tag] = $perm;
							$this->permission_items[] = strtolower($perm->tag);
						}
					}
					
					$sql = "SELECT * FROM `{$this->getPrefix()}permissions_group` WHERE `id` IN (?)";
					$result = $this->getAdapter()->FetchObject($sql, [$this->permissions]);
					if($result !== false){
						$this->permission_group = $result;
					}
				}
			}
			return $this;
		}
		catch(Exception $e){
			#echo "<b>Error:</b> ".($e->getMessage() . " [SQL-FRONT]: $sql");
			return $this;
		}
	}
}

