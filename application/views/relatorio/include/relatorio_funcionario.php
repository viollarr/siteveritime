<script type="text/javascript">
	jQuery(document).ready(function($){
		$(".auto2").autocomplete("<?php print base_url(); ?>autocomplete/usuariosAutocomplete/",
		{
		  minLength: 2,
		  scrollHeight: 220,
		  selectFirst: false
		});
		pikerIni2($(".data_inicio2"),$(".data_fim2"));
		pikerFim2($(".data_fim2"),$(".data_inicio2"));
		
		function removeCampo() {
			$(".removerCampo2").unbind("click");
			$(".removerCampo2").bind("click", function () {
				i=0;
				$("#filtros2 li.filtro2").each(function () {
					i++;
				});
				if (i>1) {
					$(this).parent().remove();
				}
			});
		}
		removeCampo();
		$("#Add2").bind("click", function () {
			novoCampo = $("#filtros2 li.filtro2:first").clone();
			
			novoCampo.children(".filtrando2").children("option:selected").removeAttr("selected").end().children("option:first").attr("selected", false);
			novoCampo.children(".data_agendada2").hide();
			novoCampo.children(".status2").hide();
			novoCampo.children(".funcionarios2").hide();
			novoCampo.find(":text").val("");
			novoCampo.find(":checkbox").attr("checked", false);
			novoCampo.find(".data_inicio2").removeAttr("id");
			novoCampo.find(".data_inicio2").removeClass("hasDatepicker");
			novoCampo.find(".data_fim2").removeAttr("id");
			novoCampo.find(".data_fim2").removeClass("hasDatepicker");
			novoCampo.insertAfter("#filtros2 li.filtro2:last");
			pikerIni2(novoCampo.find(".data_inicio2"),novoCampo.find(".data_fim2"));
			pikerFim2(novoCampo.find(".data_fim2"),novoCampo.find(".data_inicio2"));
			novoCampo.find(".auto2").autocomplete("<?php print base_url(); ?>autocomplete/usuariosAutocomplete/",
			{
			  minLength: 2,
			  scrollHeight: 220,
			  selectFirst: false
			});
			removeCampo();
		});

		$("#filtros2").on("change", ".filtrando2", function() {
            if($(this).val() == 'data_agendada'){
				$(this).parent("li").children(".auto2").val("");
				$(this).parent("li").children(".funcionarios2").hide();
				$(this).parent("li").find(":text").val("");
				$(this).parent("li").children(":checkbox").attr("checked", false);	
				$(this).parent("li").children(".status2").hide();
				$(this).parent("li").children(".data_agendada2").show();
				pikerIni2($(this).parent("li").children(".data_agendada2").children(".data_inicio2"),$(this).parent("li").children(".data_agendada2").children(".data_fim2"));
				pikerFim2($(this).parent("li").children(".data_agendada2").children(".data_fim2"),$(this).parent("li").children(".data_agendada2").children(".data_inicio2"));
			}
			else if($(this).val() == 'status'){
				$(this).parent("li").children(".auto2").val("");
				$(this).parent("li").children(".funcionarios2").hide();
				$(this).parent("li").find(":text").val("");
				$(this).parent("li").children(":checkbox").attr("checked", false);
				$(this).parent("li").children("#data_fim2").val("");
				$(this).parent("li").children(".data_agendada2").hide();
				//$(this).parent("li").children(".status").removeAttr("style");
				$(this).parent("li").children(".status2").show();
			}
			else if($(this).val() == 'nome'){
				$(this).parent("li").find(":text").val("");
				$(this).parent("li").children(".data_agendada2").hide();
				$(this).parent("li").children(":checkbox").attr("checked", false);		
				$(this).parent("li").children(".status2").hide();
				$(this).parent("li").children(".funcionarios2").show();
				
				$(this).parent("li").children(".funcionarios2").children(".auto2").autocomplete("<?php print base_url(); ?>autocomplete/usuariosAutocomplete/",
				{
				  minLength: 2,
				  scrollHeight: 220,
				  selectFirst: false
				});
			
			}
			else{
				$(this).parent("li").children(".auto2").val("");
				$(this).parent("li").children(".funcionarios2").hide();
				$(this).parent("li").children(":checkbox").attr("checked", false);	
				$(this).parent("li").children(".status2").hide();
				$(this).parent("li").find(":text").val("");
				$(this).parent("li").children(".data_agendada2").hide();
			}
        });
		
		
	<?php
		if(!empty($preenchimento2)){
			foreach($preenchimento2 AS $key => $campos){
	?>
			var key2			= <?php echo $key; ?>;
			var filtro2 		= '<?php echo $campos['filtro2'];?>';
			
			var finalizado2 		= '<?php echo (!empty($campos['finalizado2']))? $campos['finalizado2']: '' ;?>';
			var nao_concluido2 		= '<?php echo (!empty($campos['nao_concluido2']))? $campos['nao_concluido2']: '' ;?>';
			var em_andamento2 		= '<?php echo (!empty($campos['em_andamento2']))? $campos['em_andamento2']: '' ;?>';
			var em_espera2 		= '<?php echo (!empty($campos['em_espera2']))? $campos['em_espera2']: '' ;?>';
			var em_atraso2 		= '<?php echo (!empty($campos['em_atraso2']))? $campos['em_atraso2']: '' ;?>';
			
			var data_inicio2	= '<?php echo (!empty($campos['data_inicio2']))? $campos['data_inicio2']: '' ;?>';
			var data_fim2	= '<?php echo (!empty($campos['data_final2']))? $campos['data_final2']: '' ;?>';
			var nome2		= '<?php echo (!empty($campos['nome2']))? $campos['nome2']: '' ;?>';
			
			if(key2 < 1){
				if(filtro2 == 'status'){
					$(".filtrando2 option[value='"+filtro2+"']").attr("selected", true).trigger("change");
					if(finalizado2 != ''){
						$(".finalizado2").attr("checked", true);	
					}
					if(nao_concluido2 != ''){
						$(".nao_concluido2").attr("checked", true);	
					}
					if(em_andamento2 != ''){
						$(".em_andamento2").attr("checked", true);	
					}
					if(em_espera2 != ''){
						$(".em_espera2").attr("checked", true);	
					}
					if(em_atraso2 != ''){
						$(".em_atraso2").attr("checked", true);	
					}										
				}
				else if(filtro2 == 'data_agendada'){
					$(".filtrando2 option[value='"+filtro2+"']").attr("selected", true).trigger("change");
					if(data_inicio2 != ''){
						$(".data_inicio2").val(data_inicio2);	
					}
					if(data_fim2 != ''){
						$(".data_fim2").val(data_fim2);	
					}
				}
				else if(filtro2 == 'nome'){
					$(".filtrando2 option[value='"+filtro2+"']").attr("selected", true).trigger("change");
					if(nome2 != ''){
						$(".auto2").val(nome2);	
					}
				}
				
			}
			else{
				$("#Add2").trigger("click");
				var novo2 = $("li.filtro2:last");
				if(filtro2 == 'status'){
					novo2.children(".filtrando2").find("option[value='"+filtro2+"']").attr("selected", true).trigger("change");
					if(finalizado2 != ''){
						//novo.children(".status").children(".con").attr("checked", true);	
						novo2.find(".status2").find(".finalizado2").attr("checked", true);	
					}
					if(nao_concluido2 != ''){
						novo2.find(".status2").find(".nao_concluido2").attr("checked", true);	
					}
					if(em_andamento2 != ''){
						novo2.find(".status2").find(".em_andamento2").attr("checked", true);	
					}
					if(em_espera2 != ''){
						novo2.find(".status2").find(".em_espera2").attr("checked", true);	
					}
					if(em_atraso2 != ''){
						novo2.find(".status2").find(".em_atraso2").attr("checked", true);	
					}										
				}
				else if(filtro2 == 'data_agendada'){
					novo2.children(".filtrando2").find("option[value='"+filtro2+"']").attr("selected", true).trigger("change");
					if(data_inicio2 != ''){
						novo2.find(".data_agendada2").find(".data_inicio2").val(data_inicio2)
						novo2.find(".data_agendada2").show();	
					}
					if(data_fim2 != ''){
						novo2.find(".data_agendada2").find(".data_fim2").val(data_fim2)
						novo2.find(".data_agendada2").removeAttr("style");
					}
				}
				else if(filtro2 == 'nome'){
					novo2.children(".filtrando2").find("option[value='"+filtro2+"']").attr("selected", true).trigger("change");
					if(nome2 != ''){
						novo2.find(".funcionarios2").find(".auto2").val(nome2);
					}
				}
			}
	<?php
			}
		}
	?>	
		
		$("#download2").click(function(){
			if(($("#tipo2").val() == 'xls') || ($("#tipo2").val() == 'pdf')){
				$("#form_download2").attr("action", "<?php echo base_url('relatorio/download2'); ?>");
				for(var t = 0; t < $("#form_filtros2 input[type='hidden']").length ; t++){
					
					if($("#form_filtros2 input[type='hidden']")[t].value == "data_agendada"){
						$("#form_download2").append("<input type='hidden' name = 'data2' value='"+$("#form_filtros2 input[type='hidden']")[t].value+"' />");
					}
					if($("#form_filtros2 input[type='hidden']")[t].value == "hora_agendada"){
						$("#form_download2").append("<input type='hidden' name = 'hora2' value='"+$("#form_filtros2 input[type='hidden']")[t].value+"' />");
					}
					if($("#form_filtros2 input[type='hidden']")[t].value == "tempo_estimado"){
						$("#form_download2").append("<input type='hidden' name = 'tempo2' value='"+$("#form_filtros2 input[type='hidden']")[t].value+"' />");
					}
					if($("#form_filtros2 input[type='hidden']")[t].value == "prioridade"){
						$("#form_download2").append("<input type='hidden' name = 'prioridade2' value='"+$("#form_filtros2 input[type='hidden']")[t].value+"' />");
					}
				}
				$("#conteudo2").val($("#conteudo_download2").html());
				$("#form_download2").submit();
				$("#conteudo2").val("");
			}
			else{
				alert('Selecione uma opção para o download.')	
			}
		});


		$("#resetar2").click(function(){
			$("#form_filtros2").removeAttr("action");
			$("#form_filtros2").attr("action", "<?php echo base_url('relatorio/relatorios/false/funcionario'); ?>");
			$("#form_filtros2").submit();
		});
		
		$("#filtrar2").click(function(){
			if($(".filtrando2").val() == 'data_agendada'){
				if(($(".data_inicio2").val() != "")&&($(".data_fim2").val() != "")){
					$("#form_filtros2").submit();
				}
				else{
					if(($(".data_inicio2").val() == "")&&($(".data_fim2").val() != "")){
						alert("Você esqueceu de preencher o campo de data inicial.");
					}
					else if(($(".data_fim2").val() == "")&&($(".data_inicio2").val() != "")){
						alert("Você esqueceu de preencher o campo de data final.");	
					}
					else{
						alert("Você deve preencher os campos de data inicial e final para proseguir com o filtro.");	
					}
				}
			}
			else if($(".filtrando2").val() == 'status'){
				if( ($(".finalizado2").is(":checked"))||($(".nao_concluido2").is(":checked"))||($(".em_andamento2").is(":checked")) ||($(".em_espera2").is(":checked")) ||($(".em_atraso2").is(":checked")) ){
					$("#form_filtros2").submit();
				}
				else{
					alert("Você deve marcar pelo menos uma das opções de status.");	
				}
			}
			else if($(".filtrando2").val() == 'nome'){
				if($(".auto2").val()!= ""){
					$("#form_filtros2").submit();
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
		$("#ordenar2").tablesorter({
			dateFormat: 'uk' // para poder ordenar a data no formato dd/mm/yyyy
		});
	});
	
	
	function pikerIni2(i,f){
		i.datepicker({
			onSelect: function( selectedDate ) {
				f.datepicker( "option", "minDate", selectedDate );
			}
		});
	}
	function pikerFim2(f,i){
		f.datepicker({
			onSelect: function( selectedDate ) {
				i.datepicker( "option", "maxDate", selectedDate );
			}
		});
	}
</script>

<form method="post" id="form_download2" target="_self">
	<input type="hidden" name="tipo_download2" id="tipo2" value="xls" />
	<input type="hidden" name="conteudo2" id="conteudo2" value="" />
</form>
<fieldset>
<legend>Incluir filtros</legend>
<form action="<?php echo base_url('relatorio/relatorios/true/funcionario'); ?>" method="post" id="form_filtros2">
	<?php
		if(!empty($data_agendada))echo '<input type="hidden" name="data2" value="data_agendada" />';
		if(!empty($hora_agendada))echo '<input type="hidden" name="hora2" value="hora_agendada" />';
		if(!empty($tempo_estimado))echo '<input type="hidden" name="tempo2" value="tempo_estimado" />';
		if(!empty($prioridade))echo '<input type="hidden" name="prioridade2" value="prioridade" />';
	?>
	<ul id="filtros2">
		<li class="filtro2">
			<select name="filtro2[]" class="filtrando2">
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
			<span class="data_agendada2" style="display:none;">
				<div style="margin:0 15px; display:inline-block;">
					De: <input name="data_inicio2[]" class="data data_inicio2 input-small" type="text" value="" />&nbsp;&nbsp;
					Até: <input name="data_fim2[]" class="data data_fim2 input-small"  type="text" value="" />
				</div>
			</span>
			<span class = "status2" style=" display:none;">
				<div style="margin:0 15px; display:inline-block;">
					<input type="checkbox" name="nao_concluido2[]" class="nao_concluido2" value="nao_concluido" style="vertical-align:top;" /> Não Concluído&nbsp;&nbsp;
					<input type="checkbox" name="em_andamento2[]" class="em_andamento2" value="em_andamento" style="vertical-align:top;" /> Em Andamento&nbsp;&nbsp;
					<input type="checkbox" name="finalizado2[]" class="finalizado2" value="finalizado" style="vertical-align:top;" /> Finalizado&nbsp;&nbsp;
					<input type="checkbox" name="em_espera2[]" class="em_espera2" value="em_espera" style="vertical-align:top;" /> Em Espera&nbsp;&nbsp;
					<input type="checkbox" name="em_atraso2[]" class="em_atraso2" value="em_atraso" style="vertical-align:top;" /> Em Atraso&nbsp;&nbsp;
				</div>    
			</span>
			<span class = "funcionarios2" style=" display:none;">
				<div style="margin:0 15px; display:inline-block;">                
					Nome do funcionário: <input type="text" name="q2[]" class="auto2" size="40" autocomplete="off" />
				</div>
			</span>
			<button class="removerCampo2 btn btn-small btn-danger" type="button" style="vertical-align: top;"><i class="icon-remove icon-white"></i> Remover</button>
		</li>
		<li>
			<span class="buttons"><button id="Add2" class="btn btn-small btn-success" type="button"><i class="icon-plus icon-white"></i> Adicionar novo</button></span>
		</li>
   </ul>
   <ul>
		<li>&nbsp;</li>
		<li>
			<button id="filtrar2" class="btn btn-medium btn-success" type="button"> Confirmar filtros</button>            
			<button id="resetar2" class="btn btn-medium btn-warning" type="button"> Remover todos os filtros</button>   
			<button class="btn btn-inverse btn-medium" style="float: right;" type="button" id="download2">Fazer download para XLS</button>			
		</li>
	</ul>
</form>
</fieldset>
<table id="ordenar2" class="table table-condensed tablesorter">
	<thead>
		<tr>
			<th>ID</th>
			<th>Visita</th>
			<th>Funcionário</th>
			<th>Cliente</th>
			<th>Atendimento</th>
			<th>Cidade</th>
			<th>Bairro</th>
			<th>Tempo Estimado</th>
			<th>Duração</th>
			<th>Status</th>
		</tr>
	</thead>
	<tbody>
<?php
$html_donwload = '	<table id="download_tabela" class="table table-condensed">
		<tr>
			<td>ID</td>
			<td>Visita</td>
			<td>Funcionário</td>
			<td>Cliente</td>
			<td>Atendimento</td>
			<td>Cidade</td>
			<td>Bairro</td>
			<td>Tempo Estimado</td>
			<td>Duração</td>
			<td>Status</td>
		</tr>
';

foreach($atendimentos_funcionario as $funcionario){

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
	if ($funcionario->status == 'em_andamento') {
		$estilo_status = 'success'; $estilo_status_tr = 'success';
		$alt_status = 'Em Andamento'; 
		$color_linha_excel = '';
	} else if ($funcionario->status == 'em_espera') {	
		$estilo_status = 'info';  $estilo_status_tr = 'info';
		$alt_status = 'Em Espera'; 
		$color_linha_excel = '';
	} else if ($funcionario->status == 'em_atraso') {
		$estilo_status = 'important'; $estilo_status_tr = 'error'; 
		$alt_status = 'Em Atraso'; 
		$color_linha_excel = '';
	} else if ($funcionario->status == 'nao_concluido') {
		$estilo_status = 'warning'; $estilo_status_tr = 'warning';
		$alt_status = 'Não Concluído'; 
		$color_linha_excel = '';
	}else{ // finalizado
		$estilo_status = 'padrao';  $estilo_status_tr = 'padrao';
		$alt_status = 'Finalizado'; 
		$color_linha_excel = '';
	}
		
	$duracao = (!empty($funcionario->duracao)) ? substr($funcionario->duracao, 0, -3)."h" : "-";
	
	echo "<tr class='".$estilo_status_tr."'>";
		echo "<td>".$funcionario->idusuario_atendimento."</td>";
		echo "<td style='width: 48px;'>".$funcionario->visita."</td>";
		echo "<td>".$funcionario->nome."</td>";
		echo "<td>".$funcionario->cliente."</td>";
		echo "<td>".$funcionario->atendimento."</td>";
		echo "<td>".$funcionario->cidade."</td>";
		echo "<td>".$funcionario->bairro."</td>";		
		echo "<td>".substr($funcionario->tempo_estimado, 0, -3)."h</td>";
		echo "<td>".$duracao."</td>";
		echo "<td><span class='label label-".$estilo_status."'>".$alt_status."</span></td>";
	echo "<tr>";
	
	
	
	$html_donwload .=  "<tr class='".$funcionario->status."' style='background-color: ".$color_linha_excel.";'>".
		"<td>".$funcionario->idusuario_atendimento."</td>".
		"<td style='width: 48px;'>".$funcionario->visita."</td>".
		"<td>".$funcionario->nome."</td>".
		"<td>".$funcionario->cliente."</td>".
		"<td>".$funcionario->atendimento."</td>".
		"<td>".$funcionario->cidade."</td>".
		"<td>".$funcionario->bairro."</td>".
		"<td>".substr($funcionario->tempo_estimado, 0, -3)."h</td>".
		"<td>".$duracao."</td>".		
		"<td>".$alt_status."</td>".
		"<tr>";
	
}
	$html_donwload .="</table>";
?>
	</tbody>
</table>
<span id="conteudo_download2" style="display:none;">
	<?php echo $html_donwload; ?>
</span>