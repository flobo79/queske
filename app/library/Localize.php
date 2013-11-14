<?php

/* translates given strings into other languages */

class Localize {
	protected $locale = DEFAULT_LOCALE;
	protected $catalogue = array();
	
	function Localize() {
		$this->__loadCatalogue();
	}
	
	public function translate($str) {
		return (string) !empty($this->catalogue[$str]) ? $this->catalogue[$str] : $str;
	}

	private function __loadCatalogue() {
        if($_SESSION['identity']->getLanguage()) {

            if(file_exists(LOCALE."/".$_SESSION['identity']->getLanguage().".csv")) {
                $this->locale = $_SESSION['identity']->getLanguage();
            }
        }

		if (($handle = fopen(LOCALE."/".$this->locale.".csv", "r")) !== FALSE) {
            $catalogue = array();
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				$catalogue[$data[0]] = $data[1];
			}
	
			fclose($handle);
			$this->catalogue = $catalogue;
		} else {
			throw new Exception('Localization Catalogue for '.$this->locale.' does not exist in '.LOCALE);
		}
	}
}

