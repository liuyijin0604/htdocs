<?php
class FbaFormService extends Service implements FormService
{
	public function submitForm($postData)
	{
		$transaction = Yii::app()->db->beginTransaction();
		try
		{
		  	$imf = new ImportsMetaform();
		   	$imf->mdata = $postData['ImportsMetaform'];
		   	//$imf->meta = json_encode($_POST['ImportsMetaform']);
		   	$imf->create = date('Y-m-d H:i:s');
		   	$imf->type = ImportsMetaform::TYPE_FBAREMOVAL;
		   	$imf->save();
			$transaction->commit();
		}catch(Exception $ex)
		{
		    $transaction->rollback();
		    throw $ex;
		    return false;
		}
		return true;
	}
	public function checkForm($postData)
	{
		$errors = [];
		$sellerName = $postData['ImportsMetaform']['seller_name'];
		$sellerEmail = $postData['ImportsMetaform']['seller_email'];
		$sellerMobile = $postData['ImportsMetaform']['seller_mobile'];
		$productDetail = $postData['ImportsMetaform']['product_detail'];
		$totalValue = $postData['ImportsMetaform']['total_value'];
		$fbaPoNumber = $postData['ImportsMetaform']['fba_po_number'];
		$fbaShipmentId = $postData['ImportsMetaform']['fba_shipment_id'];
		if(empty($sellerName))
		{
			$errors[] = "Seller Name";
		}
		if(empty($sellerEmail))
		{
			$errors[] = "Seller Email";
		}
		if(empty($sellerMobile))
		{
			$errors[] = "Seller Mobile";
		}
		if(empty($productDetail))
		{
			$errors[] = "Product Detail";
		}
		if(empty($totalValue))
		{
			$errors[] = "Total Value";
		}
		return $errors;
	}
}
?>