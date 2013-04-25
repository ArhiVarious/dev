<?php

class Main_BlocksController extends Profbis_Controller_Action_Ajax {
    public function partnersAction() {
        $p = new Med_Model_Blocks();
        $this->view->partners = $p->getPartners($this->getParam('host'));
    }
    public function recomendationsAction() {
        $p = new Med_Model_Blocks();
        $this->view->adverts = $p->getAdvertBanners($this->getParam('mp_id'),$this->getParam('host'));
    }
}