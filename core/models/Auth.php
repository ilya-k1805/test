<?php

namespace Acceptic\Models;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use \PDO;

class Auth extends Model {

	protected $errors = array();

	public function validateLogin($login){
		if(strlen($login) === 0){
			$this->errors[] = 'Введите логин';
		}
		elseif(strlen($login) < 2 || strlen($login) > 20){
			$this->errors[] = 'Длинна логина должна быть от 2 до 30 символов';
		}
		elseif (!preg_match('|^[A-Z0-9_]+$|i', $login)){
			$this->errors[] = 'Логин может содержать только латинские буквы, цифры и нижнее подчеркивание';
		}
		
	}

	public function checkUser($login){

		$sth = $this->dbh->prepare('SELECT COUNT(*) FROM users WHERE login = :login');
		$sth->execute(array(':login' => $login));
		if($sth->fetchColumn() > 0){
			$this->errors[] = 'Пользователь с таким логином уже существует';
		}
		
	}

	public function validatePassword($password){
		if(strlen($password) === 0){
			$this->errors[] = 'Введите пароль';
		}
		elseif(strlen($password) < 6 || strlen($password) > 20){
			$this->errors[] = 'Длинна пароля должна быть от 6 до 50 символов';
		}
	}

	public function comparePasswords($password1, $password2){
		if(strlen($password2) === 0){
			$this->errors[] = 'Введите подтверждение пароля';
		}
		elseif($password1 !== $password2){
			$this->errors[] = 'Пароли не совпадают';
		}
	}

	public function validateEmail($email){
		if(strlen($email) === 0){
			$this->errors[] = 'Введите email';
		}
		elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)){
			$this->errors[] = 'Email введен некорректно';
		}
	}

	public function registerUser($login, $password, $email, $date){

		if(EMAIL_CONFIRM === true){
			$confirm = 0;
			$code = md5(time() . rand(1,1000));
			$send = $this->sendConfirmation($login, $email, $code);
		}
		else {
			$confirm = 1;
			$code = '';
		}

		$sth = $this->dbh->prepare('INSERT INTO users (login,password,email,date,confirmed,confirmation_code) VALUES (?,?,?,?,?,?)');
		$sth->execute(array($login, $password, $email, $date, $confirm, $code));

	}

	public function validateRegData($login, $password, $password2, $email){
		$this->validateLogin($login);
		$this->checkUser($login);
		$this->validatePassword($password);
		$this->comparePasswords($password, $password2);
		$this->validateEmail($email);

		return $this->errors;
	}

	public function login($login, $password){
		$this->validateLogin($login);
		$this->validatePassword($password);
		if(count($this->errors) === 0){
			$sth = $this->dbh->prepare('SELECT * FROM users WHERE login = :login');
			$sth->execute(array(':login' => $login));
			$user = $sth->fetch(PDO::FETCH_ASSOC);
			if(!$user || !password_verify($password, $user['password'])){
				$this->errors[] = 'Неверный логин/пароль';
			}
			elseif($user['confirmed'] == 0){
				$this->errors[] = 'Email не подтвержден';
			}
			else {
				$_SESSION['login'] = $user['login'];
			}
			
		}

		return $this->errors;
	}

	public function confirmUser($login, $code){
		$sth = $this->dbh->prepare('SELECT COUNT(*) FROM users WHERE login = ? AND confirmation_code = ?');
		$sth->execute(array($login, $code));
		if($sth->fetchColumn() < 1){
			$this->errors[] = 'Пользователь не найден';
		}
		else {
			$sth = $this->dbh->prepare('UPDATE users SET confirmed = 1 WHERE login = :login AND confirmation_code = :code');
			$sth->execute(array(':login' => $login, ':code' => $code));
		}

		return $this->errors;
	}

	public function getUserData($login){
		$sth = $this->dbh->prepare('SELECT login, email FROM users WHERE login = :login');
		$sth->execute(array(':login' => $login));
		$userData = $sth->fetch(PDO::FETCH_ASSOC);

		return $userData;
	}

	public function updateUser($old_login, $login, $password, $email){
		$sth = $this->dbh->prepare('UPDATE users SET login = :login, password = :password, email = :email WHERE login = :old_login');
		$sth->execute(array(':login' => $login, ':password' => $password, ':email' => $email, ':old_login' => $old_login));
	}

	protected function sendConfirmation($login, $email, $code){
		mail($email, 
			'Верификация пароля', 
			"Подтвердите email по <a href='".SITE_URL.BASE_URL."/auth/confirm/?login={$login}&code={$code}'>ссылке</a>", 
			"From: ".ADMIN_EMAIL
		);
	}

}