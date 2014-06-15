<?php

class Usuario_model extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    /**
     * Recebe os valores enviados via POST pelo formulário e trata-os para serem salvos no BD.
     */
    function post() {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $this->idempresa = $this->session->userdata('usuario')->idempresa;
            $this->idpermissao = $this->input->post('idpermissao', TRUE);
            $this->nome = $this->input->post('nome', TRUE);
			$this->email = $this->input->post('email', TRUE);
            $this->login = $this->input->post('login', TRUE);
			$this->celular = $this->input->post('celular', TRUE);
            $this->ativo = $this->input->post('ativo', TRUE);
			$this->relacionamento = $this->input->post('funcionarios_alocados', TRUE);
			$this->idfuncionario_antigos = $this->input->post('funcionarios_alocados_antigos', TRUE);
        }
    }

    function lista($params = array()) {
		if(!empty($params)){
			extract($params, EXTR_OVERWRITE);
		}	

		$condicao = "";
		if(empty($exibir_todos)){
			if($this->session->userdata('usuario')->idpermissao == 1){
				$condicao = "";
			}
			elseif($this->session->userdata('usuario')->idpermissao == 2){
				
				$ids = $this->db->query("SELECT id_usuario FROM ".$this->db->dbprefix. "usuario_relacionamento WHERE id_adm_gerente = {$this->session->userdata('usuario')->idusuario}")->result();
				//$ids = $this->db->query("SELECT DISTINCT(ua.idatendimento) FROM ".$this->db->dbprefix. "usuario_atendimento ua, ".$this->db->dbprefix. "usuario_relacionamento ur WHERE (ua.idusuario = ".$this->session->userdata('usuario')->idusuario.") OR (ur.id_adm_gerente = ".$this->session->userdata('usuario')->idusuario." and ur.id_usuario = ua.idusuario)")->result();
				
				$ids_permitidos = '';
				if(!empty($ids)){
					foreach($ids as $id){
						$ids_permitidos .= $id->id_usuario.',';
					}
					$ids_permitidos = substr($ids_permitidos, 0, -1);
				}
				if (!empty($ids_permitidos)) $ids_permitidos = $ids_permitidos.', '; 
				$condicao .= "u.idusuario IN ({$ids_permitidos} {$this->session->userdata('usuario')->idusuario})";
			}
		}
		
	    $this->db->select('u.idusuario, u.nome, u.email, u.login, u.celular, u.idpermissao, u.ativo, permissao.nome as perfil');
		$this->db->from('usuario u');
		$this->db->join('permissao', 'permissao.idpermissao = u.idpermissao');
		$this->db->where('u.idempresa', $this->session->userdata('usuario')->idempresa);
		if(!empty($condicao)){
			$this->db->where($condicao);
		}
        $this->db->order_by('u.nome');
		if((isset($offset)) && (isset($per_page))){
			$this->db->limit($per_page, $offset);
		}		
        $result =  $this->db->get();
		//print $this->db->last_query();
	    return $result;
    }

    /**
     * Salva no banco de dados os dados do usuario salvos neste modelo.
     * Observe que esta classe não possui atributos, pois os atributos são adicionados "on the fly", graças a capacidade do PHP de criar atributos dinamicamente.
     */
    function insert() {
        // Variáveis setadas pelo sistema automaticamente, ou seja, sem ação do usuário.
        $this->criado = date("Y-m-d H:i:s");
        $this->criado_por = $this->session->userdata('usuario')->idusuario;


		$this->senha = $this->input->post('senha', TRUE);
		$this->senha = cripto($this->senha);
		
		$alocados = $this->relacionamento;
		unset($this->relacionamento,$this->idfuncionario_antigos);
        //pexit('This (hospedagem)', $this, 'hospedagem model');

        if ($this->db->insert("usuario", $this)){
			$idRetorno = $this->db->insert_id();
            if(!empty($alocados)){
				foreach($alocados as $relacionamento){
					$valores = explode("-",$relacionamento);
					$id = $valores[1];
					$cadastra = $this->db->query("INSERT INTO " . $this->db->dbprefix . "usuario_relacionamento (id_adm_gerente, id_usuario) VALUES ({$id}, {$idRetorno})");
				}
			}
			return $idRetorno;
		}
        else{
            return 0;
		}
    }

    /**
     * Salva no banco de dados os dados do usuario salvos neste modelo.
     * Observe que esta classe não possui atributos, pois os atributos são adicionados "on the fly", graças a capacidade do PHP de criar atributos dinamicamente.
     */
    function update($idusuario) {
        // Garantir que o ID que está sendo passado é um inteiro ou uma String que representa um inteiro.
        $idusuario = (int) $idusuario;

        if ($idusuario > 0) {
            // Variáveis setadas pelo sistema automaticamente, ou seja, sem ação do usuário.
            $this->modificado = date("Y-m-d H:i:s");
            $this->modificado_por = $this->session->userdata('usuario')->idusuario;
			
			$senha = $this->input->post('senha', TRUE);
			if (!empty($senha)){ 
				$this->senha = cripto($senha);
			}
			
			$idTelefone = $this->input->post('emei_valido', TRUE);
			$excluir_emei = $this->input->post('excluir', TRUE);
			if($excluir_emei == "nao") $this->idtelefone = $idTelefone;
			else $this->idtelefone = "";
            
			
			$notif_email = $this->input->post('notif_email', TRUE);
			$notif_sms = $this->input->post('notif_sms', TRUE);
			
			if(!empty($notif_email)) $this->notif_email = $notif_email;
			else $this->notif_email = "nao";
			
			if(!empty($notif_sms)) $this->notif_sms = $notif_sms;
			else $this->notif_sms = "nao";
						z
			$alocados = $this->relacionamento;
			$alocados_antigos = $this->idfuncionario_antigos;
			unset($this->relacionamento,$this->idfuncionario_antigos);
			//pexit('This (usuario)', $this, 'usuario model');

            $this->db->where('idusuario', $idusuario);
			$this->db->where('idempresa', $this->session->userdata('usuario')->idempresa);
			if($this->db->update('usuario', $this)){
				if(empty($alocados_antigos)){
					$user_alocado_antigos = array();
				}
				if(empty($alocados)){
					$user_alocado = array();
				}
				foreach($alocados_antigos as $ids_antigos){
					$valores_antigos = explode("-",$ids_antigos);
					$idfuncionario_antigos = (int) end($valores_antigos);
					$user_alocado_antigos[] = $idfuncionario_antigos;
				}
				foreach($alocados as $ids){
					$valores = explode("-",$ids);
					$idfuncionario = (int) end($valores);
					$user_alocado[] = $idfuncionario;
				}	
				$arr = array_diff($user_alocado_antigos, $user_alocado);
				$arr2= array_diff($user_alocado, $user_alocado_antigos);
				foreach($arr as $exclui){
					$excluir = $this->db->query("DELETE FROM " . $this->db->dbprefix . "usuario_relacionamento WHERE id_adm_gerente = {$exclui} AND id_usuario = {$idusuario}");
				}
				foreach($arr2 as $insere){
					$add_usuario = array(
								'id_adm_gerente' 	=> $insere,
								'id_usuario'	=> $idusuario	
					);
					
					$insert_user_atendimento_selecionado = $this->db->insert("usuario_relacionamento", $add_usuario);
				}
				
				return TRUE;
			}
			else{
				return FALSE;	
			}
			
            //return ($this->db->update('usuario', $this)) ? TRUE : FALSE;
        }
        else
            return FALSE;
    }
	
	function cadTelefone($idTelefone, $idusuario){
		$cadastrar = $this->db->query("UPDATE " . $this->db->dbprefix . "usuario SET idtelefone = '{$idTelefone}' WHERE idusuario = {$idusuario}");
	}
	
	function getTelefone($idTelefone){
		$result = $this->db->query("SELECT * FROM ".$this->db->dbprefix."usuario WHERE idtelefone = {$idTelefone} LIMIT 1")->row();
		return $result;	
	}
    function get_idusuario($login, $senha) {
        // Tratando login e senha.
        $login = addslashes($login);
        $senha = addslashes($senha);

        if (!empty($login) && !empty($senha)):
            $usuario = $this->db->query("SELECT idusuario, ativo FROM  " . $this->db->dbprefix . "usuario WHERE ((login = '{$login}') OR (email = '{$login}')) AND senha = '{$senha}' LIMIT 1")->row();
			if (!empty($usuario))
                return $usuario;
            else
                return -1;
        else: return -1;
        endif;
    }

    function get_usuario($idusuario) {
        //$usuario = $this->db->query("SELECT * FROM " . $this->db->dbprefix . "usuario WHERE idusuario = {$idusuario} LIMIT 1")->row();
		$usuario = $this->db->query("
			SELECT 
				u.idusuario,
				u.nome as nome_usuario,
				u.email,
				u.criado as data_criado_usuario,		
				u.celular,
				u.notif_email,
				u.notif_sms,
				u.ativo as status_usuario,
				e.nome as nome_empresa,
				e.idempresa,
				e.plano,
				e.ativo as status_empresa,
				DATE_FORMAT(e.criado, '%d/%m/%Y') as data_criado_empresa,
				p.idpermissao,
				p.nome as perfil,	
				p.descricao as descricao_permissao
			FROM 
				vt_usuario u
				INNER JOIN vt_empresa e ON (e.idempresa = u.idempresa)
				INNER JOIN vt_permissao p ON (p.idpermissao = u.idpermissao)		
			WHERE 
				u.idusuario = {$idusuario} 
			LIMIT 1
			")->row();

		//pr('$usuario',$usuario);


        return $usuario;
		
		
    }

	
    /**
     *
     * Retorna um objeto de usuario, contendo apenas nome e id.
     * @return objeto usuario 
     */
    function get_usuarios_by_empresa() {
        $idempresa = $this->session->userdata('usuario')->idempresa;
        $usuario_dadaos = $this->db->query("SELECT idusuario, nome, email FROM " . $this->db->dbprefix . "usuario WHERE idempresa = {$idempresa} AND ativo = 'sim' ORDER BY idpermissao ASC ")->result();
        return $usuario_dadaos;
    }

    /**
     *
     * Retorna um objeto de usuario, contendo apenas usuarios con nivel de permissao 2(gerente) e 3(funcionarios).
     * @return objeto usuario 
     */
    function get_usuarios_by_atendimento($idatendimento) {
	    $usuario = $this->db->query("SELECT ua.idusuario, u.nome FROM " . $this->db->dbprefix . "usuario u, " . $this->db->dbprefix . "usuario_atendimento ua WHERE ua.idatendimento = {$idatendimento} AND ua.idusuario = u.idusuario  AND u.ativo = 'sim' ")->result();
        return $usuario;
    }

    /**
     *
     * Retorna um objeto de usuario, contendo apenas usuarios con nivel de permissao 2(gerente) e 3(funcionarios).
     * @return objeto usuario 
     */
    function get_by_valida($iduser, $idatendimento) {
		if(!empty($idatendimento))
			$condicao = "AND a.idatendimento <> {$idatendimento}";
		else
			$condicao = "";
	    $usuario = $this->db->query("SELECT ua.idusuario, a.data_agendada, a.hora_agendada, a.tempo_estimado FROM " . $this->db->dbprefix . "usuario_atendimento ua, " . $this->db->dbprefix . "atendimento a WHERE ua.idusuario = {$iduser} AND ua.idatendimento = a.idatendimento {$condicao} ")->result();
        return $usuario;
    }

    /**
     *
     * Retorna um objeto de usuario, contendo apenas usuarios con nivel de permissao 2(gerente) e 3(funcionarios).
     * @return objeto usuario 
     */
    function get_usuarios_by_relacionamento($idusuario, $consulta, $procura) {	
	    $usuario = $this->db->query("SELECT u.idusuario, u.nome FROM " . $this->db->dbprefix . "usuario u, " . $this->db->dbprefix . "usuario_relacionamento ur WHERE ur.{$consulta} = {$idusuario} AND  ur.{$procura} = u.idusuario")->result();
        return $usuario;
    }

    /**
     *
     * Retorna um objeto de usuario, contendo apenas usuarios con nivel de permissao 2(gerente) e 3(funcionarios).
     * @return objeto usuario 
     */
    function get_usuarios_by_atendimento_exclusao($idusuario, $idatendimento) {	
	    $usuario = $this->db->query("SELECT ua.data_hora_checkin, a.data_agendada, a.hora_agendada, a.tempo_estimado, a.status FROM " . $this->db->dbprefix . "usuario_atendimento ua, " . $this->db->dbprefix . "atendimento a WHERE ua.idatendimento = {$idatendimento} AND ua.idusuario = {$idusuario} AND ua.idatendimento = a.idatendimento ")->row();
        return json_encode($usuario);
    }
	
    /**
     *
     * Retorna um objeto de usuario, contendo apenas usuarios con nivel de permissao 2(gerente) e 3(funcionarios).
     * @return objeto usuario 
     */
    function get_usuarios_by_atendimento_inclusao($idusuario) {	
	    $usuario = $this->db->query("SELECT a.data_agendada, a.hora_agendada, a.tempo_estimado, a.status FROM " . $this->db->dbprefix . "usuario_atendimento ua, " . $this->db->dbprefix . "atendimento a WHERE ua.idusuario = {$idusuario} AND ua.idatendimento = a.idatendimento ")->row();
        return json_encode($usuario);
    }

    /**
     *
     * Retorna um objeto de usuario de um ID específico, caso encontre. Caso não encontre retorna '0'.
     * @param type $idusuario
     * @return objeto usuario 
     */
    function get_by_id($idusuario) {
        $idusuario = (int) $idusuario;
        if ($idusuario > 0)
            return $this->db->query("SELECT *, u.nome, u.ativo , e.plano 
									FROM 
									" . $this->db->dbprefix . "usuario u 
									INNER JOIN vt_empresa e ON (e.idempresa = u.idempresa)
									WHERE u.idusuario = {$idusuario} 
									AND u.idempresa = ".$this->session->userdata('usuario')->idempresa."
									LIMIT 1")->row();
			

			
			
        else
            return 0;
    }
	
    /**
     *
     * Retorna um objeto de usuario de um NOME específico, caso encontre. Caso não encontre retorna 'false'.
     * @param type $usuarioNome
     * @return objeto usuario 
     */
    function get_by_id_nome($usuarioNome) {
        $usuarioNome = (string) $usuarioNome;
        if (strlen($usuarioNome) > 0){
			$idempresa = $this->session->userdata('usuario')->idempresa;
			$users = $this->db->query("SELECT idusuario, nome, email FROM " . $this->db->dbprefix . "usuario WHERE nome = '{$usuarioNome}' AND idempresa = ".$idempresa." LIMIT 1")->row();
            return json_encode($users);
		}
        else
            return false;
    }


    //função de busca de todos as permissões possiveis
    function get_permissao() {
        $this->db->select('idpermissao, nome, descricao');
        $this->db->from($this->db->dbprefix . 'permissao');
        $this->db->order_by('nome');
        $query = $this->db->get();
        return $query;
    }
	
	function get_retorna_gerentes($idusuario = 0){
		$this->db->select('u.nome');
        $this->db->from($this->db->dbprefix . 'usuario_relacionamento ur, '.$this->db->dbprefix .'usuario u');
		$this->db->where("ur.id_adm_gerente = u.idusuario");
		$this->db->where("ur.id_usuario", $idusuario);
        $this->db->order_by('nome');
        $query = $this->db->get();
        return $query;
	}
	
	
	//função que utilizo para buscar os dados do administrardores no caso de ser um gestor.
	//ou do gestor no caso de ser um funconário.
	function get_usuarios_adm(){
		$this->db->select('u.idusuario, u.nome as nome_usuario, u.email, u.celular, u.ativo as status_usuario, e.nome as nome_empresa, e.idempresa, e.plano, e.ativo as status_empresa, p.idpermissao, p.nome as perfil, p.descricao as descricao_permissao');
        $this->db->from($this->db->dbprefix . 'usuario u, '.$this->db->dbprefix .'empresa e, '.$this->db->dbprefix .'permissao p');
		$this->db->where("e.idempresa = u.idempresa");
		$this->db->where("p.idpermissao = u.idpermissao");
		if($this->session->userdata('usuario')->idpermissao == 2){
			$this->db->where("p.idpermissao", '1');
		}else if($this->session->userdata('usuario')->idpermissao == 3){
			$this->db->join($this->db->dbprefix . 'usuario_relacionamento ur', 'ur.id_adm_gerente = u.idusuario', 'left');
			$this->db->where("ur.id_usuario", $this->session->userdata('usuario')->idusuario);
		}
		$this->db->where("u.idempresa", $this->session->userdata('usuario')->idempresa);
        $this->db->order_by('u.nome');
        $query = $this->db->get();
		//pexit("teste", $this->db->last_query());
        return $query;
	}
	
}

/* End of file usuario_model.php */
    /* Location: ./application/models/usuario_model.php */