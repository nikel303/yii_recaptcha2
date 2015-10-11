<?php

namespace YiiRecaptcha2;

use ReCaptcha;

class Validator extends \CValidator {

	public $skipOnError = true;
	public $privateKey;

	protected function validateAttribute($object, $attribute) {

		$value = $object->{$attribute};

		$recaptcha = new \ReCaptcha\ReCaptcha($this->privateKey);
		$resp = $recaptcha->verify($value, \Yii::app()->request->getUserHostAddress());

		if(!$resp->isSuccess())
			foreach ($resp->getErrorCodes() as $code)
				$this->addError($object, $attribute, $code);
	}

}