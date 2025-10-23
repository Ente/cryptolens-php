<?php
namespace Cryptolens_PHP_Client {
    /**
     * Analytics
     * 
     * Allows the use of all Analytics endpoints
     * 
     * @author Bryan Böhnke-Avan <bryan@openducks.org>
     * @license MIT
     * @since v0.9
     * @link https://app.cryptolens.io/docs/api/v3/AI
     */
    class Analytics {
        private Cryptolens $cryptolens;

        private string $group;

        public function __construct(Cryptolens $cryptolens){
            $this->cryptolens = $cryptolens;
            $this->group = Cryptolens::CRYPTOLENS_ANALYTICS;
        }
        /**
         * `register_event()` - Adds a event
         * 
         * This function adds a event which has occurred in the client application to trigger certain interaction in third-party integrations or else.
         * According to the API documentation events are hold for 30 days and might get deleted after.
         * You can provide either the license key or the key ID for the `$key` variable
         * 
         * @link https://app.cryptolens.io/docs/api/v3/RegisterEvent
         *
         * @param string $key - Either the license key or the key ID, optional
         * @param string $machineid - Machine ID to parse, optional "but very useful to help us to destinguish the users" (Cryptolens Description for this parameter)
         * @param string $featurename - The name of the feature, e.g. "VideoRecorder" or "F1", max. 100 chars, optional
         * @param string $eventname - The name of the event, e.g. "click" or "start". Can be any string of max. 100 chars, optional
         * @param string $value - The value of the event, decimals allowed, optional
         * @param string $currency - Currency if the event is an transaction, e.g. "USD"
         * @param string $metadata - A JSON string with any value. The API states to not parse personal identifiable information unless needed, more information here: https://help.cryptolens.io/legal/DataPolicy
         * @return bool Returns true on success and false on failure
         */
        public function register_event(string $key, string $machineid, string $featurename, string $eventname, string $value, string $currency, string $metadata){
            $additional_flags = array(
                "Key" => $key,
                "MachineCode" => $machineid,
                "FeatureName" => $featurename,
                "EventName" => $eventname,
                "Value" => $value,
                "Currency" => $currency,
                "Metadata" => $metadata
            );
            $parms = Helper::build_params($this->cryptolens->getToken(), $this->cryptolens->getProductId(), $key, $machineid, $additional_flags);
            $c = Helper::connection($parms, "registerEvent", $this->group);
            if($c == true){
                if(Helper::check_rm($c)){
                    return Cryptolens::outputHelper($c);
                } else {
                    return Cryptolens::outputHelper($c, 1);
                }
            } else {
                return false;
            }
        }

        /**
         * `register_events()` - Adds multiple events
         * 
         * This function adds multiple events which has occurred in the client application to trigger certain interaction in third-party integrations or else.
         * According to the API documentation events are hold for 30 days and might get deleted after.
         * You can provide either the license key or the key ID for the `$key` variable
         * 
         * @link https://app.cryptolens.io/docs/api/v3/RegisterEvent
         *
         * @param string $key - Either the license key or the key ID, optional
         * @param string $machineid - Machine ID to parse, optional "but very useful to help us to destinguish the users" (Cryptolens Description for this parameter)
         * @param array $events an array containing fields FeatureName, EventName, Currency, Time (unix timestamp) and metadata. If no time is specified in any of the objects, the current time will be used instead.
         * @return bool Returns true on success and false on failure
         */
        public function register_events(string $key = null, string $machineid = null, array $events){
            $additional_flags = array(
                "Key" => $key,
                "MachineCode" => $machineid,
                "Events" => $events
            );
            $parms = Helper::build_params($this->cryptolens->getToken(), $this->cryptolens->getProductId(), $key, $machineid, $additional_flags);
            $c = Helper::connection($parms, "registerEvents", $this->group);
            if($c == true){
                if(Helper::check_rm($c)){
                    return Cryptolens::outputHelper($c);
                } else {
                    return Cryptolens::outputHelper($c, 1);
                }
            } else {
                return false;
            }
        }

