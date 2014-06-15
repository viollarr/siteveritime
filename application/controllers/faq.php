<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Faq extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    function index() {
        $this->lista();
    }

    function lista() {		
        $dados = array();
		$dados['view'] = $this->load->view('faq/lista', $dados, TRUE);
        $this->load->view('dashboard/interna', $dados);
    }
   
}

/* End of file atendimento.php */
/* Location: ./application/controllers/faq.php */