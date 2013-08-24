<?php

/**
 * ViewAutoload JsAutoload Helper
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
 * @subpackage    ViewAutoload.View.Helper
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class JsAutoloadHelper extends AppHelper {
	
	private $_defaultSettings = array(
        
    );
    /**
     *
     * @var View 
     */
	public $view = null;
	function __construct($View, $settings){
		
		$this->settings = Set::merge($this->_defaultSettings, $settings);
		$this->view = $View;
		parent::__construct($View, $settings);
		
	}
	function beforeRender($target_view){
		
		if (!empty($this->view->viewVars['JsAutoload.paths'])){
			
			foreach ($this->view->viewVars['JsAutoload.paths'] as $path => $options) {
				if (!empty($options['external'])){
                    $Html = $this->view->Helpers->load('Html');
                    $Html->script($path, ['block' => $options['block']]);
                }
                else {
                    $this->view->append($options['block'], $this->loadFile($path, $options['eval']));
                }
				
			}
		}
	}
    
    function loadFile($path, $eval = false){
        $out = "\n";
        $out .= '<script type="text/javascript">';
        if ($eval) {
            $out .= $this->_evaluate($path, $this->view->viewVars);
        }
        else {
            $out .= @file_get_contents($path);
        }
        $out .= '</script>';
        $out .= "\n";
        return $out;
    }
    
    function linkFile($path){
        $this->view->Html->script($path);
    }
    
    function _evaluate($path, $data = array()){
        extract($data);
        ob_start();
        include $path;
        return ob_get_clean();
    }
	
	
}



?>