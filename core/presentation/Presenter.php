<?php
/**
 * @copyright 2014 Mark LeBlanc
 * @author  Mark LeBlanc <mark@compute.systems>
 */
namespace framework\core\presentation;

/**
 * Handles presentation logic.
 */
class Presenter {
	/**
	 * A collection of dynamically set attributes.
	 */
	private $attributes;

	/**
	 * The layouts, either partial or full layouts.
	 */
	private $layouts;

	public function __construct($attributes = array()) {
		$this->attributes = $attributes;
	}

	public function with($layout) {
		$layout = str_replace('.', DIRECTORY_SEPARATOR, $layout);
		$this->layouts[] = PATH . 'application' . DIRECTORY_SEPARATOR . 'views' .
							DIRECTORY_SEPARATOR . $layout . '.tpl';
		return $this;
	}

	/**
	 * Renders the content.
	 * @param  boolean $buffered Whether to buffer data.
	 */
	public function render($isBuffered) {
		if(count($this->attributes) != 0) extract($this->attributes);
		ob_start();
		if(count($this->layouts) != 0) {
			foreach($this->layouts as $layout) {
				if(is_readable($layout))
					require($layout);
			}
		}

		$content = ob_get_clean();
		if(!$isBuffered) echo $content;

		return $content;
	}

	/**
	 * Sets a dynamic variable in our internal array, will overwrite
	 * values with identical keys.
	 * @param string $key   The key or name of the value.
	 * @param mixed $value The value being stored.
	 */
	public function __set($key, $value) {
		$this->attributes[$key] = $value;
	}

	/**
	 * Retrieves dynamic variables from an internal array.
	 * @param  string $key The key or name of the value that is stored.
	 * @return mixed      The value, or false if none exist.
	 */
	public function __get($key) {
		if(!array_key_exists($key, $this->attributes))
			return false;
		return $this->attributes[$key];
	}
}