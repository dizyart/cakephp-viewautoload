<?php


class JsAutoloadComponent extends Component {
	
	private $_loadFiles = array();
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
    
    public function stop(){
		$this->_loadFiles = array();
	}
	
}


?>