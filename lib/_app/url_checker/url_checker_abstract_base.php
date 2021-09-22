<?php

/**
 *  /_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_
 *  /_-_-_-_-_-_-_-_-_-_-_-_-_-_-2021_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_
 *  /_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_
 * 
 *  @author JacobSeated
 */

namespace new_dk\_app\url_checker;

use new_dk\_app\abstract_app_base;

abstract class url_checker_abstract_base extends abstract_app_base
{

    protected $status_sorter = array();
    protected $tpl = array('title' => 'URL Checker', 'h1' => 'URL Checker', 'content' => '', 'side_box' => '');

    /**
     * Method to try and establish a database connection. If the required database is missing, we try to create it.
     * @return void 
     * @throws Exception 
     */
    protected function database()
    {
        try {
            $this->db->connect();
        } catch (\Throwable $th) {
            if (1049 === $th->getCode()) {

                // Try to create the database if missing
                try {
                    $this->db->query('CREATE DATABASE url_checker DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci');
                } catch (\Throwable $th) {
                    $html = '<p>' . $th->getMessage() . ' Code: ' . $th->getCode() . '</p>';
                    respond(500, $html);
                }

                // Try to select newly created database
                if (!$this->db->db_link->select_db('url_checker')) {
                    $html = '<p>The database was created, but it could not be selected after creating it.</p>';
                    if ($this->db_link->errno) {
                        $html .= '<p>The error code was: ' . $this->db_link->errno . '</p>';
                    }
                    respond(500, $html);
                }

                // Try to create the database table'(s) if missing
                try {
                    $this->db->query('CREATE TABLE urls (url text NOT NULL, status smallint NOT NULL, soft_404 tinyint(1) NOT NULL, md5 varchar(32) NOT NULL, last_checked datetime NOT NULL DEFAULT CURRENT_TIMESTAMP, extra text NOT NULL, UNIQUE KEY md5 (md5)) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;');
                } catch (\Throwable $th) {
                    $html = '<p>' . $th->getMessage() . ' Code: ' . $th->getCode() . '</p>';
                    respond(500, $html);
                }
            }
        }
    }
    protected function status_sorter_list()
    {
        $result = $this->db->query('SELECT status FROM urls LIMIT 5000');
        // If no URLs found
        if (false === $result) {
            return false;
        }
        while ($row = $result->fetch_assoc()) {
            if (!in_array($row['status'], $this->status_sorter)) {
                $this->status_sorter[] = $row['status'];
            }
        }
        // Create a list of buttons to sort by available status codes
        $sort_buttons = '';
        foreach ($this->status_sorter as $status) {
            if ($status == 0) {
                $a_txt = 'Failed';
            } else {
                $a_txt = $status;
            }
            $sort_buttons .= '<li><a href="/dashboard?status=' . $status . '" class="dk_button">' . $a_txt . '</a></li>';
        }
        if (isset($_GET['status'])) {
            $sort_buttons .= '<li><a href="/dashboard" class="dk_button">All</a></li>';
        }
        $this->tpl['side_box'] .= '<div class="dk_border dk_mar dk_pad"><p>Sort by:</b><ol class="dk_flexbox" style="list-style-type:none">' . $sort_buttons . '</ol></div>';
        return true;
    }
}
