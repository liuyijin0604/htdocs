<style>
    table {
        font-family: arial, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    td,
    th {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
    }

    tr:nth-child(even) {
        background-color: #dddddd;
    }
</style>
<h1>SMS Record</h1>
<br>
<div id="form_container">
    <form id="sms_record_form">
        <label for="ref">Ref</label>
        <input type="text" id="ref" name="ref" />
        <br>
        <button type="button" id="submit_btn">Search</button>
    </form>
</div>

<div id="result_container">
    <h4 id="err_msg"></h4>
    <table id="result_table" style="display: none;">
        
    </table>
</div>

<script type="text/javascript">
    const status = {
        0: "Scheduled",
        1: "Received",
        2: "Sent",
        3: "Fail"
    };
    $(function() {
        $('#submit_btn').on('click', function() {
            $.ajax({
                method: 'POST',
                url: '<?= Yii::app()->createUrl("cargoProcess/checkSMSRecord"); ?>',
                dataType: 'json',
                data: {
                    'ref': $('input#ref').val(),
                },
                success: function(res) {
                    // Clear previous contents
                    $('h4#err_msg').empty();
                    $('#result_table').empty();

                    let r = JSON.stringify(res);
                    r = jQuery.parseJSON(r);
                    console.log(r);
                    if (r.count_messages == 0) {
                        $('h4#err_msg').text('No result found.');
                    } else {
                        let strHtml = '<tr><th>Phone</th><th>Time Sent</th><th>Status</th><th>Message</th></tr>';
                        for (let i = 0; i < r.count_messages; i++) {
                            //debugger;
                            strHtml += '<tr><td>' + r.notices[i].phone + '</td><td>' + r.notices[i].sent_time + '</td><td>' + status[r.notices[i].is_processed] + '</td><td>' + r.notices[i].message + '</td></tr>';
                        }
                        $('#result_table').append(strHtml);
                        $('#result_table').show();
                    }
                }
            })
        })
    })
</script>