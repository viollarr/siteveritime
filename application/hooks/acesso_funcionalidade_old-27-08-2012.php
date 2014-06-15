<?php

/**
 * Verifica quais funcionalidades o usuário pode ter acesso. Por exemplo: o usuário comum não pode ter acesso ao cadastro/edição de usuários.
 */
function acesso_funcionalidade() {
    // Pegar objeto do CodeIgniter para poder acessar os métodos, atributos e etc. dele.
    $CI = & get_instance();

    $controller_acessado = strtolower(get_class($CI));
    // if ($controller_acessado == 'escritorio') {
    // Controllers que possuem funções (métodos) de administrador.
    // 'controller' => ('metodo1', 'metodo2', 'metodoN')
    $controllers_admin = array(
        'usuario' => array('cadastro', 'insert', 'editar', 'update')
    );

    // Verificar se o controller acessado possui funções (métodos) de administrador.
    if (array_key_exists($controller_acessado, $controllers_admin)) {
        // Pegar o método chamado.
        $metodo = $CI->uri->segment(2);
        // Caso esteja usando algum "apelido" ($route['areas'] = 'area/lista'; - application/config/routes.php) pega o primeiro parâmetro.
        if(empty($metodo)) $metodo = $CI->uri->segment(1);

        // Pegar o tipo de usuário.
        // A sessão 'usuario' foi setada no controller 'acesso'.
        $tipo_usuario = $CI->session->userdata('usuario')->idpermissao;
		
        // Verificar se o método acessado é restrito a administradores.
        if (in_array($metodo, $controllers_admin[$controller_acessado]) && $tipo_usuario != '1') {
            //echo '<br />Não possui privilégios de administrador!<br /><br />';
            // Exibe uma mensagem para o usuário indicando que ele não tem acesso as esta função (método).
            $CI->session->set_flashdata('msg_controller_erro', '<strong>O perfil deste usuário não possui permissão para acessar esta funcionalidade.</strong>');
            redirect('/dashboard/mensagem/');
        }
    }
    // }
    //echo "<br /><br /><hr />";
}

?>
