<?php

class View {
    public $template = 'default';
    public $notemplate = false;
    public $directed = false;


    function __construct() {




    }

    function render($view=false, $layout='layout') {

        if($this->notemplate) return;

        if(!$this->directed) {
            ob_start();

            $this->directed = true;

            if(!$layout) {
                $layout = "{content}";

            } else {
                require(BASEDIR.'/templates/'.$this->template.'/'.$layout.'.php');
                $template = ob_get_contents();
                ob_clean();
            }


            $controllername = substr(getControllerName(),0, -10);
            $view = VIEW.'/'.($view ? $view : strtolower($controllername).'/'.strtolower(substr($this->action,0,-6))).'.phtml';

            //ob_start();
            require($view);
            $content = ob_get_contents();
            ob_clean();

            echo str_replace(array('{content}'), array($content), $template);
        }
    }

    public function __str($string) {
        $LOC = new Localize();
        return $LOC->translate($string);
    }

    public function setTemplate($template) {
        $this->template = $template;
    }
}