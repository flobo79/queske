<?php

class SurveysController extends BaseController {

    var $uploading = false;
    var $survey = false;
    var $client = false;
    var $ftp_totalfiles = false;
    var $ftp_uploaded = false;
    var $ftp_status = array();
    var $list;
    var $status;
    var $bookfolder;
    var $route = "surveys";

    function SurveysController() {

        $this->noauths = array(
          'surveyAction'
        );
        
        $this->BaseController();
    }

    /**
     * default index
     */
    function indexAction() {
        $list = $this->_getList();
        $this->list = $list;
    }


    /**
     * shows a survey
     */
    function surveyAction() {

        //$this->addJs('app/library/js/jquery.hashchange.js');

        if(!empty($this->request[0])) {
            if(!$this->survey = new Survey($this->request[0], 'url')) {
                die("survey not found");
            }
        }
    }
    
    
    /**
     * survey editor
     */
    function resultsAction() {
        if(!empty($this->request[0])) {

            $categories = new Category();
            $this->categories = $categories->getAll(' id order by title');

            $this->survey = new Questionnaire($this->request[0]);
        }
        
        
        //$this->list = $this->_getList();
    }
    
    /**
     * helper to load the book list
     *
     * @return mixed
     */
    private function _getList() {

        $surveys = new Survey();
        $where = "";

        if($this->identity->getRole() != 'admin') {
            $where = sprintf(" where user_id = %d", $this->identity->getId());
        }
        
        return $surveys->getAll($where);
    }


    /**
     * @throws Exception
     */
    function createAction() {
        $this->notemplate = true;
        $this->result = false;
        $this->error = false;
        
        $return = false;
        
        if(!empty($_REQUEST['data'])) {
            
            $start = time();
            // create Publication
            $surveys = new Survey();
            $_REQUEST['data']['created'] = time();
            $_REQUEST['data']['user_id'] = $this->identity->getId();
            
            if($surveys->insert($_REQUEST['data'])) {
               $survey_id = $surveys->getId();
               
               if(!empty($_REQUEST['data']['copy'])) {
                 if($src = new Survey($_REQUEST['data']['copy'])) {
                   foreach($src->getQuestions() as $q) {
                     $q->duplicate(array('survey_id' => $survey_id));
                   }
                 }
               }
               
               $data = $surveys->getData();
               $data['created'] = date('d.m.y, h:i', $data['created']);
               $return = array('result' => 'success', 'data' => $data);
            }
        }
        
        echo json_encode($return);
    }


    /**
     * deletes a survey
     *
     */
    function deleteAction() {
        $this->notemplate = true;
        
        if(empty($this->request[0]) || !intval($this->request[0])) {
            $this->redirect($this->route.'/index');
            return;
        }
        
        foreach(explode(",", $this->request[0]) as $id) {
            if($ent = new Survey($id)) {
               $ent->trash();
            }
        }
        
        $this->redirect($this->route."/index");
    }

    function updateAction() {
      $this->notemplate = true;
      foreach($_POST['questions'] as $q) {
        $question = new Question($q['id']);
        unset($q['survey_id'], $q['table']);
        $question->update($q);
      }
      
      $survey = new Survey($_POST['id']);
      $survey->update($_POST);
    }
    
    
    
    function addquestionAction() {
      $this->notemplate = true;
      
      $questions = new Question();
      $questions->insert($_POST);
      
      echo json_encode(array("id" => $questions->getId()));
    }
    
    
    
    function delquestionAction () {
      $this->notemplate = true;
      $q = new Question($_POST['id']);
      $q->delete();
    }
}

