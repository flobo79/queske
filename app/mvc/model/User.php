<?php

class User extends BaseModel {

    protected $loggedIn = false;
    protected $name;
    protected $login;
    protected $password;
    protected $usertype;
    protected $id;
    protected $language;
    protected $table = 'users';
	
	function User() {
		$this->BaseModel($this->table);
	}
	
	function Login() {
		if($r = $_REQUEST['login']) {
			$db = $this->getDB();
            $q = "select * from ".$this->table." where
				login = ".$db->quote($r['login'])." and
				password = '".md5($r['password'])."'";

			$row = $db->fetchQuery($q);

			if(!empty($row['id'])) {
				$this->loggedIn = true;

                foreach($row as $e => $v) {
                    $this->$e = $v;
                }


			} else {
				return "User not found";
			}
		}

        return $this;
	}

    public function getName() {
        return $this->name;
    }

    public function getLogin() {
        return $this->login;
    }

    public function getPassword() {
        return $this->password;
    }

	function isLoggedIn() {
		return $this->loggedIn;
	}

    public function getId() {
        return $this->id;
    }

    public function getRole() {
        return $this->usertype;
    }
    public function getTable() {
        return $this->table;
    }
    public function getLanguage() {
       return $this->language;
    }

    public function setLanguage($val) {
        return $this->language = $val;
    }
    
    public function isAdmin() {
      return $this->getRole() == "admin";
    }
}
