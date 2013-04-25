<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Autch
 *
 * @author Зуфар
 */
class Profbis_Controller_Plugin_Autch extends Zend_Controller_Plugin_Abstract {

    public function preDispatch(\Zend_Controller_Request_Abstract $request) {
        parent::preDispatch($request);
        $user = Zend_Session::namespaceGet("User");
        @$user['logined'] = (bool)$user['logined']; 
        if (!$user['logined'] && $request->getModuleName()=='cabinet' && $request->getControllerName()!='user' && !in_array($request->getActionName(), array('login','restore','message'))) {
            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
            if(!$request->isXmlHttpRequest())
            {
                $redirector->gotoUrl('/cabinet/user/login/');
            }
            else
            {
                $this->getResponse()->setHttpResponseCode(401)->sendResponse(); 
                die;
            }
        }
        elseif($user['logined'])
        {
            $session = new Zend_Session_Namespace('User');
            $front = Zend_Controller_Front::getInstance();
            $options = $front->getParam('bootstrap')->getOption("cabinet");
            $session->setExpirationSeconds($options['session']['lifetime']);
        }
        
    }

}

?>
