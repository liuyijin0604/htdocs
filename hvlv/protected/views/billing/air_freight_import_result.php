
<h1> Air Freight Billing Import Result </h1>

<?php
if ( !empty($model->meta) ) {

    echo '<div><span> We got the following errors :</span> <br/>';
    $errors = json_decode( $model->meta, true );
    foreach ( $errors as $error ) {
        echo '<span> Sheet index : <span style="font-weight: bold;">' . $error['index'].  '</span></span> <br/>';
        echo '<span> Error : <span style="color:#ff0000;">' . $error['error'].  '</span></span> <br/>';
    }
    echo '</div>';
} else {
?>
    <div><span style="color: #008000;"> ALl Imported Successfully</span></div>
<?php
}
?>
