<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

if (!function_exists('gera_senha')) {

    //função para gerar senha randomica
    function gera_senha() {
        $CaracteresAceitos = 'abcdxywzABCDZYWZ0123456789';
        $max = strlen($CaracteresAceitos) - 1;
        $password = "";
        for ($i = 0; $i < 8; $i++) {
            $password .= $CaracteresAceitos{mt_rand(0, $max)};
        }
        return $password;
    }

}

/* End of file log_helper.php */
/* Location: ./application/helpers/log_helper.php */