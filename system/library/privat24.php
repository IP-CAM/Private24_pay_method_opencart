<?php

class Privat24 {
private $data = array();
    public function __construct($registry) {
        $this->session = $registry->get('session');
        if (!isset($this->session->data['privat24']) || !is_array($this->session->data['privat24'])) {
            $this->session->data['privat24'] = array();
        }
    }
}

?>