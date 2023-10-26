<?php
class MessageService extends Service
{
    public static function createNewMessage($toId,$msgs,$fromId=3514,$type=0,$status=0)
    {
        $model = new Message;
        $model->attributes = $_POST['Message'];
        $model->from_id = $fromId;
        $model->to_id = $toId;
		$model->type = $type;
		$model->msg = self::getMessage($msgs);
        $model->status = $status;
		$model->time = date('Y-m-d h:i:s',time());
        $model->save();

        return true;
    }

    public static function getMessage($msgs){
        $result = "";
        if(is_array($msgs)){
            foreach($msgs as $msg){
                $result .= $msg."\n";
            }
        }else{
            $result = $msgs;
        }
        echo $result;
        return $result;
    }
}
