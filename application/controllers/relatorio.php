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
	
	public function relatorios($filtros = "false", $aba = "atendimento"){
		$dados = array();
		$adicionais = array();
		$campos_adicionais = "";

		$dados['data_agendada'] = TRUE;
		$campos_adicionais .= ", data_agendada";
		$adicionais[]="data";

		$dados['hora_agendada'] = TRUE;
		$campos_adicionais .= ", hora_agendada";
		$adicionais[]="hora";

		$dados['tempo_estimado'] = TRUE;
		$campos_adicionais .= ", tempo_estimado";
		$adicionais[]="tempo";
		
		$dados['filtros'] = array("Data","Status","Funcionário");
		$dados["adicionais"] = $adicionais;
		$dados["aba"] = $aba;
		
	
		$retorno_atendimento = $this->calcula_duracao_atendimentos($this->relatorio_atendimento(array("filtros" => $filtros, "aba" => $aba, "campos_adicionais" => $campos_adicionais)));
		
		$dados['atendimentos'] = $retorno_atendimento->atendimentos;
		if(!empty($retorno_atendimento->preenchimento)){
			$dados['preenchimento'] = $retorno_atendimento->preenchimento;	
		}		
		//pr("query", $this->db->last_query());
		//pr("atendimentos", $dados['atendimentos']);
		
		
		$retorno_funcionarios = $this->relatorio_funcionario(array("filtros" => $filtros, "aba" => $aba));
		
		$dados['atendimentos_funcionario'] = $retorno_funcionarios->atendimentos_funcionario;
		if(!empty($retorno_funcionarios->preenchimento2)){
			$dados['preenchimento2'] = $retorno_funcionarios->preenchimento2;	
		}
		//r("query", $this->db->last_query());
		//pr("atendimentos_funcionario", $dados['atendimentos_funcionario']);
		
		$dados['menu_selecionado'] = 'menu_relatorios';
		$dados['view'] = $this->load->view('relatorio/relatorios', $dados, TRUE);
		$this->load->view('dashboard/interna', $dados);
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
	
    function download2() {
		
		$this->load->helper('download');
		
		if($this->input->post('tipo_download2')){
			
			$nome_arquivo = "relatorio_veritime_funcionarios.".$this->input->post('tipo_download2');
			$relatorio =  utf8_decode($this->input->post('conteudo2'));
			force_download($nome_arquivo,$relatorio); // forçando o download do arquivo com função nativa do codegniter
			
		}
		exit;		
    }

	function gerar($filtros = FALSE){
	
		$dados = array();
		$adicionais = array();
		$campos_adicionais = "";
		
		/*	
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
		*/

		$dados['data_agendada'] = TRUE;
		$campos_adicionais .= ", data_agendada";
		$adicionais[]="data";

		$dados['hora_agendada'] = TRUE;
		$campos_adicionais .= ", hora_agendada";
		$adicionais[]="hora";

		$dados['tempo_estimado'] = TRUE;
		$campos_adicionais .= ", tempo_estimado";
		$adicionais[]="tempo";
		
		$dados['filtros'] = array("Data","Status","Funcionário");
		$dados["adicionais"] = $adicionais;
		
		$retorno_atendimento = $this->relatorio_atendimento_old(array("filtros" => $filtros, "campos_adicionais" => $campos_adicionais));
		
		$dados['atendimentos'] = $retorno_atendimento->atendimentos;
		if(!empty($retorno_atendimento->preenchimento)){
			$dados['preenchimento'] = $retorno_atendimento->preenchimento;	
		}
		$dados['menu_selecionado'] = 'menu_relatorios';
		$dados['view'] = $this->load->view('relatorio/gerar', $dados, TRUE);
		$this->load->view('dashboard/interna', $dados);
	}
	
	public function relatorio_atendimento($params){
		if(!empty($params)){
			extract($params, EXTR_OVERWRITE);
		}	
		
		$idempresa = $this->session->userdata('usuario')->idempresa;
		
		$objeto = new stdClass(); 
		$arr = array();
		
		if(($filtros == "true") && ($aba == "atendimento")){				
			foreach($this->input->post('filtro') AS $valor){
				$filtro[] = $valor;
			}
			
			$contadorFinalizado = 0;
			$contadorNaoConcluido = 0;
			$contadorEmAndamento = 0;
			$contadorEmEspera = 0;
			$contadorEmAtraso = 0;
			
			$contandoFiltros = 0;
			$cstats = 0; //utilizado para só retornar o filtro status uma vez
			$filtro_nome = 0; 

			$status = array();
			$between = array();
			$nomes = array();
			

			
			$nunFiltros = count($filtro);
						
			foreach($filtro AS $key => $filtro_select){   // "k" no inico da variavel é de "key" e "v" é de "valor" 
				$contandoFiltros++;

				if($filtro_select == "status"){

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
						$status[] = "(aten.status = '".$finalizado."')";
					}
					if(!empty($nao_concluido)){
						$status[] = "(aten.status = '".$nao_concluido."')";
					}
					if(!empty($em_andamento)){
						$status[] = "(aten.status = '".$em_andamento."')";
					}
					if(!empty($em_espera)){
						$status[] = "(aten.status = '".$em_espera."')";
					}
					if(!empty($em_atraso)){
						$status[] = "(aten.status = '".$em_atraso."')";
					}									
					
					if($cstats == 0){
						$arr[] = array(
							"filtro" => $filtro_select, 
							"finalizado"=>(!empty($finalizado))? $finalizado:"", 
							"nao_concluido"=>(!empty($nao_concluido))? $nao_concluido:"", 
							"em_andamento"=>(!empty($em_andamento))? $em_andamento:"",
							"em_espera"=>(!empty($em_espera))? $em_espera:"",
							"em_atraso"=>(!empty($em_atraso))? $em_atraso:"",
							);
						$cstats++;
					}
					
					unset($finalizado, $nao_concluido, $em_andamento, $em_espera, $em_atraso);
									
				}
				
				elseif($filtro_select == "data_agendada"){					
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
						$between[] = "(data_agendada BETWEEN '".$data_inicial."' AND '".$data_final."')";
						$arr[] = array("filtro" => $filtro_select, "data_inicio" => fdata($data_inicial,"/"), "data_final" => fdata($data_final,"/"));
					}
				}
				
				elseif($filtro_select == "nome"){	
					if($this->input->post('q')){
						foreach($this->input->post('q') AS $knome => $vnome){
							if($key == $knome){
								if($filtro_nome == 0){
									$this->db->select("GROUP_CONCAT(DISTINCT usu.nome SEPARATOR ', ') AS usuario", false);
									$this->db->from($this->db->dbprefix."usuario usu, ".$this->db->dbprefix."usuario_atendimento usera");
									$this->db->where("usera.idatendimento = aten.idatendimento AND usera.idusuario = usu.idusuario");
									$this->db->group_by("usera.idatendimento");
								}
								$nome = $vnome;
							}
						}
					}
										
					if(!empty($nome)){
						$nomes[] = "(usu.nome LIKE '%".$nome."%')";
						$arr[] = array("filtro" => $filtro_select, "nome" => $nome);
					}
					$filtro_nome++;
				}
			}
			
			if(!empty($status)){
				$this->db->where("(".implode(" OR ", $status).")");
			}
			if(!empty($between)){
				$this->db->where("(".implode(" OR ", $between).")");
			}
			if(!empty($nomes)){
				$this->db->where("(".implode(" OR ", $nomes).")");
			}
			
		}
		
		$this->db->select("cli.nome, titulo, aten.idatendimento, aten.endereco, aten.endereco_numero, aten.endereco_complemento, aten.bairro, estado.sigla AS estado, cidade.nome AS cidade, aten.status");
		$this->db->select($campos_adicionais);
		//$this->db->select("(SELECT min(ua.data_hora_checkin) FROM ".$this->db->dbprefix."usuario_atendimento as ua WHERE ua.idatendimento = aten.idatendimento ORDER BY ua.data_hora_checkin ASC LIMIT 1) as ultimo_checkin");
		//$this->db->select("(SELECT max(ua.data_hora_checkout) FROM ".$this->db->dbprefix."usuario_atendimento AS ua WHERE ua.idatendimento = aten.idatendimento HAVING count(ua.data_hora_checkout) = count(ua.idusuario_atendimento)) as ultimo_checkout");
		$this->db->select("(SELECT max(ua.visita) FROM vt_usuario_atendimento AS ua WHERE ua.idatendimento = aten.idatendimento) as total_visitas");
		$this->db->from($this->db->dbprefix."atendimento aten, ".$this->db->dbprefix."cliente cli, ".$this->db->dbprefix."estado estado,".$this->db->dbprefix."cidade cidade");
		$this->db->where("aten.idcliente = cli.idcliente AND aten.idestado = estado.idestado AND aten.idcidade = cidade.idcidade AND aten.idempresa = ".$idempresa);
	
		//utilizo para fazer um filtro por data logo de inicio e limitar a quantidade de registros
		if(($filtros == "false") || ((empty($data_inicial)) && (empty($data_final)))){
			$data_final = date("Y-m-d");
			$date = new DateTime($data_final);
			$date->modify("-15 days");
			$data_inicial = $date->format("Y-m-d");
	
			$this->db->where("aten.data_agendada BETWEEN '".$data_inicial."' AND '".$data_final."'");
			$arr[] = array("filtro" => "data_agendada", "data_inicio" => fdata($data_inicial,"/"), "data_final" => fdata($data_final,"/"));
		}
		
		$atendimentos = $this->db->get()->result();
		//pexit("teste", $this->db->last_query());
		
		$objeto->atendimentos = $atendimentos;
		$objeto->preenchimento = $arr;
		//pr("objeto", $this->db->last_query());
		return $objeto;
	}	
	
	public function relatorio_funcionario($params){
		if(!empty($params)){
			extract($params, EXTR_OVERWRITE);
		}	
		
		$idempresa = $this->session->userdata('usuario')->idempresa;
		
		$objeto = new stdClass(); 
		$arr = array();
		
		if(($filtros == "true") && ($aba == "funcionario")){			
			
			foreach($this->input->post('filtro2') AS $valor){
				$filtro[] = $valor;
			}
			
			$contadorFinalizado = 0;
			$contadorNaoConcluido = 0;
			$contadorEmAndamento = 0;
			$contadorEmEspera = 0;
			$contadorEmAtraso = 0;
			
			$contandoFiltros = 0;
			$cstats = 0; //utilizado para só retornar o filtro status uma vez
			$filtro_nome = 0; 

			$status = array();
			$between = array();
			$nomes = array();
			
			$nunFiltros = count($filtro);
						
			foreach($filtro AS $key => $filtro_select){   // "k" no inico da variavel é de "key" e "v" é de "valor" 
				$contandoFiltros++;

				if($filtro_select == "status"){

					if($contadorFinalizado < 1){
						if($this->input->post('finalizado2')){
							$contadorFinalizado++;
							foreach($this->input->post('finalizado2') AS $kfinalizado => $vfinalizado){
									$finalizado = $vfinalizado;
							}
						}
					}
					if($contadorNaoConcluido < 1){
						if($this->input->post('nao_concluido2')){
							$contadorNaoConcluido++;
							foreach($this->input->post('nao_concluido2') AS $knao_concluido => $vnao_concluido){
									$nao_concluido = $vnao_concluido;
							}
						}
					}
					if($contadorEmAndamento < 1){
						if($this->input->post('em_andamento2')){
							$contadorEmAndamento++;
							foreach($this->input->post('em_andamento2') AS $kem_andamento => $vem_andamento){
									$em_andamento = $vem_andamento;
							}
						}
					}
					if($contadorEmEspera < 1){
						if($this->input->post('em_espera2')){
							$contadorEmEspera++;
							foreach($this->input->post('em_espera2') AS $kem_espera => $vem_espera){
									$em_espera = $vem_espera;
							}
						}
					}
					if($contadorEmAtraso < 1){
						if($this->input->post('em_atraso2')){
							$contadorEmAtraso++;
							foreach($this->input->post('em_atraso2') AS $kem_atraso => $vem_atraso){
									$em_atraso = $vem_atraso;
							}
						}
					}										
					
					if(!empty($finalizado)){
						$status[] = "(aten.status = '".$finalizado."')";
					}
					if(!empty($nao_concluido)){
						$status[] = "(aten.status = '".$nao_concluido."')";
					}
					if(!empty($em_andamento)){
						$status[] = "(aten.status = '".$em_andamento."')";
					}
					if(!empty($em_espera)){
						$status[] = "(aten.status = '".$em_espera."')";
					}
					if(!empty($em_atraso)){
						$status[] = "(aten.status = '".$em_atraso."')";
					}									
					
					if($cstats == 0){
						$arr[] = array(
							"filtro2" => $filtro_select, 
							"finalizado2"=>(!empty($finalizado))? $finalizado:"", 
							"nao_concluido2"=>(!empty($nao_concluido))? $nao_concluido:"", 
							"em_andamento2"=>(!empty($em_andamento))? $em_andamento:"",
							"em_espera2"=>(!empty($em_espera))? $em_espera:"",
							"em_atraso2"=>(!empty($em_atraso))? $em_atraso:"",
							);
						$cstats++;
					}
					
					unset($finalizado, $nao_concluido, $em_andamento, $em_espera, $em_atraso);
									
				}
				
				elseif($filtro_select == "data_agendada"){					
					if($this->input->post('data_inicio2')){
						foreach($this->input->post('data_inicio2') AS $kdini => $vdini){
							if($key == $kdini){
								$data_inicial = fdata($vdini,"-");
							}
						}
					}
					if($this->input->post('data_fim2')){
						foreach($this->input->post('data_fim2') AS $kdfim => $vdfim){
							if($key == $kdfim){
								$data_final = fdata($vdfim,"-");
							}
						}
					}
										
					if((!empty($data_inicial)) && (!empty($data_final))){
						$between[] = "(aten.data_agendada BETWEEN '".$data_inicial."' AND '".$data_final."')";
						$arr[] = array("filtro2" => $filtro_select, "data_inicio2" => fdata($data_inicial,"/"), "data_final2" => fdata($data_final,"/"));
					}
				}
				
				elseif($filtro_select == "nome"){	
					if($this->input->post('q2')){
						foreach($this->input->post('q2') AS $knome => $vnome){
							if($key == $knome){
								$this->db->select("usu.nome AS usuario");
								$nome = $vnome;
							}
						}
					}
										
					if(!empty($nome)){
						$nomes[] = "(usu.nome LIKE '%".$nome."%')";
						$arr[] = array("filtro2" => $filtro_select, "nome2" => $nome);
					}
					$filtro_nome++;
				}
			}
			
			if(!empty($status)){
				$this->db->where("(".implode(" OR ", $status).")");
			}
			if(!empty($between)){
				$this->db->where("(".implode(" OR ", $between).")");
			}
			if(!empty($nomes)){
				$this->db->where("(".implode(" OR ", $nomes).")");
			}
				
		}

		
		$this->db->select("usu_at.idusuario_atendimento, usu_at.data_hora_checkin, usu_at.data_hora_checkout, usu_at.visita, TIMEDIFF(usu_at.data_hora_checkout, usu_at.data_hora_checkin) AS duracao", false);
		$this->db->select("aten.data_agendada, aten.tempo_estimado, usu.nome, aten.titulo as atendimento, cli.nome as cliente, cid.nome as cidade, aten.bairro, aten.status");
		$this->db->from($this->db->dbprefix."usuario_atendimento usu_at, ".$this->db->dbprefix."usuario usu, ".$this->db->dbprefix."atendimento aten, ".$this->db->dbprefix."cliente cli, ".$this->db->dbprefix."cidade cid");
		$this->db->where("usu_at.idusuario = usu.idusuario AND usu_at.idatendimento = aten.idatendimento AND aten.idcidade = cid.idcidade AND aten.idcliente = cli.idcliente AND aten.idempresa = ".$idempresa);
		
		//utilizo para fazer um filtro por data logo de inicio e limitar a quantidade de registros
		if(($filtros == "false") || ((empty($data_inicial)) && (empty($data_final)))){	
			$data_final = date("Y-m-d");
			$date = new DateTime($data_final);
			$date->modify("-15 days");
			$data_inicial = $date->format("Y-m-d");
	
			$this->db->where("aten.data_agendada BETWEEN '".$data_inicial."' AND '".$data_final."'");
			$arr[] = array("filtro2" => "data_agendada", "data_inicio2" => fdata($data_inicial,"/"), "data_final2" => fdata($data_final,"/"));
		}
		
		
		$atendimentos = $this->db->get()->result();
		//pexit("teste", $this->db->last_query());
			
		$objeto->atendimentos_funcionario = $atendimentos;
		$objeto->preenchimento2 = $arr;
		
		return $objeto;
	}
	
	public function calcula_duracao_atendimentos($atendimentos = array()){
		//pr("atendimentos", $atendimentos);
		if(!empty($atendimentos->atendimentos)){
			foreach($atendimentos->atendimentos as $atendimento){
				$duracoes = array();
				$total_duracao = new DateTime("00:00:00");
				$visitas = $atendimento->total_visitas;
				if(!empty($visitas)){
					for($visita = 1; $visita <= $visitas; $visita++){
						$query = $this->db->query("SELECT TIMEDIFF(
														(SELECT max(ua.data_hora_checkout) FROM ".$this->db->dbprefix."usuario_atendimento AS ua WHERE ua.idatendimento = '".$atendimento->idatendimento."' AND ua.visita = '$visita' HAVING count(ua.data_hora_checkout) = count(ua.idusuario_atendimento)),
														(SELECT min(ua.data_hora_checkin) FROM ".$this->db->dbprefix."usuario_atendimento as ua WHERE ua.idatendimento = '".$atendimento->idatendimento."' AND ua.visita = '$visita' ORDER BY ua.data_hora_checkin ASC LIMIT 1)
														) duracao")->row();
						$duracoes[] = $query->duracao;
					}
					
					foreach($duracoes as $duracao){
						if(empty($duracao)){
							$total_duracao = "-";
							break;
						}
						$partes_data = explode(":", $duracao);
						$total_duracao->modify("+ $partes_data[0] hours");
						$total_duracao->modify("+ $partes_data[1] minutes");
						$total_duracao->modify("+ $partes_data[2] seconds");
					}
					if($total_duracao != "-"){
						$total_duracao = $total_duracao->format("H:i")."h";
					}
					$atendimento->duracao = $total_duracao;
				}else{
					$atendimento->duracao = "-";
				}
			}
		}
		//pr("atendimentos", $atendimentos);
		return $atendimentos;
	}
	
	
	public function relatorio_atendimento_old($params){
		if(!empty($params)){
			extract($params, EXTR_OVERWRITE);
		}	
		
		$idempresa = $this->session->userdata('usuario')->idempresa;
		
		$objeto = new stdClass(); 
		if($filtros){			
			$condicao = "(";
			$and = "";
			
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
					{$busca},
					(SELECT min(ua.data_hora_checkin) FROM ".$this->db->dbprefix."usuario_atendimento as ua WHERE ua.idatendimento = aten.idatendimento ORDER BY ua.data_hora_checkin ASC LIMIT 1) as ultimo_checkin,
					(SELECT max(ua.data_hora_checkout) FROM ".$this->db->dbprefix."usuario_atendimento AS ua WHERE ua.idatendimento = aten.idatendimento HAVING count(ua.data_hora_checkout) = count(ua.idusuario_atendimento)) as ultimo_checkout
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
			$objeto->atendimentos = $atendimentos;
			$objeto->preenchimento = $arr;
		}
		else{

			
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
					aten.status,
					(SELECT min(ua.data_hora_checkin) FROM ".$this->db->dbprefix."usuario_atendimento as ua WHERE ua.idatendimento = aten.idatendimento ORDER BY ua.data_hora_checkin ASC LIMIT 1) as ultimo_checkin,
					(SELECT max(ua.data_hora_checkout) FROM ".$this->db->dbprefix."usuario_atendimento AS ua WHERE ua.idatendimento = aten.idatendimento HAVING count(ua.data_hora_checkout) = count(ua.idusuario_atendimento)) as ultimo_checkout
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
				$objeto->atendimentos = $atendimentos;

		}
		pr("objeto", $this->db->last_query());
		return $objeto;
	}
}

/* End of file usuario.php */
/* Location: ./application/controllers/usuario.php */