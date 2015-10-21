<?php

namespace YiiRecaptcha2;

class Widget extends \CInputWidget
{

	public $publicKey;
	//
	public $lang = 'ru';
	public $size = 'normal';
	public $theme = 'light';
	public $jsCallback;

	public function run()
	{

		\Yii::app()->clientScript->registerScriptFile('https://www.google.com/recaptcha/api.js?hl=' . $this->lang, \CClientScript::POS_END);

		$this->customFieldPrepare();

		echo \CHtml::tag('div', [
			'class' => 'g-recaptcha',
			'data-sitekey' => $this->publicKey,
			'data-size' => $this->size,
			'data-theme' => $this->theme,
			'data-callback' => $this->jsCallback,
		], '', true);
	}

	protected function customFieldPrepare()
	{

		echo \CHtml::activeHiddenField($this->model, $this->attribute, []);

		$inputId = \CHtml::resolveNameID($this->model, $this->attribute);


		if (empty($this->jsCallback)) {
			$jsCode = "var recaptchaCallback_{$inputId} = function(response){jQuery('#{$inputId}').val(response);};";
		} else {
			$jsCode = "var recaptchaCallback_{$inputId} = function(response){jQuery('#{$inputId}').val(response); {$this->jsCallback}(response);};";
		}

		$this->jsCallback = "recaptchaCallback_{$inputId}";

		\Yii::app()->clientScript->registerScript("recaptchaCallback_{$inputId}", $jsCode, \CClientScript::POS_END);

	}

}
