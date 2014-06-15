<?php

class Cidade_model extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

	function get_cidade_by_estado($idestado) {
		$idestado = (int) $idestado;
		if ($idestado > 0)
            return $this->db->query("SELECT * FROM " . $this->db->dbprefix . "cidade WHERE idestado = {$idestado} ORDER BY capital DESC, nome ASC")->result();
        else
            return 0;

	}	
	
	function get_cidade_by_id($idcidade) {
		$idcidade = (int) $idcidade;
		if ($idcidade > 0)
            return $this->db->query("SELECT nome FROM " . $this->db->dbprefix . "cidade WHERE idcidade = {$idcidade} LIMIT 1")->row();
        else
            return 0;

	}		
			

}

/* End of file cidade_model.php */
    /* Location: ./application/models/cidade_model.php */