<?php

include_once("classes/items/type.message.class.php");
class TypeKbhffMessage extends TypeMessage {

	/**
	* Initialization: set variable names and validation rules.
	*
	* @return void 
	*/
	function __construct() {

		parent::__construct(get_class());


		$this->db_log = SITE_DB.".project_kbhffmessage_log";

		// // Department
		$this->addToModel("department_id", array(
			"type" => "select",
			"label" => "Afdeling",
			"required" => true,
			"hint_message" => "Vælg hvilke medlemmer der skal modtage mailen.",
			"error_message" => "Du skal vælge hvem der skal modtage mailen."
		));
	}
	
	function sendKbhffMessage($action) {

		global $UC;
		include_once("classes/users/supermember.class.php");
		$MC = new SuperMember();

		$this->getPostedEntities();

		$name = $this->getProperty("name", "value");
		$description = $this->getProperty("description", "value");
		$html = $this->getProperty("html", "value");
		$department_id = getPost("department_id", "value");

		$recipients = [];

		if($department_id == "all_departments") {
			
			// get all active members
			$members = $MC->getMembers(["only_active_members" => true]);

			// convert member list to recipients list
			foreach ($members as $member) {
				
				$member_email = $UC->getUsernames(["user_id" => $member["user"]["id"], "type" => "email"]);
				$recipients[] = $member_email ? $member_email["username"] : "";
			}

		}
		else {
			// get department users with active membership
			$users = $UC->getDepartmentUsers($department_id, ["only_active_members" => true]);

			// convert user list to recipients list
			foreach ($users as $user) {
				
				$user_email = $UC->getUsernames(["user_id" => $user["id"], "type" => "email"]);
				$recipients[] = $user_email ? $user_email["username"] : "";
			}

		}

		// create quasi message item
		$message = [
			"name" => $name,
			"description" => $description,
			"html" => $html,
			"layout" => "template-b.html"
		];

		// create final HTML
		$final_html = $this->mergeMessageIntoLayout($message);

		// send final HTML
		if(mailer()->sendBulk(["recipients" => $recipients, "subject" => $name, "html" => $final_html])) {

			global $page;
			$page->addLog("TypeKbhffMessage->sendKbhffMessage: user_id:".session()->value("user_id").", department_id:".$department_id);

			// add to Kbhff message log
			$query = new Query();
			$query->checkDbExistence($this->db_log);

			if($department_id == 'all_departments') {
				$receiving_department = "All departments";
			}
			else {
				global $DC;
				$department = $DC->getDepartment(["id" => $department_id]);
				$receiving_department = $department ? $department["name"] : false;
			}

			$sql = "INSERT INTO ".$this->db_log." SET name = '$name', recipient = '$receiving_department', html = '$html'";
			$query->sql($sql);

			// add receipt data to session
			session()->value("recipient_count", count($recipients));
			session()->value("department_id", $department_id);

			return true;
		}

		return false;
		
	}

	function getLogEntries($_options = false) {
		
		$id = false;

		if($_options !== false) {
			foreach($_options as $_option => $_value) {
				switch($_option) {
					case "id"        : $id             = $_value; break;
				}
			}
		}

		$query = new Query();
		$sql = "SELECT * FROM ".$this->db_log;

		// get specific log entry
		if($id) {
			$sql .= " WHERE id = $id";
		}

		if($query->sql($sql)) {

			if($id) {
				return $query->result(0);
			}
			
			return $query->results();
		}

		return false;
	}
}

?>