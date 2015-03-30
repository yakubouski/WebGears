<?php
/**
 * @package Core
 * @subpackage Debug
 */
final class Console {
    static public function Error($object) {
	if(DEBUG) {
	    echo '<script>console.error('.  json_encode($object,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE).');</script>';
	}
    }
}