<?php
class XmlArray 
{
    static public function toArray($Xml) {
	$parser = xml_parser_create();
	xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
	xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
	xml_parse_into_struct($parser, $Xml, $tags);
	xml_parser_free($parser);

	$stack = [];
	$elements = [];
	
	foreach ($tags as $tag) {
	    if ($tag['type'] == "complete" || $tag['type'] == "open") {
		$elements[$tag['tag']] = isset($tag['value']) ? $tag['value'] : '';
		if ($tag['type'] == "open") {
		    $elements[$tag['tag']] = [];
		    $stack[count($stack)] =& $elements;
		    $elements =& $elements[$tag['tag']];
		}
		
	    }
	    elseif ($tag['type'] == "close") {  // pop
		$elements = &$stack[count($stack) - 1];
		unset($stack[count($stack) - 1]);
	    }
	}
	return $elements;  // the single top-level element
    }
    
    static private function toNode($Name,$Value,$Nested=0,$EOL=PHP_EOL,$Indent="\t") {
	if(!is_array($Value)) {
	    return (!empty($Indent) ? str_repeat($Indent, $Nested) : '') . '<'.$Name.'><![CDATA['.$Value.']]></'.$Name.'>'.$EOL;
	}
	else {
	    if(is_int(array_keys($Value)[0])) {
		foreach ($Value as $v) {
		    $Xml.=$this->toNode($Name, $v, $Nested, $EOL, $Indent);
		}
	    }
	    else {
		$Xml = (!empty($Indent) ? str_repeat($Indent, $Nested) : '') . '<'.$Name.'>'.$EOL;
		foreach ($Value as $k=>$v) {
		    $Xml.=self::toNode($k, $v, $Nested+1, $EOL, $Indent);
		}
		$Xml.=(!empty($Indent) ? str_repeat($Indent, $Nested) : '') . '</'.$Name.'>'.$EOL;
	    }
	    return $Xml;
	}
    }


    static public function toXml($Array,$EOL=PHP_EOL,$Indent="\t") {
	$Xml = '';
	foreach ($Array as $k=>$v) {
	    $Xml.=self::toNode($k, $v,0,$EOL,$Indent);
	}
	return $Xml;
    }
}
