<?php

namespace smce;

use smce\lib\SmForm\SmForm;
use smce\lib\SmGump\SmGump;
use smce\base\SmLib\SmCli;

class SmFormModel extends SmCli
{
	public $error=false;
	private $lastError=false;

	public function addError($attribute,$params)
	{
		$attribute=$attribute;
		SmForm::$errorData[$attribute]=$params;
		$this->error=true;
	}

	public function validate()
	{
		$_rules=$this->Smrules();
		if(count($_rules)>0 || $this->error==true)
			return false;
		else
			return true;
	}

	private function Smrules()
	{
		if (method_exists($this, "rules")) {

			$_rules=$this->rules();
			$_lastvalid=array();

			if (is_array($this->rules())) {

				$valid=array();
				$data=array();
				foreach ($_rules as $key=>$value) {

					$validExplode=explode(",",$value[0]);

					foreach ($validExplode as $key2=>$value2) {

						if ($value[1]!=false && $value[1]!="after") {
							$value2=trim($value2);
							$valid[$value2]=trim($value[1]);
							$data[$value2]=$this->$value2;

						} else {
							if ($value[1]!="after") {
								$this->$value[2]($value2,$this->$value2);
							} else {
								$this->lastError=true;
								$_lastvalid[]=array("model"=>$value[2],
										"attribute"=>$value2,
										"value"=>$this->$value2
								);
							}

						}

					}

				}
				
				$SmGump = new SmGump();
				$SmGump->validate($data, $valid);
				$rul=$SmGump->get_readable_errors();
				if (count($rul)>0) {
					foreach ($rul as $key=>$value) {

						$this->addError($key,$value);
					}
				} elseif ($this->lastError==true && count($rul)==0) {
					if (count($_lastvalid)>0) {
						foreach($_lastvalid as $key=>$value)
							$this->$value["model"]($value["attribute"],$this->$value2);
							
					}
				}
			
			}
		}
	}
}
