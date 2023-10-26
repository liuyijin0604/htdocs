<h1>ChatGPT</h1>
<div class="form">    
    <div class="container">
        <div class="row">
            <textarea rows="25" cols="80" id="result" style="font-size: 1.5em" disabled><?= $data ?></textarea>
        </div>
    </div>
    <form id="chat_gpt_form">
        <div class="row">
            <div class="row rowcol">
                <?php echo CHtml::label('Question', 'send_message'); ?>
                <textarea rows="5" cols="80" id="question" name="question" placeholder="send a message to ChatGPT" ></textarea>
            </div>
        </div>
        <div class="row">
            <button type="submit" id="submit">submit</button>
        </div>
    </form>
</div>
<script type="text/javascript">
    $(function() {
        var tab = $('#<?=$_GET["tabid"];?>');
        var panel = tab.data('panel');

        $('#chat_gpt_form', panel).submit(function(event) {
            $('#result', panel).html($('#result', panel).html()+"Your Question: &#13;&#10;  "+$('#question', panel).val()+"&#13;&#10; &#13;&#10;");
            var formData = {
                question: $('#question', panel).val(),
                // result: $('#result').html()
            };
            $('#question', panel).val("");

            $.ajax({
                type: "POST",
                url: '<?= Yii::app()->createUrl('app/chatGPT'); ?>',
                data: formData,
                encode: true,
                success: function(data) {
                    //console.log(data);
                    // $('#result').html(data);
                    $('#result', panel).html($('#result', panel).html()+"ChatGPT: &#13;&#10;  "+data+"&#13;&#10; &#13;&#10;");
                }
            });

            event.preventDefault();
        });

        $("#chat_gpt_form", panel).keyup(function(event) {
            if (event.keyCode === 13) {
                $("#submit", panel).click();
            }
        });
    });
</script>