<?php
/*
2019-08-20 Gero
*/
class JsonResponsePacking
{
	private static $defaultMsg = "try again";
	public static function packing($code,$data,$msg = false)
	{
		if(empty($msg))
		{
			$msg = self::$defaultMsg;
		}

		return json_encode(["code"=>$code,"data"=>$data,"msg"=>$msg]);
	}
}

?>