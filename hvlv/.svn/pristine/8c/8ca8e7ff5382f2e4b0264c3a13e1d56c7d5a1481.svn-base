<?php
class HolidayHelper
{
	public static $holidays=['01-01','01-28','04-19','04-20','04-21','04-22','04-25','06-10','10-07','12-25','12-26'];

	public static function checkDateIsHoliday($nextDay,$pod = false)
	{
		$pod = empty($pod)?'AUSYD':$pod;
		$thisYear =  date('Y',strtotime($nextDay));
		$checkDate =  date('Ymd',strtotime($nextDay));
		$holidays=[];
		foreach (self::$holidays as $key => $value) $holidays[]= $thisYear.'-'.$value;
		$holidays = self::getHoliday($thisYear);
		if(!empty($holidays[$checkDate.$pod]))
		{
            if(in_array($holidays[$checkDate.$pod]['Holiday Name'], ['Bank Holiday']))
            {
                return false;
            }
            
			return true;
		}else
		{
			$da= date("w",strtotime($nextDay));
			if($da=="6"||$da=="0")
			{
				return true;
			}
		}

		return false;

	}

	public static function checkDateIsOnlyHoliday($nextDay,$pod = false)
	{
		$pod = empty($pod)?'AUSYD':$pod;
		$thisYear =  date('Y',strtotime($nextDay));
		$checkDate =  date('Ymd',strtotime($nextDay));
		$holidays=[];
		foreach (self::$holidays as $key => $value) $holidays[]= $thisYear.'-'.$value;
		$holidays = self::getHoliday($thisYear);
		if(!empty($holidays[$checkDate.$pod]))
		{
			return true;
		}

		return false;

	}
    public static function next_months($date, $months = 1)
    {

        if (is_numeric($date)) {
            $date = date('Y-m-d', $date);

        }

        $next_month = date('Y-m-d', strtotime('+' . $months . ' month', strtotime($date)));

        list($d_y, $d_m, $d_d) = explode('-', $date);

        list($n_y, $n_m, $n_d) = explode('-', $next_month);

        $diff = ($n_y - $d_y) * 12 + ($n_m - $d_m);

        if ($diff > $months) {

        $next_month = date('Y-m-d', strtotime('last day of next month', strtotime($date)));

        }

        return $next_month;

    }

    public static function getMonthNextMonth($month)
    {
    	$theMonth = intval(explode("-", $month)[1]);
	    $month2 = "";
	    if($theMonth==12)
	    {
	      $month2 = (intval(explode("-", $month)[0])+1)."-01";
	    }elseif($theMonth>=9)
	    {
	      $month2 = explode("-", $month)[0]."-".($theMonth+1);
	    }elseif($theMonth<9)
	    {
	      $month2 = explode("-", $month)[0]."-0".($theMonth+1);
	    }
    	return $month2;
    }

    public static function next_weeks($date, $weeks = 1)
    {
        $next_week = date('Y-m-d', strtotime('+' .(7*$weeks) . ' day', strtotime($date)));
        return $next_week;

    }

    public static function next_fortnights($date, $fortnights = 1)
    {

        $next_week = date('Y-m-d', strtotime('+'. (14*$weeks) . ' day', strtotime($date)));
        return $next_week;

    }


	public static function getNextWeekday($date,$pod)
	{
		$nextDay = date('Y-m-d',strtotime('+1 day',strtotime($date)));
		$re = true;
		while($re)
		{
			if(!self::checkDateIsHoliday($nextDay,$pod))
			{
				$re = false;
			}else
			{
				$nextDay = date('Y-m-d',strtotime('+1 day',strtotime($nextDay)));
			}
		}
		return $nextDay;
	}

	public static function getLastWeekday($date,$pod)
	{
		$nextDay = date('Y-m-d',strtotime('-1 day',strtotime($date)));
		$re = true;
		while($re)
		{
			if(!self::checkDateIsHoliday($nextDay,$pod))
			{
				$re = false;
			}else
			{
				$nextDay = date('Y-m-d',strtotime('-1 day',strtotime($nextDay)));
			}
		}
		return $nextDay;
	}

    public static function getLastDay($date)
    {
        $lastDay = date('Y-m-d',strtotime('-1 day',strtotime($date)));
        return $lastDay;
    }
    public static function getNextDay($date)
    {
        $lastDay = date('Y-m-d',strtotime('+1 day',strtotime($date)));
        return $lastDay;
    }

	public static function getWeekendDays($fromDate,$toDate)
    {
         $start_z = date('z',strtotime($fromDate));
         $end_z = date('z',strtotime($toDate));
         $weeksOffset = ($end_z/7) - ($start_z/7);
         $start_N = date('N',  strtotime($fromDate));
         $end_N = date('N',strtotime($toDate));
         if(($start_N+$end_N)>10)
         {
             if($start_N>5)
                 $weeksOffset +=(5-$start_N)/2;
             if($end_N>5)
                 $weeksOffset +=($end_N-5)/2;
         }
         return $weeksOffset*2;
    }

