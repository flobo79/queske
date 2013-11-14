<?php


class LogoutController extends BaseController {
	function LogoutController () {
		$this->BaseController();
		$_SESSION['identity'] = new User();
		$this->identity = $_SESSION['identity'];
		$this->redirect(DEFAULT_ROUTE);
	}
}
