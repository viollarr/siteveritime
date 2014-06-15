<script type="text/javascript">
	jQuery(document).ready(function($){
		$(".auto").autocomplete("<?php print base_url(); ?>autocomplete/usuariosAutocomplete/",
		{
		  minLength: 2,
		  scrollHeight: 220,
		  selectFirst: false
		});
		pikerIni($(".data_inicio"),$(".data_fim"));
		pikerFim($(".data_fim"),$(".data_inicio"));
		
		function removeCampo() {
			$(".removerCampo").unbind("click");
			$(".removerCampo").bind("click", function () {
				i=0;
				$("#filtros li.filtro").each(function () {
					i++;
				});
				if (i>1) {
					$(this).parent().remove();
				}
			});
		}
		removeCampo();
		$("#Add").bind("click", function () {
			novoCampo = $("#filtros li.filtro:first").clone();
			
			novoCampo.children(".filtrando").children("option:selected").removeAttr("selected").end().children("option:first").attr("selected", false);
			novoCampo.children(".data_agendada").hide();
			novoCampo.children(".status").hide();
			novoCampo.children(".funcionarios").hide();
			novoCampo.find(":text").val("");
			novoCampo.find(":checkbox").attr("checked", false);
			novoCampo.find(".data_inicio").removeAttr("id");
			novoCampo.find(".data_inicio").removeClass("hasDatepicker");
			novoCampo.find(".data_fim").removeAttr("id");
			novoCampo.find(".data_fim").removeClass("hasDatepicker");
			novoCampo.insertAfter("#filtros li.filtro:last");
			pikerIni(novoCampo.find(".data_inicio"),novoCampo.find(".data_fim"));
			pikerFim(novoCampo.find(".data_fim"),novoCampo.find(".data_inicio"));
			novoCampo.find(".auto").autocomplete("<?php print base_url(); ?>autocomplete/usuariosAutocomplete/",
			{
			  minLength: 2,
			  scrollHeight: 220,
			  selectFirst: false
			});
			removeCampo();
		});

		$("#filtros").on("change", ".filtrando", function() {
            if($(this).val() == 'data_agendada'){
				$(this).parent("li").children("#auto").val("");
				$(this).parent("li").children(".funcionarios").hide();
				$(this).parent("li").find(":text").val("");
				$(this).parent("li").children(":checkbox").attr("checked", false);	
				$(this).parent("li").children(".status").hide();
				$(this).parent("li").children(".data_agendada").show();
				pikerIni($(this).parent("li").children(".data_agendada").children(".data_inicio"),$(this).parent("li").children(".data_agendada").children(".data_fim"));
				pikerFim($(this).parent("li").children(".data_agendada").children(".data_fim"),$(this).parent("li").children(".data_agendada").children(".data_inicio"));
			}
			else if($(this).val() == 'status'){
				$(this).parent("li").children("#auto").val("");
				$(this).parent("li").children(".funcionarios").hide();
				$(this).parent("li").find(":text").val("");
				$(this).parent("li").children(":checkbox").attr("checked", false);
				$(this).parent("li").children("#data_fim").val("");
				$(this).parent("li").children(".data_agendada").hide();
				//$(this).parent("li").children(".status").removeAttr("style");
				$(this).parent("li").children(".status").show();
			}
			else if($(this).val() == 'nome'){
				$(this).parent("li").find(":text").val("");
				$(this).parent("li").children(".data_agendada").hide();
				$(this).parent("li").children(":checkbox").attr("checked", false);		
				$(this).parent("li").children(".status").hide();
				$(this).parent("li").children(".funcionarios").show();
				
				$(this).parent("li").children(".funcionarios").children(".auto").autocomplete("<?php print base_url(); ?>autocomplete/usuariosAutocomplete/",
				{
				  minLength: 2,
				  scrollHeight: 220,
				  selectFirst: false
				});
			
			}
			else{
				$(this).parent("li").children("#auto").val("");
				$(this).parent("li").children(".funcionarios").hide();
				$(this).parent("li").children(":checkbox").attr("checked", false);	
				$(this).parent("li").children(".status").hide();
				$(this).parent("li").find(":text").val("");
				$(this).parent("li").children(".data_agendada").hide();
			}
        });
		
		
	<?php
		if(!empty($preenchimento)){
			foreach($preenchimento AS $key => $campos){
	?>
			var key			= <?php echo $key; ?>;
			var filtro 		= '<?php echo $campos['filtro'];?>';
			
			var finalizado 		= '<?php echo (!empty($campos['finalizado']))? $campos['finalizado']: '' ;?>';
			var nao_concluido 		= '<?php echo (!empty($campos['nao_concluido']))? $campos['nao_concluido']: '' ;?>';
			var em_andamento 		= '<?php echo (!empty($campos['em_andamento']))? $campos['em_andamento']: '' ;?>';
			var em_espera 		= '<?php echo (!empty($campos['em_espera']))? $campos['em_espera']: '' ;?>';
			var em_atraso 		= '<?php echo (!empty($campos['em_atraso']))? $campos['em_atraso']: '' ;?>';
			
			var data_inicio	= '<?php echo (!empty($campos['data_inicio']))? $campos['data_inicio']: '' ;?>';
			var data_fim	= '<?php echo (!empty($campos['data_final']))? $campos['data_final']: '' ;?>';
			var nome		= '<?php echo (!empty($campos['nome']))? $campos['nome']: '' ;?>';
			
			if(key < 1){
				if(filtro == 'status'){
					$(".filtrando option[value='"+filtro+"']").attr("selected", true).trigger("change");
					if(finalizado != ''){
						$(".finalizado").attr("checked", true);	
					}
					if(nao_concluido != ''){
						$(".nao_concluido").attr("checked", true);	
					}
					if(em_andamento != ''){
						$(".em_andamento").attr("checked", true);	
					}
					if(em_espera != ''){
						$(".em_espera").attr("checked", true);	
					}
					if(em_atraso != ''){
						$(".em_atraso").attr("checked", true);	
					}										
				}
				else if(filtro == 'data_agendada'){
					$(".filtrando option[value='"+filtro+"']").attr("selected", true).trigger("change");
					if(data_inicio != ''){
						$(".data_inicio").val(data_inicio);	
					}
					if(data_fim != ''){
						$(".data_fim").val(data_fim);	
					}
				}
				else if(filtro == 'nome'){
					$(".filtrando option[value='"+filtro+"']").attr("selected", true).trigger("change");
					if(nome != ''){
						$(".auto").val(nome);	
					}
				}
				
			}
			else{
				$("#Add").trigger("click");
				var novo = $("li.filtro:last");
				if(filtro == 'status'){
					novo.children(".filtrando").find("option[value='"+filtro+"']").attr("selected", true).trigger("change");
					if(finalizado != ''){
						//novo.children(".status").children(".con").attr("checked", true);	
						novo.find(".status").find(".finalizado").attr("checked", true);	
					}
					if(nao_concluido != ''){
						novo.find(".status").find(".nao_concluido").attr("checked", true);	
					}
					if(em_andamento != ''){
						novo.find(".status").find(".em_andamento").attr("checked", true);	
					}
					if(em_espera != ''){
						novo.find(".status").find(".em_espera").attr("checked", true);	
					}
					if(em_atraso != ''){
						novo.find(".status").find(".em_atraso").attr("checked", true);	
					}										
				}
				else if(filtro == 'data_agendada'){
					novo.children(".filtrando").find("option[value='"+filtro+"']").attr("selected", true).trigger("change");
					if(data_inicio != ''){
						novo.find(".data_agendada").find(".data_inicio").val(data_inicio)
						novo.find(".data_agendada").show();	
					}
					if(data_fim != ''){
						novo.find(".data_agendada").find(".data_fim").val(data_fim)
						novo.find(".data_agendada").removeAttr("style");
					}
				}
				else if(filtro == 'nome'){
					novo.children(".filtrando").find("option[value='"+filtro+"']").attr("selected", true).trigger("change");
					if(nome != ''){
						novo.find(".funcionarios").find(".auto").val(nome);
					}
				}
			}
	<?php
			}
		}
	?>	
		
		$("#download").click(function(){
			if(($("#tipo").val() == 'xls') || ($("#tipo").val() == 'pdf')){
				$("#form_download").attr("action", "<?php echo base_url('relatorio/download'); ?>");
				for(var t = 0; t < $("#form_filtros input[type='hidden']").length ; t++){
					
					if($("#form_filtros input[type='hidden']")[t].value == "data_agendada"){
						$("#form_download").append("<input type='hidden' name = 'data' value='"+$("#form_filtros input[type='hidden']")[t].value+"' />");
					}
					if($("#form_filtros input[type='hidden']")[t].value == "hora_agendada"){
						$("#form_download").append("<input type='hidden' name = 'hora' value='"+$("#form_filtros input[type='hidden']")[t].value+"' />");
					}
					if($("#form_filtros input[type='hidden']")[t].value == "tempo_estimado"){
						$("#form_download").append("<input type='hidden' name = 'tempo' value='"+$("#form_filtros input[type='hidden']")[t].value+"' />");
					}
					if($("#form_filtros input[type='hidden']")[t].value == "prioridade"){
						$("#form_download").append("<input type='hidden' name = 'prioridade' value='"+$("#form_filtros input[type='hidden']")[t].value+"' />");
					}
				}
				$("#conteudo").val($("#conteudo_download").html());
				$("#form_download").submit();
				$("#conteudo").val("");
			}
			else{
				alert('Selecione uma opção para o download.')	
			}
		});


		$("#resetar").click(function(){
			$("#form_filtros").removeAttr("action");
			$("#form_filtros").attr("action", "<?php echo base_url('relatorio/relatorios/false/atendimento'); ?>");
			$("#form_filtros").submit();
		});
		
		$("#filtrar").click(function(){
			if($(".filtrando").val() == 'data_agendada'){
				if(($(".data_inicio").val() != "")&&($(".data_fim").val() != "")){
					$("#form_filtros").submit();
				}
				else{
					if(($(".data_inicio").val() == "")&&($(".data_fim").val() != "")){
						alert("Você esqueceu de preencher o campo de data inicial.");
					}
					else if(($(".data_fim").val() == "")&&($(".data_inicio").val() != "")){
						alert("Você esqueceu de preencher o campo de data final.");	
					}
					else{
						alert("Você deve preencher os campos de data inicial e final para proseguir com o filtro.");	
					}
				}
			}
			else if($(".filtrando").val() == 'status'){
				if( ($(".finalizado").is(":checked"))||($(".nao_concluido").is(":checked"))||($(".em_andamento").is(":checked")) ||($(".em_espera").is(":checked")) ||($(".em_atraso").is(":checked")) ){
					$("#form_filtros").submit();
				}
				else{
					alert("Você deve marcar pelo menos uma das opções de status.");	
				}
			}
			else if($(".filtrando").val() == 'nome'){
				if($(".auto").val()!= ""){
					$("#form_filtros").submit();
				}
				else{
					alert("Você deve informar um nome de usuário válido para prosseguir");	
				}
			}
			else{
				alert("Você deve selecionar ao menos um filtro para prosseguir.");	
			}
		});
				
		$("th").css("cursor","pointer");
		$("#ordenar").tablesorter({
			dateFormat: 'uk' // para poder ordenar a data no formato dd/mm/yyyy
		});
	});
	
	
	function pikerIni(i,f){
		i.datepicker({
			onSelect: function( selectedDate ) {
				f.datepicker( "option", "minDate", selectedDate );
			}
		});
	}
	function pikerFim(f,i){
		f.datepicker({
			onSelect: function( selectedDate ) {
				i.datepicker( "option", "maxDate", selectedDate );
			}
		});
	}
