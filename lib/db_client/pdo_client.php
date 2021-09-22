<?php
/*
 *           Doorkeeper Database Client
 *
 *        Class to handle Database Connection and Queries
 * 
 *        A new PDO based database class (2020)
 * 
 *          Note. MySQLi might be better for MySQL databases..
 *
 *        Author: Jacob (JacobSeated)
 */

namespace doorkeeper\lib\db_client;

use Exception, PDO;

class pdo_client implements db_client_interface
{

  private $pdo;

  function __construct(object $credentials)
  {
    $this->s = $credentials;
    $this->connect();
  }

  public function connect()
  {

    $dsn = 'mysql:host=' . $this->s->db_host . ';dbname=' . $this->s->db_database . ';charset=utf8mb4';

    $options = [
      PDO::ATTR_EMULATE_PREPARES   => false, // turn off emulation mode for "real" prepared statements
      PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // turn on errors in the form of exceptions
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // make the default fetch be an associative array
    ];
    try {
      $this->pdo = new PDO($dsn, $this->s->db_user, $this->s->db_password, $options);
    } catch (Exception $e) {
      error_log($e->getMessage());
      exit('Unable to connect to the database.'); //something a user can understand
    }
  }

  /**
   * A query wrapper for prepared statements.
   */
  public function prepared_query(string $query, array $replacements)
  {
    $stmt = $this->pdo->prepare($query);
    $stmt->execute($replacements);

    return $stmt;
  }

  /**
   * Executes a raw query
   */
  public function query(string $query)
  {
    $stmt = $this->pdo->query($query);
    return $stmt;
  }


  public function get_link()
  {
    return $this->pdo;
  }

  use \doorkeeper\lib\class_traits\no_set;

}
