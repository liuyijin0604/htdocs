<?php
class ImportsMailService extends Service
{
    public function sendFeedbackEmail()
	{
		$fromTime = date("Y-m-d H:i:s", strtotime("-4 days"));
		$toTime = date("Y-m-d H:i:s", strtotime("-3 days"));
		$sql = "SELECT * FROM imports_mail WHERE 
		ticket != '' 
		AND `date` >= '" . $fromTime . "'
		AND `date` <= '" . $toTime . "'
		AND subject NOT LIKE '%Re:%' 
		AND id NOT IN (SELECT id FROM imports_mail WHERE from_email LIKE '%@toplogistics.com.au%' AND to_email LIKE '%@toplogistics.com.au%' AND `date` >= '" . $fromTime . "' AND `date` <= '" . $toTime . "')
		AND from_email NOT LIKE '%@fastway.com.au%'
		AND from_email NOT LIKE '%@tollgroup.com%'
		AND from_email NOT LIKE '%@au.gotoubi.com%'
		AND to_email NOT LIKE '%@fastway.com.au%'
		AND to_email NOT LIKE '%@tollgroup.com%'
		AND to_email NOT LIKE '%@au.gotoubi.com%'
        GROUP BY ticket
		ORDER BY to_email";

		$emailList = ImportsMail::model()->findAllBySql($sql);	// emailList is ordered by to_email attribute for erasing duplicated emails
		$count = 1;			// default count value of each email address
		$prevTo = '';		// store the last email address
		foreach ($emailList as $email) {
            if ($email->date > $email->getDeadline()) {
                continue;
            }
			if ($prevTo == $email->to_email) {
				$count++;
				// for each duplicated email address, only keep top 10 to send feedback email
				if ($count > 10) {
					continue;
				}
			} else {
				$count = 1;     // Reset count
			}
			$service = new EmailService();
			$service->sendImportsMailFeedbackEmail($email);
			// Set email status
			$email->status = ImportsMail::STATE_FEEDBACK_SENT;
			$email->save();
			// update previous email address
			$prevTo = $email->to_email;
		}
	}

