<?php

/**
 * This is the model class for table "d2z_country_rate".
 *
 * The followings are the available columns in table 'd2z_country_rate':
 * @property integer $id
 * @property string $tracking_no
 */
class D2zCountryRate extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    const START_TRACKING_ID=9000000;
    const END_TRACKING_ID=9049999;
    public function tableName()
    {
        return 'd2z_country_rate';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['tracking_no', 'length', 'max'=>20],
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            ['id, tracking_no', 'safe', 'on'=>'search'],
        ];
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return [
        ];
    }
    public function afterFind()
    {
        if (empty($this->tracking_no)) {
            $this->tracking_no='33A8Y'.(self::START_TRACKING_ID+$this->id);
            $this->update('tracking_no');
        }
    }
    public static function genNo()
    {
        return ConnoteRange::newNumber('Org', 1426);
    }
    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tracking_no' => 'Tracking No',
        ];
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('tracking_no', $this->tracking_no, true);

        return new CActiveDataProvider($this, [
            'criteria'=>$criteria,
        ]);
    }
    /*
     * manifest via FTP,
     * @Params  $rs an Array of Shipments
     * @Return boolean
     */
    public static function manifest($rs, $ref)
    {
        if (!empty($rs)&& is_array($rs)) {
            //generate excel file
            $fileName= Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'tla_'.$ref.time().".xls";
            $xsl=new oExcel();
            $xsl->supported('tla_'.$ref.time().".xls");
            $i=1;
            $xsl->setColWidth([30,15,15,15,30,15,15,15,15,20,20,30,15,15,15,30,15,15,15,15,15,15,15,15,15,15,15,15]);
            $xsl->addRow($i++, ['invoice_number','value','shipped_quantity','delname','deladdr1','deladdr2 (suburb)','deladdr3 (state)','deladdr4 (country)','postcode',
                'email','telephone','product_description','origin','weight','tracking template','tracking number','inventory short name','supplier',
                'bill me','ServiceType','BagName','Length','Width','Height','Currency','Cost_Freight','Cost_Insurance','ABN_ARN Number']);
            foreach ($rs as $r) {
                $lref=$r->ref.sprintf('%02s', 1).'00093'.'50'.'0';
                $lref .= AusPostAPI::aidChkDgt($lref);
                $lref="019931265099999891".$lref;
                $xsl->addRow($i++, [
                    $r->hbn,$r->dvalue,$r->pkg,$r->cnee->name,$r->cnee->address,$r->cnee->suburb,$r->cnee->state,'AU',$r->cnee->postcode,
                    $r->cnee->email,$r->cnee->tel,$r->getGoods(),'China',$r->weight,'',$lref,'','',1,'APST',1,1,1,1,'USD','','','']);
            }
            $xsl->getActiveSheet()->getStyle('B2:B'.($i-1))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
            $xsl->getActiveSheet()->getStyle('N2:N'.($i-1))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
            $xsl->getActiveSheet()->getStyle('C2:C'.($i-1))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
            $xsl->getActiveSheet()->getStyle('S2:S'.($i-1))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
            $xsl->getActiveSheet()->getStyle('U2:X'.($i-1))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER); //FORMAT_TEXT
                
            $xsl->getActiveSheet()->getStyle('A2:A'.($i-1))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            $xsl->getActiveSheet()->getStyle('D2:M'.($i-1))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            $xsl->getActiveSheet()->getStyle('P2:P'.($i-1))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            $xsl->getActiveSheet()->getStyle('T2:T'.($i-1))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            $xsl->getActiveSheet()->getStyle('Y2:Y'.($i-1))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                 
            $xsl->output($fileName, null, false);
            if (file_exists($fileName)) {
                $d2z=new D2zAPI();
                if (self::upLoadManifestFile($fileName)) {
                    $transaction=Yii::app()->db->beginTransaction();
                    try {
                        foreach ($rs as $r) {
                            if (empty($r->trans)) {
                                $ts=new Tranship();
                            } else {
                                $ts=$r->trans[0];
                            }
                            $r->mdata['manifest_weight']=$r->weight;
                            $r->updateMeta();
                            $ts->org_id = 101;  // for Fastway post office
                            $ts->man_id = $r->man_id;
                            $ts->pid=$r->id;
                            $ts->type = 80;  // shipment transfer to a different delivery courier
                            $ts->status = 19; // in finally moving status
                            $ts->connote = $r->ref;
                            $ts->time = date('Y-m-d H:i:s');
                            $ts->cost = number_format($d2z->getD2zCountryCost($r), 2, '.', '');
                            $ts->mdata['oid']=$r->ref;
                            $ts->save();
                        }
                        $transaction->commit();
                    } catch (Exception $ex) {
                        $transaction->rollback();
                        throw $ex;
                    }
                }
                unlink($fileName);
                return true;
            }
            unlink($fileName);
        }
        return false;
    }
    public static function upLoadManifestFile($fileName)
    {
        $uploaded = false;
        try {
            $ftp = ftp_connect("ec2-18-216-183-227.us-east-2.compute.amazonaws.com", 21, 10);
            if ($ftp && ftp_login($ftp, "ftpuser", "letmein")) {
                ftp_set_option($ftp, FTP_USEPASVADDRESS, false);
                ftp_pasv($ftp, true);
                $file=fopen($fileName, 'r');
                $uploaded = ftp_fput($ftp, basename($fileName), $file, FTP_BINARY);
            }
        } catch (Exception $ex) {
        } finally {
            if($ftp){
                ftp_close($ftp);
                fclose($file);
            }
        }

        if(!$uploaded){
            include_once('PHPMailer/class.phpmailer.php');
            $mail = new PHPMailer();
            $mail->CharSet = 'UTF-8';
            $mail->IsHTML(true);
            $mail->From     = 'donotreplay@toplogistics.com.au';
            $mail->FromName = 'PCA Express - No Reply';
            $mail->Subject  = 'Unable to upload manifest to FTP server';
            $mail->Body = "There's some problem upload manifest to FTP server";
            $mail->AddAddress('chris@d2z.com.au');
            $mail->AddCC('gero@toplogistics.com.au');
            $mail->AddAttachment($fileName, basename($fileName));
            $mail->Send();
        }
        
        return $uploaded;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return D2zCountryRate the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}
