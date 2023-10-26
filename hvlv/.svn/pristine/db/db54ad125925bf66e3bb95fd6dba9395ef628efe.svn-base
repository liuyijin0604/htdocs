<?php
	interface TlaTaskFunction
	{
		public function getTaskContent();
		// return string
		// 由于不同的父类的content实现的内容不一样
		// 所以调用此接口 来实现getTaskContent()方法
		// 这样就可以输出不同的Task内容

		public function assignUser($userId);
		// return [boolean,msg]
		// 不同的子类assignUser时实现方法的内容也不一样所以
		// 都要实现此接口函数的实现


		public function getOperationLink();
		// return string
		// 不同的子类获取的operation tab 的link是不一样的，所以调用
		// 的实现也要不一样

		public function complete();
		// return [boolean,msg]

		public function close();
		// return [boolean,msg]
		

	}
?>