<?php
// Transformando a data e hora do banco e do dia atual em mktime para poder fazer as comparações e subtrações de horas.
$formatDataBanco =  explode("-", $atendimento->data_agendada);
$formatHoraBanco = explode(":",$atendimento->hora_agendada);
$mktimeBanco = mktime($formatHoraBanco[0]-1, $formatHoraBanco[1], $formatHoraBanco[2], $formatDataBanco[1], $formatDataBanco[2], $formatDataBanco[0]);
$mktimeAtual = mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y"));
?>

<script>
jQuery(document).ready(function($){

	<?php if(!empty($_POST["tem_contra_senha"])){
				if($_POST["tem_contra_senha"] == 'sim'){?>
					$(".mostra-contrasenha").show();
			<?php }else{?>
					$(".mostra-contrasenha").hide();	
			<?php }
		}else{
			if($atendimento->tem_contra_senha == 'sim'){?>
				$(".mostra-contrasenha").show();
		<?php }else{?>
				$(".mostra-contrasenha").hide();	
		<?php }
		}
	?>
	
	$("input[name=tem_contra_senha]").change(function(){
		tem_contra_senha = $(this).val();
		if (tem_contra_senha=='sim')
			$(".mostra-contrasenha").show();		
		else
			$(".mostra-contrasenha").hide();		
	}); 

	 $("select[name=idestado]").change(function(){
		$("select[name=idcidade]").html('<option value="0">Carregando...</option>');
		
		$.post("<?php print base_url(); ?>cidade/getCidades/",
			  {idestado:$(this).val()},
				  function(valor){
					 $("select[name=idcidade]").html(valor);
				  }
			  )
		
	 });  
	 
// ADD Cliente
	
	$("#buscar_cliente").on("focus", "#cli",function(e) {
		$(".auto1_conteudo").remove();
	});
	
	$("#cli").autocomplete("<?php print base_url(); ?>autocomplete/clientesAutocomplete/",
	{
	  minLength: 2,
	  scrollHeight: 220,
	  selectFirst: false
	});
	
	$("#incluir_cliente").bind("click",function(){
		if($("#cli").val() != ""){
			addCliente();
		}
	});
	 
// FIM

// ADD Funcionário

	$("#buscar_responsavel").on("focus", "#auto1",function(e) {
		//$(".ac_results").remove();
		var quantidade = $(".ac_results").length;

		$(".auto1_conteudo").remove();
		var tipo = "texto";
		var conteudo = $("#cli").val();
		if(conteudo == ""){
			tipo = "id";
			conteudo = $("#idcliente").val();
		}
		if ( conteudo != "" ) { // If the autocomplete wasn't called yet:
			
			$(this).autocomplete("<?php print base_url(); ?>autocomplete/contatosAutocomplete/"+tipo+"/"+conteudo+"/", 
			{
			  minLength: 2,
			  scrollHeight: 220,
			  selectFirst: false
			});
			
		}
	});
	
	$("#auto").autocomplete("<?php print base_url(); ?>autocomplete/usuariosAutocomplete/",
	{
	  minLength: 2,
	  scrollHeight: 220,
	  selectFirst: false
	});
	
	$("#incluir_funcionario").bind("click",function(){
		if($("#auto").val() != ""){
			addUser();
		} 
	});	
	
	$("#incluir_responsavel").bind("click",function(){
		if($("#auto1").val() != ""){
			addResponsavel();
		}
	});	

	
<?php
if(!empty($_POST["funcionarios_alocados"])){
	foreach($_POST["funcionarios_alocados_antigos"] as $ids_antigos){
?>		
		var value_imput_antigo = '<?php echo $ids_antigos; ?>';
		$("#form").append('<input type="hidden" name="funcionarios_alocados_antigos[]" value="'+value_imput_antigo+'"/>');
<?php
	}
	foreach($_POST["funcionarios_alocados"] as $ids){
		$valores = explode("|&",$ids);
		$nome_funcionario = str_replace("_"," ",$valores[0]);
		$ids_funcionario = end($valores);
		
?>
		
		var classeUser = 'user_<?php echo $ids_funcionario ;?>';
		$("#funcionarios").show();
		$("#form").append('<input type="hidden" name="funcionarios_alocados[]" value="<?php echo $ids; ?>" class="'+classeUser+'" />');
		$("#result_funcionarios").append('<span class="'+classeUser+'"><?php echo $nome_funcionario; ?> <button alt="" class="'+classeUser+' btn btn-danger btn-mini"  style="cursor:pointer;" onclick="remuve(\''+classeUser+'\');">Remover</button></span><br class="'+classeUser+'" />');		
<?php		
	}
}
else{
	foreach ($funcionarios_alocados as $funcionario) {	
?>
		var nome = "<?php echo $funcionario->nome; ?>";
		var id   = <?php echo $funcionario->idusuario; ?>;
		var value_imput = nome.replace(/\s/g,'_')+'|&'+id;
		var classeUser = 'user_'+id;
		$("#funcionarios").show();
		$("#form").append('<input type="hidden" name="funcionarios_alocados_antigos[]" value="'+value_imput+'"/>');
		$("#form").append('<input type="hidden" name="funcionarios_alocados[]" value="'+value_imput+'" class="'+classeUser+'" />');
		$("#result_funcionarios").append('<span class="'+classeUser+'">'+nome+' <button alt="" class="'+classeUser+' btn btn-danger btn-mini"  style="cursor:pointer;" onclick="remuve(\''+classeUser+'\');">Remover</button></span><br class="'+classeUser+'" />');
<?php		
	}
}
?>

<?php
if(!empty($_POST["nome_responsavel"])){
		$ids = $_POST["nome_responsavel"];
		$valores = explode("|&",$ids);
		$nome_cliente = str_replace("_"," ",$valores[0]);
		$ids_cliente = end($valores);
		
?>
		var classeCliente = 'responsavel_<?php echo $ids_cliente ;?>';
		$("#responsavel").show();
		$("#form").append('<input type="hidden" id="nome_responsavel" name="nome_responsavel" value="<?php echo $ids; ?>" class="'+classeCliente+'" />');
		$("#result_responsavel").append('<span class="'+classeCliente+'"><?php echo $nome_cliente; ?> <button alt="" class="'+classeCliente+' btn btn-danger btn-mini" style="cursor:pointer;" onclick="remuve(\''+classeCliente+'\',\'incluir_responsavel\');">Remover</button></span><br class="'+classeCliente+'" />');		
		$("#incluir_responsavel").val("Substituir");
<?php		
}
else{
	foreach ($contatos as $contato) {
		if($contato->idcliente_contato == $atendimento->responsavel){
?>
		var nome = "<?php echo $contato->contato_responsavel; ?>";
		var id   = <?php echo $contato->idcliente_contato; ?>;
		var value_imput = nome.replace(/\s/g,'_')+'|&'+id;
		var classeCliente = 'responsavel_'+id;
		$("#responsavel").show();
		$("#form").append('<input type="hidden" id="nome_responsavel" name="nome_responsavel" value="'+value_imput+'" class="'+classeCliente+'" />');
		$("#result_responsavel").append('<span class="'+classeCliente+'">'+nome+' <button alt="" class="'+classeCliente+' btn btn-danger btn-mini" style="cursor:pointer;" onclick="remuve(\''+classeCliente+'\',\'incluir_responsavel\');">Remover</button></span><br class="'+classeCliente+'" />');		
		$("#incluir_responsavel").val("Substituir");
<?php	
		}
	}
}
?>

<?php
if(!empty($_POST["cliente_alocado"])){
		$ids = $_POST["cliente_alocado"];
		$valores = explode("|&",$ids);
		$nome_cliente = str_replace("_"," ",$valores[0]);
		$ids_cliente = end($valores);
		
?>
		var classeCliente = 'cli_<?php echo $ids_cliente ;?>';
		$("#cliente").show();
		$("#idcliente").val(<?php echo $ids_cliente; ?>);
		$("#form").append('<input type="hidden" id="cliente_alocado" name="cliente_alocado" value="<?php echo $ids; ?>" class="'+classeCliente+'" />');
		$("#result_clientes").append('<span class="'+classeCliente+'"><?php echo $nome_cliente; ?> <button alt="" class="'+classeCliente+' btn btn-danger btn-mini" style="cursor:pointer;" onclick="remuve(\''+classeCliente+'\',\'incluir_cliente\');">Remover</button></span><br class="'+classeCliente+'" />');		
		$("#incluir_cliente").val("Substituir");
<?php		
}
else{
	
	foreach ($clientes as $cliente) {
		if($cliente->idcliente == $atendimento->idcliente){
?>
		var nome = "<?php echo $cliente->nome; ?>";
		var id   = <?php echo $cliente->idcliente; ?>;
		var value_imput = nome.replace(/\s/g,'_')+'|&'+id;
		var classeCliente = 'cli_'+id;
		$("#cliente").show();
		$("#idcliente").val(id);
		$("#form").append('<input type="hidden" id="cliente_alocado" name="cliente_alocado" value="'+value_imput+'" class="'+classeCliente+'" />');
		$("#result_clientes").append('<span class="'+classeCliente+'">'+nome+' <button alt="" class="'+classeCliente+' btn btn-danger btn-mini" style="cursor:pointer;" onclick="remuve(\''+classeCliente+'\',\'incluir_cliente\');">Remover</button></span><br class="'+classeCliente+'" />');		
		$("#incluir_cliente").val("Substituir");
<?php	
		}
	}
}
?>

	$("#idcliente").change(function(){
		var tipo = "texto";
		var conteudo_cli = $("#cli").val();
		if(conteudo_cli == ""){
			tipo = "id";
			conteudo_cli = $("#idcliente").val();
		}
		$.post("<?php print base_url(); ?>atendimento/getEnderecoPrincipal/",{conteudo_cli: conteudo_cli, tipo: tipo}, function(j){
			var retorno = j.split("|");
			if(retorno !=''){
				$("#mostrar_tipo").show();
				$("#tipo_endereco").attr("checked",true);
				$("#endereco").val(retorno[0]);
				$("#endereco_numero").val(retorno[1]);
				$("#endereco_complemento").val(retorno[2]);
				$("#bairro").val(retorno[3]);
				$("#pontos_referencias").val(retorno[6]);
				$("#idestado").find("option[value='"+retorno[4]+"']").attr("selected",true).trigger('change');
				$.post("<?php print base_url(); ?>atendimento/cadastro/", function(){
					$("#idcidade option[value="+retorno[5]+"]").attr("selected",true);
				});
				if(retorno.length > 7){
					$("#tipo_endereco2").show()
					$("#tipo_endereco_texto2").show()
					$("#select_filial").find("option").remove();
					$("#select_filial").append('<option value="0">selecionar</option>');
					var x = 1;
					for(var i=7; i < retorno.length; i++){
						$("#select_filial").append("<option value='"+retorno[i]+"'>Filial "+x+"</option>");
						x++;
					}
				}
				else{
					$("#tipo_endereco2").hide()
					$("#tipo_endereco_texto2").hide()
					$("#select_filial").find("option").remove();
					$("#select_filial").append('<option value="0">selecionar</option>');
				}
			}
			else{
				select_filial();
				$(".tipo_endereco").attr("checked",false);
				$("#mostrar_tipo").hide();
				$("#filial").hide();
				$("#endereco").val('');
				$("#endereco_numero").val('');
				$("#endereco_complemento").val('');
				$("#bairro").val('');
				$("#pontos_referencias").val('');
				$("#idestado").find("option:eq(0)").attr("selected",true);
				$("#idcidade").find("option").remove();
				$("#idcidade").append("<option value='0' disabled='disabled'>escolha o estado primeiro</option>");
			}
		});
	});
	// Pegar endereço Filial
	 $("#select_filial").change(function(){
		$.post("<?php print base_url(); ?>atendimento/getEnderecoFilial/",{idcliente_filial: $(this).val()}, function(k){
			var retorno = k.split("|");
			if(retorno !=''){
				select_filial();
				$("#tipo_endereco2").attr("checked",true);
				$("#endereco").val(retorno[0]);
				$("#endereco_numero").val(retorno[1]);
				$("#endereco_complemento").val(retorno[2]);
				$("#bairro").val(retorno[3]);
				$("#pontos_referencias").val(retorno[6]);
				$("#idestado").find("option[value='"+retorno[4]+"']").attr("selected",true).trigger('change');
				$.post("<?php print base_url(); ?>atendimento/cadastro/", function(){
					$("#idcidade option[value="+retorno[5]+"]").attr("selected",true);
				});
				
			}
			else{
				select_filial();
				$("#tipo_endereco2").attr("checked",true);
				$("#endereco").val('');
				$("#endereco_numero").val('');
				$("#endereco_complemento").val('');
				$("#bairro").val('');
				$("#pontos_referencias").val('');
				$("#idestado").find("option:eq(0)").attr("selected",true);
				$("#idcidade").find("option").remove();
				$("#idcidade").append("<option value='0' disabled='disabled'>escolha o estado primeiro</option>");
			}
		});
	});
	$(".tipo_endereco").click(function(){
		if($(this).val() == 1){
			$("#filial").hide();
			$("#idcliente").trigger("change");
		}
		else if($(this).val() == 2){

			$("#select_filial").trigger("change");
			if($('#select_filial > option').length > 1){
				select_filial();
			}
		}
	});
// FIM
});



