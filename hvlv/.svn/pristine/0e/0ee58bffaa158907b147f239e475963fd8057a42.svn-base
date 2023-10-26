<!DOCTYPE html>
<head>

</head>
<body>
    <form id="command_form">
        <label for="command">Select Command</label> <br />
        <select id="command" name="command">
            <option value="cron">CronCommand</option>
            <option value="az">azCommand</option>
            <option value="ajf">ajfCommand</option>
            <option value="db">DBCommand</option>
            <option value="dxt">DxtCommand</option>
            <option value="emailPipe">EmailPipeCommand</option>
            <option value="fl">flCommand</option>
            <option value="gz">gzCommand</option>
            <option value="ics">icsCommand</option>
            <option value="iExp">iExpCommand</option>
            <option value="jr">jrCommand</option>
            <option value="jrf">jrfCommand</option>
            <option value="jyg">jygCommand</option>
            <option value="mq">MQCommand</option>
            <option value="pl">plCommand</option>
            <option value="ray">rayCommand</option>
            <option value="thread">ThreadCommand</option>
            <option value="tracking">trackingCommand</option>
            <option value="ubm">UbmCommand</option>
            <option value="xero">XeroCommand</option>
            <option value="yx">yxCommand</option>
        </select>
        <br />
        <label for="function">Function: </label> <br />
        <input type="text" id="funciton" name="function" /> <br />
        <label for="arguments">Arguments: </label><br />
        <textarea id="arguments" name="arguments" rows="5" cols="75" ></textarea>
        <br />
        <input type="submit" value="Run" />
    </form>

    <br />
    <h1>Result:</h1>
    <div class="container" id="result">
        <p id="command_result"></p>
    </div>

    <script type="text/javascript">
        $(function() {
            $('#command_form').on('submit', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                let formData = new FormData(this);

                $.ajax({
                    url: '<?=$this->createUrl('it/command')?>',
                    type: 'POST',
                    data: formData,
                    enctype: 'multipart/form-data',
                    cache: false,
                    contentType: false,
                    processData: false,

                    success: function(res) {
                        $('#command_result').html(res);
                    }
                })
            })
        })
    </script>
</body>