<h5>Goods information</h5>
<div>
    <table class="table table-striped" id="items">
        <thead>
            <tr>
                <th>No#</th>
                <th>Name</th>
                <th>Qty</th>
            </tr>
        </thead>
        <tbody>  
        </tbody>
    </table>
</div>
<script>
   $(function(){
       var pitems = <?=json_encode(empty($model->eitems)? '' : $model->eitems);?> || {};
       var tb = $('#items tbody');
       for(var i=0;i<pitems.g.length;i++){
           tb.append('<tr><td>'+(i+1)+'</td><td>'+pitems.g[i]+'</td><td>'+pitems.q[i]+'</td></tr>')          
        }
 }); 
</script>

