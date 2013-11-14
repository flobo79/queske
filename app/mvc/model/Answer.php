<?php

class Question extends BaseModel {

    public $id;
  	protected $table = 'questions';
    public $question;
    public $question2;
    public $survey_id;
    public $order;
    public $type;
	
  	function Question ($id=false) {
  		$this->BaseModel($this->table);
  		$this->db = $this->getDB(); 
          if($id) {
              return $this->load($id);
          }
  	}


    function load($id) {
        if($data = $this->get($id)) {
            foreach($data as $k => $v) {
                $this->$k = $v;
            }
            return $this;
        }

        return false;
    }    
    
    function create ($title, $user_id, $filename, $file, $url = '') {
        if(!file_exists($file)) {
            die("could not create ".$this->table.", file $file doenst exist");
        }

        // PREPARE DATA
        $data = array(
            'title' => $title,
            'filename' => $this->_sanitzeString(basename($filename)),
            'user_id' => $user_id,
            'created' => time(),
            'url' => $url
        );

        // SAVE BOOK TO DATABASE
        $this->db->insert($this->table, $data);
        if($this->id = $this->db->lastInsertId()) {
            $this->load($this->id);
        } else {
            die("could not save book to database");
        }


        
        return $this;
    }
    
    
    /**
     * deletes a book and its files
     *
     * @return bool
     */
    public function trash() {
        if(!$this->id) die('book not loaded');

        if($this->delete($this->id)) {
            Files::deleteFolder(dirname(BASEDIR).'/books/'.$this->getBookFolder());
        }
        return true;
    }

    

    public function getId() {
        return $this->id;
    }

    public function getUserId() {
        return $this->user_id;
    }
}
