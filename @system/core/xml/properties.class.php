<?php
namespace Xml;
class Properties extends \SimpleXMLElement
{
    public function __toString() {
        return $this->asXML();
    }
}
