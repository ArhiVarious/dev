<?php
class Cabinet_UserController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->layout->setLayout('cabinet_login');
        /* Initialize action controller here */
    }

    public function indexAction()
    {  
        
    }
    
    public function loginAction()
    {
        $user = Zend_Session::namespaceGet("User");
        if($this->getRequest()->getMethod()=='POST' && $this->getParam('username') && !$user['logined'])
        {
            $oUser = new Med_Model_User();
            $arUser = $oUser->where('ds_user_name', $this->getParam('username'))->where('ds_user_password', $this->getParam('password'))->getData();
            $session = new Zend_Session_Namespace('User');
            $front = Zend_Controller_Front::getInstance();
            $options = $front->getParam('bootstrap')->getOption("cabinet");
            $session->setExpirationSeconds($options['session']['lifetime']);
            $session->data = $arUser[0];
            $user['logined'] = $session->logined = (bool)$arUser;
        }
        if(@$user['logined'])$this->_redirect('/cabinet/');
    }
    
    public function logoutAction()
    {
        $user = Zend_Session::namespaceUnset("User");
        $this->_redirect('/cabinet/');
    }
    
    public function restoreAction()
    {
        if($this->getParam('username'))
        {
            $oUser = new Med_Model_User();
            $oUser->where('ds_user_name',$this->getParam('username'));
            $newpass=$oUser->newPassword($this->getParam('username'));
            $oUser->editUser($this->getParam('username'), array('ds_user_password' =>$newpass ));
            $data=$oUser->getData()[0];
            $mail = new Profbis_Mail_Send(); 
            $mail->setTemplate('newpassword');              
            $mail->setRecipient($data['ds_user_mail']); 
            $mail->setSubject('Техническая поддержка Справмедика');
            $mail->__set('Логин',$data['ds_user_name']);
            $mail->__set('Пароль',$newpass);
            $mail->send();
            $this->_redirect('/cabinet/user/message/');
            
            
        }    
    }
    public function messageAction()
    {
   
    }
}