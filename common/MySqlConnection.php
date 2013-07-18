<?php

/**
 * Class for manage mysql connection
 * @author Maurizio Brioschi (maurizio.brioschi@ridesoft.org) 
 * @version 0.2 
 * (c) Maurizio Brioschi (maurizio.brioschi@ridesoft.org) 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ridesoft\MySqlConnectionBundle\common;

use ridesoft\MySqlConnectionBundle\DependencyInjection\Configuration;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class MySqlConnection {

    protected $link;
    private $server, $username, $password, $db;
    protected static $myConnection;

    /**
     * constructor
     * @param string $server
     * @param string $username
     * @param string $password
     * @param string $db 
     */
    public function __construct($host, $username, $password, $dbname) {
        $this->server = $host;
        $this->username = $username;
        $this->password = $password;
        $this->db = $dbname;
        $this->connect();
        return $this;
    }

    /**
     * connect to my sql db
     */
    private function connect() {
        $this->link = mysql_connect($this->server, $this->username, $this->password, true) or die("Impossible to connect to mySql DB: " . mysql_error());
        ;
        mysql_select_db($this->db, $this->link) or die("Schema don't exist: " . mysql_error());
        mysql_set_charset('utf8', $this->link);
    }

    /**
     * sleep method for serialize object
     * @return serialize array
     */
    public function __sleep() {
        return array('server', 'username', 'password', 'db');
    }

    /**
     * wakeup method for serialize object
     * @return the connection
     */
    public function __wakeup() {

        $this->connect();
    }

    /**
     * non permette la clonazione della connessione
     */
    public function __clone() {
        echo 'connection already exist!';
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }

    /**
     * close connection
     */
    public function close() {
        mysql_close($this->link);
    }

    /*
     * Esegue una query sul database
     * @param string $statement
     */

    function exeSQL($statement) {

        $result = mysql_query($statement, $this->link);
        if ($result > 0) {
            $num_rows = @mysql_numrows($result);
            return array($result, $num_rows);
        }else
            throw new Exception(mysql_error());
    }

    /*
     * Inizialize a transaction
     */

    function beginTransaction() {

        @mysql_query("BEGIN");
    }

    /*
     * commit transaction
     */

    function CommitTransaction() {
        @mysql_query("COMMIT");
    }

    /*
     * roll back transaction
     */

    function RollBackTransaction() {
        @mysql_query("ROLLBACK");
    }

    /*
     * get a recordset row
     * @param array $result
     * @param int $i
     */

    function getResult($result, $i = -1) {
        if ($i >= 0) {
            @mysql_data_seek($result, $i);
        }
        return mysql_fetch_array($result);
    }

    /*
     * get numbers of fields
     * @param array $result
     */

    public function getNumFields($result) {
        return @mysql_num_fields($result);
    }

    /**
     * clean field for db query
     * @param type $field
     * @return type
     */
    public function cleanField($field) {
        $field = addslashes($field);
        if (!get_magic_quotes_gpc()) {
            $field = stripslashes($field);
        }

        $field = mysql_real_escape_string($field);
        return $field;
    }

    /**
     * return the error 
     * @param string $msg
     */
    public function LogError($msg) {
        $e->error_get_last();
        return $msg . "->" . $e["message"];
    }

}

?>