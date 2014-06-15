<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Acesso extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        // Destruir a sessão, por garantia, caso o usuário tenha esquecido de fazer logoff.
        // $this->session->sess_destroy();
        // Redirecionar para a tela de login.
        $this->load->view("acesso/login");
    }

    public function logar() {
        // Zerando variáveis da sessão para salvar uma nova mais abaixo.
        $this->session->unset_userdata('logado');
        $this->session->unset_userdata('usuario');

        // Login e senha digitados pelo usuário.
        $login = $this->input->post('login');
        // Encriptografar a senha para comparar com a do banco de dados que está encriptografada. Ver helpers/cripto_helper.php.
        // Caso venha usar outro método de encriptografia, verificar tamanho do valor possível do atributo no banco de dados.
        $senha = cripto($this->input->post('senha'));

        // Carregar model de usuário.
        $this->load->model('Usuario_model', 'usuario_model');
        $usuario_login = $this->usuario_model->get_idusuario($login, $senha);

        if ((!empty($usuario_login->idusuario)) && ($usuario_login->idusuario > 0)) {
            if ($usuario_login->ativo == "sim") {
                // Pegar dados do Usuário
                $usuario = $this->usuario_model->get_usuario($usuario_login->idusuario);
                // Salvar sessão do usuário logado.
                $this->session->set_userdata('logado', TRUE); // Variável usada pelos demais controllers para verificar se um usuário está logado.
                $this->session->set_userdata('usuario', $usuario); // Usuário foi retornado do banco de dados como um objeto.
                // Salvar o log da operação. salvar_log() está dentro do "log_helper".
                /*
                  if (!salvar_log('acesso', 'logar'))
                  $this->redirect_login("Não foi possível salvar o log. Favor entrar em contato com o desenvolvedor do sistema. [acesso/logar]");
                  else
                  redirect('/dashboard/');
                 */
                if($this->session->userdata('usuario')->idpermissao == 3){//se for apenas funcionário vai para a tela de atendimentos.
					redirect('/atendimento');
				}else{
					redirect('/mapa/visualizar/');
				}
				
            } else {
                $this->redirect_login("Este usuário foi desativado, por favor entre em contato com o adminstrador do sistema.");
            }
        }else
            $this->redirect_login("Usuário não encontrado.<br/>Verifique se login e senha estão corretos.");
    }


