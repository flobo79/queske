<?php

class Ftp {
    var $conn_id = false;
    var $logmsg = array();
    var $progress = 0;
    var $totalfiles = false;
    var $uploaded = 0;
    var $logfile;
    var $force;


    /**
     * establishes a ftp connection
     *
     * @param $client
     * @return resource
     */
    public function Ftp($host, $user, $pass, $logfile = false, $force=false) {
        if($logfile) {
            $this->logfile = $logfile;
            $this->log("");
        }
        $this->force = $force;

        // extract port
        $server = explode(":", $host);
        $ftp_server = $server[0];
        $ftp_port = empty($server[1]) ? 21 : $server[1];

        if(!$conn_id = ftp_connect($ftp_server)) {
            $this->log("Could not connect to $ftp_server at port $ftp_port");
            return false;
        }

        // login into FTP server
        if (!ftp_login($conn_id, trim($user), trim($pass))) {
            $this->log("Could not connect as $user:$pass to $ftp_server at port $ftp_port");
            return false;
        }

        $this->log("Connected to '".$user."@".$ftp_server."' at port ".$ftp_port);

        ftp_pasv($conn_id, true);
        $this->conn_id = $conn_id;

        return $this;
    }


    public function put ($src_dir, $dst_dir) {
        $this->totalfiles = $this->getTotalFiles($src_dir);
        $this->uploaded = 0;

        $this->upload_folder($src_dir, $dst_dir);
    }


    /**
     * uploads a folder recursively to an ftp location
     *
     * @param $conn_id
     * @param $src_dir
     * @param $dst_dir
     * @return bool
     */
    function upload_folder($src_dir, $dst_dir) {
        $d = dir($src_dir);

        $this->create_path($dst_dir);
        while(false !== ($file = $d->read())) { // do this for each file in the directory
            if ($file != "." && $file != "..") { // to prevent an infinite loop
                if (is_dir($src_dir."/".$file)) { // do the following if it is a directory
                    $this->upload_folder($src_dir."/".$file, $dst_dir."/".$file); // recursive part
                }
                else {
                    $this->__put($dst_dir."/".$file, $src_dir."/".$file, FTP_BINARY);

                }
            }
            //sleep(1);
        }
        $d->close();

        return true;
    }


    /**
     * @param $conn_id
     * @param $dst_dir
     */
    private function __put($dstfile, $srcfile, $FTP_BINARY) {

        // check if file exists
        if(ftp_size($this->conn_id, $dstfile) != -1) {
            if(!$this->force) {
                $this->log($dstfile." exists on target, skipping");
                return;
            } else {
                ftp_delete($this->conn_id, $dstfile);
                $this->log($dstfile." deleted");
            }
        }

        $this->log("uploading ".$dstfile.' ... 0%');
        if(ftp_put($this->conn_id, $dstfile, $srcfile, $FTP_BINARY)) {
            $this->log($dstfile.' ... 100%', true);

        } else {
            $error = error_get_last();
            $this->log($error['message']);
        }

        $this->uploaded = $this->uploaded+1;

        return true;
    }

    /**
     * creates a path on the target FTP location
     *
     * @param $conn_id
     * @param $path
     */
    private function create_path($path, $loghandle=false) {
        $path = explode("/", $path);
        $pieces = "";

        foreach($path as $piece) {
            $pieces .= "/".$piece;

            if(!$this->dir_exists($pieces)) {
                $this->log("creating ".$pieces);
                ftp_mkdir($this->conn_id, $pieces);
            }
        }
    }

    /**
     * checks if a dir exists on the target FTP location
     *
     * @param $conn_id
     * @param $dir
     * @return Bool
     */
    public function dir_exists($dir) {
        if(ftp_chdir($this->conn_id, $dir)) {
            return true;
        }
        return false;
    }

    public function close() {
        if ($this->conn_id) {
            ftp_close($this->conn_id);
        }
    }

    /**
     * get total files of dir
     */
    static public function getTotalFiles ($folder) {
        $i=0;
        $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folder), RecursiveIteratorIterator::SELF_FIRST);
        foreach($objects as $k => $v) {
            $i++;
        }
        return $i;
    }


    /**
     * writes a log
     *
     * @param $str
     * @param bool $replace_lastrow
     */
    public function log($str, $replace_lastrow = false) {

        // replace last row
        if($replace_lastrow) {
            $this->logmsg[count($this->logmsg)-1] = $str;

        } else {
            $this->logmsg[] = $str;
        }

        if($this->logfile) {
            // clear logfile
            if($str == "") {
                if(file_exists($this->logfile)) {
                    unlink($this->logfile);
                }
            }

            // create file if not exist
            if(!file_exists($this->logfile)) {
                touch($this->logfile);
            }

            $json = array();
            $json['uploaded'] = $this->uploaded;
            $json['total'] = $this->totalfiles;
            $json['progress'] = $this->uploaded > 0 ? (round(100 / $this->totalfiles * $this->uploaded)) : 0;
            $json['log'] = implode("\n", $this->logmsg);

            file_put_contents($this->logfile, json_encode($json));
        }
    }


    public function getLog() {
        return $this->logmsg;
    }

}