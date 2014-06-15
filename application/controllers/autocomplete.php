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
		
		
        $busca = trim($this->input->get('q'));
		
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
	
	//função para retornar usuariose atendimentos para o autocomplete
    public function mapaAutocomplete($permissao = NULL) {
		
		$this->load->model("usuario_model");
		
		$this->load->model("atendimento_model");
		
		
        $busca = trim($this->input->get('q'));
		
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
		
		$atendimentos = $this->atendimento_model->get_atendimentos_nome()->result();
		
		if((!empty($usuarios)) || (!empty($atendimentos))){
			$arr = array();
			foreach($usuarios as $nome){
				echo urldecode($nome->nome)."\n";
			}
			
			foreach($atendimentos as $atendimento){
				echo urldecode($atendimento->titulo)."\n";
			}
		}
		else{
			echo "Não encontrado";	
		}
		
		return ;      
    }
	

    //função para retornar usuarios para o autocomplete
    public function contatosAutocomplete($tipo = "", $conteudo = "") {
		
		$this->load->model("cliente_model");
		
		
        $busca = trim($this->input->get('q'));
		
		$conteudo = urldecode($conteudo);
		
		if($tipo == "texto"){
			$a_buscar = "AND c.nome = '".$conteudo."'";
		}else if($tipo == "id"){
			$a_buscar = "AND cc.idcliente = '$conteudo'";
		}
		//pexit("teste2", $a_buscar);
		
		$idempresa = $this->session->userdata('usuario')->idempresa;
		
		// "SELECT nome FROM ".$this->db->dbprefix."usuario WHERE nome LIKE '%".$busca."%' AND idempresa = ".$idempresa." AND ativo = 'sim' ".$condicao
		$usuarios = $this->db->query("SELECT cc.contato_responsavel as nome FROM ".$this->db->dbprefix."cliente_contato as cc, ".$this->db->dbprefix."cliente as c WHERE cc.idcliente = c.idcliente ".$a_buscar." AND cc.contato_responsavel LIKE '%".$busca."%' AND idempresa = ".$idempresa." AND ativo = 'sim' ")->result();
		//pexit("teste", $this->db->last_query());
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
	

    //função para retornar clientes para o autocomplete
    public function clientesAutocomplete() {
		
		//$this->load->model("usuario_model");
		
		
        $busca = trim($this->input->get('q'));
		
		$idempresa = $this->session->userdata('usuario')->idempresa;
		
		$usuarios = $this->db->query("SELECT nome FROM ".$this->db->dbprefix."cliente WHERE nome LIKE '%".$busca."%' AND idempresa = {$idempresa} AND ativo = 'sim'")->result();
		
		if($usuarios){
			$arr = array();
			foreach($usuarios as $nome){
				pr("Query", $this->db->last_query());
				//echo urldecode($nome->nome)."\n";
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