<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class relatorio extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }
	
    function index() {
        $this->visualizar();
    }
	
    function atendimentos() {
        $dados = array();
        $dados['menu_selecionado'] = 'menu_relatorios';
        $dados['view'] = $this->load->view('relatorio/atendimentos', $dados, TRUE);
        $this->load->view('dashboard/interna', $dados);
    }
	
    function download() {
		
		$this->load->helper('download');
		
		if($this->input->post('tipo_download')){
			
			$nome_arquivo = "relatorio_veritime.".$this->input->post('tipo_download');
			$relatorio =  utf8_decode($this->input->post('conteudo'));
			force_download($nome_arquivo,$relatorio); // forçando o download do arquivo com função nativa do codegniter
			
		}
		exit;		
    }

	function gerar($filtros = FALSE){
		
		$idempresa = $this->session->userdata('usuario')->idempresa;
		
		if($filtros){			
			$dados = array();
			$adicionais = array();
			$campos_adicionais = "";
			$condicao = "(";
			$and = "";
				
			if($this->input->post('data')){
				$dados['data_agendada'] = TRUE;
				$campos_adicionais .= ", ".$this->input->post('data');
				$adicionais[]="data";
			}
			if($this->input->post('hora')){
				$dados['hora_agendada'] = TRUE;
				$campos_adicionais .= ", ".$this->input->post('hora');
				$adicionais[]="hora";
			}
			if($this->input->post('tempo')){
				$dados['tempo_estimado'] = TRUE;
				$campos_adicionais .= ", ".$this->input->post('tempo');
				$adicionais[]="tempo";
			}
			if($this->input->post('prioridade')){
				$dados['prioridade'] = TRUE;
				$campos_adicionais .= ", ".$this->input->post('prioridade');
				$adicionais[]="prioridade";
			}
			
			foreach($this->input->post('filtro') AS $valor){
				$filtro[] = $valor;
			}
			
			$contadorFinalizado = 0;
			$contadorNaoConcluido = 0;
			$contadorEmAndamento = 0;
			$contadorEmEspera = 0;
			$contadorEmAtraso = 0;
			
			$contandoFiltros = 0;
			$j = 0;
			$busca = "";
			$tabela = "";
			$condicao_funcionario = "";	
			$between = "";
			$status_geral = "";
			$nomes = "";
			$arr = array();

			
			$nunFiltros = count($filtro);
						
			foreach($filtro AS $key => $filtro_select){   // "k" no inico da variavel é de "key" e "v" é de "valor" 
				$contandoFiltros++;

				if($filtro_select == "status"){
					$i=0;
					$status = "";
					$status2 = "";
					$status3 = "";
					$status4 = "";
					$status5 = "";
					
					if($contadorFinalizado < 1){
						if($this->input->post('finalizado')){
							$contadorFinalizado++;
							foreach($this->input->post('finalizado') AS $kfinalizado => $vfinalizado){
									$finalizado = $vfinalizado;
							}
						}
					}
					if($contadorNaoConcluido < 1){
						if($this->input->post('nao_concluido')){
							$contadorNaoConcluido++;
							foreach($this->input->post('nao_concluido') AS $knao_concluido => $vnao_concluido){
									$nao_concluido = $vnao_concluido;
							}
						}
					}
					if($contadorEmAndamento < 1){
						if($this->input->post('em_andamento')){
							$contadorEmAndamento++;
							foreach($this->input->post('em_andamento') AS $kem_andamento => $vem_andamento){
									$em_andamento = $vem_andamento;
							}
						}
					}
					if($contadorEmEspera < 1){
						if($this->input->post('em_espera')){
							$contadorEmEspera++;
							foreach($this->input->post('em_espera') AS $kem_espera => $vem_espera){
									$em_espera = $vem_espera;
							}
						}
					}
					if($contadorEmAtraso < 1){
						if($this->input->post('em_atraso')){
							$contadorEmAtraso++;
							foreach($this->input->post('em_atraso') AS $kem_atraso => $vem_atraso){
									$em_atraso = $vem_atraso;
							}
						}
					}										
					
					if(!empty($finalizado)){
						$i++;
						$status = "aten.status = '".$finalizado."'";
					}
					if(!empty($nao_concluido)){
						$i++;
						$status2 = "aten.status = '".$nao_concluido."'";
					}
					if(!empty($em_andamento)){
						$i++;
						$status3 = "aten.status = '".$em_andamento."'";
					}
					if(!empty($em_espera)){
						$i++;
						$status4 = "aten.status = '".$em_espera."'";
					}
					if(!empty($em_atraso)){
						$i++;
						$status5 = "aten.status = '".$em_atraso."'";
					}									
					
					//if($i == 1){
					if($status != ""){ $status_geral .= "(".$status.") or ";}
					if($status2 != ""){	$status_geral .= "(".$status2.") or ";}
					if($status3 != ""){	$status_geral .= "(".$status3.") or ";}
					if($status4 != ""){	$status_geral .= "(".$status4.") or ";}
					if($status5 != ""){	$status_geral .= "(".$status5.") or ";}
						
					
					
					
					//$status_geral .= ')';
					
					//$status_geral .= "".$status." ".$status2." ".$status3." ".$status4." ".$status5."";	
					//}
					//elseif($i > 2){
						//$status_geral .= "(".$status." OR ".$status2." OR ".$status3." OR ".$status4." OR ".$status5.")";
					//}
					/*elseif($i > 2){
						if(($status != "")&&($status2 != "")){
							$status_geral .= "(".$status." OR ".$status2.")";
						}
						if(($status != "")&&($status3 != "")){
							$status_geral .= "(".$status." OR ".$status3.")";
						}
						if(($status2 != "")&&($status3 != "")){
							$status_geral .= "(".$status2." OR ".$status3.")";
						}
					}
					elseif($i == 3){
						$status_geral .= "(".$status." OR ".$status2." OR ".$status3." OR ".$status4." OR ".$status5.")";
					}	
					elseif($i == 4){
						$status_geral .= "(".$status." OR ".$status2." OR ".$status3." OR ".$status4." OR ".$status5.")";
					}	
					elseif($i == 5){
						$status_geral .= "(".$status." OR ".$status2." OR ".$status3." OR ".$status4." OR ".$status5.")";
					}				
					*/
					if($j == 0){
						$arr[] = array(
							"filtro" => $filtro_select, 
							"finalizado"=>(!empty($finalizado))? $finalizado:"", 
							"nao_concluido"=>(!empty($nao_concluido))? $nao_concluido:"", 
							"em_andamento"=>(!empty($em_andamento))? $em_andamento:"",
							"em_espera"=>(!empty($em_espera))? $em_espera:"",
							"em_atraso"=>(!empty($em_atraso))? $em_atraso:"",
							);
						$j++;
					}
					
					unset($finalizado, $nao_concluido, $em_andamento, $em_espera, $em_atraso);
									
				}
				
				elseif($filtro_select == "data_agendada"){
					if(!empty($between)){
						$between .= " OR ";
					}
					if($between == ""){
						$between .= "(";	
					}
					
					if($this->input->post('data_inicio')){
						foreach($this->input->post('data_inicio') AS $kdini => $vdini){
							if($key == $kdini){
								$data_inicial = fdata($vdini,"-");
							}
						}
					}
					if($this->input->post('data_fim')){
						foreach($this->input->post('data_fim') AS $kdfim => $vdfim){
							if($key == $kdfim){
								$data_final = fdata($vdfim,"-");
							}
						}
					}
										
					if((!empty($data_inicial)) && (!empty($data_final))){
						$between .= "(data_agendada BETWEEN '".$data_inicial."' AND '".$data_final."')";
						$arr[] = array("filtro" => $filtro_select, "data_inicio" => fdata($data_inicial,"/"), "data_final" => fdata($data_final,"/"));
				}
					
				}
				
				elseif($filtro_select == "nome"){
					$nome = "";
					if(!empty($nomes)){
						$nomes .= " OR ";
					}
					if($nomes == ""){
						$nomes .= "(";	
					}
					
					if($this->input->post('q')){
						foreach($this->input->post('q') AS $knome => $vnome){
							if($key == $knome){
								$busca = ", usu.nome AS usuario";
								$tabela = ", ".$this->db->dbprefix."usuario usu, ".$this->db->dbprefix."usuario_atendimento usera";
								$condicao_funcionario = "usera.idatendimento = aten.idatendimento AND usera.idusuario = usu.idusuario AND ";
								$nome = $vnome;
							}
						}
					}
										
					if(!empty($nome)){
						$nomes .= "(usu.nome LIKE '%".$nome."%')";
						$arr[] = array("filtro" => $filtro_select, "nome" => $nome);
					}
				}
			}
			
			$status_geral = substr($status_geral, 0, -3); // Adicionado para Retirar o OR sobreçalente que ficava no final da consulta.
			
			
			if($between != "" ){
				$condicao .= $between.")";	
			}
			if($status_geral != ""){
				if($between != ""){
					$condicao .= " AND ";
				}
				$condicao .= $status_geral;
			}
			if($nomes != ""){
				if(($between != "")||($status_geral != "")){
					$condicao .= " AND ";	
				}
				$condicao .= $nomes.")";
			}
			
			if($condicao != "("){
				$and = ") AND ";	
			}
						
			$dados['filtros'] = array("Data","Status","Funcionário");
			$dados["adicionais"] = $adicionais;
			$atendimentos = $this->db->query("
				SELECT 
					cli.nome, 
					titulo, 
					aten.idatendimento,
					aten.endereco,
					aten.endereco_numero,
					aten.endereco_complemento,
					aten.bairro,
					estado.sigla AS estado,
					cidade.nome AS cidade
					{$campos_adicionais}, 
					aten.status
					{$busca}
				FROM 
					".$this->db->dbprefix."atendimento aten, 
					".$this->db->dbprefix."cliente cli,
					".$this->db->dbprefix."estado estado,
					".$this->db->dbprefix."cidade cidade
					".$tabela."
				WHERE 
					".$condicao_funcionario."
					".$condicao."".$and."
					aten.idcliente = cli.idcliente AND
					aten.idestado = estado.idestado AND
					aten.idcidade = cidade.idcidade AND
					aten.idempresa = ".$idempresa."
				")->result();
			
/*			echo "<pre>";
			var_dump("
				SELECT 
					cli.nome, 
					titulo, 
					aten.endereco
					{$campos_adicionais}, 
					aten.status 
				FROM 
					".$this->db->dbprefix."atendimento aten, 
					".$this->db->dbprefix."cliente cli
					".$tabela."
				WHERE 
					".$condicao_funcionario."
					".$condicao."".$and."
					aten.idcliente = cli.idcliente  
				");
			echo "</pre>";
*/			
			$dados['atendimentos'] = $atendimentos;	
			$dados['menu_selecionado'] = 'menu_relatorios';
			$dados['preenchimento'] = $arr;
			$dados['view'] = $this->load->view('relatorio/gerar', $dados, TRUE);
			$this->load->view('dashboard/interna', $dados);
	
		}
		else{
			$dados = array();
			$adicionais = array();
			$campos_adicionais = "";
				
			if($this->input->post('data')){
				$dados['data_agendada'] = TRUE;
				$campos_adicionais .= ", ".$this->input->post('data');
				$adicionais[]="data";
			}
			if($this->input->post('hora')){
				$dados['hora_agendada'] = TRUE;
				$campos_adicionais .= ", ".$this->input->post('hora');
				$adicionais[]="hora";
			}
			if($this->input->post('tempo')){
				$dados['tempo_estimado'] = TRUE;
				$campos_adicionais .= ", ".$this->input->post('tempo');
				$adicionais[]="tempo";
			}
			if($this->input->post('prioridade')){
				$dados['prioridade'] = TRUE;
				$campos_adicionais .= ", ".$this->input->post('prioridade');
				$adicionais[]="prioridade";
			}
			
			$dados['filtros'] = array("Data","Status","Funcionário");
			$dados["adicionais"] = $adicionais;
			$atendimentos = $this->db->query("
				SELECT 
					cli.nome, 
					titulo, 
					aten.idatendimento,
					aten.endereco,
					aten.endereco_numero,
					aten.endereco_complemento,
					aten.bairro,
					estado.sigla AS estado,
					cidade.nome AS cidade
					{$campos_adicionais}, 
					aten.status 
				FROM 
					".$this->db->dbprefix."atendimento aten, 
					".$this->db->dbprefix."cliente cli,
					".$this->db->dbprefix."estado estado,
					".$this->db->dbprefix."cidade cidade
				WHERE 
					aten.idcliente = cli.idcliente AND
					aten.idestado = estado.idestado AND
					aten.idcidade = cidade.idcidade AND
					aten.idempresa = ".$idempresa."
				")->result();
				
			$dados['atendimentos'] = $atendimentos;	
			$dados['menu_selecionado'] = 'menu_relatorios';
			$dados['view'] = $this->load->view('relatorio/gerar', $dados, TRUE);
			$this->load->view('dashboard/interna', $dados);
		}
	}
}

/* End of file usuario.php */
/* Location: ./application/controllers/usuario.php */