<?php

/**
 * @author DizyArt 
 */
class JSautoloadHelper extends AppHelper {
	
	private $_defaultSettings = array(
        
    );
	public $view = null;
	function __construct($View, $settings){
		
		$this->settings = Set::merge($this->_defaultSettings, $settings);
		$this->view = $View;
		parent::__construct($View, $settings);
		
	}
	function beforeRender($target_view){
		
		if (!empty($this->view->viewVars['JsAutoload.paths'])){
			
			foreach ($this->view->viewVars['JsAutoload.paths'] as $path => $options) {
				
                $this->view->append($options['block'], $this->loadFile($path, $options['eval']));
				
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
    
    function _evaluate($path, $data = array()){
        extract($data);
        ob_start();
        include $path;
        return ob_get_clean();
    }
	
	
}



?>