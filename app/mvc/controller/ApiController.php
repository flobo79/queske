<?php
/**
 * Created by JetBrains PhpStorm.
 * User: flobo
 * Date: 11.06.13
 * Time: 01:01
 */

class ApiController extends BaseController {

    var $user;
    var $client;
    var $error;

    public function ApiController() {
        $this->notemplate = true;
        $this->noauth = true;

        $this->BaseController();

        $clients = new Client();

        if(empty($this->request[0])) {
            return $this->error("401", "no api key", true);
        }


        $client = $clients->get($this->request[0], "api_key");
        if(!$client) {
            return $this->error("403", "wrong API key");
        }

        if($client['status'] != 0) {
            return $this->error("403", "Account blocked");
        }

        $this->client = $client;
    }



    public function indexAction() {


    }



    public function fAction() {
        $books = new Book();

        $response = array(
            'result' => "",
            'data' => ""
        );


        if(empty($_REQUEST['url'])) {
            $this->error("404", "book url not specified");
            $response['result'] = "book url not specified";
        }



        // because we explode the URL on the "/", we need to put the url back together
        $this->request[1] = ($_REQUEST['url']);

        if(!$book = $books->get(array("user_id" => $this->client['user_id'], "url" => md5($this->request[1])))) {
            if($this->_createBook()) {
                $response['result'] = "creating";
            } else {
                $response['result'] = "error";
            }

        } else {
            $book = new Book($book['id']);
            $response['result'] = "success";
            $response['data'] = array("url" => HOST.BASEURL."/books/".$book->getBookFolder());
        }

        echo json_encode($response);
    }



    private function _createBook() {
        // check if client is allowed to created books
        // fetch URL from Server
        $url = $this->request[1];
        $books = new Book();

        if($this->_remoteFileExists($url)) {
            if($tmpfile = $this->_downloadFile($url)) {
                if($book = $books->create(basename(urldecode($url), '.pdf'), $this->client['user_id'], urldecode($url), $tmpfile, md5($url))) {

                    return true;
                }
            } else {
                die("could not download file ".$url);
            }
        } else {
            // something went wrong, file not found
            return false;
        }
    }



    private function _remoteFileExists($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->error = "file not found";
        return $code == 200 ? true : false;
    }


    private function _downloadFile($src_url) {
        $url  = $src_url;
        $path = TMPDIR."/".md5($src_url);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        file_put_contents($path, $data);
        curl_close($ch);

        return $path;
    }




    private function error($code, $message, $fatal = true) {
        header("HTTP/1.0 ".$code." ".$message);
        if($fatal) die($message);
    }
}