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
			throw new \CException(\Yii::t('yii', 'Property YiiRecaptcha2\AppComponent.publicKey cannot be empty.'));

		if (empty($this->privateKey))
			throw new \CException(\Yii::t('yii', 'Property YiiRecaptcha2\AppComponent.privateKey cannot be empty.'));
	}

	public function registerClientScript() {

		if (true !== $this->scriptRegistered) {

			$params = [
				'onload' => 'recaptchaLoadCallback',
				'render' => 'explicit',
			];

			if(!empty($this->lang))
				$params['hl'] = $this->lang;

			$widgetParams = [
				'sitekey'=> $this->publicKey,
			];

			if(!empty($this->size))
				$widgetParams['size'] = $this->size;

			if(!empty($this->theme))
				$widgetParams['theme'] = $this->theme;

			$widgetParams = \CJavaScript::jsonEncode($widgetParams);

			$jsCallback =  <<<EOT
(function(g){
	'use strict';
	var ReCaptchaComponent = function () {
	    var self, defaultParams = {$widgetParams}, dfd = $.Deferred();
	    self = {
	        init: function () {
	            return dfd.resolve(self);
	        },
	        promise: function () {
	            return dfd.promise();
	        },
	        recaptcha: function () {
	            return grecaptcha;
	        },
	        widget: function (id, params) {
				params = $.extend({}, defaultParams, params || {});
	            return grecaptcha.render(id, params);
	        }
	    };
        return self;
	};
	g.recaptchaLoadCallback = (g.window.reCaptchaComponent = new ReCaptchaComponent()).init
})(this);
EOT;

			$cs = \Yii::app()->clientScript;

			$cs->registerPackage('jquery');
			$cs->registerScript('ReCaptchaComponent', $jsCallback, \CClientScript::POS_END);
			$cs->registerScriptFile($this->apiUrl . ([] !== $params ? '?' . http_build_query($params) : ''), \CClientScript::POS_END);
		}
	}

}