function verificaUser(user,atendimento){
		var retorno = new Boolean;
		$.getJSON("<?php print base_url(); ?>atendimento/vericarExclusaoFuncionarioAtendimento/"+user+"/"+atendimento,{}, function(j){
			if( j != ''){
				
				var mktimeBanco = <?php echo $mktimeBanco;?>;
				var mktimeAtual = <?php echo $mktimeAtual;?>;
				
				if(j.data_hora_checkin != null){
					alert("Este funcionário não pode ser exluído do atendimento, pois o funcionário já validou sua presença no atendimento.");
					retorno = false;
				}
				else if((mktimeBanco <= mktimeAtual)&&(j.status == "finalizado")){
					alert("Este funcionário não pode ser exluído do atendimento, pois o mesmo já foi finalizado.");
					retorno = false;
				}
				else if((mktimeBanco <= mktimeAtual)&&(j.status == "em_andamento")){
					alert("Este funcionário não pode ser exluído do atendimento, pois o mesmo já foi iniciado.");
					retorno = false;
				}
				else if((mktimeBanco <= mktimeAtual)&&(j.status == "nao_concluido")){
					alert("Este funcionário não pode ser exluído do atendimento, pois o mesmo não esta concluído.");
					retorno = false;
				}
				else{
					$("."+user).remove();
					if($("#form").find('input[name="funcionarios_alocados[]"]').length == 0){
						$("#funcionarios").hide();
					}
					retorno = true;
				}
			}
			else{
				retorno = false;
			}	
		});
		return retorno;
}