</script>

<form method="post" id="form_download" target="_self">
	<!--<select name="tipo_download" id="tipo">
		<option>Selecione</option>
		<option value="xls">XLS</option>
		<option value="pdf">PDF</option>
	</select>-->
	<input type="hidden" name="tipo_download" id="tipo" value="xls" />
	<input type="hidden" name="conteudo" id="conteudo" value="" />

</form>
<fieldset>
<legend>Incluir filtros</legend>
<form action="<?php echo base_url('relatorio/relatorios/true/atendimento'); ?>" method="post" id="form_filtros">
	<?php
		if(!empty($data_agendada))echo '<input type="hidden" name="data" value="data_agendada" />';
		if(!empty($hora_agendada))echo '<input type="hidden" name="hora" value="hora_agendada" />';
		if(!empty($tempo_estimado))echo '<input type="hidden" name="tempo" value="tempo_estimado" />';
		if(!empty($prioridade))echo '<input type="hidden" name="prioridade" value="prioridade" />';
	?>
	<ul id="filtros">
		<li class="filtro">
			<select name="filtro[]" class="filtrando">
				<option selected="selected">Selecione</option>
			<?php
				foreach($filtros AS $filtro){
					if($filtro == "Data")echo "<option value='data_agendada'>".$filtro."</option>";
					elseif($filtro == "Status")echo "<option value='status'>".$filtro."</option>";
					elseif($filtro == "Funcionário")echo "<option value='nome'>".$filtro."</option>";
					else echo "<option value='".$filtro."'>".$filtro."</option>";
				}
			?>
			</select>
			<span class="data_agendada" style="display:none;">
				<div style="margin:0 15px; display:inline-block;">
					De: <input name="data_inicio[]" class="data data_inicio input-small" type="text" value="" />&nbsp;&nbsp;
					Até: <input name="data_fim[]" class="data data_fim input-small"  type="text" value="" />
				</div>
			</span>
			<span class = "status" style=" display:none;">
				<div style="margin:0 15px; display:inline-block;">
					<input type="checkbox" name="nao_concluido[]" class="nao_concluido" value="nao_concluido" style="vertical-align:top;" /> Não Concluído&nbsp;&nbsp;
					<input type="checkbox" name="em_andamento[]" class="em_andamento" value="em_andamento" style="vertical-align:top;" /> Em Andamento&nbsp;&nbsp;
					<input type="checkbox" name="finalizado[]" class="finalizado" value="finalizado" style="vertical-align:top;" /> Finalizado&nbsp;&nbsp;
					<input type="checkbox" name="em_espera[]" class="em_espera" value="em_espera" style="vertical-align:top;" /> Em Espera&nbsp;&nbsp;
					<input type="checkbox" name="em_atraso[]" class="em_atraso" value="em_atraso" style="vertical-align:top;" /> Em Atraso&nbsp;&nbsp;
				</div>    
			</span>
			<span class = "funcionarios" style=" display:none;">
				<div style="margin:0 15px; display:inline-block;">                
					Nome do funcionário: <input type="text" name="q[]" class="auto" size="40" autocomplete="off" />
				</div>
			</span>
			<button class="removerCampo btn btn-small btn-danger" type="button" style="vertical-align: top;"><i class="icon-remove icon-white"></i> Remover</button>
		</li>
		<li>
			<span class="buttons"><button id="Add" class="btn btn-small btn-success" type="button"><i class="icon-plus icon-white"></i> Adicionar novo</button></span>
		</li>
   </ul>
   <ul>
		<li>&nbsp;</li>
		<li>
			<button id="filtrar" class="btn btn-medium btn-success" type="button"> Confirmar filtros</button>            
			<button id="resetar" class="btn btn-medium btn-warning" type="button"> Remover todos os filtros</button>  
			<button class="btn btn-inverse btn-medium" style="float: right;" type="button" id="download">Fazer download para XLS</button>			
		</li>
	</ul>
