<?php
/**
 * Created by JetBrains PhpStorm.
 * User: flobo
 * Date: 11.06.13
 * Time: 01:01
 */

class AdminController extends BaseController {

    var $user;

    public function AdminController() {
        $this->BaseController();
    }

    public function indexAction() {
       $users = new User();
       $clients = new Client();
       $this->user = false;
       $updated = false;
       

        if(!empty($_POST)) {

           if(!empty($_POST['password']) && $_POST['password'] != "") {
               $_POST['password'] = md5($_POST['password']);

           } else {
               unset($_POST['password']);
           }

           if(empty($_POST['ftp_pass'])) {
               unset($_POST['ftp_pass']);
           }

           if($this->request[0] == "neu") {
               $_POST['usertype'] = 'mandant';
               $users->insert($_POST);

               $_POST['user_id'] = $users->getDB()->lastInsertId();
               $this->request[0] = $_POST['user_id'];
               $clients->insert($_POST);

               $this->_saveBGImage();

               $this->redirect((BASEURL ? BASEURL."/" : "")."admin/index/".$_POST['user_id']);
               return;
           }

           else { // update
               $clients->update($_POST, $this->request[0], "user_id");
               $users->update($_POST, $this->request[0]);


               $this->_saveBGImage();
               $updated = true;
           }
        }
        
        if(!empty($this->request[0])) {
           if($this->request[0] != "neu") {
               if($user = $users->get($this->request[0])) {
                   $this->user = array_merge($user, $clients->get($this->request[0], "user_id"));
                   if($updated) {
                       $this->user['updated'] = true;
                   }
               }
           } else {
               $this->user = "neu";
           }

        }
        
        
       $this->list = $this->_mandantList();
    }



    /**
     * deletes a background image of a client
     *
     * @return string
     */
    public function deletehgAction() {
        $this->output("");
        if($this->identity->getRole() == 'admin') {
            $file = WEBROOT."/data/".$this->request[0]."/bgimage.jpg";
            if(file_exists($file)) {
                unlink($file);
                echo "ok";
                return;
            } else {
                echo "file doesnt exist";
                return;
            }
        }
        echo "not allowed";


        return;
    }

    private function _saveBGImage() {
        if(file_exists($_FILES['bgimage']['tmp_name']) && $this->request[0] != "neu") {
            //check dir
            if(!file_exists($datadir = WEBROOT.'/data/'.$this->request[0])){
                mkdir($datadir, 0777);

            }
            move_uploaded_file($_FILES['bgimage']['tmp_name'], $datadir.'/bgimage.jpg');
        }
    }

    private function _mandantList () {
        $users = new User();
        
        return $users->getAll(" id order by name");
    }
}