function addUser(){
	$.getJSON("<?php print base_url(); ?>atendimento/getIdNomeUsuario/"+$("#auto").val(),{}, function(j){
					
			if(j != ''){
				var conteudo = $("#form").find('input[name="funcionarios_alocados[]"]').length;
				var value_imput = j.nome.replace(/\s/g,'_')+'|&'+j.idusuario;
				var idAtendimento = <?php echo $atendimento->idatendimento; ?>;
				var classeUser = 'user_'+j.idusuario;
				
				if(conteudo == 0){
						$("#form").append('<input type="hidden" name="funcionarios_alocados[]" value="'+value_imput+'" class="'+classeUser+'" />');
						$("#result_funcionarios").append('<span class="'+classeUser+'">'+j.nome+' <button alt="" class="'+classeUser+' btn btn-danger btn-mini"  style="cursor:pointer;" onclick="remuve(\''+classeUser+'\');">Remover</button></span><br class="'+classeUser+'" />');		
						$("#funcionarios").show();
						$("#auto").val("");
				}
				else{
					var encontrado = 0;
					for(var i = 0; i < conteudo; i++){
						var id_consulta = $("#form").find('input[name="funcionarios_alocados[]"]')[i].value.split("|&");
						if(id_consulta[1] == j.idusuario){
								encontrado++;
						}
					}
					if(encontrado >= 1){
						alert("Você já adicionou esse funcionário");
					}
					else{
						$("#form").append('<input type="hidden" name="funcionarios_alocados[]" value="'+value_imput+'" class="'+classeUser+'" />');
						$("#result_funcionarios").append('<span class="'+classeUser+'">'+j.nome+' <button alt="" class="'+j.idusuario+' btn btn-danger btn-mini"  style="cursor:pointer;" onclick="remuve(\''+classeUser+'\');">Remover</button></span><br class="'+classeUser+'" />');		
						
						$("#funcionarios").show();
						$("#auto").val("");
					}
				}
			}
			else {
				alert("Adicione um funcionário válido.")
			}
	});
}

