<?php
class TableController extends CController
{   



    public function actionIndex()
    {

        $model = new Product('search');
        $model->unsetAttributes();

        if (isset($_GET['Product'])) {
        $model->attributes = $_GET['Product'];
    }

       $dataProvider = $model->search();

     
       $this->render('index', array(
        'model' => $model,
        'dataProvider' => $dataProvider,
    ));


    }




    protected function loadModel($id)
   {
    $model = Product::model()->findByPk($id);
    if($model === null)
        throw new CHttpException(404,'The requested page does not exist.');
    return $model;
    }



     public function actionUpdate()
    {
  	  
  	   
  	$id = Yii::app()->request->getPost('id');
    $name = Yii::app()->request->getPost('name');
    $description = Yii::app()->request->getPost('description');
    $price = Yii::app()->request->getPost('price');

    // 获取要更新的模型
    $model = Product::model()->findByPk($id);

    // 更新模型的属性
    $model->name = $name;
    $model->description = $description;
    $model->price = $price;

    // 保存模型到数据库
    if ($model->save()) {
        // 如果保存成功，返回 JSON 格式的成功消息
        echo CJSON::encode(array('success' => true, 'message' => 'Model updated successfully.'));
    } else {
        // 如果保存失败，返回 JSON 格式的错误消息
        echo CJSON::encode(array('success' => false, 'message' => 'Failed to update model.'));
    }

  }


    public function actionGetData() {

        // 从数据库中获取模型数据
        $id= $_GET["id"];
        $model = Product::model()->findByPk($id);

         // 将模型数据编码为 JSON 格式并返回响应
         echo CJSON::encode(array(
        'name' => $model->name,
        'description' => $model->description,
        'price' => $model->price,
        ));
    }

}
