<?php
class ExItem extends CFormModel{
	public $id, $g, $m, $q, $p, $gw, $nw, $wu, $v, $t, $o, $s;
	
	public function rules(){
		return array(
			array('id, g, m, q, p, gw, nw, wu, v, t, o, s', 'safe'),
		);
	}
	
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'g' => 'Goods',
			'm' => 'Consol',
			'q' => 'Qty',
			'p' => 'Package',
			'gw' => 'Gross',
			'nw' => 'Net',
			'wu' => 'Weight Unit',
			'v' => 'Value',
			't' => 'Tax',
			'o' => 'Origin',
			's' => 'Serial',
		);
	}
}
