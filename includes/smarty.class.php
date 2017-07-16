<?php
/**
 * Copyright by IT Stall (www.itstall.de) 2017
 * User: Dennis Eisold
 * Date: 16.07.2017
 */

require_once ('lib/smarty/Smarty.class.php');

class Template extends Smarty {
    var $Template_config = array(
            'template_dir' => 'templates/',
            'compile_dir' => 'templates_c/',
            'cache_dir' => 'cache/',
            'config_dir' => 'lib/smarty/config/',
        );
    /**
     * Konstruktor.
     * Erzeugt eine neue Smarty-Instanz und konfiguriert die Smarty-Pfade
     */
    function __construct(){
        parent::__construct();

        $this->template_dir = $this->Template_config['template_dir'];
        $this->compile_dir = $this->Template_config['compile_dir'];
        $this->cache_dir = $this->Template_config['cache_dir'];
        $this->config_dir = $this->Template_config['config_dir'];
    }

    function render($template) {
        $this->display($template . '.html');
    }
}