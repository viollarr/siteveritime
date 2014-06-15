<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class usuario extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    function index() {
        $this->visualizar();
    }

    function visualizar() {
		
        $idusuario = $this->session->userdata('usuario')->idusuario;

        // Carregando o model de usuario.
        $this->load->model("usuario_model");
        $usuario = $this->usuario_model->get_usuario($idusuario);
		
		//pr("Result - usuario", $usuario);
		
		if (!empty($usuario)) {

			$dados = array();
            // Passa os dados para a view para visualização (view).
            $dados['usuario'] = $usuario;		
				
			if ($usuario->plano=='teste'){ $dados['plano']='Teste'; }
			if ($usuario->plano=='assinante'){ $dados['plano']='Assinante'; }
			
			if ($usuario->status_usuario=='sim'){ $dados['status_usuario']='Ativo'; }else{ $dados['status_usuario']='Inativo';}
			
			
			$dados['diferenca_dias'] ='';
			if ($usuario->plano=='teste'){
				$data_hoje = date('d/m/Y');
				$data_emp_criada = $usuario->data_criado_empresa;

				// Cria uma função que retorna o timestamp de uma data no formato DD/MM/AAAA
				function geraTimestamp($data) {
					$partes = explode('/', $data);
					return mktime(0, 0, 0, $partes[1], $partes[0], $partes[2]);
				}
				
				// Usa a função criada e pega o timestamp das duas datas:
				$time_inicial = geraTimestamp($data_emp_criada);
				$time_final = geraTimestamp($data_hoje);
				
				// Calcula a diferença de segundos entre as duas datas:
				$diferenca = $time_final - $time_inicial; // 19522800 segundos
				
				// Calcula a diferença de dias
				$diferenca_dias = (int)floor( $diferenca / (60 * 60 * 24)); // 225 dias
				
				// Exibe uma mensagem de resultado:
				$dados['diferenca_dias'] = $diferenca_dias;
				
			}
			
			
			$dados['view'] = $this->load->view('usuario/visualizar', $dados, TRUE);
			$this->load->view('dashboard/interna', $dados);
		}//if empty usuario
    }
	
	
    function lista($offset = 0, $offset2 = 0) {
        $dados = array();
		$parms = array();
		
		$this->load->model("usuario_model");
		
        // Pegar box da paginação dos usuários.
        $dados['paginacao'] = $this->pagination->paginacao(
                array(
                    'uri' => 'usuario/lista/',
                    'total_rows' => $this->usuario_model->lista($parms)->num_rows()      //$this->cliente_model->contar_todos_registros()//'5'
                )
        );		


		//utilizo esse parametro para pegar todos os usuários
		$parms["exibir_todos"] = "sim"; 
        // Pegar box da paginação de todos os usuários.
        $dados['paginacao2'] = $this->pagination->paginacao(
                array(
                    'uri' => 'usuario/lista/0/',
                    'total_rows' => $this->usuario_model->lista($parms)->num_rows(),      //$this->cliente_model->contar_todos_registros()//'5'
					'uri_segment' => 4
                )
        );
		
		//excluo o parametro para exibir todos na busca dos usuários de um gestor especifico
		unset($parms["exibir_todos"]);
		
		$parms["per_page"] = $this->pagination->get_per_page();
		
		$parms["offset"] = $offset;
        // Registros que serão exibidos.
        $dados['usuarios'] = $this->usuario_model->lista($parms)->result();		
		
		$parms["offset"] = $offset2;
		$parms["exibir_todos"] = "sim"; 
        // Registros que serão exibidos.
        $dados['todos_usuarios'] = $this->configura_com_gerente($this->usuario_model->lista($parms)->result());
        $dados['view'] = $this->load->view('usuario/lista', $dados, TRUE);
        $this->load->view('dashboard/interna', $dados);
    }

    public function cadastro() {
        //pr('session (usuario)', $this->session->userdata('usuario'));
        $this->load->model("usuario_model");
        $dados = array();
        $dados["permissoes"] = $this->usuario_model->get_permissao()->result();

        $dados['menu_selecionado'] = 'menu_usuarios';
        $dados['view'] = $this->load->view('usuario/cadastro', $dados, TRUE);
        $this->load->view('dashboard/interna', $dados);
    }

    /**
     * Valida os dados do formulário e chama o model para salvar no banco de dados os dados do novo registro.
     */
    public function insert() {

        // 1º VALIDAR O FORMULÁRIO.
		$this->form_validation->set_rules('senha', 'Senha', 'required');
		
        if ($this->validar_form()) :
            $this->load->model("usuario_model");

            // 2º CHAMAR MÉTODO POST() DO MODELO.
            $this->usuario_model->post();

            // 3º CHAMAR MÉTODO INSERT() DO MODELO.
            $idusuario = $this->usuario_model->insert();

            if ($idusuario > 0):

                //pegando campos para o envio do email e chamando a função de envio
                $nome = $this->input->post('nome');
                $login = $this->input->post('email');
                $email = $this->input->post('email');
                $assunto = "Cadastro Veritime";
                $mensagem = "";
                $mensagem .= "Bem vindo ao Veritime $nome, cadatro efetuado com sucesso.\r\n";
                $mensagem .= "Seu login e sua senha ao sistema são:\r\n\r\n";
                $mensagem .= "Login: $login\r\n";
                $mensagem .= "Senha: $senhae\r\n";
                //$this->envia_email($nome, $email, $assunto, $mensagem);

                /*
				if (!salvar_log('usuario', 'insert', $idusuario)) {
                    $this->session->set_flashdata('msg_controller_erro', "Não foi possível salvar o log. Favor entrar em contato com o desenvolvedor do sistema. [usuario/insert - id: {$idusuario}]");
                    redirect('/usuario/lista/');
                }
				*/
                $this->session->set_flashdata('msg_controller_sucesso', 'Usuário cadastrado.');
                redirect('/usuario/lista/');
            else:
                $this->session->set_flashdata('msg_controller_erro', 'Cadastro <strong>não</strong> efetuado.');
                redirect('/dashboard/mensagem/');
            endif;
        else:
            $this->cadastro();
        endif;
    }

    public function editar() {
        $idusuario = $this->input->post('idusuario');
		
		$edicao_conta = $this->input->post('edicao_conta',TRUE);

        // Carregando o model de usuario.
        $this->load->model("usuario_model");
        $usuario = $this->usuario_model->get_by_id($idusuario);

        if (!empty($usuario)) {
            // Passa os dados para o formulário de edição (view).
            $dados['usuario'] = $usuario;
			//parametros passados id do usuario, achar a pessoa que esta procurando pelo id passado, buscar os nomes na outra tabela referente a esse campo//
			$dados['relacionamento'] = $this->usuario_model->get_usuarios_by_relacionamento($idusuario, "id_usuario", "id_adm_gerente");
            $dados["permissoes"] = $this->usuario_model->get_permissao()->result();

            if ((!empty($edicao_conta)) and ($edicao_conta == 'sim'))
				$dados['edicao_conta'] = 'sim';
			else
				$dados['edicao_conta'] = '';
	
				
			
            $dados['view'] = $this->load->view('usuario/editar', $dados, TRUE);
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

        $idusuario = $this->input->post('idusuario');		
		$plano = $this->input->post('plano');

        if (!empty($idusuario)) {
            // 1º VALIDAR O FORMULÁRIO.
            if ($this->validar_form()) {
                $this->load->model("usuario_model");
				
                // 2º CHAMAR MÉTODO POST() DO MODELO.
                $this->usuario_model->post();
				

                // 3º CHAMAR MÉTODO UPDATE() DO MODELO.
                $atualizado = $this->usuario_model->update($idusuario);

                if ($atualizado) {
					
					if($this->session->userdata('usuario')->idpermissao == 1){					
						$idempresa = $this->session->userdata('usuario')->idempresa;
						$cadastrar_empresa = $this->db->query("UPDATE " . $this->db->dbprefix . "empresa SET plano = '{$plano}' WHERE idempresa = {$idusuario}");
								
					}
					
                    $this->session->set_flashdata('msg_controller_sucesso', 'Usuário atualizado com sucesso.');
                    redirect('/usuario/lista/');
                } else {
                    $this->session->set_flashdata('msg_controller_erro', 'Atualização <strong>não</strong> efetuada.');
                    redirect('/dashboard/mensagem/');
                }
            } else {
                // Retorna para o formulário de edição caso não passe na validação do form.
                $this->session->set_userdata('idusuario', $idusuario);
                $this->editar();
            }
        } else {
            // Redireciona para a tela principal caso não tenha nenhum ID preenchido.
            redirect('/dashboard/');
        }
    }
	
	public function configura_com_gerente($usuarios = array()){
		$this->load->model("usuario_model");
		foreach($usuarios as $usuario){
			$gerentes = $this->usuario_model->get_retorna_gerentes($usuario->idusuario)->result();
			$usuario->gerentes = $gerentes;
		}
		return $usuarios;
	}
	

    /**
     * O controller deve validar o formulário antes de salvar no banco de dados.
     */
    private function validar_form() {
		$this->form_validation->set_rules('login', 'Login', 'callback_valida_login_check');
        $this->form_validation->set_rules('nome', 'Nome', 'required');
		$this->form_validation->set_rules('celular', 'Celular', 'required');
        $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email|callback_valida_email_check');
		
		$this->form_validation->set_rules('idpermissao', 'Perfil', 'required');
		$this->form_validation->set_rules('senha', 'senha', 'callback_valida_senha_check');
		$this->form_validation->set_rules('confirma_senha', 'confirma_senha');
		$this->form_validation->set_rules('ativo', 'Ativo', 'required');
				
        return $this->form_validation->run();
    }

    private function envia_email($nome, $email, $assunto, $mensagem) {
        $headers = "From: Veritime <werther@imaginatto.com.br>\r\n";
        //$headers .= "BCC: <$ema2>\r\n";
        $headers .= "Reply-To: <$email>\r\n";
        $headers .= "Return-Path: <$email>\r\n";

        //@mail($email, $assunto, $mensagem, $headers) or die("Error: mail could not be sent!");
    }

	
	//função para verificar se login existe
    public function valida_login_check() {
        //recebendo variaveis login e login atual
        $login = $this->input->post('login');
        $login_atual = $this->input->post('login_atual');
        if (empty($login)) {
            $this->form_validation->set_message('valida_login_check', "O campo <strong>Login</strong> é necessário.");
            return false;
        } else {
            if ($login != $login_atual) {
                $usuario = $this->db->query("SELECT count(idusuario) as qntd FROM ".$this->db->dbprefix. "usuario WHERE login = '{$login}' LIMIT 1")->row();
                $qntd = $usuario->qntd;
                //pexit("aa", $qntd);
                if ($qntd >= 1) {
                    $this->form_validation->set_message('valida_login_check', "<strong>Login</strong> já cadastrado no sistema.");
                    return false;
                } else {
                    return true;
                }
            } else {
                return true;
            }
        }
    }
	
	public function valida_senha_check(){
		//recebendo variaveis login e login atual
		$controller = $this->uri->segment(2);
		$nova_senha = $this->input->post('senha');
		$confirma_senha = $this->input->post('confirma_senha');

		if(!empty($nova_senha)){
			if($nova_senha != $confirma_senha){
				$this->form_validation->set_message('valida_senha_check', "Os campos <b>Senha</b> e <b>Confirmação de Senha</b> não são iguais.");
				return false;
			}else{
				return true;
			}
		}else{
			if($controller == "insert"){
				$this->form_validation->set_message('valida_senha_check', "O campo <b>Senha</b> é necessário.");
				return false;
			}else{
				return true;
			}
		}
		
	
	}
	
	/*
    //função para verificar se login existe
    public function valida_login_check() {
        //recebendo variaveis login e login atual
        $login = $this->input->post('email');
        $login_atual = $this->input->post('email_atual');
        if (empty($login)) {
            $this->form_validation->set_message('valida_login_check', "O campo <strong>e-mail</strong> é necessário.");
            return false;
        } else {
            if ($login != $login_atual) {
                $usuario = $this->db->query("SELECT count(idusuario) as qntd FROM ".$this->db->dbprefix. "usuario WHERE login = '{$login}' LIMIT 1")->row();
                $qntd = $usuario->qntd;
                //pexit("aa", $qntd);
                if ($qntd >= 1) {
                    $this->form_validation->set_message('valida_login_check', "<strong>Login(e-mail)</strong> já cadastrado no sistema.");
                    return false;
                } else {
                    return true;
                }
            } else {
                return true;
            }
        }
    }
	*/
	
    //função para verificar se o email existe
    public function valida_email_check() {
        //recebendo variaveis login e login atual
        $email = $this->input->post('email');
        $email_atual = $this->input->post('email_atual');

            if ($email != $email_atual) {
                $usuario = $this->db->query("SELECT count(idusuario) as qntd FROM ".$this->db->dbprefix."usuario WHERE email = '{$email}' LIMIT 1")->row();
                $qntd = $usuario->qntd;
                if ($qntd >= 1) {
                    $this->form_validation->set_message('valida_email_check', "<strong>E-mail</strong> já cadastrado no sistema.");
                    return false;
                } else {
                    return true;
                }
            } else {
                return true;
            }
        
    }
   
}

/* End of file usuario.php */
/* Location: ./application/controllers/usuario.php */