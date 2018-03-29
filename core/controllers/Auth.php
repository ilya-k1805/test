<?php

namespace Acceptic\Controllers;

use Acceptic\Controller as Controller;

class Auth extends Controller {

	public function registration() {
		if(isset($_SESSION['login'])){
			header('Location: /cabinet');
		}
		/*Одно из полей формы, так как используется js submit() и кнопка submit не отправляется*/
		if(isset($_POST['login'])){
			$login = trim($_POST['login']);
			$password = trim($_POST['password']);
			$password2 = trim($_POST['password2']);
			$email = trim($_POST['email']);

			$date = date('d.m.y');

			$Auth_model = $this->core->loadModel('Auth');
			$errors = $Auth_model->validateRegData($login, $password, $password2, $email);

			if(count($errors) > 0){
				$data['errors'] = $errors;
				$data['login'] = $login;
				$data['email'] = $email;
			}
			else {
				$Auth_model->registerUser($login, password_hash($password, PASSWORD_DEFAULT), $email, $date);
			}
		}

		$data['title'] = 'Регистрация';

		echo $this->render('registration.html', $data);
	}

	public function login(){

		if(isset($_SESSION['login'])){
			header('Location: /cabinet');
		}

		if(isset($_POST['login'])){
			$login = trim($_POST['login']);
			$password = trim($_POST['password']);
			$Auth_model = $this->core->loadModel('Auth');
			$errors = $Auth_model->login($login, $password);
			if(count($errors) > 0){
				$data['errors'] = $errors;
				$data['login'] = $login;
			}
			else {
				header('Location: /cabinet');
			}
		}

		$data['title'] = 'Логин';

		echo $this->render('login.html', $data);
	}

	public function logout(){
		unset($_SESSION['login']);
		header('Location: /auth/login');
	}

	public function confirm(){
		$code = $_GET['code'];
		$login = $_GET['login'];

		$Auth_model = $this->core->loadModel('Auth');
		$errors = $Auth_model->confirmUser($login, $code);
		if(count($errors) > 0){
			$data['errors'] = $errors;
		}
		$data['title'] = 'Подтверждение email';

		echo $this->render('confirmed.html', $data);
	}

	public function getForm(){

		if(isset($_SESSION['login'])){
			$Auth_model = $this->core->loadModel('Auth');
			$login = $_SESSION['login'];
			$userData = $Auth_model->getUserData($login);

			$data['login'] = $userData['login'];
			$data['email'] = $userData['email'];

			echo $this->render('registration_form.html', $data);
		}
	}

	public function updateUser(){
		if(isset($_SESSION['login'])){
			$postData = file_get_contents('php://input');
			$data = json_decode($postData, true);

			$login = trim($data['login']);
			$password = trim($data['password']);
			$password2 = trim($data['password2']);
			$email = trim($data['email']);

			$Auth_model = $this->core->loadModel('Auth');
			$errors = $Auth_model->validateRegData($login, $password, $password2, $email);

			if(count($errors) > 0){
				$response['status'] = 'error';
				$response['errors'] = $errors;
			}
			else {
				$old_login = $_SESSION['login'];
				$Auth_model->updateUser($old_login, $login, password_hash($password, PASSWORD_DEFAULT), $email);
				$_SESSION['login'] = $login;
				$response['status'] = 'success';
			}
			echo json_encode($response);
		}
	}

	public function getUserData(){
		if(isset($_SESSION['login'])){
			$Auth_model = $this->core->loadModel('Auth');
			$login = $_SESSION['login'];
			$userData = $Auth_model->getUserData($login);

			echo json_encode($userData);
		}
	}

}