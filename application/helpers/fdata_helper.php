<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

if (!function_exists('fdata')) {

    //função de formatação de data (Ex.: 31/04/2012 => 2012-04-31)
    function fdata($data, $tipo_transforma = "") {
        //tipos de criptografia
        /*
          "-" - transforma de / para - e inverte
          "/" - transforma de - para / e inverte
         */
		if(!empty($data)){
			if ($tipo_transforma == "-") {
				$parte = explode("/", $data);
				$data = $parte[2] . "-" . $parte[1] . "-" . $parte[0];
			} else if ($tipo_transforma == "/") {
				$parte = explode("-", $data);
				$data = $parte[2] . "/" . $parte[1] . "/" . $parte[0];
			}
		}

        return $data;
    }

}

/* End of file log_helper.php */
/* Location: ./application/helpers/cripto_helper.php */
?>