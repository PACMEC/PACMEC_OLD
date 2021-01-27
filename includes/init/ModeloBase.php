<?php 
/**
  * ModeloBase
  * 
  * 
  * @package    PACMEC
  * @author     FelipheGomez <feliphegomez@gmail.com>
  * 
  **/
class ModeloBase implements EntidadBase {	
	private $table;
	private $db;
	private $prefix;
	private $rules = [];
	private $columns = [];
	private $labels = [];
	private $records = [];
	
	/**
	* 
	* Constructor
	*
	*/
	public function __construct($table=null, $load_columns=true){
		try {
			if(!isset($table) || empty($table) || $table == null){ throw new Exception("No exite tabla configurada en el modelo ".@get_class($this).""); }
			if(!isset($GLOBALS['PACMEC']['DB']) || empty($GLOBALS['PACMEC']['DB']->getPrefix())){ throw new Exception("Conexion DB no encontrada"); }
			$this->db = $GLOBALS['PACMEC']['DB'];
			$this->prefix = $GLOBALS['PACMEC']['DB']->getPrefix();
			$this->table = $this->prefix.$table;
			if($load_columns == true){
				$this->loadColumns();
			}
			// if(method_exists($this, 'rules')) { $this->rules = $this->rules(); }
		} catch (Exception $e) {
			echo $e->getMessage();
			exit();
		}
	}
	
	public function getAdapter() {
		return $this->db;
	}	
	
    public function FetchObject($sql, $params = []){
        try {
            return $this->getAdapter()->FetchObject($sql,$params);
        }
        catch(Exception $e){
            return false;
        }
    }
	
    public function FetchAllObject($sql, $params = []){
        try {
            return $this->getAdapter()->FetchAllObject($sql,$params);
        }
        catch(Exception $e){
            return false;
        }
    }
	
	public function __sleep(){
		$r = [];
		foreach($this->columns as $k){ if(isset($this->{$k->name})){ $r[] = $k->name; } }
		return $r;
	}
	
	public function __toString(){
		$b = [];
		foreach($this->__sleep() as $k){
			if(isset($this->{$k})){
				$b[] = [$k, $this->{$k}];
			}
		}	
		return (string) (json_encode($b));
	}
		
	public function getBy($column, $val){
		$sql = "Select * FROM `{$this->getTable()}` WHERE `{$column}`=?";
		$result = $this->FetchObject($sql, [$val]);
		if($result !== false){ $this->setAll($result); }
		return $result;
	}
	
	public function getById($id){
		$sql = "Select * FROM `{$this->getTable()}` WHERE `id`=?";
		$result = $this->FetchObject($sql, [$id]);
		if($result !== false){ $this->setAll($result); }
		return $result;
	}
	
	public function getRules(){
		return $this->rules;
	}
	
	public function isValid(){
		#return count($this->records)>0 ? true : ((isset($this->id) && $this->id > 0 && $this->id !== null) ? true : false);
		return ((isset($this->id) && $this->id > 0 && $this->id !== null) ? true : false);
	}
	
	public function setAll($array = []){
		$array = (object) $array;
		foreach(array_keys($this->labels) as $label){
			if(isset($array->{$label})){
				$this->{$label} = $array->{$label};
			}
		}
	}
	
	public function getRand($limit = 1){
		$sql = "Select * FROM `{$this->getTable()}` ORDER BY RAND() LIMIT ?";
		if($limit > 1){
			$result = $this->FetchAllObject($sql, [$limit]);
		} else {
			$result = $this->FetchObject($sql, $limit);
		}
		return $result;
	}
	
	public function filterEq($filters=[]){
		$sql = "Select * FROM `{$this->getTable()}`";
		$partSqlWhere = "";
		$arrayValues = [];
		$queryValues = "";
		$i = 0;
		foreach($filters as $filter){
			$filter = (object) $filter;
			if($i == 0){ $sql .= " WHERE"; } else { $sql .= " AND"; }
			$sql .= " `{$filter->column}` IN (?)";
			$arrayValues[] = $filter->value;
			#$arrayValues[] = "'" . (is_array($filter->value) ? implode("','", $filter->value) : "{$filter->value}") . "'";
			$i++;
		}
		$result = $this->FetchObject($sql, $arrayValues);
		if($result !== false){ $this->setAll($result); }
		return $result;
	}
	
	public function setTable($table){
		$this->table = $table;
	}
	
	public function getPrefix(){
		return $this->prefix;
	}
	
	public function getLabels(){
		return $this->labels;
	}
	
	public function getTable(){
		return $this->table;
	}
	
