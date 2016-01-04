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

		if (empty($this->publicKey))
			throw new \CException(Yii::t('yii', 'Property YiiRecaptcha2\Widget.publicKey cannot be empty.'));

		list($name, $id) = $this->resolveNameID();

		if (isset($this->htmlOptions['id']))
			$id = $this->htmlOptions['id'];
		else
			$this->htmlOptions['id'] = $id;

		if (isset($this->htmlOptions['name']))
			$name = $this->htmlOptions['name'];

		$this->registerClientScript();

		$this->htmlOptions['value'] = null;

		if ($this->hasModel())
			echo \CHtml::activeHiddenField($this->model, $this->attribute, $this->htmlOptions);
		else
			echo \CHtml::hiddenField($name, $this->value, $this->htmlOptions);

		echo \CHtml::tag('div', [
			'class' => 'g-recaptcha',
			'data-sitekey' => $this->publicKey,
			'data-size' => $this->size,
			'data-theme' => $this->theme,
			'data-callback' => $this->jsCallback,
		], '', true);
	}

	protected function registerClientScript()
	{

		$cs = \Yii::app()->clientScript;

		$cs->registerScriptFile('https://www.google.com/recaptcha/api.js?hl=' . $this->lang, \CClientScript::POS_END);

		$id = $this->htmlOptions['id'];

		if (empty($this->jsCallback)) {
			$jsCode = "var recaptchaCallback_{$id} = function(response){jQuery('#{$id}').val(response);};";
		} else {
			$jsCode = "var recaptchaCallback_{$id} = function(response){jQuery('#{$id}').val(response); {$this->jsCallback}(response);};";
		}

		$this->jsCallback = "recaptchaCallback_{$id}";
		$cs->registerScript("recaptchaCallback_{$id}", $jsCode, \CClientScript::POS_END);
	}

}
