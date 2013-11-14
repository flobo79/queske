<?php

class Client extends BaseModel {

    private $user_id;
    protected $ftp_address;
    protected $ftp_user;
    protected $ftp_pass;
    protected $ftp_dir;
    protected $css_bgcolor;
    protected $ga_code;
    protected $js_code;
    protected $bgtype;
    protected $titlebase;


    public $table = 'clients';
	
	function Client() {
		$this->BaseModel($this->table);
	}
	
	function load($id) {
        if($data = $this->get($id)) {
            return $data;
        }
        return false;
    }

    public function getTable() {
        return $this->table;
    }

    public function getCssBgColor() {
        return $this->css_bgcolor;
    }
}
