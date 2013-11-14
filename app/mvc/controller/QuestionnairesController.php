<?php

class QuestionnairesController extends BaseController {

    var $uploading = false;
    var $survey = false;
    var $client = false;
    var $ftp_totalfiles = false;
    var $ftp_uploaded = false;
    var $ftp_status = array();
    var $list;
    var $status;
    var $bookfolder;
    var $route = "questionnaires";

    function QuestionnairesController() {
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
     * Questionnaire editor
     */
    function editorAction() {
        if(!empty($this->request[0])) {

            $categories = new Category();
            $this->categories = $categories->getAll(' id order by title');

            $this->survey = new Questionnaire($this->request[0]);
        }
    }

    /**
     * Questionnaire preview
     */
    function previewAction() {

        if(!empty($this->request[0])) {

            $categories = new Category();
            $this->categories = $categories->getAll(' id order by title');

            $this->survey = new Questionnaire($this->request[0]);
            return;
        }

        $this->redirect('questionnaires');
    }



    /**
     * helper to load the book list
     *
     * @return mixed
     */
    private function _getList() {
        $questionnaires = new Questionnaire();
        $where = "";

        if($this->identity->getRole() != 'admin') {
            $where = sprintf(" where user_id = %d", $this->identity->getId());
        }
        
        return $questionnaires->getAll($where);
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
            $questionnaires = new Questionnaire();
            $_REQUEST['data']['created'] = time();
            $_REQUEST['data']['user_id'] = $this->identity->getId();
            
            if($questionnaires->insert($_REQUEST['data'])) {
               $survey_id = $questionnaires->getId();
               
               if(!empty($_REQUEST['data']['copy'])) {
                 if($src = new Questionnaire($_REQUEST['data']['copy'])) {
                   foreach($src->getQuestions() as $q) {
                     $q->duplicate(array('survey_id' => $survey_id));
                   }
                 }
               }
               
               $data = $questionnaires->getData();
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

