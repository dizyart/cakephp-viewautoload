<?php

/**
 * ViewAutoload JsAutoload component
 *
 * Copyright 2013, DizyArt sp. (http://github.com/dizyart/)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @author DizyArt <vanja@dizyart.com>
 * @copyright     Copyright 2013, DizyArt sp. (http://github.com/dizyart/)
 * @link          http://github.com/dizyart/cakephp-viewautoload
 * @package       ViewAutoload
 * @subpackage    ViewAutoload.Controller.Component
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class JsAutoloadComponent extends Component {
	
	/**
     * A list of files passed to the helper
     * - key: Absolute path to file
     * - vaue: array of options
     * @var array
     */
    private $_loadFiles = array();
    
    /**
     * Default settings for component, to be overridden in component definition.
     * 
     * @var array
     */
	private $_defaultSettings = array(
        'block' => 'script',
        'eval' => false,
        'auto' => true
    );
	
	public function __construct(&$ComponentCollection, $settings=array()){
        $this->settings = Set::merge($this->_defaultSettings, $settings);
    }
	
	public function initialize(Controller $controller){
		if (!array_key_exists('ViewAutoload.JsAutoload', $controller->helpers)){
			$controller->helpers['ViewAutoload.JsAutoload'] = array();
		}
	}
	
	public function startup(Controller $controller){
		$paths = App::path('View');
		$this->path = $paths[0];
        if ($this->settings['auto']){
            $this->loadFile($controller->view);
        }
	}
	
    /**
     * Loads a file from the current view scope.
     * 
     * Options:
     *  - eval (bool) - whether to eval the included .js file
     *  - block (string) - the view block to which to write the file
     * 
     * @param string $name
     * @param array $options
     */
	public function loadFile($name, $options = array()){
        $options = $options + $this->settings;
		$this->_loadFiles[$name] = $options;
	}
	
	public function beforeRender(Controller $controller){
		if (empty($this->_loadFiles)) return;
		$paths = array();
		foreach ($this->_loadFiles as $file => $options) {
            $base = $this->path . $controller->viewPath . DS . $file;
            if (@file_exists($base)) {
                $paths[$base] = $options;
            }
            elseif(@file_exists($base . '.js')){
                $paths[$base.'.js'] = $options;
            }
            else {
                trigger_error("Could not include $base(.js) - file doesn't exist", E_USER_WARNING);
            }
			
		}
		$controller->set('JsAutoload.paths', $paths);
	}
    
}


?>