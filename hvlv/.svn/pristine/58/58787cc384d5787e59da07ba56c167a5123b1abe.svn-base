<style>
    .odd {
        background: #E5F1F4;
    }

    .even {
        background: #F8F8F8;
    }

    table {
        background: white;
        width: 100%;
        border: 1px #D0E3EF solid;
    }

    th {
        color: white;
        background-color: rgb(107, 157, 207);
        text-align: center;
    }
    
    .display_none{
        display: none;
    }
</style>


<div class="content-padded ">
    <h3>Split Sorting</h3>
    <div class="split_sorting_form">
        <form action="" method="post" data-bit="1">
            <div id="div_task">
                <div class="input-addon" style="width: 100%;">
                    <input class="task_number barcode required" type="search" placeholder="task number (for T000123 , input 123)" name="task_number" style="width: 100%;" />
                </div>
                <button id="btn_task" class="btn btn-primary btn-block" onclick="funcSearchTask()">OK</button>
            </div>
            <div id="div_split_sorting" class="display_none">
                <h3 id="h_task_number">h_task_number</h3>
                <h3 id="h_hbn">h_hbn</h3>
                <table>
                    <thead>
                        <tr>
                            <th>from</th>
                            <th>to</th>
                            <th>pieces</th>
                            <th>location</th>
                            <th>type</th>
                        </tr>
                    </thead>
                    <tbody id="table_records">

                    </tbody>
                </table>
                </br>
                <div class="input-addon">
                    <input class="pallet_number barcode required" type="search" placeholder="pallet_number" name="pallet_number" />
                    <span>
                        <div class="toggle lock_pallet_number">
                            <div class="toggle-handle"></div>
                        </div>
                    </span>
                </div>
                <div class="input-addon">
                    <input class="split_number barcode required" type="search" placeholder="split_number Barcode" name="split_number" />
                    <span>
                        <div class="toggle kloc">
                            <div class="toggle-handle"></div>
                        </div>
                    </span>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Save</button>

            </div>

        </form>

    </div>
    <div class="undo">
    </div>
    <div class="res">
    </div>
</div>

<script type="text/javascript">
    function funcSearchTask() {
        strTaskNumber = 'task_number=' + $('input.task_number').val();
        htmlobj = $.ajax({
            url: "/wma/job/splitSortingTask",
            data: strTaskNumber,
            async: false
        });
        objRes = JSON.parse(htmlobj.responseText);
        if (objRes.done) {
            $('#table_records').html(objRes.records);
            $('#div_task').attr('class','display_none');
            $('#div_split_sorting').attr('class','');
            $('#h_task_number').html(objRes.task_number);
            $('#h_hbn').html(objRes.hbn);
        }
        event.preventDefault();
    }

    $(function() {


        $('input.split_number').on('keydown', function(e) {
            if (e.which == 13 && $('input.pallet_number').val() == '') {
                $('input.pallet_number').focus();
                return false;
            }
        }).on('afterBarcode', function() {
            if ($('input.pallet_number').val() == '') {
                $('input.pallet_number').focus();
                return false;
            } else {
                $('.split_sorting_form form').submit();
            }
        });

        $('.split_sorting_form form').on('success', function(e, r) {
            $('.res').html(r.data);
            $('#table_records').html(r.records);
            $('input.pallet_number').focus();
            if (!$('.lock_pallet_number').hasClass('active')) {
                $('input[name="pallet_number"]').val('');
            }
            if (!$('.kloc').hasClass('active')) {
                $('input[name="split_number"]').val('');
            }
            var audio = new Audio();
            audio.src = 'https://os.toplogistics.com.au/site/voice/confirmed.mp3';
            audio.play();
        }).on('error', function(e, r) {
            $('.res').html(r.data);
            var audio = new Audio();
            audio.src = 'https://os.toplogistics.com.au/site/voice/beep_err.mp3';
            audio.play();
        });
    });
</script>