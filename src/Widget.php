<?php

namespace YiiRecaptcha2;

class Widget extends \CWidget {

	public $publicKey;
	//
	public $lang = 'ru';
	public $size = 'normal';
	public $theme = 'light';

	public function run() {

		\Yii::app()->clientScript->registerScriptFile('https://www.google.com/recaptcha/api.js?hl=' . $this->lang, \CClientScript::POS_END);

		echo \CHtml::tag('div', [
			'class' => 'g-recaptcha',
			'data-sitekey' => $this->publicKey,
			'data-size' => $this->size,
			'data-theme' => $this->theme,
		], '', true);
	}

}