</form>
</fieldset>

<?php
$dow_data = "";
$dow_hora = "";
$dow_prioridade = "";
$dow_tempo = "";
$nome_tabela = "";
$down_nome_tabela = "";
$nome = "";
if(!empty($atendimentos[0]->usuario)){
$nome_tabela = "<th>Funcionário</th>";
$down_nome_tabela = "<td>Funcionário</td>";
}

?>
<table id="ordenar" class="table table-condensed tablesorter">
	<thead>
		<tr>
			<th>ID</th>
			<th>Cliente</th>
			<th>Atendimento</th>
			<th>Endereço</th>
			<th>Bairro</th>
		<?php
			if($data){
				echo "<th class='dateFormat'>Data</th>";
				$dow_data = "<td class='dateFormat'>Data</td>";
			}
			if($hora){
				echo "<th>Hora</th>";
				$dow_hora = "<td>Hora</td>";
			
			}
			if($tempo){
				echo "<th>Tempo Estimado</th>";
				$dow_tempo = "<td>Tempo Estimado</td>";;
			}
			
			echo "<th>Duração</th>";
			$dow_duracao = "<td>Duração</td>";;
			
			if($prioridade){
				echo "<th>Prioridade</th>";
				$dow_prioridade = "<td>Prioridade</td>";
			}
		?>	
			<th>Status</th>
		<?php echo $nome_tabela; ?>
			<th style="width:80px; background-image: none;">Informações</th>
		</tr>
	</thead>
	<tbody>
