<div>
    <h1>Import Excel</h1>
    <br/>
    <br/>
    <label for="pickup_excel">Excel - <small>.csv/.xls/.xlsx File</small> (<a href="../template/marketing_address.xlsx" target="_blank">Template file</a>)</label>
    <br/>
    <input type="file" name="excel" id="excel" value="" placeholder="please select excel template">
    <br/>
    <br/>
    <input type="button" onclick="funcImportExcel();" value="Import" name="" >
    <br/>
</div>

<script type="text/javascript">
function funcImportExcel(){
    setTimeout(() => {
            var listData = new FormData();
            listData.append("excel",$("#excel")[0].files[0]);

            htmlobj = $.ajax({
                type:"POST",
                url: "/marketingTool/importExcelAddress",
                data: listData,
                contentType: false,
                processData: false,
                async: false
            });
            obj = JSON.parse(htmlobj.responseText);
            if(obj.isSuccess){
                $('#jqmw_<?=$_GET["tabid"];?>').jqmHide();
                $('#<?=$_GET["tabid"]?>_address-grid').yiiGridView('update');
                myApp.notice('success', 5000);
            }
            else{
                alert(obj.strMessage);
            }
        }, 0);
}
</script>