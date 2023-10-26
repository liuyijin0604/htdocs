<h1><?=$this->t('Import Products');?></h1>
<div id="impro-tabs">
  <ul>
  <li><a href="#<?=$_GET["tabid"];?>-price">Price List</a></li>
  <li><a href="#<?=$_GET["tabid"];?>-spec">Product Spec</a></li>
  </ul>
  <div id="<?=$_GET["tabid"];?>-price" class="pane">
	<form id="form1" name="form1" class="ifrm-form" enctype="multipart/form-data" method="post" action="import/prodprice" target="<?=$_GET["tabid"];?>_ifrm">
	  <label for="retailer">Retailer:</label>
	 <?php echo CHtml::dropDownList('ret', '', Org::getRetailers(), array('empty' => $this->t('Select One'), 'id'=> "retailer")); ?>
	 <br />
	  <label for="import_file">Excel File: </label>
	  <input type="file" name="import" id="import_file" /> &nbsp;
	  <input type="submit" value="Upload" />
	  <p><small>.xls or .xlsx file supported</small></p>
	</form>
  </div>
  <div id="<?=$_GET["tabid"];?>-spec" class="pane">
	<form id="form1" name="form1" class="ifrm-form" enctype="multipart/form-data" method="post" action="import/prodspec" target="<?=$_GET["tabid"];?>_ifrm">
	  <label for="import_file">Excel File: </label>
	  <input type="file" name="import" id="import_file" /> &nbsp;
	  <input type="submit" value="Upload" />
	  <p><small>.xls or .xlsx file supported</small></p>
	</form>
  </div>
</div>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	$('#impro-tabs', tab.data('panel')).tabs({active: <?php echo empty($_GET['actab'])? 0 : $_GET['actab']; ?>});
});
</script>
<iframe name="<?=$_GET["tabid"];?>_ifrm" id="<?=$_GET["tabid"];?>_ifrm" src="" width="800" height="400" border="0" style="border:1px #ccc solid; margin-top:10px;">
</iframe>