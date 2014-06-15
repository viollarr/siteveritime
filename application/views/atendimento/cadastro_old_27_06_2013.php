<script type="text/javascript">
jQuery(document).ready(function($){
	
	$(".mostra-contrasenha").hide();
	<?php if(!empty($_POST["tem_contra_senha"])){
				if($_POST["tem_contra_senha"] == 'sim'){?>
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
	
	

	 $("select[name=idestado_text]").change(function(){
		$("#idestado").val($(this).val());
		$("select[name=idcidade_text]").html('<option value="0">Carregando...</option>');
		
		$.post("<?php print base_url(); ?>cidade/getCidades/",
			  {idestado:$(this).val()},
				  function(valor){
					 $("select[name=idcidade_text]").html(valor);
					 $("#idcidade").val($("select[name=idcidade_text]").val());
				  }
			  )
		
	 }); 
	 
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
	foreach($_POST["funcionarios_alocados"] as $ids){
		$valores = explode("|&",$ids);
		$nome_funcionario = str_replace("_"," ",$valores[0]);
		$ids_funcionario = end($valores);
		
?>		var classeUser = 'user_<?php echo $ids_funcionario ;?>';
		$("#funcionarios").show();
		$("#form").append('<input type="hidden" name="funcionarios_alocados[]" value="<?php echo $ids; ?>" class="'+classeUser+'" />');
		$("#result_funcionarios").append('<span class="'+classeUser+'"><?php echo $nome_funcionario; ?> <button alt="" class="'+classeUser+' btn btn-danger btn-mini"  style="cursor:pointer;" onclick="remuve(\''+classeUser+'\',1);">Remover</button></span><br class="'+classeUser+'" />');
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
		$("#result_responsavel").append('<span class="'+classeCliente+'"><?php echo $nome_cliente; ?> <button alt="" class="'+classeCliente+' btn btn-danger btn-mini" style="cursor:pointer;" onclick="remuve(\''+classeCliente+'\',3);">Remover</button></span><br class="'+classeCliente+'" />');		
		$("#incluir_responsavel").val("Substituir");
<?php		
}
?>
	
// FIM

// ADD Cliente
	
	$("#buscar_cliente").on("focus", "#cli",function(e) {
		//utilizo isso para sumir com as divs do auto complete, usado para impedir que fiquem 2 auto completes um sobre o outro
		$(".auto1_conteudo").remove();
		
	});	
	
	$("#buscar_cliente").on("focusin focusout keyup", "#cli",function(e) {
		$("#idcliente").trigger("change");
	
		var texto_cliente = $("#cli").val();
		var idcliente = $("#idcliente").val();
		if((empty(texto_cliente)) && (empty(idcliente))){
			$("#endereco_text").attr("disabled", false);
			$("#endereco").val('');
			$("#endereco_numero_text").attr("disabled", false);
			$("#endereco_numero").val('');
			$("#endereco_complemento_text").attr("disabled", false);
			$("#endereco_complemento").val('');
			$("#bairro_text").attr("disabled", false);
			$("#bairro").val('');
			$("#idestado_text").attr("disabled", false);
			$("#idestado").val('');
			$("#idcidade_text").attr("disabled", false);
			$("#idcidade").val('');
		}
		
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
<?php
if(!empty($_POST["cliente_alocado"])){
		$ids = $_POST["cliente_alocado"];
		$valores = explode("|&",$ids);
		$nome_cliente = str_replace("_"," ",$valores[0]);
		$ids_cliente = end($valores);
		
?>
		var classeCliente = 'cli_<?php echo $ids_cliente ;?>';
		$("#cliente").show();
		//$("#idcliente").val(<?php echo $ids_cliente; ?>);
		$("#form").append('<input type="hidden" id="cliente_alocado" name="cliente_alocado" value="<?php echo $ids; ?>" class="'+classeCliente+'" />');
		$("#result_clientes").append('<span class="'+classeCliente+'"><?php echo $nome_cliente; ?> <button alt="" class="'+classeCliente+' btn btn-danger btn-mini" style="cursor:pointer;" onclick="remuve(\''+classeCliente+'\',2);">remover</button></span><br class="'+classeCliente+'" />');
		$("#idcliente").val("<?php echo $ids_cliente; ?>").trigger("change");
		$("#incluir_cliente").val("Substituir");
<?php		
}
?>
	 // Pegar endereço Princial
	 
	 $("#idcliente").change(function(){
		var tipo = "texto";
		var conteudo_cli = $("#cli").val();
		if(conteudo_cli == ""){
			tipo = "id";
			conteudo_cli = $("#idcliente").val();
		}

		$("#endereco_text").attr("disabled", true);
		$("#endereco").val('');
		$("#endereco_numero_text").attr("disabled", true);
		$("#endereco_numero").val('');
		$("#endereco_complemento_text").attr("disabled", true);
		$("#endereco_complemento").val('');
		$("#bairro_text").attr("disabled", true);
		$("#bairro").val('');
		$("#idestado_text").attr("disabled", true);
		$("#idestado").val('');
		$("#idcidade_text").attr("disabled", true);
		$("#idcidade").val('');
		
		$.post("<?php print base_url(); ?>atendimento/getEnderecoPrincipal/",{conteudo_cli: conteudo_cli, tipo: tipo}, function(j){
			var retorno = j.split("|");
			if(retorno !=''){
				$("#mostrar_tipo").show();
				$("#mostrar_tipos").val('1');
				$("#tipo_endereco").attr("checked",true);
				$("#endereco_text").val(retorno[0]);
				$("#endereco").val(retorno[0]);
				$("#endereco_numero_text").val(retorno[1]);
				$("#endereco_numero").val(retorno[1]);
				$("#endereco_complemento_text").val(retorno[2]);
				$("#endereco_complemento").val(retorno[2]);
				$("#bairro_text").val(retorno[3]);
				$("#bairro").val(retorno[3]);
				$("#pontos_referencias").val(retorno[6]);
				$("#idestado_text").find("option[value='"+retorno[4]+"']").attr("selected",true).trigger('change');
				//$("#idestado_text").attr("disabled", true);
				$("#idestado").val(retorno[4]);
				$.post("<?php print base_url(); ?>atendimento/cadastro/", function(){
					$("#idcidade_text option[value="+retorno[5]+"]").attr("selected",true);
					$("#idcidade").val(retorno[5]);
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
				$("#mostrar_tipos").val('0');
				$("#filial").hide();
				$("#endereco_text").val('');
				$("#endereco").val('');
				$("#endereco_numero_text").val('');
				$("#endereco_numero").val('');
				$("#endereco_complemento_text").val('');
				$("#endereco_complemento").val('');
				$("#bairro_text").val('');	
				$("#bairro").val('');	
				$("#pontos_referencias").val('');
				$("#idestado_text").find("option:eq(0)").attr("selected",true);
				//$("#idestado_text").find("option").remove();
				$("#idestado").val('');	
				$("#idcidade_text").append("<option value='0' disabled='disabled'>escolha o estado primeiro</option>");
				$("#idcidade").val('');	
			}
		});
	});
	
	// Pegar endereço Filial
	 $("#select_filial").change(function(){
	 
		$("#endereco_text").attr("disabled", true);
		$("#endereco").val('');
		$("#endereco_numero_text").attr("disabled", true);
		$("#endereco_numero").val('');
		$("#endereco_complemento_text").attr("disabled", true);
		$("#endereco_complemento").val('');
		$("#bairro_text").attr("disabled", true);
		$("#bairro").val('');
		$("#idestado_text").attr("disabled", true);
		$("#idestado").val('');
		$("#idcidade_text").attr("disabled", true);
		$("#idcidade").val('');
		
		$.post("<?php print base_url(); ?>atendimento/getEnderecoFilial/",{idcliente_filial: $(this).val()}, function(k){
			var retorno = k.split("|");
			if(retorno !=''){
				select_filial();
				$("#tipo_endereco2").attr("checked",true);
				$("#endereco").val(retorno[0]);
				$("#endereco_text").val(retorno[0]);
				$("#endereco_numero").val(retorno[1]);
				$("#endereco_numero_text").val(retorno[1]);
				$("#endereco_complemento").val(retorno[2]);
				$("#endereco_complemento_text").val(retorno[2]);
				$("#bairro").val(retorno[3]);
				$("#bairro_text").val(retorno[3]);
				$("#pontos_referencias").val(retorno[6]);
				$("#idestado_text").find("option[value='"+retorno[4]+"']").attr("selected",true).trigger('change');
				$("#idestado").val(retorno[4]);
				$.post("<?php print base_url(); ?>atendimento/cadastro/", function(){
					$("#idcidade_text option[value="+retorno[5]+"]").attr("selected",true);
					$("#idcidade").val(retorno[5]);
				});
				
			}
			else{
				select_filial();
				$("#tipo_endereco2").attr("checked",true);
				$("#endereco_text").val('');
				$("#endereco").val('');
				$("#endereco_numero_text").val('');
				$("#endereco_numero").val('');
				$("#endereco_complemento_text").val('');
				$("#endereco_complemento").val('');
				$("#bairro_text").val('');
				$("#bairro").val('');
				$("#pontos_referencias").val('');
				$("#idestado_text").find("option:eq(0)").attr("selected",true);
				$("#idestado").val('');
				$("#idcidade_text").find("option").remove();
				$("#idcidade_text").append("<option value='0' disabled='disabled'>escolha o estado primeiro</option>");
				$("#idcidade").val('');
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
		}else if($(this).val() == 3){
			$("#filial").find("option:eq(0)").attr("selected",true);
			$("#filial").hide();
			$("#endereco_text").attr("disabled", false);
			$("#endereco_text").val('');
			$("#endereco").val('');
			$("#endereco_numero_text").attr("disabled", false);
			$("#endereco_numero_text").val('');
			$("#endereco_numero").val('');
			$("#endereco_complemento_text").attr("disabled", false);
			$("#endereco_complemento_text").val('');
			$("#endereco_complemento").val('');
			$("#bairro_text").attr("disabled", false);
			$("#bairro_text").val('');
			$("#bairro").val('');
			$("#idestado_text").attr("disabled", false);
			$("#idestado_text").find("option:eq(0)").attr("selected",true);
			$("#idestado").val('');
			$("#idcidade_text").attr("disabled", false);
			$("#idcidade_text").find("option").remove();
			$("#idcidade").val('');
		}
	});
	<?php
		if(!empty($_POST['tipo_endereco'])){
	?>	
		
		<?php if ((!isset($_POST['mostrar_tipos'])) || ($_POST['mostrar_tipos'] == 1)){ ?>
			if(<?php echo $_POST['tipo_endereco']; ?> == 1){
				$("#idcliente").trigger("change");		
			}
			else if(<?php echo $_POST['tipo_endereco']; ?> == 2){
				$("#idcliente").trigger("change");
				$("#tipo_endereco2").trigger("click");
				$.post("<?php print base_url(); ?>atendimento/cadastro/", function(){
					$("#select_filial option[value=<?php echo $_POST['select_filial'] ; ?>]").attr("selected",true).trigger("change");
				});
							
			}else{
				$("#tipo_endereco3").attr("checked",true);
			}
		$("#mostrar_tipo").show();	
		$("#mostrar_tipos").val('1');	
		<?php } ?>
	<?php } ?>
	
	$(".duplicado").on("keyup change", "", function(){
		var valorcampo = $(this).val();
		var idcampo = $(this).attr("duplicar_valor");
		$("#"+idcampo).val(valorcampo);
	});	

});

function select_filial(){
	if($('#select_filial > option').length > 1){
		$("#filial").show();
	}
}

function remuve(e,tipo){
		$("."+e).remove();
		if(tipo == 1){
			if($("#form").find('input[name="funcionarios_alocados[]"]').length <= 0){
				$("#funcionarios").hide();
			}
		}
		else if(tipo == 2){
			if($("#form").find('input[name="cliente_alocado"]').length <= 0){	
			
				$("#cliente").hide();
				$("#idcliente").val(0);
				$("#mostrar_tipo").hide();
				$("#mostrar_tipos").val('0');
				$("#filial").hide();
				$("#endereco_text").val('').attr("disabled", false);
				$("#endereco").val('');
				$("#endereco_numero_text").val('').attr("disabled", false);
				$("#endereco_numero").val('');
				$("#endereco_complemento_text").val('').attr("disabled", false);
				$("#endereco_complemento").val('');
				$("#bairro_text").val('').attr("disabled", false);
				$("#bairro").val('');
				$("#pontos_referencias").val('');
				$("#idestado_text").attr("disabled", false);		
				$("#idestado_text").find("option:eq(0)").attr("selected",true);		
				$("#idestado").val('');		
				$("#idcidade_text").attr("disabled", false);
				$("#idcidade_text").find("option").remove();
				$("#idcidade_text").append("<option value='0' disabled='disabled'>escolha o estado primeiro</option>");
				$("#idcidade").val('');
				//$("#idcliente").html(0);
			}
			$("#incluir_cliente").val("Incluir");
		}
		else if(tipo == 3){
			$("#incluir_responsavel").val("Incluir");
		}
}

function addUser(){
	$.getJSON("<?php print base_url(); ?>atendimento/getIdNomeUsuario/"+fixedEncodeURIComponent($("#auto").val()),{}, function(j){
					
			if(j != ''){
				var conteudo = $("#form").find('input[name="funcionarios_alocados[]"]').length;
				var value_imput = j.nome.replace(/\s/g,'_')+'|&'+j.idusuario;
				var classeUser = 'user_'+j.idusuario;
				
				if(conteudo == 0){
						$("#form").append('<input type="hidden" name="funcionarios_alocados[]" value="'+value_imput+'" class="'+classeUser+'" />');
						$("#result_funcionarios").append('<span class="'+classeUser+'">'+j.nome+' <button alt="" class="'+classeUser+' btn btn-danger btn-mini"  style="cursor:pointer;" onclick="remuve(\''+classeUser+'\',1);">Remover</button></span><br class="'+classeUser+'" />');
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
						$("#result_funcionarios").append('<span class="'+classeUser+'">'+j.nome+' <button alt="" class="'+j.idusuario+' btn btn-danger btn-mini"  style="cursor:pointer;" onclick="remuve(\''+classeUser+'\',1);">Remover</button></span><br class="'+classeUser+'" />');
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
function fixedEncodeURIComponent(str){
     return encodeURIComponent(str).replace(/[!'()]/g, escape).replace(/\*/g, "%2A");
}

function addCliente(){
	$.getJSON("<?php print base_url(); ?>atendimento/getIdNomeCliente/"+fixedEncodeURIComponent($("#cli").val()),{}, function(j){
			if(j != ''){
				var conteudo = $("#form").find('input[name="cliente_alocado"]').length;
				var value_imput = j.nome.replace(/\s/g,'_')+'|&'+j.idcliente;
				var classeCliente = 'cli_'+j.idcliente;
				
				if(conteudo == 0){
						$("#form").append('<input type="hidden" id="cliente_alocado" name="cliente_alocado" value="'+value_imput+'" class="'+classeCliente+'" />');
						$("#result_clientes").html('<span class="'+classeCliente+'">'+j.nome+' <button alt="" class="'+classeCliente+' btn btn-danger btn-mini"  style="cursor:pointer;" onclick="remuve(\''+classeCliente+'\',2);">remover</button></span><br class="'+classeCliente+'" />');
						$("#cliente").show();
						$("#idcliente").val(j.idcliente).trigger("change");
						$("#cli").val("");
						$("#incluir_cliente").val("Substituir");
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
						$("#result_clientes").html('<span class="'+classeCliente+'">'+j.nome+'  <button alt="" class="'+classeCliente+' btn btn-danger btn-mini"  style="cursor:pointer;" onclick="remuve(\''+classeCliente+'\',2);">remover</button></span><br class="'+classeCliente+'" />');
						$("#filial").hide();
						$("#cliente").show();
						$("#idcliente").val(j.idcliente).trigger("change");
						$("#cli").val("");
						$("#incluir_cliente").val("Substituir");
					}
				}
			}
			else {
				alert("Adicione um cliente válido.")
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
					var classeUser = 'responsavel_'+j.idcliente_contato;

					if(conteudo == 0){
							$("#form").append('<input type="hidden" id="nome_responsavel" name="nome_responsavel" value="'+value_imput+'" class="'+classeUser+'" />');
							$("#result_responsavel").append('<span class="'+classeUser+'">'+j.contato_responsavel+' <button alt="" class="'+classeUser+' btn btn-danger btn-mini"  style="cursor:pointer;" onclick="remuve(\''+classeUser+'\',3);">Remover</button></span><br class="'+classeUser+'" />');		
							$("#emails_assinatura").val(j.contato_email);
							$("#responsavel").show();
							$("#auto1").val("");
							$("#incluir_responsavel").val("Substituir");
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
							alert("Você já adicionou esse funcionário");
						}
						else{
							$("#nome_responsavel").val(value_imput).attr("class",classeUser);
							$("#result_responsavel").html('<span class="'+classeUser+'">'+j.contato_responsavel+' <button alt="" class="'+j.idcliente_contato+' btn btn-danger btn-mini"  style="cursor:pointer;" onclick="remuve(\''+classeUser+'\',3);">Remover</button></span><br class="'+classeUser+'" />');		
							$("#emails_assinatura").val(j.contato_email);
							$("#responsavel").show();
							$("#auto1").val("");
							$("#incluir_responsavel").val("Substituir");
						}
					}
				}
				else {
					alert("Adicione um funcionário válido.")
				}
		});
	}else{
		alert("Adicione um cliente antes de adicionar um contato.");
		$("#cli").focus();
	}
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
		
		for (var l = 0; l < quantidadeUser; l++){
			conteudo_pessoa = $("#form").find('input[name="funcionarios_alocados[]"]')[l].value.split("|&");
			nomes += conteudo_pessoa[0]+"%383";
			idsconsultas += conteudo_pessoa[1]+"%383";
		}

		$.getJSON("<?php print base_url(); ?>atendimento/getValidacaoUser/"+idsconsultas+"/"+dataInformada+"/"+horaInformada+"/"+tempoInformado+"/"+nomes,{}, function(j){
			
			if(j.pass){
				if(confirm(j.texto)){
					$("#form").submit();
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
      <h2>Adicionar Novo Atendimento</h2>
  </div>
  <div class="pull-right">
      <a class="btn btn_voltar">« voltar</a>
  </div>
  
  <div class="clearfix"></div>

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
    <form action="<?php echo base_url('atendimento/insert'); ?>" method="post" id="form" class="well" autocomplete="off" >
    <input type="hidden" id="idcliente" value="" />
      <fieldset>
        <legend>Informações Principais</legend>
            <ul>
                <li>
                    <label>Título: </label>
                    <input name="titulo" class="input-xxlarge"  type="text" value="<?php echo set_value('titulo'); ?>" />
                <img src="<?php echo base_url('/assets/images/ico_duvida.png'); ?>" class="tooltip-custom" title="" data-toggle="tooltip" data-original-title="Este campo é livre para você identificar o atendimento do seu jeito. Utilize nomes, números ou qualquer outro padrão já adotado na sua empresa." width="16" height="16" /></li>
                <!--<li>
                    <label>Tag: </label>
                    <input name="idtag" size="40" type="text" class="idtag input-mini" value="<?php echo set_value('tag'); ?>" />
                </li>-->     
                <li id="buscar_cliente">
                    <label>Cliente: </label>
					<input type="text" name="campo_cliente" id="cli" class="input-xlarge" autocomplete="off" value="<?php echo set_value("campo_cliente"); ?>" /> <input type="button" id="incluir_cliente" class="btn btn-success btn-small" value="Incluir" style="vertical-align:top" />
                <img src="<?php echo base_url('/assets/images/ico_duvida.png'); ?>" width="16" height="16" class="tooltip-custom" title="" data-toggle="tooltip" data-original-title="Basta digitar qualquer letra do nome do cliente que logo surgirá uma listagem para selecioná-lo. Só é possível inserir um cliente. Para substituí-lo basta fazer uma nova seleção/inclusão que a troca será automática." /></li>
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
                    <textarea name="descricao" rows="5" class="span5"><?php echo set_value('descricao'); ?></textarea>
                <img src="<?php echo base_url('/assets/images/ico_duvida.png'); ?>" class="tooltip-custom" title="" data-toggle="tooltip" data-original-title="Coloque aqui tudo que possa orientar a sua equipe externa na execução do serviço. Podem ser instruções, lembretes, checklists entre outros." width="16" height="16" /></li>  
                <li>
                    <label>Data agendada: </label>
                    <input name="data_agendada" id="data_agendada" type="text" class="datepicker data input-small" value="<?php echo set_value('data_agendada'); ?>" />
                <img src="<?php echo base_url('/assets/images/ico_duvida.png'); ?>" class="tooltip-custom" title="" data-toggle="tooltip" data-original-title="Selecione no calendário o dia pretendido para o agendamento ou digite a data no formato dd/mm/aaaa. " width="16" height="16" /></li> 
                <li>
                    <label>Hora agendada: </label>
                    <input name="hora_agendada" id="hora_agendada" type="text" class="hora input-mini" value="<?php echo set_value('hora_agendada'); ?>" />
                <img src="<?php echo base_url('/assets/images/ico_duvida.png'); ?>" class="tooltip-custom" title="" data-toggle="tooltip" data-original-title="Digite a hora de início do atendimento no formato hh:mm. " width="16" height="16" /></li> 
                <li>
                    <label>Duração: </label>
                    <input name="tempo_estimado" id="tempo_estimado" type="text" class="hora input-mini" value="<?php echo set_value('tempo_estimado'); ?>" />
                <img src="<?php echo base_url('/assets/images/ico_duvida.png'); ?>" class="tooltip-custom" title="" data-toggle="tooltip" data-original-title="Digite no formato hh:mm o tempo estimado para este atendimento. " width="16" height="16" /></li>            
                                                     
            </ul>
      </fieldset>
	  <fieldset>
        <legend>Responsável (cliente)</legend>
            <ul>
                <li id="buscar_responsavel">
                    <label style="width: 150px;">Adicionar responsável: </label>
					<input type="text" name="campo_responsavel" id="auto1" class="input-xlarge" autocomplete="off" value="<?php echo set_value("campo_responsavel"); ?>" /> <input type="button" id="incluir_responsavel" class="btn btn-success btn-small" value="Incluir" style="vertical-align:top" />
                <img src="<?php echo base_url('/assets/images/ico_duvida.png'); ?>" class="tooltip-custom" title="" data-toggle="tooltip" data-original-title="Basta digitar qualquer letra do nome do colaborador que logo sugirá uma listagem para selecioná-lo." width="16" height="16" /></li>
                <li id="responsavel" style="display:none;">
                	<div style="width:164px; min-height: 32px; float:left;">
                    	<label style="width: 154px;">Responsavel Escolhido: </label>
                    </div>
                    <div style="width:auto; float:left;">
						<span id="result_responsavel"></span>
                    </div>
                </li>
            </ul>
      </fieldset>   
	  <fieldset>
        <legend>Equipe Externa</legend>
        <ul>
                <li id="buscar">
                    <label style="width: 150px;">Adicionar colaborador: </label>
					<input type="text" name="campo_funcionario" id="auto" class="input-xlarge" autocomplete="off" value="<?php echo set_value("campo_funcionario"); ?>" /> <input type="button" id="incluir_funcionario" class="btn btn-success btn-small" value="Incluir" style="vertical-align:top" />
                <img src="<?php echo base_url('/assets/images/ico_duvida.png'); ?>" class="tooltip-custom" title="" data-toggle="tooltip" data-original-title="Basta digitar qualquer letra do nome do colaborador que logo sugirá uma listagem para selecioná-lo. Você pode incluir quantos forem necessário para compor a equipe externa deste atendimento." width="16" height="16" /></li>
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

	  <hr>

            
	  <fieldset>
        <legend>Informações de Localização <img src="<?php echo base_url('/assets/images/ico_duvida.png'); ?>" class="tooltip-custom" title="" data-toggle="tooltip" data-original-title="Estas informações são carregadas diretamente do cadastro do cliente. Você pode editá-las, mas atenção, elas valerão apenas para este atendimento. Qualquer alteração feita aqui não modificará o cadastro original de localização do cliente" width="16" height="16" /></legend>
        
<ul>
       	  <li id="mostrar_tipo" style="display:none;">
					<input type="hidden" id="mostrar_tipos" name="mostrar_tipos" value="" />
                	<label>Tipo de endereço:</label>
                    <input name="tipo_endereco" id="tipo_endereco" class="tipo_endereco" size="40" type="radio" value="1" /><span id="tipo_endereco_texto"> Principal</span>&nbsp;&nbsp;&nbsp;
                    <input name="tipo_endereco" id="tipo_endereco2" class="tipo_endereco" size="40" type="radio" value="2" /><span id="tipo_endereco_texto2"> Filial</span>&nbsp;&nbsp;&nbsp;
					<input name="tipo_endereco" id="tipo_endereco3" class="tipo_endereco" size="40" type="radio" value="3" /><span id="tipo_endereco_texto3"> Endereço Temporário</span>
                </li>
                <li id="filial" style="display:none;">
                	<label>Endereço filial:</label>
                    <select id="select_filial" name="select_filial">
                    	<option value="0">selecionar</option>
                    </select>
                </li>
                <li>
                    <label>Endereço: </label>
                    <input name="endereco_text" id="endereco_text" class="input-xlarge duplicado" type="text" value="<?php echo set_value('endereco'); ?>" duplicar_valor="endereco" />
					<input type="hidden" id="endereco" name="endereco" value="<?php echo set_value('endereco'); ?>" />
                </li>
                <li>
                    <label>Número: </label>
                    <input name="endereco_numero_text" id="endereco_numero_text" type="text" class="numero input-mini duplicado" value="<?php echo set_value('endereco_numero'); ?>" duplicar_valor="endereco_numero" />
					<input type="hidden" id="endereco_numero" name="endereco_numero" value="<?php echo set_value('endereco_numero'); ?>" />
                </li>   
                <li>
                    <label>Complemento: </label>
                    <input name="endereco_complemento_text" id="endereco_complemento_text" type="text" class="complemento input-small duplicado" value="<?php echo set_value('endereco_complemento'); ?>" duplicar_valor="endereco_complemento" />
					<input type="hidden" id="endereco_complemento" name="endereco_complemento" value="<?php echo set_value('endereco_complemento'); ?>" />
                </li>                                   
                <li>
                    <label>Bairro: </label>
                    <input name="bairro_text" id="bairro_text" class="input-medium duplicado" type="text" value="<?php echo set_value('bairro'); ?>" duplicar_valor="bairro" />
					<input type="hidden" id="bairro" name="bairro" value="<?php echo set_value('bairro'); ?>" />
                </li>  
                <li>
                    <label>Estado: </label>
                    <select name="idestado_text" id="idestado_text" valida="true" duplicar_valor="idestado" class="duplicado">
                        <option value="">selecione</option>
                        <?php foreach ($estados as $estado) { ?>
                            <option value="<?php echo $estado->idestado; ?>"
                                    <?php echo set_select("idestado", $estado->idestado); ?>>
                                        <?php echo $estado->nome; ?>
                            </option>
                        <?php } ?>
                	</select>
					<input type="hidden" id="idestado" name="idestado" value="<?php echo set_value('idestado'); ?>" />
                </li>
                <li>
                    <label>Cidade: </label>
                    <select name="idcidade_text" id="idcidade_text" duplicar_valor="idcidade" class="duplicado">
                    	<?php if (set_value('idestado')){
							  	foreach ($cidades as $cidade) { ?>
                        			<option value="<?php echo $cidade->idcidade; ?>"
                                    <?php echo set_select("idcidade", $cidade->idcidade, TRUE); ?>><?php echo $cidade->nome; ?></option>
	                    <?php 	} 
							  }else{ ?>
                        <option value="0" disabled="disabled">escolha o estado primeiro</option>
                        <?php }?>
                    </select>     
					<input type="hidden" id="idcidade" name="idcidade" value="<?php echo set_value('idcidade'); ?>" />					
                </li>          
                <li>
                  <label>Pontos de Referência: </label>
                    <textarea name="pontos_referencias" id="pontos_referencias" rows="5" class="span5"><?php echo set_value('pontos_referencias'); ?></textarea>
                <img src="<?php echo base_url('/assets/images/ico_duvida.png'); ?>" class="tooltip-custom" title="" data-toggle="tooltip" data-original-title="Você pode complementar com dicas temporárias para ajudar a chegada ao local, principalmente em situações atípicas. Alternativas de transportes e melhores rotas são de grande utilidade." width="16" height="16" /></li>            
            </ul>
      </fieldset>      


 	  <fieldset>
        <legend>Controle de Status</legend>
            <ul>
                <li>
                    <label>Prioridade: </label>
                    <input type="radio" name="prioridade" value="sim" <?php echo set_radio('prioridade', 'sim'); ?>> Sim&nbsp;&nbsp;
                    <input type="radio" name="prioridade" value="nao" <?php echo set_radio('prioridade', 'nao', TRUE); ?>> Não&nbsp;&nbsp;<img src="<?php echo base_url('/assets/images/ico_duvida.png'); ?>" class="tooltip-custom" title="" data-toggle="tooltip" data-original-title="Caso opte por 'Sim', o atendimento será assinalado com uma estrela." width="16" height="16" /></li>
                <li>
                    <label>Status: </label>
                    <input type="radio" name="status" value="nao_concluido" <?php echo set_radio('status', 'nao_concluido'); ?>> 
                    Não Concluído &nbsp;&nbsp;
                    <input type="radio" name="status" value="em_andamento" <?php echo set_radio('status', 'em_andamento'); ?>> Em Andamento&nbsp;&nbsp;
                    <input type="radio" name="status" value="finalizado" <?php echo set_radio('status', 'finalizado'); ?>> Finalizado&nbsp;&nbsp;
                    <input type="radio" name="status" value="em_espera" <?php echo set_radio('status', 'em_espera', TRUE); ?>> Em Espera&nbsp;&nbsp;
                    <input type="radio" name="status" value="em_atraso" <?php echo set_radio('status', 'em_atraso'); ?>> Em Atraso&nbsp;&nbsp;
                <img src="<?php echo base_url('/assets/images/ico_duvida.png'); ?>" class="tooltip-custom" title="" data-toggle="tooltip" data-original-title="Todo novo atendimento é marcado como 'Em Espera' de forma automática, mas você tem a liberdade de escolher o status que mais se adequar a este atendimento." width="16" height="16" /></li> 
                <li>
                    <label>Possui assinatura? </label>
                    <input type="radio" name="tem_contra_senha" value="sim" <?php echo set_radio('tem_contra_senha', 'sim'); ?>> Sim&nbsp;&nbsp;
                    <input type="radio" name="tem_contra_senha" value="nao" <?php echo set_radio('tem_contra_senha', 'nao', TRUE); ?>> Não&nbsp;&nbsp;
                <img src="<?php echo base_url('/assets/images/ico_duvida.png'); ?>" class="tooltip-custom" title="" data-toggle="tooltip" data-original-title="Esta opção pode ser utilizada caso o atendimento exija uma confirmação local, como a assinatura de um recibo. Escolhendo esta opção, abaixo será exibida um campo para cadastrar a assinatura, que será enviada para o email registrado como contato do cliente." width="16" height="16" /></li>  
                <li class="mostra-contrasenha">
                    <label>Assinatura: </label>
                    <input type="text" name="contra_senha" class="input-small" maxlength="10" value="<?php echo set_value('contra_senha'); ?>"> <small>Obs: Até 10 caracteres</small>
                <img src="<?php echo base_url('/assets/images/ico_duvida.png'); ?>" class="tooltip-custom" title="" data-toggle="tooltip" data-original-title="Aqui você cadastra a assinatura que é enviada diretamente para o email registrado como contato do cliente. Na finalização do atendimento, o cliente deverá digitá-la no campo 'Assinatura' na tela de checkout no aplicativo do celular da equipe externa." width="16" height="16" /></li>  
				<li class="mostra-contrasenha">
                    <label>E-mail: </label>
                    <input type="text" name="emails_assinatura" id="emails_assinatura" class="input-xlarge" value="<?php echo set_value('emails_assinatura'); ?>"> <small>Obs: Caso queria mais de um email, separar por " ; "</small>
					<img src="<?php echo base_url('/assets/images/ico_duvida.png'); ?>" class="tooltip-custom" title="" data-toggle="tooltip" data-original-title="Aqui você cadastra a assinatura que é enviada diretamente para o email registrado como contato do cliente. Na finalização do atendimento, o cliente deverá digitá-la no campo 'Assinatura' na tela de checkout no aplicativo do celular da equipe externa." width="16" height="16" />
				</li>   
            </ul>
      </fieldset>    
      
      <hr>      

      <fieldset>
      <ul>
          <li>
              <label></label>
              <button class="btn btn-success btn-large" type="button" onclick="validarUsuarios();">Cadastrar</button>
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
