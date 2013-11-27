<?php



spl_autoload_extensions(".php");
spl_autoload_register();

/* BOOTSTRAP OF APPLICATION FRAMEWORK */

// if possibe increase memory limit
if (!defined('MEMORY_LIMIT')) 
	define('MEMORY_LIMIT', '256M');

if (function_exists('memory_get_usage') && ( (int) @ini_get('memory_limit') < abs(intval(MEMORY_LIMIT))))
	@ini_set('memory_limit', MEMORY_LIMIT);


// try to find globals overwrite 
if (isset($_REQUEST['GLOBALS']) )
	die('GLOBALS overwrite attempt detected');



require dirname(__FILE__).'/config.php';
//require __DIR__.'/doctrine.php';



function autoload($name) {
	if(file_exists($file = CONTROLLER.'/'.ucfirst($name).'.php')) {
		include_once $file;

	} elseif(file_exists($file = LIBRARY.'/'.(string)$name.'.php')) {
		include_once $file;

	} elseif(file_exists($file = MODEL.'/'.ucfirst($name).'.php')) {
		include_once $file;
	}
}
//spl_autoload_register('autoload');



if(!isset($_SESSION)) session_start();

if(isset($_GET['reset'])) session_destroy();



// initialise PDO database
\app\library\Dba::factory((DB_TYPE.':host='.DB_HOST.'; dbname='.DB_NAME), DB_USER, DB_PASS);


function bootstrap() {
    // director
    $FrontController = getControllerName();
    $FrontController = new $FrontController();
    $FrontController->direct();
}



function getControllerName () {
	if(isset($_REQUEST['query']) && $_REQUEST['query']) {
		$path = explode('/', $_REQUEST['query']);
		if($path[0] != "") {
			if(class_exists(ucfirst($path[0]).'Controller')) {
				return ucfirst($path[0]).'Controller';
			} else {
				// Try looking for an exposed model
				if(class_exists($path[0])) {
					$classname = $path[0];
					$myclass = new $classname;
					if(in_array('exposed', get_class_vars($classname)) || $myclass->exposed == true) {
						return $path[0];
					}
				}
				
				die('Controller "'.ucfirst($path[0]).'Controller" does not exist');
			}
		}
	}
	
	return DEFAULT_ROUTE."Controller";
}




?>