<?php
namespace PirloDB;

use \PDO;

class Database
{
  private static $_instance = null;
  private $_conn, $_table, $_query, $_columns = '*', $_params = null, $_attr = null,
          $_statement, $_results, $_prevData;

   /**
     * setup database config
     */
  protected function __construct($server, $user, $pass, $db_name)
  {
    try {
      $this->_conn = new PDO("mysql:host=$server;dbname=$db_name", $user, $pass);
      $this->_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
      echo 'error! :' . $e->getMessage();
    }
  }

  public function __clone()
  {
    return false;
  }

  /**
    * Get Database instance
    * Singleton pattern
    */
  public static function getInstance($server, $user, $pass, $db_name)
  {
    if(!isset(self::$_instance))
     self::$_instance = new Database($server, $user, $pass, $db_name);

    return self::$_instance;
  }

  /**
    * Set which table to use
    * @param string $table
    */
  public function setTable($table)
  {
    $this->_table = $table;
    return $this;
  }

  /**
    * Set which table to use
    * @param string $columns
    */
  public function select($columns = '*')
  {
    $this->_query = "SELECT $columns FROM $this->_table";
    $this->_columns = $columns;
    return $this;
  }

  /**
    * Set where clause for query, call getWhere method
    * @param string $col, string $sign, string $value, string $bridge
    */
  public function where($col, $sign, $value, $bridge = ' AND ')
  {
    $this->_query = "SELECT $this->_columns FROM $this->_table WHERE"; //achtung extra whitespace at the end

    //first where method
    if (count($this->_prevData) == 0) {
      $bridge = '';
    }

    $this->_prevData[]  = array(
                            'col'    => $col,
                            'sign'   => $sign,
                            'value'  => $value,
                            'bridge' => $bridge,
                          );

    $this->getWhere($bridge);
    return $this;
  }

  /**
    * Set where clause for query, call where method, set bridge as OR
    * @param string $col, string $sign, string $value, string $bridge
    */
  public function orWhere($col, $sign, $value)
  {
    $this->where($col, $sign, $value, ' OR ');
    return $this;
  }

  /**
    * Adding where cluase as $_attr property
    * @param string $bridge
    */
  public function getWhere($bridge)
  {
    //clear attribute and params if not first time
    if (count($this->_prevData) >  1)
    {
      $this->_attr   = '';
      $this->_params = [];
    }

    $x = 1;
    foreach ($this->_prevData as $prev) {

      if ($x <= count($this->_prevData)) {
        $this->_attr .= $prev['bridge'];
      }

      $this->_attr    .= $prev['col'] .' '.  $prev['sign'] .' '. '?';
      $this->_params[] = $prev['value'];

      $x++;
    }

    return $this;
  }

  /**
    * Create a new record(s)
    * @param array $fields
    */
  public function create($fields = array())
  {
    $cols   = implode(", ", array_keys($fields));
    $values = '';
    $x      = 1;

    foreach ($fields as $field) {
      $this->_params[] = $field;
      $values .= '?';

      if ($x < count($fields)) {
        $values .= ', ';
      }
      $x++;
    }

    $this->_query  = "INSERT INTO $this->_table($cols) VALUES ($values)";
    $this->run();
  }

  /**
    * Update a record(s)
    * @param array $fields
    */
  public function update($fields = array())
  {
    $cols   = '';
    $x      = 1;
    //get previous data count
    $total_prev = count($this->_params);

    foreach ($fields as $key => $value) {
      $this->_params[] = $value;
      $cols .= $key ."=?";

      if ($x < count($fields)) {
        $cols .= ', ';
      }
      $x++;
		}

    //pop previous array to end, kalo multiple where
    for ($i=0; $i<$total_prev; $i++) {
      $stack = array_shift($this->_params);
      $this->_params[] = $stack;
    }

    $this->_query = "UPDATE $this->_table SET $cols WHERE";
    $this->run();
  }

  /**
    * Delete a record(s)
    */
  public function delete()
  {
    $this->_query = "DELETE FROM $this->_table WHERE";
    $this->run();
  }

  /**
    * Retrieve results as object
    */
  public function all()
  {
    $this->run();
    return $this->_statement->fetchAll(PDO::FETCH_OBJ);
  }

  /**
    * Retrieve a result as object
    */
  public function first()
  {
    $this->run();
    return $this->_statement->fetch(PDO::FETCH_OBJ);
  }

  /**
    * Clear everything to allow multiple actions
    */
  public function clearAll()
  {
    $this->_attr   = '';
    $this->_query  = '';
    $this->_params = null;
    $this->_prevData = null;
  }

  /**
    * Order the query by
    * @param string $col, string $type
    */
  public function orderBy($col = 'id', $type)
  {
    $this->_attr .= " ORDER BY $col $type";
    return $this;
  }

  /**
    * limit the query by
    * if used, orderBy must come first
    * @param int $num
    */
  public function take($num)
  {
    $this->_attr .= " LIMIT $num";
    return $this;
  }

  /**
    * Prepare and execute query
    * clear all previous data
    */
  public function run()
  {
    try {
      $this->_statement = $this->_conn->prepare($this->_query . ' ' . $this->_attr);
      $this->_statement->execute($this->_params);
      $this->clearAll(); //clear for multiple action
    } catch (Exception $e) {
      echo $e->getMessage();
    }
  }

  //rowCount

  //return as JSON

  //fetch array

  //fetch assoc

  //fetch object
}
