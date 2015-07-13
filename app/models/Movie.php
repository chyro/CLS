<?php
//namespace Watchlist\Model;

class Movie extends \Phalcon\Mvc\Collection {
	public function getSource() { return "movies"; }

    //public $db;
    //public function initialize() { $this->db=$this->getDi()->getShared('db'); }   
    //public function test() { $result=$this->db->query("SELECT * FROM phalcon.system_users"); }
}

