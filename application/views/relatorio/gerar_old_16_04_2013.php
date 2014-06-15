<script type="text/javascript">
	jQuery(document).ready(function($){
		
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
				$(this).parent("li").children(".data_agendada").fadeIn();
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
				$(this).parent("li").children(".status").fadeIn();
			}
			else if($(this).val() == 'nome'){
				$(this).parent("li").find(":text").val("");
				$(this).parent("li").children(".data_agendada").hide();
				$(this).parent("li").children(":checkbox").attr("checked", false);		
				$(this).parent("li").children(".status").hide();
				$(this).parent("li").children(".funcionarios").fadeIn();
				
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
			var con 		= '<?php echo (!empty($campos['con']))? $campos['con']: '' ;?>';
			var parcon 		= '<?php echo (!empty($campos['parcon']))? $campos['parcon']: '' ;?>';
			var ncon 		= '<?php echo (!empty($campos['ncon']))? $campos['ncon']: '' ;?>';
			var data_inicio	= '<?php echo (!empty($campos['data_inicio']))? $campos['data_inicio']: '' ;?>';
			var data_fim	= '<?php echo (!empty($campos['data_final']))? $campos['data_final']: '' ;?>';
			var nome		= '<?php echo (!empty($campos['nome']))? $campos['nome']: '' ;?>';
			
			if(key < 1){
				if(filtro == 'status'){
					$(".filtrando option[value='"+filtro+"']").attr("selected", true).trigger("change");
					if(con != ''){
						$(".con").attr("checked", true);	
					}
					if(parcon != ''){
						$(".parcon").attr("checked", true);	
					}
					if(ncon != ''){
						$(".ncon").attr("checked", true);	
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
					if(con != ''){
						novo.children(".status").children(".con").attr("checked", true);	
					}
					if(parcon != ''){
						novo.children(".parcon").attr("checked", true);	
					}
					if(ncon != ''){
						novo.children(".ncon").attr("checked", true);	
					}
				}
				else if(filtro == 'data_agendada'){
					novo.children(".filtrando").find("option[value='"+filtro+"']").attr("selected", true).trigger("change");
					if(data_inicio != ''){
						novo.children(".data_agendada").children(".data_inicio").val(data_inicio)
						novo.children(".data_agendada").show();	
					}
					if(data_fim != ''){
						novo.children(".data_agendada").children(".data_fim").val(data_fim)
						novo.children(".data_agendada").removeAttr("style");
					}
				}
				else if(filtro == 'nome'){
					novo.children(".filtrando").find("option[value='"+filtro+"']").attr("selected", true).trigger("change");
					if(nome != ''){
						novo.children(".funcionarios").children(".auto").val(nome);
						novo.children(".funcionarios").removeAttr("style");	
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
			$("#form_filtros").attr("action", "<?php echo base_url('relatorio/gerar'); ?>");
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
				if(($(".con").is(":checked"))||($(".parcon").is(":checked"))||($(".ncon").is(":checked"))){
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

<style type="text/css">
	table.bordasimples {
		width: 100%;
		border-collapse: collapse;
	}
	
	table.bordasimples tr td {
		border:1px solid #999;
	}
	th{
		border:1px solid #999;
	}
</style>
<?php
/*echo "<pre>";
var_dump($preenchimento);
echo "</pre>";
*/
// Exibir ou nao colunas do formulario

	$array_adicionais = array("data","hora","tempo","descricao","prioridade");
	
	$comparar_adicionais = array_diff($array_adicionais,$adicionais);
	$data = TRUE;
	$hora = TRUE;
	$tempo = TRUE;
	$prioridade = TRUE;
	
	foreach($comparar_adicionais AS $comparar){
		if($comparar == "data")
			$data = FALSE;
		if($comparar == "hora")
			$hora = FALSE;
		if($comparar == "tempo")
			$tempo = FALSE;
		if($comparar == "prioridade")
			$prioridade = FALSE;
	}

?>
  <div class="pull-left">
      <h2>Relatório de Atendimentos</h2>
  </div>
  <div class="pull-right">
      <a href="#" class="btn btn_voltar">« voltar</a>
  </div>
  
  <div class="clearfix"></div>

<?php
// Include das mensagens dos controllers. Sucesso ou erro
require('application/views/dashboard/mensagem.php');
?>

<?php
// Em caso de erro no preenchimento do formulário, os erros irão aparecer aqui.
if (validation_errors()) :
    ?>
    <div class="alert alert-error">
        <ul>
            <?php echo validation_errors('<li>', '</li>'); ?>
        </ul>
    </div>
<?php endif; ?>

<div class="form-style">
    <form method="post" id="form_download" target="_self">
    <ul>
        <li>
            <select name="tipo_download" id="tipo">
                <option>Selecione</option>
                <option value="xls">XLS</option>
                <!--<option value="pdf">PDF</option>-->
            </select>
            <input type="hidden" name="conteudo" id="conteudo" value="" />
            <span class="buttons" style="padding-left: 50px !important;">
                <button type="button" id="download" class="positive">
                    <img src="<?php echo base_url('assets/images/ico_tick.png'); ?>" alt=""/> 
                    Download
                </button>  
            </span>               
        </li>
    </ul>
    </form>
<fieldset>
    <legend>Filtros</legend>
    <form action="<?php echo base_url('relatorio/gerar/true'); ?>" method="post" id="form_filtros">
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
                <span class = "data_agendada" style=" display:none;">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                	De: <input name="data_inicio[]" class="data data_inicio" size="40" type="text" value="" />&nbsp;&nbsp;&nbsp;
                    Até: <input name="data_fim[]" class="data data_fim" size="40" type="text" value="" />
                </span>
                <span class = "status" style=" display:none;">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <span>
                        <input type="checkbox" name="con[]" class="con" value="concluido" /> Concluído
                    </span>
                    <span>&nbsp;&nbsp;&nbsp;&nbsp;</span>
                    <span>
                        <input type="checkbox" name="parcon[]" class="parcon" value="parcialmente_concluido" /> Parcialmente Concluído
                    </span>
                    <span>&nbsp;&nbsp;&nbsp;&nbsp;</span>
                    <span>
                        <input type="checkbox" name="ncon[]" class="ncon" value="nao_concluido" /> Não Concluído
                    </span>                
                </span>
                <span class = "funcionarios" style=" display:none;">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                	Funcionário: <input type="text" name="q[]" class="auto" size="40" autocomplete="off" /><!-- <input type="button" id="incluir_funcionario" value="Incluir" />-->
                </span>
                <button type="button" class="negative removerCampo">
                    <img src="<?php echo base_url('assets/images/ico_cross.png'); ?>" alt=""/> 
                    Remove
                </button>  
            </li>
            <li>
                <span class="buttons">
                    <button type="button" id="Add" class="positive">
                        <img src="<?php echo base_url('assets/images/ico_tick.png'); ?>" alt=""/> 
                        Add
                    </button>  
                </span>  
            </li>
       </ul>
       <ul>
            <li>
            	&nbsp;
            </li>
            <li>
                <span class="buttons" style="padding-left: 40% !important;">
                    <button type="button" id="filtrar" class="positive">
                        <img src="<?php echo base_url('assets/images/ico_tick.png'); ?>" alt=""/> 
                        Filtrar
                    </button>  
                </span>  
                <span class="buttons">
                    <button type="button" id="resetar" class="positive">
                        <img src="<?php echo base_url('assets/images/ico_tick.png'); ?>" alt=""/> 
                        Resetar
                    </button>  
                </span>               
            </li>
        </ul>
    </form>
</fieldset>
<fieldset>
<?php
$dow_data = "";
$dow_hora = "";
$dow_prioridade = "";
$dow_tempo = "";
$nome_tabela = "";
$nome = "";
if(!empty($atendimentos[0]->usuario)){
	$nome_tabela = "<td>Funcionário</td>";
}

?>
    <legend>Relatório</legend>
	<table id="ordenar" class="bordasimples">
    	<thead>
        	<tr>
            	<th>Cliente</th>
            	<th>Título</th>
            	<th>Endereço</th>
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
					echo "<th>Tempo</th>";
					$dow_tempo = "<td>Tempo</td>";;
				}
				if($prioridade){
					echo "<th>Prioridade</th>";
					$dow_prioridade = "<td>Prioridade</td>";
				}
            ?>	
                <th>Status</th>
        	</tr>
    	</thead>
    	<tbody>
<?php
	$html_donwload = '	<table id="download_tabela" class="bordasimples">
        	<tr>
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
				$dow_prioridade		
            .'	
                <td>Status</td>'.
				$nome_tabela
        	.'</tr>
';

foreach($atendimentos AS $atendimento){
	
		$array_banco = array("/_/", "/concluido/", "/nao/", "/parcialmente/", "/sim/");
		$array_texto = array(" ", "Concluído", "Não", "Parcialmente", "Sim");
			
		if($atendimento->status == 'concluido')
			$color = "#D0FCC9";
		elseif($atendimento->status == 'parcialmente_concluido')
			$color = "#FBDAA8";
		elseif($atendimento->status == 'nao_concluido')
			$color = "#FBACAE";
		
		
		echo "<tr class='".$atendimento->status."' style='background-color: ".$color.";'>";
			echo "<td>".$atendimento->nome."</td>";
			echo "<td>".$atendimento->titulo."</td>";
			echo "<td>".$atendimento->endereco."</td>";
			
			if($data){
				echo "<td>".fdata($atendimento->data_agendada, "/")."</td>";
				$dow_data = "<td>".fdata($atendimento->data_agendada, "/")."</td>";
			}
			if($hora){
				echo "<td>".$atendimento->hora_agendada."</td>";
				$dow_hora = "<td>".$atendimento->hora_agendada."</td>";
			}
			if($tempo){
				echo "<td>".$atendimento->tempo_estimado."</td>";
				$dow_tempo = "<td>".$atendimento->tempo_estimado."</td>";
			}
			if($prioridade){
				echo "<td>".$atendimento->prioridade."</td>";
				$dow_prioridade = "<td>".$atendimento->prioridade."</td>";
			}
			
			$status = preg_replace($array_banco, $array_texto, $atendimento->status);
			echo "<td>".$status."</td>";
		echo "<tr>";
		
		if(!empty($atendimento->usuario)){
			$nome = "<td>".$atendimento->usuario."</td>";
		}
		
		$html_donwload .=  "<tr class='".$atendimento->status."' style='background-color: ".$color.";'>".
			"<td>".$atendimento->nome."</td>".
			"<td>".$atendimento->titulo."</td>".
			"<td>".$atendimento->endereco."</td>".
			"<td>".$atendimento->endereco_numero."</td>".
			"<td>".$atendimento->endereco_complemento."</td>".
			"<td>".$atendimento->bairro."</td>".
			"<td>".$atendimento->cidade."</td>".
			"<td>".$atendimento->estado."</td>".
			$dow_data.
			$dow_hora.
			$dow_tempo.
			$dow_prioridade.			
			"<td>".$status."</td>".
			$nome.
			"<tr>";
		
}

	$html_donwload .="</table>";
?>
    	</tbody>
	</table>
	<span id="conteudo_download" style="display:none">
    	<?php echo $html_donwload; ?>
    </span>
</fieldset>
</div><!--fim da div formstyle-->
