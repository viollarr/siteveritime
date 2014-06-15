<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');




class Atendimento extends CI_Controller {

    public function __construct() {
        parent::__construct();
		$this->load->helper('text');
    }

    function index() {
        $this->lista();
    }

    function lista($offset = 0) {
        $dados = array();
		$parms = array();
				
		$this->load->model("atendimento_model");

        // Pegar box da paginação.
        $dados['paginacao'] = $this->pagination->paginacao(
                array(
                    'uri' => 'atendimento/lista/',
                    'total_rows' => $this->atendimento_model->get_atendimentos($parms)->num_rows()      //$this->cliente_model->contar_todos_registros()//'5'
                )
        );
		$parms["per_page"] = $this->pagination->get_per_page();
		$parms["offset"] = $offset;
		$parms['campo_busca'] = '';

		$dados['atendimentos'] = $this->atendimento_model->get_atendimentos($parms)->result();
		//pr("teste", $this->db->last_query());
		
        $dados['view'] = $this->load->view('atendimento/lista', $dados, TRUE);
        $this->load->view('dashboard/interna', $dados);
    }


	public function busca($offset = 0) {
        $dados = array();
		$parms = array();
				
		$this->load->model("atendimento_model");
		//$exibe_finalizados = $this->input->post('exibe_finalizados'); //se tiver checado o valor é "sim"

		$parms['campo_busca'] = $this->input->post('campo_busca', TRUE);
				
        // Pegar box da paginação.
		$dados['paginacao'] = '';

        if (!empty($parms['campo_busca'])) {
            $dados['atendimentos'] = $this->atendimento_model->get_atendimentos($parms)->result();

			$dados['view'] = $this->load->view('atendimento/lista', $dados, TRUE);
            $this->load->view('dashboard/interna', $dados);
        } else {
            // Redireciona para a tela inicial do Dashboard.
           redirect('atendimento/');
        }
    }

	function enviar_notificacao(){
        $this->load->model("atendimento_model");
		$idatendimento = $this->input->post('idatendimento', TRUE);

		$notificacao = $this->atendimento_model->marcar_notificacao($idatendimento);		
		
		if ($notificacao) {
			  $this->session->set_flashdata('msg_controller_sucesso', 'A Notificação foi envida com sucesso. O funcionário receberá assim que os dados forem sincronizados.');
			  redirect('/atendimento/lista/');
		}else{
			$this->session->set_flashdata('msg_controller_erro', 'A Notificação <strong>não</strong> foi enviada.');
			redirect('/dashboard/mensagem/');		
		}		
		
	}


	public function consultaApp($id_usuario,$id_empresa){
		$this->load->model("atendimento_model");
        $this->db->query('SELECT * FROM '.$this->db->dbprefix.'atendimento');
        $dados['atendimentos'] = $this->db->get()->result();
	}

    public function cadastro() {
		
        //pr('session(idestado)', $this->input->post('idestado'));
        
		$this->load->model("atendimento_model");
		$this->load->model("cliente_model");
		$this->load->model("usuario_model");
		$this->load->model("estado_model");

        $dados = array();
		$dados["clientes"] = $this->cliente_model->get_clientes_by_empresa();
		$dados["estados"] = $this->estado_model->get_estados();
		
		$idestado_selecionado = $this->input->post('idestado');				
		
		if ($idestado_selecionado){
			$this->load->model("cidade_model");
			$dados["cidades"] = $this->cidade_model->get_cidade_by_estado($idestado_selecionado);
		}
				
        $dados['menu_selecionado'] = 'menu_atendimentos';
        $dados['view'] = $this->load->view('atendimento/cadastro', $dados, TRUE);
        $this->load->view('dashboard/interna', $dados);
    }