function addResponsavel(){
	var tipo = "texto";
	var conteudo_cli = $("#cli").val();
	if(conteudo_cli == ""){
		tipo = "id";
		conteudo_cli = $("#idcliente").val();
	}

	if(!empty(conteudo_cli)){
		$.getJSON("<?php print base_url(); ?>atendimento/getIdNomeResponsavel/"+$("#auto1").val()+"/"+tipo+"/"+conteudo_cli,{}, function(j){
				if(j != ''){
					var conteudo = $("#form").find('input[name="nome_responsavel"]').length;
					var value_imput = j.contato_responsavel.replace(/\s/g,'_')+'|&'+j.idcliente_contato;
					var idAtendimento = <?php echo $atendimento->idatendimento; ?>;
					var classeUser = 'responsavel_'+j.idcliente_contato;
					var btn_add = "incluir_responsavel";

					if(conteudo == 0){
							$("#form").append('<input type="hidden" id="nome_responsavel" name="nome_responsavel" value="'+value_imput+'" class="'+classeUser+'" />');
							$("#result_responsavel").append('<span class="'+classeUser+'">'+j.contato_responsavel+' <button alt="" class="'+classeUser+' btn btn-danger btn-mini"  style="cursor:pointer;" onclick="remuve(\''+classeUser+'\', \''+btn_add+'\');">Remover</button></span><br class="'+classeUser+'" />');		
							$("#emails_assinatura").val(j.contato_email);
							$("#responsavel").show();
							$("#auto1").val("");
							$("#"+btn_add).val("Substituir");
					}
					else{
					
						var encontrado = 0;
						for(var i = 0; i < conteudo; i++){
							var id_consulta = $("#form").find('input[name="nome_responsavel"]')[i].value.split("|&");
							if(id_consulta[1] == j.idcliente_contato){
									encontrado++;
							}
						}
						if(encontrado >= 1){
							alert("Você já adicionou esse contato");
						}
						else{
							$("#nome_responsavel").val(value_imput).attr("class",classeUser);
							$("#result_responsavel").html('<span class="'+classeUser+'">'+j.contato_responsavel+' <button alt="" class="'+j.idcliente_contato+' btn btn-danger btn-mini"  style="cursor:pointer;" onclick="remuve(\''+classeUser+'\', \''+btn_add+'\');">Remover</button></span><br class="'+classeUser+'" />');		
							$("#emails_assinatura").val(j.contato_email);
							$("#responsavel").show();
							$("#auto1").val("");
							$("#"+btn_add).val("Substituir");
						}
					}
				}
				else {
					alert("Adicione um contato válido.");
				}
		});
	}else{
		alert("Adicione um cliente antes de adicionar um contato.");
		$("#cli").focus();
	}
}

function addCliente(){
	$.getJSON("<?php print base_url(); ?>atendimento/getIdNomeCliente/"+$("#cli").val(),{}, function(j){

			if(j != ''){
				var conteudo = $("#form").find('input[name="cliente_alocado"]').length;
				var value_imput = j.nome.replace(/\s/g,'_')+'|&'+j.idcliente;
				var classeCliente = 'cli_'+j.idcliente;
				var btn_add = "incluir_cliente";
				
				if(conteudo == 0){
						//$("#idcliente").html(j.idcliente);
						$("#form").append('<input type="hidden" id="cliente_alocado" name="cliente_alocado" value="'+value_imput+'" class="'+classeCliente+'" />');
						$("#result_clientes").html('<span class="'+classeCliente+'">'+j.nome+' <button alt="" class="'+classeCliente+' btn btn-danger btn-mini" style="cursor:pointer;" onclick="remuve(\''+classeCliente+'\', \''+btn_add+'\');">remover</button></span><br class="'+classeCliente+'" />');							
						
						$("#cliente").show();
						$("#idcliente").val(j.idcliente).trigger("change");
						$("#cli").val("");
						$("#"+btn_add).val("Substituir");
				}
				else{
					var encontrado = 0;
					for(var i = 0; i < conteudo; i++){
						var id_consulta = $("#form").find('input[name="cliente_alocado"]')[i].value.split("|&");
						if(id_consulta[1] == j.idcliente){
								encontrado++;
						}
					}
					if(encontrado >= 1){
						alert("Você já adicionou esse cliente");
					}
					else{
						$("#cliente_alocado").val(value_imput).attr("class",classeCliente);
						$("#result_clientes").html('<span class="'+classeCliente+'">'+j.nome+' <button alt="" class="'+classeCliente+' btn btn-danger btn-mini" style="cursor:pointer;" onclick="remuve(\''+classeCliente+'\', \''+btn_add+'\');">Remover</button></span><br class="'+classeCliente+'" />');		
						//$("#idcliente").html(j.idcliente);
						$("#idcliente").val(j.idcliente).trigger("change");		
						$("#filial").hide();						
						$("#cliente").show();
						$("#cli").val("");
						$("#"+btn_add).val("Substituir");
					}
				}
			}
			else {
				alert("Adicione um cliente válido.")
			}
	});
}

