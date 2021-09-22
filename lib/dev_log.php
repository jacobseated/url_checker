<?php

namespace new_dk\dev;

use Exception;

class dev_log
{

    public function __construct()
    {
        if (true === file_exists('dev-log.txt')) {
            unlink('dev-log.txt');
        }
    }

    /**
     * Write data to the developers log
     * 
     * @param mixed $data 
     * @return void 
     */
    public function write($data, string $entry_name = '')
    {
        if ((is_string($data)) || (is_int($data)) || (is_float($data))) {
            if (false === file_put_contents('/var/www/test-beamtic/dev-log.txt', $this->entry_format($data, $entry_name), FILE_APPEND)) {
                throw new Exception("Unable to save log");
            }
        } elseif ((is_array($data)) || (is_object($data))) {
            if (false === file_put_contents('/var/www/test-beamtic/dev-log.txt', $this->entry_format(print_r($data, true), $entry_name), FILE_APPEND)) {
                throw new Exception("Unable to save log");
            }
        } elseif (is_resource($data)) {
            if (false === file_put_contents('/var/www/test-beamtic/dev-log.txt', $this->entry_format('Resource: ' . $data, $entry_name), FILE_APPEND)) {
                throw new Exception("Unable to save log");
            }
        } else {
            throw new Exception("Unsupported data type...");
        }
    }

    private function entry_format($data, string $entry_name)
    {
        return "--------ENTRY START----\n\n  " . $entry_name . "    \n" . $data . "\n\n--------ENTRY END----\n\n";
    }
}
