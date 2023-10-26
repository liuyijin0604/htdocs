<?php
ini_set('serialize_precision', 14);
class FarmlandController extends PController{
	
	public $layout = 'pcaw';

	public $products = [
		['87-911-68001', 'Cute Betty\'s Infant Goat Milk Powder Formula 800g (0-6 Months, Stage 1)', 300, 0.8],
		['87-911-68002', 'Cute Betty\'s Older Infant Goat Milk Powder Formula 800g (6-12 Months, Stage 2)', 300, 0.8],
		['87-911-68003', 'Cute Betty\'s Young Children Goat Milk Powder Formula 800g (12-36 Months, Stage 3)', 300, 0.8],
		['87-911-64001', 'Cute Betty\'s Infant Goat Milk Powder Formula 400g (0-6 Months, Stage 1)', 672, 0.4],
		['87-911-64002', 'Cute Betty\'s Older Infant Goat Milk Powder Formula 400g (6-12 Months, Stage 2)', 672, 0.4],
		['01-911-88001', 'Biostime SN-2 BIO PLUS Ultra Goat Infant Formula 800g (0-6 Months, Stage 1)', 300, 0.8],
		['01-911-88002', 'Biostime SN-2 BIO PLUS Ultra Goat Follow On Formula 800g (6-12 Months, Stage 2)', 300, 0.8],
		['01-911-88003', 'Biostime SN-2 BIO PLUS Ultra Goat Toddler Milk Drink 800g (12-36 Months, Stage 3)', 300, 0.8],
		['45-911-78001', 'Biostime SN-2 Goat Plus Premium Goat Infant Formula 800g (0-6 Months, Stage 1)', 300, 0.8],
		['45-911-78002', 'Biostime SN-2 Goat Plus Premium Goat Follow-Up Formula 800g (6-12 Months, Stage 2)', 300, 0.8],
		['45-911-78003', 'Biostime SN-2 Goat Plus Premium Goat Growing-Up Formula 800g (12-36 Months, Stage 3)', 300, 0.8],
	];

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex(){
	}
	
	public function actionPalletLabel(){
		if(!empty($_POST)){
			foreach($this->products as $p){
				if($p[0] == $_POST['prod']){
					$prod = $p;
					break;
				}
			}
			$html = $this->renderPartial('_farmland_plt_label', ['prod' => $prod, 'data' => $_POST], true);
			oPDF::html2pdf($html, 1);
		}
		$this->render('pallet_label');
	}
}