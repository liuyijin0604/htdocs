<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iain
 * Date: 21/05/13
 * Time: 01:01
 * To change this template use File | Settings | File Templates.
 */

/*
 * Base class for Xero Entity Objects to inherit from
 * @author Iain Gray <igray@itgassociates.com>
 * @copyright Copyright &copy; Iain Gray 2013-
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 * @package yii-xero
 *
 * This uses code from the core yii source
 *
 */
abstract class XeModel extends CModel {


    private static $_names=array();

    /**
     * @var string
     */
    protected  $_resourceType;


    /**
     * Resource ID
     * @var null
     */
    protected  $_id=NULL;
    protected  $xeroID = '';


    /**
     * XML for the class properties
     * @var SimpleXMLElement
     */
    protected $_xml='';


    /**
     * Constructor.
     * @param string $scenario name of the scenario that this model is used in.
     * See {@link CModel::scenario} on how scenario is used by models.
     * @see getScenario
     */
    public function __construct($scenario='')
    {
        $this->setScenario($scenario);
        $this->init();
        $this->attachBehaviors($this->behaviors());
        $this->afterConstruct();
    }

    /**
     * Get a list of collections to be assigned as CTypedLists  attributeName => class
     * @return array
     */
    public function collections()
    {
        return array();
    }


    /**
     * Initializes this model.
     * This method is invoked in the constructor right after {@link scenario} is set.
     * You may override this method to provide code that is needed to initialize the model (e.g. setting
     * initial property values.)
     */
    public function init()
    {
        $this->_resourceType = substr(get_called_class(), 2); //remove the Xe prefix from model class name

        foreach ($this->collections() as $attribute => $class )
        {
            $this->$attribute = new CTypedList($class);
        }
    }

    /**
     * @param $data
     * @param bool $isArray
     */
    public function setData(&$data){

        // list all properties from json object
        $allProps = get_object_vars($data);
        foreach ( $allProps as $propName => $propValue ) {
            if ( is_object( $propValue) ) {
                if ( !property_exists($this,lcfirst($propName))) continue;
                $className = 'Xe'. $propName;
                $this->{lcfirst($propName)} =  $this->mapObject($propValue,$className);
            } elseif ( is_array($propValue) ) {
                if ( !property_exists($this,strtolower($propName))) continue;
                // in case array we should create member name by convert propname to lowercase
                // and related array item should be Xe{PropName} class
                if ( strtolower($propName) === 'addresses' ) {
                    // in case plural for Addresses
                    $className = 'Xe' .  substr($propName,0,strlen($propName) - 2); // remove 'es'
                } else {
                    $className = 'Xe' . substr($propName,0,strlen($propName) - 1); // remove 's'
                }
                $objs = $this->mapObjectByArray($propValue,$className);
                foreach ( $objs as $obj ) {
                    $this->{strtolower($propName)}->add($obj);
                }
            } else {
                if ( !property_exists($this,lcfirst($propName))) continue;
                // because id property existing in CModel , once we got id from Xero
                // we need convert to xeroID
                if ( strtolower($propName) === 'id' ) {
                    $this->xeroID = $propValue;
                } else {
                    $this->{lcfirst($propName)} = $propValue;
                }
            }
        }
    }

    private function mapObject(&$data, $className){
        if ( !class_exists($className,false) ) {
            include_once(Yii::app()->basePath.DIRECTORY_SEPARATOR .'models/'.$className.'.php');
        }
        // still not existing , must be something wrong
        if ( !class_exists($className,false) ) {
            return NULL;
        }

        $obj = new $className;
        $obj->setData($data);
        return $obj;
    }

    private function mapObjectByArray(&$arData,$className){
        if ( !is_array($arData) ) return NULL;
        if ( !class_exists($className,false) ) {
            include_once(Yii::app()->basePath.DIRECTORY_SEPARATOR .'models/'.$className.'.php');
        }
        // still not existing , must be something wrong
        if ( !class_exists($className,false) ) {
            return NULL;
        }

        $objs = array();
        foreach ( $arData as $objData ) {
            $obj = new $className;
            $obj->setData($objData);
            $objs[] = $obj;
        }

        return $objs;
    }
    /**
     * @return array list of attribute names. Defaults to all public properties of the class.
     */
    public function attributeNames()
    {
        $className=get_class($this);
        if(!isset(self::$_names[$className]))
        {
            $class=new ReflectionClass(get_class($this));
            $names=array();
            foreach($class->getProperties() as $property)
            {
                $name=$property->getName();
                if($property->isPublic() && !$property->isStatic())
                    $names[]=$name;
            }
            return self::$_names[$className]=$names;
        }
        else
            return self::$_names[$className];
    }


    /**
     * @return string
     */
    public function getResourceType()
    {
        return $this->_resourceType;
    }

