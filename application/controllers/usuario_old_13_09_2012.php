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
			$perfil = $this->db->query("SELECT nome FROM ".$this->db->dbprefix. "permissao WHERE idpermissao = {$usuario->idpermissao}")->row();
			$dados['perfil'] = $perfil->nome; 
			
			if ($usuario->ativo=='sim'){ $dados['status']='Ativo'; }else{ $dados['status']='Inativo';}
			
			$dados['view'] = $this->load->view('usuario/visualizar', $dados, TRUE);
			$this->load->view('dashboard/interna', $dados);
		}//if empty usuario
    }
	
	
    function lista($offset = 0) {
        $dados = array();

        /*if (!salvar_log('usuario', 'lista')) {
            $this->session->set_flashdata('msg_controller_erro', "Não foi possível salvar o log. Favor entrar em contato com o desenvolvedor do sistema. [usuario/lista]");
            redirect('/dashboard/mensagem/');
        }*/

        // Pegar box da paginação.
        $dados['paginacao'] = $this->pagination->paginacao(
                array(
                    'uri' => 'usuario/lista/',
                    'total_rows' => $this->db->count_all_results($this->db->dbprefix. "usuario")
                )
        );

        // Registros que serão exibidos.
        $this->db->select('idusuario, usuario.nome, email, celular, usuario.idpermissao, ativo, permissao.nome as perfil');
		$this->db->from('usuario');
		$this->db->join('permissao', 'permissao.idpermissao = usuario.idpermissao');
		$this->db->where('idempresa', $this->session->userdata('usuario')->idempresa);
        $this->db->order_by('nome');
        $this->db->limit($this->pagination->get_per_page(), $offset);
        $dados['usuarios'] = $this->db->get()->result();
		
        $dados['view'] = $this->load->view('usuario/lista', $dados, TRUE);
        $dados['menu_selecionado'] = 'menu_usuarios';
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

        // Carregando o model de usuario.
        $this->load->model("usuario_model");
        $usuario = $this->usuario_model->get_by_id($idusuario);

        if (!empty($usuario)) {
            // Passa os dados para o formulário de edição (view).
            $dados['usuario'] = $usuario;

            $dados["permissoes"] = $this->usuario_model->get_permissao()->result();

            $dados['menu_selecionado'] = 'menu_usuarios';
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

        if (!empty($idusuario)) {
            // 1º VALIDAR O FORMULÁRIO.
            if ($this->validar_form()) {
                $this->load->model("usuario_model");
				
                // 2º CHAMAR MÉTODO POST() DO MODELO.
                $this->usuario_model->post();
				

                // 3º CHAMAR MÉTODO UPDATE() DO MODELO.
                $atualizado = $this->usuario_model->update($idusuario);

                if ($atualizado) {
                 
                    /*if (!salvar_log('usuario', 'update', $idusuario)) {
                        $this->session->set_flashdata('msg_controller_erro', "Não foi possível salvar o log. Favor entrar em contato com o desenvolvedor do sistema. [usuario/update - id: {$idusuario}]");
                        redirect('/usuario/lista/');
                    }*/
					
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


    /**
     * O controller deve validar o formulário antes de salvar no banco de dados.
     */
    private function validar_form() {
        $this->form_validation->set_rules('nome', 'Nome', 'required');
		$this->form_validation->set_rules('celular', 'Celular', 'required');
        $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email|callback_valida_email_check');
		
		$this->form_validation->set_rules('idpermissao', 'Perfil', 'required');
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