	public function setImportsMailFeedbackReport()
	{
		/** Get all imports mail users with feedback email sent to customers. */
		$sql = "SELECT DISTINCT op_id FROM imports_mail WHERE `status` IN (110, 111) AND op_id != 0";
		$mailUsers = Yii::app()->db->createCommand($sql)->queryAll();
		$users = [];
		foreach($mailUsers as $user) {
			array_push($users, $user['op_id']);
		}
		
		$result = [];		// Store user name, overall, and this month ratings
		$date = date('Y-m-01 00:00:00');	// First day of this month
		/**
		 * Calculate all time rating and this month rating for each mail user.
		 */
		foreach($users as $user) {
			// Get all time records
			$sql = "SELECT meta FROM imports_mail WHERE `status`=111 AND op_id=".$user;
			if ($user == 3592) { // For user cs2, only record feedback after 2023-04-01
				$sql = "SELECT meta FROM imports_mail WHERE `status`=111 AND `date` >= '2023-04-01 00:00:00' AND op_id=".$user;
			}
			$overallResponse = Yii::app()->db->createCommand($sql)->queryAll();
			// Get this month's records
			$sql = "SELECT meta FROM imports_mail WHERE `status`=111 AND op_id=".$user." AND `date`>='".$date. "'";
			$mtdResponse = Yii::app()->db->createCommand($sql)->queryAll();
			// Get user name
			$sql = "SELECT fname, lname FROM user WHERE id = ".$user;
			$userName = Yii::app()->db->createCommand($sql)->queryAll();
			if (!empty($userName)) {
				$result[$user]['name'] = $userName[0]['fname']." ".$userName[0]['lname'];
			} else {
				$result[$user]['name'] = "Unknown";
			}
			// Calculate all time rating
			$overallCount = 0;
			$overallTotal = 0;
			foreach($overallResponse as $r) {
				$overallCount++;
				$mdata = json_decode($r['meta']);
				$overallTotal += $mdata->rating;
			}
			if ($overallCount != 0) {
				$result[$user]['overall_rating'] = $overallTotal/$overallCount;
			}
			$result[$user]['overall_count'] = $overallCount;
			// Calculate this month's rating
			$mtdCount = 0;
			$mtdTotal = 0;
			foreach($mtdResponse as $r) {
				$mtdCount++;
				$mtdmdata = json_decode($r['meta']);
				$mtdTotal += $mtdmdata->rating;
			}
			if ($mtdCount != 0) {
				$result[$user]['mtd_rating'] = $mtdTotal/$mtdCount;
			}
			$result[$user]['mtd_count'] = $mtdCount;
		}
		/**
		 * Save result into report_cache table
		 */
		$result = json_encode($result);
		// Deactivate old records
		$sql = "SELECT * FROM report_cache WHERE `type` = " .ReportCache::IMPORTS_MAIL_FEEDBACK_REPORT. " AND `status` = 1";
		$oldReportCache = ReportCache::model()->findAllBySql($sql);
		foreach($oldReportCache as $old) {
			$old->status = 0;
			$old->modify_time= date('Y-m-d h:i:s', time());
			$old->save();
		}
		// Save new record and set it to active
		$reportCache = new ReportCache();
		$reportCache->type = ReportCache::IMPORTS_MAIL_FEEDBACK_REPORT;
		$reportCache->status = 1;
		$reportCache->meta = $result;
		$reportCache->create_time = date('Y-m-d h:i:s', time());
		$reportCache->creater = 1;
		$reportCache->modify_time= date('Y-m-d h:i:s', time());
		$reportCache->modifyer = 1;
		$reportCache->save();
	}

	public function getEmailPreviousAndNext($model)
	{
		$id = $model->id;
		$model = new ImportsMail('search');
		$model->is3pl = null;
		$model->myUserId = User::currentUserID();

		$log = Log::model()->find('model = "User" AND lid = :lid AND type = 3', [':lid' => $model->myUserId]);
		if(!empty($log))
		{
			$user_create = $log->time;
		}else
		{
			$user_create = '2000-01-01 00:00:00';
		}


		$ec = new CDbCriteria;
		$ec->addCondition('t.create_time >= "' . $user_create . '"');
		$model->id = ">".$id;
		$previousId = 0;
		$previousNo = "";
		$nextId = 0;
		$nextNo = "";

		$model->id = ">".$id;
		$_GET['sort'] = "id";
		$previous = $model->search(true, 1, $ec, false)->data;
		if(!empty($previous))
		{
			$previousId = $previous[0]->id;
			$previousNo = $previous[0]->no;
		}

		$model->id = "<".$id;
		$_GET['sort'] = "id.desc";
		$next = $model->search(true, 1, $ec, false)->data;
		if(!empty($next))
		{
			$nextId = $next[0]->id;
			$nextNo = $next[0]->no;
		}
		return ["previousId"=>$previousId,"previousNo"=>$previousNo,"nextId"=>$nextId,"nextNo"=>$nextNo];
	}

