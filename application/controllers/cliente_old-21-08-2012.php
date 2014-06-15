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

        // Pegar box da paginação.
        $dados['paginacao'] = $this->pagination->paginacao(
                array(
                    'uri' => 'cliente/lista/',
                    'total_rows' => $this->db->count_all_results($this->db->dbprefix . "cliente")
                )
        );
        // Registros que serão exibidos.
        $this->db->select('cliente.idcliente, cliente.nome, cliente.endereco,cliente.endereco_numero, cliente.endereco_complemento, cliente.bairro, cliente.email');
        $this->db->from('cliente');
        $this->db->where('idempresa', $this->session->userdata('usuario')->idempresa);
        $this->db->order_by('cliente.nome');
        $this->db->limit($this->pagination->get_per_page(), $offset);
        $dados['clientes'] = $this->db->get()->result();

        $dados['view'] = $this->load->view('cliente/lista', $dados, TRUE);
        $dados['menu_selecionado'] = 'menu_clientes';
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

        /*
         * Trabalhando no cadastro de filiais via cadastro do cliente. [Daniel Costa]
          $this->load->model("cliente_model");
          pr('filiais no controller', $this->cliente_model->form_enderecos_filiais());
          exit('testando o envio das filiais');
         * 
         */

        // 1º VALIDAR O FORMULÁRIO.
        if ($this->validar_form()) :
            $this->load->model("cliente_model");

            // 2º CHAMAR MÉTODO POST() DO MODELO.
            $this->cliente_model->post();

            // 3º CHAMAR MÉTODO INSERT() DO MODELO.
            $idcliente = $this->cliente_model->insert();

            if ($idcliente > 0):
                $this->session->set_flashdata('msg_controller_sucesso', 'Cliente cadastrado com sucesso.');
                redirect('/cliente/lista/');
            else:
                $this->session->set_flashdata('msg_controller_erro', 'Cadastro de cliente <strong>não</strong> efetuado.');
                redirect('/dashboard/mensagem/');
            endif;
        else:
            $this->cadastro();
        endif;
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

        $this->form_validation->set_rules('endereco_filiais', 'Endereços das Filiais', 'callback_verificar_enderecos_filiais');

        return $this->form_validation->run();
    }

    public function verificar_enderecos_filiais() {
        $this->load->model("cliente_model");

        $filiais = $this->cliente_model->form_enderecos_filiais();
        //pr('filiais no controller', $filiais);
        // Se não foi preenchido endereço de nenhuma filial.
        if (empty($filiais))
            return true;

        $i = 1;
        $erros = array();
        foreach ($filiais as $filial) {
            if (empty($filial->endereco)) {
                $erros[] = "<li>O endereço da filial {$i} deve ser preenchido.</li>";
            }

            if (empty($filial->endereco_numero)) {
                $erros[] = "<li>O número do endereço da filial {$i} deve ser preenchido.</li>";
            }

            if (empty($filial->bairro)) {
                $erros[] = "<li>O bairro da filial {$i} deve ser preenchido.</li>";
            }

            if (empty($filial->idestado)) {
                $erros[] = "<li>O estado da filial {$i} deve ser selecionado.</li>";
            }

            if (empty($filial->idcidade)) {
                $erros[] = "<li>A cidade da filial {$i} deve ser selecionada.</li>";
            }

            $i++;
        }

        if (empty($erros)) {
            return true;
        } else {
            // Enviar os erros para serem exibidos na view.
            $this->form_validation->set_message('verificar_enderecos_filiais', "Falta alguma informação no cadastro dos endereços das filiais: <ul>" . implode("", $erros) . "</ul>");

            $this->session->set_userdata('enderecos_filiais', $filiais);
            //$filiais = $this->session->userdata('enderecos_filiais');
            //pr('filiais retornadas depois da validação [controller]', $filiais);

            return false;
        }
    }

}

/* End of file cliente.php */
/* Location: ./application/controllers/cliente.php */