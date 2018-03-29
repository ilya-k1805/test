<?php

namespace Acceptic\Controllers;

use Acceptic\Controller as Controller;

class Home extends Controller {

	public function index() {
		$data['title'] = 'Главная страница';
		if(isset($_SESSION['login'])){
			$data['login'] = $_SESSION['login'];
		}
		
		echo $this->render('index.html', $data);
	}


}