	public function getGoodsReleasedMailInfo($model){
		if (strpos($model->from_email, "DoNotReply@awe.gov.au") !== false || strpos($model->from_email, "DoNotReply@agriculture.gov.au") !== false) {
			if (!empty($model->attachments)) {
				$transaction = Yii::app()->db->beginTransaction();
				try {
					foreach ($model->attachments as $at) {
						if ($at->mime != "application/pdf") continue;
						$d = Yii::app()->params['fileRepoPath'].DIRECTORY_SEPARATOR.substr($at->hash,0,2);
				        if(!is_dir($d)) mkdir($d);
				        try
				        {
				            move_uploaded_file($at->name, $d.DIRECTORY_SEPARATOR.$at->hash);
				        }catch(Exception $e)
				        {
				        		continue;
				        }
						$pattern = '/Notice: Goods released/';
			            $frPath = $at->getFile();
			            $f2 = tempnam(Yii::app()->basePath . DIRECTORY_SEPARATOR . "runtime" . DIRECTORY_SEPARATOR."temp".DIRECTORY_SEPARATOR, 'check');
			            $result = oPDF::pdftotxt($frPath,$f2);                  
			            if(preg_match($pattern, $result))
			            {
			            	// $pattern1 = '/Entry: BAB/';
			            	$pattern1 = '/Entry:\s*([A-Z0-9]+)/';
			            	$pattern2 = '/HAWB\s*(\w+)/';
			            	$pattern3 = '/HBOL\s*(\w+)/';
			                if(preg_match($pattern1,$result,$e) && (preg_match($pattern2,$result,$m2) || preg_match($pattern3,$result,$m3)))
			                {		                	
			                	$m = empty($m2)?$m3:$m2;
			                	$shipmentModel = ImParcel::model()->find(["condition"=>"hbn = :hbn","params"=>[":hbn"=>$m[1]],"order"=>" id desc"]);
			                	// $pphash = FileRepo::uploadHash($shipmentModel, 19);	

			                	$temFiles = FileRepo::model()->findAll(["condition"=>"name = :name and type = 19 and fid = :fid","params"=>["fid" => $shipmentModel->id,":name"=>substr_replace($at->name, " ".$shipmentModel->hbn." released direction", -4, 0)]]);
			                	if (!empty($temFiles)) {
			                		continue;
			                	}		                	

			                	$tempAt = new FileRepo();
								$tempAt->fid = $shipmentModel->id;
								$tempAt->type = 19;
								$tempAt->name = substr_replace($at->name, " ".$shipmentModel->hbn." released direction", -4, 0);
								$tempAt->hash = $at->hash;
								$tempAt->mime = $at->mime;
								$tempAt->size = $at->size;
								$tempAt->date = date('Y-m-d H:i:s');
								$tempAt->status = $at->status;	
								if (strpos($shipmentModel->can, $e[1]) === false) {
								    $shipmentModel->can = $e[1]." ".$shipmentModel->can;
								}							
								if (strpos($shipmentModel->can, "rls ") === false) {
								    $shipmentModel->can = "rls ".$shipmentModel->can;
								}
								if ($tempAt->save() && $shipmentModel->update(['can'])) {
									// print_r($tempAt->fid."||".$shipmentModel->hbn);
									$this->saveMultiHtmlMailRecord($shipmentModel,"Released",$e[1]);

									$model->type = 80;
									$mailUsers = MailUser::model()->findAll("mail_id=".$model->id);
									if (!empty($mailUsers)) {
										foreach ($mailUsers as $key => $user) {
											$user->delete();
										}
									}			
									$model->save();
									$transaction->commit();				
									$model->newMailUser();
			                		return true;
								}
								else{
									$transaction->commit();
									return false;
								}
			                }          
			            }
					}
					$transaction->commit();
				} catch (Exception $ex) {
					$transaction->rollback();
				}
			}

        }
	}

