<?php
namespace Html;

abstract class Widget {
    private $widgetArgs;
    public function __construct($Args) {$this->widgetArgs = $Args;}
    abstract public function Begin();
    abstract public function End($InnerHTML);
    abstract public function Complete();
    protected function arg($name,$default='',$ArgList=false) { 
	return $ArgList == false ? (isset($this->widgetArgs[$name]) ? $this->widgetArgs[$name] : $default) :
	    (isset($ArgList[$name]) ? $ArgList[$name] : $default); 
    }
    
    protected function args($Excluded=[],$ArgsList=false) {
	$ArgsList = $ArgsList!=false ? $ArgsList : $this->widgetArgs;
	$Attrs = [];
	if(!empty($Excluded)) {
	    foreach ($ArgsList as $k=>$v) { if(in_array($k, $Excluded)) continue;
		$Attrs[$k] = $v;
	    }
	}else {
	    $Attrs = $ArgsList;
	}
	return !empty($Attrs) ? $Attrs : [];
    }
    
    protected function attrs($Excluded=[],$ArgsList=false) {
	$ArgsList = $ArgsList!=false ? $ArgsList : $this->widgetArgs;
	$Attrs = [];
	if(!empty($Excluded)) {
	    foreach ($ArgsList as $k=>$v) { if(in_array($k, $Excluded)) continue;
		$Attrs[] = $k.'="'. str_replace('"','\"',$v).'"';
	    }
	}else {
	    foreach ($ArgsList as $k=>$v) { 
		$Attrs[] = $k.'="'. str_replace('"','\"',$v).'"';
	    }
	}
    }
}

class Template 
{
    const blockStart = 1;
    const blockEnd = 2;
    const blockComplete = 0;
    /**
     * @ignore
     * @var string
     */
    protected $templateFileName;
    protected $templateVariables;
    private $templateObjectsStack=array();

    /**
     * Создание объекта шаблона
     * @param string $templateFileName
     * @param object $templateOwner
     */
    public function  __construct($templateFileName=false,$variables=array()) {
        $this->templateFileName = $templateFileName;
        $this->templateVariables = $variables;
    }

    public function  &__get($name) { return $this->templateVariables[$name]; }
    public function  __set($name, $value) { $this->templateVariables[$name] = $value; return $this;}
    public function  __isset($name) { return isset($this->templateVariables[$name]); }
    public function  __unset($name) { unset($this->templateVariables[$name]); }
    
    public function Fetch($templateFileName=false,$Args=false) {
        $templateFileName = (!$templateFileName?$this->templateFileName:$templateFileName);
	if($this->__compile($templateFileName))
	{
	    !empty($this->templateVariables) && extract($this->templateVariables,EXTR_REFS);
	    !empty($Args) && extract($Args,EXTR_REFS);

	    ob_start();
	    require($templateFileName);
	    return ob_get_clean();
	}
	\Error::Exception('### TEMPLATE ' . ($templateFileName?$this->templateFileName:$templateFileName) . ' # NOTEXIST ###');
    }

    public function Display($templateFileName=false,$Args=false) { print $this->Fetch($templateFileName,$Args); }

    private function __pop($class)
    {
        return array_pop($this->templateObjectsStack[$class]);
    }
    private function __push($class,$object)
    {
        $this->templateObjectsStack[$class][] = $object;
    }
    private function __current($class)
    {
        return end($this->templateObjectsStack[$class]);
    }

    private function __block($class,$subclass,$args,$CompleteBeginEnd)
    {
        $className = "{$class}{$subclass}";
        (!class_exists($className,false) && file_exists($file_name = __DIR__."/$class/$subclass.$class.php")) && include_once ($file_name); 

        if(class_exists($className)) {

            if ($CompleteBeginEnd == 0) {
                $object = new $className($args);
                method_exists($object, "Complete") && call_user_func (array($object,  "Complete"));
            }
            elseif ($CompleteBeginEnd == 1) {
                $object = new $className($args);
                $this->__push($subclass, $object);
                ob_start();
                method_exists($object, "Begin") && call_user_func (array($object, "Begin"));
            } elseif ($CompleteBeginEnd == 2) {
                $object = $this->__pop($subclass);
                method_exists($object, "End") && call_user_func(array($object, "End"), ob_get_clean());
            }
            return;
        }
        if($this->__current($class)) {
            $object = $this->__current($class);
            $subclass = str_replace(':', '_', $subclass);
            if ($CompleteBeginEnd == 0) {
                method_exists($object, "{$subclass}Complete") && call_user_func (array($object, "{$subclass}Complete"), $args);
            }
            elseif ($CompleteBeginEnd == 1) {
                ob_start();
                $this->__push("{$class}{$subclass}", $args);
                method_exists($object, "{$subclass}Begin") && call_user_func (array($object, "{$subclass}Begin"), $args);
            } elseif ($CompleteBeginEnd == 2) {
                $args = $this->__pop("{$class}{$subclass}");
                method_exists($object, "{$subclass}End") && call_user_func_array(array($object, "{$subclass}End"), array($args,ob_get_clean()));
            }
        }
    }
    private function __compile(&$sourceFileName) 
    {
        if(file_exists(( $destFileName = \Application::Path(\Application::VirtualPath('.compiles/'.str_replace(array('/','\\',':','-'), '_', $sourceFileName))) )) &&
                @filemtime(\Application::Path(\Application::VirtualPath('modules/'.$sourceFileName))) < filemtime($destFileName) )
        {
            $sourceFileName = $destFileName;
            return true;
        }
        
        \Application::MkDir(\Application::VirtualPath('.compiles/'));

        $phpSource = file_get_contents(\Application::Path(\Application::VirtualPath('modules/'.$sourceFileName)));

        $sourceFileName = $destFileName;

        file_put_contents($sourceFileName, preg_replace_callback('%<(/?)(\w+):([\w:]+)(?:\s+(.*?))?\s*(/?)(?!>[^<]+>)>%s', function($match) {
            if(isset($match[4]))
                $match[4] = preg_replace(array('/([\w-]+)\s*=\s*("|\')(.*?)\2\s*/s','/([\w-]+)\s*=\s*((?:\w+::)?\$[^\s\/]+)\s*/s'), array('\'\1\'=>\2\3\2,','\'\1\'=>\2,'), $match[4]);
            else
                $match[4] = '';
            $match[5] = (isset($match[5]) && $match[5]=='/')?0:($match[1]=='/'?2:1);
            return "<?php \$this->__block('$match[2]','$match[3]',array($match[4]),$match[5]); ?>";
        }, $phpSource));
        return true;
    }

    public function  __toString() { return $this->Fetch(); }
}
