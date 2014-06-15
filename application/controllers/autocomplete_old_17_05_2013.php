<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Autocomplete extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    //função para retornar usuarios para o autocomplete
    public function usuariosAutocomplete($permissao = NULL) {
		
		$this->load->model("usuario_model");
		
		
        $busca = $this->input->get('q');
		
		$idempresa = $this->session->userdata('usuario')->idempresa;
		
		if($permissao){
			$permissao = explode("-",$permissao);
			$condicao = "AND (idpermissao = {$permissao[0]} OR idpermissao = {$permissao[1]})";
			
		}
		else{
			$condicao = "";
		}
		// "SELECT nome FROM ".$this->db->dbprefix."usuario WHERE nome LIKE '%".$busca."%' AND idempresa = ".$idempresa." AND ativo = 'sim' ".$condicao
		$usuarios = $this->db->query("SELECT nome FROM ".$this->db->dbprefix."usuario WHERE nome LIKE '%".$busca."%' AND idempresa = ".$idempresa." AND ativo = 'sim' ".$condicao)->result();
		
		if($usuarios){
			$arr = array();
			foreach($usuarios as $nome){
				echo urldecode($nome->nome)."\n";
			}
		}
		else{
			echo "Usuário não encontrado";	
		}
		
		return ;      
    }
	z

    //função para retornar clientes para o autocomplete
    public function clientesAutocomplete() {
		
		//$this->load->model("usuario_model");
		
		
        $busca = $this->input->get('q');
		
		$idempresa = $this->session->userdata('usuario')->idempresa;
		
		$usuarios = $this->db->query("SELECT nome FROM ".$this->db->dbprefix."cliente WHERE nome LIKE '%".$busca."%' AND idempresa = {$idempresa} AND ativo = 'sim'")->result();
		
		if($usuarios){
			$arr = array();
			foreach($usuarios as $nome){
				echo urldecode($nome->nome)."\n";
			}
		}
		else{
			echo "Cliente não encontrado";	
		}
		
		return ;      
    }
}

/* End of file cliente.php */
/* Location: ./application/controllers/cliente.php */