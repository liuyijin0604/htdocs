<?php
    $bankStatement = BankStatement::model()->findByPk($sid);
    $model = Invoice::model();
    $model->unsetattributes();
    $mdp = $model->search(true,100);
    if ( !empty($bankStatement) ) {
        $debits = $bankStatement->debits;
        $credits = $bankStatement->credits;
        if ( $credits > 0 ) {
            // try to match all credits
            // including invoices, receive money
            $model = Invoice::model();
            $model->unsetattributes();
            $mdp = $model->search(true,100);
        }
    }
?>
<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'selectableRows' => 2,
    'id'=>'bank-reconcile-targets-grid',
    'cssFile' => false,
    'dataProvider'=> $mdp,
    'columns'=>array(
        array(
            'id'=>'selectedItems',
            'class'=>'CCheckBoxColumn',
        ),
        'date',
        'no',
        'total',
        array('name' => 'status', 'value' => '$data->cust->name'),
        'ref')
));
?>
