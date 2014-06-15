<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cidade extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

	function getCidades() {

		$this->load->model("cidade_model");

		$idestado = $this->input->post('idestado');
		$cidades = $this->cidade_model->get_cidade_by_estado($idestado);

		//pr('cidades',$cidades);

		if( empty ( $cidades ) ) 
			print '<option value="0">Não há cidades nesse estado</option>';

		foreach ($cidades as $cidade) {
			print '<option value="'.$cidade->idcidade.'">'.$cidade->nome.'</option>';
		}
		return;
	}
	
	function getEstadosJson(){
		$this->load->model("estado_model");
		$estados = json_encode($this->estado_model->get_estados());	
	}

	
}

/* End of file cliente.php */
/* Location: ./application/controllers/cliente.php */