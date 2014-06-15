<script type="text/javascript">
jQuery(document).ready(function($){

	$(".mostra-contrasenha").hide();
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
	 
// ADD Funcionário

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

<?php
if(!empty($_POST["funcionarios_alocados"])){
	foreach($_POST["funcionarios_alocados"] as $ids){
		$valores = explode("-",$ids);
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
	
// FIM

// ADD Cliente

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
		$valores = explode("-",$ids);
		$nome_cliente = str_replace("_"," ",$valores[0]);
		$ids_cliente = end($valores);
		
?>
		var classeCliente = 'cli_<?php echo $ids_cliente ;?>';
		$("#cliente").show();
		$("#form").append('<input type="hidden" id="cliente_alocado" name="cliente_alocado" value="<?php echo $ids; ?>" class="'+classeCliente+'" />');
		$("#result_clientes").append('<span class="'+classeCliente+'"><?php echo $nome_cliente; ?> <button alt="" class="'+classeCliente+' btn btn-danger btn-mini" style="cursor:pointer;" onclick="remuve(\''+classeCliente+'\',2);">remover</button></span><br class="'+classeCliente+'" />');
		$("#idcliente").val("<?php echo $ids_cliente; ?>").trigger("change");

<?php		
}
?>
	 // Pegar endereço Princial
	 
	 $("#idcliente").change(function(){
		$.post("<?php print base_url(); ?>atendimento/getEnderecoPrincipal/",{idCliente: $(this).val()}, function(j){
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
	<?php
		if(!empty($_POST['tipo_endereco'])){
	?>	
		if(<?php echo $_POST['tipo_endereco']; ?> == 1){
			$("#idcliente").trigger("change");		
		}
		else{
			$("#idcliente").trigger("change");
			$("#tipo_endereco2").trigger("click");
			$.post("<?php print base_url(); ?>atendimento/cadastro/", function(){
				$("#select_filial option[value=<?php echo $_POST['select_filial'] ; ?>]").attr("selected",true).trigger("change");
			});
						
		}
		$("#mostrar_tipo").show();	
	<?php } ?>
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
				$("#idcliente").val('');
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
		}
}

function addUser(){
	$.getJSON("<?php print base_url(); ?>atendimento/getIdNomeUsuario/"+$("#auto").val(),{}, function(j){
					
			if(j != ''){
				var conteudo = $("#form").find('input[name="funcionarios_alocados[]"]').length;
				var value_imput = j.nome.replace(/\s/g,'_')+'-'+j.idusuario;
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
						var id_consulta = $("#form").find('input[name="funcionarios_alocados[]"]')[i].value.split("-");
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

function addCliente(){
	$.getJSON("<?php print base_url(); ?>atendimento/getIdNomeCliente/"+$("#cli").val(),{}, function(j){

			if(j != ''){
				var conteudo = $("#form").find('input[name="cliente_alocado"]').length;
				var value_imput = j.nome.replace(/\s/g,'_')+'-'+j.idcliente;
				var classeCliente = 'cli_'+j.idcliente;
				
				if(conteudo == 0){
						$("#form").append('<input type="hidden" id="cliente_alocado" name="cliente_alocado" value="'+value_imput+'" class="'+classeCliente+'" />');
						$("#result_clientes").html('<span class="'+classeCliente+'">'+j.nome+' <button alt="" class="'+classeCliente+' btn btn-danger btn-mini"  style="cursor:pointer;" onclick="remuve(\''+classeCliente+'\',2);">remover</button></span><br class="'+classeCliente+'" />');
						$("#cliente").show();
						$("#idcliente").val(j.idcliente).trigger("change");
						$("#cli").val("");
				}
				else{
					var encontrado = 0;
					for(var i = 0; i < conteudo; i++){
						var id_consulta = $("#form").find('input[name="cliente_alocado"]')[i].value.split("-");
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
						$("#cliente").show();
						$("#idcliente").val(j.idcliente).trigger("change");
						$("#cli").val("");
					}
				}
			}
			else {
				alert("Adicione um cliente válido.")
			}
	});
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
			conteudo_pessoa = $("#form").find('input[name="funcionarios_alocados[]"]')[l].value.split("-");
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
    <form action="<?php echo base_url('atendimento/insert'); ?>" method="post" id="form" class="well">
    <input type="hidden" id="idcliente" value="" />
      <fieldset>
        <legend>Informações Principais</legend>
            <ul>
              <li>
                    <label>Título: </label>
                    <input name="titulo" class="input-xxlarge"  type="text" value="<?php echo set_value('titulo'); ?>" />
                </li>
                <!--<li>
                    <label>Tag: </label>
                    <input name="idtag" size="40" type="text" class="idtag input-mini" value="<?php echo set_value('tag'); ?>" />
                </li>-->     
                <li id="buscar_cliente">
                    <label>Cliente: </label>
					<input type="text" name="q" id="cli" class="input-xlarge" autocomplete="off" /> <input type="button" id="incluir_cliente" class="btn btn-success btn-small" value="Incluir" style="vertical-align:top" />
                </li>
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
                </li>  
                <li>
                    <label>Data agendada: </label>
                    <input name="data_agendada" id="data_agendada" type="text" class="datepicker data input-small" value="<?php echo set_value('data_agendada'); ?>" />
                </li> 
                <li>
                    <label>Hora agendada: </label>
                    <input name="hora_agendada" id="hora_agendada" type="text" class="hora input-mini" value="<?php echo set_value('hora_agendada'); ?>" />
                </li> 
                <li>
                    <label>Tempo estimado: </label>
                    <input name="tempo_estimado" id="tempo_estimado" type="text" class="hora input-mini" value="<?php echo set_value('tempo_estimado'); ?>" />
                </li>            
                                                     
            </ul>
      </fieldset>
	 
	  <fieldset>
        <legend>Funcionários</legend>
            <ul>
                <li id="buscar">
                    <label>Adicionar funcionário: </label>
					<input type="text" name="q" id="auto" class="input-xlarge" autocomplete="off" /> <input type="button" id="incluir_funcionario" class="btn btn-success btn-small" value="Incluir" style="vertical-align:top" />
                </li>
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
        <legend>Informações de Localização</legend>
            <ul>
            	<li id="mostrar_tipo" style="display:none;">
                	<label>Tipo de endereço:</label>
                    <input name="tipo_endereco" id="tipo_endereco" class="tipo_endereco" size="40" type="radio" value="1" /><span id="tipo_endereco_texto"> Principal</span>&nbsp;&nbsp;&nbsp;
                    <input name="tipo_endereco" id="tipo_endereco2" class="tipo_endereco" size="40" type="radio" value="2" /><span id="tipo_endereco_texto2"> Filial</span>
                </li>
                <li id="filial" style="display:none;">
                	<label>Endereço filial:</label>
                    <select id="select_filial" name="select_filial">
                    	<option value="0">selecionar</option>
                    </select>
                </li>
                <li>
                    <label>Endereço: </label>
                    <input name="endereco" id="endereco" class="input-xlarge" type="text" value="<?php echo set_value('endereco'); ?>" />
                </li>
                <li>
                    <label>Número: </label>
                    <input name="endereco_numero" id="endereco_numero" type="text" class="numero input-mini" value="<?php echo set_value('endereco_numero'); ?>" />
                </li>   
                <li>
                    <label>Complemento: </label>
                    <input name="endereco_complemento" id="endereco_complemento" type="text" class="complemento input-small" value="<?php echo set_value('endereco_complemento'); ?>" />
                </li>                                   
                <li>
                    <label>Bairro: </label>
                    <input name="bairro" id="bairro" class="input-medium" type="text" value="<?php echo set_value('bairro'); ?>" />
                </li>  
                <li>
                    <label>Estado: </label>
                    <select name="idestado" id="idestado" valida="true">
                        <option value="">selecione</option>
                        <?php foreach ($estados as $estado) { ?>
                            <option value="<?php echo $estado->idestado; ?>"
                                    <?php echo set_select("idestado", $estado->idestado); ?>>
                                        <?php echo $estado->nome; ?>
                            </option>
                        <?php } ?>
                	</select>
                </li>
                <li>
                    <label>Cidade: </label>
                    <select name="idcidade" id="idcidade">
                    	<?php if (set_value('idestado')){
							  	foreach ($cidades as $cidade) { ?>
                        			<option value="<?php echo $cidade->idcidade; ?>"
                                    <?php echo set_select("idcidade", $cidade->idcidade, TRUE); ?>><?php echo $cidade->nome; ?></option>
	                    <?php 	} 
							  }else{ ?>
                        <option value="0" disabled="disabled">escolha o estado primeiro</option>
                        <?php }?>
                    </select>                    
                </li>          
                <li>
                    <label>Pontos de Referência: </label>
                    <textarea name="pontos_referencias" id="pontos_referencias" rows="5" class="span5"><?php echo set_value('pontos_referencias'); ?></textarea>
                </li>            
            </ul>
      </fieldset>      


 	  <fieldset>
        <legend>Controle de Status</legend>
            <ul>
                <li>
                    <label>Prioridade: </label>
                    <input type="radio" name="prioridade" value="sim" <?php echo set_radio('prioridade', 'sim'); ?>> Sim&nbsp;&nbsp;
                    <input type="radio" name="prioridade" value="nao" <?php echo set_radio('prioridade', 'nao', TRUE); ?>> Não&nbsp;&nbsp;
                </li>
                <li>
                    <label>Status: </label>
                    <input type="radio" name="status" value="nao_concluido" <?php echo set_radio('status', 'nao_concluido'); ?>> Não Concluído&nbsp;&nbsp;
                    <input type="radio" name="status" value="em_andamento" <?php echo set_radio('status', 'em_andamento'); ?>> Em Andamento&nbsp;&nbsp;
                    <input type="radio" name="status" value="finalizado" <?php echo set_radio('status', 'finalizado'); ?>> Finalizado&nbsp;&nbsp;
                    <input type="radio" name="status" value="em_espera" <?php echo set_radio('status', 'em_espera', TRUE); ?>> Em Espera&nbsp;&nbsp;
                    <input type="radio" name="status" value="em_atraso" <?php echo set_radio('status', 'em_atraso'); ?>> Em Atraso&nbsp;&nbsp;
                </li> 
                <li>
                    <label>Possui contra-senha? </label>
                    <input type="radio" name="tem_contra_senha" value="sim" <?php echo set_radio('tem_contra_senha', 'sim'); ?>> Sim&nbsp;&nbsp;
                    <input type="radio" name="tem_contra_senha" value="nao" <?php echo set_radio('tem_contra_senha', 'nao', TRUE); ?>> Não&nbsp;&nbsp;
                </li>  
                <li class="mostra-contrasenha">
                    <label>Contra-senha: </label>
                    <input type="text" name="contra_senha" class="input-small" maxlength="10" value="<?php echo set_value('contra_senha'); ?>"> Obs: Até 10 caracteres
                </li>  

            </ul>
      </fieldset>    
      
      <hr>      

      <fieldset>
      <ul>
          <li>
              <label></label>
              <button class="btn btn-success btn-large" type="submit" onclick="validarUsuarios();">Cadastrar</button>
          </li>  
      </ul>
      </fieldset> 

  </form>
</div><!--fim da div formstyle-->
