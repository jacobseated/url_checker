<?php
/*
 *           Doorkeeper Database Client
 *
 *        Class to handle Database Connection and Queries
 * 
 *        2020 - Database class
 *          This is the new database client with support for prepared statements.
 * 
 *          Note. Exceptions should probably be translated in the exception handler...
 *
 *        Author: Jacob (JacobSeated)
 */

namespace doorkeeper\lib\db_client;

use Exception;
use Throwable;

class mysqli_client implements db_client_interface
{
    public \mysqli $db_link;

    private $s; // database credentials

    // Map MySQL errors to localized error codes
    private $known_errors = [1062 => 1];

    // Data types used to prepare data in prepared statements
    private $data_types = ['double' => 'd', 'integer' => 'i', 'string' => 's', 'blob' => 'b'];

    public function __construct(dk_credentials $credentials)
    {
        $this->s = $credentials;
    }

    public function connect()
    {
        // The "\" is needed to make PHP look for mysqli in the "root" namespace
        $this->db_link = @new \mysqli($this->s->db_host, $this->s->db_user, $this->s->db_password);

        // Check that connection was successful
        if ($this->db_link->connect_errno) {
            if (isset($this->db_link->connect_errno)) {
                throw new \Exception('Failed to connect to the database: ', $this->db_link->connect_errno);
            } else {
                throw new \Exception('Failed to connect to the database.');
            }
        }

        // Try to select DB
        $this->db_link->select_db($this->s->db_database);

        // Check if database selection failed
        if ($this->db_link->errno) {
            throw new \Exception('Failed to select database: ', $this->db_link->errno);
        } else {
            // Note. Setting this might cause problems in Windows according to a comment in php.net
            $this->db_link->query("SET NAMES utf8mb4 COLLATE utf8mb4_0900_ai_ci");
            return true;
        }
    }
    public function close_connection()
    {
        mysqli_close($this->db_link);
    }

    public function prepared_query(string $query, array $replacements_to_prepare, bool $return_insert_id = false, bool $debug = false)
    {
        $return_value = true; // Note. Return value is mixed depending on $options and other things

        // Prepare the query
        if (false === ($stmt = $this->db_link->prepare($query))) {
            $error_msg = (false !== isset($this->db_link->error)) ? $this->db_link->error : '';
            throw new Exception("Unable to prepare statement. Response was: " . $error_msg . ' ');
        }

        // Determine type of the replacements
        $types_str = $this->prepare_types($replacements_to_prepare);

        // Bind parameters with types
        // Note. "..." will unpack the array into arguments
        if (false === $stmt->bind_param($types_str, ...$replacements_to_prepare)) {
            throw new Exception("Unable to bind parameters in database query.");
        }
        // Attempt to execute the query, return error code on failure
        $result = $stmt->execute();
        if (true === $debug) {
            header('content-type: text/plain; charset=utf-8');
            var_dump($stmt);
            exit();
        }
        if (false === $result) {
            $return_value = (isset($this->known_errors["{$stmt->errno}"])) ? $stmt->errno : false;
            $stmt->close();
            return $return_value;
        }

        // If this was a select query, return the results and close the statement
        if (stripos($query, 'select') === 0) {
            $return_value = $stmt->get_result();
        } elseif ((stripos($query, 'insert') === 0) && (true === $return_insert_id)) {
            // If this was an insert query and the "insert_id" option was supplied, set the "insert_id" as return value
            $return_value = $stmt->insert_id;
        } elseif (stripos($query, 'delete') === 0) {
            if (0 === $stmt->affected_rows) {
                $return_value = false;
            }
        }

        // For everything else, just close the statement and return true.
        $stmt->close();
        return $return_value;
    }

    /**
     * Method checks if a query returned the duplicated key error; useful after sending an insert query
     * @param mixed $stmt_result 
     * @return bool 
     */
    public function is_duplicate_key($stmt_result): bool
    {
        if (1062 === $stmt_result) {
            return true;
        }
        // The error code was not duplicate key (1062 in MySQL)
        return false;
    }

    public function query(string $query)
    {
        preg_match('/^([a-zA-Z]+) [^"]+/', $query, $matches);
        $query_type = $matches[1];

        $result = $this->db_link->query($query);
        if ($result === false) {
            throw new Exception('Something seems to be wrong with the query.', $this->db_link->errno);
            return false;
        } else {
            // Note. I am not sure the below is the best approach
            // and now we got a lot of code depending on this behaviour.
            // I guess the idea was to avoid having too much database-related logic
            // in other code..
            //
            // To circumvent restrictions emposed by this method,
            // developers may use the "get_dblink()" method and query the database directly.
            if ($query_type == 'SELECT') {
                if ($result->num_rows >= 1) {
                    return $result;
                } else {
                    // If nom_rows on a SELECT is 0, then it simply means
                    // nothing matched the query. This is not an exception!
                    // So, we probably best return false instead of throwing exceptions.
                    return false;
                }
            } else {
                // If the Query was unknown,
                // assume the developer know what he is doing and return true
                // Alternativly, we may throw an exception (consider this carefully!)
                return true;
            }
        }
    }
    public function get_link()
    {
        if (isset($this->db_link)) {
            return $this->db_link;
        }
        throw new Exception('A database connection is not established yet.');
    }
    /**
     * Method to return the types of query replacements.
     */
    private function prepare_types($replacements_to_prepare): string
    {
        // Prepare the replacements
        $replacement_types = '';
        foreach ($replacements_to_prepare as $value) {
            $type = gettype($value);

            if (false === isset($this->data_types["$type"])) {
                throw new Exception("Unknown type used in prepared statement");
            }

            $replacement_types .= $this->data_types["$type"];
        }
        return $replacement_types;
    }

    private function time_today($format = 'friendly')
    {
        $datetime['friendly'] = date("Y-m-d H:i:s");
        $datetime['unix'] = strtotime($datetime['friendly']);
        return $datetime["$format"];
    }

    use \doorkeeper\lib\class_traits\no_set;
}
