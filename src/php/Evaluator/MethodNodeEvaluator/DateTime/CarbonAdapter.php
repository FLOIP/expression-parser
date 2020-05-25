<?php

namespace Viamo\Floip\Evaluator\MethodNodeEvaluator\DateTime;

use Carbon\Carbon;

class CarbonAdapter extends Carbon {
	
	private static $shouldDefer;

	private $settings = [];
	
	public function settings(array $settings) {
		return $this->deferIfImplemented(__FUNCTION__, function (array $settings) {
			$this->settings = $settings;
			return $this;
		}, $settings);
	}

	private function deferIfImplemented($methodName, callable $implementation, array $args = []) {
		if (self::$shouldDefer === true) {
			return call_user_func_array([$this, "parent::$methodName"], $args);
		}
		if (\method_exists(parent::class, $methodName)) {
			self::$shouldDefer = true;
			return $this->deferIfImplemented($methodName, $implementation, $args);
		}
		self::$shouldDefer = false;
		return $implementation($args);
	}

	public function add($value) {
		return (new Carbon($this))->add($value);
	}

	public function sub($value) {
		return (new Carbon($this))->sub($value);
	}

	public function __toString() {
		return $this->deferIfImplemented('settings', function () {
			$format = (isset($this->settings['toStringFormat'])) ? 
				$this->settings['toStringFormat'] : static::$toStringFormat;
			return $this->format($format instanceof \Closure ? $format($this) : $format);
		});
	}
}
