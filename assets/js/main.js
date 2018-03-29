'use strict'

function Form(options){
	
	if (this.constructor === Form) {
      throw new Error("Абстрактный класс");
    }

    this.form = options.elem;
    this.errors = [];

    this.form.onsubmit = function(e){
    	e.preventDefault();
    	this.resetErrors();
    	this.validate();
    	if(this.errors.length > 0){
    		this.printErrors();
    	} else {
    		this.formSubmit();
    	}
    }.bind(this);

}

Form.prototype.formSubmit = function(){
	this.form.submit();
}

Form.prototype.validateLogin = function(){
	var loginValue = this.form.login.value.trim();
	if(!loginValue){
		this.errors.push('Введите логин');
	} else if(loginValue.length < 2 || loginValue.length > 30){
		this.errors.push('Длинна логина должна быть от 2 до 30 символов');
	} else if(!/^[A-Z0-9_]+$/i.test(loginValue)){
		this.errors.push('Логин может содержать только латинские буквы, цифры и нижнее подчеркивание');
	}
}

Form.prototype.validatePassword = function(){
	var passwordValue = this.form.password.value.trim();
	if(!passwordValue){
		this.errors.push('Введите пароль');
	} else if(passwordValue.length < 6 || passwordValue.length > 50){
		this.errors.push('Длинна пароля должна быть от 6 до 50 символов');
	}
}

Form.prototype.comparePasswords = function(){
	var passwordValue = this.form.password.value.trim();
	var passwordValue2 = this.form.password2.value.trim();
	if(!passwordValue2){
		this.errors.push('Введите подтверждение пароля');
	} else if(passwordValue !== passwordValue2){
		this.errors.push('Пароли не совпадают');
	}
}

Form.prototype.validateEmail = function(){
	var emailValue = this.form.email.value.trim();
	if(!emailValue){
		this.errors.push('Введите email');
	} else if(!/^([a-z0-9_-]+\.)*[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]{2,6}$/i.test(emailValue)){
		this.errors.push('Email введен некорректно');
	}
}

Form.prototype.printErrors = function(){
	var errors = document.createElement('div');
	var error;

	this.errors.forEach(function(item) {
		error = document.createElement('p');
		error.innerHTML = item;
		errors.appendChild(error);
	}.bind(this));

	document.querySelector('.errors').appendChild(errors);
}

Form.prototype.resetErrors = function(){
	this.errors = [];
	document.querySelector('.errors').innerHTML = '';
}

Login.prototype = Object.create(Form.prototype);
Login.prototype.constructor = Login;

function Login(options){
	Form.apply(this, arguments);
}

Login.prototype.validate = function(){
	this.validateLogin();
	this.validatePassword();
}

Registration.prototype = Object.create(Form.prototype);
Registration.prototype.constructor = Registration;

function Registration(options){
	Form.apply(this, arguments);
}

Registration.prototype.validate = function(){
	this.validateLogin();
	this.validatePassword();
	this.comparePasswords();
	this.validateEmail();
}

Edit.prototype = Object.create(Registration.prototype);
Edit.prototype.constructor = Edit;

function Edit(){
	Form.apply(this, arguments);
}

Edit.prototype.formSubmit = function(){

	var data = JSON.stringify({
		login: this.form.login.value.trim(),
		password: this.form.password.value.trim(),
		password2: this.form.password2.value.trim(),
		email: this.form.email.value.trim()
	})
	
	var xhr = new XMLHttpRequest();
	xhr.open('POST', 'auth/updateUser', false);
	xhr.setRequestHeader('Content-type', 'application/json; charset=utf-8');
	xhr.send(data);
	if (xhr.status != 200) {
	  alert('Ошибка');
	} else {
		Cabinet.hideForm();
		Cabinet.updateUserData();
	}
}

function Cabinet(options){
	this.cabinet = options.elem;
	this.updateUserData();
	this.cabinet.onclick = function(e){
		var target = e.target;
		if(target.id === 'edit'){
			this.getForm();
		} else if(target.id === 'overlay'){
			this.hideForm();
		} 
	}.bind(this);
}

Cabinet.prototype.getForm = function(){
	var xhr = new XMLHttpRequest();
	xhr.open('GET', 'auth/getForm', false);
	xhr.send();
	if (xhr.status != 200) {
	  alert('Ошибка');
	} else {
		this.cabinet.querySelector('#form').innerHTML = xhr.responseText;
		this.cabinet.querySelector('#form').classList.remove('none');
		this.cabinet.querySelector('#overlay').classList.remove('none');
		this.editForm = new Edit({
			elem: document.getElementById('registration')
		});
	}
}

Cabinet.prototype.hideForm = function(){
	this.cabinet.querySelector('#form').classList.add('none');
	this.cabinet.querySelector('#overlay').classList.add('none');
}

Cabinet.prototype.updateUserData = function(){
	var xhr = new XMLHttpRequest();
	xhr.open('GET', 'auth/getUserData', false);
	xhr.send();
	if (xhr.status != 200) {
	  alert('Ошибка');
	} else {
		var data = JSON.parse(xhr.responseText);
		this.cabinet.querySelector('#userdata').innerHTML = '<p>Логин: ' + data.login + '</p><p>Пароль: ' + data.email + '</p>';
		console.log(JSON.parse(xhr.responseText))
	}
}