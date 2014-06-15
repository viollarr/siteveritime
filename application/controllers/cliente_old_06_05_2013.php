<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cliente extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    function index() {
        $this->lista();
    }

    function lista($offset = 0) {
        $dados = array();
		$parms = array();
		
		$this->load->model('cliente_model');

        // Pegar box da paginação.
        $dados['paginacao'] = $this->pagination->paginacao(
                array(
                    'uri' => 'cliente/lista/',
                    'total_rows' => $this->cliente_model->lista($parms)->num_rows()      //$this->cliente_model->contar_todos_registros()//'5'
                )
        );
		
		$parms["per_page"] = $this->pagination->get_per_page();
		$parms["offset"] = $offset;

        // Registros que serão exibidos.
        $dados['clientes'] = $this->cliente_model->lista($parms)->result();//$this->cliente_model->lista($this->pagination->get_per_page(), $offset);

        $dados['view'] = $this->load->view('cliente/lista', $dados, TRUE);
        $this->load->view('dashboard/interna', $dados);
    }

    public function cadastro() {

        $this->load->model("estado_model");

        $dados = array();
        $dados["estados"] = $this->estado_model->get_estados();

        //usado para retornar o idestado sque foi selecionado para popular as cidades após um erro de formulário		
        $idestado_selecionado = $this->input->post('idestado');
        if ($idestado_selecionado) {
            $this->load->model("cidade_model");
            $dados["cidades"] = $this->cidade_model->get_cidade_by_estado($idestado_selecionado);
        }

        $dados['view'] = $this->load->view('cliente/cadastro', $dados, TRUE);
        $dados['menu_selecionado'] = 'menu_clientes';
        $this->load->view('dashboard/interna', $dados);
    }

    public function insert() {

        // 1º VALIDAR O FORMULÁRIO.
        if ($this->validar_form()) {
            $this->load->model("cliente_model");

            // 2º CHAMAR MÉTODO POST() DO MODELO.
            $this->cliente_model->post();

            // 3º CHAMAR MÉTODO INSERT() DO MODELO.
            if ($this->cliente_model->insert() > 0) {
                // Salvar os endereços das filiais.
                $this->cliente_model->salvar_enderecos_filiais();

                // Exibir a lista de clientes.
                $this->session->set_flashdata('msg_controller_sucesso', 'Cliente cadastrado com sucesso.');
                redirect('/cliente/lista/');
            } else {
                $this->session->set_flashdata('msg_controller_erro', 'Cadastro de cliente <strong>não</strong> efetuado.');
                redirect('/dashboard/mensagem/');
            }//else
        } else {
            $this->cadastro();
        }//else;
    }

    public function editar() {
        $idcliente = $this->input->post('idcliente');

        // Carregando o model de cliente.
        $this->load->model("cliente_model");
        $cliente = $this->cliente_model->get_by_id($idcliente);

        if (!empty($cliente)) {

            // Passa os dados para o formulário de edição (view).
            $dados['cliente'] = $cliente;

            $this->load->model("estado_model");
            $dados["estados"] = $this->estado_model->get_estados();
            //usado para retornar o idestado sque foi selecionado para popular as cidades após um erro de formulário		
            $idestado_selecionado = $cliente->idestado;

            if ($idestado_selecionado) {
                $this->load->model("cidade_model");
                $dados["cidades"] = $this->cidade_model->get_cidade_by_estado($idestado_selecionado);
            }

            // Pegando os endereços das filiais.
            $dados['filiais'] = $this->cliente_model->enderecos_filiais($idcliente);

            $dados['menu_selecionado'] = 'menu_clientes';
            $dados['view'] = $this->load->view('cliente/editar', $dados, TRUE);
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

        $idcliente = $this->input->post('idcliente');

        if (!empty($idcliente)) {
            // 1º VALIDAR O FORMULÁRIO.
            if ($this->validar_form()) {
                $this->load->model("cliente_model");

                // 2º CHAMAR MÉTODO POST() DO MODELO.
                $this->cliente_model->post();

                // 3º CHAMAR MÉTODO UPDATE() DO MODELO.
                $atualizado = $this->cliente_model->update($idcliente);

                if ($atualizado) {
                    // Salvar os endereços das filiais.
                    $this->cliente_model->salvar_enderecos_filiais_edicao($idcliente);

                    // Exibir a lista de clientes.
                    $this->session->set_flashdata('msg_controller_sucesso', 'Cliente atualizado com sucesso.');
                    redirect('/cliente/lista/');
                } else {
                    $this->session->set_flashdata('msg_controller_erro', 'Atualização <strong>não</strong> efetuada.');
                    redirect('/dashboard/mensagem/');
                }
            } else {
                // Retorna para o formulário de edição caso não passe na validação do form.
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
        $this->form_validation->set_rules('razao_social');
        $this->form_validation->set_rules('cnpj');
        $this->form_validation->set_rules('endereco', 'Endereço', 'required');
        $this->form_validation->set_rules('endereco_numero');
        $this->form_validation->set_rules('endereco_complemento');
        $this->form_validation->set_rules('bairro', 'Bairro', 'required');
        $this->form_validation->set_rules('idestado', 'Estado', 'required');
        $this->form_validation->set_rules('idcidade', 'Cidade', 'required');
        $this->form_validation->set_rules('telefone');
        $this->form_validation->set_rules('celular');
        $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email');
        $this->form_validation->set_rules('observacao');

        // Validação dos endereços das filiais.
        $this->form_validation->set_rules('endereco_filiais', 'Endereços das Filiais', 'callback_verificar_enderecos_filiais');

        return $this->form_validation->run();
    }

    public function verificar_enderecos_filiais() {
        $this->load->model("cliente_model");

        // Pegar endereços das filiais preenchidos até o momento no formulário.
        $filiais = $this->cliente_model->form_enderecos_filiais();

        // Se não foi preenchido endereço de nenhuma filial.
        if (empty($filiais))
            return true;

        $i = 1;
        $erros = array();
        foreach ($filiais as $filial) {
            if (empty($filial->endereco)) {
                $erros[] = "<li>O <strong>endereço da filial {$i}</strong> deve ser preenchido.</li>";
            }

            if (empty($filial->endereco_numero)) {
                $erros[] = "<li>O <strong>número do endereço da filial {$i}</strong> deve ser preenchido.</li>";
            }

            if (empty($filial->bairro)) {
                $erros[] = "<li>O <strong>bairro da filial {$i}</strong> deve ser preenchido.</li>";
            }

            if (empty($filial->idestado)) {
                $erros[] = "<li>O <strong>estado da filial {$i}</strong> deve ser selecionado.</li>";
            }

            if (empty($filial->idcidade)) {
                $erros[] = "<li>A <strong>cidade da filial {$i}</strong> deve ser selecionada.</li>";
            }

            $i++;
        }

        if (empty($erros)) {
            return true;
        } else {
            // Enviar os erros para serem exibidos na view.
            $this->form_validation->set_message('verificar_enderecos_filiais', "Falta alguma informação no cadastro dos endereços das filiais: <ul>" . implode("", $erros) . "</ul>");

            return false;
        }
    }
z
}

/* End of file cliente.php */
/* Location: ./application/controllers/cliente.php */