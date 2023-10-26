<?php

/**
 * This is the model class for table "general_cost_split".
 *
 * The followings are the available columns in table 'general_cost_split':
 * @property integer $id
 * @property integer $chargecode_id
 * @property string $note
 * @property string $percent_meta
 * @property string $created
 */
class GeneralCostSplit extends CActiveRecord
{
    // define some fields related to charge code split details
    public $chargecode,$desc,$p3pl,$paf,$pim,$pex;

    public $mdata = array();

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'general_cost_split';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('chargecode_id', 'required'),
			array('chargecode_id', 'numerical', 'integerOnly'=>true),
			array('note', 'length', 'max'=>100),
			array('created', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, chargecode_id, note, percent_meta, created', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
            'chargecode' => array(self::BELONGS_TO,'Chargecode','chargecode_id')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'chargecode_id' => 'Charge Code',
			'note' => 'Remark',
			'percent_meta' => 'Percent Info.',
			'created' => 'Created',
		);
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

		$criteria->compare('id',$this->id);
		$criteria->compare('chargecode_id',$this->chargecode_id);
		$criteria->compare('note',$this->note,true);
		$criteria->compare('percent_meta',$this->percent_meta,true);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'pagination'=> array(
                'pageSize' => 100,
            )
		));
	}

    public function getCharcodeDesc(){
        $chargeCode = Chargecode::model()->findByPk($this->chargecode_id);
        if ( !empty($chargeCode) ) {
            return $chargeCode->code .  ':' . $chargeCode->name;
        }
        return '';
    }

    public function beforeSave(){
        if ( empty($this->created)) $this->created = date('Y-m-d H:i:s');
        if( !empty($this->mdata)) $this->percent_meta = json_encode($this->mdata);
        return true;
    }

    public function afterFind(){
        if ( !empty($this->percent_meta) ) $this->mdata = json_decode($this->percent_meta, true);
        if ( isset($this->mdata['pim'])) $this->pim = $this->mdata['pim'];
        if ( isset($this->mdata['pex'])) $this->pex = $this->mdata['pex'];
        if ( isset($this->mdata['p3pl'])) $this->p3pl = $this->mdata['p3pl'];
        if ( isset($this->mdata['paf'])) $this->paf = $this->mdata['paf'];

     }

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return GeneralCostSplit the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
