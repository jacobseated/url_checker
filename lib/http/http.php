<?php

namespace new_dk\http_client;

class dk_http_client
{
    public function __construct($dev_log)
    {
        $this->dev_log = $dev_log;
    }

    public function get($url, array $request_headers = [])
    {
        $default_req_headers = ['user-agent' => 'PHP'];

        $request_headers_str = $this->build_header_str($request_headers + $default_req_headers);

        $HTTP = array(
            'http' => // The wrapper to be used
            array(
                'ignore_errors' => true,
                'follow_location' => false,
                'method'  => 'GET',
                'header'  => $request_headers_str
            )
        );
        $context = stream_context_create($HTTP);

        if (false === ($response_data = @file_get_contents($url, false, $context))) {
            $error = error_get_last();
            $message = "Request failed. Are you sure this URL is valid?";
            if (!empty($error['message'])) {
                if (preg_match('/^file_get_contents\([^\)]+\):(.*)/', $error['message'], $matches)) {
                    $message = $matches[1];
                }
            }
            throw new \Exception($message);
        }

        return new http_response($response_data, $http_response_header);
    }


    public function post($url, array $parameters = [], array $request_headers = [])
    {

        $default_req_headers = ['Content-type' => 'application/x-www-form-urlencoded', 'user-agent' => 'PHP'];

        $parameters_str = $this->build_post_str($parameters);
        $request_headers_str = $this->build_header_str($request_headers + $default_req_headers);

        // dev_log::write_log($parameters_str, 'parameters_str');

        $HTTP = array(
            'http' => // The wrapper to be used
            array(
                'ignore_errors' => true,
                'follow_location' => false,
                'method'  => 'POST',
                'header'  => $request_headers_str,
                'content' => $parameters_str
            )
        );
        $context = stream_context_create($HTTP);

        if (false === ($response_data = @file_get_contents($url, false, $context))) {
            $error = error_get_last();
            $message = "Request failed. Are you sure this URL is valid?";
            if (!empty($error['message'])) {
                if (preg_match('/^file_get_contents\([^\)]+\):(.*)/', $error['message'], $matches)) {
                    $message = $matches[1];
                }
            }
            throw new \Exception($message);
        }

        return new http_response($response_data, $http_response_header);
    }

    private function build_post_str(array $parameters)
    {

        if (count($parameters) < 1) {
            return '';
        }

        $parm_string = key($parameters) . '=' . current($parameters);
        if (!next($parameters)) {
            return $parm_string;
        }
        foreach ($parameters as $key => $value) {
            $parm_string .= '&' . $key . '=' . $value;
        }
        return $parm_string;
    }

    private function build_header_str(array $parameters)
    {

        if (count($parameters) < 1) {
            return '';
        }

        $parm_string = '';
        foreach ($parameters as $key => $value) {
            $parm_string .= $key . ': ' . $value . "\r\n";
        }
        return $parm_string;
    }
}