<?php

$service = new BwtrunkService();

$data = $service->getDate();
print_r($data);

$model = new Bwtrunk();
$model->attributes =$data;
$model->save();