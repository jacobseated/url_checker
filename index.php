<?php

/**
 *  /_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_
 *  /_-_-_-_-_-_-_-_-_-_-_-_-_-_-2021_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_
 *  /_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_
 * 
 *  @author JacobSeated
 */


opcache_invalidate(__FILE__, true);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
set_time_limit(0);

// Define BASE_PATH for use with file-handling and includes
define('BASE_PATH', rtrim(preg_replace('#[/\\\\]{1,}#', '/', __DIR__), '/') . '/');

// Include required files
require_once BASE_PATH . '../.credentials/db_credentials.php';
require_once BASE_PATH . 'includes/global_functions.php';
require_once BASE_PATH . 'lib/dev_log.php';
require_once BASE_PATH . 'lib/http/http.php';
require_once BASE_PATH . 'lib/http/http_response.php';
require_once BASE_PATH . 'lib/router/router.php';
require_once BASE_PATH . 'lib/router/types/route.php';
require_once BASE_PATH . 'lib/_app/abstract_app_base.php';
require_once BASE_PATH . 'lib/_containers/abstract_global_container.php';
require_once BASE_PATH . 'lib/_containers/app_container.php';
require_once BASE_PATH . 'lib/class_traits/no_set.php';
require_once BASE_PATH . 'lib/db_client/dk_credentials.php';
require_once BASE_PATH . 'lib/db_client/db_client_interface.php';
require_once BASE_PATH . 'lib/db_client/mysqli_client.php';

use new_dk\dev\dev_log;
use new_dk\http_client\dk_http_client;
use new_dk\router\router;
use new_dk\router\types\route;
use doorkeeper\lib\db_client\dk_credentials;
use doorkeeper\lib\db_client\mysqli_client;

use new_dk\_containers\app_container;

$dev_log = new dev_log();
$http = new dk_http_client($dev_log);
$db_client = new mysqli_client(new dk_credentials('localhost', $db_user, $db_password, 'url_checker'));

$gc = new app_container(BASE_PATH, $db_client, $http, $dev_log); // Global Container

// dev_log::write($response->headers());exit();

$routes = [
    (new route(['GET', 'POST']))->string('/')->class_handler('url_checker/url_checker'),
    (new route(['GET']))->string('/dashboard')->class_handler('url_checker/dashboard')
];

$router = new router(BASE_PATH, $routes, $http, $dev_log, $db_client, $gc);

// If the route was not recognized by the router
respond(404, '<h1>404 Not Found</h1><p>Page not recognized...</p>');
