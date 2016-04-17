<?php

namespace YiiRecaptcha2;

use ReCaptcha;

class Validator extends \CValidator {

	public $skipOnError = true;
	public $privateKey;

	public $recaptchaComponent = 'recaptcha';
	protected $_recaptchaComponent;

	protected function validateAttribute($object, $attribute) {

		if($this->skipOnError && $object->hasErrors())
			return;

		if (null == $this->recaptchaComponent)
			throw new \CException(\Yii::t('yii', 'Property YiiRecaptcha2\Widget.recaptchaComponent can be define.'));

		$this->_recaptchaComponent = \Yii::app()->{$this->recaptchaComponent};

		$value = $object->{$attribute};

		$recaptcha = new \ReCaptcha\ReCaptcha($this->_recaptchaComponent->privateKey);
		$resp = $recaptcha->verify($value, \Yii::app()->request->getUserHostAddress());

		if(!$resp->isSuccess())
			foreach ($resp->getErrorCodes() as $code)
				$this->addError($object, $attribute, $code);
	}

}