    public function modelInitial($columna){
		/*
		Result: 
			{
			  "posicion_original": 1,
			  "columna_nombre": "id",
			  "nullValido": "NO",
			  "columna_value_default": null,
			  "data_tipo": "int",
			  "columna_tipo": "int(11)",
			  "length_max": null,
			  "columna_key": "PRI",
			  "columna_extra": "auto_increment",
			  "columna_comnetario": "",
			  "tabla_referencia": null,
			  "columna_referencia": null
			}
		*/
        $column = new stdClass();
        if(!is_object($columna)){ return $column; }
        $column->name = isset($columna->columna_nombre) ? $columna->columna_nombre : 'no_detect';
        $column->nullValid = (isset($columna->nullValido) && $columna->nullValido == 'YES') ? true : false;
        $column->value_default = $columna->columna_value_default;
        $column->type = $columna->data_tipo;
        $column->key = array_filter(explode(',', $columna->columna_key));
        $column->extra = array_filter(explode(',', $columna->columna_extra));
        $column->tbl_ref = $columna->tabla_referencia;
        $column->tbl_column = $columna->columna_referencia;
        $column->auto_increment = (array_search('auto_increment', $column->extra) !== false) ? true : false;
        $column->unique = (array_search('UNI', $column->key) !== false) ? true : false;
        $column->primary = (array_search('PRI', $column->key) !== false) ? true : false;
        $column->mult = (array_search('MUL', $column->key) !== false) ? true : false;
        $column->length_max = isset($columna->length_max) ? (int) $columna->length_max : false;

        $column->value_default = isset($column->value_default) ? $column->value_default : null;
        $column->nullValid = isset($column->nullValid) ? $column->nullValid : true;
        $column->required = ($column->auto_increment == true) ? false : true;
        $column->required = ($column->nullValid == true) ? false : true;

        $column->value = ($column->nullValid == true) ? $column->value_default : $this->get_value_default_sql($column->type, $column->value_default);
        $column->value = (strip_tags($column->value) == strip_tags("CURRENT_TIMESTAMP")) ? date("Y-m-d H:i:s") : $column->value;

        return $column;
    }

	public function loadColumns(){
        try {
			$db = DB_database;
			$sql = "SELECT 
                `tbl_columns`.`ORDINAL_POSITION` AS `posicion_original`,
                `tbl_columns`.`COLUMN_NAME` AS `columna_nombre`, 
                `tbl_columns`.`IS_NULLABLE` AS `nullValido`,
                `tbl_columns`.`COLUMN_DEFAULT` AS `columna_value_default`,
                `tbl_columns`.`DATA_TYPE` AS `data_tipo`,
                `tbl_columns`.`COLUMN_TYPE` AS `columna_tipo`,
                `tbl_columns`.`CHARACTER_MAXIMUM_LENGTH` AS `length_max`,
                `tbl_columns`.`COLUMN_KEY` AS `columna_key`,
                `tbl_columns`.`EXTRA` AS `columna_extra`,
                `tbl_columns`.`COLUMN_COMMENT` AS `columna_comnetario`,
                `tbl_rship`.`REFERENCED_TABLE_NAME` AS `tabla_referencia`,
                `tbl_rship`.`REFERENCED_COLUMN_NAME` AS `columna_referencia` 
            FROM `information_schema`.`columns` AS `tbl_columns` 
            LEFT JOIN `information_schema`.`KEY_COLUMN_USAGE` AS `tbl_rship` 
            ON 
                `tbl_rship`.`CONSTRAINT_SCHEMA` IN (?) AND `tbl_columns`.`COLUMN_NAME` = `tbl_rship`.`COLUMN_NAME` AND `tbl_columns`.`table_name` = `tbl_rship`.`table_name` AND `tbl_rship`.`REFERENCED_TABLE_SCHEMA` IS NOT NULL 
			WHERE `tbl_columns`.`table_schema` IN (?) AND `tbl_columns`.`table_name` IN (?) ORDER BY `tbl_columns`.`ORDINAL_POSITION` ASC";
			
			$sending = [$db, $db, "{$this->getTable()}"];
			$result = $this->getAdapter()->FetchAllObject($sql, $sending);
			
			if($result !== null && count($result) > 0){
				#$this->columns = $result;
				foreach ($result as $column) {
					$column = $this->modelInitial($column);
					$this->columns[] = $column;
					$this->{$column->name} = $column->value;
					$inArray = array_search('UNI', $column->key);
					if(isset($this->isUnique) && $this->isUnique == null && $inArray !== false){ $this->isUnique = true; }
					// Create RULE
					#if(!method_exists($this, 'rules')) {}
					$rule = [];
					
					$rule["name"] = $column->name;
					$rule["required"] = $column->required;
					$rule["unique"] = $column->unique;
					if($column->length_max !== false && $column->length_max > 0){ $rule["length_max"] = $column->length_max; }
					$this->rules[$column->name] = $rule;
					// CREATE LABELS
					$this->labels[$column->name] = $column->value;
				}
			} else {
				throw new Exception("No se cargaron las columnas del modelo ".@get_class($this));
			}
        }
        catch(Exception $e){
			echo $e->getMessage();
            return false;
			exit();
        }
	}

    public function get_value_default_sql($type="varchar", $default=null){
        /*"boolean" o "bool"
            "integer" o "int"
            "float" o "double"
            "string"
            "array"
            "object"
            "null"*/
        switch ($type) {
            case 'varchar':
                return strip_tags($default);
                break;
            case 'text':
                return is_string($default) ? strip_tags($default) : $default;
                break;
            case 'int':
                return (int) $default;
                break;
            case 'datetime':
                return (string) $default;
                break;
            default:
                return is_string($default) ? strip_tags($default) : $default;
                break;
        }
    }
			
	public function getColumns($include_id=false){
		 $items = [];
		 foreach($this->columns as $item){
			 if($item->name == 'id' && $include_id == false){
				 
			 }else{
				 $items[] = $item->name;
			 }
		 }
		 return $items;
	}
}