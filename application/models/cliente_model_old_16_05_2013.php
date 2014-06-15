<?php

class Cliente_model extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    /**
     * Recebe os valores enviados via POST pelo formulário e trata-os para serem salvos no BD.
     */
    function post() {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $this->nome = $this->input->post('nome', TRUE);
            $this->razao_social = $this->input->post('razao_social', TRUE);
            $this->cnpj = $this->input->post('cnpj', TRUE);
            $this->endereco = $this->input->post('endereco', TRUE);
            $this->endereco_numero = $this->input->post('endereco_numero', TRUE);
            $this->endereco_complemento = $this->input->post('endereco_complemento', TRUE);
            $this->bairro = $this->input->post('bairro', TRUE);
            $this->idestado = $this->input->post('idestado', TRUE);
            $this->idcidade = $this->input->post('idcidade', TRUE);
            $this->responsavel = $this->input->post('responsavel', TRUE);
            $this->telefone = $this->input->post('telefone', TRUE);
            $this->ramal = $this->input->post('ramal', TRUE);
            $this->celular = $this->input->post('celular', TRUE);
            $this->email = $this->input->post('email', TRUE);
			$this->pontos_referencias = $this->input->post('pontos_referencias', TRUE); // add dia 11/09/2012 by Victor
            $this->observacao = $this->input->post('observacao', TRUE);
        }
    }

    /**
     * Salva no banco de dados os dados do atendimento salvos neste modelo.
     * Observe que esta classe não possui atributos, pois os atributos são adicionados "on the fly", graças a capacidade do PHP de criar atributos dinamicamente.
     */
    function insert() {

        $this->criado = date("Y-m-d H:i:s");
        $this->criado_por = $this->session->userdata('usuario')->idusuario;
        $this->idempresa = $this->session->userdata('usuario')->idempresa;

        if ($this->db->insert("cliente", $this))
            return $this->idcliente = $this->db->insert_id();
        else
            return 0;
    }

    function update($idcliente) {
        $idcliente = (int) $idcliente;

        if ($idcliente > 0) {

            $this->modificado = date("Y-m-d H:i:s");
            $this->modificado_por = $this->session->userdata('usuario')->idusuario;

            $this->db->where('idcliente', $idcliente);
			$this->db->where('idempresa', $this->session->userdata('usuario')->idempresa);
            return ($this->db->update('cliente', $this)) ? TRUE : FALSE;
        }
        else
            return FALSE;
    }

    /**
     *
     * Retorna um objeto de cliente de um ID específico, caso encontre. Caso não encontre retorna '0'.
     * @param type $idcliente
     * @return objeto cliente 
     */
    function get_by_id($idcliente) {
        $idcliente = (int) $idcliente;
		$idempresa = $this->session->userdata('usuario')->idempresa;
        if ($idcliente > 0)
            return $this->db->query("SELECT * FROM " . $this->db->dbprefix . "cliente WHERE idcliente = {$idcliente} AND idempresa = {$idempresa}  LIMIT 1")->row();
        else
            return 0;
    }

    function get_clientes_by_empresa() {
        $idempresa = $this->session->userdata('usuario')->idempresa;
        $cliente = $this->db->query("SELECT * FROM " . $this->db->dbprefix . "cliente WHERE idempresa = {$idempresa} ")->result();
        return $cliente;
    }

    /**
     *
     * Retorna um objeto de usuario de um NOME específico, caso encontre. Caso não encontre retorna 'false'.
     * @param type $usuarioNome
     * @return objeto usuario 
     */
    function get_by_id_nome($clienteNome) {
		$idempresa = $this->session->userdata('usuario')->idempresa;
        $clienteNome = (string) $clienteNome;
        if (strlen($clienteNome) > 0){
			$cliente = $this->db->query("SELECT idcliente, nome FROM " . $this->db->dbprefix . "cliente WHERE nome = '{$clienteNome}' AND idempresa = {$idempresa} LIMIT 1")->row();
            return json_encode($cliente);
		}
        else
            return false;
    }
	
    function get_by_dados_nome($clienteNome) {
		$idempresa = $this->session->userdata('usuario')->idempresa;
        $clienteNome = (string) $clienteNome;
        if (strlen($clienteNome) > 0){
			$cliente = $this->db->query("SELECT * FROM " . $this->db->dbprefix . "cliente WHERE nome = '{$clienteNome}' AND idempresa = {$idempresa} LIMIT 1")->row();
            return $cliente;
		}
        else
            return false;
    }

    function form_enderecos_filiais() {
        $array_endereco = $this->input->post("filial_endereco", true);
        $array_endereco_numero = $this->input->post("filial_endereco_numero", true);
        $array_endereco_complemento = $this->input->post("filial_endereco_complemento", true);
        $array_bairro = $this->input->post("filial_bairro", true);
        $array_idestado = $this->input->post("filial_idestado", true);
        $array_idcidade = $this->input->post("filial_idcidade", true);
		$array_pontos_referencias = $this->input->post("filial_pontos_referencias", true);

        // Verificar se o endereço de alguma filial foi preenchido.
        if (
                empty($array_endereco) ||
                ((count($array_endereco) == 1) && (empty($array_endereco[0]) && empty($array_endereco_numero[0]) && empty($array_bairro[0]) && empty($array_idestado[0]) && empty($array_idcidade[0]) && empty($array_pontos_referencias[0])))
        )
            return array();

        $filiais = array();
        for ($i = 0; $i < count($array_endereco); $i++) {

            // Se o usuário deixar alguma filial com os campos completamente vazios, não é necessário adicioná-la no array de retorno.
            if (empty($array_endereco[$i]) && empty($array_endereco_numero[$i]) && empty($array_bairro[$i]) && empty($array_idestado[$i]) && empty($array_idcidade[$i]) && empty($array_pontos_referencias[$i]))
                continue;

            $objeto_temp = new stdClass();
            $objeto_temp->endereco = (!empty($array_endereco[$i])) ? $array_endereco[$i] : "";
            $objeto_temp->endereco_numero = (!empty($array_endereco_numero[$i])) ? $array_endereco_numero[$i] : "";
            $objeto_temp->endereco_complemento = (!empty($array_endereco_complemento[$i])) ? $array_endereco_complemento[$i] : "";
            $objeto_temp->bairro = (!empty($array_bairro[$i])) ? $array_bairro[$i] : "";
            $objeto_temp->idestado = (!empty($array_idestado[$i])) ? $array_idestado[$i] : "";
            $objeto_temp->idcidade = (!empty($array_idcidade[$i])) ? $array_idcidade[$i] : "";
			$objeto_temp->pontos_referencias = (!empty($array_pontos_referencias[$i])) ? $array_pontos_referencias[$i] : "";
            $filiais[] = $objeto_temp;
            unset($objeto_temp);
        }

        return $filiais;
    }
	
	function form_contatos() {
        $array_contato_responsavel = $this->input->post("contato_responsavel", true);
        $array_contato_telefone = $this->input->post("contato_telefone", true);
        $array_contato_ramal = $this->input->post("contato_ramal", true);
        $array_contato_celular = $this->input->post("contato_celular", true);
        $array_contato_email = $this->input->post("contato_email", true);


        // Verificar se o endereço de algum contato foi preenchido.
        if (
                empty($array_contato_responsavel) ||
                ((count($array_contato_responsavel) == 1) && (empty($array_contato_responsavel[0]) && empty($array_contato_telefone[0]) && empty($array_contato_ramal[0]) && empty($array_contato_celular[0]) && empty($array_contato_email[0]) ))
        )
            return array();

        $contatos = array();
        for ($i = 0; $i < count($array_contato_responsavel); $i++) {

            // Se o usuário deixar algum contato com os campos completamente vazios, não é necessário adicioná-la no array de retorno.
            if (empty($array_contato_responsavel[$i]) && empty($array_contato_telefone[$i]) && empty($array_contato_ramal[$i]) && empty($array_contato_celular[$i]) && empty($array_contato_email[$i]) )
                continue;

            $objeto_temp = new stdClass();
            $objeto_temp->contato_responsavel = (!empty($array_contato_responsavel[$i])) ? $array_contato_responsavel[$i] : "";
            $objeto_temp->contato_telefone = (!empty($array_contato_telefone[$i])) ? $array_contato_telefone[$i] : "";
            $objeto_temp->contato_ramal = (!empty($array_contato_ramal[$i])) ? $array_contato_ramal[$i] : "";
            $objeto_temp->contato_celular = (!empty($array_contato_celular[$i])) ? $array_contato_celular[$i] : "";
            $objeto_temp->contato_email = (!empty($array_contato_email[$i])) ? $array_contato_email[$i] : "";
            $contatos[] = $objeto_temp;
            unset($objeto_temp);
        }

        return $contatos;
    }
	
	function contatos_adicionais($idcliente = 0) {
        $this->db->where('idcliente', $idcliente);
        return $this->db->get('cliente_contato')->result();
    }
	
    /**
     * Salva os contatos do cliente.
     * Essas informações são salvas no momento em que o cliente é salvo.
     * 
     * O atributo $this->idcliente é criado e setado dinamicamente quando o cliente é inserido no banco de dados. (Ver método insert())
     */
    function salvar_contatos_cliente() {

        // Pegar os contatos do formulário de cadastro do cliente.
        $contatos = $this->form_contatos();

        if (!empty($this->idcliente) && !empty($contatos) && is_array($contatos)) {
            foreach ($contatos as $contato) {
                // As variáveis abaixo foram setadas para poder usar o padrão Active Record.
                $contato->idcliente = $this->idcliente;

                $this->db->insert('cliente_contato', $contato);
                //pr('last query contato', $this->db->last_query());
            }//foreach
        }//if
        //exit('cliente_model salvar_enderecos_filiais');
    }

    function salvar_contatos_cliente_edicao($idcliente) {
        // Deletar os contatos antigos para cadastrar os novos.
        $this->db->where('idcliente', $idcliente);
        $this->db->delete('cliente_contato');

        // Pegar os contatos do formulário de cadastro do cliente.
        $contatos = $this->form_contatos();

        if (!empty($idcliente) && !empty($contatos) && is_array($contatos)) {
            foreach ($contatos as $contato) {

                // As variáveis abaixo foram setadas para poder usar o padrão Active Record.
                $contato->idcliente = $idcliente;

                $this->db->insert('cliente_contato', $contato);
                //pr('last query salvar_contatos_cliente_edicao($idcliente)', $this->db->last_query());
            }//foreach
        }//if
        //exit('cliente_model salvar_contatos_cliente_edicao');
    }
	
	/**
     * Salva os endereços das filiais do cliente.
     * Essas informações são salvas no momento em que o cliente é salvo.
     * 
     * O atributo $this->idcliente é criado e setado dinamicamente quando o cliente é inserido no banco de dados. (Ver método insert())
     */
    function salvar_enderecos_filiais() {

        // Pegar os endereços das filiais do formulário de cadastro do cliente.
        $filiais = $this->form_enderecos_filiais();

        if (!empty($this->idcliente) && !empty($filiais) && is_array($filiais)) {
            foreach ($filiais as $filial) {
                // As variáveis abaixo foram setadas para poder usar o padrão Active Record.
                $filial->idcliente = $this->idcliente;
                $filial->criado = $this->criado;
                $filial->criado_por = $this->criado_por;

                $this->db->insert('cliente_filial', $filial);
                //pr('last query enderecos filiais', $this->db->last_query());
            }//foreach
        }//if
        //exit('cliente_model salvar_enderecos_filiais');
    }

    function enderecos_filiais($idcliente = 0) {
        $this->db->where('idcliente', $idcliente);
        return $this->db->get('cliente_filial')->result();
    }

    function salvar_enderecos_filiais_edicao($idcliente) {
        //exit('em construção... [cliente_mode]');
        // Deletar os endereços de filiais antigos para cadastrar os novos.
        $this->db->where('idcliente', $idcliente);
        $this->db->delete('cliente_filial');

        // Pegar os endereços das filiais do formulário de cadastro do cliente.
        $filiais = $this->form_enderecos_filiais();

        if (!empty($idcliente) && !empty($filiais) && is_array($filiais)) {
            foreach ($filiais as $filial) {

                // As variáveis abaixo foram setadas para poder usar o padrão Active Record.
                $filial->idcliente = $idcliente;
                $filial->criado = $this->modificado;
                $filial->criado_por = $this->modificado_por;

                $this->db->insert('cliente_filial', $filial);
                //pr('last query salvar_enderecos_filiais_edicao($idcliente)', $this->db->last_query());
            }//foreach
        }//if
        //exit('cliente_model salvar_enderecos_filiais');
    }

    function lista($params = array()) {
		if(!empty($params)){
			extract($params, EXTR_OVERWRITE);
		}	
        $this->db->select('idcliente, vt_cliente.nome, endereco, endereco_numero, endereco_complemento, bairro, email, ativo, vt_cidade.nome as nomecidade, vt_estado.sigla as uf');
        $this->db->from('cliente');
		$this->db->join('vt_estado', 'vt_estado.idestado = cliente.idestado');
		$this->db->join('vt_cidade', 'vt_cidade.idcidade = cliente.idcidade');
        $this->db->where('idempresa', $this->session->userdata('usuario')->idempresa);
        $this->db->order_by('vt_cliente.nome');
		if((isset($offset)) && (isset($per_page))){
			$this->db->limit($per_page, $offset);
		}		
        $result =  $this->db->get();
		//print $this->db->last_query();
	    return $result;
    }
}

/* End of file Cliente_model.php */
/* Location: ./application/models/cliente_model.php */