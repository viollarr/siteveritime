<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Ajax_controller extends CI_Controller {

	//function altera_status($idCampo, $id, $nomeCampo, $valorCampo, $nivel) {
		
		
		function altera_status($id = "") {

			$id = $this->input->post('id');
			
			if (!empty($id)) {
				$modificado = date("Y-m-d H:i:s");
				$modificado_por = $this->session->userdata('usuario')->idusuario;
				
				$tabela = $this->input->post('tabela');
				$nomeCampo = $this->input->post('nomeCampo');
				$valorCampo = $this->input->post('valorCampo');
				$idCampo = $this->input->post('idCampo');
				
				$data = array(
							   'modificado' => $modificado,
							   'modificado_por' => $modificado_por,
							   $nomeCampo => $valorCampo
							);
				
				$this->db->where($idCampo, $id);
				$query = $this->db->update($tabela, $data);
				print $query; 
				//exit();
				//return ($this->db->update($tabela, $data)) ? TRUE : FALSE;   
								
			}
			
	}
}

/* End of file ajax */
/* Location: ./application/controllers/ajax.php */
