<?php

/**
 * Verificar se o usuário está logado.
 * A variável de sessão 'logado' é setada assim que o usuário entra no sistema, ou seja, no controller 'acesso'.
 */

function logado() {
    //echo "<hr />";

    $CI = & get_instance();
    /*
      echo "ci object: <pre>";
      print_r($CI);
      echo "</pre>";
     * 
     */
    //echo "<br />controller atual (class): " . get_class($CI);
    if (strtolower(get_class($CI)) != 'acesso'):
        //echo '<br />antes de mais nada, verificar se está logado';
        //echo '<br />logado (int): ' . (int) $CI->session->userdata('logado');
        if (!$CI->session->userdata('logado')):
            // Destruir a sessão.
            $CI->session->sess_destroy();

            // Redirecionar para a tela de login.
            //header("Location: " . base_url());
            redirect('/acesso/index/');
        endif;
    endif;

    //echo "<hr />";
}

?>
