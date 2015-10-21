<?php 

	/**
	 * @author Mehmet Salih Deveci
	 * @since 31.08.2015
	 * @copyright Mehmet Salih Deveci - msdvcii. 2015
	 * @version 1.0
	**/

	class Accounts {

		private $dbHost = "_";
		private $dbName = "_";
		private $dbUser = "_";
		private $dbPass = "_";
		private $tblName = "accounts";

		public function __construct() {
		    $dsn = 'mysql:host='.$this->dbHost.';dbname='.$this->dbName.';charset=utf8';
		    $this->db = new PDO($dsn, $this->dbUser, $this->dbPass);
		}

		public function passwordTester($user,$password) {
            $mdPass = md5($password);
			$query = $this->db->query('SELECT account_password FROM '.$this->tblName.' WHERE account_username = "'.$user.'"');
			foreach ($query as $pass) {
				if ($mdPass == $pass[0]) {
					return true;
				} else {
					return false;
				}
			}
		}

		private function infoTester($type, $info) {
			if ($type == "username") {
				$query = $this->db->query('SELECT account_username FROM '.$this->tblName.' WHERE account_username = "'.$info.'"');
				foreach ($query as $user) {
					if ($info == $user[0]) {
						return true;
					} else {
						return false;
					}
				}
			}
		}

		public function getAccounts() {
			$query = $this->db->query('SELECT * FROM '.$this->tblName.'');
			return $query;
		}

		public function getAccountInfo($id) {
			$query = $this->db->query('SELECT * FROM '.$this->tblName.' WHERE account_id="'.$id.'"');
			return $query;
		}

		public function addAccount($username, $password, $picture, $title, $area) {
            $mdPass = md5($password);
			if (!$this->infoTester('username', $username)) {
				if ( $username != "" || $password != "" || $picture != "" || $title != "" || $area != "") {
					$query = $this->db->prepare('INSERT INTO '.$this->tblName.' (account_username, account_password, account_name, account_picture, account_title, account_area, account_save_date) VALUES (:account_username, :account_password, :account_picture, :account_title, :account_title, :account_area, :account_save_date)');
					$save = date("Y-m-d");
					$query->bindParam(':account_username', $username);
					$query->bindParam(':account_password', $mdPass);
					$query->bindParam(':account_picture', $picture);
					$query->bindParam(':account_title', $title);
					$query->bindParam(':account_area', $area);
					$query->bindParam(':account_save_date', $save);
					$query->execute();
				} else {
					return "eksik birşey mi var?";
				}
				
			} else {
				echo "kullanıcı adı kullanımda";
			}	
		}
		
		public function removeAccount($id) {
			$delete = $this->db->exec('DELETE FROM '.$this->tblName.' WHERE account_id = "'.$id.'"');
		}

		public function updateAccount($id, $password, $picture, $title, $area) {
			if ( $password != "" || $picture != "" || $title != "" || $area != "") {
				$query = $this->db->prepare('UPDATE '.$this->tblName.' SET account_password = ?, account_picture = ?, account_title = ?, account_area = ? WHERE account_id = "'.$id.'"');
				$query->execute(array($password,$picture,$title,$area));
			} else {
				return "eksik birşey mi var?";
			}
		}

	}

	$account = new Accounts();
	$getAccounts = $account->getAccounts();

?>
