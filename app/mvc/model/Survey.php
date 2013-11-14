<?php


/**
 * Class Survey
 *
 * a Survey is a Questionnaire sent to a Questionee
 */

class Survey extends BaseModel {

    protected $id;
    public $data;
    public $questionee;
    public $questionnaire;
    public $table = 'surveys';
    public $user_id;
    protected $created;


  	function __construct ($id=false, $field=false) {
  		$this->BaseModel($this->table);
  		$this->db = $this->getDB();
          if($id) {
              return $this->load($id, $field);
          }
  	}


    function load($id, $field=false) {

        if($data = $this->get($id, $field)) {
          
          //if($this->getIdentity()->isAdmin() || $data->user_id == $this->getIdentity()->getId()) {
          
            foreach($data as $k => $v) {
                $this->$k = $v;
            }

            return $this;
          /*
          } else {
            throw new Exception("Entity doesn't belong to user");
          }
          */
        }

        return false;
    }
    
    
    
    public function getQuestions() {
        $questions = new Question();
        $list = $questions->getAll("survey_id = ".$this->id." order by `order`");
        
        foreach($list as $e => $q) {
          $list[$e] = new Question($q->id);
        }
        
        return $list;
    }
        


    /**
     * deletes a survey and its attachments
     *
     * @return bool
     */
    public function trash() {
        if(!$this->id) die('survey not loaded');
        
        foreach($this->getQuestions() as $q) {
          $q->delete();
        }
        
        
        if($this->delete()) {
            //
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
