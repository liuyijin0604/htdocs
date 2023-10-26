<?php 
class PaymentService extends Service
{
	public function autoGenerateCreditNote($fromInvoiceId,$amount,$comment,$pass=false)
	{
		$inv = Invoice::model()->findByPk($fromInvoiceId);
		if(empty($pass))
		{
			$check = CreditNoteFromInvoice::model()->find("invoice_id = :invId",[":invId"=>$fromInvoiceId]);
			if(!empty($check))
			{
				$invCheck[] = $inv->no;
				$creditNoteCheck[] = Payment::model()->findByPk($check->payment_id)->no;
				if(!empty($invCheck))
				{
					return ['done'=>false,'msg'=>'confirmGen','invoice'=>join(',',$invCheck),'credit_note'=>join(',',$creditNoteCheck)];
				}
			}
		}
		$consol = Consol::model()->findByPk($inv->consol_id);
		$payment = new Payment;
		$payment->org_id = $inv->to_id;
		if (!empty($inv->mdata['suborg'])) $payment->mdata['suborg'] = $inv->mdata['suborg'];
		$payment->amount = $amount;
		$payment->ata = $payment->amount;
		$payment->ref = 'Dispute for Invoice: ' . $inv->no;
		$payment->date = date('Y-m-d');
		$payment->bank = 91; // manual credit note
		$payment->currency = $inv->currency;
		$payment->status = Payment::PAYMENT_STATUS_POSTED;
		$payment->type = Payment::PAYMENT_TYPE_CREDIT_NOTE;
		$payment->dpmt = $inv->dpmt;
		if(!empty($consol))
		{
			$payment->mdata['consol'] = $consol->no;
		}
		$payment->flag = Payment::RESEND_EMAIL;
		$payment->save();
		$total = 0;

		$il = new CreditLine;
		$il->pid = $payment->id;
		$il->description = $comment;
		$il->rate = $amount;
		$il->qty = 1;
		$il->amount = $amount;
		if ($inv->gst>0) {
			$gst = round($il->amount * 10 / 100, 2);
			$il->tax = 'OUTPUT';
		} else {
			$gst = 0;
			$il->tax = 'EXEMPTOUTPUT';
		}
		$il->gst = $gst;
		$il->amount += $il->gst;
		$il->gst = number_format($il->gst, 2, '.', '');
		$il->amount = number_format($il->amount, 2, '.', '');
		$il->save();

		$total += $il->amount;

		$payment->amount = number_format($total, 2, '.', '');
		$payment->ata = number_format($total, 2, '.', '');
		$payment->save();


		$creditNoteFromInvoiceObj = new CreditNoteFromInvoice();
		$creditNoteFromInvoiceObj->invoice_id = $inv->id;
		$creditNoteFromInvoiceObj->payment_id = $payment->id;
		$creditNoteFromInvoiceObj->save();

		$payment->refresh();
		return ['done'=>true,'msg'=>'Done','no'=>$payment->no];
	}
}
?>