<?php

/**
 * Verifica quais funcionalidades o usuário pode ter acesso. Por exemplo: o usuário comum não pode ter acesso ao cadastro/edição de usuários.
 */
function acesso_funcionalidade() {

    /*
     * Nível de permissão do usuário no sistema. Ver tabela vt_permissao no banco de dados.
     * 
     * 1, Administrador, O administrador geral da conta pode editar todas as informações da sua conta no Veritime.
     * 2, Gerente, Gerencia funcionários e visualiza informações de todos os funcionários.
     * 3, Funcionário, Visualiza somente os seus dados e as informações de seus atendimentos.
     * 
     */

    // A chave do array de primeiro nível nessa estrutura abaixo é o idpermissao.
    // No array de segundo nível, as chaves são os nomes dos controllers e o valor são os métodos deste controller que a permissão pode enxergar.
    $permissoes = array(
        1 => array(
            "dashboard" => array("index", "home", "interna", "mensagem"),
            "ajax_controller" => array("altera_status"),
            "cliente" => array("index", "lista", "cadastro", "editar", "cadastro", "insert", "editar", "update"),
            "usuario" => array("index", "lista", "cadastro", "editar", "cadastro", "insert", "editar", "update", "get_usuarios_by_atendimento_exclusao", "get_usuarios_by_atendimento_inclusao", "get_by_valida"),
            "atendimento" => array("index", "lista", "cadastro", "editar", "cadastro", "insert", "editar", "update", "excluir", "get_by_id_app", "realizarCheck", "insertComentApp", "consultAppGrupos", "getEnderecoPrincipal", "getEnderecoFilial", "getIdNomeUsuario", "vericarExclusaoFuncionarioAtendimento", "vericarInclusaoFuncionarioAtendimento", "getIdNomeCliente", z"getValidacaoUser", "mapa_local", "busca","reagendar", "update_reagendar", "enviar_notificacao"),
			"cidade" => array("getCidades"),
			"relatorio" => array("atendimentos", "download", "gerar"),
			"mapa" => array("visualizar"),
			"autocomplete" => array("usuariosAutocomplete", "clientesAutocomplete"),
			"app" => array("atendimento"),
			"faq" => array("index","lista")
        ),
        2 => array(
            "dashboard" => array("index", "home", "interna", "mensagem"),
            "ajax_controller" => array("altera_status"),
			"cliente" => array("index", "lista", "cadastro", "editar", "cadastro", "insert", "editar", "update"),
            "usuario" => array("index", "lista", "cadastro", "editar", "cadastro", "insert", "editar", "update", "get_usuarios_by_atendimento_exclusao", "get_usuarios_by_atendimento_inclusao", "get_by_valida"),
            "atendimento" => array("index", "lista", "cadastro", "editar", "cadastro", "insert", "editar", "update", "excluir", "get_by_id_app", "realizarCheck", "insertComentApp", "consultAppGrupos", "getEnderecoPrincipal", "getEnderecoFilial", "getIdNomeUsuario", "vericarExclusaoFuncionarioAtendimento", "vericarInclusaoFuncionarioAtendimento", "getIdNomeCliente", "getValidacaoUser", "mapa_local", "busca","reagendar", "update_reagendar", "enviar_notificacao"),
			"cidade" => array("getCidades"),
			"relatorio" => array("atendimentos", "download", "gerar"),
			"mapa" => array("visualizar"),
			"autocomplete" => array("usuariosAutocomplete", "clientesAutocomplete"),
			"app" => array("atendimento"),
			"faq" => array("index","lista")
        ),
        3 => array(
            "dashboard" => array("index", "home", "interna", "mensagem"),
			"usuario" => array("index"),
            "atendimento" => array("index", "lista", "get_by_id_app", "realizarCheck", "getEnderecoPrincipal", "getEnderecoFilial", "getIdNomeUsuario", "insertComentApp", "consultAppGrupos", "vericarExclusaoFuncionarioAtendimento", "vericarInclusaoFuncionarioAtendimento", "getIdNomeCliente", "getValidacaoUser", "mapa_local", "busca"),
			"cidade" => array("getCidades"),
			"autocomplete" => array("usuariosAutocomplete", "clientesAutocomplete"),
			"app" => array("atendimento"),
			"faq" => array("index","lista")
        )
    );

    // Pegar objeto do CodeIgniter para poder acessar os métodos, atributos e etc. dele.
    $CI = & get_instance();
    //pexit('ci object', $CI);

    $controller = $CI->router->class;
    $method = $CI->router->method;
    //echo "controller: " . $controller . "<br />";
    //echo "method: " . $method . "<br /><br />";

    $usuario_logado = $CI->session->userdata('usuario');

    $idpermissao_usuario = (!empty($usuario_logado)) ? $usuario_logado->idpermissao : "";
    //$idpermissao_usuario = "";
    // O controller acesso não precisa ser verificado.
    if ($controller != "acesso") {
        // Se o usuário logado não tiver com o idpermissao setado, deve redirecioná-lo para a tela de login, 
        // mas antes deve-se apagar a sessão, por isso foi chamado o método acesso->sair().
        if (empty($idpermissao_usuario)) {
            redirect('/acesso/sair/');
        } else {
            // Verificar se o idpermissao existe como chave no array de permissões.
            if (!array_key_exists($idpermissao_usuario, $permissoes)) {
                redirect('/acesso/sair/');
            } else {
                // Verificar se o usuário logado tem acesso ao controller/método.
                $metodos_permitidos = $permissoes[$idpermissao_usuario];
                //pexit('controllers permitidos', $controllers_permitidos);
                // Verificar se o usuário logado tem acesso ao método chamado do controller.
                if (!array_key_exists($controller, $metodos_permitidos) || !in_array($method, $metodos_permitidos[$controller])) {
                    // Setar a mensagem de erro e chamar o método dashboard->mensagem() para exibí-la.
                    $CI->session->set_flashdata("msg_controller_atencao", "Você não tem permissão para acessar esta funcionalidade ({$controller}/{$method}). Entre em contato com o administrador da sua conta no Veritime.");
                    redirect('/dashboard/mensagem/');
                } else {
                    if ($controller == "atendimento" && ($method == "editar" || $method == "update")) {
                        $CI->load->model("atendimento_model");
                        $tem_acesso = $CI->atendimento_model->verificar_acesso_atendimento();
                        if (!$tem_acesso) {
                            // Setar a mensagem de erro e chamar o método dashboard->mensagem() para exibí-la.
                            $CI->session->set_flashdata("msg_controller_atencao", "Você não tem permissão para editar os dados deste atendimento. Entre em contato com o administrador da sua conta no Veritime.");
                            redirect('/dashboard/mensagem/');
                        }//if
                    }//if
                }//else
            }//else
        }//else
    }//if
}

?>
