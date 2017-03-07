[![API Testing](https://img.shields.io/badge/API%20Test-RapidAPI-blue.svg)](https://rapidapi.com/package/EasyPostTracking/functions?utm_source=EasyPostGithub&utm_medium=button&utm_content=Vender_GitHub)

Installation
------------------

Required PHP Extensions:
- [cURL](http://php.net/manual/en/book.curl.php)
- [JSON](http://php.net/manual/en/book.json.php)

Clone the EasyPost PHP client repository:

    git clone https://github.com/easypost/easypost-php

Include the EasyPost client in your PHP script:

    require_once("/path/to/easypost-php/lib/easypost.php");

Example
----------------

    <?php

    require_once("lib/easypost.php");
    EasyPost::setApiKey("cueqNZUb3ldeWTNX7MU3Mel8UXtaAMUi");
    
    $to_param = array("street1" => "388 Townsend St", "street2" => "Apt 20", "city" => "San Francisco", "state" => "CA", "zip" => "94107");
    $from_param = array("company" => "Simpler Postage Inc", "street1" => "764 Warehouse Ave", "street2" => "", "city" => "Kansas City", "state" => "KS", "zip" => "66101", "phone" => "620-123-4567");
    $parcel_param = array("predefined_package" => "LargeFlatRateBox", "weight" => 100.0); // weight in ounces

    $to_address = EasyPost_Address::create($to_param);
    $from_address = EasyPost_Address::create($from_param);
    $parcel = EasyPost_Parcel::create($parcel_param);

    $shipment = EasyPost_Shipment::create(array(
      "to_address" => $to_address,
      "from_address" => $from_address,
      "parcel" => $parcel,
    ));

    // print_r($shipment->rates);
       
    $shipment->buy($shipment->lowest_rate());

    echo $shipment->postage_label->label_url;

Documentation
--------------------

Up-to-date documentation at: https://www.geteasypost.com/docs

Tests
--------------------
Requires [PHPUnit](https://github.com/sebastianbergmann/phpunit/)

    phpunit tests/
