<?php

class ExcelController extends Controller {

    protected $nonAjax = ['create'];

    public function actionCreate() {

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (!empty($_POST)) {

            $err = array();
            $data = array();
            if (is_uploaded_file($_FILES['manifest']['tmp_name'])) {
                $xls = new oExcel;
                $xls->supported($_FILES['manifest']['name']);
                $xls->load($_FILES['manifest']['tmp_name']);
                foreach($xls->xls->getAllSheets() as $n=>$sheet){
                    $xls->goSheet($n);
                    if($sheet->getTitle() == '考勤记录') break;
                }
                // $xls->goSheet(2);
                $data = $xls->getAll();
                $output = [];

                $output[1]['A'] = 'DATE';
                $output[1]['B'] = 'NAME';
                $output[1]['C'] = 'DEPARTMENT';
                $output[1]['D'] = 'IN-TIME';
                $output[1]['E'] = 'OUT-TIME';
                $output[1]['F'] = 'WORKING HOURS';
                $date = $data[3][3];
                $date = explode('~', $date);
                $startDate = explode('-', $date[0]);
                $startDate[2]=$data[4][1];
                $endDate = explode('-', $date[1]);
                $days = $data[4];
                $daysCount = 0;
                foreach ($days as $day) {
                    if (!empty($day)) {
                        $daysCount++;
                    }
                }

                $rowCount = sizeof($data);
                $staffNumber = ($rowCount - 4) / 2; //count the staff Number 
//get the time       

                function getTime($cell_data) {
                    if (preg_match_all('/(\d{2}:\d{2})/', $cell_data, $matches_out)) {
                        $index = count($matches_out[0]);
                        if ($index === 0) {
                            return ['', ''];
                        } elseif ($index === 1) {
                            return[$matches_out[0][0], ''];
                        } else {
                            return[$matches_out[0][0], $matches_out[0][$index - 1]];
                        }
                    }
                }

                function calculateTime($startTime, $endTime) {
                    $to_time = strtotime($startTime);
                    $from_time = strtotime($endTime);
                    return round(abs($to_time - $from_time) / 60 / 60, 2);
                }

                $daysEnd = (!empty($_POST['end_date']) && $_POST['end_date'] <= $daysCount+$startDate[2]-1) ? $_POST['end_date'] : ($daysCount+$startDate[2]-1);
                $s = (!empty($_POST['start_date']) && $_POST['start_date'] <= $daysEnd&&$_POST['start_date']>=$startDate[2]) ? $_POST['start_date'] :$startDate[2];

                for ($n = 1; $n <= $staffNumber; $n++) {
                    for ($m = $s; $m <= $daysEnd; $m++) {
                        $tempDate = date('Y/m/d', strtotime($startDate[0] . '/' . $startDate[1] . '/1' . ' + ' . ($m - 1) . ' days'));
                        // $tempDate = $startDate[0] . '/' . $startDate[1] . '/' . $m;
                        $arr['A'] = $tempDate;
                        $arr['B'] = $data[$n * 2 + 3][11];
                        $arr['C'] = $data[$n * 2 + 3][21];
                        $startTime = getTime($data[$n * 2 + 4][$m-$startDate[2]+1])[0];
                        $endTime = getTime($data[$n * 2 + 4][$m-$startDate[2]+1])[1];
                        $arr['D'] = $startTime;
                        $arr['E'] = $endTime;
                        if (!empty($endTime)) {
                            $arr['F'] = calculateTime($startTime, $endTime);
                        } else {
                            $arr['F'] = '';
                        }
                        array_push($output, $arr);
                    }
                }

                $outputExcel = new oExcel();
                $outputFile = 'output.xls';
                $outputExcel->supported($outputFile);
                for ($i = 1; $i <= sizeof($output); $i++) {
                    for ($j = 'A'; $j <= 'F'; $j++) {
                        $outputExcel->setCell($j . $i, $output[$i][$j]); //set the cell
                    }
                }
            }
            $outputExcel->output($outputFile);
        }
        $this->render('create', array());
    }

}
