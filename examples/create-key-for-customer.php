<?php

/**
 * This example shows how to create a key for a new customer and then later activate the key on a machine
 * To create a example customer and a automated link to the key the customer bought, simply boot up a PHP webserver (e.g. `php -S 0.0.0.0`) and access via browser with "?exampleCustomer"
 * 
 * You atleast require to use the base cryptolens, customer and key class.
 */

require_once "../loader.php";

use Cryptolens_PHP_Client\Cryptolens;
use Cryptolens_PHP_Client\Customer;
use Cryptolens_PHP_Client\Key;

$c = new Cryptolens("YOUR_TOKEN", 12345, Cryptolens::CRYPTOLENS_OUTPUT_PHP);
$cc = new Customer($c);
$k = new Key($c);

/**  
* assuming you're recieving the request to add a new customer
* the $data array should contain the following keys: name, email, company_name, additional_flags
* For the additional_flags, please see the function documentation for Cryptolens_PHP_Client\Customer::add_customer() */
function add_customer($data, $cc){
    $customerIsAdded = $cc->add_customer($data["name"], $data["email"], $data["company_name"], $data["additional_flags"]);

    if($customerIsAdded != false){
        return $customerIsAdded["customerId"];
    } else {
        die($customerIsAdded);
    }
}

function link_key($customer_id, $k){
    $key = $k->create_key(array("CustomerId" => $customer_id, "MachineId" => $k->getMachineId()));
    if($key != false){
        return $key;
    } else {
        die($key);
    }
}

if($_GET["action"] == "addCustomer"){
    if(isset($_GET["name"], $_GET["email"], $_GET["company_name"])){
        $data = [
            "name" => $_GET["name"],
            "email" => $_GET["email"],
            "company_name" => $_GET["company_name"],
            "additional_flags" => unserialize($_GET["additional_flags"])
        ];
        $customer = add_customer($data, $cc);
        if($customer != false){
            $key = link_key($customer, $k);
            if($key != false){
                echo "Key " . $key["key"] . " has been linked to customer ID " . $customer . " and machine ID" . $k->getMachineId();
            } else {
                echo "An error occurred\n";
                print_r($key);
            }
        } else {
            echo "An error occurred\n";
            print_r($customer);
        }
    }
}

if(isset($_GET["exampleCustomer"])){
    $data = [
        "action" => "addCustomer",
        "name" => "John Doe",
        "email" => "john.doe@acme.inc",
        "company_name" => "Acme Inc.",
        "additional_flags" => serialize(array("AllowActivationManagement" => true))
    ];
    $data = http_build_query($data);
    header("Location: ?{$data}");
}

?>