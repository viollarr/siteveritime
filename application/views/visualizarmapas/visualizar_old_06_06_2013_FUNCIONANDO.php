<script type="text/javascript">
jQuery(document).ready(function($){
// ADD Funcionário

	$("#auto").autocomplete("<?php print base_url(); ?>autocomplete/usuariosAutocomplete/",
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
    <a href="<?php echo base_url('atendimento/cadastro'); ?>" class="btn btn-success btn-large"><i class="icon-plus icon-white"></i> Adicionar Atendimentos</a>
    <a href="<?php echo base_url('atendimento'); ?>" class="btn btn-large btn-inverse"><i class="icon-align-justify icon-white"></i> Listar Atendimentos</a>
</div>                

<div class="pesquisa-mapa pull-left">
	<input type="text" name="q" id="auto" size="40" autocomplete="off" placeholder="Insira o Nome do Funcionário" class="input-xlarge" /> 
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
<p>Obs: No mapa acima são sinalizados somente os atendimentos do dia e a última ação de cada funcionário (checkin ou checkout)</p>
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
		  
		  <?php
		  if(!empty($atendimentos)){
			if(!empty($atendimentos->em_espera)){
		  ?>
			  var em_espera = [<?php echo implode(", ", $atendimentos->em_espera); ?>];  
			  var endereco_imagem = "<?php echo base_url("assets/images/icon_map_espera.png"); ?>";
			  setMarkers_atendimentos(map, em_espera, endereco_imagem);
		  <?php
			}
			if(!empty($atendimentos->em_andamento)){
		  ?>
			  var em_andamento = [<?php echo implode(", ", $atendimentos->em_andamento); ?>];  
			  var endereco_imagem = "<?php echo base_url("assets/images/icon_map_andamento.png"); ?>";
			  setMarkers_atendimentos(map, em_andamento, endereco_imagem);
		  <?php
			}
			if(!empty($atendimentos->em_atraso)){
		  ?>
			  var em_atraso = [<?php echo implode(", ", $atendimentos->em_atraso); ?>]; 
			  var endereco_imagem = "<?php echo base_url("assets/images/icon_map_atraso.png"); ?>";			  
			  setMarkers_atendimentos(map, em_atraso, endereco_imagem);
		  <?php
			}
			if(!empty($atendimentos->nao_concluido)){
		  ?>
			  var nao_concluido = [<?php echo implode(", ", $atendimentos->nao_concluido); ?>];  
			  var endereco_imagem = "<?php echo base_url("assets/images/icon_map_nao_concluido.png"); ?>";	
			  setMarkers_atendimentos(map, nao_concluido, endereco_imagem);
		  <?php
			}
			if(!empty($atendimentos->finalizado)){
		  ?>
			  var finalizado = [<?php echo implode(", ", $atendimentos->finalizado); ?>]; 
			  var endereco_imagem = "<?php echo base_url("assets/images/icon_map_finalizado.png"); ?>";
			  setMarkers_atendimentos(map, finalizado, endereco_imagem);
		  <?php
			}
		  }
		  if(!empty($funcionario_checkin)){
		  ?>
			  var checkin = [<?php echo implode(", ", $funcionario_checkin); ?>];	
			  setMarkers_checkin(map, checkin);
		  <?php
		  }
		  ?>
		   markerClusterer = new MarkerClusterer(map, markers, {
			  maxZoom: null,
			  gridSize: 30,
			  styles: styles
			});
		  
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
						alert("Nenhum funcionário encontrado.");
					}
					 
				}
				else if($("#auto").val() == "Usuário não encontrado"){
					alert("Por favor selecione um nome de funcionário válido.");
				}
				
			});
		  
	});
	
		  
	var styles = [{
        url: '<?php echo base_url('assets/js/MarkerClusterer/images/m1.png'); ?>',
        height: 52,
        width: 53,
        anchor: [16, 0],
        textColor: '#ff00ff',
        textSize: 10
      }];
	  
	var markers = [];
	function setMarkers_atendimentos(map, locations, endereco_imagem) {

	  var markerClusterer = null;
	  
	  var image = new google.maps.MarkerImage(endereco_imagem,
		  // This marker is 20 pixels wide by 32 pixels tall.
		  new google.maps.Size(32, 37),
		  // The origin for this image is 0,0.
		  new google.maps.Point(0,0),
		  // The anchor for this image is the base of the flagpole at 0,32.
		  new google.maps.Point(0, 32));

	  for (var i = 0; i < locations.length; i++) {
		var atendimento = locations[i];
		var myLatLng = new google.maps.LatLng(atendimento[1], atendimento[2]);
		var html = "<div class='markerpopup' style='width: 300px; min-height:200px;'><div style='font-size:12px'>"+atendimento[3]+"<\/div><\/div>";
		//var html = checkin[3];
		var marker = new google.maps.Marker({
			position: myLatLng,
			map: map,
			icon: image,
			title: atendimento[0],
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
	  
	}	
	var checkins = [];
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
		var html = "<div class='markerpopup' style='width: 300px; min-height:200px;'><div style='font-size:12px'>"+checkin[3]+"<\/div><\/div>";
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
		checkins[i] = [checkin[0], marker];
	 }
	  //google.maps.event.trigger(marker, 'click');
   }	

</script>