function select_filial(){
	if($('#select_filial > option').length > 1){
		$("#filial").show();
	}
}

function remuve(e, btn_add){
	$("."+e).remove();
	$("#"+btn_add).val("Incluir");
	if($("#form").find('input[name="cliente_alocado"]').length == 0){
		$("#cliente").hide();
		$("#idcliente").val(0);
		$("#mostrar_tipo").hide();
		$("#filial").hide();
		$("#endereco").val('');
		$("#endereco_numero").val('');
		$("#endereco_complemento").val('');
		$("#bairro").val('');
		$("#pontos_referencias").val('');
		$("#idestado").find("option:eq(0)").attr("selected",true);
		$("#idcidade").find("option").remove();
		$("#idcidade").append("<option value='0' disabled='disabled'>escolha o estado primeiro</option>");
		
	}
	return false;
}

function validarUsuarios(){
	
	var quantidadeUser = $("#form").find('input[name="funcionarios_alocados[]"]').length;
	
	if( ($("#data_agendada").val()!= "") && ($("#tempo_estimado").val() != "") && ($("#hora_agendada").val() != "") && (quantidadeUser > 0)){

		var textoConfirm = '';
		var dataInformada = $("#data_agendada").val().split("/",3);
		dataInformada = dataInformada[1]+'_'+dataInformada[0]+'_'+dataInformada[2];
		var horaInformada = $("#hora_agendada").val().replace(/:/g,"_");
		var tempoInformado = $("#tempo_estimado").val().replace(/:/g,"_");
		var idsconsultas = '';
		var nomes = '';
		var conteudo_pessoa = '';
		var idatendimento = $("#idatendimento").val();
		
		for (var l = 0; l < quantidadeUser; l++){
			conteudo_pessoa = $("#form").find('input[name="funcionarios_alocados[]"]')[l].value.split("|&");
			nomes += conteudo_pessoa[0]+"%383";
			idsconsultas += conteudo_pessoa[1]+"%383";
		}

		$.getJSON("<?php print base_url(); ?>atendimento/getValidacaoUser/"+idsconsultas+"/"+dataInformada+"/"+horaInformada+"/"+tempoInformado+"/"+nomes+'/'+idatendimento,{}, function(j){
			if(j.pass){
				if(confirm(j.texto)){
					window.document.getElementById("form").submit();
					//$("#form").submit();
				}
				else{
					return false;
				}
			}
			else{
				$("#form").submit();	
			}
		});
	}
	else{
		$("#form").submit();	
	}
}
</script>
                
  <div class="pull-left">
      <h2>Editar atendimento</h2>
  </div>
  <div class="pull-right">
      <a class="btn btn_voltar">« voltar</a>
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
    <form action="<?php echo base_url('atendimento/update'); ?>" method="post" id="form" class="well" autocomplete="off">
    <input type="hidden" name="idatendimento" id="idatendimento" value="<?php echo $atendimento->idatendimento; ?>" /> 
    <input type="hidden" id="idcliente" value="" />
	<input type="hidden" id="data_anterior" name="data_anterior" value="<?php echo $atendimento->data_agendada?>" />
	<input type="hidden" id="hora_anterior" name="hora_anterior" value="<?php echo $atendimento->hora_agendada?>" />
	  <fieldset>
        <legend>Informações principais</legend>
            <ul>
                <li>
                    <label>Título: </label>
                    <input name="titulo" class="input-xxlarge" type="text" value="<?php echo set_value('titulo', $atendimento->titulo); ?>" />
                    <img src="<?php echo base_url('/assets/images/ico_duvida.png'); ?>" class="tooltip-custom" title="" data-toggle="tooltip" data-original-title="Nomeie aos atendimentos do jeito que achar melhor. Pode usar nomes, números ou qualquer outro padrão adotado pela sua empresa." width="16" height="16" /></li>
                <!--<li>
                    <label>Tag: </label>
                    <input name="idtag" size="40" type="text" class="idtag" value="<?php echo set_value('idtag', $atendimento->idtag); ?>" />
                </li>-->                                   
                <li id="buscar_cliente">
                    <label>Cliente: </label>
					<input type="text" name="campo_cliente" id="cli" class="input-xlarge" autocomplete="off" value="<?php echo set_value("campo_cliente"); ?>" /> <input type="button" id="incluir_cliente" class="btn btn-success btn-small" value="Incluir" style="vertical-align:top" />
                <img src="<?php echo base_url('/assets/images/ico_duvida.png'); ?>" width="16" height="16" class="tooltip-custom" title="" data-toggle="tooltip" data-original-title="É possível cadastrar apenas um cliente para cada atendimento. Se precisar substituí-lo, selecione outro cliente e clique no botão Substituir. O sistema sugere os nomes a partir das letras digitadas." /></li>
                
                <li id="cliente" style="display:none;">
                	<div style="width:150px; min-height: 32px; float:left;">
                    	<label>Cliente escolhido: </label>
                    </div>
                    <div style="width:auto; float:left;">
						<span id="result_clientes"></span>
                    </div>
                </li>
                
                <div class="clearfix"></div>
                
                <li>
                    <label>Descrição: </label>
                    <textarea name="descricao" rows="5" class="span5"><?php echo set_value('descricao', $atendimento->descricao); ?></textarea>
                <img src="<?php echo base_url('/assets/images/ico_duvida.png'); ?>" class="tooltip-custom" title="" data-toggle="tooltip" data-original-title="Acrescente informações que podem ajudar a equipe externa a executar o serviço com mais eficiência. Por exemplo: instruções, checklists, lembretes e etc." width="16" height="16" /></li>  
                <li>
                    <label>Data agendada: </label>
                    <input name="data_agendada" id="data_agendada" type="text" class="datepicker data input-small" value="<?php echo set_value('data_agendada', fdata($atendimento->data_agendada, "/")); ?>" />
                <img src="<?php echo base_url('/assets/images/ico_duvida.png'); ?>" class="tooltip-custom" title="" data-toggle="tooltip" data-original-title="Quando será este atendimento? Escolha no calendário ou digite no formato dd/mm/aaaa." width="16" height="16" /></li> 
                <li>
                    <label>Hora agendada: </label>
                    <input name="hora_agendada" id="hora_agendada" type="text" class="hora input-mini" value="<?php echo set_value('hora_agendada', $atendimento->hora_agendada); ?>" />
                <img src="<?php echo base_url('/assets/images/ico_duvida.png'); ?>" class="tooltip-custom" title="" data-toggle="tooltip" data-original-title="Qual o horário previsto para o início deste atendimento? Digite no formato hh:mm." width="16" height="16" /></li> 
                <li>
                    <label>Duração: </label>
                    <input name="tempo_estimado" id="tempo_estimado" type="text" class="hora input-mini" value="<?php echo set_value('tempo_estimado', $atendimento->tempo_estimado); ?>" />
                <img src="<?php echo base_url('/assets/images/ico_duvida.png'); ?>" class="tooltip-custom" title="" data-toggle="tooltip" data-original-title="Qual a duração estimada para este atendimento? Digite no formato hh:mm." width="16" height="16" /></li>            

                                                     
            </ul>
      </fieldset>
	  <fieldset>
        <legend>Contato responsável</legend>
            <ul>
                <li id="buscar_responsavel">
                    <label style="width: 150px;">Adicionar contato: </label>
					<input type="text" name="campo_responsavel" id="auto1" class="input-xlarge" autocomplete="off" value="<?php echo set_value("campo_responsavel"); ?>" /> <input type="button" id="incluir_responsavel" class="btn btn-success btn-small" value="Incluir" style="vertical-align:top" />
                <img src="<?php echo base_url('/assets/images/ico_duvida.png'); ?>" class="tooltip-custom" title="" data-toggle="tooltip" data-original-title="Quem no cliente é responsável por este atendimento? Lembre-se que o contato deve estar incluído em Informações do cliente. O sistema sugere os nomes a partir das letras digitadas." width="16" height="16" /></li>
                <li id="responsavel" style="display:none;">
                	<div style="width:164px; min-height: 32px; float:left;">
                    	<label style="width: 154px;">Responsável escolhido: </label>
                    </div>
                    <div style="width:auto; float:left;">
						<span id="result_responsavel"></span>
                    </div>
                </li>
            </ul>
      </fieldset>  

	  <fieldset>
        <legend>Equipe externa</legend>
            <ul>
                <li id="buscar">
                    <label style="width: 150px;">Adicionar funcionário: </label>
					<input type="text" name="campo_funcionario" id="auto" class="input-xlarge" autocomplete="off" value="<?php echo set_value("campo_funcionario"); ?>" /> <input type="button" id="incluir_funcionario" class="btn btn-success btn-small" value="Incluir" style="vertical-align:top" />
                <img src="<?php echo base_url('/assets/images/ico_duvida.png'); ?>" class="tooltip-custom" title="" data-toggle="tooltip" data-original-title="Quem vai ao cliente realizar o atendimento? Para adicionar mais de um funcionário, faça uma nova busca e clique no botão Incluir. O sistema sugere os nomes a partir das letras digitadas." width="16" height="16" /></li>
                <li id="funcionarios" style="display:none;">
                	<div style="width:150px; min-height: 32px; float:left;">
                    	<label>Relacionados: </label>
                    </div>
                    <div style="width:auto; float:left;">
						<span id="result_funcionarios"></span>
                    </div>
                </li>
            </ul>
      </fieldset>   


 <fieldset>
        <legend>Informações de localização
        <img src="<?php echo base_url('/assets/images/ico_duvida.png'); ?>" class="tooltip-custom" title="" data-toggle="tooltip" data-original-title="Onde será feito o atendimento? Escolha entre o endereço principal do cliente, alguma das filiais cadastradas ou adicione um endereço temporário. Lembre-se: endereços temporários não ficam registrados em Informações do cliente." width="16" height="16" /></legend>
