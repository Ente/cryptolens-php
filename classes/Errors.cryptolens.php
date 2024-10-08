<?php
namespace Cryptolens_PHP_Client {

    /**
     * Errors class
     * 
     * This class is an implemention of the PHP builtin Exceptions class.
     * It is able to write to a log and logrotate it - logs are located within the libraries folder (\[document_root\]/data/log...).
     */
    class Errors extends \Exception {
        public function __construct($message, $code = 0, \Exception $previous = null){
            parent::__construct($message, $code, $previous);
        }
        public function __toString(): string{
            return __CLASS__ . ": [($this->code}]: {$this->message}\n";
        }

        public static function error_rep($message, $method = NULL){
            $error_file = self::logrotate(); // file on your fs, e.g. /var/www/html/error.log
            $version = @json_decode(file_get_contents("../composer.json"), true)["version"];
            if($method == NULL){
                $method = $_SERVER["REQUEST_METHOD"];
            }
            $time = date("[d.m.Y | H:i:s]");
            error_log("{$time} \"{$message}\"\nURL: {$_SERVER["HTTP_HOST"]}{$_SERVER["REQUEST_URI"]} \nVersion: {$version} Server IP:{$_SERVER["SERVER_ADDR"]} - Server Name: {$_SERVER["SERVER_NAME"]} - Request Method: '{$method}'\nRemote Addresse: {$_SERVER["REMOTE_ADDR"]} - Remote Name: '{$_SERVER["REMOTE_HOST"]}' - Remote Port: {$_SERVER["REMOTE_PORT"]}\nScript Name: '{$_SERVER["SCRIPT_FILENAME"]}'\n=======================\n", 3, $error_file);
        }   

        public static function logrotate(){
            $logpath = $_SERVER["DOCUMENT_ROOT"] . "/data/logs/";
            $date = date("Y-m-d");
            $lastrotate = @file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/data/logs/logrotate-cache.txt");

            if($date != $lastrotate){
                if(!file_exists($logpath . "log-{$date}.log")){
                    $newlog = $logpath . "log-{$date}.log";
                    file_put_contents($newlog, "");
                    file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/data/logs/logrotate-cache.txt", $date);
                }
                return $_SERVER["DOCUMENT_ROOT"] . "/data/logs/log-{$date}.log";
            }
            return $_SERVER["DOCUMENT_ROOT"] . "/data/logs/log-{$date}.log";
        }
    }
}

?>
