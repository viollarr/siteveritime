<?php

class Atendimento_model extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    /**
     * Recebe os valores enviados via POST pelo formulário e trata-os para serem salvos no BD.
     */
    function post($latitude = '', $longitude = '') {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
			
			$campo_responsavel = $this->input->post('campo_responsavel', TRUE);
			
            $this->titulo = $this->input->post('titulo', TRUE);
            $this->idtag = $this->input->post('idtag', TRUE);
			/* Alterado por Victor, depois por lincoln */
			//$this->idcliente = $this->input->post('idcliente', TRUE);
			$campo_cliente = $this->input->post('campo_cliente', TRUE);
			if(!empty($campo_cliente)){
				$this->load->model("cliente_model");
				$idcliente = $this->cliente_model->get_by_dados_nome($campo_cliente);
				$this->idcliente = $idcliente->idcliente;
				
			}else{
				$cliente_alocado = $this->input->post('cliente_alocado', TRUE);
				if(!empty($cliente_alocado)){
					$cliente_alocado = explode("-",$cliente_alocado);
					$this->idcliente = (int) $cliente_alocado[1];
				}
				
				
			}
			if(!empty($campo_responsavel)){
				$this->load->model("cliente_model");
				$responsavel = json_decode($this->cliente_model->get_by_id_nome_contato($campo_responsavel, $this->idcliente));
				$this->responsavel = $responsavel->idcliente_contato;
			}else{
				$nome_responsavel = $this->input->post('nome_responsavel', TRUE);
				if(!empty($nome_responsavel)){
					$nome_responsavel = explode("-",$nome_responsavel);
					$this->responsavel = (int) $nome_responsavel[1];
				}
			}
			
			/* FIM */
            $this->descricao = $this->input->post('descricao', TRUE);
            $this->data_agendada = fdata($this->input->post('data_agendada', TRUE), "-");
            $this->hora_agendada = $this->input->post('hora_agendada', TRUE);
            $this->tempo_estimado = $this->input->post('tempo_estimado', TRUE);
            $this->prioridade = $this->input->post('prioridade', TRUE);
            $this->status = $this->input->post('status', TRUE);
            $this->tem_contra_senha = $this->input->post('tem_contra_senha', TRUE);
			if ($this->tem_contra_senha=='sim'){
				$this->contra_senha = $this->input->post('contra_senha', TRUE);
				$this->emails_assinatura = $this->input->post('emails_assinatura', TRUE);
			}else{
				$this->contra_senha = '';
				$this->emails_assinatura = '';
			}
            $this->meios_transportes = $this->input->post('meios_transportes', TRUE);
			$this->latitude = $latitude;
			$this->longitude = $longitude;
			/* add by Victor */
			$this->idfuncionario = $this->input->post('funcionarios_alocados', TRUE);
			$this->idfuncionario_antigos = $this->input->post('funcionarios_alocados_antigos', TRUE);
			$campo_funcionario = $this->input->post('campo_funcionario', TRUE);		
			
			$this->endereco = $this->input->post('endereco', TRUE);
			$this->endereco_numero = $this->input->post('endereco_numero', TRUE);
			$this->endereco_complemento = $this->input->post('endereco_complemento', TRUE);
			$this->bairro = $this->input->post('bairro', TRUE);
			$this->idestado = $this->input->post('idestado', TRUE);
			$this->idcidade = $this->input->post('idcidade', TRUE);
			$this->pontos_referencias = $this->input->post('pontos_referencias', TRUE);
			
			if(!empty($campo_funcionario)){
				if(empty($this->idfuncionario)){
					$this->idfuncionario = array();
				}
				$this->load->model("usuario_model");
				$funcionario = json_decode($this->usuario_model->get_by_id_nome($campo_funcionario));
				$funcionario_id = array($funcionario->nome."-".$funcionario->idusuario);
				$this->idfuncionario = array_merge($this->idfuncionario, $funcionario_id);
			}
			//pexit("teste", $this);
        }
    }
	
	//função que uitilizo para verificar as permições de visualização de atendimento e retornar a condição (se houver)
	function verifica_permissao_atendimento(){
		 // EXIBIÇÃO DE ATENDIMENTOS REFERENTE AOS NÍVEIS DE ACESSO
		 /* 1 => Administrador => visualiza tudo
		    2 => Gerente/Supervisor => visualisa aqueles atendimentos em que ele participa direta e indiretamente
		    3 => Funcionarios => visualiza somente os atendimentos no qual ele esta alocado.*/		
		$condicao = "";
		if($this->session->userdata('usuario')->idpermissao == 1){
			$condicao = "";

		}elseif($this->session->userdata('usuario')->idpermissao == 2){
			$ids = $this->db->query("SELECT DISTINCT(a.idatendimento) FROM vt_atendimento a LEFT JOIN vt_usuario_atendimento ua ON ua.idatendimento = a.idatendimento LEFT JOIN vt_usuario_relacionamento ur ON ua.idusuario = ur.id_usuario WHERE ((a.criado_por = ".$this->session->userdata('usuario')->idusuario.") OR ((ua.idusuario = ".$this->session->userdata('usuario')->idusuario.") OR (ur.id_adm_gerente = ".$this->session->userdata('usuario')->idusuario.")))")->result();
			//pexit("teste", $this->db->last_query());
			if(!empty($ids)){
				$ids_permitidos = '';
				foreach($ids as $id){
					$ids_permitidos .= $id->idatendimento.',';
				}
				$ids_permitidos = substr($ids_permitidos, 0, -1);
				$condicao .= "at.idatendimento IN ({$ids_permitidos})";		
			}
			else{
				$condicao .= "at.idatendimento = 0";	
			}
			
		}elseif($this->session->userdata('usuario')->idpermissao == 3){
			$ids = $this->db->query("SELECT idatendimento FROM ".$this->db->dbprefix. "usuario_atendimento WHERE idusuario = ".$this->session->userdata('usuario')->idusuario)->result();
			
			if(!empty($ids)){
				$ids_permitidos = '';
				foreach($ids as $id){
					$ids_permitidos .= $id->idatendimento.',';
				}
				$ids_permitidos = substr($ids_permitidos, 0, -1);
				$condicao .= "at.idatendimento IN ({$ids_permitidos})";		
			}
			else{
				$condicao .= "at.idatendimento = 0";	
			}		
		}
		return $condicao;
	}
	
	//function get_atendimentos($campo_busca='', $exibe_finalizados=''){
	function get_atendimentos($params = array()){	
		if(!empty($params)){
			extract($params, EXTR_OVERWRITE);
		}	
		 // EXIBIÇÃO DE ATENDIMENTOS REFERENTE AOS NÍVEIS DE ACESSO
		 /* 1 => Administrador => visualiza tudo
		    2 => Gerente/Supervisor => visualisa aqueles atendimentos em que ele participa direta e indiretamente
		    3 => Funcionarios => visualiza somente os atendimentos no qual ele esta alocado.*/		

		$condicao = $this->verifica_permissao_atendimento();
		
		//pexit("teste", $this->db->last_query());
		
	    $this->db->select("at.idatendimento,at.idempresa,at.titulo,at.prioridade, at.data_agendada,at.hora_agendada,at.endereco, at.endereco_numero, at.endereco_complemento,at.bairro, at.status,cli.idcliente, cli.nome AS nome_cliente, GROUP_CONCAT(usu.nome ) as nome_usuario");
		$this->db->from("vt_atendimento at");
		//adicionei left em todos os joins by lincoln
		$this->db->join("vt_cliente cli", "cli.idcliente = at.idcliente", "left");
		$this->db->join("vt_usuario_atendimento usu_at", "usu_at.idatendimento = at.idatendimento", "left");
		$this->db->join("vt_usuario usu", "usu.idusuario = usu_at.idusuario", "left");				
		$this->db->where("at.idempresa = ".$this->session->userdata('usuario')->idempresa);
		if(!empty($campo_busca)){
			$where_campo_busca = "(at.titulo LIKE '%{$campo_busca}%' OR usu.nome LIKE '%{$campo_busca}%' OR cli.nome LIKE '%{$campo_busca}%')";
			$this->db->where($where_campo_busca);
		}
		if(!empty($condicao)){
			$this->db->where($condicao);
		}
		$this->db->group_by("at.idatendimento");
        $this->db->order_by("at.prioridade");
		$this->db->order_by("at.data_agendada","DESC");
		$this->db->order_by("at.ordem");
		$this->db->order_by("at.titulo");
		if((isset($offset)) && (isset($per_page))){
			$this->db->limit($per_page, $offset);
		}		
        $atendimentos =  $this->db->get();
		//print $this->db->last_query();
	    return $atendimentos;
		/*
		$atendimentos = $this->db->query("
				SELECT 
				at.idatendimento,at.idempresa,at.titulo,at.prioridade, at.data_agendada,at.hora_agendada,at.endereco, at.endereco_numero, at.endereco_complemento, 	at.bairro, at.status,cli.idcliente, cli.nome AS nome_cliente, 
				GROUP_CONCAT(usu.nome SEPARATOR ', ') as nome_usuario
			FROM 
				vt_atendimento at
				INNER JOIN vt_cliente cli ON (cli.idcliente = at.idcliente)
				INNER JOIN vt_usuario_atendimento usu_at ON (usu_at.idatendimento = at.idatendimento)		
				INNER JOIN vt_usuario usu ON (usu.idusuario = usu_at.idusuario)		
			WHERE 
				(
				at.idempresa = '".$this->session->userdata('usuario')->idempresa."'
				{$campo_busca}
				{$condicao}
				)
			GROUP BY at.idatendimento	
			ORDER BY
			  at.prioridade ASC,
			  at.data_agendada DESC,
			  at.ordem ASC,
			  at.titulo ASC	
			{$condicao_limit}
			")->result();

			//$atendimentos =  $this->db->get();
			print $this->db->last_query();
			//pr('atendimentos',$atendimentos);
			return $atendimentos;			
			*/
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

			$idfuncionario = $this->idfuncionario;
			unset($this->idfuncionario);
			
			// return $this->db->insert_id()
			/* Bloco if colocado para cadastrar os funcionários alocados */
			if ($this->db->insert("atendimento", $this)){
				$id_registro = (int) $this->db->insert_id();
				foreach($idfuncionario as $ids){
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


    function delete($idregistro) {
        // Garantir que o ID que está sendo passado é um inteiro ou uma String que representa um inteiro.
        $idregistro = (int) $idregistro;
        if ($idregistro > 0) {
			return $this->db->delete('vt_atendimento', array('idatendimento' => $idregistro,  'idempresa'=>$this->session->userdata('usuario')->idempresa)) ? TRUE : FALSE ; 
        }
        else
            return FALSE;
    }   	



	function updateApp($dataTime, $hora, $latitude, $lat, $longitude, $long, $idUsuario, $idAtendimento, $status=NULL, $txtStatus=NULL, $textoCheck){
		if($status){
			$this->status = $txtStatus;
			$this->db->where("idatendimento", (int)$idAtendimento);
			$this->db->update("atendimento", $this);
			$this->status = $txtStatus;
		}
		else{
			$this->status = "em_andamento";
			$this->db->where("idatendimento", (int)$idAtendimento);
			$this->db->update("atendimento", $this);
			$this->status = "em_andamento";	
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
		$this->db->where("id_usuario", (int)$id_usuario);
		$this->db->where("notificar", "sim");
		$qdt_notficacao = $this->db->count_all_results();
		return $qdt_notficacao;
	}

	/**
	 * Script responsavel por atualizar as informações de notificações fazendo com que as que ja tenham sido baixadas não sejam notificadas novamente. Para o aplicativo
	*/
	function atualizar_notificacao($id_usuario){
		$id_usuario = (int)$id_usuario;
		$at = $this->db->query("UPDATE " . $this->db->dbprefix . "atendimentos_novos SET notificar = 'nao' WHERE id_usuario = '".$id_usuario."'");
	}


	function marcar_notificacao($id_atendimento){
		$marcar = $this->db->query("UPDATE " . $this->db->dbprefix . "atendimentos_novos SET data_cadastro = NOW(), notificar = 'sim' 
		WHERE id_atendimento = '{$id_atendimento}'");
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
			$this->db->where('idempresa', $this->session->userdata('usuario')->idempresa);
			
			$funcionarios_antigos = $this->idfuncionario_antigos;
			$funcionarios = $this->idfuncionario;
			unset($this->idfuncionario_antigos, $this->idfuncionario);
			
			if ($this->db->update("atendimento", $this)){
				$user_alocado_antigos = array();
				$user_alocado = array();
				if(!empty($funcionarios_antigos)){
					foreach($funcionarios_antigos as $ids_antigos){
						$valores_antigos = explode("-",$ids_antigos);
						$idfuncionario_antigos = (int) end($valores_antigos);
						$user_alocado_antigos[] = $idfuncionario_antigos;
					}
				}
				if(!empty($funcionarios)){
					foreach($funcionarios as $ids){
						$valores = explode("-",$ids);
						$idfuncionario = (int) end($valores);
						$user_alocado[] = $idfuncionario;
					}	
				}
				$arr = array_diff($user_alocado_antigos, $user_alocado);
				$arr2= array_diff($user_alocado, $user_alocado_antigos);
				foreach($user_alocado as $atualiza){
					$update_status_user_atendimento_selecionado = $this->db->query("UPDATE ".$this->db->dbprefix."usuario_atendimento SET status = '".$this->status."' WHERE idusuario = {$atualiza} AND idatendimento = {$idatendimento}");
				}
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
            return $this->db->query("SELECT * FROM " . $this->db->dbprefix . "atendimento 
			WHERE idatendimento = {$idatendimento} 
			AND idempresa = ".$this->session->userdata('usuario')->idempresa."
			LIMIT 1")->row();
        else
            return 0;
    }

    function get_local_atendimento($idatendimento) {
        $idatendimento = (int) $idatendimento;
        if ($idatendimento > 0){
            /*$atendimento = $this->db->query("
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
			")->row();*/
			$atendimento = $this->db->query("
			SELECT 
				at.idatendimento,
				at.idempresa,
				at.titulo,
				at.prioridade, 
				at.latitude,
				at.longitude,				
				at.data_agendada,
				at.hora_agendada,
				at.endereco, 
				at.endereco_numero, 
				at.endereco_complemento, 
				at.bairro, 
				at.status,
				cli.idcliente,
				cli.nome AS nomecliente, 
				es.nome AS nomeestado, 
				es.sigla AS uf, 
				ci.nome AS nomecidade,				
				GROUP_CONCAT(usu.nome SEPARATOR ', ') as nome_usuario
			FROM 
				vt_atendimento at
				INNER JOIN vt_cliente cli ON (cli.idcliente = at.idcliente)
				INNER JOIN vt_usuario_atendimento usu_at ON (usu_at.idatendimento = at.idatendimento)
				INNER JOIN vt_usuario usu ON (usu.idusuario = usu_at.idusuario)	
				INNER JOIN vt_cidade ci ON (ci.idcidade = at.idcidade)
				INNER JOIN vt_estado es ON (es.idestado = at.idestado)		
			WHERE 
				at.idatendimento = {$idatendimento}
			GROUP BY 
				at.idatendimento	
			LIMIT 1	
			")->row();
			//pr('$atendimento', $atendimento);
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
						MAX(us_at.data_hora_checkin) AS data_hora_ultima 
					FROM 
						vt_usuario_atendimento us_at, 
						vt_atendimento at 
					WHERE 
						at.idatendimento = us_at.idatendimento AND 
						at.idempresa = {$idempresa} AND 
						us_at.idusuario = us.idusuario 
					GROUP BY 
						us_at.idusuario
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
			
			//print $this->db->last_query();
			//pr('$usuarios',$usuarios);
			
            return $usuarios; 
		}
        else
            return 0;
    }
	
	/**
     *
     * Retorna varios objeto de atendimento de um ID de empresa específico, caso encontre. Caso não encontre retorna '0'.
     * @param type $idatendimento
     * @return objeto atendimento 
     */
	function get_atendimentos_mapa($status = ""){
		$idempresa = $this->session->userdata('usuario')->idempresa;
		if(!empty($idempresa)){
			
			$condicao = $this->verifica_permissao_atendimento();
			//pexit("teste", $this->db->last_query());
			
			$this->db->select('at.idatendimento, at.titulo, at.latitude, at.longitude, at.status');
			$this->db->select('at.data_agendada, at.hora_agendada, at.endereco, at.endereco_numero, at.endereco_complemento, at.bairro');
			$this->db->select('cli.nome AS nomecliente, es.nome AS nomeestado, es.sigla AS uf, ci.nome AS nomecidade, GROUP_CONCAT(usu.nome SEPARATOR ", ") as nome_usuario', false);
			$this->db->select('(SELECT date(ua.data_hora_checkin) FROM '.$this->db->dbprefix.'usuario_atendimento as ua WHERE ua.idatendimento = at.idatendimento ORDER BY ua.data_hora_checkin ASC LIMIT 1) as ultimo_checkin');
			$this->db->select('(SELECT date(ua.data_hora_checkout) FROM '.$this->db->dbprefix.'usuario_atendimento as ua WHERE ua.data_hora_checkout is not null AND ua.idatendimento = at.idatendimento ORDER BY ua.data_hora_checkout ASC LIMIT 1) as ultimo_checkout');
			$this->db->from($this->db->dbprefix . 'atendimento at');
			$this->db->join($this->db->dbprefix."cliente cli", "cli.idcliente = at.idcliente", "left");
			$this->db->join($this->db->dbprefix."usuario_atendimento usu_at", "usu_at.idatendimento = at.idatendimento", "left");
			$this->db->join($this->db->dbprefix."usuario usu", "usu.idusuario = usu_at.idusuario", "left");		
			$this->db->join($this->db->dbprefix."cidade ci", "ci.idcidade = at.idcidade", "left");		
			$this->db->join($this->db->dbprefix."estado es", "es.idestado = at.idestado", "left");		
			$this->db->where("at.idempresa", $idempresa);
			$this->db->where("at.latitude IS NOT NULL");
			$this->db->where("at.longitude IS NOT NULL");
			if(!empty($condicao)){
				$this->db->where($condicao);
			}
			//if(!empty($status)){
				/*
				if($status == "em_espera"){
					$this->db->having("((at.data_agendada = '".date("Y-m-d")."') AND (at.status = 'em_espera'))");
				}else if($status == "em_atraso"){
					$this->db->having("(((at.data_agendada < '".date("Y-m-d")."') AND (ultimo_checkout IS NULL)) or (at.status = 'em_atraso'))");
				}else if($status == "em_andamento"){
					$this->db->having("((ultimo_checkout IS NULL) AND (at.status = 'em_andamento'))");
				}else if($status == "finalizado"){
					$this->db->having("((ultimo_checkout IS NOT NULL) AND (date(ultimo_checkout) = '".date("Y-m-d")."') AND (at.status = 'finalizado'))");
				}else if($status == "nao_concluido"){
					$this->db->having("((ultimo_checkout IS NOT NULL) AND (date(ultimo_checkout) = '".date("Y-m-d")."') AND (at.status = 'nao_concluido'))");
				}
				*/
				/*
				switch ($status){
					case "em_espera":
						$this->db->having("((at.data_agendada = '".date("Y-m-d")."') AND (at.status = 'em_espera'))");
						break;
					case "em_atraso":
						$this->db->having("(((at.data_agendada < '".date("Y-m-d")."') AND (ultimo_checkout IS NULL)) or (at.status = 'em_atraso'))");
						break;
					case "em_andamento":
						$this->db->having("((ultimo_checkout IS NULL) AND (at.status = 'em_andamento'))");
						break;
					case "finalizado":
						$this->db->having("((ultimo_checkout IS NOT NULL) AND (date(ultimo_checkout) = '".date("Y-m-d")."') AND (at.status = 'finalizado'))");
						break;
					case "nao_concluido":
						$this->db->having("((ultimo_checkout IS NOT NULL) AND (date(ultimo_checkout) = '".date("Y-m-d")."') AND (at.status = 'nao_concluido'))");
						break;
				}			
			}
			*/
			$this->db->having("((at.data_agendada = '".date("Y-m-d")."') AND (at.status = 'em_espera'))");
			$this->db->or_having("(((at.data_agendada < '".date("Y-m-d")."') AND (ultimo_checkout IS NULL)) or (at.status = 'em_atraso'))");
			$this->db->or_having("((ultimo_checkout IS NULL) AND (at.status = 'em_andamento'))");
			$this->db->or_having("((ultimo_checkout IS NOT NULL) AND (date(ultimo_checkout) = '".date("Y-m-d")."') AND (at.status = 'finalizado'))");
			$this->db->or_having("((ultimo_checkout IS NOT NULL) AND (date(ultimo_checkout) = '".date("Y-m-d")."') AND (at.status = 'nao_concluido'))");
			
			$this->db->group_by('at.idatendimento');
			$this->db->order_by('at.titulo');
			$query = $this->db->get();
			
			
			return $query;
		}else{
			return 0;
		}
	}	
	
	function get_funcionario_checkin(){
		$idempresa = $this->session->userdata('usuario')->idempresa;
		if(!empty($idempresa)){
		
			$this->db->select('ua.*, u.nome as nome_usuario, at.titulo as titulo_atendimento');
			$this->db->select('CASE WHEN ua.latitude_checkout is not null THEN ua.latitude_checkout ELSE ua.latitude_checkin END AS latitude', false);
			$this->db->select('CASE WHEN ua.longitude_checkout is not null THEN ua.longitude_checkout ELSE ua.longitude_checkin END AS longitude', false);
			$this->db->from($this->db->dbprefix . 'usuario_atendimento as ua, '.$this->db->dbprefix.'usuario as u, '.$this->db->dbprefix.'atendimento as at');
			$this->db->where("u.idusuario = ua.idusuario");
			$this->db->where("at.idatendimento = ua.idatendimento");
			$this->db->where("u.ativo", "sim");
			//utilizado para pegar a ultima data hora de um respectivo usuario
			$this->db->where("data_hora_checkin =(SELECT MAX(ua2.data_hora_checkin )FROM vt_usuario_atendimento as ua2 WHERE ua2.idusuario = ua.idusuario )");
			//$this->db->where("((date(ua.data_hora_checkin) = '".date("Y-m-d")."') OR (ua.status = 'em_andamento'))");
			$this->db->where("at.idempresa", $idempresa);
			$query = $this->db->get();
			
			return $query;
		}else{
			return 0;
		}
	}
	
	function atualizarStatus($idAtendimento){
		$idAtendimento = (int)$idAtendimento;
		if($idAtendimento > 0){
			$updateAtendimento = $this->db->query("
				UPDATE 
					".$this->db->dbprefix."atendimento 
				SET 
					status = 'em_atraso' 
				WHERE 
					idatendimento = {$idAtendimento}
			");
			$updateAtendimentoUser = $this->db->query("
				UPDATE 
					".$this->db->dbprefix."usuario_atendimento 
				SET 
					status = 'em_atraso' 
				WHERE 
					idatendimento = {$idAtendimento}
			");
		}
		//return 1;
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
					usatendimento.status, 
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
				GROUP_CONCAT(ua.status) AS status,
				COUNT(c.idcliente) AS qtde
			FROM
				vt_usuario u
				INNER JOIN " . $this->db->dbprefix . "usuario_atendimento ua ON ua.idusuario = u.idusuario
				INNER JOIN " . $this->db->dbprefix . "atendimento a ON a.idatendimento = ua.idatendimento AND a.idempresa = u.idempresa
				INNER JOIN " . $this->db->dbprefix . "cliente c ON c.idcliente = a.idcliente
				INNER JOIN " . $this->db->dbprefix . "estado e ON e.idestado = a.idestado
				INNER JOIN " . $this->db->dbprefix . "cidade cd ON cd.idcidade = a.idcidade
			WHERE
				u.idusuario = '".$id_usuario."'
			GROUP BY 
				ua.status
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
        
		if(($this->session->userdata('usuario')->idpermissao == 1) or ($this->session->userdata('usuario')->idpermissao == 2))
			return true;	
		else
        	return ($result->num_rows() > 0) ? true : false;
		
    }

}

/* End of file atendimento_model.php */
    /* Location: ./application/models/atendimento_model.php */