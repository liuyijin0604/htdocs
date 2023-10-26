<?php

class GoogleChart extends CWidget {

	/**
	 * @var string $containerId the container Id to render the chart to
	 */
	public $containerId;

	/**
	 * @var string $chartType the type of visualization -ie PieChart
	 */
	public $chartType;

	/**
	 * @var string $packages the type of packages, default is corechart
	 */
	public $packages = ['corechart'];  // such as 'orgchart' and so on.
	
	public $loadVersion = "current"; //such as 1 or 1.1

	/**
	 * @var array $data
	 */
	public $data = array();

	/**
	 * @var array $options additional configuration options
	 */
	public $options = array();

	/**
	 * @var array $htmlOption the HTML tag attributes configuration
	 */
	public $htmlOptions = array();

	/**
	 * Widget's run method
	 */
	public function run() {
		$id = $this->getId();
		// if no container is set, it will create one
		if ($this->containerId == null) {
			$this->htmlOptions['id'] = 'div-chart' . $id;
			$this->containerId = $this->htmlOptions['id'];
			echo '<div ' . CHtml::renderAttributes($this->htmlOptions) . '></div>';
		}
		$this->registerClientScript();
	}

	/**
	 * Registers required scripts
	 */
	public function registerClientScript() {
		$id = $this->getId();
		$jsData = CJavaScript::jsonEncode($this->data);
		$jsOptions = CJavaScript::jsonEncode($this->options);

		$script = 'var loadCharts = function(){
var ' . $id . '=null;
function drawChart' . $id . '() {
	var data = google.visualization.arrayToDataTable(' . $jsData . ');
	var options = ' . $jsOptions . ';
	' . $id . ' = new google.visualization.' . $this->chartType . '(document.getElementById("' . $this->containerId . '"));
	' . $id . '.draw(data, options);
}

try{
	google.charts.load("'.$this->loadVersion.'", {packages: ' . CJavaScript::jsonEncode($this->packages) . '});
	google.charts.setOnLoadCallback(drawChart' . $id . ');
}catch(e){
	drawChart' . $id . '();
}
};
if(typeof(google) != "object") $.getScript("https://www.gstatic.com/charts/loader.js", loadCharts);
else loadCharts();
';

		$cs = Yii::app()->getClientScript();
		$cs->registerScript(__CLASS__.'#'.$id, $script, CClientScript::POS_HEAD);
	}

}
