<?php 
/* *******************************
 *
 * Developer by FelipheGomez
 *
 * Clase básica para adminsitrar sesiones
 *
 * ******************************/

// class PacmecSessionHandler 

class SysSession implements SessionHandlerInterface {
    private $link;
	
    public function open($savePath, $sessionName) {
        $link = $GLOBALS['PACMEC']['DB'];
        if($link){
            $this->link = $link;
            return true;
        } else { return false; }
    }
	
    public function close() {
        #mysqli_close($this->link);
		$this->link = NULL;
        return true;
    }
	
    public function read($id) {
		try {
			$result = $this->link->FetchObject("SELECT Session_Data FROM {$GLOBALS['PACMEC']['DB']->getPrefix()}sessions WHERE Session_Id=? AND Session_Expires > ?", [$id, date('Y-m-d H:i:s')]);;
			if($result !== false && isset($result->Session_Data)){ return $result->Session_Data; } else { return ""; }
		}
		catch(Exception $e){ 
			echo $e->getMessage();
			return "";
		}
    }
	
    public function write($id, $data) {
		try {
			$DateTime = date('Y-m-d H:i:s');
			$NewDateTime = date('Y-m-d H:i:s',strtotime($DateTime.' + 1 hour'));
			$result = $this->link->FetchObject("REPLACE INTO {$GLOBALS['PACMEC']['DB']->getPrefix()}sessions SET Session_Id=?, Session_Expires=?, Session_Data=?", [$id, $NewDateTime, $data]);
			if($result !== false){ return true; } else { return false; }
		}
		catch(Exception $e){ 
			echo $e->getMessage();
			return false;
		}
    }
	
    public function destroy($id) {
		try {			
			$result = $this->link->FetchObject("DELETE FROM {$GLOBALS['PACMEC']['DB']->getPrefix()}sessions WHERE Session_Id =?", [$id]);
			if($result !== false){ return true; } else { return false; }
		}
		catch(Exception $e){ 
			echo $e->getMessage();
			return false;
		}
    }
	
    public function gc($maxlifetime) {
		try {			
			$result = $this->link->FetchObject("DELETE FROM {$GLOBALS['PACMEC']['DB']->getPrefix()}sessions WHERE ((UNIX_TIMESTAMP(Session_Expires) + ?) < ?)", [$maxlifetime, $maxlifetime]);
			if($result !== false){ return true; } else { return false; }
		}
		catch(Exception $e){ 
			echo $e->getMessage();
			return false;
		}
    }
}
