<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Reports
 *
 * @author Зуфар
 */
class Cabinet_ReportsController extends Zend_Controller_Action {

    public function init() {
        $this->user = Zend_Session::namespaceGet("User");
        $this->view->child_menu = (new Med_Model_Cabinet)->getFirmsByUID($this->user['data']['ds_user_id']);
        $this->view->firm_id = $this->getParam('id');
        if ($this->getParam('json')) {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
        }
        $this->view->headScript()->appendFile('/spina/js/cabinet.js');
    }

    public function demandanddirectionAction() {

        $page = $this->getParam('page') ? $this->getParam('page') : 1;
        $np = 150;

        $sort = array();
        if ($this->getParam('key') && !is_array($this->getParam('key'))) {
            $sort[$this->getParam('key')] = $this->getParam('sort');
        } elseif (is_array($this->getParam('key'))) {
            $arParams = $this->getAllParams();
            foreach ($arParams['key'] as $i => $key)
                $sort[$key] = $arParams['sort'][$i];
        }
        $filter = $this->getParam('filter');
        $filter = is_array($filter) ? $filter : array();
        $o = new Med_Model_Reports();
        $dd = new Med_Model_Drugstore();
        $data = $o->getDemandAndDirection($this->getParam('id'), $dd->getCityByDDId($this->getParam('id')), $this->user['data']['ds_user_id'], $page, $np, $sort, $filter);

        if ($this->getParam('json'))
            $this->_helper->json(array('meta' => array(
                    'cnt' => $data['cnt'][0],
                    'page' => $page,
                    'np' => $np,
                    'start' => (($page - 1) * $np) + 1,
                    'end' => $page * $np
                ), 'result' => $data['data']));
        if(isset($data['data']))
            $this->view->table = $data['data'];
        $this->view->table_meta = array(
            'cnt' => $data['cnt'][0],
            'page' => $page,
            'np' => $np,
            'start' => (($page - 1) * $np) + 1,
            'end' => $page * $np
        );
    }
    
        public function directionsonfirmAction() {

        $page = $this->getParam('page') ? $this->getParam('page') : 1;
        $np = 150;

        $sort = array();
        if ($this->getParam('key') && !is_array($this->getParam('key'))) {
            $sort[$this->getParam('key')] = $this->getParam('sort');
        } elseif (is_array($this->getParam('key'))) {
            $arParams = $this->getAllParams();
            foreach ($arParams['key'] as $i => $key)
                $sort[$key] = $arParams['sort'][$i];
        }
        $filter = $this->getParam('filter');
        $filter = is_array($filter) ? $filter : array();
        $o = new Med_Model_Reports();
        $dd = new Med_Model_Drugstore();
        $data = $o->getDirectonsOnFirm($this->getParam('id'), $dd->getCityByDDId($this->getParam('id')), $this->user['data']['ds_user_id'], $page, $np, $sort, $filter);

        if ($this->getParam('json'))
            $this->_helper->json(array('meta' => array(
                    'cnt' => $data['cnt'][0],
                    'page' => $page,
                    'np' => $np,
                    'start' => (($page - 1) * $np) + 1,
                    'end' => $page * $np
                ), 'result' => $data['data']));
        if(isset($data['data']))
            $this->view->table = $data['data'];
        $this->view->table_meta = array(
            'cnt' => $data['cnt'][0],
            'page' => $page,
            'np' => $np,
            'start' => (($page - 1) * $np) + 1,
            'end' => $page * $np
        );
    }
    
        public function presenceAction() {

        $page = $this->getParam('page') ? $this->getParam('page') : 1;
        $np = 150;

        $sort = array();
        if ($this->getParam('key') && !is_array($this->getParam('key'))) {
            $sort[$this->getParam('key')] = $this->getParam('sort');
        } elseif (is_array($this->getParam('key'))) {
            $arParams = $this->getAllParams();
            foreach ($arParams['key'] as $i => $key)
                $sort[$key] = $arParams['sort'][$i];
        }
        $filter = $this->getParam('filter');
        $filter = is_array($filter) ? $filter : array();
        $o = new Med_Model_Reports();
        $dd = new Med_Model_Drugstore();
        $data = $o->getPresence($this->getParam('id'), $dd->getCityByDDId($this->getParam('id')), $this->user['data']['ds_user_id'], $page, $np, $sort, $filter);

        if ($this->getParam('json'))
            $this->_helper->json(array('meta' => array(
                    'cnt' => $data['cnt'][0],
                    'page' => $page,
                    'np' => $np,
                    'start' => (($page - 1) * $np) + 1,
                    'end' => $page * $np
                ), 'result' => $data['data']));
        if(isset($data['data']))
            $this->view->table = $data['data'];
        
        $this->view->table_meta = array(
            'cnt' => $data['cnt'][0],
            'page' => $page,
            'np' => $np,
            'start' => (($page - 1) * $np) + 1,
            'end' => $page * $np
        );
    }

    public function outsAction() {

        $page = $this->getParam('page') ? $this->getParam('page') : 1;
        $np = 150;

        $sort = array();
        if ($this->getParam('key') && !is_array($this->getParam('key'))) {
            $sort[$this->getParam('key')] = $this->getParam('sort');
        } elseif (is_array($this->getParam('key'))) {
            $arParams = $this->getAllParams();
            foreach ($arParams['key'] as $i => $key)
                $sort[$key] = $arParams['sort'][$i];
        }
        $filter = $this->getParam('filter');
        $filter = is_array($filter) ? $filter : array();
        $o = new Med_Model_Reports();
        $dd = new Med_Model_Drugstore();
        $data = $o->getOuts($this->getParam('id'), $dd->getCityByDDId($this->getParam('id')), $this->user['data']['ds_user_id'], $page, $np, $sort, $filter);

        if ($this->getParam('json'))
            $this->_helper->json(array('meta' => array(
                    'cnt' => $data['cnt'][0],
                    'page' => $page,
                    'np' => $np,
                    'start' => (($page - 1) * $np) + 1,
                    'end' => $page * $np
                ), 'result' => $data['data']));
        if(isset($data['data']))
            $this->view->table = $data['data'];
        $this->view->table_meta = array(
            'cnt' => $data['cnt'][0],
            'page' => $page,
            'np' => $np,
            'start' => (($page - 1) * $np) + 1,
            'end' => $page * $np
        );
    }

}

?>
