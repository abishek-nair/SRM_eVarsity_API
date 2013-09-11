<?php
	require_once("simple_html_dom.php");
	require_once("evarsityURLClass.php");
	class evarsityAPI extends evarsityURL{
		private $regNo;
		private $password;
		public $curlHandle;
		public $isServerActive;
		public $isLoggedIn;
		public function __construct($loginDetails) {
			$this->regNo = $loginDetails['uname'];
			$this->password = $loginDetails['pass'];
			$this->curlHandle = curl_init();
			$this->isServerActive = true;
			$this->isLoggedIn = false;		

			$curlOptions = array(
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HEADER => false,
				CURLOPT_COOKIEJAR => "cookies.txt",
				CURLOPT_COOKIEFILE => "cookies.txt"
			);
			curl_setopt_array($this->curlHandle, $curlOptions);					
		}
		public function evarsityLogin() {
			$curlOptions = array(
				CURLOPT_URL => $this->loginURL,
				CURLOPT_REFERER => $this->loginURL,
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => "txtSN={$this->regNo}&txtPD={$this->password}&txtPA=1"
			);
			curl_setopt_array($this->curlHandle, $curlOptions);
			$loginPage = curl_exec($this->curlHandle);
			$this->isServerActive = !preg_match("#.*scheduled\s+maintenance.*#", $loginPage);
			if(!$this->isServerActive) {
				return array("ERROR" => "SERVERDOWN");
			}
			$this->isLoggedIn = preg_match("#My Info#", $loginPage);
		}

		public function fetchStudentInfo() {
			if(!$this->isServerActive) {
				return array("ERROR" => "SERVERDOWN");
			}
			elseif(!$this->isLoggedIn) {
				return array("ERROR" => "NOTLOGGEDIN");
			}
			else {
				$stuInfoArray = array();
				$curlOptions = array(
					CURLOPT_URL => $this->studentDetailsURL,
					CURLOPT_REFERER => $this->homeURL,
					CURLOPT_HTTPGET => true
				);	
				curl_setopt_array($this->curlHandle, $curlOptions);
				$stuDetailsPage = curl_exec($this->curlHandle);
				$htmlDOM = new simple_html_dom();
				$htmlDOM->load($stuDetailsPage);
				$infoTable = $htmlDOM->find('table', 1);
				//Name and Picture
				$temp = $infoTable->find('tr', 1);
				$stuInfoArray[strtolower(str_replace(' ','_', $temp->children(0)->innertext))] = trim($temp->children(1)->innertext);
				// $stuInfoArray[strtolower(str_replace(' ','_', $temp->children(0)->innertext))] =
				// $stuInfoArray['picture'] = "<a target='_BLANK' href='http://evarsity.srmuniv.ac.in/srmswi/resource/".$temp->children(2)->find('img', 0)->src."'>Click Here</a>"; 
				$stuInfoArray['picture'] = "http://evarsity.srmuniv.ac.in/srmswi/resource/".$temp->children(2)->find('img', 0)->src; 

				//Other Details
				for($cnt = 2; $cnt <= 14; $cnt++) {
					if($temp = $infoTable->find('tr', $cnt))				
						$stuInfoArray[strtolower(str_replace('.', '', str_replace(' ','_', trim($temp->children(0)->innertext))))] = trim($temp->children(1)->innertext);
				}
				return $stuInfoArray;
			}
		}
		public function fetchStudentAttendance() {
			if(!$this->isServerActive) {
				return array("ERROR" => "SERVERDOWN");
			}
			elseif(!$this->isLoggedIn) {
				return array("ERROR" => "NOTLOGGEDIN");
			}			
			else {
				$tempArray = array();
				$headArray = array();
				$stuAttArray = array();
				$curlOptions = array(
					CURLOPT_URL => $this->studentAttendanceURL,
					CURLOPT_REFERER => $this->homeURL,
					CURLOPT_HTTPGET => true
				);
				curl_setopt_array($this->curlHandle, $curlOptions);
				$stuAttPage = curl_exec($this->curlHandle);
				$htmlDOM = new simple_html_dom();
				$htmlDOM->load($stuAttPage);
				$table = $htmlDOM->find('table', 0);
				$i = 0;
				foreach($table->find('tr') as $tr) {
					if($i > 2) {
						$key = trim($tr->find('td', 0)->plaintext);
						$tempArray = array();
						foreach($tr->find('td') as $val) {
							$tempArray[] = trim($val->plaintext);
						}
						$stuAttArray[$key] = $tempArray;
					}
					$i++;
				}
				$td = $table->find('tr', $i+1);
                $tempArray = array();
                return $stuAttArray;
			}
		}
		public function fetchStudentPerformance() {
			$stuPerfArray = array();
			if(!$this->isServerActive) {
				return array("ERROR" => "SERVERDOWN");
			}
			elseif(!$this->isLoggedIn) {
				return array("ERROR" => "NOTLOGGEDIN");
			}			
			else {
				$curlOptions = array(
					CURLOPT_URL => $this->studentPerformanceURL,
					CURLOPT_REFERER => $this->homeURL,
					CURLOPT_HTTPGET => true
				);
				curl_setopt_array($this->curlHandle, $curlOptions);
				$stuPerfPage = curl_exec($this->curlHandle);
				$htmlDOM = new simple_html_dom();
				$htmlDOM->load($stuPerfPage);			
				$table = $htmlDOM->find('table', 0);
				$i = 0;
				foreach($table->find('tr') as $tr) {
					if($i > 2) {
						$stuPerfArray[trim($tr->find('td', 0)->plaintext)] = array(
								'subject_code' => trim($tr->find('td', 0)->plaintext),
								'subject_name' => trim($tr->find('td', 1)->plaintext),
								'mark' => trim($tr->find('td', 2)->plaintext)
							);
					}
					$i++;
				}
			}
			return $stuPerfArray;
		}
	}
?>