	public function getDoNotMoveMailInfo($model){
		if (strpos($model->from_email, "DoNotReply@awe.gov.au") !== false || strpos($model->from_email, "DoNotReply@agriculture.gov.au") !== false) {
			if (!empty($model->attachments)) {
				$transaction = Yii::app()->db->beginTransaction();
				try {
					foreach ($model->attachments as $at) {
						if ($at->mime != "application/pdf") continue;
						$d = Yii::app()->params['fileRepoPath'].DIRECTORY_SEPARATOR.substr($at->hash,0,2);
				        if(!is_dir($d)) mkdir($d);
				        try
				        {
				            move_uploaded_file($at->name, $d.DIRECTORY_SEPARATOR.$at->hash);
				        }catch(Exception $e)
				        {
				        	continue;
				        }
						$pattern = '/Biosecurity Direction: Do not move goods/';
			            $frPath = $at->getFile();
			            $f2 = tempnam(Yii::app()->basePath . DIRECTORY_SEPARATOR . "runtime" . DIRECTORY_SEPARATOR."temp".DIRECTORY_SEPARATOR, 'check');
			            $result = oPDF::pdftotxt($frPath,$f2);                  
			            if(preg_match($pattern, $result))
			            {
			            	// $pattern1 = '/Entry: BAB/';
			            	$pattern1 = '/Entry:\s*([A-Z0-9]+)/';
			            	$pattern2 = '/HAWB\s*(\w+)/';
			            	$pattern3 = '/HBOL\s*(\w+)/';
			                if(preg_match($pattern1,$result,$e) && (preg_match($pattern2,$result,$m2) || preg_match($pattern3,$result,$m3)))
			                {
			                	$m = empty($m2)?$m3:$m2;
			                	$shipmentModel = ImParcel::model()->find(["condition"=>"hbn = :hbn","params"=>[":hbn"=>$m[1]],"order"=>" id desc"]);
			                	// $pphash = FileRepo::uploadHash($shipmentModel, 19);		

			                	$temFiles = FileRepo::model()->findAll(["condition"=>"name = :name and type = 19 and fid = :fid","params"=>["fid" => $shipmentModel->id,":name"=>substr_replace($at->name, " ".$shipmentModel->hbn." do not move", -4, 0)]]);
			                	if (!empty($temFiles)) {
			                		continue;
			                	}	                	

			                	$tempAt = new FileRepo();
								$tempAt->fid = $shipmentModel->id;
								$tempAt->type = 19;
								$tempAt->name = substr_replace($at->name, " ".$shipmentModel->hbn." do not move", -4, 0);
								$tempAt->hash = $at->hash;
								$tempAt->mime = $at->mime;
								$tempAt->size = $at->size;
								$tempAt->date = date('Y-m-d H:i:s');
								$tempAt->status = $at->status;
								if (strpos($shipmentModel->can, $e[1]) === false) {
								    $shipmentModel->can = $e[1]." ".$shipmentModel->can;
								}							
								if ($tempAt->save() && $shipmentModel->update(['can'])) {
									$doNotMovereportCache = ReportCache::model()->find('create_time = :today and type=:type and status=1',[':today'=>date('Y-m-d')." 00:00:00",":type"=>ReportCache::DoNotMoveSumMailTotalByDaily]);
									// print_r($doNotMovereportCache);
									if ((!empty($doNotMovereportCache))) {										
										// $provide = json_decode($doNotMovereportCache->meta);
										$doNotMovereportCache->mdata['total'] +=1;
										$doNotMovereportCache->mdata['shipments'][] = ['id' => $shipmentModel->id, 'hbn' => $shipmentModel->hbn, 'can' => $e[1], 'time' => date('Y-m-d H:i:s')];
										$doNotMovereportCache->modify_time = date('Y-m-d H:i:s');
										$doNotMovereportCache->save();
									}
									else{
										$shipmentInfo = ['id' => $shipmentModel->id, 'hbn' => $shipmentModel->hbn, 'can' => $e[1], 'time' => date('Y-m-d H:i:s')];
										$shipments[] = $shipmentInfo;
										$provide =  ['total' => 1, "shipments" => $shipments];
										$temp = json_encode($provide);										
										$doNotMovereportCache = new ReportCache();
										$doNotMovereportCache->type = ReportCache::DoNotMoveSumMailTotalByDaily;
										$doNotMovereportCache->status = ReportCache::Active;
										$doNotMovereportCache->meta = $temp;
										// $doNotMovereportCache->mdata['shipments'] = ['id' => $shipmentModel->id, 'hbn' => $shipmentModel->hbn];
										$doNotMovereportCache->create_time = date('Y-m-d')." 00:00:00";
										$doNotMovereportCache->creater = 1;
										$doNotMovereportCache->modify_time = date('Y-m-d H:i:s');
										$doNotMovereportCache->modifyer = 1;		
										$doNotMovereportCache->save();	
									}
									$model->type = 80;
									$mailUsers = MailUser::model()->findAll("mail_id=".$model->id);
									if (!empty($mailUsers)) {
										foreach ($mailUsers as $key => $user) {
											$user->delete();
										}
									}
									$model->save();
									$transaction->commit();							
									$model->newMailUser();
			                		return true;
								}
								else{
									echo $tempAt->no."not saved";
									$transaction->commit();
									return false;
								}
			                }          
			            }
					}
					$transaction->commit();
				} catch (Exception $ex) {
					print_r($ex);
					$transaction->rollback();
				}
			}

        }
	}

