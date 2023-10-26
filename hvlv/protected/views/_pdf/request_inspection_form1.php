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
		background-color: rgb(88, 88, 88);
	}

	th,
	td {
		border: 1px solid black;
		padding: 0px;
		font-size: 1.2em;
	}

	.left-column {
		width: 40%;
	}

	.right-column {
		width: 60%;
	}
</style>
<img width="40%" src="<?=Yii::app()->request->hostInfo.Yii::app()->baseUrl.'/images/inspection_header.png'?>" />
<table>
	<th colspan="2" style="font-size: 2em;">Request for Inspection Form</th>
</table>

<table>
	<tr>
		<th colspan="2">Consignment details<input name="type" type="hidden" value="<?=$type?>" /></th>
	</tr>
	<tr>
		<td class="left-column">Quarantine entry number/s:</td>
		<td class="right-column"><label><?=@$model->process->mdata['insf']['quarantine_entry_number']?></label></td>
	</tr>
	<tr>
		<td class="left-column">Airway bill (if nil quarantine entry number):</td>
		<td class="right-column"><label><?=@$model->process->mdata['insf']['airway_bill']?></label></td>
	</tr>
</table>

<table>
	<tr>
		<th colspan="2">Booking agent</th>
	</tr>
	<tr>
		<td class="left-column">Contact name:</td>
		<td class="right-column"><label><?=empty($model->process->mdata['insf']['contact_name_1'])?$setting["agent"][0]:$model->process->mdata['insf']['contact_name_1']?></label></td>
	</tr>
	<tr>
		<td class="left-column">Phone number:</td>
		<td class="right-column"><label><?=empty($model->process->mdata['insf']['phone_number_1'])?$setting["agent"][1]:$model->process->mdata['insf']['phone_number_1']?></label></td>
	</tr>
	<tr>
		<td class="left-column">Email:</td>
		<td class="right-column"><label><?=empty($model->process->mdata['insf']['email'])?$setting["agent"][2]:$model->process->mdata['insf']['email']?></label></td>
	</tr>
</table>

<table>
	<tr>
		<th colspan="2">Location</th>
	</tr>
	<tr>
		<td class="left-column">Change of location:</td>
		<td class="right-column">&#x25A1;Change *charges may apply</td>
	</tr>
	<tr>
		<td class="left-column">Approved Arrangement (Name & AA number):</td>
		<td class="right-column"><label><?=empty($model->process->mdata['insf']['approved_arrangement'])?$setting["location"][0]:$model->process->mdata['insf']['approved_arrangement']?></label></td>
	</tr>
	<tr>
		<td class="left-column">Address of premise:</td>
		<td class="right-column"><label><?=empty($model->process->mdata['insf']['address_premise'])?$setting["location"][1]:$model->process->mdata['insf']['address_premise']?></label></td>
	</tr>
	<tr>
		<td class="left-column">Opening hours:</td>
		<td class="right-column"><label><?=empty($model->process->mdata['insf']['opening_hours'])?$setting["location"][2]:$model->process->mdata['insf']['opening_hours']?></label></td>
	</tr>
	<tr>
		<td class="left-column">Contact name:</td>
		<td class="right-column"><label><?=empty($model->process->mdata['insf']['contact_name_2'])?$setting["location"][3]:$model->process->mdata['insf']['contact_name_2']?></label></td>
	</tr>
	<tr>
		<td class="left-column">Phone number:</td>
		<td class="right-column"><label><?=empty($model->process->mdata['insf']['phone_number_2'])?$setting["location"][4]:$model->process->mdata['insf']['phone_number_2']?></label></td>
	</tr>
</table>

<table>
	<tr>
		<th colspan="2">Additional information</th>
	</tr>
	<tr>
		<td class="left-column">Consignment Type (if applicable):</td>
		<td class="right-column">&#x25A1;Flat Rack &#x25A1;Open top container &#x25A1;Isotank</td>
	</tr>
	<tr>
		<td class="left-column">Hazardous goods?</td>
		<td class="right-column">&#x25A1;Yes &#x25A0;No</td>
	</tr>
</table>

<table>
	<tr>
		<th colspan="2">Inspection type</th>
		<th></th>
	</tr>
	<tr>
		<td class="left-column">Type of Inspection (Unpack, IFIP, Dual, CCV, Produce, etc):</td>
		<td class="right-column"><label><?=empty($model->process->mdata['insf']['inspection_type'])?'':$model->process->mdata['insf']['inspection_type']?></label></td>
	</tr>
	<tr>
		<td class="left-column">Number of officers:<br />Inspection duration requested:</td>
		<td class="right-column">1<br />15 Minutes</td>
	</tr>
</table>

<table>
	<tr>
		<th colspan="2">Booking request</th>
		<th></th>
	</tr>
	<tr>
		<td class="left-column">READY NOW:</td>
		<td class="right-column">&#x25A0;Yes &#x25A1;No</td>
	</tr>
	<tr>
		<td class="left-column">If contacted by a booking officer, are your goods ready to be inspected?</td>
		<td class="right-column">
			*To be eligible to be placed on the ‘ready now’ list your consignment must be ready to be inspected at any time from the submission of this form.<br />
			**Please note: if your consignment is not ready when the department contact you, charges may apply.
		</td>
	</tr>
</table>

<p>OR</p>

<table>
  <tr>
    <td class="left-column">Date goods are available from:</td>
    <td class="right-column"></td>
  </tr>
  <tr>
    <td class="left-column">Requested date for inspection:</td>
    <td class="right-column"></td>
  </tr>
  <tr>
    <td class="left-column">Requested time for inspection:</td>
    <td class="right-column">
      <p>&#x25A0;Anytime &#x25A1;AM &#x25A1;PM</p>
      <p>We will endeavor to allocate inspections as requested, however, where this is not possible, you will be allocated the next available appointment.</p>
    </td>
  </tr>
  <tr>
    <th colspan="2">Comments</th>
  </tr>
  <tr>
    <td colspan="2">Eg: overtime request, quantity of goods, etc...</td>
  </tr>
</table>