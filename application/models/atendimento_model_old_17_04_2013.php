<?php

class Atendimento_model extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    /**
     * Recebe os valores enviados via POST pelo formulário e trata-os para serem salvos no BD.
     */
    function post($latitude, $longitude) {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {

            $this->titulo = $this->input->post('titulo', TRUE);
            $this->idtag = $this->input->post('idtag', TRUE);
			/* Alterado por Victor */
			//$this->idcliente = $this->input->post('idcliente', TRUE);
            $cliente_alocado = $this->input->post('cliente_alocado', TRUE);
			if(!empty($cliente_alocado)){
				$cliente_alocado = explode("-",$cliente_alocado);
				$this->idcliente = (int) $cliente_alocado[1];
			}
			/* FIM */
            $this->descricao = $this->input->post('descricao', TRUE);
            $this->data_agendada = fdata($this->input->post('data_agendada', TRUE), "-");
            $this->hora_agendada = $this->input->post('hora_agendada', TRUE);
            $this->tempo_estimado = $this->input->post('tempo_estimado', TRUE);
            $this->prioridade = $this->input->post('prioridade', TRUE);
            $this->status = $this->input->post('status', TRUE);
            $this->tem_contra_senha = $this->input->post('tem_contra_senha', TRUE);
            $this->endereco = $this->input->post('endereco', TRUE);
            $this->endereco_numero = $this->input->post('endereco_numero', TRUE);
            $this->endereco_complemento = $this->input->post('endereco_complemento', TRUE);
            $this->bairro = $this->input->post('bairro', TRUE);
            $this->idestado = $this->input->post('idestado', TRUE);
            $this->idcidade = $this->input->post('idcidade', TRUE);
            $this->meios_transportes = $this->input->post('meios_transportes', TRUE);
            $this->pontos_referencias = $this->input->post('pontos_referencias', TRUE);
            $this->latitude = $latitude;
            $this->longitude = $longitude;
			/* add by Victor */
			$this->idfuncionario = $this->input->post('funcionarios_alocados', TRUE);
			$this->idfuncionario_antigos = $this->input->post('funcionarios_alocados_antigos', TRUE);	
        }
    }


	function get_atendimentos($campo_busca='', $exibe_finalizados=''){
		
		/*if($exibe_finalizados=='sim'){
			$condicao_finalizados = "at.status = 'finalizado' AND";
		}else{
			$condicao_finalizados = "at.status <> 'finalizado' AND";
		}*/

		 // EXIBIÇÃO DE ATENDIMENTOS REFERENTE AOS NÍVEIS DE ACESSO
		 /*
		 1 => Administrador => visualiza tudo
		 2 => Gerente/Supervisor => visualisa aqueles atendimentos em que ele participa direta e indiretamente
		 3 => Funcionarios => visualiza somente os atendimentos no qual ele esta alocado.*/		

		$condicao = "";
		
		if($this->session->userdata('usuario')->idpermissao == 1){
			$condicao = "";
			
		}elseif($this->session->userdata('usuario')->idpermissao == 2){

			$ids = $this->db->query("SELECT DISTINCT(ua.idatendimento) FROM ".$this->db->dbprefix. "usuario_atendimento ua, ".$this->db->dbprefix. "usuario_relacionamento ur WHERE (ua.idusuario = ".$this->session->userdata('usuario')->idusuario.") OR (ur.id_adm_gerente = ".$this->session->userdata('usuario')->idusuario." and ur.id_usuario = ua.idusuario)")->result();
			
			if(!empty($ids)){
				$cont = count($ids);
				foreach($ids AS $key => $id){
					if($key == 0){
						$condicao .= " AND ( at.idatendimento = ".$id->idatendimento;
					}
					else{
						$condicao .= " OR at.idatendimento = ".$id->idatendimento;
					}
					if($key == ($cont-1)){
						$condicao .=" )";
					}
				}
			}
			else{
				$condicao .= " AND at.idatendimento = 0";	
			}
			
		}elseif($this->session->userdata('usuario')->idpermissao == 3){
			$ids = $this->db->query("SELECT idatendimento FROM ".$this->db->dbprefix. "usuario_atendimento WHERE idusuario = ".$this->session->userdata('usuario')->idusuario)->result();
			
			if(!empty($ids)){
				$cont = count($ids);
				foreach($ids AS $key => $id){
					if($key == 0){
						$condicao .= " AND ( at.idatendimento = ".$id->idatendimento;
					}
					else{
						$condicao .= " OR at.idatendimento = ".$id->idatendimento;
					}
					if($key == ($cont-1)){
						$condicao .=" )";
					}
				}
			}
			else{
				$condicao .= " AND at.idatendimento = 0";	
			}		
		}
		
		$atendimentos = $this->db->query("
				SELECT 
				DISTINCT(usu_at.idatendimento),
				at.idatendimento,
				at.idempresa,
				at.titulo,
				at.prioridade, 
				at.data_agendada,
				at.hora_agendada,
				at.endereco, 
				at.endereco_numero, 
				at.endereco_complemento, 
				at.bairro, 
				at.status,
				cli.idcliente,
				cli.nome AS nome_cliente, 
				usu.nome as nome_usuario
			FROM 
				" . $this->db->dbprefix . "cliente cli, 
				" . $this->db->dbprefix . "atendimento at, 
				" . $this->db->dbprefix . "usuario_atendimento usu_at, 
				" . $this->db->dbprefix . "usuario usu 				
			WHERE 
				(
				at.idempresa = '".$this->session->userdata('usuario')->idempresa."' AND
				at.idcliente = cli.idcliente AND 
				at.idatendimento = usu_at.idatendimento AND
				usu.idusuario = usu_at.idusuario AND
				(at.titulo LIKE '%{$campo_busca}%' OR usu.nome LIKE '%{$campo_busca}%' OR cli.nome LIKE '%{$campo_busca}%')
				{$condicao}
				)
			ORDER BY
			  at.prioridade ASC,
			  at.ordem ASC,
			  at.titulo ASC	
			")->result();
			
			print $this->db->last_query();
			
            return $atendimentos; 
	}



    /**
     * Salva no banco de dados os dados do atendimento salvos neste modelo.
     * Observe que esta classe não possui atributos, pois os atributos são adicionados "on the fly", graças a capacidade do PHP de criar atributos dinamicamente.
     */
    function insert() {
        // Variáveis setadas pelo sistema automaticamente, ou seja, sem ação do usuário.
        $this->criado = date("Y-m-d H:i:s");
        $this->criado_por = $this->session->userdata('usuario')->idusuario;
        $this->idempresa = $this->session->userdata('usuario')->idempresa;

        //verifica se já existe uma tag para essa empresa com o mesmo nome
        if (!empty($this->idtag)) {
            $existe_tag = $this->db->query("SELECT * FROM " . $this->db->dbprefix . "tag WHERE tag = '{$this->idtag}' AND idempresa = {$this->idempresa}  LIMIT 1")->row();
            if (!$existe_tag) { //se ainda não existe a tag criada
                //insere na tabela tag
                $data = array(
                    'criado' => $this->criado,
                    'criado_por' => $this->criado_por,
                    'idempresa' => $this->idempresa,
                    'tag' => $this->idtag
                );
                if ($this->db->insert("tag", $data)) {
                    $this->idtag = $this->db->insert_id();
                }//if insert tag			
            } else {
                //pega o id da tag para ser inserido na tabela atendimento.
                $tag = $this->db->query("SELECT idtag FROM " . $this->db->dbprefix . "tag WHERE tag = '{$this->idtag}' AND idempresa = {$this->idempresa}  LIMIT 1")->row();
                $this->idtag = $tag->idtag;
            }// if !$existe_tag
        } else {
            $this->idtag = NULL;
        }// if !empty($this->idtag
		
			$this->meios_transportes = NULL;
			
			if(empty($this->pontos_referencias)){
				$this->pontos_referencias = NULL;
			}
			
			$idfuncionarios = $this->idfuncionario_antigos;
			unset($this->idfuncionario_antigos);

			// return $this->db->insert_id()
			/* Bloco if colocado para cadastrar os funcionários alocados */
			if ($this->db->insert("atendimento", $this)){
				$id_registro = (int) $this->db->insert_id();
				foreach($this->idfuncionario as $ids){
					$valores = explode("-",$ids);
					$idfuncionario = (int) end($valores);

					$user_alocado = array(
								'idusuario' 	=> $idfuncionario,
								'idatendimento'	=> $id_registro	
					);
					$insert_user_atendimento_selecionado = $this->db->insert("usuario_atendimento", $user_alocado);
				}
				return $id_registro;
			}
        else
            return 0;
    }

//insert

	function updateApp($dataTime, $hora, $latitude, $lat, $longitude, $long, $idUsuario, $idAtendimento, $status=NULL, $txtStatus=NULL){
		if($status){
			$this->status = $txtStatus;
			$this->db->where("idatendimento", (int)$idAtendimento);
			$this->db->update("atendimento", $this);
			$this->status = $txtStatus;
		}
		
		$this->$dataTime = $hora;
		$this->$latitude = $lat;
		$this->$longitude = $long;
		$this->db->where("idatendimento", (int)$idAtendimento);
		$this->db->where("idusuario", (int)$idUsuario);
		//$check = $this->db->query("UPDATE " . $this->db->dbprefix . "usuario_atendimento SET $dataTime = '{$hora}', $latitude = '{$lat}', $longitude = '{$long}' WHERE idatendimento = '{$idAtendimento}' AND idusuario = '{$idUsuario}'");
		//$this->db->update("usuario_atendimento", $this);
		if($this->db->update("usuario_atendimento", $this)){
			return true;
		}
		else{
			return false;
		}
	}
	
	function insertComentApp($idUsuario, $idAtendimento, $observacao, $dataObservacao){
		$data = array(
			"id_usuario" => (int)$idUsuario,
			"id_atendimento" => (int)$idAtendimento,
			"observacao" => $observacao,
			"data_observacao" => $dataObservacao
		);
		$obs = $this->db->insert("atendimento_observacao", $data);
	}

	/**
	 * Script responsavel por consultar os dados da tabela de notificações e retornar a quantidade de notificações ativas para o usuario informado existe.
	*/
	function notificacao($id_usuario){
		$this->db->select('*');
		$this->db->from("vt_atendimentos_novos");
		$this->db->where("id_usuario", $id_usuario);
		$this->db->where("notificar", "sim");
		$qdt_notficacao = $this->db->count_all_results();
		return $qdt_notficacao;
	}

	/**
	 * Script responsavel por atualizar as informações de notificações fazendo com que as que ja tenham sido baixadas não sejam notificadas novamente.
	*/
	function atualizar_notificacao($id_usuario){
		$this->notificar = "nao";
		$this->db->where("id_usuario", (int)$id_usuario);
		if($this->db->update("atendimentos_novos", $this))
			return true;
		else
			return false;
	}


	function marcar_notificacao($id_atendimento){
		$marcar = $this->db->query("UPDATE " . $this->db->dbprefix . "atendimentos_novos SET notificar = 'sim' WHERE id_atendimento = '{$id_atendimento}'");
		if($marcar)
			return true;
		else
			return false;
	}

    /**
     * Salva no banco de dados os dados do atendimento salvos neste modelo.
     * Observe que esta classe não possui atributos, pois os atributos são adicionados "on the fly", graças a capacidade do PHP de criar atributos dinamicamente.
     */
    function update($idatendimento) {
        // Garantir que o ID que está sendo passado é um inteiro ou uma String que representa um inteiro.
        $idatendimento = (int) $idatendimento;
        $this->idempresa = $this->session->userdata('usuario')->idempresa;
		
        if ($idatendimento > 0) {
            // Variáveis setadas pelo sistema automaticamente, ou seja, sem ação do usuário.
            //verifica se já existe uma tag para essa empresa com o mesmo nome
            if (!empty($this->idtag)) {
                $existe_tag = $this->db->query("SELECT * FROM " . $this->db->dbprefix . "tag WHERE tag = '{$this->idtag}' AND idempresa = {$this->idempresa}  LIMIT 1")->row();
                if (!$existe_tag) { //se ainda não existe a tag criada
                    //insere na tabela tag
					$this->criado = date("Y-m-d H:i:s");
					$this->criado_por = $this->session->userdata('usuario')->idusuario;
					
                    $data = array(
                        'criado' => $this->criado,
                        'criado_por' => $this->criado_por,
                        'idempresa' => $this->idempresa,
                        'tag' => $this->idtag
                    );
                    if ($this->db->insert("tag", $data)) {
                        $this->idtag = $this->db->insert_id();
                    }//if insert tag			
                } else {
                    //pega o id da tag para ser inserido na tabela atendimento.
                    $tag = $this->db->query("SELECT idtag FROM " . $this->db->dbprefix . "tag WHERE tag = '{$this->idtag}' AND idempresa = {$this->idempresa}  LIMIT 1")->row();
                    $this->idtag = $tag->idtag;
                }// if !$existe_tag
            } else {
                $this->idtag =  NULL;
            }// if !empty($this->idtag


            $this->modificado = date("Y-m-d H:i:s");
            $this->modificado_por = $this->session->userdata('usuario')->idusuario;
            $this->db->where('idatendimento', $idatendimento);
			
			$funcionarios_antigos = $this->idfuncionario_antigos;
			$funcionarios = $this->idfuncionario;
			unset($this->idfuncionario_antigos, $this->idfuncionario);
			
			if ($this->db->update("atendimento", $this)){
				$user_alocado_antigos = array();
				$user_alocado = array();
				foreach($funcionarios_antigos as $ids_antigos){
					$valores_antigos = explode("-",$ids_antigos);
					$idfuncionario_antigos = (int) end($valores_antigos);
					$user_alocado_antigos[] = $idfuncionario_antigos;
				}
				foreach($funcionarios as $ids){
					$valores = explode("-",$ids);
					$idfuncionario = (int) end($valores);
					$user_alocado[] = $idfuncionario;
				}	
				$arr = array_diff($user_alocado_antigos, $user_alocado);
				$arr2= array_diff($user_alocado, $user_alocado_antigos);
				foreach($arr as $exclui){
					$excluir = $this->db->query("DELETE FROM " . $this->db->dbprefix . "usuario_atendimento WHERE idusuario = {$exclui} AND idatendimento = {$idatendimento}");
				}
				foreach($arr2 as $insere){
					$add_usuario = array(
								'idusuario' 	=> $insere,
								'idatendimento'	=> $idatendimento	
					);
					
					$insert_user_atendimento_selecionado = $this->db->insert("usuario_atendimento", $add_usuario);
				}

				return TRUE;
			}
			else{
				return FALSE;
			}
           // return ($this->db->update('atendimento', $this)) ? TRUE : FALSE;
        }
        else
            return FALSE;
    }

    /**
     *
     * Retorna um objeto de atendimento de um ID específico, caso encontre. Caso não encontre retorna '0'.
     * @param type $idatendimento
     * @return objeto atendimento 
     */
    function get_by_id($idatendimento) {
        $idatendimento = (int) $idatendimento;
        if ($idatendimento > 0)
            return $this->db->query("SELECT * FROM " . $this->db->dbprefix . "atendimento WHERE idatendimento = {$idatendimento} LIMIT 1")->row();
        else
            return 0;
    }

    function get_local_atendimento($idatendimento) {
        $idatendimento = (int) $idatendimento;
        if ($idatendimento > 0){
            $atendimento = $this->db->query("
				SELECT 
				cli.nome AS nomecliente, 
				at.latitude,
				at.longitude,
				at.titulo, 
				at.endereco, 
				at.endereco_numero, 
				at.endereco_complemento, 
				at.bairro, 
				es.nome AS nomeestado, 
				es.sigla AS uf, 
				ci.nome AS nomecidade
			FROM 
				" . $this->db->dbprefix . "cliente cli, 
				" . $this->db->dbprefix . "atendimento at, 
				" . $this->db->dbprefix . "estado es , 
				" . $this->db->dbprefix . "cidade ci 
			WHERE 
				at.idatendimento = {$idatendimento} AND 
				at.idcliente = cli.idcliente AND 
				at.idestado = es.idestado AND 
				at.idcidade = ci.idcidade 
			LIMIT 1	
			")->row();
			
            return $atendimento; 
		}else{
            return 0;
		}
    }

	
	
	

    /**
     *
     * Retorna um objeto de atendimento de um ID específico, caso encontre. Caso não encontre retorna '0'.
     * @param type $idatendimento
     * @return objeto atendimento 
     */
    function get_by_id_check($idatendimento) {
        $idatendimento = (int) $idatendimento;
        return $this->db->query("SELECT * FROM " . $this->db->dbprefix . "atendimento WHERE idatendimento = {$idatendimento} LIMIT 1")->row();
    }
	

	
    /**
     *
     * Retorna um objeto de atendimento de um ID específico, caso encontre. Caso não encontre retorna '0'.
     * @param type $idatendimento
     * @return objeto atendimento 
     */
    function get_by_id_app($idatendimento) {
        $idatendimento = (int) $idatendimento;
            echo $this->db->query("SELECT * FROM " . $this->db->dbprefix . "atendimento WHERE idatendimento = {$idatendimento} LIMIT 1")->row();
    }
    /**
     *
     * Retorna varios objeto de atendimento de um ID de empresa específico, caso encontre. Caso não encontre retorna '0'.
     * @param type $idatendimento
     * @return objeto atendimento 
     */
    function get_by_id_todos($idempresa) {
        $idempresa = (int) $idempresa;
        if ($idempresa > 0){
			
			$usuarios = $this->db->query("
				SELECT 
				us.idusuario, 
				us.nome AS nomeusuario, 
				ua.latitude_checkin AS latitude, 
				ua.longitude_checkin AS longitude, 
				ua.data_hora_checkin, 
				at.titulo, 
				at.endereco, 
				at.endereco_numero, 
				at.endereco_complemento, 
				at.bairro, 
				es.nome AS nomeestado, 
				es.sigla AS uf, 
				ci.nome AS nomecidade, 
				(
					SELECT 
						MAX(usera.data_hora_checkin) AS data_hora_ultima 
					FROM 
						" . $this->db->dbprefix . "usuario_atendimento usera, 
						" . $this->db->dbprefix . "atendimento atend 
					WHERE 
						atend.idatendimento = usera.idatendimento AND 
						atend.idempresa = {$idempresa} AND 
						atend.idatendimento = usera.idatendimento AND 
						usera.idusuario = us.idusuario 
					GROUP BY 
						usera.idusuario
				) AS ultima_data 
			FROM 
				" . $this->db->dbprefix . "usuario_atendimento ua, 
				" . $this->db->dbprefix . "usuario us, 
				" . $this->db->dbprefix . "atendimento at, 
				" . $this->db->dbprefix . "estado es , 
				" . $this->db->dbprefix . "cidade ci 
			WHERE 
				at.idempresa = {$idempresa} AND 
				at.idatendimento = ua.idatendimento AND 
				ua.idusuario = us.idusuario AND 
				at.idestado = es.idestado AND 
				at.idcidade = ci.idcidade AND 
				(
					ua.latitude_checkin IS NOT NULL AND 
					ua.longitude_checkin IS NOT NULL 
				)
			HAVING 
				ua.data_hora_checkin = ultima_data 
			ORDER BY 
				nomeusuario ASC
			")->result();
			
            return $usuarios; 
		}
        else
            return 0;
    }
################################################# CONSULTAS APP #######################################################################
	function consultApp($id_usuario){
		$id_usuario = (int)$id_usuario;
		
		if($id_usuario > 0){
			
			$consulta = $this->db->query("
			
			SELECT 
					atendimento.idatendimento, 
					atendimento.titulo, 
					atendimento.prioridade, 
					atendimento.data_agendada, 
					atendimento.hora_agendada,
					atendimento.tempo_estimado, 
					atendimento.descricao,
					atendimento.endereco,
					atendimento.endereco_numero,
					atendimento.endereco_complemento,
					atendimento.tem_contra_senha,
					atendimento.contra_senha,
					cidade.nome AS cidade,
					estado.sigla AS estado,
					cliente.idcliente, 
					atendimento.bairro, 
					atendimento.status, 
					cliente.nome AS nome_cliente, 
					atendimento.idempresa,
					usatendimento.data_hora_checkin,
					usatendimento.data_hora_checkout
				FROM
					" . $this->db->dbprefix . "atendimento atendimento,
					" . $this->db->dbprefix . "cliente cliente,
					" . $this->db->dbprefix . "usuario usuario,
					" . $this->db->dbprefix . "usuario_atendimento usatendimento,
					" . $this->db->dbprefix . "estado estado, 
					" . $this->db->dbprefix . "cidade cidade 
				WHERE
 					usuario.idusuario = '".$id_usuario."' AND 
 					usatendimento.idusuario = usuario.idusuario AND
 					atendimento.idatendimento = usatendimento.idatendimento AND
					atendimento.idempresa = usuario.idempresa AND
					cliente.idcliente = atendimento.idcliente AND
					atendimento.idestado = estado.idestado AND 
					atendimento.idcidade = cidade.idcidade
				ORDER BY 
					atendimento.status ASC
			")->result();
						
			return $consulta;
		}
		else{
			return 0;	
		}
		
	}

	function consultAppGrupos($id_usuario){
		$id_usuario = (int)$id_usuario;
		
		if($id_usuario > 0){
			
			$consulta = $this->db->query("
			
			SELECT 
					atendimento.idatendimento, 
					atendimento.titulo, 
					atendimento.prioridade, 
					atendimento.data_agendada, 
					atendimento.hora_agendada,
					atendimento.tempo_estimado, 
					atendimento.descricao,
					atendimento.endereco,
					atendimento.endereco_numero,
					atendimento.endereco_complemento,
					atendimento.tem_contra_senha,
					atendimento.contra_senha,
					cidade.nome AS cidade,
					estado.sigla AS estado,
					cliente.idcliente, 
					atendimento.bairro, 
					atendimento.status, 
					cliente.nome AS nome_cliente, 
					atendimento.idempresa,
					usatendimento.data_hora_checkin,
					usatendimento.data_hora_checkout
				FROM
					" . $this->db->dbprefix . "atendimento atendimento,
					" . $this->db->dbprefix . "cliente cliente,
					" . $this->db->dbprefix . "usuario usuario,
					" . $this->db->dbprefix . "usuario_atendimento usatendimento,
					" . $this->db->dbprefix . "estado estado, 
					" . $this->db->dbprefix . "cidade cidade 
				WHERE
 					usuario.idusuario = '".$id_usuario."' AND 
 					usatendimento.idusuario = usuario.idusuario AND
 					atendimento.idatendimento = usatendimento.idatendimento AND
					atendimento.idempresa = usuario.idempresa AND
					cliente.idcliente = atendimento.idcliente AND
					atendimento.idestado = estado.idestado AND 
					atendimento.idcidade = cidade.idcidade
				ORDER BY 
					atendimento.status ASC
			")->result();
						
			return $consulta;
		}
		else{
			return 0;	
		}
		
	}
################################################################################################################################################
    function getCidades($idestado) {
        $idestado = (int) $idestado;
        if ($idestado > 0)
            return $this->db->query("SELECT * FROM " . $this->db->dbprefix . "cidade WHERE idestado = {$idestado}")->result();
        else
            return 0;
    }
	
    function get_tag_by_id($idtag) {
        $idtag = (int) $idtag;
        if ($idtag > 0)
            return $this->db->query("SELECT tag FROM " . $this->db->dbprefix . "tag WHERE idtag = {$idtag} LIMIT 1")->row();
        else
            return 0;
    }
    
    /**
     * Este método é usado pelo hook acesso_funcionalidade.
     */
    function verificar_acesso_atendimento(){
        $idusuario = $this->session->userdata('usuario')->idusuario;
        $idatendimento = $this->input->post("idatendimento", TRUE);
        //echo "<br />idusuario: " . $idusuario;
        //echo "<br />idatendimento: " . $idatendimento;
        //exit("<br /><br />atendimento_model->verificar_acesso_atendimento()");
        
        $this->db->select("idusuario_atendimento");
        $this->db->where("idusuario", $idusuario);
        $this->db->where("idatendimento", $idatendimento);
        $result = $this->db->get("vt_usuario_atendimento");
        
		if($this->session->userdata('usuario')->idpermissao == 1)
			return true;	
		else
        	return ($result->num_rows() > 0) ? true : false;
		
    }

}

/* End of file atendimento_model.php */
    /* Location: ./application/models/atendimento_model.php */