<?php
$html_donwload = '	<table id="download_tabela" class="table table-condensed">
		<tr>
			<td>OS</td>
			<td>Cliente</td>
			<td>Título</td>
			<td>Endereço</td>
			<td>Nº</td>
			<td>Complemento</td>
			<td>Bairro</td>
			<td>Cidade</td>
			<td>Estado</td>
		'.
			$dow_data.
			$dow_hora.
			$dow_tempo.
			$dow_duracao.
			$dow_prioridade		
		.'	
			<td>Status</td>'.
			$down_nome_tabela
		.'</tr>
';

foreach($atendimentos as $atendimento){

	//$array_banco = array("/_/", "/concluido/", "/nao/", "/parcialmente/", "/sim/");
	//$array_texto = array(" ", "Concluído", "Não", "Parcialmente", "Sim");


	//'em_andamento','em_espera','em_atraso','nao_concluido','finalizado'
	/***************************************
	ATENÇÃO: Os status agora passarão a ser 5
	Em Andamento - Verde : success
	Em espera - Azul : info
	Em Atraso - Vermelho : important
	Não Concluído - Laranja : warning
	Finalizado / concluído - Cinza : ''
	***************************************/
	if ($atendimento->status == 'em_andamento') {
		$estilo_status = 'success'; $estilo_status_tr = 'success';
		$alt_status = 'Em Andamento'; 
		$color_linha_excel = '';
	} else if ($atendimento->status == 'em_espera') {	
		$estilo_status = 'info';  $estilo_status_tr = 'info';
		$alt_status = 'Em Espera'; 
		$color_linha_excel = '';
	} else if ($atendimento->status == 'em_atraso') {
		$estilo_status = 'important'; $estilo_status_tr = 'error'; 
		$alt_status = 'Em Atraso'; 
		$color_linha_excel = '';
	} else if ($atendimento->status == 'nao_concluido') {
		$estilo_status = 'warning'; $estilo_status_tr = 'warning';
		$alt_status = 'Não Concluído'; 
		$color_linha_excel = '';
	}else{ // finalizado
		$estilo_status = 'padrao';  $estilo_status_tr = 'padrao';
		$alt_status = 'Finalizado'; 
		$color_linha_excel = '';
	}
		
	echo "<tr class='".$estilo_status_tr."'>";
		echo "<td>".$atendimento->idatendimento."</td>";
		echo "<td>".$atendimento->nome."</td>";
		echo "<td>".$atendimento->titulo."</td>";
		
		$endereco_numero='';
		$endereco_complemento='';
		if($atendimento->endereco_numero){
			$endereco_numero=', '.$atendimento->endereco_numero;
		}
		if($atendimento->endereco_complemento){
			$endereco_complemento=', '.$atendimento->endereco_complemento;
		}	
		
		
		echo "<td>".$atendimento->endereco."".$endereco_numero."".$endereco_complemento."</td>";
		
		$bairro='';
		if($atendimento->bairro){
			$bairro=$atendimento->bairro;
		}			
		echo "<td>".$bairro."</td>";
		
		if($data){
			echo "<td>".fdata($atendimento->data_agendada, "/")."</td>";
			$dow_data = "<td>".fdata($atendimento->data_agendada, "/")."</td>";
		}
		if($hora){

			$hora = explode(":", $atendimento->hora_agendada);
			$hora_agendada = $hora[0].':'.$hora[1].'h';

			
			echo "<td>".$hora_agendada."</td>";
			$dow_hora = "<td>".$hora_agendada."</td>";
		}
		if($tempo){

			$tempo = explode(":", $atendimento->tempo_estimado);
			$tempo_estimado = $tempo[0].':'.$tempo[1].'h';
			
			echo "<td>".$tempo_estimado."</td>";
			$dow_tempo = "<td>".$tempo_estimado."</td>";
		}
		
		echo "<td>".$atendimento->duracao."</td>";
		$dow_duracao = "<td>".$atendimento->duracao."</td>";
		
		if($prioridade){
			
			if ($atendimento->prioridade == 'nao'){ 
				$info_prioridade = '<i class="icon-star icon-white"></i>';
				$info_prioridade_excel = 'Não';
			}else{
				$info_prioridade = '<i class="icon-star"></i>';
				$info_prioridade_excel = 'Sim';
				
			}
			
			echo "<td>".$info_prioridade."</td>";
			$dow_prioridade = "<td>".$info_prioridade_excel."</td>";
		}
		
		//$status = preg_replace($array_banco, $array_texto, $atendimento->status);
		
		echo "<td><span class='label label-".$estilo_status."'>".$alt_status."</span></td>";
		if(!empty($atendimento->usuario)){
			echo "<td>".$atendimento->usuario."</td>";
			$nome = "<td>".$atendimento->usuario."</td>";
		}
		echo "<td style='text-align:center;'><a class='modal_info' href='".base_url('atendimento/info_atendimento/'.$atendimento->idatendimento)."'><button class='btn btn-mini btn-primary' type='button'>Mais info</button></a></td>";
	echo "<tr>";

	
	
	
	$html_donwload .=  "<tr class='".$atendimento->status."' style='background-color: ".$color_linha_excel.";'>".
		"<td>".$atendimento->idatendimento."</td>".
		"<td>".$atendimento->nome."</td>".
		"<td>".$atendimento->titulo."</td>".
		"<td>".$atendimento->endereco."</td>".
		"<td>".$atendimento->endereco_numero."</td>".
		"<td style='text-align:right'>".$atendimento->endereco_complemento."</td>".
		"<td>".$atendimento->bairro."</td>".
		"<td>".$atendimento->cidade."</td>".
		"<td>".$atendimento->estado."</td>".
		$dow_data.
		$dow_hora.
		$dow_tempo.
		$dow_duracao.
		$dow_prioridade.			
		"<td>".$alt_status."</td>".
		$nome.
		"<tr>";
	
}
	$html_donwload .="</table>";
?>
	</tbody>
</table>
<span id="conteudo_download" style="display:none;">
	<?php echo $html_donwload; ?>
</span>
<script>
jQuery(document).ready(function($){ 
	$(".modal_info").colorbox({iframe:true, width:"60%", height:"90%"});
}); 
</script>