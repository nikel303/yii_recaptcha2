<?php

namespace YiiRecaptcha2;

class AppComponent extends \CApplicationComponent {

	public $apiUrl = 'https://www.google.com/recaptcha/api.js';

	public $publicKey;
	public $privateKey;

	public $lang;
	public $size;
	public $theme;

	protected $scriptRegistered;

	public function init() {

		parent::init();

		if (empty($this->publicKey))
			throw new \CException(Yii::t('yii', 'Property YiiRecaptcha2\AppComponent.publicKey cannot be empty.'));

		if (empty($this->privateKey))
			throw new \CException(Yii::t('yii', 'Property YiiRecaptcha2\AppComponent.privateKey cannot be empty.'));
	}

	public function registerClientScript() {

		if (true !== $this->scriptRegistered) {

			$params = [
				'onload' => 'recaptchaLoadCallback',
				'render' => 'explicit',
			];

			if(!empty($this->lang))
				$params['hl'] = $this->lang;

			$jsCallback =  <<<EOT
(function(this){

	'use strict';
	var ReCaptchaComponent = function () {

	    var self;
	    var dfd = $.Deferred();

	    self = {

	        init: function () {
	            return dfd.resolve(self);
	        },

	        promise: function () {
	            return dfd.promise();
	        },

	        recaptcha: function () {
	            return grecaptcha;
	        }

	    };

	    return self;
	};

	this.recaptchaLoadCallback = (this.window.reCaptchaComponent = new ReCaptchaComponent()).init

})(this);
EOT;

			$cs = \Yii::app()->clientScript;
			$cs->registerScript(__CLASS__, $jsCallback, \CClientScript::POS_END);
			$cs->registerScriptFile($this->apiUrl . ([] !== $params ? '?' . http_build_query($params) : ''), \CClientScript::POS_END);
		}
	}

}