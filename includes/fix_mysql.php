<?php
/**
 * MySQL to MySQLi Compatibility Wrapper
 * This allows legacy PHP 5.x code to run on PHP 7/8.
 */

if (!function_exists('mysql_connect')) {
    $GLOBALS['mysql_link'] = null;

    function mysql_connect($host, $user, $pass) {
        $GLOBALS['mysql_link'] = mysqli_connect($host, $user, $pass);
        return $GLOBALS['mysql_link'];
    }

    function mysql_select_db($db, $link = null) {
        $link = $link ?: $GLOBALS['mysql_link'];
        return mysqli_select_db($link, $db);
    }

    function mysql_query($query, $link = null) {
        $link = $link ?: $GLOBALS['mysql_link'];
        return mysqli_query($link, $query);
    }

    function mysql_fetch_array($result, $type = MYSQLI_BOTH) {
        return mysqli_fetch_array($result, $type);
    }

    function mysql_fetch_assoc($result) {
        return mysqli_fetch_assoc($result);
    }

    function mysql_num_rows($result) {
        return mysqli_num_rows($result);
    }

    function mysql_error($link = null) {
        $link = $link ?: $GLOBALS['mysql_link'];
        return mysqli_error($link);
    }

    function mysql_real_escape_string($string, $link = null) {
        $link = $link ?: $GLOBALS['mysql_link'];
        return mysqli_real_escape_string($link, $string);
    }
    
    function mysql_insert_id($link = null) {
        $link = $link ?: $GLOBALS['mysql_link'];
        return mysqli_insert_id($link);
    }

    function mysql_close($link = null) {
        $link = $link ?: $GLOBALS['mysql_link'];
        return mysqli_close($link);
    }
}
?>