	public function getCustomHtmlMailAtt($model){
		if (strpos($model->from_email, "AutoEntry@agriculture.gov.au") !== false) {
			if (!empty($model->attachments)) {
				$submit = 0;
				$transaction = Yii::app()->db->beginTransaction();
				try {
					foreach ($model->attachments as $at) {
						if($at->mime != "text/html") continue;
						$d = Yii::app()->params['fileRepoPath'].DIRECTORY_SEPARATOR.substr($at->hash,0,2);
				        if(!is_dir($d)) mkdir($d);
				        try
				        {
				            move_uploaded_file($at->name, $d.DIRECTORY_SEPARATOR.$at->hash);
				        }catch(Exception $e)
				        {
				        	continue;
				        }
						// $pattern = '/Biosecurity Direction: Do not move goods/';
			            // $frPath = $at->getFile();
			            // $f2 = tempnam(Yii::app()->basePath . DIRECTORY_SEPARATOR . "runtime" . DIRECTORY_SEPARATOR."temp".DIRECTORY_SEPARATOR, 'check');
			            // $result = oPDF::pdftotxt($frPath,$f2);  
			            // $result = file_get_contents($frPath);                
			            
		            	$pattern = '/Interim Record of Service/';
		            	$frPath = $at->getFile();
		            	$result = file_get_contents($frPath); 
		            	$pattern1 = '/\*BAB(\w+)\*/';
		            	// $pattern1 = '/Entry:\s*([A-Z0-9]+)/';
		            	$pattern2 = '/HAWB:(\w+)/';
		            	$pattern3 = '/HBOL:(\w+)/';

		                if(preg_match($pattern, $result) && preg_match($pattern1,$result,$e) && (preg_match($pattern2,$result,$m2) || preg_match($pattern3,$result,$m3)))
		                {
		                	$m = empty($m2)?$m3:$m2;
		                	$shipmentModel = ImParcel::model()->find(["condition"=>"hbn = :hbn","params"=>[":hbn"=>$m[1]],"order"=>" id desc"]);
		                	// $pphash = FileRepo::uploadHash($shipmentModel, 19);		

		                	if (empty($shipmentModel)) continue;
	                		foreach ($model->attachments as $key2 => $sa) {
		                		if($sa->mime == "text/html")
		            			{
		            				$temFiles = FileRepo::model()->findAll(["condition"=>"name = :name and type = 19 and fid = :fid","params"=>["fid" => $shipmentModel->id,":name"=>$sa->name]]);
				                	if (!empty($temFiles)) {
				                		continue;
				                	}

				                	$tempAt = new FileRepo();
									$tempAt->fid = $shipmentModel->id;
									$tempAt->type = 19;
									$tempAt->name = $sa->name;
									$tempAt->hash = $sa->hash;
									$tempAt->mime = $sa->mime;
									$tempAt->size = $sa->size;
									$tempAt->date = date('Y-m-d H:i:s');
									$tempAt->status = $sa->status;
									$tempAt->save();
		            			}
		            			$submit = 1;
	                		}                		
		                	
							if (!empty($e[1]) && strpos($shipmentModel->can, ('BAB'.$e[1])) === false) {
							    $shipmentModel->can = 'BAB'.$e[1]." ".$shipmentModel->can;
							}							
							if (!empty($submit) && $shipmentModel->update(['can'])) {
								$doNotMovereportCache = ReportCache::model()->find('create_time = :today and type=:type and status=1',[':today'=>date('Y-m-d')." 00:00:00",":type"=>ReportCache::CustomAttHtmlMailTotalByDaily]);
								// print_r($doNotMovereportCache);
								if ((!empty($doNotMovereportCache))) {										
									// $provide = json_decode($doNotMovereportCache->meta);
									$doNotMovereportCache->mdata['total'] +=1;
									$doNotMovereportCache->mdata['shipments'][] = ['id' => $shipmentModel->id, 'hbn' => $shipmentModel->hbn, 'can' => 'BAB'.$e[1], 'time' => date('Y-m-d H:i:s')];
									$doNotMovereportCache->modify_time = date('Y-m-d H:i:s');
									$doNotMovereportCache->save();
								}
								else{
									$shipmentInfo = ['id' => $shipmentModel->id, 'hbn' => $shipmentModel->hbn, 'can' => 'BAB'.$e[1], 'time' => date('Y-m-d H:i:s')];
									$shipments[] = $shipmentInfo;
									$provide =  ['total' => 1, "shipments" => $shipments];
									$temp = json_encode($provide);										
									$doNotMovereportCache = new ReportCache();
									$doNotMovereportCache->type = ReportCache::CustomAttHtmlMailTotalByDaily;
									$doNotMovereportCache->status = ReportCache::Active;
									$doNotMovereportCache->meta = $temp;
									// $doNotMovereportCache->mdata['shipments'] = ['id' => $shipmentModel->id, 'hbn' => $shipmentModel->hbn];
									$doNotMovereportCache->create_time = date('Y-m-d')." 00:00:00";
									$doNotMovereportCache->creater = 1;
									$doNotMovereportCache->modify_time = date('Y-m-d H:i:s');
									$doNotMovereportCache->modifyer = 1;		
									$doNotMovereportCache->save();	
								}
								$model->type = 80;
								$mailUsers = MailUser::model()->findAll("mail_id=".$model->id);
								if (!empty($mailUsers)) {
									foreach ($mailUsers as $key => $user) {
										$user->delete();
									}
								}
								$model->save();
								$transaction->commit();							
								$model->newMailUser();
		                		return true;
							}
							else{
								echo $shipmentModel->hbn."not saved";
								$transaction->commit();
								return false;
							}
		                }          
			           
					}
					$transaction->commit();
				} catch (Exception $ex) {
					print_r($ex);
					$transaction->rollback();
				}
			}

        }
	}

