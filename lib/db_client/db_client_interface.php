<?php

/**
 *         Doorkeeper
 *
 *           Database client interface
 * 
 *         @author Jacob (JacobSeated)
 */

namespace doorkeeper\lib\db_client;

interface db_client_interface
{

    public function __construct(dk_credentials $credentials);
    /**
     * Creates a new database connection using the supplied credentials
     */
    public function connect();
    /**
     * Prepares a query and then executes it. First parameter is the query string, second is an array of replacements for the query.
     * Use $return_insert_id to return last inserted ID, use $debug to dump the response to the browser.
     */
    public function prepared_query(string $query, array $replacements_to_prepare, bool $return_insert_id = false, bool $debug = false);
    /**
     * Executes a raw SQL query. Remember to escape input manually!
     */
    public function query(string $query);
    /**
     *  Method to return the current database link
     *  @return object
     *  @throws Exception on failure.
     */
    public function get_link();

    /**
     * Checks if a query result produced a duplicate key error.
     */
    public function is_duplicate_key($stmt_result);
}
