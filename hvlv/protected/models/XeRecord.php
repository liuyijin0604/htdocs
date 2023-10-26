<?php
/*
 * Base class for Xero Entity Objects to inherit from
 * @author Iain Gray <igray@itgassociates.com>
 * @copyright Copyright &copy; Iain Gray 2013-
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 * @package yii-xero
 * @see CActiveRecord
 *
 * This uses code from the core yii source
 *
 */
abstract class XeRecord extends XeModel {

    /**
     * @var string
     */
    protected  $_endPoint;


    private static $_models=array(); // class name => model


    /**
     * @var string UTC timestamp of last update to record
     */
    public $updatedDateUTC;
    public $id;
    public $status;
    public $providerName;
    public $dateTimeUTC;


    /**
     * Gets the id for the given model.  Override with appropriate Id for each model
     * @return string
     */
    public abstract function getId();


    /**
     * @return string
     */
    public function getEndPoint()
    {
        return $this->_endPoint;
    }



    public function init(){
        parent::init();
        $this->_endPoint = $this->_resourceType.'s';
    }

    public function setEndPoint($endPoint){
        $this->_endPoint = $endPoint;
    }

    /**
     * get xml data no XML title , we need to make up bulk changed xml data format
     * @return mixed
     * @throws CException
     */
    public function getXmlData(){
        $this->updateXml();
        $xmlData = $this->_xml->asXML();

        /* just remove title : <?xml version="1.0" ?> */
        return str_replace('<?xml version="1.0"?>',"",$xmlData);

    }

    /**
     * Retrieves an object by ID and populates the model attributes
     * @param $id string xero object ID or other id e.g. contactname
     * @return XeRecord if successful, null otherwise
     */
    public function retrieve($id = NULL,$updateUTC = NULL)
    {
        $class = get_called_class();
        $record = new $class;
        try
        {
        $result = Yii::app()->xero->apiGet($this->endPoint, $id,$updateUTC);
        }
        catch (CHttpException $ecp)
        {
            if ( 404 === $ecp->statusCode ) //404 not found, just return null
                return null;
            else
                throw new CHttpException($ecp->statusCode, $ecp->getMessage());
        }
        $jsonData = json_decode($result);

        if ( empty($id) ) {
            $record->setData($jsonData);
        } else {
            $record->setData($jsonData->{$record->endPoint}[0]);
        }
        return $record;
    }


    /**
     * Returns the static model of the specified Xero Model class.
     * The model returned is a static instance of the Xero Model class.
     * It is provided for invoking class-level methods (something similar to static class methods.)
     *
     * @param string $className xero model class name.
     * @return XeModel xero model instance.
     */
    public static function model($className=null)
    {
        if(!$className)
            $className = get_called_class();

        if(isset(self::$_models[$className]))
            return self::$_models[$className];
        else
        {
            $model=self::$_models[$className]=new $className(null);
            $model->attachBehaviors($model->behaviors());
            return $model;
        }
    }

    /**
     * This event is raised before the record is saved.
     * By setting {@link CModelEvent::isValid} to be false, the normal {@link save()} process will be stopped.
     * @param CModelEvent $event the event parameter
     */
    public function onBeforeSave($event)
    {
        $this->raiseEvent('onBeforeSave',$event);
    }


    /**
     * This event is raised after the record is saved.
     * @param CEvent $event the event parameter
     */
    public function onAfterSave($event)
    {
        $this->raiseEvent('onAfterSave',$event);
    }

    /**
     * Adds endpoint xml code and saves the model,
     * then updates the model with the values returned from
     * Xero if successful (to include ID)
     * @param boolean $runValidation whether to run validation
     * @return boolean
     * TODO add save error checking
     *
     */
    public function save($runValidation=true)
    {
        if ($this->beforeSave())
        {
            if(!$runValidation || $this->validate())
            {
                $this->updateXml();

                $result =  Yii::app()->xero->apiPost($this->_endPoint, $this->_xml->asXML(), $this->_id);

                if(!is_null($result))
                {
                    $jsonData = json_decode($result);
                    $this->setData($jsonData->{$this->endPoint}[0]);
                    if (property_exists($this, 'ValidationErrors') && !empty($jsonData->{$this->endPoint}[0]->ValidationErrors)) {
                        $this->ValidationErrors = $jsonData->{$this->endPoint}[0]->ValidationErrors;
                    }
                    return true;
                }
            }
            else
            {
                Yii::log(json_encode($this->getErrors()), 'warning');
            }
        }
        else
        {
            Yii::log('Fail to save', 'warning');
        }
        return false;
    }


    /**
     * save all contacts with bulk mode
     * @param $data
     * @return bool
     */
    public function bulkSave($contents){
        $result =  Yii::app()->xero->apiPost($this->_endPoint, $contents, $this->_id);
        if ( !is_null($result) )
        {
            $jsonData = json_decode($result);
            $this->setData($jsonData);
            return true;
        }
        return false;
    }


    /**
     * This method is invoked after saving a record successfully.
     * The default implementation raises the {@link onAfterSave} event.
     * You may override this method to do postprocessing after record saving.
     * Make sure you call the parent implementation so that the event is raised properly.
     */
    protected function afterSave()
    {
        if($this->hasEventHandler('onAfterSave'))
            $this->onAfterSave(new CEvent($this));
    }


    /**
     * This method is invoked before saving a record (after validation, if any).
     * The default implementation raises the {@link onBeforeSave} event.
     * You may override this method to do any preparation work for record saving.
     * Make sure you call the parent implementation so that the event is raised properly.
     * @return boolean whether the saving should be executed. Defaults to true.
     */
    protected function beforeSave()
    {
        if($this->hasEventHandler('onBeforeSave'))
        {
            $event=new CModelEvent($this);
            $this->onBeforeSave($event);
            return $event->isValid;
        }
        else
            return true;
    }

}





