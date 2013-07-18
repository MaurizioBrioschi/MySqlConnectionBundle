MySqlConnectionBundle
=====================

Symfony 2.+ Bundle to manage Mysql connection and make repositories for objects

Configure
=======


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

License
=======

This code is under MIT license present in the root directory