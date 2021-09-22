<?php
/**
 *  /_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_
 *  /_-_-_-_-_-_-_-_-_-_-_-_-_-_-2021_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_
 *  /_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_
 * 
 *  @author JacobSeated
 */


namespace new_dk\_app;

use \doorkeeper\lib\db_client\mysqli_client;
use \new_dk\http_client\dk_http_client;
use \new_dk\dev\dev_log;
use \new_dk\http_client\http_response;

/**
 * All app classes should be extended from this class and placed in subdirectories. I.e.: _app/[app_name]/[app_class_name].php
 * @package new_dk\_app
 */
abstract class abstract_app_base {

    protected mysqli_client $db;
    protected dk_http_client $http;
    protected dev_log $dev_log;
    protected http_response $res;

    private \new_dk\router\types\route $matched_route;

    public function __construct(object $gc)
    {
        $this->gc = $gc;
        $this->db = $gc->db();
        $this->http = $gc->http();
        $this->dev_log = $gc->dev_log();
        $this->main();
    }

    abstract public function main();

}