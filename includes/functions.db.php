<?php
/* G Mysql LIB v2.0 beta */
									  
class sql {

	public static function connect(){
		global $_CFG;
		$cn = mysqli_connect($_CFG['mysqlServer'], $_CFG['mysqlUser'], $_CFG['mysqlPass']);
		mysqli_select_db($cn,$_CFG['mysqlDB']);
		self::transact_UTF8($cn);
		return $cn;
	}
	
	private static function close($cn){
		@mysqli_close($cn);
	}

	public static function transact_UTF8($cn){
		mysqli_query($cn,"SET CHARACTER SET utf8");
	}
	public static function safe($string){
		$cn = self::connect();
		return mysqli_real_escape_string($cn,$string);
	}
	public static function safeArray($array){
		$cn = self::connect();
		foreach($array as $k => $v){
			$array[$k] = mysqli_real_escape_string($cn,$v);
		}
		return $array;
	}
	##########################################
	public static function getElem($sql){
		$cn = self::connect();
		$r = mysqli_query($cn, $sql);
		$row = mysqli_fetch_row($r);
		self::close($cn);
		return $row[0];
	}
	public static function CNT($sql){
		$r = 0;
		$cn = self::connect();
		$rows = mysqli_query($cn, $sql);
		$r = @$rows->num_rows;
		self::close($cn);
		return $r;
	}
	public static function getCol($sql){
		$cn = self::connect();
		$r = mysqli_query($cn, $sql);
		$column = array();
		for($i=0;$row=mysqli_fetch_row($r);$i++){
			$column[] = $row[0];
		}
		self::close($cn);
		return $column;
	}
	public static function getRow($sql){
		$cn = self::connect();
		$result = mysqli_query($cn,$sql);
		return mysqli_fetch_assoc($result);
	}
	public static function getRows($sql){
		$cn = self::connect();
		$result = mysqli_query($cn,$sql);
		$rows = array();
		for($i=0;@$row=mysqli_fetch_assoc($result);$i++){
			$rows[] = $row;
		}
		self::close($cn);
		return $rows;
	}
	public static function insert($sql, $id = false){
		$cn = self::connect();
		$result = mysqli_query($cn,$sql);
		$err = mysqli_error($cn);
		if($err) echo $err;
		$result = (($id==false) ? "" : mysqli_insert_id($cn));
		self::close($cn);
		return $result;
	}
	public static function update($sql){
		$cn = self::connect();
		mysqli_query($cn,$sql);
		self::close($cn);
	}
	public static function delete($sql){
		$cn = self::connect();
		mysqli_query($cn,$sql);
		self::close($cn);
	}

}
?>