<?php

namespace YiiRecaptcha2;

class Widget extends \CInputWidget
{

	public $recaptchaComponent = 'recaptcha';
	protected $_recaptchaComponent;

	public $publicKey;
	//
	public $lang = 'ru';
	public $size = 'normal';
	public $theme = 'light';

	public $jsCallback;

	private static $_counter = 0;
	private $_id;

	public function getId($autoGenerate = true)
	{
		if ($this->_id !== null)
			return $this->_id;
		elseif ($autoGenerate)
			return $this->_id = 'widgetRecaptcha_' . self::$_counter++;
	}

	public function run()
	{

		if (null == $this->recaptchaComponent)
			throw new \CException(Yii::t('yii', 'Property YiiRecaptcha2\Widget.recaptchaComponent can be define.'));

		$this->_recaptchaComponent = \Yii::app()->{$this->recaptchaComponent};

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
			'id' => $this->getId(),
		], '', true);
	}

	protected function registerClientScript()
	{

		$this->_recaptchaComponent->registerClientScript();

		$id = $this->htmlOptions['id'];

		$jsCode = [];
		if (empty($this->jsCallback)) {
			$jsCode[] = "var recaptchaCallback_{$id} = function(response){jQuery('#{$id}').val(response);};";
		} else {
			$jsCode[] = "var recaptchaCallback_{$id} = function(response){jQuery('#{$id}').val(response); {$this->jsCallback}(response);};";
		}

		$this->jsCallback = "recaptchaCallback_{$id}";

		$jsCode[] = "window.reCaptchaComponent.promise().then(function(c){c.widget('{$this->getId()}',{callback:'recaptchaCallback_{$id}'});});";

		$cs = \Yii::app()->clientScript;
		$cs->registerScript("recaptchaCallback_{$id}", join("\n", $jsCode), \CClientScript::POS_END);
	}

}
