<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Acesso_teste extends CI_Controller {

    public function index() {
        echo "Controller <strong><em>acesso</em></strong> acessado! Exibir <strong><em>view login</em></strong>... [FAZER]";
        echo "<br /><br />";
        
        // Coloquei um prefixo na configuração do BD.
        // Obser que na segunda linha não foi preciso adicionar um prefixo,
        // pois usei o padrão Active Record do CodeIgniter.
        pr("Result - usando QUERY()", $this->db->query("SELECT * FROM " . $this->db->dbprefix . "testando")->result());
        pr("Result - usando GET()", $this->db->get("testando")->result());
    }
    
}

/* End of file acesso.php */
/* Location: ./application/controllers/acesso.php */