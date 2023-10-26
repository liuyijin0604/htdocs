<?php 
class BwtrunkService extends Service
{
    private $api;
    private $model;

    public function __construct()
    {
        $this->api = new BwtrunkAPI();
        $this->model = new Bwtrunk();
    }

    public function updateData()
    {
        $data = $this->api->getData();
        // $transaction=Yii::app()->db->beginTransaction();
        // try{
            if (!empty($data)) {
            foreach ($data as $item) {
            if(preg_match('/(\d{3})[\s\-]*(\d{4})[\s\-]*(\d{4})/',$item['MAWB_number'],$m)){
                $mawb = $m[1]."-".$m[2].$m[3];
                if(empty($item['CTO_start']))
                {
                    $model = $this->model->find("MAWB_number = :MAWB_number and CTO_start is null",array('MAWB_number' => $mawb));
                }else
                {
                    $model = $this->model->findByAttributes(array('MAWB_number' => $mawb,'CTO_start'=>$item['CTO_start']));
                    if(empty($model))
                    {
                        $model = $this->model->find("MAWB_number = :mawbNumber and CTO_start is null",[":mawbNumber"=>$mawb]);
                    }
                }
                if (empty($model)) {
                        $model = new Bwtrunk();
                        $model->MAWB_number = $mawb;
                        $model->pieces_pick_up = $item['pieces_pick_up'];
                        $model->CTO_start = $item['CTO_start'];
                        $model->CTO_finish = $item['CTO_finish'];
                        $model->Client_start = $item['Client_start'];
                        $model->Client_finish = $item['Client_finish'];
                        $model->assigned = @$item['Assigned'];
                        $model->save();
                    }else if ($model->CTO_start==$item['CTO_start']&&$model->CTO_finish==$item['CTO_finish']&&$model->Client_start==$item['Client_start']&&$model->Client_finish==$item['Client_finish']&&$model->assigned==$item['Assigned']) {
                        continue;
                    } else {
                        $model->pieces_pick_up = $item['pieces_pick_up'];
                        $model->CTO_start = $item['CTO_start'];
                        $model->CTO_finish = $item['CTO_finish'];
                        $model->Client_start = $item['Client_start'];
                        $model->Client_finish = $item['Client_finish'];
                        $model->assigned = @$item['Assigned'];
                        $model->save();
                    }

                 }
              }
            }

    }
}