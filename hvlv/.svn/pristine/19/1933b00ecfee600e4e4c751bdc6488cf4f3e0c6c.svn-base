<?php

class CheckController extends Controller {
    
    /**
     * @return array action filters
     */

    public function actionIndex() {
        $this->render("site/index");
    }

    public function actionCheck() {
        $this->render("check");
    }

    public function actionDetail() {
        $taskId = $_GET["taskid"];
        $temp_taskId = $taskId;
        $taskId = (int)substr($taskId, 1);

        $task = WmsTask::model()->findByPk($taskId);
        $states = array("Scheduled");
        $types = array("Pick Pallet", "Pick Carton", "Pick Unit");

        if ($task && in_array(WmsTask::$states[(int)$task->status], $states) && in_array(WmsTask::$types[(int)$task->type], $types) && $task->is_request) {
            $products = array();
            $note = "";
            $taskItems = WmsTaskItem::model()->findAll("task_id = :task_id", array(":task_id" => $taskId));
            foreach ($taskItems as $taskItem) {
                $meta = json_decode($taskItem->meta, true);

                $stock = WmsStock::model()->findByPk($meta["si"]);

                $product = WmsProd::model()->findByPk($stock->prod_id);
                $product = json_decode(json_encode($product->attributes), true);

                $product["pq"] = isset($meta["pq"]) && $meta["pq"] ? $meta["pq"] : 0;
                $product["cq"] = isset($meta["cq"]) && $meta["cq"] ? $meta["cq"] : 0;
                $product["uq"] = isset($meta["uq"]) && $meta["uq"] ? $meta["uq"] : 0;
                $product["note"] = isset($meta["nt"]) && $meta["nt"] ? $meta["nt"] : "无特殊要求";
                $product["taskid"] = $temp_taskId;

                $products[$product["name"]] = $product;
            }

            $this->renderPartial("taskdetail", array("result" => true, "task" => $task, "products" => $products));
        } else {
            $this->renderPartial("taskdetail", array("result" => false, "text" => "订单号出错了"));
        }

        // $stockLedgers = WmsStockLedger::model()->findAll("ti_id = :ti_id", array(":ti_id" => $taskItem->id));
        // $tt = new WmsTaskItem();
        // $tt->id = $taskItem->id;
        // $tt->task = $task;
        // $tt->mdata = json_decode($taskItem->meta, true);
        // $tt->stockLedgers = $stockLedgers;
        // Yii::log(json_encode($tt->toStock()), "warning", "trace");
    }

    public function actionComplete() {
        $taskId = $_POST["taskid"];

        $result = array("result" => true, "text" => "此订单配货完成");
        echo json_encode($result);
        Yii::app()->end();
    }

}