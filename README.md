MySqlConnectionBundle
=====================

Symfony 2.+ Bundle to manage Mysql connection and make repositories for objects.

Useful if you need to do some operation to your mysql based app without doctrine or if you need a repository for a entity that doctrine can't provide (for example is is a relational database).
Simple, light and alternative bundle for relational database.

You can connect to mysql using service mysqlconnection or thown DBRepository class you can call a select, update, insert or delete to your mysql database.


Configure
=======
```php
    //config.yml
    my_sql_connection:
        username: myusr
        password: mypwd
        dbname: mydatabase
        host: localhost

  ```

How to use
=======
## Use MySqlConnection object 
To istanziate a Mysql object and execute some operation to db:
```php
      ...
      use ridesoft\MySqlConnectionBundle\common\MySqlConnection;
      ...
      $connection = $this->get('MySqlConnection'); 
      list($dbd,$i) = $connection->exeSQL("SELECT * from user;");
      $recordset = $connection->getResult($dbd,0);
      echo $recordset["id"];

  ```
... for others functions look into the class

## Use DBRepository object 

this example is for an update 
```php
      ...
      use ridesoft\MySqlConnectionBundle\common\DBRepository;
      ...
       $repository = DBRepository::Inizialize($this->get('MySqlConnection'));
       $myarray = array();
       $myarray["name"] = "maurizio";
       $myarray["surname"] = "brioschi";
       /*this function set name=maurizio and surname=brioschi to the id=666 on table user
       * N.B. if you have a form with same key of your table db, you can execute the function just thrown $_POST
       */
       if($repository->Update("user",$myarray,"666","id"))
            echo "OK";
       else
            echo "KO";

  ```
... for other functions look into the class
You can extend this class and create your own repository for your objects using this useful functuion

## Create your own repository
```php
<?php

...
use ridesoft\MySqlConnectionBundle\common\MySqlConnection;
use ridesoft\MySqlConnectionBundle\common\DBRepository;

class TestRepository extends DBRepository{
    protected static $TestRepository;
    
    public static function Inizialize(MySqlConnection $DBConnection) {
        /*         * load configuration */
        if (!isset(self::$TestRepository)) {
            $c = __CLASS__;
            self::$TestRepository = new $c($DBConnection);
        }
        return self::$TestRepository;
    }
    
    public function getUsers() {
        return $this->Select("SELECT * FROM tblusers");
    }
}
?>
 ```
.. now use your repository:
```php
      ...
      
      $repository = TestRepository::Inizialize($this->get('MySqlConnection'));
      list($dbd,$i) = $repository->getUsers();
      $recordset = $repository->getConnection->getResult($dbd,0);
      

  ```
License
=======

This code is under MIT license present in the root directory
