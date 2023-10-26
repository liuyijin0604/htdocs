<?php 
class BillingService extends Service
{
	public function submitArrange($id,$amount,$pass=false)
	{
		$selected = [];
		$selected[$id] = $amount;
		Yii::app()->cache->set('billing_streamline_selected_' . session_id(), $selected);
		$selected = Yii::app()->cache->get('billing_streamline_selected_' . session_id()) ? Yii::app()->cache->get('billing_streamline_selected_' . session_id()) : [];

		$data = Billing::getPayListByLine();
		$rs = [];
		foreach ($data as $billing) {
			$rs[$billing->currency][] = $billing;
		}

		foreach ($rs as $currency => $billings) {
			foreach ($billings as $billing) {
				$toPay = (!empty($selected[$billing->id]) ? $selected[$billing->id] : $billing->getBalance());
				if($toPay>$billing->getBalance())
				{
					return json_encode(['done' => false, 'msg' => $billing->billing_cref.' to pay is larger than balance, please select again']);
				}
			}
		}

		foreach ($rs as $currency => $billings) {
			$pa = new PaymentArrange;
			$pa->created = date('Y-m-d');
			$pa->user_id = Yii::app()->user->id;
			if(!empty($pass))
			{
				$pa->status = PaymentArrange::PAYMENT_ARRANGE_STATUS_BOSS_APPROVED;
			}else
			{
				$pa->status = PaymentArrange::PAYMENT_ARRANGE_STATUS_REQURING_BOSS_APPROVE;
			}


			$pa->currency = $currency;
			$pa->save();
			foreach ($billings as $billing) {
				$pab = new PaymentArrangeBilling;
				$pab->payment_arrange_id = $pa->id;
				$pab->billing_id = $billing->id;
				$pab->amount = !empty($selected[$billing->id]) ? $selected[$billing->id] : $billing->getBalance();
				if(!empty($pass))
				{
					$pab->status = PaymentArrange::PAYMENT_ARRANGE_STATUS_BOSS_APPROVED;
				}else
				{
					$pab->status = PaymentArrange::PAYMENT_ARRANGE_STATUS_REQURING_BOSS_APPROVE;
				}
				$pab->save();
				$billing->status = Billing::BILLING_STATUS_ARRANGED_PAYMENT;
				$billing->update('status');
			}
			$pa->updateTotal();
		}

		Yii::app()->cache->set('billing_streamline_selected_' . session_id(), []);
		
		return json_encode(['done' => true, 'msg' => 'Successfully']);
	}

}
?>