        /**
         * `get_events()` - Retrieves events registered using the "registerEvent"-API method
         * 
         * You may only be returned events no older than 30 days.
         *
         * @param integer $limit  	Specifies how many events should be returned.
         * @param integer|null $startingafter Works as a cursor (for pagination). If the last element had the id=125, then setting this to 125 will return all events coming after 125
         * @param integer|null $endingbefore Works as a cursor (for pagination). If the last element had the id=125, then setting this to 125 will return all events coming before 125. 
         * @param array|integer $time Can be either an array or the unix timestamp. Please read more about this in the documentation.
         * @param integer|null $productid Filter the product
         * @param $key The key to filter on (Requires set productId)
         * @param string $metadata Some sort of metadata
         * @return array|bool Either returns false on failure or the Events within an array which can be accessed via the "Events" key
         */
        public function get_events(int $limit = 10, int $startingafter = null, int $endingbefore = null, $time = null, int $product_id = null, string $key = null, $metadata = null){
            if(isset($key) && !isset($product_id)){
                return false;
            }
            $parms = Helper::build_params($this->cryptolens->getToken(), $product_id ?? $this->cryptolens->getProductId(), $key, null, array("Limit" => $limit, "StartingAfter" => $startingafter, "EndingBefore" => $endingbefore, "Time" => $time, "Metadata" => $metadata));
            $c = Helper::connection($parms, "getEvents", $this->group);
            if($c == true){
                if(Helper::check_rm($c)){
                    return Cryptolens::outputHelper($c);
                } else {
                    return Cryptolens::outputHelper($c, 1);
                }
            } else {
                return false;
            }
        }

        /**
         * `get_object_log()` - Retrieves a list of object logs 
         * 
         * @link https://app.cryptolens.io/docs/api/v3/GetObjectLog
         *
         * @param integer $limit Specifies how many events should be returned (default: 10, maximum: 100)
         * @param integer|null $startingafter For pagination. If the last element had the id=125, then setting this to 125 will return all events coming after 125. 
         * @return array|bool Either returns an array containing the error message or the object logs (in the "Events" key)
         */
        public function get_object_log(int $limit = 10, int $startingafter = null){
            if($limit > 100){
                return false;
            }
            $parms = Helper::build_params($this->cryptolens->getToken(), $this->cryptolens->getProductId(), null, null, array("Limit" => $limit, "StartingAfter" => $startingafter));
            $c = Helper::connection($parms, "getObjectLog", $this->group);
            if($c == true){
                if(Helper::check_rm($c)){
                    return Cryptolens::outputHelper($c);
                } else {
                    return Cryptolens::outputHelper($c, 1);
                }
            } else {
                return false;
            }
        }

        /**
         * `get_web_api_log()` - Retrieves a list of Web API logs 
         * 
         * @link https://app.cryptolens.io/docs/api/v3/GetWebAPILog
         *
         * @param integer|null $product_id Filter the product id
         * @param string|null $key Filter for the key
         * @param integer $limit Specify how many events should be returned (default: 10, maximum: 1000)
         * @param integer|null $startingafter For pagination.
         * @param integer|null $endingbefore For pagination.
         * @param boolean $anomalyClassification If enabled, the result contains informaton about the events returned and any of them could be anomalous
         * @return array|bool Either returns false or an error array on failure or on success returning an array containing the events, stored in the "Events" key.
         */
        public function get_web_api_log(int $product_id = null, string $key = null, $limit = 10, int $startingafter = null, int $endingbefore = null, bool $anomalyClassification = false){
            if($limit > 1000){
                return false;
            }

            if(isset($key) && !isset($product_id)){
                return false;
            }

            $parms = Helper::build_params($this->cryptolens->getToken(), $this->cryptolens->getProductId(), null, null, array("ProductId" => $product_id, "Key" => $key, "Limit" => $limit, "StartingAfter" => $startingafter, "EndingBefore" => $endingbefore, "AnomalyClassification" => $anomalyClassification));
            $c = Helper::connection($parms, "getWebAPILog", $this->group);
            if($c == true){
                if(Helper::check_rm($c)){
                    return Cryptolens::outputHelper($c);
                } else {
                    return Cryptolens::outputHelper($c, 1);
                }
            } else {
                return false;
            }
        }
    }
}
