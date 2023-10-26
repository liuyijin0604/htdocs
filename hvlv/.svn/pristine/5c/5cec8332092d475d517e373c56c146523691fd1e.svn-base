<?php

?>

<!DOCTYPE html>
<html>

<head>
    <title>海外仓预约系统</title>
    <style>
        #book_schedule_tabs {
            width: 140%;
            margin-left: -16%;
            text-align: center;
        }
    </style>
</head>

<body>
    <center>
        <h1>海外仓预约系统</h1>
    </center> 
    <div id="book_schedule_tabs">
        <ul class="nav nav-tabs">
            <?php
            $tabs = [];
            $tabs[] = ['need_schedule', $this->t('Waiting For Booking'), true];
            $tabs[] = ['waiting_for_delivery', $this->t('Waiting For Delivery'), true];
            

            foreach ($tabs as $key => $tab) {
                $href = strpos($tab[0], '/') === false ? $this->createUrl('shipment/processPage', array('tab' => $tab[0], "tabid" => "tab" . $key)) : $tab[0];
                /* if ($key == 0) {
                    echo '<li><a href="' . $href . '" class="active">' . $tab[1] . '&nbsp;<span class="badge" style="background-color: red;">'. $rejectedCount .'</span></a></li>';
                } else {
                    echo '<li><a href="' . $href . '" class="active">' . $tab[1] . '</a></li>';
                } */
                echo '<li><a href="' . $href . '" class="active">' . $tab[1] . '</a></li>';
            }
            ?>
        </ul>
    </div>
    <script type="text/javascript">
        $(function() {
            $('#book_schedule_tabs').tabs({
                active: <?php echo empty($_GET['actab']) ? 0 : $_GET['actab']; ?>,
                load: function(event, ui) {
                    // posApp.ajaxifyForm(this);
                }
            });
        });
    </script>
</body>

</html>