########################################################  ACESSO ANDROID ####################################################################################

    public function logarApp($login = NULL, $senha = NULL, $telefoneId = NULL) {
		$this->session->unset_userdata('logado');
        $this->session->unset_userdata('usuario');
		$login = $this->input->post('login');
		// Encriptografar a senha para comparar com a do banco de dados que está encriptografada. Ver helpers/cripto_helper.php.
		// Caso venha usar outro método de encriptografia, verificar tamanho do valor possível do atributo no banco de dados.
		$senha = cripto($this->input->post('senha'));
		$telefoneId = $this->input->post('telefoneId');
		$cadastraTelefone = true;
        $this->load->model('Usuario_model', 'usuario_model');
        $usuario_login = $this->usuario_model->get_idusuario($login, $senha);

        if ((!empty($usuario_login->idusuario)) && ($usuario_login->idusuario > 0)) {
            if ($usuario_login->ativo == "sim") {
                // Pegar dados do Usuário
                $usuario = $this->usuario_model->get_usuario($usuario_login->idusuario);
				$this->load->model('Atendimento_model', 'atendimento_model');
				$atendimentos = $this->atendimento_model->consultApp($usuario_login->idusuario);
				$notificacao = $this->atendimento_model->notificacao($idUsuario);
				$this->atendimento_model->atualizar_notificacao($usuario_login->idusuario);
				$cadTelefone = $this->usuario_model->cadTelefone($telefoneId,$usuario_login->idusuario);
				
				$emAndamento	= array();
				$emEspera		= array();
				$emAtraso		= array();
				$finalizado		= array();	
				$data_hora_atual 	= strtotime(date("Y-m-d H:i:s"));
				
				foreach($atendimentos as $atendimento){
					if($atendimento->status == "em_andamento"){
						$emAndamento[] = $atendimento;
						
					}
					elseif($atendimento->status == "em_espera"){
						$data_hora_banco = strtotime($atendimento->data_agendada." ".$atendimento->hora_agendada);
						
						if($data_hora_atual > $data_hora_banco){
							$this->atendimento_model->atualizarStatus($atendimento->idatendimento);
							$emAtraso[] = $atendimento;
						}
						else{
							$emEspera[] = $atendimento;
						}
					}
					elseif($atendimento->status == "em_atraso"){
						$emAtraso[] = $atendimento;
					}
					elseif($atendimento->status == "finalizado"){
						$finalizado[] = $atendimento;
					}
				}

				$gruposAtendimentos = $this->atendimento_model->consultAppGrupos($usuario_login->idusuario);

				$result["login"][] = array("acesso"=>true,"id_usuario"=>$usuario_login->idusuario,"nome_usuario"=>$usuario->nome_usuario,"email"=>$usuario->notif_email,"sms"=>$usuario->notif_sms,"notificacao"=>$notificacao,"grupos"=>$gruposAtendimentos,"atendimentos"=>$atendimentos,"emAndamento"=>$emAndamento,"emEspera"=>$emEspera,"emAtraso"=>$emAtraso,"finalizado"=>$finalizado);
				
				echo json_encode($result);
            } else {
				$result["login"][] = array("acesso"=>false,"mensagem"=>"erro1"); 
				echo json_encode($result);
            }
        }else{
			$result["login"][] = array("acesso"=>false,"mensagem"=>"erro2"); 
			echo json_encode($result);
		}
    }

	public function recuperarImei(){
		$this->session->unset_userdata('logado');
        $this->session->unset_userdata('usuario');
		$idTelefone = $this->input->post('idTelefone');
		$this->load->model("Usuario_model", "usuario_model");
		$this->load->model('Atendimento_model', 'atendimento_model');
		$usuario = $this->usuario_model->getTelefone($idTelefone);
		if((!empty($usuario->idusuario)) && ($usuario->idusuario > 0)){
			$notificacao = $this->atendimento_model->notificacao($usuario->idusuario);
			$atendimentos = $this->atendimento_model->consultApp($usuario->idusuario);
			$this->atendimento_model->atualizar_notificacao($usuario->idusuario);
			
			$emAndamento		= array();
			$emEspera			= array();
			$emAtraso			= array();
			$finalizado			= array();
			$data_hora_atual 	= strtotime(date("Y-m-d H:i:s"));
			
			foreach($atendimentos as $atendimento){
				if($atendimento->status == "em_andamento"){
					$emAndamento[] = $atendimento;
					
				}
				elseif($atendimento->status == "em_espera"){
					$data_hora_banco = strtotime($atendimento->data_agendada." ".$atendimento->hora_agendada);
					
					if($data_hora_atual > $data_hora_banco){
						$this->atendimento_model->atualizarStatus($atendimento->idatendimento);
						$emAtraso[] = $atendimento;
					}
					else{
						$emEspera[] = $atendimento;
					}
				}
				elseif($atendimento->status == "em_atraso"){
					$emAtraso[] = $atendimento;
				}
				elseif($atendimento->status == "finalizado"){
					$finalizado[] = $atendimento;
				}
			}
			
			$gruposAtendimentos = $this->atendimento_model->consultAppGrupos($usuario->idusuario);
					
			$result["login"][] = array("acesso"=>true,"id_usuario"=>$usuario->idusuario,"nome_usuario"=>$usuario->nome,"email"=>$usuario->notif_email,"sms"=>$usuario->notif_sms,"notificacao"=>$notificacao,"grupos"=>$gruposAtendimentos,"atendimentos"=>$atendimentos,"emAndamento"=>$emAndamento,"emEspera"=>$emEspera,"emAtraso"=>$emAtraso,"finalizado"=>$finalizado);
			$this->session->set_userdata('logado', TRUE); // Variável usada pelos demais controllers para verificar se um usuário está logado.
			$this->session->set_userdata('usuario', $usuario); // Usuário foi retornado do banco de dados como um objeto.
			
		}
		else{
			$result["login"][] = array("acesso"=>false,"mensagem"=>"erro1"); 
		}
		echo json_encode($result);
	}
	
	public function realizarCheck(){
		$this->load->model('Atendimento_model', 'atendimento_model');
		$this->load->model('Usuario_model', 'usuario_model');
		
		$lat 				= $this->input->post('latitude');
		$long 				= $this->input->post('longitude');
		$hora 				= $this->input->post('dataHora');
		$idAtendimento 		= $this->input->post('idAtendimento');
		$idUsuario 			= $this->input->post('idUsuario');
		$txtStatus 			= $this->input->post('txtStatus');
		$notif_email 		= $this->input->post('notif_email');
		$titulo_atendimento = $this->input->post('titulo');
		$idRelacionamento 	= $this->input->post('idRelacionamento');
		$tipoCheck 			= (boolean)$this->input->post('tipoCheck');
		
		$email_criador 		=  $this->atendimento_model->emailCriador($idAtendimento);
		
		//$testeCheck;
		if($tipoCheck){
			$dataTime  = "data_hora_checkin";
			$latitude  = "latitude_checkin";
			$longitude = "longitude_checkin";
			$textoCheck = "Check-In";
			$status		= false;
			$observacao = false;
			$txtObservacao = "";
		}
		else{
			$dataTime  = "data_hora_checkout";
			$latitude  = "latitude_checkout";
			$longitude = "longitude_checkout";
			$textoCheck = "Check-Out";
			$status		= true;
			$txtObservacao = $this->input->post('txtObservacao');
			
			if($txtObservacao!=""){
				$observacao = true;	
			}
			else{
				$observacao = false;	
			}
		}
		//$obs = $this->atendimento_model->insertComentApp($idUsuario, $idAtendimento, $txtObservacao, $hora);
		
		if($observacao){
			$obs = $this->atendimento_model->insertComentApp($idUsuario, $idAtendimento, $txtObservacao, $hora, $idRelacionamento);
		}
		
		$check = $this->atendimento_model->updateApp($dataTime, $hora, $latitude, $lat, $longitude, $long, $idUsuario, $idAtendimento, $status, $txtStatus, $textoCheck, $idRelacionamento);
		
		
		$upUsuarioLatLong = $this->usuario_model->updateLatLong($idUsuario, $lat, $long);
		
		
		if($check){
			if($notif_email == "sim"){
				$usuario = $this->usuario_model->get_usuario($idUsuario);
				$mensagem = "Realizado o ".$textoCheck." para o atendimento de título ".$titulo_atendimento.".";
				$this->envia_email($usuario->nome_usuario,$usuario->email, $email_criador->email,$textoCheck." realizado para o atendimento: ".$titulo_atendimento,$mensagem);
			}
		}
		
		$atendimentos = $this->atendimento_model->consultApp($idUsuario); //PEGA NOVO ARRAY DE ATENDIMENTO ATUALIZADO.
		$notificacao = $this->atendimento_model->notificacao($idUsuario);
		$this->atendimento_model->atualizar_notificacao($idUsuario);
		
		$emAndamento	= array();
		$emEspera		= array();
		$emAtraso		= array();
		$finalizado		= array();	
		$data_hora_atual 	= strtotime(date("Y-m-d H:i:s"));

		
		foreach($atendimentos as $atendimento){
			if($atendimento->status == "em_andamento"){
				$emAndamento[] = $atendimento;
				
			}
			elseif($atendimento->status == "em_espera"){
				$data_hora_banco = strtotime($atendimento->data_agendada." ".$atendimento->hora_agendada);
				
				if($data_hora_atual > $data_hora_banco){
					$this->atendimento_model->atualizarStatus($atendimento->idatendimento);
					$emAtraso[] = $atendimento;
				}
				else{
					$emEspera[] = $atendimento;
				}
			}
			elseif($atendimento->status == "em_atraso"){
				$emAtraso[] = $atendimento;
			}
			elseif($atendimento->status == "finalizado"){
				$finalizado[] = $atendimento;
			}
		}

		$gruposAtendimentos = $this->atendimento_model->consultAppGrupos($idUsuario);//PEGA NOVO ARRAY DE GRUPOS DE ATENDIMENTO ATUALIZADO.

		if($check){
			$result["login"][] = array("acesso"=>true,"textocheck"=>$txtObservacao,"id_usuario"=>$idUsuario, "idUsuario"=>$idUsuario,"notificacao"=>$notificacao,"grupos"=>$gruposAtendimentos, "IdAtendimento"=>$idAtendimento, "latitude"=>$lat, "longitude"=>$long, "hora"=>$hora, "tipoCheck"=>$textoCheck, "atendimentos"=>$atendimentos,"emAndamento"=>$emAndamento,"emEspera"=>$emEspera,"emAtraso"=>$emAtraso,"finalizado"=>$finalizado);
		}
		else{
			$result["login"][] = array("acesso"=>true,"textocheck"=>$txtObservacao,"id_usuario"=>$idUsuario, "idUsuario"=>$idUsuario,"notificacao"=>$notificacao,"grupos"=>$gruposAtendimentos, "IdAtendimento"=>$idAtendimento, "latitude"=>$lat, "longitude"=>$long, "hora"=>$hora, "tipoCheck"=>$textoCheck, "atendimentos"=>$atendimentos,"emAndamento"=>$emAndamento,"emEspera"=>$emEspera,"emAtraso"=>$emAtraso,"finalizado"=>$finalizado);
		}
		
		echo json_encode($result);
		
	}


    private function envia_email($nome, $email_from, $email_to, $assunto, $mensagem) {
		
		/*$headers = "From: ".$nome."  <".$email_from.">\r\n";
		$headers .= "Reply-To: ".$email_from."\r\n";
		$headers .= "Return-Path: <$email_to>\r\n";
		$headers .= "MIME-Version: 1.1\r\n";
		$headers .= "Content-type: text/html; charset=utf-8\r\n"; 
		$headers .= "X-Priority: 3\r\n";
		@mail($email_to, $assunto, $mensagem, $headers);*/
		
		$headers = "From: ".$nome."  <".$email_from.">\n";
		$headers .= "Reply-To: ".$email_from."\n";
		$headers .= "MIME-Version: 1.1\n";
		$headers .= "Content-type: text/html; charset=utf-8\n"; 
		$headers .= "X-Priority: 3\n";
		
		$email_veritime = 'equipe@veritime.com.br';
		
		@mail($email_to, $assunto, $mensagem, $headers ,"-r".$email_veritime);	
		
    }
	
	
	public function meuLocal(){
		$this->load->model('Usuario_model', 'usuario_model');
		
		$lat 		= $this->input->post('latitude');
		$long 		= $this->input->post('longitude');
		$idUsuario 	= $this->input->post('idUsuario');
		
		$upUsuarioLatLong = $this->usuario_model->updateLatLong($idUsuario, $lat, $long);
		
		if($upUsuarioLatLong){
			$result["login"][] = array("acesso"=>true,"mensagem"=>"Local atualizado com sucesso.");
		}
		else{
			$result["login"][] = array("acesso"=>true,"mensagem"=>"Erro ao salvar seu local, por favor tente novamente.");
		}	
		echo json_encode($result);
	}
	
