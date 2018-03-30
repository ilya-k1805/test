<?php

/*Адрес сайта*/
if (!defined('SITE_URL')) {
	define('SITE_URL', 'http://localhost');
}

/*Путь к проекту*/
if (!defined('BASE_URL')) {
	define('BASE_URL', '/');
}

/*Подтверждение по email*/
if (!defined('EMAIL_CONFIRM')) {
	define('EMAIL_CONFIRM', true);
}

if (!defined('PROJECT_BASE_PATH')) {
	define('PROJECT_BASE_PATH', strtr(realpath(dirname(dirname(__DIR__))), '\\', '/') . '/');
}

/*Путь к шаблонам*/
if (!defined('VIEWS_PATH')) {
	define('VIEWS_PATH', PROJECT_BASE_PATH . 'core/views/');
}

/*Путь к кешу Twig*/
if (!defined('TEMPLATES_CACHE')) {
	define('TEMPLATES_CACHE', PROJECT_BASE_PATH . 'core/cache/');
}

if (!defined('TWIG_AUTO_RELOAD')) {
	define('TWIG_AUTO_RELOAD', true);
}

if (!defined('ADMIN_EMAIL')) {
	define('ADMIN_EMAIL', 'webmaster@gmail.com');
}

/*Настройки БД*/
return [
    'database' => [
        'host' => 'localhost',
        'dbname' => 'acceptic',
        'user' => 'root',
        'pass' => '',
        'char' => 'utf8'
    ]
];