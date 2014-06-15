<?php

// Pegando as variáveis passadas pelos controllers.
$msg_controller_sucesso = $this->session->flashdata('msg_controller_sucesso');
$msg_controller_erro = $this->session->flashdata('msg_controller_erro');
$msg_controller_atencao = $this->session->flashdata('msg_controller_atencao');

// Verificando qual box e estilo deve exibir.
if (!empty($msg_controller_sucesso)) {
    echo '<div class="alert alert-success">' . $msg_controller_sucesso . '</div>';
} else if (!empty($msg_controller_erro)) {
    echo '<div class="alert alert-error">' . $msg_controller_erro . '</div>';
} else if (!empty($msg_controller_atencao)) {
    echo '<div class="alert">' . $msg_controller_atencao . '</div>';
}
?>	