<?php


class IndexController extends BaseController {
	function IndexController () {
		$this->BaseController();
        $this->noauth = true;

        $this->auth();

	}
	
	function loginAction() {
  	
  	
	}
}
