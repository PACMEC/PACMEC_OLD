<?php 
/**
  * Menu
  * 
  * 
  * @package    PACMEC
  * @author     FelipheGomez <feliphegomez@gmail.com>
  * 
  **/

class Menus extends ModeloBase {
	private $table = "menus";
	private $prefix = null;
	private $db = null;
	public $id;
	public $name;
	public $slug;
	public $items = [];
	
	public function __construct($args=[]){
		$args = (array) $args;
		parent::__construct("menus");
		if(isset($args['id'])){
			$this->getBy('id', $args['id']);
		}
	}
	
	public static function allLoad() : array {
		$r = [];
		# if(!isset($GLOBALS['PACMEC']['DB']) || empty($GLOBALS['PACMEC']['DB']->getPrefix())){ return $r; }
		
		foreach($GLOBALS['PACMEC']['DB']->FetchAllObject("SELECT * FROM {$GLOBALS['PACMEC']['DB']->getPrefix()}menus ", []) as $menu){
			$r[] = new Self($menu);
		}
		return $r;
	}
	
	public function getBy($column='id', $val=""){
		try {
			$this->setThis($GLOBALS['PACMEC']['DB']->FetchObject("SELECT * FROM {$this->getTable()} WHERE `{$column}`=?", [$val]));
			return $this;
		}
		catch(Exception $e){
			#echo "<b>Error:</b> ".($e->getMessage() . " [SQL-FRONT]: $sql");
			return $this;
		}
	}
	
	private function loadItemsMenu($id=0, $parent = 0){
		$r = [];
		foreach($GLOBALS['PACMEC']['DB']->FetchAllObject("SELECT * FROM {$GLOBALS['PACMEC']['DB']->getPrefix()}menus_items WHERE `menu`=? AND `parent`=?", [$id,$parent]) as $item){
			$childs = $this->loadItemsMenu($id, $item->id);
			$item->childs = [];
			if($childs !== false){
				$item->childs = $childs;
			}
			$r[] = $item;
		}
		return $r;
	}
	
	private function setThis($arg=[]){
		$arg = (array) $arg;
		foreach($arg as $k=>$v){
			if(isset($this->{$k})){
				$this->{$k} = $v;
			}
		}
		if($this->isValid()){
			$this->items = $this->loadItemsMenu($this->id);
		}
	}
}

