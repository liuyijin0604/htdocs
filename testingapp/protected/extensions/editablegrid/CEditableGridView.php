<?php

/**
 * CEditableGridView represents a grid view which contains editable rows
 * and an optional 'Quickbar' which fires an action that quickly adds
 * entries to the table.
 *
 * To make a Column editable you have to assign it to the class 'CEditableColumn'
 *
 * Use it like the CGridView:
 *
 * $this->widget('zii.widgets.grid.CEditableGridView', array(
 *     'dataProvider'=>$dataProvider,
 *     'showQuickBar'=>'true',
 *     'quickCreateAction'=>'QuickCreate', // will be actionQuickCreate()
 *     'columns'=>array(
 *           'title',          // display the 'title' attribute
 *            array('header' => 'editMe', 'name' => 'editable_row', 'class' => 'CEditableColumn')
 *     ));
 *
 * With this Config, the column "editable_row" gets rendered with
 * inputfields. The Table-header will be called "editMe".
 *
 * You have to define a action that receives $_POST data like this:
 *   public function actionQuickCreate() {
 *	   $model=new Model;
 *      if(isset($_POST['Model']))
 *       {
 * 	      $model->attributes=$_POST['Model'];
 * 	      if($model->save())
 * 	      $this->redirect(array('admin')); //<-- assuming the Grid was used unter view admin/
 *       }
 *     }
 *
 * @author Herbert Maschke <thyseus@gmail.com>
 * @package zii.widgets.grid
 * @since 1.1
 */

Yii::import('zii.widgets.grid.CGridView');
Yii::import('application.extensions.editablegrid.CEditableColumn');
Yii::import('application.extensions.editablegrid.CEditableButtonColumn');
Yii::import('application.extensions.editablegrid.Relation');

class CEditableGridView extends CGridView {
	public $showQuickBar = true;
	public $addButtonLabel = 'Add';
	public $addButtonUrl = false;
	public $formUrl;
	public $afterInit;
	public $afterSave;
	public $enableHistory = false;
	public $editable;
	public $modelClass;
	
	public function init(){
		parent::init();
		$this->publishAssets();
		if(isset($this->editable)){
			foreach($this->columns as $c){
				if(isset($c->editable)) $c->editable = $this->editable;
			}
		}
	}
	
	public function renderQuickBar() {
		echo "<tr>";
		foreach($this->columns as $c=>$column){
			if(!$column instanceof CButtonColumn){
				if($column instanceof CEditableColumn){
					if(!empty($this->modelClass) && is_object($this->modelClass)){
						$m = $this->modelClass;
					}else{
						$newDPobj = function(){
							$dpd = $this->dataProvider->getData();
							if(empty($dpd)) return new $this->modelClass;
							$cn = get_class($dpd[0]);
							return new $cn;
						};
						$m = empty($this->dataProvider->modelClass)? $newDPobj() : new $this->dataProvider->modelClass;
					}
					if(strstr($column->name, '.') != false){ // Column contains an relation
						$data = explode('.', $column->name);
						$this->widget('Relation', array('model' => $m, 'relation' => $data[0] , 'fields' => $data[1])); 
					}else{
						echo CHtml::openTag('td', $column->htmlOptions);
						$column->renderDataCellContent(-1, $m);
						echo '</td>';
					}
				}else{
					printf('<td></td>');
				}
			}
		}
		printf('<td><a href="%1$s" class="save_btn add_btn" title="%2$s">%2$s</a></td>', $this->addButtonUrl, $this->addButtonLabel);
		echo "</tr>";
	}
	
	public function renderTableFooter(){
		$hasFilter=$this->filter!==null && $this->filterPosition===self::FILTER_POS_FOOTER;
		$hasFooter=$this->getHasFooter();
		if($this->showQuickBar || $hasFilter || $hasFooter){
			echo "<tfoot>\n";
			if($this->showQuickBar)	$this->renderQuickBar();
			if($hasFooter){
				echo "<tr>\n";
				foreach($this->columns as $column)
					$column->renderFooterCell();
				echo "</tr>\n";
			}
			if($hasFilter)
				$this->renderFilter();
			echo "</tfoot>\n";
		}
	}
	
	
	public function getHasFooter(){
		foreach($this->columns as $column)
			if($column->getHasFooter())
				return true;
		return false;
	}
	
