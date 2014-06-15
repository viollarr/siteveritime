<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Contato extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    function index() {
        $this->formulario();
    }

    function formulario() {		
	
		if($this->input->server('REQUEST_METHOD') == 'POST') {
			if ($this->validar_form()) {
				
				$nome = $this->input->post('nome');
				$email = $this->input->post('email');						
				$mensagem = $this->input->post('mensagem');

				$email_veritime = 'equipe@veritime.com.br';
				$assunto = "Contato via Sistema Web";
				

				$mensagem = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
				<html>
				<head><title>Veritime</title></head>
				<body>
				<table border="0">
				  <tr>
					<td>
						<p>Informações de Contato:</p>
						<p>Nome: <strong>'.$nome.'</strong>
						<br>E-mail: <strong>'.$email.'</strong>
						<br>Mensagem: <strong>'.$mensagem.'</strong></p>
					</td>
				  </tr>
				</table>
				</div>	
				</body>
				</html>		
				';		
				$headers = "From: ".$nome."  <".$email_veritime.">\n";
				$headers .= "Reply-To: ".$email."\n";
				$headers .= "MIME-Version: 1.1\n";
				$headers .= "Content-type: text/html; charset=utf-8\n"; 
				$headers .= "X-Priority: 3\n";
				
				if (mail($email_veritime, $assunto, $mensagem, $headers))			
					$this->session->set_flashdata('msg_sucesso', 'Mensagem enviada com sucesso.');
				else
					$this->session->set_flashdata('error', 'Ocorreu um erro ao enviar a mensagem. Tente novamente.');
					
				redirect(current_url());
					
			}
	
		}
	
	
        $dados = array();
		$dados['msg_sucesso'] = $this->session->flashdata('msg_sucesso');
		$dados['error'] = $this->session->flashdata('error');
		
		$dados['view'] = $this->load->view('contato/formulario', $dados, TRUE);
        $this->load->view('dashboard/interna', $dados);
    }
	
 	private function validar_form() {
        $this->form_validation->set_rules('nome', 'nome', 'required');
        $this->form_validation->set_rules('email', 'e-mail', 'required|valid_email');
        $this->form_validation->set_rules('mensagem', 'mensagem', 'required');
		
        return $this->form_validation->run();
    }	
   
}

/* End of file atendimento.php */
/* Location: ./application/controllers/faq.php */