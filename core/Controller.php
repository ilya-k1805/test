<?php

namespace Acceptic;

class Controller {
	/** @var Core $core */
	public $core;


	/**
	 * Конструктор класса, требует передачи Core
	 *
	 * @param Core $core
	 */
	function __construct(Core $core) {
		$this->core = $core;
	}

	public function redirect($url = '/') {
		header("Location: {$url}");
		exit();
	}

	public function render($template, array $data = array()) {
		$output = '';
		if ($twig = $this->core->getTwig()) {
			try {
				$output = $twig->render($template, $data);
			}
			catch (Exception $e) {
				$this->core->log($e->getMessage());
			}
		}

		return $output;
	}

}