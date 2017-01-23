<?php
namespace Xml;
class FormXMLElement extends \SimpleXMLElement {
    public function __toString() {
        return $this->asXML();
    }
    
    public function get($name) {
        !isset($this->$name) && $this->addChild($name);
        return $this->$name;
    }
}

class Form
{
    const XmlDocType = '<?xml version="1.0" encoding="UTF-8"?>';
    /**
     * @var FormProperty 
     */
    private $RootNode;
    public function __construct($XML=false) {
        $this->LoadResource($XML, true);
    }
    
    public function LoadResource($XML,$IsXmlString=false) {
        if(empty($XML)) {
            $XML = self::XmlDocType.'<form></form>';
            $IsXmlString = true;
        }
        
        try {
            $this->RootNode = $IsXmlString ? simplexml_load_string($XML,'Xml\FormXMLElement') : simplexml_load_file($XML,'Xml\FormXMLElement');
        } catch (Exception $ex) {
        }
    }
    public function __get($name) {
        return $this->RootNode->get($name);
    }
    
    public function __toString() {
        return empty($this->RootNode) ? self::XmlDocType.'<form></form>' : $this->RootNode->asXML();
    }
}