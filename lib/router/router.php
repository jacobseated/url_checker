<?php

/**
 *  /_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_
 *  /_-_-_-_-_-_-_-_-_-_-_-_-_-_-2021_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_
 *  /_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_
 * 
 *  @author JacobSeated
 */


namespace new_dk\router;

use Exception;

/**
 * The router takes the requested path and checks whether it has a matching route
 * @package new_dk\router
 */
class router
{

    private \doorkeeper\lib\db_client\mysqli_client $db;
    private \new_dk\http_client\dk_http_client $http;
    private \new_dk\dev\dev_log $dev_log;
    private \new_dk\http_client\http_response $res;

    private \new_dk\router\types\route $matched_route;

    private $base_path;
    private $requested_path;

    public function __construct($base_path, $routes, \new_dk\http_client\dk_http_client $http, \new_dk\dev\dev_log $dev_log, \doorkeeper\lib\db_client\mysqli_client $db_client, object $global_container)
    {

        // Dependencies
        $this->http = $http;
        $this->dev_log = $dev_log;
        $this->db = $db_client;
        $this->gc = $global_container;

        // Variables
        $this->base_path = $base_path;
        $parsed_url = parse_url($_SERVER['REQUEST_URI']);
        $this->requested_path = $parsed_url['path'];

        // Check if the requested path matches one of our routes
        foreach ($routes as $route) {
            if (null !== $route->pattern()) {
                if (preg_match($route->pattern(), $this->requested_path, $matches)) {
                    $this->select_route($route);
                }
            } else if (null !== $route->string()) {
                if ($route->string() === $this->requested_path) {
                    $this->select_route($route);
                }
            } else {
                throw new Exception("Missing required parameter (string or pattern) in route.");
            }
        }
        // If no route was selected, show a generic 404
        respond(404, 'Resource not found!');
    }
    private function select_route(\new_dk\router\types\route $route)
    {

        // Verrify that used parameters are allowed by requested resource
        // Note.. A POST request can also contain GET parameters
        //        since they are included in the URL
        //        We therefor verrify both parameter types.
        if (null !== $route->get_parms()) {
            $this->handle_parameters($route->get_parms, $_GET);
        }
        if (null !== $route->post_parms()) {
            $this->handle_parameters($route->post_parms, $_POST);
        }

        // Check that the request method is supported
        if (!in_array($_SERVER['REQUEST_METHOD'], $route->methods())) {
            respond(
                405,
                '<h1>405 Method Not Allowed</h1>',
                ['allow' => implode(', ', $route->methods())]
            );
        }

        // Attempt to call the route handler
        $class = $route->class_handler();
        $function_name = $route->function_handler();

        if (null !== $class) {
            $this->class_route_handler($class);
        } elseif (null !== $function_name) {
            $this->function_route_handler($function_name);
        } else {
            throw new Exception("A route handler was not selected.");
        }
    }
    /**
     * Handles a route by instantiating a class from _app/
     * @param string $class 
     * @return void 
     */
    private function class_route_handler(string $class)
    {
        // If everything was ok, try to handle the request
        // Replace "-" with "_" since class names uses "_"
        $app_name = str_replace('-', '_', $class);

        // Return the relevant application object
        $classToLoad = '\\new_dk\\_app\\' . str_replace('/', '\\', $app_name);

        $class_file_path = $this->base_path . 'lib/_app/' . $app_name . '.php';

        if (!file_exists($class_file_path)) {
            throw new Exception("The app class was not found. Please check your installation paths.");
        } else {
            require_once $class_file_path;
        }

        $this->app = new $classToLoad($this->gc);

        // In case developer forgets to call exit from their app
        exit();
    }
    /**
     * Handles a route by calling a specific function
     * @param string $function_name 
     * @return never 
     */
    private function function_route_handler(string $function_name)
    {
        // Make sure the route handler is callable
        if (!is_callable($function_name)) {
            $content = '<h1>500 Internal Server Error</h1>';
            $content .= '<p>Specified route-handler does not exist.</p>';
            $content .= '<pre>' . htmlspecialchars($function_name) . '</pre>';
            respond(500, $content);
        }

        // If we got any RegEx matches
        if (isset($matches[1])) {
            call_user_func($function_name, $matches);
            exit();
        } else {
            call_user_func($function_name);
            exit();
        }
    }

    public function feature_create()
    {

    }

    public function handle_parameters($allowed_parameters, $post_or_get_parameters)
    {

        $invalid_parameters = [];
        foreach ($post_or_get_parameters as $parm_name => $parm_value) {
            if (!in_array($parm_name, $allowed_parameters)) {
                $invalid_parameters[] = $parm_name;
            }
        }


        if ($invalid_parameters !== []) {
            echo '<p><b>Invalid request:</b> parameters not allowed.</p>';
            echo '<pre>';
            foreach ($invalid_parameters as $invalid_key => $invalid_name) {
                echo $invalid_key . ': ' . $invalid_name . "\n";
            }
            echo '</pre>';
            exit();
        }

        return true;
    }
}
