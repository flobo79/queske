<?php

class BaseController
{
    private $directed = false;
    public $template = 'default';
    public $requeststr = "";
    public $request = false;
    public $identity = false;
    public $error = false;
    public $notemplate = false;
    public $noauth = false;
    public $action = false;
    public $noauths = array();
    public $noview = false;
    public $js = array();
    public $css = array();


    function BaseController()
    {



        if (empty($_SESSION['identity'])) {
            $_SESSION['identity'] = array(); //new Users();
        }

        $this->identity = $_SESSION['identity'];

        if (!empty($_REQUEST['query'])) {
            $this->requeststr = $_REQUEST['query'];
            $pieces = explode('/', $this->requeststr);
            $this->request = (array)array_splice($pieces, 2);
        }

        $this->getAction();


        if (!in_array($this->action, $this->noauths)) {
            $this->auth();
        }

        $this->view = new View();

        $action = $this->action;
        $this->$action();
    }


    function getAction()
    {
        $action = "indexAction";

        if ($this->requeststr) {
            $path = explode('/', $this->requeststr);

            if (!empty($path[1])) {
                if (method_exists($this, strtolower($path[1]) . "Action")) {
                    $action = strtolower($path[1]) . "Action";
                } else {
                    die('Action "' . $this->action . '" does not exist');
                }
            } else {
                $action = "indexAction";
            }
        }

        $this->action = $action;
    }



    function indexAction() {

    }



    public function auth() {

        $this->identity = $_SESSION['identity'];

        if (!$this->identity->isLoggedIn()) {
            if (array_key_exists('login', $_REQUEST)) {
                if (!$this->error = $this->identity->login()) {
                    $this->redirect('index');
                }
            }

            if (!$this->identity->isLoggedIn()) {
                $this->direct("index/login");
            }
        }
    }


    // VIEW FUNCTIONS

    function addJs($file)
    {
        if (!in_array($file, $this->js)) {
            $this->js[] = $file;
        }
    }


    function addCss($file)
    {
        if (!in_array($file, $this->js)) {
            $this->css[] = $file;
        }
    }


    function direct($view = false, $layout = 'layout')
    {


        if ($this->notemplate) return;
        if (!$this->directed) {
            ob_start();

            $this->directed = true;

            if (!$layout) {
                $layout = "{content}";

            } else {
                require(BASEDIR . '/templates/' . $this->template . '/' . $layout . '.php');
                $template = ob_get_contents();
                ob_clean();
            }


            $controllername = substr(getControllerName(), 0, -10);
            $view = VIEW . '/' . ($view ? $view : strtolower($controllername) . '/' . strtolower(substr($this->action, 0, -6))) . '.phtml';

            //ob_start();
            require($view);
            $content = ob_get_contents();
            ob_clean();

            echo str_replace(array('{content}'), array($content), $template);
        }
    }


    function redirect($route) {
        header("Location:" . BASEURL . "/" . $route);
        return;
    }

    function output($html = "")
    {
        echo $html;

        $this->directed = true;
    }

    public function __str($string)
    {
        $LOC = new Localize();
        return $LOC->translate($string);
    }

    public function setTemplate($template)
    {
        $this->template = $template;
    }
}
