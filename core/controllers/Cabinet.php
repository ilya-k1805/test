<?php

namespace Acceptic\Controllers;

use Acceptic\Controller as Controller;

class Cabinet extends Controller {

	public function index(){
		if(!isset($_SESSION['login'])){
			header('Location: /auth/login');
		}
		
		$login = $_SESSION['login'];
		$data['title'] = 'Личный кабинет';
		$data['user'] = $_SESSION['login'];

		echo $this->render('cabinet.html', $data);


	}

}