	// function to publish and register assets on page 
	public function publishAssets()	{
		$assets = dirname(__FILE__).'/assets';
		$this->baseScriptUrl = Yii::app()->assetManager->publish($assets);
		if(is_dir($assets)){
			Yii::app()->clientScript->registerCoreScript('jquery');
			Yii::app()->clientScript->registerCssFile($this->baseScriptUrl . '/jqEditableGrid.css');
			Yii::app()->clientScript->registerScriptFile($this->baseScriptUrl . '/jqEditableGrid.js', CClientScript::POS_END);
		} else {
			throw new Exception('EFancyBox - Error: Couldn\'t find assets to publish.');
		}
	}
	
	public function renderTableRow($row){
		$data=$this->dataProvider->data[$row];
		if($this->rowCssClassExpression!==null){
			$class=$this->evaluateExpression($this->rowCssClassExpression,array('row'=>$row,'data'=>$data));
		}else if(is_array($this->rowCssClass) && ($n=count($this->rowCssClass))>0)
			$class=$this->rowCssClass[$row%$n];
		else
			$class='';
		
		echo '<tr', empty($class) ? '' : ' class="'.$class.'"', (isset($data->id)? ' data-id="'.$data->id.'"' : ''), '>';
		
		foreach($this->columns as $column)
			$column->renderDataCell($row);
		echo "</tr>\n";
	}
	
	public function registerClientScript(){
		$id=$this->getId();

		if($this->ajaxUpdate===false)
			$ajaxUpdate=false;
		else
			$ajaxUpdate=array_unique(preg_split('/\s*,\s*/',$this->ajaxUpdate.','.$id,-1,PREG_SPLIT_NO_EMPTY));
		$options=array(
			'ajaxUpdate'=>$ajaxUpdate,
			'ajaxVar'=>$this->ajaxVar,
			'pagerClass'=>$this->pagerCssClass,
			'loadingClass'=>$this->loadingCssClass,
			'filterClass'=>$this->filterCssClass,
			'tableClass'=>$this->itemsCssClass,
			'selectableRows'=>$this->selectableRows,
		    'enableHistory'=>$this->enableHistory,
			'filterSelector'=>$this->filterSelector,
		);

		if(empty($this->afterInit))
			$this->afterInit = "null";
		if(empty($this->afterSave))
			$this->afterSave = "null";
		$this->afterAjaxUpdate = "function(id, data){jQuery('#".$this->id."').jqEditableGrid({formUrl: '".$this->formUrl."', afterInit: ".$this->afterInit.", afterSave: ".$this->afterSave."}); }";
		
		if($this->ajaxUrl!==null)
			$options['url']=CHtml::normalizeUrl($this->ajaxUrl);
		if($this->updateSelector!==null)
			$options['updateSelector']=$this->updateSelector;
		if($this->enablePagination)
			$options['pageVar']=$this->dataProvider->getPagination()->pageVar;
		if($this->beforeAjaxUpdate!==null)
			$options['beforeAjaxUpdate']=(strpos($this->beforeAjaxUpdate,'js:')!==0 ? 'js:' : '').$this->beforeAjaxUpdate;
		if($this->afterAjaxUpdate!==null)
			$options['afterAjaxUpdate']=(strpos($this->afterAjaxUpdate,'js:')!==0 ? 'js:' : '').$this->afterAjaxUpdate;
		if($this->ajaxUpdateError!==null)
			$options['ajaxUpdateError']=(strpos($this->ajaxUpdateError,'js:')!==0 ? 'js:' : '').$this->ajaxUpdateError;
		if($this->selectionChanged!==null)
			$options['selectionChanged']=(strpos($this->selectionChanged,'js:')!==0 ? 'js:' : '').$this->selectionChanged;
		
		$options=CJavaScript::encode($options);
		$cs=Yii::app()->getClientScript();
		$cs->registerCoreScript('jquery');
		$cs->registerCoreScript('bbq');
        if($this->enableHistory)
			$cs->registerCoreScript('history');
		$ziigrid_base = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('zii.widgets.assets')).'/gridview';
		$cs->registerScriptFile($ziigrid_base.'/jquery.yiigridview.js',CClientScript::POS_END);
		$cs->registerScript(__CLASS__.'#'.$id, "jQuery('#$id').yiiGridView($options).jqEditableGrid({formUrl: '".$this->formUrl."', afterInit: ".$this->afterInit.", afterSave: ".$this->afterSave."});");
	}

}
