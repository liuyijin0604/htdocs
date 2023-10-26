<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 10/05/2016
 * Time: 4:38 PM
 */


/**
 * Cron to send UBM(Underbond Movement) to ICS(Integrated Cargo System) on schedule by their API
 * Class UbmCommand
 */
class UbmCommand extends CConsoleCommand {

    private $debug = true;

    public function run($args) {

        if(!empty($args[0]) && method_exists($this, $args[0])){
            $this->{$args[0]}();
        }
    }

    /**
     * sync flight information for UBM message
     */
    private function syncubm(){

        $pid = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'ubm_sync.pid';
        if ( is_file($pid) && filectime($pid) > time() - 600 ) return false;
        file_put_contents($pid, '1');

        $this->_sendUbm();

        // delete temporary file , for next time running again
        unlink($pid);

    }

    /**
     * sync flight information for UBM message
     */
    private function _sendUbm(){

        // get ubm message from message queue
        $rs = Edimsg::model()->findAll('status = 18'); // get all schedule UBM message
        $this->echo_debug('=== Found '.sizeof($rs)." UBM message to update flight info. ===");


        // call flight tracking logic
        include(Yii::app()->basePath.DIRECTORY_SEPARATOR.'commands/trackingCommand.php');
        $tracking = new trackingCommand('ubm', null);
        foreach($rs as $r){
            $d = $tracking->flight($r->mdata['flight'], $r->mdata['etd']);
            // $d[7] flight status
            // $d[5] flight arrival time
            if(!empty($d)){
                if ( $this->canSendUbm($d[7],$d[5]) ) {
                    $r->status = 20 ; // push to sending queue
                    $r->save();

                    // set underbond movement sent flag for the console
                    $model=ImcoConsol::model()->findByPk($r->fid);
                    if( !empty($model) ) {
                        $model->mdata['ubmsent'] = 1;
                        $model->update(['meta']);
                    }
                }
            }
        }

        $this->echo_debug('=== ALL DONE ===');
    }

    private function canSendUbm($flightStatus,$arriveTime){
        $bCanSendUbm = false;
        $sArriveTime = strtotime($arriveTime);
        switch ( $flightStatus ) {
            case 2 :
            {
                // we set arrived , so that we can send UBM message
                // get real arrived time
                $laterTime = $sArriveTime;
                if ( time() > $laterTime ) {
                    $bCanSendUbm = true;
                }
            }
                break;
            case 0 :
            {
                // flight is on the schedule
                // we check to see if flight will arrival at two hours late
                // if yes, we can send UBM in advance now
                $advanceTime = $sArriveTime - 2 * 60 * 60;
                if ( time() > $advanceTime ) {
                    $bCanSendUbm = true;
                }
            }
                break;
        }

        return $bCanSendUbm;
    }

    /**
     * @param $l
     */
    private function log($msg){
        $tmp = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR;
        file_put_contents($tmp.'ubm_sync.log', date('Y-m-d H:i:s') . ' ' . $msg ."\n", FILE_APPEND);
    }

    /**
     * echo debug information
     * @param $str
     */
    private function echo_debug($str){
        if ( $this->debug )  echo $str . "\n";
    }

}