	public function checkCustomMail($model){
		$this->getGoodsReleasedMailInfo($model);
		$this->getDoNotMoveMailInfo($model);
		// $this->getCustomHtmlMailAtt($model);
		$this->getMultiHtmlMailAtt($model);

	}

	public function uploadDNMAttToEmail($shipmentId){
		$shipmentModel = ImParcel::model()->findByPk($shipmentId);
		$temFiles = FileRepo::model()->findAll('type = 19 and fid = :fid and name like "%'.$shipmentModel->hbn.' do not move%"',["fid" => $shipmentModel->id]);

		if (!empty($temFiles)) {
			foreach ($temFiles as $key => $temFile) {
				$at = FileRepo::model()->findAll('type = 80 and name = :name and fid = :fid and status =20 ',["fid" => $shipmentModel->id, ":name" => $temFile->name ]);
				if (!empty($at)) continue;
				$tempAt = new FileRepo();
				$tempAt->fid = $shipmentModel->id;
				$tempAt->type = 80;
				$tempAt->name = $temFile->name;
				$tempAt->hash = $temFile->hash;
				$tempAt->mime = $temFile->mime;
				$tempAt->size = $temFile->size;
				$tempAt->date = date('Y-m-d H:i:s');
				$tempAt->status = 20;
				$tempAt->save();
			}
		}	

	}

