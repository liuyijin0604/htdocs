<?php $form=$this->beginWidget('CActiveForm', array(
    'action'=>Yii::app()->createUrl($this->route),
    'method'=>'get',
)); ?>
    <?php echo $form->textField($model,'name',['id'=>'myName']); ?>
    <?php echo CHtml::submitButton('Search',["id"=>"searchButton"]); ?>
<?php $this->endWidget(); ?>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'testingId',
    'dataProvider' => $dataProvider,
    'filter'=>$model,
    'columns'=>array
	 (
		array(
            'header' => 'Name',
            'name' => 'name',
            'filter'=>CHtml::hiddenField('Product[name]',@$model->name),
        ),

        array(
            'header' => 'Description',
            'name' => 'description',
        ),
        // add more columns as needed
        array(
            'header' => 'Price',
            'name' => 'price',
            'value' => 'Yii::app()->numberFormatter->formatCurrency($data->price, "USD")',
        ),
        
        array(
                'class' => 'CButtonColumn',
                'template' => '{update}',
                'buttons' => array(
                    'update' => array(
                     'label' => 'Update',
                    'imageUrl' => Yii::app()->baseUrl.'/images/update.png',
                    'url' => '$data->id',
                     'options' => array(
                        'class' => 'update-model'
                    ),
                ),
            ),
        ),

	 ),
)); ?>





<div id="modal-form" class="modal fade">
  <div class="modal-header">
  	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Update Model</h3>
  </div>
  <div class="modal-body" style="background-color:#FFF;">
        <form id="update-model-form" method="post">
            <input type="hidden" name="id" id="id" />

            <label for="name">Name:</label>
            <input type="text" name="name" id="name" /><br />

            <label for="description">Description:</label>
            <textarea name="description" id="description"></textarea><br />

            <label for="price">Price:</label>
            <input type="text" name="price" id="price" /><br />

            <input type="submit" value="Update" />
        </form>
    </div>
</div>






<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" type="text/javascript"></script>
<script type="text/javascript">
	
	$('#searchButton').on('click',function(){
		$.fn.yiiGridView.update('testingId',{data:{'Product[name]':$('#myName').val()}});
		return false;
	});



	$(document).on('click', '.update-model', function(e) {
		let id = $(this).attr('href');
		// alert(id);
         // 发送 AJAX 请求以获取模型数据
       $.ajax({
        url: '<?php echo Yii::app()->createUrl("table/getData"); ?>',
        type: 'get',
        data: {'id':id},
        success: function(response) {
            // 将数据填充到表单中
            var data = $.parseJSON(response);
            // console.log(data);
            $('#update-model-form input[name="name"]').val(data.name);
            $('#update-model-form textarea[name="description"]').val(data.description);
            $('#update-model-form input[name="price"]').val(data.price);
        },
    });
     e.preventDefault();

    // $('#modal-form').on('show.bs.modal', function() {
    //  var modal = $(this);
    //  var dialog = modal.find('.modal-form');
    //  modal.css('display', 'block');
    //  dialog.css('margin-top', Math.max(0, ($(window).height() - dialog.height()) / 2));
    // });

     $('#modal-form').modal('show'); 

    // 将模型 ID 保存在隐藏的输入字段中
     $('#update-model-form input[name="id"]').val(id);
    

    // 为表单提交设置事件处理程序
    $('#update-model-form').submit(function(e) {
        e.preventDefault();
      // 发送 AJAX 请求以更新模型
        $.ajax({
            url: '<?php echo Yii::app()->createUrl("table/update"); ?>',
            type: 'post',
            data:$(this).serialize(),
            success: function(response) {
                // 关闭弹出窗口
                 
                $('#modal-form').modal('hide');

                // 刷新 Grid View
                $.fn.yiiGridView.update('testingId');
               },
            });
        });
    });

  
</script>


