<?php
// This file contains the PHP functions that are used with the module 'Dutch Addresses'
// Please read the `README.md` file for further instructions on configuration and use.

use Ampersand\Log\Logger;
use Ampersand\Rule\ExecEngine;

function concatext($t1="_NULL",$t2="_NULL",$t3="_NULL",$t4="_NULL",$t5="_NULL")
{  $result = "";
   $result = ($t1 == '_NULL') ? $result : $result.$t1." ";
   $result = ($t2 == '_NULL') ? $result : $result.$t2." ";
   $result = ($t3 == '_NULL') ? $result : $result.$t3." ";
   $result = ($t4 == '_NULL') ? $result : $result.$t4." ";
   $result = ($t5 == '_NULL') ? $result : $result.$t5." ";
   return trim($result);
}

function concatlines($t1,$t2="_NULL",$t3="_NULL",$t4="_NULL",$t5="_NULL")
{   $result = $t1;
    $result = ($t2 == '_NULL') ? $result : $result.PHP_EOL.$t2; 
    $result = ($t3 == '_NULL') ? $result : $result.PHP_EOL.$t3;
    $result = ($t4 == '_NULL') ? $result : $result.PHP_EOL.$t4;
    $result = ($t5 == '_NULL') ? $result : $result.PHP_EOL.$t5;
    return $result;
}

// ****************************************************************************************************
// ROLE ExecEngine MAINTAINS "Set Street and City given the ZIPCode"
// RULE "Set Street and City given the ZIPCode": I[DutchAddr];daddrZIPCode |- daddrStreet;V /\ daddrCity;V
// VIOLATION (TXT "{EX} postcodeSetAddrInfo;", SRC I, TXT ";", TGT I) 
// ****************************************************************************************************
ExecEngine::registerFunction('postcodeSetAddrInfo', function ($DutchAddr,$ZIPCode,$huisnummer)
{	if(func_num_args() != 3) throw new Exception("Function postcodeSetAddrInfo() requires 3 arguments, not: ".func_num_args(), 500);
    $this->info("postcodeSetAddrInfo({$DutchAddr},{$ZIPCode},{$huisnummer})");
    $allAddresses = array();
	postcodeAPI_SingleAddress($ZIPCode, $huisnummer, $allAddresses); 
    $this->info(count($allAddresses)." adressen zijn ontvangen.");

//	Nu alle adressen met het betreffende huisnummer opzoeken (dat kunnen er meerdere zijn!)
//	$addresses = array();
//	foreach($allAddresses as $address)
//	{	if ($address['number'] == $huisnummer)
//			$addresses[] = $address;
//	}

	// If the ZIPCode exists, then we only need one of the returned addresses
	if (count($allAddresses) == 0)
	{	ExecEngine::getFunction('InsPair')->call($this, 'daddrPostcodeErr','DutchAddr',$DutchAddr,'DutchAddr',$DutchAddr);
		return;
	}
	
	$address = $allAddresses[0]; // Every address has the street, city, municipality and province

	// We comment out all the fields that make no sense for this function.
//	ExecEngine::getFunction('InsPair')->call($this, 'daddrZIPCode','DutchAddr',$DutchAddr,'ZIPCode',$address['postcode']);
	ExecEngine::getFunction('InsPair')->call($this, 'daddrCity','DutchAddr',$DutchAddr,'City',$address['city']['label']);         

//	ExecEngine::getFunction('InsPair')->call($this, 'daddrID','DutchAddr',$DutchAddr,'DutchAddrID',$address['id']) -- ID van het kadaster
	ExecEngine::getFunction('InsPair')->call($this, 'daddrStreet','DutchAddr',$DutchAddr,'Street',$address['street']);                
//	ExecEngine::getFunction('InsPair')->call($this, 'daddrStreetNr','DutchAddr',$DutchAddr,'StreetNr',$address['number']);                
//	ExecEngine::getFunction('InsPair')->call($this, 'daddrStrNrAddition','DutchAddr',$DutchAddr,'StrNrAddition',$address['addition']);              
//	ExecEngine::getFunction('InsPair')->call($this, 'daddrStrNrLetter','DutchAddr',$DutchAddr,'StrNrLetter',$address['letter']);                
	ExecEngine::getFunction('InsPair')->call($this, 'daddrMunicipality','DutchAddr',$DutchAddr,'Municipality',$address['municipality']['label']); 
	ExecEngine::getFunction('InsPair')->call($this, 'daddrProvince','DutchAddr',$DutchAddr,'Province',$address['province']['label']);
	
	return; 
});

function postcodeAPI_SingleAddress($postcode, $huisnummer, &$allAddresses)
{	if(func_num_args() != 3) throw new Exception("Function postcodeAPI_SingleAddress() requires 3 arguments, not: ".func_num_args(), 500);
    Logger::getLogger('EXECENGINE')->info("postcodeAPI_SingleAddress({$postcode},{$huisnummer},allAddresses)");

	// De headers worden altijd meegestuurd als array
	$headers = array();
	
	global $ampersandApp; // TODO: remove dependency on global var
	$apikey = $ampersandApp->getSettings()->get('postcodeAPI.X-Api-Key');
	if (empty($apikey)) throw new Exception("Function postcodeAPI_SingleAddress(): X-Api-Key is not specified in file `project.yaml`", 500); 

	$headers[] = 'X-Api-Key: '.$apikey;

	// De URL naar de API call
	$url = 'https://postcode-api.apiwise.nl/v2/addresses/?postcode='.$postcode.'&number='.$huisnummer;
	
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	
	// Indien de server geen TLS ondersteunt kun je met 
	// onderstaande optie een onveilige verbinding forceren.
	// Meestal is dit probleem te herkennen aan een lege response.
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	
	// The raw JSON response
	$response = curl_exec($curl);
    if ($response === false) throw new Exception("Function postcodeAPI_SingleAddress() has curl error ".curl_error($curl), 500);

	// Use json_decode() to convert the response to a PHP array
	$allAddresses = json_decode($response,true)["_embedded"]["addresses"]; // list of addresses
    Logger::getLogger('EXECENGINE')->info(count($allAddresses)." adresses have been found.");

	curl_close($curl);

	return;
}
