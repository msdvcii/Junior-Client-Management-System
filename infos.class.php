<?php 

	/**
	 * @author Mehmet Salih Deveci
	 * @since 06.09.2015
	 * @copyright Mehmet Salih Deveci - msdvcii. 2015
	 * @version 1.0
	**/

	class Infos {

		private $dbHost = "_";
		private $dbName = "_";
		private $dbUser = "_";
		private $dbPass = "_";
		private $tblName = "infos";

		public function __construct() {
		    $dsn = 'mysql:host='.$this->dbHost.';dbname='.$this->dbName.';charset=utf8';
		    $this->db = new PDO($dsn, $this->dbUser, $this->dbPass);
		    $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
		}

		/**
		 * @return dizi içerisinde tüm bilgiler.
		 **/
		public function getInfos($type="current",$id="") {
            if ($id == "") {
                $query = $this->db->query('SELECT * from infos JOIN clients ON infos.info_firm=clients.client_id JOIN accounts ON infos.info_save_who=accounts.account_id WHERE infos.info_status = "1"');
            } else {
                if ($type=="current") {
                    $query = $this->db->query('SELECT * from infos JOIN clients ON infos.info_firm=clients.client_id JOIN accounts ON infos.info_save_who=accounts.account_id WHERE infos.info_status = "1" AND info_id = "'.$id.'"');
                } else {
                    $query = $this->db->query('SELECT * from infos JOIN clients ON infos.info_firm=clients.client_id JOIN accounts ON infos.info_save_who=accounts.account_id WHERE infos.info_status = "1" AND info_firm = "'.$id.'"');
                }
            }
			return $query;
		}

		/**
		 * @param info_name = Bilgi adı? (VARCHAR)
		 * @param info_firm = Hangi firma? (CLIENTS - FOREIGN KEY - INT)
		 * @param info_authority = Yetkiler, kimler görebilecek? (INT)
		 * @param info_detail_title = Kısa Başlık? (VARCHAR)
		 * @param info_detail = Bilgi içeriği? (TEXT)
		 * @param info_save_who = Kim tarafından? (ACCOUNTS - FOREIGN KEY - INT)
		 * @param info_status = Açık mı? Kapalı mı? (1/0 - INT)
		 * @param info_list_show = Liste'de görünsün mü? (1/0 - INT)
		 **/
		public function addInfo($info_name, $info_firm, $info_authority, $info_detail_title, $info_detail, $info_detail, $info_save_who, $info_status="1", $info_list_show="1") {
			$query = $this->db->prepare('INSERT INTO '.$this->tblName.' (info_name, info_firm, info_authority, info_detail_title, info_detail, info_save_who, info_status, info_list_show, info_save_date) VALUES (:info_name, :info_firm, :info_authority, :info_detail_title, :info_detail, :info_save_who, :info_status, :info_list_show, :info_save_date)');
			$save = date("Y-m-d");
			$query->bindParam(':info_name', $info_name);
			$query->bindParam(':info_firm', $info_firm);
			$query->bindParam(':info_authority', $info_authority);
			$query->bindParam(':info_detail_title', $info_detail_title);
			$query->bindParam(':info_detail', $info_detail);
			$query->bindParam(':info_save_who', $info_save_who);
			$query->bindParam(':info_status', $info_status);
			$query->bindParam(':info_list_show', $info_list_show);
			$query->bindParam(':info_save_date', $save);
			$query->execute();
		}

		/**
		 * @param id = Hangi Bilgi? (INT)
		 **/
		public function removeInfo($id) {
			$delete = $this->db->exec('DELETE FROM '.$this->tblName.' WHERE info_id = "'.$id.'"');
		}

		/**
		 * @param id = Hangi bilgi? (VARCHAR)
		 * @param info_name = (Yeni Değer) Bilgi adı? (VARCHAR)
		 * @param info_firm = (Yeni Değer) Hangi firma? (CLIENTS - FOREIGN KEY - INT)
		 * @param info_authority = (Yeni Değer) Yetkiler, kimler görebilecek? (INT)
		 * @param info_detail_title = (Yeni Değer) Kısa Başlık? (VARCHAR)
		 * @param info_detail = (Yeni Değer) Bilgi içeriği? (TEXT)
		 * @param info_save_who = (Yeni Değer) Kim tarafından? (ACCOUNTS - FOREIGN KEY - INT)
		 * @param info_status = (Yeni Değer) Açık mı? Kapalı mı? (1/0 - INT)
		 * @param info_list_show = (Yeni Değer) Liste'de görünsün mü? (1/0 - INT)
		 **/		
		public function updateInfo($id, $info_name, $info_firm, $info_authority, $info_detail_title, $info_detail, $info_status="1", $info_list_show="1") {
			$query = $this->db->prepare('UPDATE '.$this->tblName.' SET info_name = ?, info_firm = ?, info_authority = ?, info_detail_title = ?, info_detail = ?, info_detail = ?, info_status = ?, info_list_show = ? WHERE info_id = "'.$id.'"');
			$query->execute(array($info_name, $info_firm, $info_authority, $info_detail_title, $info_detail, $info_detail, $info_status, $info_list_show));
		}

	}

	$infos = new Infos();
	$getInfos = $infos->getInfos();
	
?>
