<?php 
/**
  * Menu
  * 
  * 
  * @package    PACMEC
  * @author     FelipheGomez <feliphegomez@gmail.com>
  * 
  **/

class Pages extends ModeloBase {
	private $table = "pages";
	private $prefix = null;
	private $db = null;
	public $id;
	public $page_name;
	public $page_slug;
	public $page_author;
	public $page_date;
	public $page_content;
	public $page_thumb;
	public $page_title;
	public $page_excerpt;
	public $page_status;
	public $comment_status;
	public $page_password;
	public $page_modified;
	public $page_parent;
	public $comment_count;
	
	public function __construct($args=[]){
		$args = (array) $args;
		parent::__construct("pages");
		if(isset($args['id'])){ $this->getBy('id', $args['id']); }
		if(isset($args['page_name'])){ $this->getBy('page_name', $args['page_name']); }
		if(isset($args['page_slug'])){ $this->getBy('page_slug', $args['page_slug']); }
	}
	
	public static function allLoad() : array {
		$r = [];
		if(!isset($GLOBALS['_PACMEC']['DB']['conn']) || !isset($GLOBALS['_PACMEC']['DB']['prefix'])){ return $r; }
		foreach($GLOBALS['_PACMEC']['DB']['conn']->FetchAllObject("SELECT * FROM {$GLOBALS['_PACMEC']['DB']['prefix']}pages ", []) as $menu){
			$r[] = new Pages($menu);
		}
		return $r;
	}
		
		
	public function getBy($column='id', $val=""){
		try {
			$this->setThis($GLOBALS['_PACMEC']['DB']['conn']->FetchObject("SELECT * FROM {$this->getTable()} WHERE `{$column}`=?", [$val]));
			return $this;
		}
		catch(Exception $e){
			#echo "<b>Error:</b> ".($e->getMessage() . " [SQL-FRONT]: $sql");
			return $this;
		}
	}
	
	private function setThis($arg){
		if($arg !== null){
			if(is_object($arg) || is_array($arg)){
				$arg = (array) $arg;
				foreach($arg as $k=>$v){
					$this->{$k} = $v;
				}
			}
		}
	}
	
	public function isValid(){
		return $this->id > 0 ? true : false;
	}
}

