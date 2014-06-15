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
        $atendimentos = $this->atendimento_model->get_by_id_todos($idempresa);
/*		echo "<pre>";
		var_dump($atendimentos);
		echo "</pre>";
		exit;
*/		if(!empty($atendimentos)){
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
	
        $dados['view'] = $this->load->view('visualizarmapas/visualizar', $dados, TRUE);
        $this->load->view('dashboard/interna', $dados);
		
    }
   
}

/* End of file visualizarmapas.php */
/* Location: ./application/controllers/visualizarmapas.php */