    /**
     * Valida os dados do formulário e chama o model para salvar no banco de dados os dados do novo registro.
     */
    public function insert() {


        // 1º VALIDAR O FORMULÁRIO.
		//$this->form_validation->set_rules('titulo', 'Título', 'required');

        if ($this->validar_form()) :
            $this->load->model("atendimento_model");


			/*Código para capturar a latitude e longitude do endereço informado.*/
			$this->load->model("cidade_model");
			$endereco = $this->input->post('endereco');
			$endereco_numero = $this->input->post('endereco_numero');
			$bairro = $this->input->post('bairro');
			$cidade = $this->cidade_model->get_cidade_by_id($this->input->post('idcidade'));
			$nome_cidade = $cidade->nome;
			$address = $endereco.' '.$endereco_numero.' '.$nome_cidade;
			$address = convert_accented_characters(str_replace(" ", "+", $address));
			$json = file_get_contents("http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false");//&region=$region
			$json = json_decode($json);
			$latitude = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
			$longitude = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};

			//print 'http://maps.google.com/maps/api/geocode/json?address='.$address.'&sensor=false';

			if (empty($latitude)){
				$latitude = 0;
				$longitude = 0;
			}
				
/*			var_dump("http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false");
			exit;
*/            // 2º CHAMAR MÉTODO POST() DO MODELO.
            $this->atendimento_model->post($latitude, $longitude);
			
				$idatendimento = $this->atendimento_model->insert();
				
				if ($idatendimento > 0):
					$this->session->set_flashdata('msg_controller_sucesso', 'Atendimento cadastrado com sucesso.');
					redirect('/atendimento/lista/');
				else:
					$this->session->set_flashdata('msg_controller_erro', 'Cadastro de atendimento <strong>não</strong> efetuado.');
					redirect('/dashboard/mensagem/');
				endif;
				
