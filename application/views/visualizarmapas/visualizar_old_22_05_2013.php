<script type="text/javascript">
jQuery(document).ready(function($){
// ADD Funcionário

	$("#auto").autocomplete("<?php print base_url(); ?>autocomplete/usuariosAutocomplete/",
	{
	  minLength: 2,
	  scrollHeight: 220,
	  selectFirst: false
	});
	
	$("#incluir_funcionario").bind("click",function(){
		if(($("#auto").val() != "") && ($("#auto").val() != "Usuário não encontrado")){
			var i = 0;
			$("#sidebar_map li").each(function(index) {
				if($(this).text() == $("#auto").val()){
					$(this).trigger("click");
				}
				else{
					i++;
				}
            });
			consultar(i);
		}
		else if($("#auto").val() == "Usuário não encontrado"){
			alert("Por favor selecione um nome de funcionário válido.");
		}
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
	$(document).ready(function() {
		  var myOptions = {
			zoom: 10,
			center: new google.maps.LatLng(-22.884051, -43.415130),
			mapTypeId: google.maps.MapTypeId.ROADMAP
		  }
		  var map = new google.maps.Map(document.getElementById("map-canvas"), myOptions);
		  
		  <?php
		  if(!empty($atendimentos)){
		  ?>
			  var atendimentos = [<?php echo implode(", ", $atendimentos); ?>];  
			  setMarkers_atendimentos(map, atendimentos, "nao");
		  <?php
		  }
		  if(!empty($atendimentos_checkout)){
		  ?>
			  var atendimentos_checkout = [<?php echo implode(", ", $atendimentos_checkout); ?>];	
			  setMarkers_atendimentos(map, atendimentos_checkout, "sim");
		  <?php
		  }
		  if(!empty($funcionario_checkin)){
		  ?>
			  var checkin = [<?php echo implode(", ", $funcionario_checkin); ?>];	
			  setMarkers_checkin(map, checkin);
		  <?php
		  }
		  ?>
	});
	
		  
	
	function setMarkers_atendimentos(map, locations, checkout) {
		var endereco_imagem = "";
		if(checkout == "sim"){
			endereco_imagem = "<?php echo base_url("assets/images/icon_map_checkout.png"); ?>";
		}else{
			endereco_imagem = "<?php echo base_url("assets/images/icon_map.png"); ?>";
		}
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
		var marker = new google.maps.Marker({
			position: myLatLng,
			map: map,
			icon: image,
			title: atendimento[0],
			zIndex: 1
		});
	  }
	}	
	
	function setMarkers_checkin(map, locations) {
	  var image = new google.maps.MarkerImage('http://www.veritime.com.br/admin/assets/images/icon_map_checkin.png',
		  // This marker is 20 pixels wide by 32 pixels tall.
		  new google.maps.Size(32, 37),
		  // The origin for this image is 0,0.
		  new google.maps.Point(0,0),
		  // The anchor for this image is the base of the flagpole at 0,32.
		  new google.maps.Point(0, 37));

	  for (var i = 0; i < locations.length; i++) {
		var checkin = locations[i];
		var myLatLng = new google.maps.LatLng(checkin[1], checkin[2]);
		var marker = new google.maps.Marker({
			position: myLatLng,
			map: map,
			icon: image,
			title: checkin[0],
			zIndex: 2,
			html: "<div class='MarkerPopUp' style='width: 300px; min-height:200px;'><div style='font-size:12px'>"+checkin[3]+"<\/div><\/div>"
		});
		var infowindow = new google.maps.InfoWindow();			
		google.maps.event.addListener(marker, 'click', function() {
		   infowindow.setContent(this.html);//set the content
		   infowindow.open(map,this);
	   });
	 }
	  
   }		
</script>




