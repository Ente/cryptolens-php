<?php
namespace Cryptolens_PHP_Client {
    class Endpoints {
        public static array $endpoints = [
            "activate" => "https://api.cryptolens.io/api/key/activate",
            "deactivate" => "https://api.cryptolens.io/api/key/deactivate",
            "createKey" => "https://api.cryptolens.io/api/key/createkey",
            "createTrialKey" => "https://api.cryptolens.io/api/key/createtrialkey",
            "createKeyFromTemplate" => "https://api.cryptolens.io/api/key/createkeyfromtemplate",
            "getKey" => "https://api.cryptolens.io/api/key/getkey",
            "addFeature" => "https://api.cryptolens.io/api/key/addfeature",
            "blockKey" => "https://api.cryptolens.io/api/key/blockkey",
            "extendLicense" => "https://api.cryptolens.io/api/key/extendlicense",
            "removeFeature" => "https://api.cryptolens.io/api/key/removefeature",
            "unblockKey" => "https://api.cryptolens.io/api/key/unblockkey",
            "machineLockLimit" => "https://api.cryptolens.io/api/key/machinelocklimit"
        ];

        public static array $no_response_check = [
            "createKey"
        ];

        public static function get_endpoint($function_name){
            if(array_search($function_name, array_flip(self::$endpoints))){
                return self::$endpoints[$function_name];
            }
        }
    }
}


?>