########################################################################################################################################################

    private function redirect_login($msg_controller) {
        // Destruir a sessão.
        $this->session->sess_destroy();

        // Redirecionar para a tela de login e exibir mensagem de erro.
        $dados = array('msg_controller' => $msg_controller);
        $this->load->view('acesso/login', $dados);
    }

    function sair() {
        // Salvar o log da operação. salvar_log() está dentro do "log_helper".
        /*
          if (!salvar_log('acesso', 'sair'))
          $this->redirect_login("Não foi possível salvar o log. Favor entrar em contato com o desenvolvedor do sistema. [acesso/sair]");
          else {
          // Destruir a sessão.
          $this->session->sess_destroy();

          // Redirecionar para a tela de login.
          $this->load->view("acesso/login");
          }
         */

        // Destruir a sessão.
        $this->session->sess_destroy();

        // Redirecionar para a tela de login.
        $this->load->view("acesso/login");
    }

    function esqueci_minha_senha($nova_senha_cadastrada = "") {
        $dados = array();
        if (!empty($nova_senha_cadastrada)) {
            $dados['nova_senha_cadastrada'] = $nova_senha_cadastrada;
        }
        $this->load->view('acesso/esqueci_minha_senha', $dados);
    }

    function nova_senha() {
        // 1º VALIDAR O FORMULÁRIO.
        if ($this->validar_nova_senha()) {
            if (!empty($this->idusuario)) {

                //gera nova senha
                $nova_senha = gera_senha();
                $nova_senha_criptografada = cripto($nova_senha);

                $nova_senha_insert = new stdClass();
                $nova_senha_insert->senha = $nova_senha_criptografada;
                $nova_senha_insert->modificado = date("Y-m-d H:i:s");
                $nova_senha_insert->modificado_por = $this->idusuario;
                $this->db->where('idusuario', $this->idusuario);
                $this->db->update('usuario', $nova_senha_insert);

				$email_veritime = 'equipe@veritime.com.br';
				$assunto = "Nova Senha Veritime";
				$nome_que_dispara="Veritime - Equipes Externas";

				$mensagem = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
				<html>
				<head><title>Veritime</title></head>
				<body>
				<table border="0">
				  <tr>
					<td>
						<p><p><img src="http://www.veritime.com.br/images/veritime-gestao-de-equipe-externa-logo.png" border="0"></p></p>
						<p>Olá,</p> 
						<p>Abaixo encontra-se o seu e-mail e senha de aceesso ao sistema web.</p>
						<p>E-mail: <strong>'.$this->email.'</strong><br> Senha: <strong>'.$nova_senha.'</strong></p>
						
						<p>Acesse o <a href="http://www.veritime.com.br/admin">sistema web aqui</a>, usando o login(e-mail) e senha</p>
						
						<p>equipe@veritime.com.br<br>
						<a href="http://twitter.com/veritime">twitter.com/veritime</a><br>
						<a href="http://facebook.com/veritime">facebook.com/veritime</a></p>
					</td>
				  </tr>
				</table>
				</div>	
				</body>
				</html>		
				';		
				$headers = "From: ".$nome_que_dispara."  <".$email_veritime.">\n";
				$headers .= "Reply-To: ".$email_veritime."\n";
				$headers .= "MIME-Version: 1.1\n";
				$headers .= "Content-type: text/html; charset=utf-8\n"; 
				$headers .= "X-Priority: 3\n";
				
				if(!mail($this->email, $assunto, $mensagem, $headers ,"-r".$email_veritime)){ // Se for Postfix
					mail($this->email, $assunto, $mensagem, $headers);
				}

                $foi = "nova_senha_cadastrada";
                redirect("/acesso/esqueci_minha_senha/$foi");
            } else {
                $this->session->set_flashdata('msg_controller_erro', 'Operação <strong>não</strong> efetuada.');
                redirect("/acesso/esqueci_minha_senha/");
            }
        } else {
            $this->esqueci_minha_senha();
        }
    }

    private function validar_nova_senha() {
        //$this->form_validation->set_rules('login', 'Login', 'required');
        $this->form_validation->set_rules('email', 'E-mail', 'required');
        $this->form_validation->set_rules('valida_login_email', 'valida_login_email', 'callback_valida_check');

        return $this->form_validation->run();
    }

    //função para verificar se o login e o email batem, se baterem, passa na validação
    public function valida_check() {
        $login = $email = $this->input->post('email');
        //$login = $this->input->post('login');
        $usuario = $this->db->query("SELECT idusuario, nome, email FROM " . $this->db->dbprefix . "usuario WHERE email = '$email' and ativo = 'sim' limit 1");
        //verifico se foi encontrado algum registro, se for crio 3 variaveis dentro do objeto principal, com elas que farei a troca para nova senha e enviarei o email
        if ($usuario->num_rows() > 0) {
            $resultado_usuario = $usuario->row();
            $this->idusuario = $resultado_usuario->idusuario;
            $this->nome = $resultado_usuario->nome;
            $this->login = $login;
            $this->email = $email;
            return true;
        } else {
            $this->form_validation->set_message('valida_check', "Não foi encontrado nenhum usuário com este e-mail.");
            return false;
        }
    }

}

/* End of file acesso.php */
/* Location: ./application/controllers/acesso.php */