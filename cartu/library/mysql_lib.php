<?php

/* G Mysql LIB v1.0 beta */

class sql {
	public static function connect(){
	global $_CONFIG;
		if (!$cn=@mysql_connect($_CONFIG['host'], $_CONFIG['user'], $_CONFIG['pass'])){
			err_log(mysql_error());
			die(returnErrorResponse('-3','Connection permanently Problem'));
		}
	return $cn;
	}
	
	public static function close(){
		@mysql_close();
	}
	public static function chooseDB($DB){
	global $_CONFIG;
		$DB = isset($DB) ? $_CONFIG['db'] : $DB;
		if (!mysql_select_db($DB)){
			err_log(mysql_error());
			die(returnErrorResponse('-3','Connection permanently Problem'));

		}
	}
	public static function prepareDB($DB=false){
		self::connect();
		self::chooseDB($DB);
		self::transact_UTF8();
	}
	public static function transact_UTF8(){
		mysql_query("SET CHARACTER SET utf8");
	}
	##########################################
	public static function CNT($sql,$DB = true){
		$CNT = 0;
		self::prepareDB();
		$CNT = @mysql_num_rows(mysql_query($sql));
		self::close();
		return ($CNT>0) ? $CNT : 0;
	}
	
	public static function getElem($sql,$DB = true){
		self::prepareDB($DB);
		$result = mysql_query($sql);
		$row = mysql_fetch_row($result);
		self::close();
		return $row[0];
	}

	public static function getColum($sql,$DB = true){
		self::prepareDB();
		$result= mysql_query($sql);
		$column = array();
		for($i=0;$row=mysql_fetch_row($result);$i++){
			$column[] = $row[0];
		}
		self::close();
		return $column;
	}
	
	public static function getRow($sql,$DB = true){
		self::prepareDB();
		$result = mysql_query($sql);
		return mysql_fetch_assoc($result);
	}

	public static function getRows($sql,$DB = true){
		self::prepareDB();
		$result = mysql_query($sql);
		$rows = array();
		for($i=0;$row=mysql_fetch_assoc($result);$i++){
			$rows[] = $row;
		}
		self::close();
		return $rows;
	}

	public static function insert($sql, $id = false, $DB = true){
		self::prepareDB();
		$result = mysql_query($sql);
		$result = (($id==false) ? "" : mysql_insert_id());
		self::close();
		return $result;
	}

	public static function update($sql,$DB = true){
		self::prepareDB();
		mysql_query($sql);
		self::close();
	}

	public static function delete($sql,$DB = true){
		self::prepareDB();
		mysql_query($sql);
		self::close();
	}

	public static function transaction($sql,$DB = true){
		self::prepareDB();
		mysql_query($sql);
		self::close();
	}
}
			  
?>