	public function getMultiHtmlMailAtt($model){
		if (strpos($model->from_email, "AutoEntry@agriculture.gov.au") !== false) {
			if (!empty($model->attachments)) {
				$assignToNoOne = 0;
				$transaction = Yii::app()->db->beginTransaction();
				try {
					foreach ($model->attachments as $at) {
						if($at->mime != "text/html") continue;
						$d = Yii::app()->params['fileRepoPath'].DIRECTORY_SEPARATOR.substr($at->hash,0,2);
				        if(!is_dir($d)) mkdir($d);
				        try
				        {
				            move_uploaded_file($at->name, $d.DIRECTORY_SEPARATOR.$at->hash);
				        }catch(Exception $e)
				        {
				        	continue;
				        }

		            	$patterns = ["Interim","Pending[\s]*(\&nbsp;)*payment","Pending[\s]*(\&nbsp;)*Information","Inspection","Secure","Present[\s]*(\&nbsp;)*all[\s]*(\&nbsp;)*documentation","Pending[\s]*(\&nbsp;)*Disposal[\s]*(\&nbsp;)*Permission","Disposal[\s]*(\&nbsp;)*Permission","Final[\s]*(\&nbsp;)*Record","Released"];
		            	$frPath = $at->getFile();
		            	$result = file_get_contents($frPath); 
		            	$pattern1 = '/\*BAB(\w+)\*/';
		            	// $pattern1 = '/Entry:\s*([A-Z0-9]+)/';
		            	$pattern2 = '/HAWB:(\w+)/';
		            	$pattern3 = '/HBOL:(\w+)/';
		            	$pattern4 = '/\*BAA(\w+)\*/';

		            	preg_match('/<span\s+style="font-size:16pt">(.*?)<\/span>/i', $result, $title1);
		            	preg_match('/<span\s+style="font-size:12pt">(.*?)<\/span>/i', $result, $title2);
		            	// print_r($title1);
		            	// print_r($title2);

		                foreach ($patterns as $i => $pattern) {
		                	$keyword = "/{$pattern}/i";
		                	if (!empty($title1) && preg_match($keyword, $title1[1], $key1) ) {
		                		$keys = $key1;
		                	}
		                	elseif (!empty($title2) && preg_match($keyword, $title2[1], $key2)) {
		                		$keys = $key2;
		                	}
		                	else{
		                		continue;
		                	}
		                	if((preg_match($pattern1,$result,$e1) || preg_match($pattern4,$result,$e2)) && (preg_match($pattern2,$result,$m2) || preg_match($pattern3,$result,$m3)))
			                {
			                	$e = empty($e1)?("BAA".$e2[1]):("BAB".$e1[1]);
			                	$m = empty($m2)?$m3:$m2;
			                	// print_r($e);
			                	// print_r($m);
			                	$shipmentModel = ImParcel::model()->find(["condition"=>"hbn = :hbn","params"=>[":hbn"=>$m[1]],"order"=>" id desc"]);
			                	// $pphash = FileRepo::uploadHash($shipmentModel, 19);		

			                	if (empty($shipmentModel)) continue;
		                		
		                		$temFiles = FileRepo::model()->findAll(["condition"=>"name = :name and type = 19 and fid = :fid","params"=>[":fid" => $shipmentModel->id,":name"=>substr_replace($at->name, " ".$shipmentModel->hbn." ".$keys[0], -4, 0)]]);
			                	if (!empty($temFiles)) {
			                		continue;
			                	}

			                	$tempAt = new FileRepo();
								$tempAt->fid = $shipmentModel->id;
								$tempAt->type = 19;
								$tempAt->name = substr_replace($at->name, " ".$shipmentModel->hbn." ".$keys[0], -4, 0);
								$tempAt->hash = $at->hash;
								$tempAt->mime = $at->mime;
								$tempAt->size = $at->size;
								$tempAt->date = date('Y-m-d H:i:s');
								$tempAt->status = $at->status;     		
	                	
								if (!empty($e) && strpos($shipmentModel->can,$e) === false) {
								    $shipmentModel->can = $e." ".$shipmentModel->can;								    
								}
								if ($pattern == "Released" && strpos($shipmentModel->can, "rls ") === false) {
							    	$shipmentModel->can = "rls ".$shipmentModel->can;
							    }							
								if ($tempAt->save() && $shipmentModel->update(['can'])) {
									$this->saveMultiHtmlMailRecord($shipmentModel,$keys[0],$e);
									$assignToNoOne = 1;
									break;									
								}
								else{
									echo $shipmentModel->hbn."not saved";
								}
			                }   
		                }		           
					}

					if (!empty($assignToNoOne)) {	
						$model->type = 80;
						$mailUsers = MailUser::model()->findAll("mail_id=".$model->id);
						if (!empty($mailUsers)) {
							foreach ($mailUsers as $key => $user) {
								$user->delete();
							}
						}
						$model->save();
						$transaction->commit();							
						$model->newMailUser();
                		return true;
					}
					else{
						echo $model->no." not done";
						$transaction->commit();
						return false;
					}
					$transaction->commit();
				} catch (Exception $ex) {
					print_r($ex);
					$transaction->rollback();
				}
			}

        }
	}

