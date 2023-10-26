<?php

class ApiDPlatformAction extends CAction 
{
    public $ctlr;
    public $debug;
    public $user;

    public function run()
    {
        $this->ctlr = $this->getController();
        if (!empty($_POST['method']) && method_exists($this, $_POST['method'])) {
			if (!in_array($_POST['method'], ['get'])) {
				$this->log(json_encode($_POST));
			}
			$this->{$_POST['method']}();
		} else {
			throw new CHttpException(400, 'API method not found!');
		}
    }

    public function log($l)
    {
        $tmp = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR . 'dplatform_api' . DIRECTORY_SEPARATOR ;
        if (!is_dir($tmp)) {
            mkdir($tmp);
        }
		file_put_contents($tmp.'dplatform_api_'  . date('Y-m-d') . '.log', date('Y-m-d H:i:s').' '.$l."\n", FILE_APPEND);
    }

    public function createDriver()
    {
        $d = $this->ctlr->data;
        $res = new stdClass;
        $res->status = 0;
        if (!is_object($d)) {
            $res->status = 3001;
            $res->message = 'Json data malformatted';
        } else {
            $org = self::createOrg($d);
            if (!empty($org)) {
                // no need to create user in hvlv
                /* foreach($d->users as $user) {
                    if(!self::createUser($org->id, $user)) {
                        $res->status = 3003;
                        $res->message = "Create user account failed, please check invalid parameters";
                        break;
                    }
                } */
            } else {
                $res->status = 3002;
                $res->message = "Create company failed: please check your invalid parameters";
            }
        }
        if ($res->status == 0) {
            $res->message = "Success";
        }
        echo json_encode($res);
    }

    private function createOrg($data)
    {
        $org = new Org();
        $org->name = $data->company_name;
        $org->address = $data->company_address;
        $org->suburb = $data->company_suburb;
        $org->state = $data->company_state;
        $org->postcode = $data->company_postcode;
        $org->phone = $data->company_tel;
        $org->email = $data->company_email;
        $org->country = "AU";
        $org->extra['client_billing_email'] = $data->company_email;
        $org->type = Org::TYPE_SUPPLIER;
        $org->code = strtoupper(substr($data->company_name,0,2) . rand(1,9));
        $org->cargo_driver_type = array_sum($data->delivery_city);
        $org->extra['tld_driver_waiting_approval'] = 1;
        $org->status = 0;   // set org status to inactive for now

        if ($org->save()) {
            // Save photoes
            if (!empty($data->images)) {
                $index = 0;
                foreach ($data->images as $image) {
                    $fileName = $org->name . '_truck_image_' . $index;
                    FileRepo::storeUrlFile($image, $fileName, FileRepo::SUPPLIER_TRUCK_PHOTO, $org->id);
                }
            }

            // Save truck info
            if (!empty($data->trucks)) {
                foreach ($data->trucks as $truck) {
                    $vehicle = new Vehicle();
                    $vehicle->org_id = $org->id;
                    $vehicle->plate_no = $truck->plate_no;
                    $vehicle->vehicle_model = $truck->vehicle_model;
                    $vehicle->load_capacity = $truck->load_capacity;
                    $vehicle->has_tailgate = $truck->has_tailgate;
                    if ($vehicle->has_tailgate) {
                        $vehicle->tailgate_width = $truck->tailgate_width;
                        $vehicle->tailgate_length = $truck->tailgate_length;
                        $vehicle->tailgate_weight = $truck->tailgate_weight;
                        $vehicle->inner_length = $truck->inner_length;
                        $vehicle->inner_width = $truck->inner_width;
                        $vehicle->inner_height = $truck->inner_height;
                        $vehicle->inner_volume = $truck->inner_volume;
                    }
                    $vehicle->save();
                }
            }
            return $org;
        }
        return false;
    }

    private function createUser($org_id, $user_data)
    {
        $user = new User();
        $user->type = User::DRIVER;
        $user->title = 'Mr';
        $user->fname = $user_data->first_name;
        $user->lname = $user_data->last_name;
        $user->email = $user_data->email;
        $user->since = date('Y-m-d H:i:s');
        $user->active = 0;
        $user->org_id = $org_id;
        $user->dpt_id = 106;
        $user->password = md5("deFauLtPwd333");
        return $user->save();
    }
}