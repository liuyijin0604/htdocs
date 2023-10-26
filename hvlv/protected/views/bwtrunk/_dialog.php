<div id="dialog-container">
<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'dialog-grid',
    'dataProvider' => new CArrayDataProvider($bwtrunks),
    'columns' => array(
        'id',
        array(
            'name' => 'CTO_start',
            'type' => 'raw',
            'value' => function($data) {
                return CHtml::activeTextField($data, 'CTO_start', array('class' => 'editable-field'));
            },
        ),
        array(
            'name' => 'CTO_finish',
            'type' => 'raw',
            'value' => function($data) {
                return CHtml::activeTextField($data, 'CTO_finish', array('class' => 'editable-field'));
            },
        ),
        array(
            'name' => 'Client_start',
            'type' => 'raw',
            'value' => function($data) {
                return CHtml::activeTextField($data, 'Client_start', array('class' => 'editable-field'));
            },
        ),
        array(
            'name' => 'Client_finish',
            'type' => 'raw',
            'value' => function($data) {
                return CHtml::activeTextField($data, 'Client_finish', array('class' => 'editable-field'));
            },
        ),
        array(
            'class' => 'CButtonColumn',
            'template' => '{save} {delete}',
            'buttons' => array(
                'save' => array(
                    'label' => 'Save',
                    'options' => array(
                        'class' => 'save-button',
                    ),
                ),
                'delete' => array(
                    'label' => 'Delete',
                    'options' => array(
                        'class' => 'delete-button',
                    ),
                ),
            ),
        ),
    ),
));

?>
</div>


<script>
$(document).ready(function() {

    // Save button click
    $(document).on('click', '.save-button', function(e) {
        event.preventDefault(); 
        var row = $(this).closest('tr');
        var id = row.find('td:first-child').text();
        var ctoStart = row.find('input[name*=CTO_start]').val();
        var ctoFinish = row.find('input[name*=CTO_finish]').val();
        var clientStart = row.find('input[name*=Client_start]').val();
        var clientFinish = row.find('input[name*=Client_finish]').val();

        $.ajax({
            url: '<?php echo $this->createUrl("bwtrunk/save"); ?>',
            type: 'POST',
            data: {
                id: id,
                CTO_start: ctoStart,
                CTO_finish: ctoFinish,
                Client_start: clientStart,
                Client_finish: clientFinish
            },
            success: function(response) {
                if (response === 'success') {
                    // Handle successful save (if needed)
                } else {
                    alert('An error occurred while saving the data.');
                }
            },
            error: function() {
                alert('An error occurred while saving the data.');
            }
        });

        return false;
    });

    // Delete button click
    $(document).on('click', '.delete-button', function(e) {
        e.preventDefault();

  
        var row = $(this).closest('tr');
        var id = row.find('td:first-child').text();
  
       
        $.ajax({
            url: '<?php echo $this->createUrl("bwtrunk/delete"); ?>',
            type: 'POST',
            data: { id: id },
            success: function(response) {
                if (response === 'success') {
                    //alert('Data deleted successfully!');
                    row.remove();
                } else {
                    alert('An error occurred while deleting the data.');
                }
            },
            error: function() {
                alert('An error occurred while deleting the data.');
            }
            
        });

        return false;
    });
});
</script>


