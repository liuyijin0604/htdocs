<?php
if($orgId==null)
{
    $orgId = yii::app()->getId();
}
$org= Org::model()->findByPk($orgId);
$name=isset($org->name)?$org->name:'All';
if ($orgId==1401) $name="商壹";
?>
<input type="hidden" value="<?=$orgId?>" id='org_id' />
<h4><?=$name?></h4>
<style>
    .cloumn_red{
        background-color:red;
    }   
</style>

<?php
$this->widget('application.extensions.CSpanableGridView.CSpanableGridView', [
    'id'=>$_GET["tabid"].'_cs_grid',
    'cssFile' => false,
    'dataProvider'=>$modelQuestion->search(true, 10, false,true),
    'filter'=>$modelQuestion,
    'columns'=>[
      ["name"=>"ticket","value"=>'$data->ShipmentQuestionSubmit->ticket','spanable' => true, 'spanDepands' => ['$data->ShipmentQuestionSubmit->ticket']],
      ['name'=>'s_read','filter'=>CHtml::dropDownList('ShipmentQuestion[s_read]', $modelQuestion->s_read, $this->t(ShipmentQuestion::$readTypes2), ['prompt'=>$this->t('all')]),'value'=>'ShipmentQuestion::$readTypes2[$data->s_read]'],
      ['name'=>'ShipmentQuestionSubmit.type','filter'=>CHtml::dropDownList('ShipmentQuestionSubmit[type]', $modelQuestion->submitType, $this->t(ShipmentQuestionSubmit::$types), ['prompt'=>$this->t('all')]),'value'=>'ShipmentQuestionSubmit::$types[$data->ShipmentQuestionSubmit->type]'],
      ["name"=>"Shipment.hbn",'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("imParcel/update", array("id" => @$data->Shipment->id))."\" class=\"tab_link\" title=\"".@$data->Shipment->hbn."\">".@$data->Shipment->hbn."</a>"','spanable' => true, 'spanDepands' => ['@$data->Shipment->hbn']],
      // ["name"=>"ShipmentQuestionSubmit.org.name","value"=>'$data->ShipmentQuestionSubmit->getSubmitUserType()'],
      ["name"=>"ref",'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("imParcel/update", array("id" => @$data->Shipment->id))."\" class=\"tab_link\" title=\"".@$data->Shipment->ref."\">".@$data->Shipment->ref."</a>"','spanable' => true, 'spanDepands' => ['@$data->Shipment->ref']],
      ["name"=>"Shipment.status",'spanable' => true, 'spanDepands' => ['empty($data->Shipment)?"":$data->Shipment->getStatus()'],'value'=>'empty($data->Shipment)?"":$data->Shipment->getStatus()'],
       // ["name"=>"ShipmentQuestionSubmit.email"],
       //  ["name"=>"ShipmentQuestionSubmit.phone"],
      ["name"=>"faq",
      'filter'=>CHtml::dropDownList('ShipmentQuestion[faq]', $modelQuestion->faq, $this->t(CsFaq::getFaqList()), ['prompt'=>$this->t('All')]),],
      // ["name"=>"c_read","value"=>'ShipmentQuestion::$readTypes2[$data->c_read]','filter'=>CHtml::dropDownList('ShipmentQuestion[c_read]', $modelQuestion->c_read, $this->t(ShipmentQuestion::$readTypes2), ['prompt'=>$this->t('All')])],
      ["name"=>"faq_answer",'type' => 'raw',"value"=>'CHtml::dropDownList("quickFaqAnswer".$data->id, $data->faq_answer, $data->getFaqAnswerList(), array("prompt"=>"SELECT","onchange"=>"return quickFaqAnswerChange($data->id);","value"=>$data->id))',"filter"=>false],
      // ['name'=>'ShipmentQuestionSubmit.type','type'=>'raw','value'=>'ShipmentQuestionSubmit::$types[$data->ShipmentQuestionSubmit->type]'],
      
      ['name'=>'ShipmentQuestionSubmit.c_note','type'=>'raw','value'=>function ($data) {
            return CHtml::tag('div', ['title'=>strip_tags($data->ShipmentQuestionSubmit->c_note)], mb_substr(strip_tags($data->ShipmentQuestionSubmit->c_note), 0, 25));
        }],
      ['name'=>'process_type','filter'=>CHtml::dropDownList('ShipmentQuestion[process_type]', $modelQuestion->process_type, $this->t(ShipmentQuestion::$processTypes), ['prompt'=>$this->t('all')]),'value'=>'ShipmentQuestion::$processTypes[$data->process_type]'],
      ["name"=>"date","value"=>'$data->ShipmentQuestionSubmit->date'],
       ["name"=>"answer_date"],
      [
            'class'=>'oButtonColumn',
            'template'=>'{Operation}',
            'buttons'=>[
                
                'Operation' => [
                    'url'=>'Yii::app()->createURL("customerService/update",array("id"=>$data->id))',
                    'imageUrl'=>false,
                    'visible'=>'true',
                    'options' => ['class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->ShipmentQuestionSubmit->ticket', 'data-win-class' => 'L'],
                ],
            ],
        ],
      [
            'class'=>'oButtonColumn',
            'template'=>'{images}{emails}{log}',
            'buttons'=>[
              'images' => [
                    'imageUrl'=>false,
                    'options' => ['class' => 'jqm_link grid_view_btn', 'label' => 'Log', 'data-win-class' => 'L'],
                    'visible' => 'true',
                    'url' => 'Yii::app()->createUrl("customerService/getImages", ["id" => $data->id])',
                    'label' => 'Images'
                ],
              'emails' => [
                    'imageUrl'=>false,
                    'options' => ['class' => 'tab_link grid_view_btn','title'=>'$data->ShipmentQuestionSubmit->ticket." ".@$data->Shipment->ref', 'label' => 'Log', 'data-win-class' => 'L'],
                    'visible' => 'true',
                    'url' => 'Yii::app()->createUrl("customerService/getEmails", ["id" => $data->id])',
                    'label' => 'Related Emails',
                ],
                'log' => [
                    'imageUrl'=>false,
                    'options' => ['class' => 'jqm_link grid_view_btn', 'label' => 'Log', 'data-win-class' => 'L'],
                    'visible' => 'true',
                    'url' => 'Yii::app()->createUrl("customerService/log", ["id" => $data->id])',
                    'label' => 'Log'
                ],
            ],
        ],
    ],
]); ?>

<script type="text/javascript">

    var MyFAQTab = $('#<?=$_GET["tabid"];?>');
    var MyFAQPanel = MyFAQTab.data('panel');
    $(function(){
        MyFAQTab.unbind('reload_cs_grid').bind('reload_cs_grid', function(){
                    $('#<?=$_GET["tabid"];?>_cs_grid', MyFAQTab.data('panel')).yiiGridView('update');
                    return false;
                });

    });

    function quickFaqAnswerChange(id)
    {
            if(confirm("Are you sure to faq answer?"))
            { 
               var selectFA= $("#quickFaqAnswer"+id).children('option:selected').val();
               $.ajax({
                       url: '<?=$this->createUrl("customerService/faqAnswer")?>',
                       type: "post",
                       data: {"faqAnswer":selectFA,"id":$("#quickFaqAnswer"+id).attr("value")},
                       success: function(r) {
                           if(r=='done')
                            {
                               myApp.notice('Done', 5000);
                            }else
                            {
                               myApp.alert(r, false);   
                            }
                            MyFAQTab.trigger('reload_cs_grid');
                        },
                       error: function(e) {
                           console.log(e);
                       }
                   });     
            }
    }

</script>