<?php
$endereco_completo='';
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
?>
            
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>Gestão de equipes externas e atendimentos - Veritime</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="<?php echo base_url('assets/css/bootstrap.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/bootstrap-responsive.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/estilo-custom-modal.css'); ?>" rel="stylesheet">

    <!-- jQuery -->
    <script type="text/javascript" src="<?php echo base_url('assets/js/jquery.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/bootstrap.min.js'); ?>"></script>        

    <!--[if lt IE 9]>
    <script>
    document.createElement('header');
    document.createElement('nav');
    document.createElement('section');
    document.createElement('article');
    document.createElement('aside');
    document.createElement('footer');
    document.createElement('hgroup');
    </script>
    <![endif]-->
    <!-- Pulled from http://code.google.com/p/html5shiv/ -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

<?php /*API KEY: AIzaSyChwamqGkTma6SI7u816UrUE9_xPKRGRvg */?>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyChwamqGkTma6SI7u816UrUE9_xPKRGRvg&sensor=false">
    </script>
    <script type="text/javascript">
      function initialize() {
        var mapOptions = {
          zoom: 13,
          center: new google.maps.LatLng(<?php print $atendimento->latitude;?>, <?php print $atendimento->longitude;?>),
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
		

		 var contentString = '<div id="content" style="width:250px;">'+
		'<div id="siteNotice">'+
		'</div>'+
		'<h4 id="firstHeading" class="firstHeading"><?php print $atendimento->nomecliente;?></h4>'+
		'<p><strong>Atendimento:</strong> <?php print $atendimento->titulo;?></p>'+
		'<div id="bodyContent">'+
		'<p><?php print $endereco_completo;?></p>'+
		'<p><strong>Funcionários alocados:</strong><br>'+
		'<?php print $atendimento->nome_usuario;?></p>'+
		'</div>'+
		'</div>';
		 var infowindow = new google.maps.InfoWindow({
			  content: contentString
		 });
			
		var image = '<?php echo base_url('assets/images/icon_map.png'); ?>';
		var myLatLng = new google.maps.LatLng(<?php print $atendimento->latitude;?>, <?php print $atendimento->longitude;?>);
		var marker = new google.maps.Marker({
			  position: myLatLng,
			  map: map,
			  icon: image,
			  title: '<?php print $atendimento->nomecliente;?>'
		});
		
      google.maps.event.addDomListener(window, 'load', infowindow.open(map,marker));	
	  google.maps.event.addListener(marker, 'click', function() {
		infowindow.open(map,marker);
	  });
  	
      }
      google.maps.event.addDomListener(window, 'load', initialize);
    </script>

	<style>
    html { height: 100% }
    body { height: 100%; margin: 0; padding: 0 }	
    #map-canvas { width:100%; height: 100%; }
	#map-canvas img {
		max-width: none;
	}
    </style>

</head>

<body>
    <div id="content_modal" style="height: 88%;">
            <?php 
			/*
			[nomecliente] => Mega Mate
			[latitude] => -22.8722026
			[longitude] => -43.3423262
			[titulo] => teste 16/04
			[endereco] => Rua Firmino Fragoso 
			[endereco_numero] => 131
			[endereco_complemento] => casa 8
			[bairro] => Madureira
			[nomeestado] => Rio de Janeiro
			[uf] => RJ
			[nomecidade] => Rio de Janeiro
			*/
			?>
            <div id="map-canvas"></div>

    </div>
</body>
</html>