    public static function getDatesBetweenTwoDays($startDate,$endDate){
        $dates = [];
        $startDate = date("Y-m-d",strtotime($startDate));
        $endDate = date("Y-m-d",strtotime($endDate));
        if(strtotime($startDate)>strtotime($endDate)){
            //如果开始日期大于结束日期，直接return 防止下面的循环出现死循环
            return $dates;
        }elseif($startDate == $endDate){
            //开始日期与结束日期是同一天时
            array_push($dates,$startDate);
            return $dates;
        }else{
            array_push($dates,$startDate);
            $currentDate = $startDate;
            do{
                $nextDate = date('Y-m-d', strtotime($currentDate.' +1 days'));
                array_push($dates,$nextDate);
                $currentDate = $nextDate;
            }while(strtotime($endDate) > strtotime($currentDate));
            return $dates;
        }
    }

    public static function getWeekendDaysBetweenTwoDays($startDate,$endDate,$pod)
    {
    	$days = 0;
    	$dates = self::getDatesBetweenTwoDays($startDate,$endDate);
    	foreach ($dates as $key => $date) {
    		if(self::checkDateIsHoliday($date,$pod))
    		{
    			$days++;
    		}
    	}
    	return $days;
    }

    public static function getWeekdaysBetweenTwoDates($startDate,$endDate,$pod)
    {
        $days = 0;
        $dates = self::getDatesBetweenTwoDays($startDate,$endDate);
        foreach ($dates as $key => $date) {
            if(!self::checkDateIsHoliday($date,$pod))
            {
                $days++;
            }
        }
        return $days;
    }

    public static function getHoliDaysBetweenTwoDays($startDate,$endDate,$pod)
    {
    	$days = 0;
    	$dates = self::getDatesBetweenTwoDays($startDate,$endDate);
    	foreach ($dates as $key => $date) {
    		if(self::checkDateIsOnlyHoliday($date,$pod))
    		{
    			$days++;
    		}
    	}
    	return $days;
    }

    public static function diffBetweenTwoDays($day1, $day2)
    {
        $second1 = strtotime($day1);
        $second2 = strtotime($day2);
        $day1 = date("Y-m-d",$second1);
        $day2 = date("Y-m-d",$second2);
        $second1 = strtotime($day1);
        $second2 = strtotime($day2);

        if ($second1 < $second2) {
          $tmp = $second2;
          $second2 = $second1;
          $second1 = $tmp;
        }
        return ($second1 - $second2) / 86400;
    }

    public static function hourDiffBetweenTwoDays($day1, $day2)
    {
        $second1 = strtotime($day1);
        $second2 = strtotime($day2);
        return ($second1 - $second2) / 3600;
    }

    public static function getHoliday($year,$refresh = false)
    {
        $holiday = Yii::app()->cache->get('holidaysN'.$year);
        if(!empty($holiday))
        {
            $holiday = json_decode($holiday,true);
            return $holiday;
        }

        $holidays = Holidays::model()->findAll("year=:year",[":year"=>$year]);
        if(!empty($holidays))
        {
            $result=[];
            foreach ($holidays as $key => $holiday)
            {
               $result[$holiday->date.$holiday->pod] = ['Holiday Name'=>$holiday->holiday_name];
            }
            $holiday = json_encode($result);
            Yii::app()->cache->set('holidaysN'.$year,$holiday,3600);
            return $result;
        }

    	$holiday = Yii::app()->cache->get('holidays'.$year);
    	if(empty($holiday)||$refresh==true)
    	{
    		self::setHolidayByAPI($year);
    		$holiday = Yii::app()->cache->get('holidays'.$year);
    		if(empty($holiday))
    		{
    			Yii::app()->cache->set('holidays'.$year,"[]");
    			$holiday = [];
    		}else
    		{
    			$holiday = json_decode($holiday,true);
    		}
    		$holidayCount = Holidays::model()->count("year=:year",[":year"=>$year]);
    		if($holidayCount==0)
    		{
				foreach ($holiday as $key => $day) {
					$holidayObj = new Holidays();
					$pod = explode($day["Date"], $key);
					if(count($pod)>1)
					{
						$holidayObj->pod = $pod[1];
					}
					$holidayObj->date = $day["Date"];
					$holidayObj->holiday_name = $day["Holiday Name"];
					$holidayObj->information = $day["Information"];
					$holidayObj->jurisdiction = $day["Jurisdiction"];
					$holidayObj->year = $year;
					$holidayObj->save();
				}
			}


    	}else
    	{
    		$holiday = json_decode($holiday,true);
    	}

    	return $holiday;
    }
    public static function isTimeInShift($time, $start, $end) {
        if ($start < $end) {
            return $time >= $start && $time <= $end;
        } else {
            return $time >= $start || $time <= $end;
        }
    }

