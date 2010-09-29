<?php

if (!defined('IN_VWS')) {
    exit('Access Denied.');
}

class dbstuff {

    var $version = '';
    var $link;

    function connect($dbhost, $dbuser, $dbpassword, $dbname = '', $pconnect = false) {
        $func = $pconnect ? 'mysql_pconnect' : 'mysql_connect';
        if (!$this->link = @$func($dbhost, $dbuser, $dbpassword, true)) {
            die('Cannot connect to MySQL server');
        }
        else {
            mysql_query("set names 'utf8';", $this->link);
        }
        $dbname && @mysql_select_db($dbname, $this->link);
    }

    function select_db($dbname) {
        return mysql_select_db($dbname, $this->link);
    }

    function fetch_array($query, $result_type = MYSQL_ASSOC) {
        return mysql_fetch_array($query, $result_type);
    }

    function fetch_first($sql) {
        return $this->fetch_array($this->query($sql));
    }

    function result_first($sql) {
        return $this->result($this->query($sql), 0);
    }

    function query($sql, $type = '') {
        $func = $type == 'UNBUFFERED' && @function_exists('mysql_unbuffered_query') ?
            'mysql_unbuffered_query' : 'mysql_query';

        if (!($query = $func($sql, $this->link))) {
            die('MySQL Query Error');
        }

        return $query;
    }

    function result($query, $row = 0) {
        $query = @mysql_result($query, $row);
        return $query;
    }

    function num_rows($query) {
        $query = mysql_num_rows($query);
        return $query;
    }

    function insert_id() {
        return ($id = mysql_insert_id($this->link)) >= 0 ? $id : $this->result($this->query("SELECT last_insert_id()"), 0);
    }
    
    function version() {
        return $this->version = mysql_get_server_info($this->link);
    }

    function close() {
        return mysql_close($this->link);
    }

}

?>
