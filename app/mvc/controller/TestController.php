<?php


class TestController extends BaseController {
	
	function TestController() {
		
		
	}
	
	function testAction() {
		$size = getimagesize('/Webserver/pageflipper/books/56/jpg/page_0.jpg');
		print_r($size);
	}
}
