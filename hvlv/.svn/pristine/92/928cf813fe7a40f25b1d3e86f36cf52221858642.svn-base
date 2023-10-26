<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class QCAjaxButtonColumn extends CButtonColumn
{
        protected function initDefaultButtons()
        {
                parent::initDefaultButtons();

                if(Yii::app()->request->enableCsrfValidation)
                {
                      $csrfTokenName = Yii::app()->request->csrfTokenName;
                      $csrfToken = Yii::app()->request->csrfToken;
                      $csrf = "\n\t\tdata:{ '$csrfTokenName':'$csrfToken' },";
                }
                else
                        $csrf = '';

                foreach($this->buttons as $id=>$button)
                {
                    if( ($id != 'view') && ($id != 'update') && ($id != 'delete') )
                    {
                        // not default buttons ( user defined )
                        if( isset($button['ajax']) && ($button['ajax']) )
                       {

                           $this->buttons[$id]['click']=<<<EOD
function() {
        $.fn.yiiGridView.update('{$this->grid->id}', {
                type:'POST',
                url:$(this).attr('href'),$csrf
                success:function() {
                        $.fn.yiiGridView.update('{$this->grid->id}');
                }
        });
        return false;
}
EOD;

                        }
                    }
                }
        }

}