    public static function getDateByBusinessDays($pod,$dateto,$businessDays,$type=false)
    {
        while($businessDays>0)
        {
            if($type==1)
            {
                $dateto = self::getNextWeekday($dateto,$pod);
            }else
            {
                $dateto = self::getLastWeekday($dateto,$pod);
            }
            $businessDays--;
        }
       return $dateto;
    }

    public static function setHolidayByAPI($year)
    {
    	$address = "";
    	switch ($year) {
    		case '2019':
    			$address = "https://data.gov.au/data/api/3/action/datastore_search?resource_id=bda4d4f2-7fde-4bfc-8a23-a6eefc8cef80";
    			break;
    		case '2020':
    			$address = "https://data.gov.au/data/api/3/action/datastore_search?resource_id=c4163dc4-4f5a-4cae-b787-43ef0fcf8d8b";
            case '2021':
                $address = "https://data.gov.au/data/api/3/action/datastore_search?resource_id=31eec35e-1de6-4f04-9703-9be1d43d405b";
            case '2022':
                $address = "https://data.gov.au/data/api/3/action/datastore_search?resource_id=768053da-b12b-4196-8fef-9262829998f3";//2022
            case '2023':
                $address = "https://data.gov.au/data/api/3/action/datastore_search?resource_id=d256f989-8f49-46eb-9770-1c6ee9bd2661";//2023
    		default:
    			$address = "https://data.gov.au/data/api/3/action/datastore_search?resource_id=9e920340-0744-4031-a497-98ab796633e8";//2024
    			break;
    	}
    	
    	$data = file_get_contents($address);
    	$jsondata = json_decode($data,true)['result']['records'];
    	$importWarehouseList = [
		'nsw'=>'AUSYD',
		'vic'=>'AUMEL',
		'qld'=>'AUBNE',
		'act'=>'',
		'nt'=>'',
		'tas'=>'',
		'sa'=>'AUADL',
		'wa'=>['AUPER','AUFRE'],
		];
    	$holidays = [];
    	foreach ($jsondata as $key => $jd) {
    		if(is_array($importWarehouseList[$jd['Jurisdiction']]))
    		{
    			foreach ($importWarehouseList[$jd['Jurisdiction']] as $key => $value) {
    				$holidays[$jd['Date'].$value] = $jd;
    			}
    		}else
    		{
    			$holidays[$jd['Date'].$importWarehouseList[$jd['Jurisdiction']]] = $jd;
    		}
    	}
    	Yii::app()->cache->set('holidays'.date('Y',strtotime($jd['Date'])),json_encode($holidays));
    }

    public static function checkBetweenHours($d1,$d2)
    {
        $hour=floor((strtotime($d1)-strtotime($d2))/3600);
        return $hour;
    }

    public static function checkBetweenMinutes($d1,$d2)
    {
        $minutes=floor((strtotime($d1)-strtotime($d2))/60);
        return $minutes;
    }

    public static function is_dst($timezone = null, $time = null)
    {
        $oldtimezone = date_default_timezone_get();
        if (isset($timezone)) {
            date_default_timezone_set($timezone);
        }
     
        if (!isset($time)) {
            $time = time();
        }
     
        $tm = localtime($time, true);
        $isdst = $tm['tm_isdst'];
        $offset = 0;
        //$dsttime = mktime_array($tm);
        //echo strftime("%c", $dsttime);
     
        $tm['tm_isdst'] = 0;
        $nondsttime = mktime_array($tm);
        $offset = $nondsttime - $time;
     
        date_default_timezone_set($oldtimezone);
     
        return $offset != 0;
    }

    public static function getGMTDate($date)
    {
        $gmtDay = date('Ymd',strtotime('-10 hours',strtotime($date)));
        return $gmtDay;
    }

    public static function getGMTTime($date)
    {
        $gmtTime = date('Hi',strtotime('-10 hours',strtotime($date)));
        return $gmtTime;
    }

    public static function getRandom2Minute($time)
    {
        $randomSeconds = mt_rand(0,300)-200;
        $myTime = $time;
        if($randomSeconds>0)
        {
            $myTime = date('Y-m-d H:i:s',strtotime('+'.$randomSeconds.' seconds',strtotime($time)));
        }elseif($randomSeconds<0)
        {
            $myTime = date('Y-m-d H:i:s',strtotime('-'.$randomSeconds.' seconds',strtotime($time)));
        }
        return $myTime;
    }

    public static function getRandomMinute($time,$seconds)
    {
        $randomSeconds = mt_rand(0,$seconds);
        $myTime = $time;
        if($randomSeconds>0)
        {
            $myTime = date('Y-m-d H:i:s',strtotime('+'.$randomSeconds.' seconds',strtotime($time)));
        }elseif($randomSeconds<0)
        {
            $myTime = date('Y-m-d H:i:s',strtotime('-'.$randomSeconds.' seconds',strtotime($time)));
        }
        return $myTime;
    }
}

?>