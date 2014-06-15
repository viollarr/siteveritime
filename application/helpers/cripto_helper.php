<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

if (!function_exists('cripto')) {

//função de criptografia, recebe os parametronos, nome e tipo de criptografia
    function cripto($nome, $tipo_cripto = "md5") {

        // encryption_key
        $CI = & get_instance();
        //pexit('CI object', $CI->config->config['encryption_key']);
        
        $salt = $CI->config->config['encryption_key'];

        switch ($tipo_cripto) {
            case "md5":
                $nome = md5($salt . $nome);
                break;

            case "sha1":
                $nome = sha1($salt . $nome);
                break;

            case "base64_encode":
                $nome = base64_encode($salt . $nome);
                break;

            case "base64_decode":
                $nome = base64_decode($salt . $nome);
                break;

            default :
                $nome = md5($salt . $nome);
                break;
        }//switch

        return $nome;
    }

}

/* End of file log_helper.php */
/* Location: ./application/helpers/cripto_helper.php */
?>