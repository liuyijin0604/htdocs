<?php

/**
 * This is the model class for table "aupostreconciliation".
 *
 * The followings are the available columns in table 'reconciliation':
 * @property string $id
 * @property integer $client_type
 * @property string $invoice_no
 * @property string $invoice_date
 * @property string $invoice_total
 * @property string $my_total
 */
class AupostReconciliation extends Reconciliation
{

    public static $my_type = 1;

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'client_type' => 'From',
			'invoice_no' => 'Consol No.',
			'invoice_date' => 'Consol Date',
			'invoice_total' => 'Invoice Total',
			'my_total' => 'My Total',
            'deviation' => 'Deviation',
		);
	}

}
