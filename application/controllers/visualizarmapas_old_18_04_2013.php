<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class visualizarmapas extends CI_Controller {

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
*/		foreach($atendimentos as $atendimento){
			$endereco = "{$atendimento->endereco}, {$atendimento->endereco_numero}, {$atendimento->bairro}, {$atendimento->nomecidade},  {$atendimento->uf}";
			$texto = "Checkin: {$atendimento->nomeusuario}<br />Atendimento: {$atendimento->titulo}<br />{$endereco}";
			
			//depois que acertar a parte da latitude e longitude para pegar o endereço certo descomentar esse if
			if((!empty($atendimento->latitude))&&(!empty($atendimento->longitude))){
				$this->gmap->addMarkerByCoords($atendimento->longitude,$atendimento->latitude, "{$atendimento->nomeusuario}","{$texto}","","http://www.veritime.com.br/admin/assets/images/icon_map.png");
			}
			//http://red/veritime/admin/assets/images/icon_map.png
			//else{
				//$this->gmap->addMarkerByAddress("{$endereco}","", "{$texto}");
			//}
		}

// Add a custom marker icon
/*$this->gmap->setMarkerIconKey('http://red/veritime/admin/assets/images/icon_map.png', array
(
    'image' => "/assets/images/icon_map.png",
    'iconSize' => array("32", "37"),
    'iconAnchor' => array("6", "20"),
    'infoWindowAnchor' => array("5", "1")
));*/
		
		$dados['headerjs'] = $this->gmap->getHeaderJS();
		$dados['headermap'] = $this->gmap->getMapJS();
		$dados['onload'] = $this->gmap->printOnLoad();
		$dados['map'] = $this->gmap->printMap();
		$dados['sidebar'] = $this->gmap->printSidebar();
		
		
        $dados['view'] = $this->load->view('visualizarmapas/visualizar', $dados, TRUE);
        $dados['menu_selecionado'] = 'menu_mapa';
        $this->load->view('dashboard/interna', $dados);
		
    }
   
}

/* End of file visualizarmapas.php */
/* Location: ./application/controllers/visualizarmapas.php */