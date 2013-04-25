<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of LayoutLoader
 *
 * @author Зуфар
 */
class Profbis_Controller_Plugin_LayoutLoader extends Zend_Controller_Plugin_Abstract
{
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        Zend_Registry::set('request', $request);
        $this->make_Layout($request);
    }
      
    private function make_Layout($request){
        $layout = Zend_Layout::getMvcInstance();
        $fname = APPLICATION_PATH . '/layouts/'. $request->getModuleName() . '.phtml';
        if (is_file($fname)) $name = $request->getModuleName();
        else $name = 'layout';
        $layout ->setLayout($name);
    }
}

?>
