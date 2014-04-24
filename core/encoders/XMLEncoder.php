<?php
/**
 * @copyright 2014 Mark LeBlanc
 * @author  Mark LeBlanc <mark@compute.systems>
 */
namespace framework\core\encoders;

use framework\core\encoders\Encoder;

/**
 *
 */
class XMLEncoder extends Encoder {

	public function __construct() {
		parent::__construct();
	}

	public function encode($entity, $payload) {
		$xml = new SimpleXMLElement('<?xml version="1.0"?><response/>');
		$this->arrayToXml($payload, $xml);
		return $xml->asXML();
	}

	private function arrayToXml($data, &$xml) {
	    foreach($data as $key => $value) {
	        if(is_array($value)) {
	            if(!is_numeric($key)){
	                $subnode = $xml->addChild("$key");
	                $this->arrayToXml($value, $subnode);
	            }
	            else{
	                $this->arrayToXml($value, $xml);
	            }
	        }
	        else {
	            $xml->addChild("$key","$value");
	        }
	    }
	}

}