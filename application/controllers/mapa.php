
<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Mapa extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    function index() {
        $this->visualizar();
    }

    function visualizar() {
        $dados = array();
        $idempresa = $this->session->userdata('usuario')->idempresa;
				
		$this->load->library('GMap');
		
		$this->gmap->GoogleMapAPI();
		
		// valid types are hybrid, satellite, terrain, map
		$this->gmap->setMapType('map');
		
		// Carregando o model de atendimento.
		// you can also use addMarkerByCoords($long,$lat)
		// both marker methods also support $html, $tooltip, $icon_file and $icon_shadow_filename
		
        $this->load->model("atendimento_model");
		
		$this->array_local_atendimento = array();
		
		$this->array_local_checkin = array();
		
		/* BY Lincoln */
		$dados["atendimentos"] = json_encode($this->configura_atendimento_mapa($this->atendimento_model->get_atendimentos_mapa()->result()));
		//pexit("teste", $dados["atendimentos"]);
		//pexit("teste", $this->db->last_query());
		
		$dados["funcionario_checkin"] = $this->configura_funcionario_checkin($this->atendimento_model->get_funcionario_checkin()->result());
		//pexit("teste", $this->db->last_query());

		/* //BY Lincoln */
		
		/*
        $atendimentos = $this->atendimento_model->get_by_id_todos($idempresa);
		if(!empty($atendimentos)){
			foreach($atendimentos as $atendimento){
				$details_url = "http://maps.googleapis.com/maps/api/geocode/json?latlng=".$atendimento->latitude.",".$atendimento->longitude."&sensor=false";
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $details_url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$geoloc = json_decode(curl_exec($ch), true);
				$endereco_completo = $geoloc['results'][0]['formatted_address'];
				$data_hora_checkin = date('d/m/Y - H:i', strtotime($atendimento->ultima_data));
				$texto="<div class='MarkerPopUp' style='width: 300px; height:200px;'><div style='font-size:12px'><p><strong>Último Checkin</strong><br/>{$atendimento->nomeusuario}<br/>Dia {$data_hora_checkin}h</p><p><strong>Atendimento:</strong><br/>{$atendimento->titulo}</p><p><strong>Endereço do Checkin: </strong><br/>{$endereco_completo}</p></div></div>";
				
				//depois que acertar a parte da latitude e longitude para pegar o endereço certo descomentar esse if
				if((!empty($atendimento->latitude))&&(!empty($atendimento->longitude))){
					$this->gmap->addMarkerByCoords($atendimento->longitude,$atendimento->latitude, "{$atendimento->nomeusuario}","{$texto}","","http://www.veritime.com.br/admin/assets/images/icon_map.png");
				}
			}
			
			$dados['headerjs'] = $this->gmap->getHeaderJS();
			$dados['headermap'] = $this->gmap->getMapJS();
			$dados['onload'] = $this->gmap->printOnLoad();
			$dados['map'] = $this->gmap->printMap();
			$dados['sidebar'] = $this->gmap->printSidebar();			
			
		}else{//se não encontrar nenhum atendimento exibe um mapa defaut

            
            $dados['onload'] ='<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyChwamqGkTma6SI7u816UrUE9_xPKRGRvg&sensor=false"></script>
            <script type="text/javascript">
			  function initialize() {
				var mapOptions = {
				  zoom: 5,
				  center: new google.maps.LatLng(-22.91613532185742, -43.44741549999998),
				  mapTypeControl: true,
				  mapTypeControlOptions: {
					  style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
				  },
				  zoomControl: true,
				  zoomControlOptions: {
					  style: google.maps.ZoomControlStyle.DEFAULT 
				 },
				  mapTypeId: google.maps.MapTypeId.ROADMAP
				};
				var map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
			  }
			  google.maps.event.addDomListener(window, "load", initialize);
			</script>';
			$dados['map'] = '<div id="map-canvas" style="width:100%; height:450px;"></div>';

		}
		*/
        $dados['view'] = $this->load->view('visualizarmapas/visualizar', $dados, TRUE);
        $this->load->view('dashboard/interna', $dados);
		
    }
	
	/*
	public function configura_atendimento_mapa($atendimentos = ""){
		$retorno = array();
		if(!empty($atendimentos)){
			foreach($atendimentos as $atendimento){
				$endereco_completo = "";
				$endereco_completo.=trim($atendimento->endereco);

				if($atendimento->endereco_numero){
					$endereco_completo.= ', '.$atendimento->endereco_numero;
				}
				if($atendimento->endereco_complemento){
					$endereco_completo.= ', '.$atendimento->endereco_complemento;
				}
				if($atendimento->bairro){
					$endereco_completo.= ' - '.$atendimento->bairro;
				}
				if($atendimento->nomecidade){
					$endereco_completo.= ' - '.$atendimento->nomecidade;
				}			
				if($atendimento->uf){
					$endereco_completo.= '/'.$atendimento->uf;
				}
				
				if($atendimento->status == "nao_concluido"){
					$atendimento->status = "não_concluído";
				}
				$status = ucfirst(str_replace("_", " ", $atendimento->status));
				
				$contentString = '<div id="content" style="width:250px;">'.
				'<h4 id="firstHeading" class="firstHeading">'.$atendimento->nomecliente.'</h4>'.
				'<p><strong>Atendimento: </strong>'.$atendimento->titulo.'</p>'.
				'<div id="bodyContent">'.
				'<p>'.$endereco_completo.'</p>'.
				'<p><strong>Funcionários alocados:</strong><br>'.
				$atendimento->nome_usuario.'</p>'.
				'<p><strong>Horário Agendado: </strong>'.substr($atendimento->hora_agendada, 0, 5).'h</p>'.
				'<p><strong>Status do Atendimento: </strong>'.$status.'</p>'.
				'</div>'.
				'</div>';
				$retorno[] = "['".$atendimento->titulo."', ".$atendimento->latitude.", ".$atendimento->longitude.", '".$contentString."']";
			}
		}
		return $retorno;
	}	
	*/
	
	public function configura_atendimento_mapa($atendimentos = ""){
		$objeto_retorno = array();	
		if(!empty($atendimentos)){
			$data_atual = strtotime(date("Y-m-d"));
			$data_hora_atual = strtotime(date("Y-m-d H:i:s"));
			//pexit("atendimentos", $atendimentos);
			foreach($atendimentos as $atendimento){
				$retorno = new stdClass();
				
				$data_hora_atendimento = strtotime($atendimento->data_agendada." ".$atendimento->hora_agendada);
				
				$ultimo_checkout = "";
				if(!empty($atendimento->ultimo_checkout)){
					$ultimo_checkout = strtotime(substr($atendimento->ultimo_checkout, 0, 11)); 
				}
				
				//no caso do atendimento estar em espera, verifico se a data do atendimento é menor que a data atual, caso seja eu atualizo o atendimento com o status de em_atraso
				if(($data_hora_atendimento < $data_hora_atual) && ($atendimento->status == "em_espera")){
						$this->load->model("atendimento_model");
						$this->atendimento_model->atualizarStatus($atendimento->idatendimento);
						$atendimento->status = "em_atraso";
				}
				
				$endereco_completo = "";
				$endereco_completo.=trim($atendimento->endereco);

				if($atendimento->endereco_numero){
					$endereco_completo.= ', '.$atendimento->endereco_numero;
				}
				if($atendimento->endereco_complemento){
					$endereco_completo.= ', '.$atendimento->endereco_complemento;
				}
				if($atendimento->bairro){
					$endereco_completo.= ' - '.$atendimento->bairro;
				}
				if($atendimento->nomecidade){
					$endereco_completo.= ' - '.$atendimento->nomecidade;
				}			
				if($atendimento->uf){
					$endereco_completo.= '/'.$atendimento->uf;
				}
				if($atendimento->status == "nao_concluido"){
					$atendimento->status = "não_concluído";
				}
				$status = ucfirst(str_replace("_", " ", $atendimento->status));
				
				/*
				$contentString = '<div id="content" style="width:250px;">'.
				'<h4 id="firstHeading" class="firstHeading">'.$atendimento->nomecliente.'</h4>'.
				'<p><strong>Atendimento: </strong>'.$atendimento->titulo.'</p>'.
				'<div id="bodyContent">'.
				'<p>'.$endereco_completo.'</p>'.
				'<p><strong>Funcionários alocados:</strong><br>'.
				$atendimento->nome_usuario.'</p>'.
				'<p><strong>Horário Agendado: </strong>'.substr($atendimento->hora_agendada, 0, 5).'h</p>'.
				'<p><strong>Status do Atendimento: </strong>'.$status.'</p>'.
				'</div>'.
				'</div>';
				*/
				
				$checkin = (!empty($atendimento->ultimo_checkin)) ? fdata(substr($atendimento->ultimo_checkin, 0, 10), "/")." ".substr($atendimento->ultimo_checkin, 11, 5)."h" : "Nenhum check-in realizado";
				$checkout = (!empty($atendimento->ultimo_checkout)) ? fdata(substr($atendimento->ultimo_checkout, 0, 10), "/")." ".substr($atendimento->ultimo_checkout, 11, 5)."h" : "Check-out não realizado";
				
				$array_tds = array();
				
				$contentString = '<table id="content" style="width:500px; border-collapse: inherit; border-spacing: 1px;">';
				
				$array_tds[] = '<td style="width: 168px; padding: 2px 6px;"><strong>Nome do cliente</strong></td><td style="padding: 2px 6px;">'.$atendimento->nomecliente.'</td>';			
				$array_tds[] = '<td style="width: 168px; padding: 2px 6px;"><strong>Nome do atendimento</strong></td><td style="padding: 2px 6px;">'.$atendimento->titulo.'</td>';
				$array_tds[] = '<td style="width: 168px; padding: 2px 6px;"><strong>Status</strong></td><td style="padding: 2px 6px;">'.$status.'</td>';
				$array_tds[] = '<td style="width: 168px; padding: 2px 6px;"><strong>Data e hora</strong></td><td style="padding: 2px 6px;">'.fdata($atendimento->data_agendada, "/").' '.substr($atendimento->hora_agendada, 0, 5).'h </td>';
				$array_tds[] = '<td style="width: 168px; padding: 2px 6px;"><strong>Tempo estimado</strong></td><td style="padding: 2px 6px;">'.substr($atendimento->tempo_estimado, 0, 5).'h</td>';
				$array_tds[] = '<td style="width: 168px; padding: 2px 6px;"><strong>Endereço do atendimento</strong></td><td style="padding: 2px 6px;">'.$endereco_completo.'</td>';

				if(!empty($atendimento->nome_contato_responsavel)){
					$array_tds[] = "<td style='width: 168px; padding: 2px 6px;'><strong>Responsável</strong></td><td style='padding: 2px 6px;'>".$atendimento->nome_contato_responsavel."</td>";
					
					if(!empty($atendimento->telefone_contato_responsavel)){
						$ramal = (!empty($atendimento->ramal_contato_responsavel)) ? " Ramal: ".$atendimento->ramal_contato_responsavel : "";
						$array_tds[] = "<td style='width: 168px; padding: 2px 6px;'><strong>Telefone do Responsável</strong></td><td style='padding: 2px 6px;'>".$atendimento->telefone_contato_responsavel."$ramal</td>";
						
					}
					if(!empty($atendimento->celular_contato_responsavel)){
						$array_tds[] = "<td style='width: 168px; padding: 2px 6px;'><strong>Celular do Responsável</strong></td><td style='padding: 2px 6px;'>".$atendimento->celular_contato_responsavel."</td>";
					}
				}else{
					$array_tds[] = "<td style='width: 168px; padding: 2px 6px;'><strong>Contato Principal</strong></td><td style='padding: 2px 6px;'>".$atendimento->nome_contato_principal."</td>";
					
					if(!empty($atendimento->telefone_contato_principal)){
						$ramal = (!empty($atendimento->ramal_contato_principal)) ? " Ramal: ".$atendimento->ramal_contato_principal : "";
						$array_tds[] = "<td style='width: 168px; padding: 2px 6px;'><strong>Telefone do contato Principal</strong></td><td style='padding: 2px 6px;'>".$atendimento->telefone_contato_principal."$ramal</td>";
						
					}
					if(!empty($atendimento->celular_contato_principal)){
						$array_tds[] = "<td style='width: 168px; padding: 2px 6px;'><strong>Celular do contato Principal</strong></td><td style='padding: 2px 6px;'>".$atendimento->celular_contato_principal."</td>";
					}
				}					
									
				$array_tds[] = '<td style="width: 168px; padding: 2px 6px;"><strong>Check-in</strong></td><td style="padding: 2px 6px;">'.$checkin.'</td>';
				$array_tds[] = '<td style="width: 168px; padding: 2px 6px;"><strong>Check-out</strong></td><td style="padding: 2px 6px;">'.$checkout.'</td>';
				
				$i = 0;
				foreach($array_tds as $td){
					$estilo_tr = ($i % 2 == 0) ? "background: #EBEBEB;" : "background: #f5f5f5;";
					$contentString .= "<tr style='".$estilo_tr."'>";
					$contentString .= $td;
					$contentString .= "</tr>";
					$i++;
				}
				
				//$contentString .= "<tr>".implode("</tr><tr>", $array_tds)."</tr>";
				
				$contentString .= '</table>';
									
										
										
			
				//pexit("teste", $contentString);
				$array_local_registro = array();
				$array_local_registro = array($atendimento->latitude, $atendimento->longitude);

				
				if(in_array($array_local_registro, $this->array_local_atendimento)){
					$fator_parcial = 0.0000300;
					$i = 0;
					//pr("te2", array($atendimento->latitude - $fator_parcial, $atendimento->longitude - $fator_parcial));
					while($i != -1){
						$i++;
						$fator_parcial = $fator_parcial * $i;
						switch(1){
							case (!in_array(array($atendimento->latitude + $fator_parcial, $atendimento->longitude), $this->array_local_atendimento) ? 1 : ""):
								$atendimento->latitude = $atendimento->latitude + $fator_parcial;
								$i = -1;
								break;
							case (!in_array(array($atendimento->latitude, $atendimento->longitude + $fator_parcial), $this->array_local_atendimento) ? 1 : ""):
								$atendimento->longitude = $atendimento->longitude + $fator_parcial;
								$i = -1;
								break;
							case (!in_array(array($atendimento->latitude - $fator_parcial, $atendimento->longitude), $this->array_local_atendimento) ? 1 : ""):
								$atendimento->latitude = $atendimento->latitude - $fator_parcial;
								$i = -1;
								break;
							case (!in_array(array($atendimento->latitude, $atendimento->longitude - $fator_parcial), $this->array_local_atendimento) ? 1 : ""):
								$atendimento->longitude = $atendimento->longitude - $fator_parcial;
								$i = -1;
								break;
							case (!in_array(array($atendimento->latitude + $fator_parcial, $atendimento->longitude + $fator_parcial), $this->array_local_atendimento) ? 1 : ""):
								$atendimento->latitude = $atendimento->latitude + $fator_parcial;
								$atendimento->longitude = $atendimento->longitude + $fator_parcial;
								$i = -1;
								break;
							case (!in_array(array($atendimento->latitude + $fator_parcial, $atendimento->longitude - $fator_parcial), $this->array_local_atendimento) ? 1 : ""):
								$atendimento->latitude = $atendimento->latitude + $fator_parcial;
								$atendimento->longitude = $atendimento->longitude - $fator_parcial;
								$i = -1;
								break;
							case (!in_array(array($atendimento->latitude - $fator_parcial, $atendimento->longitude + $fator_parcial), $this->array_local_atendimento) ? 1 : ""):
								$atendimento->latitude = $atendimento->latitude - $fator_parcial;
								$atendimento->longitude = $atendimento->longitude + $fator_parcial;
								$i = -1;
								break;
							case (!in_array(array($atendimento->latitude - $fator_parcial, $atendimento->longitude - $fator_parcial), $this->array_local_atendimento) ? 1 : ""):
								$atendimento->latitude = $atendimento->latitude - $fator_parcial;
								$atendimento->longitude = $atendimento->longitude - $fator_parcial;
								$i = -1;
								break;
						}
						//pr("teste", $i);
						
						//$i = -1;
					}
				}
		
				
				$this->array_local_atendimento[] = array($atendimento->latitude, $atendimento->longitude);
				
				$retorno->titulo = $atendimento->titulo;
				$retorno->latitude = $atendimento->latitude;
				$retorno->longitude = $atendimento->longitude;
				$retorno->contentString = $contentString;
	
				//divido os atendimentos pelo tipo de status.
				if(($atendimento->status == "em_espera") && (strtotime($atendimento->data_agendada) == $data_atual)){
					$objeto_retorno[]["em_espera"] = $retorno;
				}else if((empty($ultimo_checkout)) && ($atendimento->status == "em_andamento")){
					$objeto_retorno[]["em_andamento"] = $retorno;
				}else if(($atendimento->status == "em_atraso")  || ((empty($ultimo_checkout) && ($data_hora_atendimento < $data_hora_atual)))){
					$objeto_retorno[]["em_atraso"] = $retorno;
				}else if(($atendimento->status == "não_concluído") && (!empty($ultimo_checkout)) && ($ultimo_checkout = $data_atual)) {
					$objeto_retorno[]["nao_concluido"] = $retorno;
				}else if(($atendimento->status == "finalizado") && (!empty($ultimo_checkout)) && ($ultimo_checkout = $data_atual)){
					$objeto_retorno[]["finalizado"] = $retorno;
				}
				
				
			}
			//pexit("teste", $objeto_retorno);
		}
		return $objeto_retorno;
	}	

	public function configura_funcionario_checkin($funcionarios = ""){
		$retorno = array();
		if(!empty($funcionarios)){
			foreach($funcionarios as $funcionario){
				$checkout = "";
				
				$endereco_completo_checkin = "";
				if((!empty($funcionario->latitude_checkin)) && (!empty($funcionario->longitude_checkin))){
					$details_url = "http://maps.googleapis.com/maps/api/geocode/json?latlng=".$funcionario->latitude_checkin.",".$funcionario->longitude_checkin."&sensor=false";
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $details_url);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					$geoloc = json_decode(curl_exec($ch), true);
					$endereco_completo_checkin = $geoloc['results'][0]['formatted_address'];
				}
						
				$endereco_completo_checkout = "";			
				if((!empty($funcionario->latitude_checkout)) && (!empty($funcionario->longitude_checkout))){
					$details_url = "http://maps.googleapis.com/maps/api/geocode/json?latlng=".$funcionario->latitude_checkout.",".$funcionario->longitude_checkout."&sensor=false";
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $details_url);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					$geoloc = json_decode(curl_exec($ch), true);
					$endereco_completo_checkout = $geoloc['results'][0]['formatted_address'];
				}
				
				if((!empty($funcionario->latitude_usuario)) && (!empty($funcionario->longitude_usuario))){
					$funcionario->latitude = $funcionario->latitude_usuario;
					$funcionario->longitude = $funcionario->longitude_usuario;
				}
				
				$array_local_registro = array();
				$array_local_registro = array($funcionario->latitude, $funcionario->longitude);

				
				if(in_array($array_local_registro, $this->array_local_checkin)){
					$fator_parcial = 0.0000300;
					$i = 0;
					//pr("te2", array($funcionario->latitude - $fator_parcial, $funcionario->longitude - $fator_parcial));
					while($i != -1){
						$i++;
						$fator_parcial = $fator_parcial * $i;
						switch(1){
							case (!in_array(array($funcionario->latitude + $fator_parcial, $funcionario->longitude), $this->array_local_checkin) ? 1 : ""):
								$funcionario->latitude = $funcionario->latitude + $fator_parcial;
								$i = -1;
								break;
							case (!in_array(array($funcionario->latitude, $funcionario->longitude + $fator_parcial), $this->array_local_checkin) ? 1 : ""):
								$funcionario->longitude = $funcionario->longitude + $fator_parcial;
								$i = -1;
								break;
							case (!in_array(array($funcionario->latitude - $fator_parcial, $funcionario->longitude), $this->array_local_checkin) ? 1 : ""):
								$funcionario->latitude = $funcionario->latitude - $fator_parcial;
								$i = -1;
								break;
							case (!in_array(array($funcionario->latitude, $funcionario->longitude - $fator_parcial), $this->array_local_checkin) ? 1 : ""):
								$funcionario->longitude = $funcionario->longitude - $fator_parcial;
								$i = -1;
								break;
							case (!in_array(array($funcionario->latitude + $fator_parcial, $funcionario->longitude + $fator_parcial), $this->array_local_checkin) ? 1 : ""):
								$funcionario->latitude = $funcionario->latitude + $fator_parcial;
								$funcionario->longitude = $funcionario->longitude + $fator_parcial;
								$i = -1;
								break;
							case (!in_array(array($funcionario->latitude + $fator_parcial, $funcionario->longitude - $fator_parcial), $this->array_local_checkin) ? 1 : ""):
								$funcionario->latitude = $funcionario->latitude + $fator_parcial;
								$funcionario->longitude = $funcionario->longitude - $fator_parcial;
								$i = -1;
								break;
							case (!in_array(array($funcionario->latitude - $fator_parcial, $funcionario->longitude + $fator_parcial), $this->array_local_checkin) ? 1 : ""):
								$funcionario->latitude = $funcionario->latitude - $fator_parcial;
								$funcionario->longitude = $funcionario->longitude + $fator_parcial;
								$i = -1;
								break;
							case (!in_array(array($funcionario->latitude - $fator_parcial, $funcionario->longitude - $fator_parcial), $this->array_local_checkin) ? 1 : ""):
								$funcionario->latitude = $funcionario->latitude - $fator_parcial;
								$funcionario->longitude = $funcionario->longitude - $fator_parcial;
								$i = -1;
								break;
						}
						//pr("teste", $i);
						
						//$i = -1;
					}
				}
		
				
				$this->array_local_checkin[] = array($funcionario->latitude, $funcionario->longitude);
				
				if($funcionario->status == "nao_concluido"){
					$funcionario->status = "não_concluído";
				}
				$status = ucfirst(str_replace("_", " ", $funcionario->status));
				
				$array_tds = array();
				$contentString = "";
				
				$contentString = '<table id="content" style="width:440px; border-collapse: inherit; border-spacing: 1px;">';
				
				//$array_tds[] = '<td colspan="2" style="width: 150px; padding: 0px 4px; text-align:center;"><h4><strong>Último Check-in<\/strong></h4></td>';			
				$array_tds[] = '<td style="width: 150px; padding: 2px 6px;"><strong>Nome do Funcionário</strong></td><td style="padding: 2px 6px;">'.$funcionario->nome_usuario.'</td>';			
				$array_tds[] = '<td style="width: 150px; padding: 2px 6px;"><strong>Nome Atendimento</strong></td><td style="padding: 2px 6px;">'.$funcionario->titulo_atendimento.'</td>';					
				$array_tds[] = '<td style="width: 150px; padding: 2px 6px;"><strong>Data e Hora Check-in</strong></td><td style="padding: 2px 6px;">'.date('d/m/Y - H:i', strtotime($funcionario->data_hora_checkin)).'h</td>';
				$array_tds[] = '<td style="width: 150px; padding: 2px 6px;"><strong>Endereço Check-in</strong></td><td style="padding: 2px 6px;">'.$endereco_completo_checkin.'</td>';
				if(!empty($funcionario->data_hora_checkout)){
					$array_tds[] = '<td style="width: 150px; padding: 2px 6px;"><strong>Data e Hora Check-out</strong></td><td style="padding: 2px 6px;">'.date('d/m/Y - H:i', strtotime($funcionario->data_hora_checkout)).'h</td>';
					$array_tds[] = '<td style="width: 150px; padding: 2px 6px;"><strong>Endereço Check-out</strong></td><td style="padding: 2px 6px;">'.$endereco_completo_checkout.'</td>';
				}
				$array_tds[] = '<td style="width: 150px; padding: 2px 6px;"><strong>Status</strong></td><td style="padding: 2px 6px;">'.$status.'</td>';

				$i = 0;
				foreach($array_tds as $td){
					$estilo_tr = ($i % 2 == 0) ? "background: #EBEBEB;" : "background: #f5f5f5;";
					$contentString .= '<tr style="'.$estilo_tr.'">';
					$contentString .= $td;
					$contentString .= "</tr>";
					$i++;
				}
				
				//$contentString .= "<tr>".implode("</tr><tr>", $array_tds)."</tr>";
				
				$contentString .= '</table>';
		
				$retorno[] = "['".$funcionario->nome_usuario."', ".$funcionario->latitude.", ".$funcionario->longitude.", '".$contentString."']";

			}
		}
		return $retorno;
	}
   
}

/* End of file visualizarmapas.php */
/* Location: ./application/controllers/visualizarmapas.php */