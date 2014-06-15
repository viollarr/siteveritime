<script type="text/javascript">
jQuery(document).ready(function($){
// ADD Funcionário

	$("#auto").autocomplete("<?php print base_url(); ?>autocomplete/mapaAutocomplete/",
	{
	  minLength: 2,
	  scrollHeight: 220,
	  selectFirst: false
	});
});

function consultar(e){
	var conteudo = $("#sidebar_map").find('li').length;
	if(e == conteudo){
		alert("Funcionário ainda não tem registro de atendimento");
	}
}

</script>

<?php require('application/views/dashboard/mensagem.php'); // Include das mensagens dos controllers. Sucesso ou erro ?>

<div class="clearfix"></div>

<div class="pull-right">
    <a href="<?php echo base_url('atendimento/cadastro'); ?>" class="btn btn-success btn-large"><i class="icon-plus icon-white"></i> Atendimentos</a>
    <a href="<?php echo base_url('atendimento'); ?>" class="btn btn-large btn-inverse"><i class="icon-align-justify icon-white"></i> Listar atendimentos</a>
</div>                

<div class="pesquisa-mapa pull-left">
	<input type="text" name="q" id="auto" size="40" autocomplete="off" placeholder="Digite o nome do funcionário ou do atendimento" class="input-xlarge" /> 
    <button type="submit" class="btn" id="incluir_funcionario"><i class="icon-search"></i></button>
</div>                

<div class="clearfix"></div>

<style>
.mapa-localizacao img {
	max-width: none;
}
</style>

<div class="mapa-localizacao">
<div id="map-canvas" style="width:100%; height:450px;"></div>

<?php
	echo "<div style='display:none;'>";
	echo $sidebar; 
	echo "</div>";
?>    
</div>
<p>Lembre-se: aparecem no mapa os atendimentos de hoje, os atendimentos em atraso e a última ação — check-in ou check-out — de cada funcionário.</p>
<div class="clearfix"></div>
<script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
<script type="text/javascript">
  var script = '<script type="text/javascript" src="<?php echo base_url('assets/js/MarkerClusterer'); ?>/markerclusterer';
  if (document.location.search.indexOf('packed') !== -1) {
	script += '_packed';
  }
  script += '.js"><' + '/script>';
  document.write(script);
