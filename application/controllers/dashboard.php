<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $dados = array();
        //$dados['view'] = $this->load->view('usuario/visualizar', $dados, TRUE);
        $this->load->view('dashboard/interna', $dados);
    }

    public function home() {
        $this->index();
    }

	public function indexApp(){
		$dados = array();
		$this->load->view("dashboard/idexApp");
	}
    /**
     * Exibe as mensagens dos controllers.
     * Por exemplo: o controller escritorio chama este método para exibir uma mensagem de confirmação na lista
     */
    public function mensagem() {
        $dados['view'] = $this->load->view('dashboard/mensagem', array(), TRUE);
        $this->load->view('dashboard/interna', $dados);
    }

    /**
     * Exibe a página interna.
     * Este método dificilmente será chamado diretamente na URL.
     * Observe que existe uma view dashboard/interna que é carregada pelos diversos controllers para exibir o resultado dos seus processamentos.
     */
    public function interna() {
        $this->load->view('dashboard/interna');
    }

}

/* End of file dashboard.php */
/* Location: ./application/controllers/dashboard.php */