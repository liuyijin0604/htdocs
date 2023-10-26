<style type="text/css">
  p{
     font-family: Calibri;
  }
  table {
    font-family: Calibri;
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 1em;
  }

  th {
    font-size: 14px;
    color: #FFF;
    background-color: #000;
    text-align: left;
  }

  th,
  td {
    border: 1px solid black;
    padding: 0px;
    font-size: 1.2em;
    height: 2em;
  }

  .left-column {
    width: 40%;
  }

  .right-column {
    width: 60%;
  }
  label
  {
    margin-left: 0.2em;
  }

</style>



<img width="40%" src="<?=Yii::app()->request->hostInfo.Yii::app()->baseUrl.'/images/inspection_header.png'?>" ></img>
<h1 style="text-align: center;">Request for permission to</h1>
<h1  style="text-align: center;">dispose of goods / conveyance</h1>

<p>This request relates to goods and/or a conveyance that must not be moved, dealt with or interfered with without authorisation, pursuant to paragraph 130(2)(a), 141(1)(b), 204(2)(a) or 216(1)(b) of the Biosecurity Act 2015.</p>
<p>I request permission under s 557 of the Act to deal with the goods and/or conveyance by disposing of them in the manner specified below for the purposes of the relevant provisions of the Act. </p>
<p>Return completed form to <a href="mailto:treatments@agriculture.gov.au">treatments@agriculture.gov.au.</a></p>
<table>
  <tr>
    <th>Description of goods and/or conveyance<label name="type" type="hidden" value ="<?=$type?>"></th>
    <th>Reference no. / Name</th>
  </tr>
  <tr>
    <td><label><?=empty($model->process->mdata['disf']['description']) ? "" : $model->process->mdata['disf']['description']?></label></td>
    <td><label><?=empty($model->process->mdata['disf']['reference']) ? "" : $model->process->mdata['disf']['reference']?></label></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
<table>
  <tr>
    <th colspan="3">Intended method of disposal of the goods and/or conveyance</br>(detail how, when and where the goods and/or conveyance is to be disposed of)</th>
  </tr>
  <tr>
    <td  colspan="3"><label><?=empty($model->process->mdata['disf']['disposal_method_how']) ? "" : $model->process->mdata['disf']['disposal_method_how']?>&nbsp;disposal at&nbsp;<?=empty($model->process->mdata['disf']['disposal_method_where']) ? "" : $model->process->mdata['disf']['disposal_method_where']?></label>
    </td>
  </tr>
  <tr>
    <td colspan="3"><label><?=empty($model->process->mdata['disf']['disposal_method_address']) ? "" : $model->process->mdata['disf']['disposal_method_address']?></label></td>
  </tr>
</table>
<table>
  <tr>
    <th colspan="3">Contact details of person requesting permission</th>
  </tr>
  <tr>
    <td>Street number and name: <label><?=empty($model->process->mdata['disf']['street_number']) ? "" : $model->process->mdata['disf']['street_number']?></label></td>
    <td colspan="2">Suburb/Town:<label><?=empty($model->process->mdata['disf']['suburb']) ? "" : $model->process->mdata['disf']['suburb']?></label></td>
  </tr>
</table>
<table style="margin-top:-1em;">
  <tr>
    <td style="border-top: 0px solid black;">State:<label><?=empty($model->process->mdata['disf']['state']) ? "" : $model->process->mdata['disf']['state']?></label></td>
    <td style="border-top: 0px solid black;">Postcode:<label><?=empty($model->process->mdata['disf']['postcode']) ? "" : $model->process->mdata['disf']['postcode']?></label></td>
    <td style="border-top: 0px solid black;">Mobile:<label><?=empty($model->process->mdata['disf']['mobile']) ? "" : $model->process->mdata['disf']['mobile']?></label></td>
  </tr>
  <tr>
    <td>Landline: <label><?=empty($model->process->mdata['disf']['landline']) ? "" : $model->process->mdata['disf']['landline']?></label></td>
    <td colspan="2">Email:<label><?=empty($model->process->mdata['disf']['email']) ? "" : $model->process->mdata['disf']['email']?></label></td>
  </tr>
</table>
<table>
  <tr>
    <th colspan="2">Person requesting permission</th>
  </tr>
  <tr>
    <td colspan="2">Signature: <label style="font-size: 2em;font-weight: bold;"><?=empty($model->process->mdata['disf']['signature']) ? "Michelle Wang" : $model->process->mdata['disf']['signature']?></label></td>
  </tr>
  <tr>
    <td>Printed Name:<label><?=empty($model->process->mdata['disf']['printed_name']) ? "Michelle Wang" : $model->process->mdata['disf']['printed_name']?></label></td>
    <td><label><?=empty($model->process->mdata['disf']['date']) ? "" : $model->process->mdata['disf']['date']?></label></td>
  </tr>
</table>
</div>

<h2 style="color:rgb(128,128,128);">Note</h2>
<p style="color:rgb(128,128,128);">
A permission granted under s 557 of the Act authorises a person to engage in specified conduct for the purposes of a provision of the Act that would otherwise prevent that conduct. Permission under s 557 does not authorise or enable conduct that would otherwise be prohibited by law. It is your responsibility to ensure that you are otherwise legally able to dispose of the goods or conveyance in the proposed manner.
</p>