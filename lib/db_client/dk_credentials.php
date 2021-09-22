<?php
/**
 *           Doorkeeper Credentials Object
 *
 *              Class for database credentials
 *
 *
 *         @author Jacob (JacobSeated)
 */

namespace doorkeeper\lib\db_client;

class dk_credentials
{
    public $db_host;
    public $db_user;
    public $db_password;
    public $db_database;

    public function __construct(string $db_host, string $db_user, string $db_password, string $db_database)
    {
        $this->db_host = $db_host;
        $this->db_user = $db_user;
        $this->db_password = $db_password;
        $this->db_database = $db_database;
    }

    public function __debugInfo()
    {
        $this->db_password = 'HIDDEN';

        $properties = get_object_vars($this);

        return $properties;
    }
}