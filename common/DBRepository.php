<?php

/**
 * Class repository for db query base, extending this class is the base for your repository
 * @author Maurizio Brioschi (maurizio.brioschi@ridesoft.org) 
 * @version 0.2 
 * (c) Maurizio Brioschi (maurizio.brioschi@ridesoft.org) 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ridesoft\MySqlConnectionBundle\common;

use ridesoft\MySqlConnectionBundle\common\MySqlConnection;

class DBRepository {

    protected static $DBRepository;
    private $connection;

    /**
     * Constructor
     * @param MySqlConnection $DBConnection
     */
    public function __construct(MySqlConnection $DBConnection) {
        $this->connection = $DBConnection;
    }

    /**
     * Singleton for inizialize repository class
     * @param MySqlConnection $DBConnection
     * @return $this
     */
    public static function Inizialize(MySqlConnection $DBConnection) {
        /*         * load configuration */
        if (!isset(self::$DBRepository)) {
            $c = __CLASS__;
            self::$DBRepository = new $c($DBConnection);
        }
        return self::$DBRepository;
    }

    /**
     * get mysql db connection
     * @return MySqlConnection
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * execute a query 
     * @param string $query
     * @return list($dbd,$i) where $dbd is the recordset and i the number of rows 
     */
    public function select($SQL) {
        try {
            return $this->connection->exeSQL($SQL);
        } catch (Exception $e) {
            return $this->connection->LogError("DBRepository->genericInsert($table)->$SQL");
            return false;
        }
    }

    /**
     * do an insert $table with $array parameters and return is $keyField
     * @param string $table
     * @param string $array
     * @param string $keyField
     * @return int
     */
    public function Insert($table, $array, $keyField = "id") {
        $SQL = "INSERT INTO $table(";
        foreach (array_keys($array) as $key) {
            $SQL .= $key . ",";
        }
        $SQL = substr($SQL, 0, strlen($SQL) - 1);
        $SQL .= ") VALUES (";
        foreach (array_keys($array) as $key) {
            if (gettype($array[$key]) == 'object') {
                if (get_class($array[$key]) == 'DateTime') {
                    $SQL .= "'" . $this->connection->cleanField($array[$key]->format('Y-m-d H:i:s')) . "',";
                }
            } else {
                $SQL .= "'" . $this->connection->cleanField($array[$key]) . "',";
            }
        }
        $SQL = substr($SQL, 0, strlen($SQL) - 1);
        $SQL .= ");";

        try {
            $this->connection->exeSQL($SQL);
            $SQL = "select max($keyField) as id FROM $table;";
            list($dbd, $i) = $this->connection->exeSQL($SQL);
            $recordset = $this->connection->getResult($dbd, 0);
            return intval($recordset["id"]);
        } catch (Exception $e) {
            return $this->connection->LogError("DBRepository->genericInsert($table)->$SQL");
            return 0;
        }
    }

    /**
     * do an update to $table cwith $array parameters on $keyField with $keyValue
     * @param string $table
     * @param type $array
     * @param string $keyValue
     * @param string $keyField
     * @return boolean
     */
    public function Update($table, $array, $keyValue, $keyField = "id") {
        $SQL = "UPDATE $table SET ";
        foreach (array_keys($array) as $key) {
            $SQL .= $key . "='";
            if (gettype($array[$key]) == 'object') {
                if (get_class($array[$key]) == 'DateTime') {
                    $SQL .= $this->connection->cleanField($array[$key]->format('Y-m-d H:i:s')) . "',";
                }
            } else {
                $SQL .= $this->connection->cleanField($array[$key]) . "',";
            }
        }
        $SQL = substr($SQL, 0, strlen($SQL) - 1);
        $SQL .= " WHERE $keyField='" . $this->connection->cleanField($keyValue) . "'";
        try {
            $this->connection->exeSQL($SQL);
            return true;
        } catch (Exception $e) {
            return $this->connection->LogError("DBRepository->Update($table)->$SQL");
            return false;
        }
    }

    /**
     * Delete a record in $table have $keyValue like $keyField
     * @param type $table
     * @param type $keyValue
     * @param type $keyField
     * @return boolean
     */
    public function Delete($table, $keyValue, $keyField = "id") {
        $SQL = "DELETE FROM $table WHERE $keyField = $keyValue;";
        try {
            $this->connection->exeSQL($SQL);
            return true;
        } catch (Exception $e) {
            $this->connection->LogError("DBRepository->Delete($table,$keyValue, $keyField)->$SQL");
            return false;
        }
    }

}

?>
