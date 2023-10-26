<?php

class PluploadWidget extends CWidget {

    const ASSETS_DIR_NAME       = 'assets';
    const PLUPLOAD_FILE_NAME    = 'plupload.full.min.js';
    const JQUERYQUEUE_FILE_NAME = 'jquery.plupload.queue/jquery.plupload.queue.min.js';
    const JQUERYUI_FILE_NAME    = 'jquery.ui.plupload/jquery.ui.plupload.min.js';
    const FLASH_FILE_NAME       = 'Moxie.swf';
    const SILVERLIGHT_FILE_NAME = 'Moxie.xap';
    const DEFAULT_RUNTIMES      = 'html5,flash';
    const PUPLOAD_CSS_PATH      = 'jquery.plupload.queue/css/jquery.plupload.queue.css';
    const JQUERYUI_CSS_PATH     = 'jquery.ui.plupload/css/jquery.ui.plupload.css';
    const I18N_DIR_NAME         = 'i18n';
	
	public $isCustom;
	
    public $config = array('file_list_height' => 60, 'visible_header' => false, 'custom' => false );

    public $callbacks = array();

    public function init() {
        $css = "";

        $localPath = dirname(__FILE__) . "/" . self::ASSETS_DIR_NAME;
        $publicPath = Yii::app()->getAssetManager()->publish($localPath);

        if(!isset($this->config['flash_swf_url'])) {
            $flashUrl = $publicPath . "/" . self::FLASH_FILE_NAME;
            $this->config['flash_swf_url'] = $flashUrl;
        }
		
		if(isset($this->config['custom'])){
			if($this->config['custom'] === true) $this->isCustom = true;
			unset($this->config['custom']);
		}

        if(!isset($this->config['silverlight_xap_url'])) {            
            $silverLightUrl = $publicPath . "/" . self::SILVERLIGHT_FILE_NAME;
            $this->config['silverlight_xap_url'] = $silverLightUrl;
        }

        if(!isset($this->config['runtimes'])) {
            $this->config['runtimes'] = self::DEFAULT_RUNTIMES;
        }

        if (!isset($this->config['no_js'])) {
            $pluploadPath = $publicPath . "/" . self::PLUPLOAD_FILE_NAME;
            Yii::app()->clientScript->registerScriptFile($pluploadPath, CClientScript::POS_END);
        }
		
        $use_jquery_ui = (isset($this->config['jquery_ui']) && $this->config['jquery_ui']);
        if($use_jquery_ui) {
            if (!isset($this->config['no_js'])) {
                $jQueryUIPath = $publicPath . "/" . self::JQUERYUI_FILE_NAME;
                Yii::app()->clientScript->registerScriptFile($jQueryUIPath, CClientScript::POS_END);
            }

            $jQueryUICssPath = $publicPath . "/" . self::JQUERYUI_CSS_PATH;
            Yii::app()->clientScript->registerCssFile($jQueryUICssPath);
        } else {
            if (!isset($this->config['no_js'])) {
                $jQueryQueuePath = $publicPath . "/" . self::JQUERYQUEUE_FILE_NAME;
                Yii::app()->clientScript->registerScriptFile($jQueryQueuePath, CClientScript::POS_END);
            }

            $cssPath = $publicPath . "/" . self::PUPLOAD_CSS_PATH;
            Yii::app()->clientScript->registerCssFile($cssPath);
        }

        if(isset($this->config['language'])) {
            if(preg_match('/^en_/', $this->config['language'])) $this->config['language'] = 'en';
            Yii::app()->clientScript->registerScriptFile($publicPath . "/" . self::I18N_DIR_NAME . "/" . $this->config['language'] . ".js", CClientScript::POS_END);
            unset($this->config['language']);
        }

        $max_file_number = 0;
        if(isset($this->config['max_file_number'])) {
            $max_file_number = $this->config['max_file_number'];
            unset($this->config['max_file_number']);
        }

        $autostart = false;
        if(isset($this->config['autostart'])) {
            $autostart = $this->config['autostart'];
            unset($this->config['autostart']);
        }

        $reset_after_upload = false;
        if(isset($this->config['reset_after_upload'])) {
            $reset_after_upload = $this->config['reset_after_upload'];
            unset($this->config['reset_after_upload']);
        }

        $callback_total_queued = false;
        if(isset($this->config['callback_total_queued'])) {
            $callback_total_queued = $this->config['callback_total_queued'];
            unset($this->config['callback_total_queued']);
        }

        if(isset($this->config['file_list_height'])) {
            $file_list_height = $this->config['file_list_height'];
            if ($file_list_height < 20)
                $file_list_height = 20;
            if ($use_jquery_ui) {
                $css .= ".plupload_scroll { max-height:".$file_list_height."px; min-height:".$file_list_height."px; }\n".
                        ".plupload_scroll .plupload_filelist table { height:".$file_list_height."px; }\n".
                        ".plupload_droptext {line-height: ".$file_list_height."px;}\n";
            } else {
                $css .= ".plupload_scroll .plupload_filelist { height:".$file_list_height."px; }\n".
                        "li.plupload_droptext {line-height: ".($file_list_height-20)."px;}\n";
            }
            unset($this->config['file_list_height']);
        }

        if(isset($this->config['visible_header'])) {
            if (!$this->config['visible_header'])
                $css .= ".plupload_header { display:none; }\n";
            unset($this->config['visible_header']);
        }

        $fnUniqueId = str_replace(".", "", uniqid("", TRUE));

		if($this->isCustom){
			$this->config['container'] = $this->id;
			$this->config['browse_button'] = $this->id.'_pickfiles';
			$jsConfig = CJavaScript::jsonEncode($this->config);
			$jqueryScript = "function do_plupload_$fnUniqueId() {var uploader = new plupload.Uploader({$jsConfig}); uploader.bind('Init', function(up, params) { $('#".$this->id."').hide();}); uploader.init();";
		}else{
			$jsConfig = CJavaScript::jsonEncode($this->config);
			$jqueryScript = "function do_plupload_$fnUniqueId() {jQuery('#$this->id').pluploadQueue({$jsConfig}); var uploader = $('#$this->id').pluploadQueue(); ";
		}

        if ($max_file_number > 0 || $autostart) {
            $jqueryScript .= "uploader.bind('FilesAdded', function(up, files) {";
            if ($max_file_number > 0) {
                $jqueryScript .= "if (up.files.length > $max_file_number) up.splice($max_file_number, up.files.length-$max_file_number); ";
            }
            if ($autostart) {
                $jqueryScript .= "if(up.files.length > 0) uploader.start(); ";
            }
            $jqueryScript .= "}); ";
        }

        if (isset($this->callbacks) && is_array($this->callbacks)) {
            foreach ($this->callbacks as $bind => $function) {
                $jqueryScript .= "uploader.bind('$bind', $function); ";
            }
        }

        if ($reset_after_upload || $callback_total_queued !== false) {
            $jqueryScript .= "uploader.bind('FileUploaded', function(up, file, res) { ";
            if ($reset_after_upload) {
                $jqueryScript .= "if(up.total.queued == 0) do_plupload_$fnUniqueId(); ";
            }
            if ($callback_total_queued !== false) {
                $jqueryScript .= "var callback_total_queued = $callback_total_queued; callback_total_queued(up.total.queued); ";
            }
            $jqueryScript .= "}); ";
        }

        if ($callback_total_queued !== false) {
            $jqueryScript .= "uploader.bind('QueueChanged', function(up) { var callback_total_queued = $callback_total_queued; callback_total_queued(up.files.length); }); ";
        }

        if (isset($this->config['add_files_text'])) {
            $jqueryScript .= "setTimeout(\"jQuery('.plupload_add').html('".$this->config['add_files_text']."');\", 1000);";
        }

        if ($autostart) {
            $css .= ".plupload_start { display:none; }\n";
        }

        $jqueryScript .= "} ";

        $uniqueId = 'Yii.' . __CLASS__ . '#' . $this->id;
        Yii::app()->clientScript->registerScript($uniqueId.".end", stripcslashes($jqueryScript), CClientScript::POS_END);
        Yii::app()->clientScript->registerScript($uniqueId.".ready", "do_plupload_$fnUniqueId();", CClientScript::POS_READY);
        if (strlen($css) > 0)
            Yii::app()->clientScript->registerCss($uniqueId.".css", $css);
    }

    public function run(){
        echo "<div id=\"$this->id\">";
        echo "<p>".Yii::t('plupload', "Your browser doesn't have Flash, Silverlight or HTML5 support.")."</p>";
		if($this->isCustom){
			echo '<a class="plupload_pickfiles" id="'.$this->id.'_pickfiles" href=""></a>';
		}
		echo "</div>";
    }
}
?>
