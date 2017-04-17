<?php
/*
// Copyright 2009, FedEx Corporation. All rights reserved.
// Version 12.0.0

require_once('library/fedex-common.php5');


//The WSDL is not included with the sample code.
//Please include and reference in $path_to_wsdl variable.
$path_to_wsdl = "wsdl/ShipService_v12.wsdl";

// PDF label files. Change to file-extension .png for creating a PNG label (e.g. shiplabel.png)
define('SHIP_LABEL', 'shiplabel.pdf');  
define('COD_LABEL', 'codlabel.pdf'); 

ini_set("soap.wsdl_cache_enabled", "0");

$client = new SoapClient($path_to_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information

$request['WebAuthenticationDetail'] = array(
	'UserCredential' =>array(
		'Key' => 'xxxxxxxxxxxxxxxxx', 
		'Password' => 'xxxxxxxxxxxxxxxxxxxxxxx'
	)
);
$request['ClientDetail'] = array(
	'AccountNumber' => 'xxxxxxxxx', 
	'MeterNumber' => 'xxxxxxxxx'
);
$request['TransactionDetail'] = array('CustomerTransactionId' => '*** Intra India Shipping Request v12 using PHP ***');
$request['Version'] = array(
	'ServiceId' => 'ship', 
	'Major' => '12', 
	'Intermediate' => '1', 
	'Minor' => '0');
$request['RequestedShipment'] = array(
	'ShipTimestamp' => date('c'),
	'DropoffType' => 'REGULAR_PICKUP', // valid values REGULAR_PICKUP, REQUEST_COURIER, DROP_BOX, BUSINESS_SERVICE_CENTER and STATION
	'ServiceType' => 'STANDARD_OVERNIGHT', // valid values STANDARD_OVERNIGHT, PRIORITY_OVERNIGHT, FEDEX_EXPRESS_SAVER
	'PackagingType' => 'YOUR_PACKAGING', // valid values FEDEX_BOX, FEDEX_PAK, FEDEX_TUBE, YOUR_PACKAGING, ...
	'Shipper' => addShipper(),
	'Recipient' => addRecipient(),
	'ShippingChargesPayment' => addShippingChargesPayment(),
	'SpecialServicesRequested' => addSpecialServices1(), //Used for Intra-India shipping - cannot use with PRIORITY_OVERNIGHT
	'CustomsClearanceDetail' => addCustomClearanceDetail(),                                                                                                      
	'LabelSpecification' => addLabelSpecification(),
	'CustomerSpecifiedDetail' => array('MaskedData'=> 'SHIPPER_ACCOUNT_NUMBER'), 
	'RateRequestTypes' => array('ACCOUNT'), // valid values ACCOUNT and LIST
	'PackageCount' => 1,                                       
	'RequestedPackageLineItems' => array(
		'0' => addPackageLineItem1()
	)
);

try
{
	if(setEndpoint('changeEndpoint'))
	{
		$newLocation = $client->__setLocation(setEndpoint('endpoint'));
	}
	
	$response = $client->processShipment($request); // FedEx web service invocation

    if ($response->HighestSeverity != 'FAILURE' && $response->HighestSeverity != 'ERROR')
    {
        printSuccess($client, $response);

        // Create PNG or PDF labels
        // Set LabelSpecification.ImageType to 'PNG' for generating a PNG labels
        $fp = fopen(SHIP_LABEL, 'wb');   
        fwrite($fp, ($response->CompletedShipmentDetail->CompletedPackageDetails->Label->Parts->Image));
        fclose($fp);
        echo 'Label <a href="./'.SHIP_LABEL.'">'.SHIP_LABEL.'</a> was generated.';           
        
        $fp = fopen(COD_LABEL, 'wb');   
        fwrite($fp, ($response->CompletedShipmentDetail->AssociatedShipments->Label->Parts->Image));
        fclose($fp);
        echo 'Label <a href="./'.COD_LABEL.'">'.COD_LABEL.'</a> was generated.';   
    }
    else
    {
        printError($client, $response);
    }

writeToLog($client);    // Write to log file

} catch (SoapFault $exception) {
    printFault($exception, $client);
}

function addShipper(){
	$shipper = array(
		'Contact' => array(
			'PersonName' => 'Contact Name',
			'CompanyName' => 'Company Name',
			'PhoneNumber' => '1234567890'),
		'Address' => array(
			'StreetLines' => array('Add1 -Not more than 35Char','Bill D&T - Sender'),
			'City' => 'city',
			'StateOrProvinceCode' => 'TN',
			'PostalCode' => '600032',
			'CountryCode' => 'IN',
			'CountryName' => 'INDIA')
	);
	return $shipper;
}
function addRecipient(){
	$recipient = array(
		'Contact' => array(
			'PersonName' => 'Contact Name',
			'CompanyName' => 'Company Name',
			'PhoneNumber' => '1234567890'),
		'Address' => array(
			'StreetLines' => array('Add1 -Not more than 35Char','Add2 -Not more than 35Char'),
			'City' => 'city',
			'StateOrProvinceCode' => 'TN',
			'PostalCode' => '641019',
			'CountryCode' => 'IN',
			'CountryName' => 'INDIA',
			'Residential' => false)
	);
	return $recipient;	                                    
}

function addShippingChargesPayment(){
	$shippingChargesPayment = array(
		'PaymentType' => 'SENDER',
        'Payor' => array(
			'ResponsibleParty' => array(
				'AccountNumber' => 'xxxxxxxxx',
				'Contact' => null,
				'Address' => array('CountryCode' => 'IN'))));
	return $shippingChargesPayment;
}
function addLabelSpecification(){
	$labelSpecification = array(
		'LabelFormatType' => 'COMMON2D', // valid values COMMON2D, LABEL_DATA_ONLY
		'ImageType' => 'PDF',  // valid values DPL, EPL2, PDF, ZPLII and PNG
		'LabelStockType' => 'PAPER_8.5X11_BOTTOM_HALF_LABEL',
		'LabelPrintingOrientation' => 'TOP_EDGE_OF_TEXT_FIRST');
	return $labelSpecification;
}
function addCustomClearanceDetail(){
	$customerClearanceDetail = array(
		'DutiesPayment' => array(
			'PaymentType' => 'SENDER', // valid values RECIPIENT, SENDER and THIRD_PARTY
			'Payor' => array(
				'ResponsibleParty' => array(
					'AccountNumber' => 'xxxxxxxxx',
					'Contact' => null,
					'Address' => array('CountryCode' => 'IN')
				))),
		'DocumentContent' => 'NON_DOCUMENTS',                                                                                            
		'CustomsValue' => array(
			'Currency' => 'INR', 
			'Amount' => 100.0
		),
		'CommercialInvoice' => array(
			'Purpose' => 'SOLD',
			'CustomerReferences' => array(
				'CustomerReferenceType' => 'CUSTOMER_REFERENCE',
				'Value' => '1234'
			)
		),
		'Commodities' => array(
			'NumberOfPieces' => 1,
			'Description' => 'Books',
			'CountryOfManufacture' => 'IN',
			'Weight' => array(
				'Units' => 'KG', 
				'Value' => 1.0
			),
			'Quantity' => 4,
			'QuantityUnits' => 'EA',
			'UnitPrice' => array(
				'Currency' => 'INR', 
				'Amount' => 100.000000
			),
			'CustomsValue' => array(
				'Currency' => 'INR', 
				'Amount' => 400.000000
			)
		),		
	);
	return $customerClearanceDetail;
}

function addPackageLineItem1(){
	$packageLineItem = array(
		'SequenceNumber'=>1,
		'GroupPackageCount'=>1,
		'InsuredValue' => array(
			'Amount' => 0, 
			'Currency' => 'INR'
		),
		'Weight' => array(
			'Value' => 9.0,
			'Units' => 'KG'
		),
		'CustomerReferences' => array(
			'CustomerReferenceType' => 'CUSTOMER_REFERENCE', // valid values CUSTOMER_REFERENCE, INVOICE_NUMBER, P_O_NUMBER and SHIPMENT_INTEGRITY
			'Value' => 'Order No 1'
		)
	);
	return $packageLineItem;
}*/
?>