        else:
            $this->cadastro();
        endif;
    }

    public function editar() {
        $idatendimento = $this->input->post('idatendimento');

		$this->load->model("cliente_model");
		$this->load->model("estado_model");
		$this->load->model("usuario_model");
		
		
        // Carregando o model de atendimento.
        $this->load->model("atendimento_model");
        $atendimento = $this->atendimento_model->get_by_id($idatendimento);
		
		if(!empty($atendimento->idtag)){
			$tag = $this->atendimento_model->get_tag_by_id($atendimento->idtag);
			$atendimento->idtag = $tag->tag;
		}


        if (!empty($atendimento)) {
            // Passa os dados para o formulário de edição (view).
			$dados = array();
            $dados['atendimento'] = $atendimento;
			$dados["contatos"] = $this->cliente_model->get_contatos_by_empresa();
			$dados["clientes"] = $this->cliente_model->get_clientes_by_empresa();
			$dados["funcionarios_alocados"] = $this->usuario_model->get_usuarios_by_atendimento($idatendimento);
			$dados["estados"] = $this->estado_model->get_estados();

			
            $idestado_selecionado = $atendimento->idestado;
			
			
            if ($idestado_selecionado) {
                $this->load->model("cidade_model");
                $dados["cidades"] = $this->cidade_model->get_cidade_by_estado($idestado_selecionado);
            }

            $dados['view'] = $this->load->view('atendimento/editar', $dados, TRUE);
            $this->load->view('dashboard/interna', $dados);
        } else {
            // Redireciona para a tela inicial do Dashboard.
            //redirect('/dashboard/');
        }
    }


 public function reagendar() {
        $idatendimento = $this->input->post('idatendimento');

		$this->load->model("cliente_model");
		//$this->load->model("estado_model");
		$this->load->model("usuario_model");
		
		
        // Carregando o model de atendimento.
        $this->load->model("atendimento_model");
        $atendimento = $this->atendimento_model->get_by_id($idatendimento);
		
		if(!empty($atendimento->idtag)){
			$tag = $this->atendimento_model->get_tag_by_id($atendimento->idtag);
			$atendimento->idtag = $tag->tag;
		}


        if (!empty($atendimento)) {
            // Passa os dados para o formulário de edição (view).
			$dados = array();
            $dados['atendimento'] = $atendimento;
			$dados["clientes"] = $this->cliente_model->get_clientes_by_empresa();
			$dados["funcionarios_alocados"] = $this->usuario_model->get_usuarios_by_atendimento($idatendimento);
			//$dados["estados"] = $this->estado_model->get_estados();

			
            //$idestado_selecionado = $atendimento->idestado;
			
			
            /*if ($idestado_selecionado) {
                $this->load->model("cidade_model");
                $dados["cidades"] = $this->cidade_model->get_cidade_by_estado($idestado_selecionado);
            }*/
            $dados['view'] = $this->load->view('atendimento/reagendar', $dados, TRUE);
            $this->load->view('dashboard/interna', $dados);
        } else {
            // Redireciona para a tela inicial do Dashboard.
            //redirect('/dashboard/');
        }
    }


    /**
     * Valida os dados do formulário e chama o model para salvar no banco de dados os dados atualizados.
     */
    public function update() {

        $idatendimento = $this->input->post('idatendimento');

        if (!empty($idatendimento)) {
            // 1º VALIDAR O FORMULÁRIO.
            if ($this->validar_form()) {
                $this->load->model("atendimento_model");
				
				/*Código para capturar a latitude e longitude do endereço informado.*/
				$this->load->model("cidade_model");
				$endereco = $this->input->post('endereco');
				$endereco_numero = $this->input->post('endereco_numero');
				$cidade = $this->cidade_model->get_cidade_by_id($this->input->post('idcidade'));
				$nome_cidade = $cidade->nome;
				$address = $endereco.' '.$endereco_numero.' '.$nome_cidade;
				$address = convert_accented_characters(str_replace(" ", "+", $address));
				$json = file_get_contents("http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false");//&region=$region
				$json = json_decode($json);
				$latitude = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
				$longitude = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};

				if (empty($latitude)){
					$latitude = 0;
					$longitude = 0;
				}				
				
				$novo_atendimento = $this->input->post('novo_atendimento');
				
				// 2º CHAMAR MÉTODO POST() DO MODELO.
				$this->atendimento_model->post($latitude, $longitude);
				

                // 3º CHAMAR MÉTODO UPDATE() DO MODELO.
                $atualizado = $this->atendimento_model->update($idatendimento);

				
                if ($atualizado) {
                 	if($novo_atendimento == 'sim'){
						$this->atendimento_model->marcar_notificacao($idatendimento);
					}
					
                    $this->session->set_flashdata('msg_controller_sucesso', 'Atendimento atualizado com sucesso.');
                    redirect('/atendimento/lista/');
                } else {
                    $this->session->set_flashdata('msg_controller_erro', 'Atualização <strong>não</strong> efetuada.');
                    redirect('/dashboard/mensagem/');
                }
            } else {
                // Retorna para o formulário de edição caso não passe na validação do form.
                $this->session->set_userdata('idatendimento', $idatendimento);
                $this->editar();
            }
        } else {
            // Redireciona para a tela principal caso não tenha nenhum ID preenchido.
            redirect('/dashboard/');
        }
    }


