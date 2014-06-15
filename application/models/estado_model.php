<?php

class Estado_model extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    function get_estados() {
       $estados = $this->db->query("SELECT * FROM " . $this->db->dbprefix . "estado ORDER BY nome ASC ")->result();
	   return $estados;
    }

}

/* End of file estado_model.php */
    /* Location: ./application/models/estado_model.php */