<ul>
          <li id="mostrar_tipo" style="display:none;">
                	<label>Tipo de endereço:</label>
                    <input name="tipo_endereco" id="tipo_endereco" class="tipo_endereco" size="40" type="radio" value="1" /><span id="tipo_endereco_texto"> Principal</span>&nbsp;&nbsp;&nbsp;
					<input name="tipo_endereco" id="tipo_endereco2" class="tipo_endereco" size="40" type="radio" value="2" /><span id="tipo_endereco_texto2"> Filial</span>
			</li>
			<li id="filial" style="display:none;">
				<label>Endereço da filial:</label>
				<select id="select_filial" name="select_filial">
					<option value="0">selecionar</option>
				</select>
			</li>
		  <li>
                    <label>Endereço: </label>
                    <input name="endereco" id="endereco" class="input-xlarge" type="text" value="<?php echo set_value('endereco', $atendimento->endereco); ?>" />
        </li>
                <li>
                    <label>Número: </label>
                    <input name="endereco_numero" id="endereco_numero" type="text" class="numero input-mini" value="<?php echo set_value('endereco_numero', $atendimento->endereco_numero); ?>" />
                </li>   
                <li>
                    <label>Complemento: </label>
                    <input name="endereco_complemento" id="endereco_complemento"  type="text" class="complemento input-small" value="<?php echo set_value('endereco_complemento', $atendimento->endereco_complemento); ?>" />
                </li>                                   
                <li>
                    <label>Bairro: </label>
                    <input name="bairro" id="bairro" class="input-medium" type="text" value="<?php echo set_value('bairro', $atendimento->bairro); ?>" />
                </li>  
                <li>
                    <label>Estado: </label>
                    <select name="idestado" id="idestado" valida="true">
                        <option value="">selecione</option>
                        <?php foreach ($estados as $estado) { ?>
                            <option value="<?php echo $estado->idestado; ?>"
                                    <?php echo set_select("idestado", $estado->idestado, ($estado->idestado == $atendimento->idestado)); ?>>
                                        <?php echo $estado->nome; ?>
                            </option>
                        <?php } ?>
                	</select>
                </li>
                <li>
                    <label>Cidade: </label>
                    <select name="idcidade" id="idcidade">

					  <?php foreach ($cidades as $cidade) { ?>
 	                     <option value="<?php echo $cidade->idcidade; ?>"
    	                    <?php echo set_select("idcidade", $cidade->idcidade, ($cidade->idcidade == $atendimento->idcidade)); ?>><?php echo $cidade->nome; ?>
                         </option>
                      <?php }?>  
                    </select>                    
                </li>          
                <?php /*<li>
                    <label>Meios de Transportes: </label>
                    <textarea name="meios_transportes" rows="5" class="span5"><?php echo set_value('meios_transportes', $atendimento->meios_transportes); ?></textarea>
                </li> */?>
                <li>
                    <label>Pontos de referência: </label>
                    <textarea name="pontos_referencias" rows="5" class="span5"><?php echo set_value('pontos_referencias', $atendimento->pontos_referencias); ?></textarea>
                <img src="<?php echo base_url('/assets/images/ico_duvida.png'); ?>" class="tooltip-custom" title="" data-toggle="tooltip" data-original-title="Adicione informações para ajudar sua equipe externa a chegar mais rápido ao local deste atendimento. Útil para situações atípicas, sugerir melhores rotas ou alternativas de transporte público." width="16" height="16" /></li>            
      </ul>
      </fieldset>      
	
      
	  <fieldset>
        <legend>Controle de status</legend>
            <ul>
                <li>
                    <label>Prioridade: </label>
                    <input type="radio" name="prioridade" value="sim" <?php echo set_radio('prioridade', 'sim', ($atendimento->prioridade=='sim')); ?>> Sim&nbsp;&nbsp;
                    <input type="radio" name="prioridade" value="nao" <?php echo set_radio('prioridade', 'nao', ($atendimento->prioridade=='nao')); ?>> Não&nbsp;&nbsp;
                <img src="<?php echo base_url('/assets/images/ico_duvida.png'); ?>" class="tooltip-custom" title="" data-toggle="tooltip" data-original-title="Este atendimento é uma prioridade? Escolhendo “Sim”, ele será marcado com uma estrela na lista de atendimentos." width="16" height="16" /></li>
                <li>
                    <label>Status: </label>
                    <input type="radio" name="status" value="em_andamento" <?php echo set_radio('status', 'em_andamento', ($atendimento->status=='em_andamento')); ?>> Em Andamento&nbsp;&nbsp;
                    <input type="radio" name="status" value="em_espera" <?php echo set_radio('status', 'em_espera', ($atendimento->status=='em_espera')); ?>> Em Espera&nbsp;&nbsp;
                    <input type="radio" name="status" value="em_atraso" <?php echo set_radio('status', 'em_atraso', ($atendimento->status=='em_atraso')); ?>> Em Atraso&nbsp;&nbsp;
                    <input type="radio" name="status" value="finalizado" <?php echo set_radio('status', 'finalizado', ($atendimento->status=='finalizado')); ?>> Finalizado&nbsp;&nbsp;
                    <input type="radio" name="status" value="nao_concluido" <?php echo set_radio('status', 'nao_concluido', ($atendimento->status=='nao_concluido')); ?>> Não Concluído&nbsp;&nbsp;
                <img src="<?php echo base_url('/assets/images/ico_duvida.png'); ?>" class="tooltip-custom" title="" data-toggle="tooltip" data-original-title="Você pode escolher o status que quiser. Lembre-se: atendimentos atrasados aparecem no seu mapa, assim como os atendimentos de hoje." width="16" height="16" /></li> 
                <li>
                    <label>Possui assinatura? </label>
                    <input type="radio" name="tem_contra_senha" value="sim" <?php echo set_radio('tem_contra_senha', 'sim', ($atendimento->tem_contra_senha=='sim')); ?>> Sim&nbsp;&nbsp;
                    <input type="radio" name="tem_contra_senha" value="nao" <?php echo set_radio('tem_contra_senha', 'nao', ($atendimento->tem_contra_senha=='nao')); ?>> Não&nbsp;&nbsp;<img src="<?php echo base_url('/assets/images/ico_duvida.png'); ?>" class="tooltip-custom" title="" data-toggle="tooltip" data-original-title="Esta opção pode ser utilizada caso o atendimento exija uma confirmação local, como a assinatura de um recibo. Escolhendo esta opção, abaixo será exibida um campo para cadastrar a assinatura, que será enviada para o email registrado como contato do cliente." width="16" height="16" /></li> 
                <li class="mostra-contrasenha">
                    <label>Assinatura: </label>
                    <input type="text" name="contra_senha" class="input-small" value="<?php echo set_value('contra_senha', $atendimento->contra_senha); ?>"> <small>Obs: Até 10 caracteres</small>
					<img src="<?php echo base_url('/assets/images/ico_duvida.png'); ?>" class="tooltip-custom" title="" data-toggle="tooltip" data-original-title="Qual será a assinatura? Ela pode contar letras, números e símbolos. O cliente deverá digitá-la no celular do funcionário, no campo 'Assinatura', para validar o check-out da equipe externa." width="16" height="16" />
				</li>  
				<li class="mostra-contrasenha">
                    <label>E-mail: </label>
                    <input type="text" name="emails_assinatura" id="emails_assinatura" class="input-xlarge" value="<?php echo set_value('emails_assinatura', $atendimento->emails_assinatura); ?>"> <small>Obs: Caso queria mais de um email, separar por " ; "</small>
					<img src="<?php echo base_url('/assets/images/ico_duvida.png'); ?>" class="tooltip-custom" title="" data-toggle="tooltip" data-original-title="Para qual e-mail a assinatura será enviada? Lembre-se que o contato deverá estar presente no momento do atendimento para validar o check-out da equipe." width="16" height="16" />
				</li>   
			</ul>
	</fieldset>
	<fieldset>
		<legend>Notificação da equipe externa</legend>
		<ul> 
			   <li>
					<label>Enviar notificação? </label>
					<input type="radio" name="novo_atendimento" value="sim" <?php echo set_radio('novo_atendimento', 'sim'); ?>> Sim&nbsp;&nbsp;
					<input type="radio" name="novo_atendimento" value="nao" <?php echo set_radio('novo_atendimento', 'nao'); ?>> Não&nbsp;&nbsp;<span class="mostra-contrasenha"><img src="<?php echo base_url('/assets/images/ico_duvida.png'); ?>" class="tooltip-custom" title="" data-toggle="tooltip" data-original-title="Ao marcar esta opção como 'Sim', a sua equipe externa receberá uma notificação no aplicativo do celular ao sincronizá-lo com o sistema." width="16" height="16" /></span>
				   
					<p style="font-size:12px; margin-left:146px;">Lembre-se: marcando esta opção como "Sim", a sua equipe externa recebe uma notificação no aplicativo do celular ao sincronizá-lo.</p>
			  </li> 
		</ul>
	</fieldset>  

	  <hr>
    
      <fieldset>
      <ul>
          <li>
              <label></label>
              <button class="btn btn-success btn-large" type="button" onclick="validarUsuarios();">Atualizar</button>
          </li>  
      </ul>
      </fieldset>  
   
	 
  </form>
</div><!--fim da div formstyle-->
<script>
//$('.tooltip-custom').tooltip('show');
// tooltip demo

$('.form-style').tooltip({
  selector: "img[data-toggle=tooltip]",
  placement: "right"
});
$(document).ready(function () {
	$('.form-style').on("mouseover", "img[data-toggle=tooltip]",function() {
		$("*.tooltip-inner").css("color", "#000000");
		$("*.tooltip.top .tooltip-arrow").css("border-top-color", "#CCCCCC");
	});
});

</script>
<script src="<?php echo base_url('assets/js/bootstrap-tooltip.js'); ?>" type="text/javascript"></script>