public function update_reagendar() {

        $idatendimento = $this->input->post('idatendimento');

        if (!empty($idatendimento)) {
            // 1º VALIDAR O FORMULÁRIO.
            if ($this->validar_form_reagendar()) {
                $this->load->model("atendimento_model");

				$latitude = $this->input->post('latitude');
				$longitude = $this->input->post('longitude');
				$novo_atendimento = $this->input->post('novo_atendimento');

				// 2º CHAMAR MÉTODO POST() DO MODELO.
				$this->atendimento_model->post($latitude, $longitude);

                // 3º CHAMAR MÉTODO UPDATE() DO MODELO.
                $atualizado = $this->atendimento_model->update($idatendimento);

				
                if ($atualizado) {
                 	if($novo_atendimento == 'sim'){
						$this->atendimento_model->marcar_notificacao($idatendimento);
					}
					
                    $this->session->set_flashdata('msg_controller_sucesso', 'Atendimento reagendado com sucesso.');
                    redirect('/atendimento/lista/');
                } else {
                    $this->session->set_flashdata('msg_controller_erro', 'Reagendamento <strong>não</strong> efetuado.');
                    redirect('/dashboard/mensagem/');
                }
            } else {
                // Retorna para o formulário de edição caso não passe na validação do form.
                $this->session->set_userdata('idatendimento', $idatendimento);
                $this->reagendar();
            }
        } else {
            // Redireciona para a tela principal caso não tenha nenhum ID preenchido.
            redirect('/dashboard/');
        }
    }	
	
	

	public function excluir() {
		$this->load->model("atendimento_model");
		
		$idregistro = $this->input->post('idatendimento', TRUE);
		$excluido = $this->atendimento_model->delete($idregistro);
		
		if ($excluido) {
			$this->session->set_flashdata('msg_controller_sucesso', 'Atendimento deletado com sucesso.');
			redirect('atendimento/lista/');

		}else{
			$this->session->set_flashdata('msg_controller_erro', 'Exclusão <strong>não</strong> efetuada.');
			redirect('atendimento/lista/');
		}  		
	}	
	
    /**
     * Retonrar endereco principal para o local de atendimento.
     */
	public function getEnderecoPrincipal() {

        // Registros que serão exibidos.
		$idcliente = $this->input->post('idCliente');
        $this->db->select('endereco, endereco_numero, endereco_complemento, bairro, idcidade, idestado, pontos_referencias');
		$this->db->from('vt_cliente');
		$this->db->where("idcliente = '$idcliente'");
        $endereco = $this->db->get()->row();
		$cliente = '';
		if(!empty($endereco)){
			$cliente .= 
				$endereco->endereco."|".
				$endereco->endereco_numero."|".
				$endereco->endereco_complemento."|".
				$endereco->bairro."|".
				$endereco->idestado."|".
				$endereco->idcidade."|".
				$endereco->pontos_referencias;
				
			$this->db->select('idcliente_filial');
			$this->db->from('vt_cliente_filial');
			$this->db->where("idcliente = '$idcliente'");
			$endereco_filial = $this->db->get()->result();
			
			foreach($endereco_filial AS $filial){
				$cliente .= "|".$filial->idcliente_filial;
			}
		}

		echo $cliente;
	}	

    /**
     * Retonrar endereco da filial para o local de atendimento.
     */
	public function getEnderecoFilial() {

        // Registros que serão exibidos.
		$idcliente_filial = $this->input->post('idcliente_filial');
        $this->db->select('endereco, endereco_numero, endereco_complemento, bairro, idcidade, idestado, pontos_referencias');
		$this->db->from('vt_cliente_filial');
		$this->db->where("idcliente_filial = '$idcliente_filial'");
        $endereco_filial = $this->db->get()->row();
		$filial = '';
		if(!empty($endereco_filial)){
			$filial .=  
				$endereco_filial->endereco."|".
				$endereco_filial->endereco_numero."|".
				$endereco_filial->endereco_complemento."|".
				$endereco_filial->bairro."|".
				$endereco_filial->idestado."|".
				$endereco_filial->idcidade."|".
				$endereco_filial->pontos_referencias;
		}
			
		echo $filial;
	}
	
    /**
     * Retorna o id e nome do usuário para preenchimento do autocomplete.
     */
	public function getIdNomeUsuario($nomeUser){
		
		$nomeUser = urldecode($nomeUser);
		$this->load->model("usuario_model");
		$usuario = $this->usuario_model->get_by_id_nome($nomeUser);
		//pexit("last_query", $this->db->last_query());
		echo $usuario;
	}
	
    /**
     * Retorna o id e nome do usuário para preenchimento do autocomplete.
     */
	public function getIdNomeCliente($nomeCliente){
	
		$nomeCliente = urldecode($nomeCliente);
		$this->load->model("cliente_model");
		$cliente = $this->cliente_model->get_by_id_nome($nomeCliente);
		echo $cliente;
	}
	
	    /**
     * Retorna o id e nome do usuário para preenchimento do autocomplete.
     */
	public function getIdNomeResponsavel($nomeContato, $tipo = "", $conteudo_cliente = ""){
		
		$nomeContato = urldecode($nomeContato);
		
		$conteudo_cliente = urldecode($conteudo_cliente);

		if($tipo == "texto"){
			$a_buscar = "AND c.nome = '".$conteudo_cliente."'";
		}else if($tipo == "id"){
			$a_buscar = "AND cc.idcliente = '$conteudo_cliente'";
		}
		//pexit("teste2", $a_buscar);
		
		$idempresa = $this->session->userdata('usuario')->idempresa;
		
		$contato = $this->db->query("SELECT cc.idcliente_contato, cc.contato_responsavel, cc.contato_email FROM ".$this->db->dbprefix."cliente_contato as cc, ".$this->db->dbprefix."cliente as c WHERE cc.idcliente = c.idcliente ".$a_buscar." AND cc.contato_responsavel = '".$nomeContato."' AND idempresa = ".$idempresa." AND ativo = 'sim' ")->row();
		
		
		//$this->load->model("cliente_model");
		//$contato = $this->cliente_model->get_by_id_nome_contato($nomeContato);

		echo json_encode($contato);
	}
	
	
	
    /**
     * Retorna um json com (pass true ou false) para a verificação de usuário em outros atendimentos.
	 * Se for true ja passa também no json o texto correto e quais usuarios que estaria alocado em outro atendimento no mesmo dia e horario
     */
	public function getValidacaoUser($iduser, $data, $hora, $tempo, $nome, $idatendimento = NULL){
	
		$iduser = explode("%383",$iduser);
		$contIds = count($iduser);
		$nomes = explode("%383",$nome);
		$this->load->model("usuario_model");
		$nomeTexto = "";
		$numPlural = 0;
		$texto = "";

		for($i = 0; $i < ($contIds-1); $i++){
			$usuarios = $this->usuario_model->get_by_valida($iduser[$i], $idatendimento);
			$contador = 0;
			foreach($usuarios as $k => $usuario){
				$formatDataInformado =  explode("_", $data);
				$formatHoraInformado = explode("_",$hora);
				$formatTempoInformado = explode("_",$tempo);
				$formatDataBanco =  explode("-", $usuario->data_agendada);
				$formatHoraBanco = explode(":",$usuario->hora_agendada);
				$formatTempoBanco = explode(":",$usuario->tempo_estimado);
				$mikrotime_informado = mktime($formatHoraInformado[0], $formatHoraInformado[1], 0, $formatDataInformado[0], $formatDataInformado[1], $formatDataInformado[2]);
				$mikrotime_informado_tempo = mktime(($formatHoraInformado[0]+$formatTempoInformado[0]), ($formatHoraInformado[1]+$formatTempoInformado[1]), 0, $formatDataInformado[0], $formatDataInformado[1], $formatDataInformado[2]);				
				$mikrotime_banco = mktime($formatHoraBanco[0], $formatHoraBanco[1], $formatHoraBanco[2], $formatDataBanco[1], $formatDataBanco[2], $formatDataBanco[0]);
				$mikrotime_banco_tempo = mktime(($formatHoraBanco[0]+$formatTempoBanco[0]), ($formatHoraBanco[1]+$formatTempoBanco[1]), $formatHoraBanco[2], $formatDataBanco[1], $formatDataBanco[2], $formatDataBanco[0]);
					
				// if que verifica se a data informada para cada usuario é iguala  do banco para outros atendimentos, se esta em algum intervalo de tempo entra as datas dos atendimentos dos usuarios no banco
				// ou se ele é anterior a data do banco mais o tempo estimado é igual ou superior as datas do banco			
				if((($mikrotime_informado >= $mikrotime_banco) && ($mikrotime_informado <= $mikrotime_banco_tempo)) || (($mikrotime_informado_tempo >= $mikrotime_banco)&&($mikrotime_informado_tempo <= $mikrotime_banco_tempo)) || (($mikrotime_informado < $mikrotime_banco)&&($mikrotime_informado_tempo > $mikrotime_banco_tempo))){
					$usuario->valida = true;
					$contador++;
					$numPlural++;
					
				}
				else{
					$usuario->valida = false;
				}
			}
			if($contador > 0){
				$nome = urldecode($nomes[$i]);
				$nome = str_replace("_"," ", $nome);
				$nomeTexto .= "- ".$nome."\n";	
			}
		}
		if($numPlural == 1){
			$texto .= "O funcionário:\n\n";	
		}
		else{
			$texto .= "Os funcionários:\n\n";
		}
		$nomeTexto .="\nJá encontra-se alocado em outro atendimento no mesmo dia e horário.\nMesmo assim deseja prosseguir ?";
		
		if($numPlural>0){
			$passou = true;	
			$nomeTexto = $texto.$nomeTexto;	
		}
		else{
			$passou = false;
			$nomeTexto = '';	
		}
		
		$result = array("texto" => $nomeTexto, "pass" => $passou);
		echo json_encode($result);
	}

    /**
     * Verifica a possibilidade de exclusão de um funcionário do atendimento.
	 * return 
	 * TRUE 	=> pode excluir
	 * FALSE 	=> não pode excluir
     */
	public function vericarExclusaoFuncionarioAtendimento($idusuario, $idatendimento){
	
		$idusuario		= explode("_",$idusuario);
		$idusuario		= (int) $idusuario[1];
		$idatendimento 	= (int) $idatendimento;
		$this->load->model("usuario_model");
		$usuario_ex = $this->usuario_model->get_usuarios_by_atendimento_exclusao($idusuario, $idatendimento);
		echo $usuario_ex;
	}
	
    /**
     * Verifica a inclusão de um funcionário no atendimento.
	 * return 
	 * TRUE 	=> inclui sem problemas
	 * FALSE 	=> exibe alerta que o usuario ja esta cadastrado em outro atendimento nesse mesmo dia e horário.
     */
	public function vericarInclusaoFuncionarioAtendimento($idusuario){
	
		$idusuario		= (int) $idusuario;
		$this->load->model("usuario_model");
		$usuario_in = $this->usuario_model->get_usuarios_by_atendimento_inclusao($idusuario);
		echo $usuario_in;
	}
	
	
	
	
	function mapa_local($idatendimento){
		$this->load->model("atendimento_model");
		$dados["atendimento"] = $this->atendimento_model->get_local_atendimento($idatendimento);
		$this->load->view('atendimento/mapa_local', $dados);
	}
	
	

    /**
     * O controller deve validar o formulário antes de salvar no banco de dados.
     */
    private function validar_form() {
		
        $this->form_validation->set_rules('titulo', 'Título', 'required');
		$this->form_validation->set_rules('tag');
		$this->form_validation->set_rules('campo_responsavel', 'campo_responsavel');
		$this->form_validation->set_rules('nome_responsavel', 'Responsavel', 'callback_valida_responsavel');
		/* Alterado por Victor, depois por lincoln*/
		//$this->form_validation->set_rules('idcliente', 'Cliente', 'required');
		//$this->form_validation->set_rules('cliente_alocado', 'Cliente', 'required');
		$this->form_validation->set_rules('campo_cliente', 'campo_cliente');
		$this->form_validation->set_rules('cliente_alocado', 'Cliente', 'callback_valida_cliente');
		/* FIM */
		
		$this->form_validation->set_rules('descricao');
		$this->form_validation->set_rules('data_agendada', 'Data agendada', 'required|callback_valida_data');
		$this->form_validation->set_rules('hora_agendada', 'Hora agendada', 'required');
		$this->form_validation->set_rules('tempo_estimado', 'Tempo estimado', 'required');
		$this->form_validation->set_rules('prioridade', 'Prioridade', 'required');
		$this->form_validation->set_rules('status', 'Status', 'required');
		$this->form_validation->set_rules('tem_contra_senha', 'Possui contra-senha?', 'required');
		$this->form_validation->set_rules('endereco', 'Endereço', 'required');
		$this->form_validation->set_rules('endereco_numero');
		$this->form_validation->set_rules('endereco_complemento');		
		$this->form_validation->set_rules('bairro', 'Bairro', 'required');
		$this->form_validation->set_rules('idestado', 'Estado', 'required');
		$this->form_validation->set_rules('idcidade', 'Cidade', 'required');
		$this->form_validation->set_rules('contra_senha');
		$this->form_validation->set_rules('emails_assinatura');
		$this->form_validation->set_rules('meios_transportes');
		$this->form_validation->set_rules('pontos_referencias');
		$this->form_validation->set_rules('campo_funcionario', 'campo_funcionario', 'callback_valida_funcinario');
		$this->form_validation->set_rules('funcionarios_alocados[]');
		
        return $this->form_validation->run();
    }
	
	//função para verificar se o responsavel existe e se está preenchido
    public function valida_responsavel() {
        //recebendo variaveis login e login atual
        $campo_responsavel = $this->input->post('campo_responsavel');
        $nome_responsavel = $this->input->post('nome_responsavel');
        if (!empty($campo_responsavel)) {
			$this->load->model("cliente_model");
			$retorno = json_decode($this->cliente_model->get_by_id_nome_contato($campo_responsavel));

			if(empty($retorno)){
				$this->form_validation->set_message('valida_responsavel', "O campo <strong>Responsavel</strong> é invalido.");
				return false;
			}else{
				return true;
			}
        } else {
                return true;
        }
    }	
	
	//função para verificar se a data é uma data valida
    public function valida_data() {
        $data_agendada = fdata($this->input->post('data_agendada'), "-");
		$data_hoje = date("Y-m-d");
		
		if($data_agendada <= $data_hoje){
			$hora_agendada = $this->input->post('hora_agendada');
			if(!empty($hora_agendada)){
				$hora_agendada = str_replace(":", "", $hora_agendada);
				$hora_hoje = date("Hi");
				//pexit("test", $hora_hoje);
				if(($data_agendada != $data_hoje) || (($data_agendada == $data_hoje) && ($hora_agendada <= $hora_hoje))){
					$this->form_validation->set_message('valida_data', "Não é possivel cadastrar um atendimento com data retroativa. Verifique os campos <strong>Data agendada</strong> e <strong>Hora agendada</strong>");
					return false;
				}else{
					return true;
				}
			}
		}else{
			return true;
		}
		
    }	
	
	//função para verificar se o cliente existe e se está preenchido
    public function valida_cliente() {
        //recebendo variaveis login e login atual
        $campo_cliente = $this->input->post('campo_cliente');
        $cliente_alocado = $this->input->post('cliente_alocado');
        if (!empty($campo_cliente)) {
			$this->load->model("cliente_model");
			$retorno = json_decode($this->cliente_model->get_by_id_nome($campo_cliente));

			if(empty($retorno)){
				$this->form_validation->set_message('valida_cliente', "O campo <strong>Cliente</strong> é invalido.");
				return false;
			}else{
				return true;
			}
        } else {
            if (empty($cliente_alocado)) {
				$this->form_validation->set_message('valida_cliente', "O campo <strong>Cliente</strong> é necessário.");
				return false;
            } else {
                return true;
            }
        }
    }
		
	//função para verificar se o funcionario/colaborador está preenchido, caso esteja verifica se existe
    public function valida_funcinario() {
        //recebendo variaveis login e login atual
        $campo_funcionario = $this->input->post('campo_funcionario');
        $cliente_alocado = $this->input->post('cliente_alocado');
        if (!empty($campo_funcionario)) {
			$this->load->model("usuario_model");
			$retorno = json_decode($this->usuario_model->get_by_id_nome($campo_funcionario));

			if(empty($retorno)){
				$this->form_validation->set_message('valida_funcinario', "O campo <strong>Colaborador</strong> de <strong>Equipe Externa</strong> é invalido.");
				return false;
			}else{
				return true;
			}
        } else {
           return true;  
        }
    }
	

    private function validar_form_reagendar() {
		
		$this->form_validation->set_rules('data_agendada', 'Data agendada', 'required');
		$this->form_validation->set_rules('hora_agendada', 'Hora agendada', 'required');
		$this->form_validation->set_rules('tempo_estimado', 'Tempo estimado', 'required');
		$this->form_validation->set_rules('funcionarios_alocados[]');
		
        return $this->form_validation->run();
    }	
	




   
}

/* End of file atendimento.php */
/* Location: ./application/controllers/atendimento.php */