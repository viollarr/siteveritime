<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class MY_Pagination extends CI_Pagination {

    public function __construct() {
        parent::__construct();

        // Itens por página
        $this->per_page = 10;
        //$this->per_page = 5;
    }

    public function paginacao($params = array()) {
        //pr('params paginacao', $params);
        
        /*
         * Params:
         * uri
         * total_rows
         */

        // Configurando a paginação
		$this->cur_page = 0;
        $config['base_url'] = site_url($params['uri']);
        $config['total_rows'] = $params['total_rows'];
        $config['per_page'] = (!empty($params['per_page'])) ? $params['per_page'] : $this->per_page;
        $config['first_link'] = FALSE;
        $config['last_link'] = FALSE;
        $config['prev_link'] = '&lt'; // valor default &lt;
        $config['next_link'] = '&gt'; // valor default &gt;
        $config['uri_segment'] = (!empty($params['uri_segment'])) ? $params['uri_segment'] : 3;
        $this->initialize($config);
        $links = $this->create_links();
        //pr('config [my_pagination]', $config);
        //pr('links [my_pagination]', $links);
        
        return "<div class=\"pagination\">{$links}</div>";
    }
	
    public function get_per_page() {
        return $this->per_page;
    }


}

// END MY_Pagination Class

/* End of file MY_Pagination.php */
/* Location: ./application/libraries/MY_Pagination.php */