</script>
<script type="text/javascript">
	$(document).ready(function() {
		  var myOptions = {
			zoom: 10,
			center: new google.maps.LatLng(-22.884051, -43.415130),
			mapTypeId: google.maps.MapTypeId.ROADMAP
		  }
		  var map = new google.maps.Map(document.getElementById("map-canvas"), myOptions);
		  

		  setMarkers_atendimentos(map);
	
		  <?php
		  if(!empty($funcionario_checkin)){
		  ?>
			  var checkin = [<?php echo implode(", ", $funcionario_checkin); ?>];	
			  setMarkers_checkin(map, checkin);
		  <?php
		  }
		  ?>
		  
		  //$(".content").on("click", "#incluir_funcionario",function(){
			$("#incluir_funcionario").bind("click",function(){
				var nome_buscado = $("#auto").val();
				
				if((nome_buscado != "") && (nome_buscado != "Usuário não encontrado")){
					var encontrado = 0;
					for (var i = 0; i < checkins.length; i++) {
						var conteudo_checkin = checkins[i];
						if(conteudo_checkin[0] == nome_buscado){
							google.maps.event.trigger(conteudo_checkin[1], 'click');
							encontrado = 1;
						}	
					}
					if(encontrado == 0){
						alert("Nenhum dado encontrado.");
					}
					 
				}
				else if($("#auto").val() == "Não encontrado"){
					alert("Por favor, selecione um nome válido.");
				}
				
			});
		  
	});
	function empty(v){
		if ((v == null) || (v == 0) || (v == '') || (v == "") || (v == undefined)){
			return true
		}else {
			return false
		}	
	}
		  
	var styles = [{
        url: '<?php echo base_url('assets/js/MarkerClusterer/images/m1.png'); ?>',
        height: 52,
        width: 53,
        anchor: [16, 0],
        textColor: '#7fff00',
        textSize: 11
      }];
	
	var checkins = [];
	var markers = [];
	function setMarkers_atendimentos(map) {
		
		var antendimentos = <?php echo (!empty($atendimentos)) ? $atendimentos : ""; ?>;
		
		if(!empty(antendimentos)){
		  var markerClusterer = null;
			
			for (var i = 0; i < antendimentos.length; i++) {
				if(!empty(antendimentos[i].em_espera)){
					var locations = [antendimentos[i].em_espera];
					var endereco_imagem = "<?php echo base_url("assets/images/icon_map_espera.png"); ?>";
				}else if(!empty(antendimentos[i].em_andamento)){
					var locations = [antendimentos[i].em_andamento];
					var endereco_imagem = "<?php echo base_url("assets/images/icon_map_andamento.png"); ?>";
				}else if(!empty(antendimentos[i].em_atraso)){
					var locations = [antendimentos[i].em_atraso];
					var endereco_imagem = "<?php echo base_url("assets/images/icon_map_atraso.png"); ?>";	
				}else if(!empty(antendimentos[i].nao_concluido)){
					var locations = [antendimentos[i].nao_concluido];
					var endereco_imagem = "<?php echo base_url("assets/images/icon_map_nao_concluido.png"); ?>";	
				}else if(!empty(antendimentos[i].finalizado)){
					var locations = [antendimentos[i].finalizado];
					var endereco_imagem = "<?php echo base_url("assets/images/icon_map_finalizado.png"); ?>";
				}
				
				 var image = new google.maps.MarkerImage(endereco_imagem,
				  // This marker is 20 pixels wide by 32 pixels tall.
				  new google.maps.Size(32, 37),
				  // The origin for this image is 0,0.
				  new google.maps.Point(0,0),
				  // The anchor for this image is the base of the flagpole at 0,32.
				  new google.maps.Point(0, 32));
				
				//alert(locations);
				for (var i2 = 0; i2 < locations.length; i2++) {	
				
					var atendimento = locations[i2];
					var myLatLng = new google.maps.LatLng(atendimento.latitude, atendimento.longitude);
					var html = "<div class='markerpopup' style='width: 520px;  height:244px; overflow: auto;'><div style='font-size:12px; '>"+atendimento.contentString+"<\/div><\/div>";
					//var html = checkin[3];
					var marker = new google.maps.Marker({
						position: myLatLng,
						map: map,
						icon: image,
						title: atendimento.titulo,
						zIndex: 1,
						html: html
					});

					var infowindow = new google.maps.InfoWindow();			
					google.maps.event.addListener(marker, 'click', function() {
					   infowindow.setContent(this.html);//set the content
					   infowindow.open(map,this);

				   });
				   
				   markers.push(marker);
			
				}
				var proximo = checkins.length;
				checkins[proximo] = [atendimento.titulo, marker];
			}
			
			 markerClusterer = new MarkerClusterer(map, markers, {
			  maxZoom: null,
			  //gridSize: 30,
			  gridSize: 25,
			  styles: styles
			});
		
		}
	}

	
	function setMarkers_checkin(map, locations) {
	  var image = new google.maps.MarkerImage('<?php echo base_url("assets/images/icon_map_checkin.png"); ?>',
		  // This marker is 20 pixels wide by 32 pixels tall.
		  new google.maps.Size(32, 37),
		  // The origin for this image is 0,0.
		  new google.maps.Point(0,0),
		  // The anchor for this image is the base of the flagpole at 0,32.
		  new google.maps.Point(0, 37));

	  for (var i = 0; i < locations.length; i++) {
		var checkin = locations[i];
		var myLatLng = new google.maps.LatLng(checkin[1], checkin[2]);
		var html = "<div class='markerpopup' style='width: 440px; min-height:200px;'><div style='font-size:12px'>"+checkin[3]+"<\/div><\/div>";
		var marker = new google.maps.Marker({
			position: myLatLng,
			map: map,
			icon: image,
			title: checkin[0],
			zIndex: 2,
			html: html
		});
		var infowindow = new google.maps.InfoWindow();			
		google.maps.event.addListener(marker, 'click', function() {
		   infowindow.setContent(this.html);//set the content
		   infowindow.open(map,this);
	   });
		var proximo = checkins.length;
		checkins[proximo] = [checkin[0], marker];
	 }
	 
	  //google.maps.event.trigger(marker, 'click');
   }	

</script>




