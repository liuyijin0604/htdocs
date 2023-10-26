<div id="quickCreateModal">
    <label for="location">Warehouse:</label>
    <select id="location" >
        <option value="" disabled selected>Select One</option>
        <?php foreach (Org::dptList() as $value => $label): ?>
            <option value="<?= $value ?>"><?= $label ?></option>
        <?php endforeach; ?>
    </select>
    
    <label for="accountCount" >Count:</label>
    <input type="text" id="accountCount">
    
    <button id="submitQuickCreate" style="cursor: pointer; background-color: lightblue;">Submit</button>
</div>

<script>
    $(document).ready(function() {
        $("#submitQuickCreate").click(function() {
            var location = $("#location").val();
            var accountCount = $("#accountCount").val();
            console.log(location);
           console.log(accountCount);
            $.ajax({
                type: "POST",
                url: "<?=$this->createUrl('user/quickcreate');?>",
                data: { location: location, accountCount: accountCount },
                success: function(response) {
                    
                    $("#quickCreateModal").fadeOut();
                },
                error: function(error) {
                }
            });
        });
    });
</script>