    /**
     * @return SimpleXmlElement
     */
    public function getXml()
    {
        $this->updateXml();
        return $this->_xml;
    }

    public function loadFromXmlObject($simpleXmlObject)
    {
        if(!$simpleXmlObject instanceof SimpleXMLElement)
            throw new CException(Yii::t('yii-xero', 'Parameter 1 must be SimpleXMLElement object'));
        $item =  $this->simpleXmltoArray($simpleXmlObject->{$this->_resourceType}[0]);
        $this->loadPropertiesFromArray($item);
    }


    /**
     * Loads the class properies from the array
     * @param $array
     */
    public function loadPropertiesFromArray($array)
    {

        foreach($array as $name => $value)
        {
            if(is_scalar($value)) //if it's just a scalar, assign it
            {
                $this->{lcfirst($name)} = $value;
            }
            elseif(is_array($value)) //if it is an array, iterate through and add the objects
            {
               //get the name of the first key and add Xe to it, as this is the class name
                $key = array_keys($value)[0];
                $className = "Xe$key";
                if ( class_exists( $className, false ) ) {
                    //iterate through the array, and add as many child node objects as possible
                    if (is_array($value[$key])) {
                        foreach ($value[$key] as $child) {
                            $item = new $className;
                            $item->loadPropertiesFromArray($child);
                            $this->{lcfirst($name)}->add($item);
                        }
                    }
                }
            }
       }


    }

    /**
     * Builds an xml string from the class attributes,
     * calling aggregated class methods where needed
     *
     */
    public function updateXml()
    {
        if ($this->beforeUpdateXml())
        {
            $this->_xml = new SimpleXMLElement("<{$this->_resourceType} />");
            foreach ($this->attributes as $name => $value)
            {
                if ( $value instanceof CList ) // if it's a list of Objects, add each Object's XML
                {
                    if ( $value->count() ) // only add the node if there are child objects
                    {
                        $node = $this->_xml->addChild(ucfirst($name));

                        foreach($value->toArray() as $object)
                        {
                            $this->simpleXmlAppend($node, $object->xml); //add child object
                        }
                    }
                }
                elseif ( is_scalar($value) ) //if it's a leaf node, add it to the tree
                {
                    $element = ucfirst($name); //convert from Yii style to xml style
                    $this->_xml->$element = $value;
                }
                elseif ( $value instanceof XeModel ) //if it's a single model, just add it
                {
                    $this->simpleXmlAppend($this->_xml, $value->xml); //add child object
                }
                elseif ( !is_null($value) && !is_array($value) ) //if it's not null or an empty CList, throw an Exception
                {
                    throw new CException(Yii::t('xero-yii', 'Invalid type {type} for node: {node}', array('{type}'=> gettype($value), '{node}'=>$name)));
                }
            }
            $this->afterUpdateXml();
        }
    }


    /**
     * Converts a simple xml object into an array,
     * by using Yii JSON functions
     *
     * @param $simpleXml SimpleXMLElement
     * @return array
     */
    protected function simpleXmltoArray($simpleXml)
    {
        $json = (CJSON::encode((array)$simpleXml));
        return CJSON::decode($json);
    }


    /**
     * Appends one SimpleXmlElement to another
     * @param SimpleXMLElement $to
     * @param SimpleXMLElement $from
     */
    protected function simpleXmlAppend($to, $from) {
        $toDom = dom_import_simplexml($to);
        $fromDom = dom_import_simplexml($from);
        $toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
    }



    /**
     * This method is invoked before xml is updated.
     * @return boolean whether the event was successful, defaults to true. Update stops if false
     */
    protected function beforeUpdateXml()
    {
        if($this->hasEventHandler('onBeforeUpdateXml'))
        {
            $event=new CModelEvent($this);
            $this->onBeforeUpdateXml($event);
            return $event->isValid;
        }
        else
            return true;
    }

    /**
     * This method is invoked after xml is updated.
     * @return boolean whether the event was successful, defaults to true
     */
    protected function afterUpdateXml()
    {
        if($this->hasEventHandler('onAfterUpdateXml'))
        {
            $event=new CModelEvent($this);
            $this->onAfterUpdateXml($event);
            return $event->isValid;
        }
        else
            return true;
    }



    /**
     * This event is raised before the Xml is updated
     * @param CModelEvent $event the event parameter
     */
    public function onBeforeUpdateXml($event)
    {
        $this->raiseEvent('onBeforeUpdateXml',$event);
    }


   /**
     * This event is raised after the Xml is updated
     * @param CModelEvent $event the event parameter
     */
    public function onAfterUpdateXml($event)
    {
        $this->raiseEvent('onAfterUpdateXml',$event);
    }





}





