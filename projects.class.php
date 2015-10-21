<?php 

	/**
	 * @author Mehmet Salih Deveci
	 * @since 09.09.2015
	 * @copyright Mehmet Salih Deveci - msdvcii. 2015
	 * @version 1.0
	**/

	class Projects {

		private $dbHost = "_";
		private $dbName = "_";
		private $dbUser = "_";
		private $dbPass = "_";
		private $tblName = "projects";
		private $tblName_R = "responsibles";

		public function __construct() {
		    $dsn = 'mysql:host='.$this->dbHost.';dbname='.$this->dbName.';charset=utf8';
		    $this->db = new PDO($dsn, $this->dbUser, $this->dbPass);
		    $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
		}

		public function getProjects($order="project_last_update",$id="") {
			if ($id == "") {
                $query = $this->db->query('SELECT * from '.$this->tblName.' WHERE status = "1" ORDER BY '.$order.' desc');
            }
			if ($id != "") {
                $query = $this->db->query('SELECT * from '.$this->tblName.' WHERE project_id = "'.$id.'"');
            }
			return $query;
		}

        public function getFeaturingProject($limit, $order, $priority, $tamam) {
            if ($limit != "") { $limit = 'LIMIT '.$limit.''; }
            if ($order != "") { $order = 'ORDER BY '.$order.''; }  
            if ($tamam != "") { $tamam = 'AND project_finish_status = "1"'; }  
            if ($priority != "") { 
                $priority = 'AND project_priority = "'.$priority.'"';
                $query = $this->db->query('SELECT * from '.$this->tblName.' WHERE status = "1" '.$tamam.' '.$priority.' '.$order.' '.$limit.' '); 
            } else {
                $query = $this->db->query('SELECT * from '.$this->tblName.' WHERE status = "1" '.$tamam.' '.$order.' ');
            }

            return $query;
        }

		public function getForUserIdProjects($account_id) {
			$query = $this->db->query('SELECT * from '.$this->tblName.' JOIN responsibles ON projects.project_id=responsibles.project_id JOIN accounts ON responsibles.account_id=accounts.account_id WHERE projects.status = "1" AND responsibles.account_id = "'.$account_id.'" AND project_approval_status = "1" ');
			return $query;
		}

		public function getResponsibles($id) {
			$query = $this->db->query('SELECT account_username, account_name, account_picture, account_title, account_authority, account_area from '.$this->tblName_R.' JOIN accounts ON '.$this->tblName_R.'.account_id=accounts.account_id WHERE project_id="'.$id.'" AND status = "1"');
			return $query;
		}
        
        public function getLastProjectId() {
            $query = $this->db->query('SELECT project_id FROM projects ORDER BY project_id DESC LIMIT 1');
            foreach($query as $q) {
                return $q;
            }
        }
        
        public function askResponsible($id,$aid) {
			$query = $this->db->query('SELECT * FROM '.$this->tblName_R.' WHERE project_id ="'.$id.'" AND account_id ="'.$aid.'" AND status="1"');
            if ($query->rowCount() != 0) {
                return true;
            } else {
                return false;
            }
        }
        
        public function responsiblesEmpty($id) {
			$query = $this->db->query('SELECT * from '.$this->tblName_R.' WHERE project_id = "'.$id.'" ');
			return $query->rowCount();
        }

		public function addProject($project_title, $project_description, $project_start, $project_deadline, $project_firm, $project_priority, $who, $accounts, $project_finish_status="1", $project_approval_status="1", $status="1") {
            date_default_timezone_set('Europe/Istanbul');
			$project_last_update = date("Y-m-d G:i:s");
			$query = $this->db->prepare('INSERT INTO '.$this->tblName.' (project_title, project_description, project_start, project_deadline, project_firm, project_priority, project_finish_status, project_approval_status, project_last_update, project_who, status) VALUES (:project_title, :project_description, :project_start, :project_deadline, :project_firm, :project_priority, :project_finish_status, :project_approval_status, :project_last_update, :project_who, :status)');
			$query->bindParam(':project_title', $project_title);
			$query->bindParam(':project_description', $project_description);
			$query->bindParam(':project_start', $project_start);
			$query->bindParam(':project_deadline', $project_deadline);
			$query->bindParam(':project_firm', $project_firm);
			$query->bindParam(':project_priority', $project_priority);
			$query->bindParam(':project_finish_status', $project_finish_status);
			$query->bindParam(':project_approval_status', $project_approval_status);
			$query->bindParam(':project_last_update', $project_last_update);
			$query->bindParam(':project_who', $who);
			$query->bindParam(':status', $status);
			$query->execute();
            $status=1;
            $lastId = $this->db->lastInsertId();
			if(count($accounts) > 0) {
				foreach ($accounts as $account) {
					$add = $this->db->prepare('CALL addResponsibles(?, ?, ?)');
					$add->bindParam(1, $lastId, PDO::PARAM_INT);
					$add->bindParam(2, $account, PDO::PARAM_INT);
					$add->bindParam(3, $status, PDO::PARAM_INT);
					$add->execute();
				}
			}
            if ($query) return $lastId;
		}

		public function addResponsibles($project_id, $account_id, $status=1) {
            if (!$this->askResponsible($project_id, $account_id)) {
                $query = $this->db->prepare('INSERT INTO '.$this->tblName_R.' (project_id, account_id, status) VALUES (:project_id, :account_id, :status)');
                $query->bindParam(':project_id', $project_id);
                $query->bindParam(':account_id', $account_id);
                $query->bindParam(':status', $status);
                $query->execute();
            }
		}

		public function updateProject($id, $project_title, $project_description, $project_start, $project_deadline, $project_firm, $project_priority, $project_finish_status="1", $project_approval_status="1", $project_who, $status="1") {
            date_default_timezone_set('Europe/Istanbul');
			$project_last_update = date("Y-m-d G:i:s");
			$query = $this->db->prepare('UPDATE '.$this->tblName.' SET project_title = ?, project_description = ?, project_start = ?, project_deadline = ?, project_firm = ?, project_priority = ?, project_finish_status = ?, project_approval_status = ?, project_last_update = ?, project_who = ?, status = ? WHERE project_id = "'.$id.'"');
			$sql = $query->execute(array($project_title, $project_description, $project_start, $project_deadline, $project_firm, $project_priority, $project_finish_status, $project_approval_status, $project_last_update, $project_who, $status));
            if ($sql) return true; 
		}

		public function updateResponsibles($id, $project_id, $account_id) {
			$query = $this->db->prepare('UPDATE '.$this->tblName_R.' SET project_id = ?, account_id = ? WHERE responsible_id = "'.$id.'"');
			$query->execute(array($project_id, $account_id));
		}

		public function removeProject($id) {
			$query = $this->db->prepare('DELETE FROM '.$this->tblName.' WHERE project_id = "'.$id.'"');
			$query->execute();
		}
        
		public function removeResponsibles($id) {
			$query = $this->db->prepare('DELETE FROM '.$this->tblName_R.' WHERE project_id = "'.$id.'"');
			$query->execute();
		}

		public function getPriorityValue($id) {
            switch($id) {
                case 1:
                    echo "label-danger";
                    break;
                case 2:
                    echo "label-warning";
                    break;
                case 3:
                    echo "label-info";
                    break;
                case 4:
                    echo "label-success";
            }
		}
        
        public function checkedProject($id) {
            switch($id) {
                case 2:
                    return true;
                    break;
                case 1:
                    return false;
                    break;
            }
        }
        
        public function projectFinishMail($project_title, $project_start_date, $project_last_update, $project_who_end, $project_id, $sorumlular, $firma, $finish_date) {
            
			include("class.phpmailer.php");
            $mail = new PHPMailer();
            $mail->IsSMTP();
            $mail->SMTPAuth = true;
            $mail->Host = 'mail.crabsmedia.com';
            $mail->Port = 587;
            $mail->SMTPSecure = 'tls';
            $mail->Username = 'smtp@crabsmedia.com';
            $mail->Password = '0kgox2lc';
            $mail->SetFrom($mail->Username, 'Crabs Media - CRM');
            $mail->AddAddress('mehmet.deveci@crabsmedia.com', 'Mehmet Salih Deveci');
            $mail->AddAddress('info@crabsmedia.com', 'Crabs Media');
            $mail->CharSet = 'UTF-8';
            $mail->Subject = $project_title.' tamamlandı! - jCRM';
            $content = '
                <span style="font-family:trebuchet ms; font-size:12px;">
                <strong>Merhaba</strong>;
                <br><br>
                '.$project_title.' adlı projemiz; '.$project_start_date.' tarihinde başlamış ve <strong>'.$project_last_update.'</strong> tarihinde '.$project_who_end.' tarafından onaylanarak tamamlanmıştır.
                <br><br>
                <strong>Detay Sayfası Linki</strong>: http://crm.crabsmedia.com/detail.php?type=project&id='.$project_id.' <br>
                <strong>Sorumlular</strong>: '.$sorumlular.'<br>
                <strong>Yapıldığı Firma</strong>: '.$firma.'<br>
                <strong>Planlanan Bitiş Tarihi</strong>: '.$finish_date.'<br>
                <br><br>
                <span style="font-size:11px;">(*)Bu bir jcrm iletisidir, geri dönmeyiniz!</span>
                </span>
            ';
            $mail->MsgHTML($content);
            if($mail->Send()) {
            } else {
                // bir sorun var, sorunu ekrana bastıralım
                echo $mail->ErrorInfo;
            }
        }
        
        public function projectAppMail($project_title, $project_start_date, $project_last_update, $project_who_end, $project_id, $sorumlular, $firma, $finish_date) {
            
			include("class.phpmailer.php");
            $mail = new PHPMailer();
            $mail->IsSMTP();
            $mail->SMTPAuth = true;
            $mail->Host = 'mail.crabsmedia.com';
            $mail->Port = 587;
            $mail->SMTPSecure = 'tls';
            $mail->Username = 'smtp@crabsmedia.com';
            $mail->Password = '0kgox2lc';
            $mail->SetFrom($mail->Username, 'Crabs Media - CRM');
            $mail->AddAddress('mehmet.deveci@crabsmedia.com', 'Mehmet Salih Deveci');
            //$mail->AddAddress('info@crabsmedia.com', 'Crabs Media');
            $mail->CharSet = 'UTF-8';
            $mail->Subject = $project_title.' onay bekliyor! - jCRM';
            $content = '
                <span style="font-family:trebuchet ms; font-size:12px;">
                <strong>Merhaba</strong>;
                <br><br>
                '.$project_title.' adlı projemiz; '.$project_start_date.' tarihinde başlamış ve <strong>'.$project_last_update.'</strong> tarihinde '.$project_who_end.' tarafından tamamlandı olarak işaretlendi, onayınız bekleniyor.
                <br><br>
                <strong>Detay Sayfası Linki</strong>: http://crm.crabsmedia.com/detail.php?type=project&id='.$project_id.' <br>
                <strong>Sorumlular</strong>: '.$sorumlular.'<br>
                <strong>Yapıldığı Firma</strong>: '.$firma.'<br>
                <strong>Planlanan Bitiş Tarihi</strong>: '.$finish_date.'<br>
                <br><br>
                <span style="font-size:11px;">(*)Bu bir jcrm iletisidir, geri dönmeyiniz!</span>
                </span>
            ';
            $mail->MsgHTML($content);
            if($mail->Send()) {
            } else {
                // bir sorun var, sorunu ekrana bastıralım
                echo $mail->ErrorInfo;
            }
        }


	}

	$projects = new Projects();
	
?>