	public function saveMultiHtmlMailRecord($shipmentModel,$key,$e){
		if ($key == "Released") {
			$type = ReportCache::ReleasedDirectionMailTotalByDaily;
		}
		else{
			$type = ReportCache::CustomAttHtmlMailTotalByDaily;
		}
		$doNotMovereportCache = ReportCache::model()->find('create_time = :today and type=:type and status=1',[':today'=>date('Y-m-d')." 00:00:00",":type"=>$type]);
		// print_r($doNotMovereportCache);
		if ((!empty($doNotMovereportCache))) {										
			// $provide = json_decode($doNotMovereportCache->meta);
			$doNotMovereportCache->mdata['total'] +=1;
			$doNotMovereportCache->mdata['shipments'][] = ['id' => $shipmentModel->id, 'hbn' => $shipmentModel->hbn, 'can' => $e, 'key' => $key, 'time' => date('Y-m-d H:i:s')];
			$doNotMovereportCache->modify_time = date('Y-m-d H:i:s');
			$doNotMovereportCache->save();
		}
		else{
			$shipmentInfo = ['id' => $shipmentModel->id, 'hbn' => $shipmentModel->hbn, 'can' => $e, 'key' => $key, 'time' => date('Y-m-d H:i:s')];
			$shipments[] = $shipmentInfo;
			$provide =  ['total' => 1, "shipments" => $shipments];
			$temp = json_encode($provide);										
			$doNotMovereportCache = new ReportCache();
			$doNotMovereportCache->type = $type;
			$doNotMovereportCache->status = ReportCache::Active;
			$doNotMovereportCache->meta = $temp;
			// $doNotMovereportCache->mdata['shipments'] = ['id' => $shipmentModel->id, 'hbn' => $shipmentModel->hbn];
			$doNotMovereportCache->create_time = date('Y-m-d')." 00:00:00";
			$doNotMovereportCache->creater = 1;
			$doNotMovereportCache->modify_time = date('Y-m-d H:i:s');
			$doNotMovereportCache->modifyer = 1;		
			$doNotMovereportCache->save();	
		}
	}


}