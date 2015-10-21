<?php 

	/**
	 * @author Mehmet Salih Deveci
	 * @since 03.09.2015
	 * @copyright Mehmet Salih Deveci - msdvcii. 2015
	 * @version 1.0
	**/

	class Clients {

		private $dbHost = "_";
		private $dbName = "_";
		private $dbUser = "_";
		private $dbPass = "_";
		private $tblName = "clients";
		private $Ser_tblName = "client_services";
		private $Sec_tblName = "client_sectors";

		public function __construct() {
		    $dsn = 'mysql:host='.$this->dbHost.';dbname='.$this->dbName.';charset=utf8';
		    $this->db = new PDO($dsn, $this->dbUser, $this->dbPass);
		    $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
		}

        public function getSectors() {
            $query = $this->db->query('SELECT * from '.$this->Sec_tblName.'');
			return $query;
        }

        public function getServices() {
            $query = $this->db->query('SELECT * from '.$this->Ser_tblName.'');
			return $query;
        }
        
        public function getClient($order="client_id",$id="") {
			if ($id == "") {
                $query = $this->db->query('SELECT * from '.$this->tblName.' JOIN '.$this->Ser_tblName.' ON clients.client_services=client_services.client_service_id JOIN '.$this->Sec_tblName.' ON clients.client_sector=client_sectors.client_sector_id WHERE clients.client_status = "1"  ORDER BY '.$order.' desc');
            }
			if ($id != "") {
                $query = $this->db->query('SELECT * from '.$this->tblName.' JOIN '.$this->Ser_tblName.' ON clients.client_services=client_services.client_service_id JOIN '.$this->Sec_tblName.' ON clients.client_sector=client_sectors.client_sector_id WHERE clients.client_status = "1" AND client_id = "'.$id.'"  ORDER BY '.$order.' desc');
            }
			return $query;
		}

		public function getClientInfo($id) {
			$query = $this->db->query('SELECT * FROM '.$this->tblName.' WHERE client_id="'.$id.'"');
			return $query;
		}
        
        public function currentClient($id) {
			$query = $this->db->query('SELECT * from '.$this->tblName.' JOIN '.$this->Ser_tblName.' ON clients.client_services=client_services.client_service_id JOIN '.$this->Sec_tblName.' ON clients.client_sector=client_sectors.client_sector_id WHERE clients.client_status = "1" AND client_id = "'.$id.'"');
			return $query;
        }

		public function addClient($client_name, $client_services, $client_sector, $client_phone, $client_address, $client_who_id, $client_status="1") {
			if ( $client_name != "" || $client_services != "" || $client_sector != "" || $client_phone != "" || $client_address != "") {
				$query = $this->db->prepare('INSERT INTO '.$this->tblName.' (client_name, client_services, client_sector, client_phone, client_address, client_who_id, client_save_time, client_status) VALUES (:client_name, :client_services, :client_sector, :client_phone, :client_address, :client_who_id, :client_save_time, :client_status)');
				$save = date("Y-m-d");
				$query->bindParam(':client_name', $client_name);
				$query->bindParam(':client_services', $client_services);
				$query->bindParam(':client_sector', $client_sector);
				$query->bindParam(':client_phone', $client_phone);
				$query->bindParam(':client_address', $client_address);
				$query->bindParam(':client_who_id', $client_who_id);
				$query->bindParam(':client_save_time', $save);
				$query->bindParam(':client_status', $client_status);
				$query->execute();
				return true;
			} else {
				return false;
			}
		}

		public function removeClient($id) {
			$delete = $this->db->exec('DELETE FROM '.$this->tblName.' WHERE client_id = "'.$id.'"');
		}
		
		public function updateClient($id, $client_name, $client_services, $client_sector, $client_phone, $client_address, $client_status="1") {
			$query = $this->db->prepare('UPDATE '.$this->tblName.' SET client_name = ?, client_services = ?, client_sector = ?, client_phone = ?, client_address = ?, client_status = ? WHERE client_id = "'.$id.'"');
			$query->execute(array($client_name, $client_services, $client_sector, $client_phone, $client_address, $client_status));
		}

	}

	$clients = new Clients();
	
?>
