#Pirlo_DB :: PDO Abstract Class

Abstract database class using PDO, the end syntax inspired by Eloquent from laravel but more basic implementations
[Eloquent Laravel] (https://laravel.com/docs/5.1/eloquent). Not much tested yet, it made for my own learning purpose but feel free to use it on your project, and let me know if there are any bugs or anything i can improve.


##Install

```
composer require "hilmanrdn/pirlo-db: dev-master"
```

##Todo

* show error, success or fail
* return as JSON
* join
* other relations

##Usage Example

* Load database

  ```
  use PirloDB\Database;

  require_once 'vendor/autoload.php';

  $test = Database::getInstance($server, $user, $pass, $db_name);
  $test->setTable($tableName);
  ```

* Select and retrieve data

 Always use select!

 Select() can be blank to select all columns, or specify column(s) name in one string.

 There are two possibilities, use all() to get an array, or first() for only one data

 ```
  $test->select()->all();
  $test->select()->first();
  $test->select('username, id')->first();
```  

* Where

  Where() has 3 parameters (column, sign and value)

  It can be used multiple times to use 'AND', or use orwhere() to use 'OR'

  ```
  $test->where('username', '=', 'name')->first();
  $test->select('username')->where('username', '=', 'name')->all();
  $test->where('username', '=', 'hilman')->where('password', '=', 'password')->all();
  $test->where('username', '=', 'name1')->orWhere('username', '=', 'name2')->all();
  ```

* Insert

 Use create() and associatve array which formatted by column_name => column_value
 ```
 $test->create([
   'username' => 'name',
   'password' => 'pass',
 ]);  
 ```

* Update

  Use where() to select which row want to be entered
 ```  
 $test->where('username', '=', 'name')->update([
   'username' => 'newName',
   'password' => 'newPass',
 ]);
 ```
* Delete

  Use where() to select which row want to be deleted
  ```
  $test->where('username', '=', 'name')->delete();
  ```

* orderBy

  orderBy() has two parameter (column and type[DESC or ASC])

  ```
  $test->select()->orderBy('username', 'DESC')->all();
  ```

* take

  take to limit the result, put the number as parameter, if chained with orderBy(), orderBy() must be used first

  ```
  $test->select()->orderBy('username', 'DESC')->take(3)->all();
  ```