<?php

// Copyright 2009, FedEx Corporation. All rights reserved.
// Version 12.0.0

require_once('library/fedex-common.php5');


//The WSDL is not included with the sample code.
//Please include and reference in $path_to_wsdl variable.
$path_to_wsdl = "app/code/core/Mage/Usa/etc/wsdl/FedEx/ShipService_v17.wsdl";

// PDF label files. Change to file-extension .png for creating a PNG label (e.g. shiplabel.png)
define('SHIP_LABEL', 'shiplabel.pdf');  
define('COD_LABEL', 'codlabel.pdf'); 

ini_set("soap.wsdl_cache_enabled", "0");

$client = new SoapClient($path_to_wsdl , array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information

$request['WebAuthenticationDetail'] = array(
	'UserCredential' =>array(
		'Key' => 'dx3Rp73SySWRBdyL', 
		'Password' => 'LH3jyyssSJS1OCl1wNuxaoFBV'
	)
);
$request['ClientDetail'] = array(
	'AccountNumber' => '510087445', 
	'MeterNumber' => '100230917'
);
$request['TransactionDetail'] = array('CustomerTransactionId' => '*** Intra India Shipping Request v12 using PHP ***');
$request['Version'] = array(
	'ServiceId' => 'ship', 
	'Major' => '17', 
	'Intermediate' => '0', 
	'Minor' => '0');
$request['RequestedShipment'] = array(
	'ShipTimestamp' => date('c'),
	'DropoffType' => 'REGULAR_PICKUP', // valid values REGULAR_PICKUP, REQUEST_COURIER, DROP_BOX, BUSINESS_SERVICE_CENTER and STATION
	'ServiceType' => 'STANDARD_OVERNIGHT', // valid values STANDARD_OVERNIGHT, PRIORITY_OVERNIGHT, FEDEX_EXPRESS_SAVER
	'PackagingType' => 'YOUR_PACKAGING', // valid values FEDEX_BOX, FEDEX_PAK, FEDEX_TUBE, YOUR_PACKAGING, ...
	'Shipper' => addShipper(),
	'Recipient' => addRecipient(),
	'ShippingChargesPayment' => addShippingChargesPayment(),
	'SpecialServicesRequested' => addSpecialServices1(), //Used for Intra-India shipping - cannot use with PRIORITY_OVERNIGHT
	'CustomsClearanceDetail' => addCustomClearanceDetail(),                                                                                                      
	'LabelSpecification' => addLabelSpecification(),
	'CustomerSpecifiedDetail' => array('MaskedData'=> 'SHIPPER_ACCOUNT_NUMBER'), 
	'RateRequestTypes' => array('ACCOUNT'), // valid values ACCOUNT and LIST
	'PackageCount' => 1,                                       
	'RequestedPackageLineItems' => array(
		'0' => addPackageLineItem1()
	)
);

try
{
	if(setEndpoint('changeEndpoint'))
	{
		$newLocation = $client->__setLocation(setEndpoint('endpoint'));
	}
	
	$response = $client->processShipment($request); // FedEx web service invocation

    if ($response->HighestSeverity != 'FAILURE' && $response->HighestSeverity != 'ERROR')
    {
        printSuccess($client, $response);

        // Create PNG or PDF labels
        // Set LabelSpecification.ImageType to 'PNG' for generating a PNG labels
        $fp = fopen(SHIP_LABEL, 'wb');   
        fwrite($fp, ($response->CompletedShipmentDetail->CompletedPackageDetails->Label->Parts->Image));
        fclose($fp);
        echo 'Label <a href="./'.SHIP_LABEL.'">'.SHIP_LABEL.'</a> was generated.';           
        
        $fp = fopen(COD_LABEL, 'wb');   
        fwrite($fp, ($response->CompletedShipmentDetail->AssociatedShipments->Label->Parts->Image));
        fclose($fp);
        echo 'Label <a href="./'.COD_LABEL.'">'.COD_LABEL.'</a> was generated.';   
    }
    else
    {
        printError($client, $response);
    }

writeToLog($client);    // Write to log file

} catch (SoapFault $exception) {
    printFault($exception, $client);
}

function addShipper(){
	$shipper = array(
		'Contact' => array(
			'PersonName' => 'Contact Name',
			'CompanyName' => 'Company Name',
			'PhoneNumber' => '1234567890'),
		'Address' => array(
			'StreetLines' => array('Add1 -Not more than 35Char','Not more than 35Char'),
			'City' => 'city',
			'StateOrProvinceCode' => 'TN',
			'PostalCode' => '600032',
			'CountryCode' => 'IN',
			'CountryName' => 'INDIA')
	);
	return $shipper;
}
function addRecipient(){
	$recipient = array(
		'Contact' => array(
			'PersonName' => 'Contact Name',
			'CompanyName' => 'Company Name',
			'PhoneNumber' => '1234567890'),
		'Address' => array(
			'StreetLines' => array('Add1 -Not more than 35Char','Add2 -Not more than 35Char'),
			'City' => 'city',
			'StateOrProvinceCode' => 'TN',
			'PostalCode' => '641019',
			'CountryCode' => 'IN',
			'CountryName' => 'INDIA',
			'Residential' => false)
	);
	return $recipient;	                                    
}

function addShippingChargesPayment(){
	$shippingChargesPayment = array(
		'PaymentType' => 'SENDER',
        'Payor' => array(
			'ResponsibleParty' => array(
				'AccountNumber' => '510087445',
				'Contact' => null,
				'Address' => array('CountryCode' => 'IN'))));
	return $shippingChargesPayment;
}
function addLabelSpecification(){
	$labelSpecification = array(
		'LabelFormatType' => 'COMMON2D', // valid values COMMON2D, LABEL_DATA_ONLY
		'ImageType' => 'PDF',  // valid values DPL, EPL2, PDF, ZPLII and PNG
		'LabelStockType' => 'PAPER_8.5X11_BOTTOM_HALF_LABEL',
		'LabelPrintingOrientation' => 'TOP_EDGE_OF_TEXT_FIRST');
	return $labelSpecification;
}
function addSpecialServices1(){
	$specialServices = array(
		'SpecialServiceTypes' => 'COD',
		'CodDetail' => array(
			'CodCollectionAmount' => array('Currency' => 'INR', 'Amount' => 100),
			'CollectionType' => 'GUARANTEED_FUNDS',// ANY, GUARANTEED_FUNDS
			'FinancialInstitutionContactAndAddress' => array(
				'Contact' => array(
					'PersonName' => 'Financial Contact',
					'CompanyName' => 'Financial Company',
					'PhoneNumber' => '8888888888'
				),
				'Address' => array(
					'StreetLines' => '1 FINANCIAL STREET',
					'City' => 'NEWDELHI',
					'StateOrProvinceCode' => 'TN',
					'PostalCode' => '600032',
					'CountryCode' => 'IN',
					'CountryName' => 'INDIA'
				)),
		'RemitToName' => 'Remitter'
	));
	return $specialServices; 
}
function addCustomClearanceDetail(){
	$customerClearanceDetail = array(
		'DutiesPayment' => array(
			'PaymentType' => 'SENDER', // valid values RECIPIENT, SENDER and THIRD_PARTY
			'Payor' => array(
				'ResponsibleParty' => array(
					'AccountNumber' => '510087445',
					'Contact' => null,
					'Address' => array('CountryCode' => 'IN')
				))),
		'DocumentContent' => 'NON_DOCUMENTS',                                                                                            
		'CustomsValue' => array(
			'Currency' => 'INR', 
			'Amount' => 100.0
		),
		'CommercialInvoice' => array(
			'Purpose' => 'SOLD',
			'CustomerReferences' => array(
				'CustomerReferenceType' => 'CUSTOMER_REFERENCE',
				'Value' => '1234'
			)
		),
		'Commodities' => array(
			'NumberOfPieces' => 1,
			'Description' => 'Books',
			'CountryOfManufacture' => 'IN',
			'Weight' => array(
				'Units' => 'KG', 
				'Value' => 1.0
			),
			'Quantity' => 4,
			'QuantityUnits' => 'EA',
			'UnitPrice' => array(
				'Currency' => 'INR', 
				'Amount' => 100.000000
			),
			'CustomsValue' => array(
				'Currency' => 'INR', 
				'Amount' => 400.000000
			)
		),		
	);
	return $customerClearanceDetail;
}

function addPackageLineItem1(){
	$packageLineItem = array(
		'SequenceNumber'=>1,
		'GroupPackageCount'=>1,
		'InsuredValue' => array(
			'Amount' => 0, 
			'Currency' => 'INR'
		),
		'Weight' => array(
			'Value' => 9.0,
			'Units' => 'KG'
		),
		'CustomerReferences' => array(
			'CustomerReferenceType' => 'CUSTOMER_REFERENCE', // valid values CUSTOMER_REFERENCE, INVOICE_NUMBER, P_O_NUMBER and SHIPMENT_INTEGRITY
			'Value' => 'Order No 1'
		)
	);
	return $packageLineItem;
}
?>
