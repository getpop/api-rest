<?php
namespace PoP\RESTAPI;

class Environment
{
    public static function disableRESTAPI()
    {
        return isset($_ENV['DISABLE_REST_API']) ? strtolower($_ENV['DISABLE_REST_API']) == "true" : false;
    }
}
