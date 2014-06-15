<?php
// Transformando a data e hora do banco e do dia atual em mktime para poder fazer as comparações e subtrações de horas.
$formatDataBanco =  explode("-", $atendimento->data_agendada);
$formatHoraBanco = explode(":",$atendimento->hora_agendada);
$mktimeBanco = mktime($formatHoraBanco[0]-1, $formatHoraBanco[1], $formatHoraBanco[2], $formatDataBanco[1], $formatDataBanco[2], $formatDataBanco[0]);
$mktimeAtual = mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y"));
?>

<script>
jQuery(document).ready(function($){

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
		$("#result_funcionarios").append('<span class="'+classeUser+'"><?php echo $nome_funcionario; ?> <button alt="" class="'+classeUser+' btn btn-danger btn-mini"  style="cursor:pointer;" onclick="remuve(\''+classeUser+'\',<?php echo $atendimento->idatendimento; ?>);">Remover</button></span><br class="'+classeUser+'" />');		
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
		$("#result_funcionarios").append('<span class="'+classeUser+'">'+nome+' <button alt="" class="'+classeUser+' btn btn-danger btn-mini"  style="cursor:pointer;" onclick="remuve(\''+classeUser+'\',<?php echo $atendimento->idatendimento; ?>);">Remover</button></span><br class="'+classeUser+'" />');
<?php		
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
		$("#form").append('<input type="hidden" id="cliente_alocado" name="cliente_alocado" value="<?php echo $ids; ?>" class="'+classeCliente+'" />');
		$("#result_clientes").append('<span class="'+classeCliente+'"><?php echo $nome_cliente; ?></span><br class="'+classeCliente+'" />');		

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
		$("#form").append('<input type="hidden" id="cliente_alocado" name="cliente_alocado" value="'+value_imput+'" class="'+classeCliente+'" />');
		$("#result_clientes").append('<span class="'+classeCliente+'">'+nome+'</span><br class="'+classeCliente+'" />');		
<?php	
		}
	}
}
?>

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
						$("#result_funcionarios").append('<span class="'+classeUser+'">'+j.nome+' <button alt="" class="'+classeUser+' btn btn-danger btn-mini"  style="cursor:pointer;" onclick="remuve(\''+classeUser+'\','+idAtendimento+');">Remover</button></span><br class="'+classeUser+'" />');		
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
						$("#result_funcionarios").append('<span class="'+classeUser+'">'+j.nome+' <button alt="" class="'+j.idusuario+' btn btn-danger btn-mini"  style="cursor:pointer;" onclick="remuve(\''+classeUser+'\','+idAtendimento+');">Remover</button></span><br class="'+classeUser+'" />');		
						
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
				var value_imput = j.nome.replace(/\s/g,'_')+'|&'+j.idcliente;
				var classeCliente = 'cli_'+j.idcliente;
				
				if(conteudo == 0){
						$("#form").append('<input type="hidden" id="cliente_alocado" name="cliente_alocado" value="'+value_imput+'" class="'+classeCliente+'" />');
						$("#result_clientes").html('<span class="'+classeCliente+'">'+j.nome+' <button alt="" class="'+classeCliente+' btn btn-danger btn-mini" style="cursor:pointer;" onclick="remuve(\''+classeCliente+'\',2);">remover</button></span><br class="'+classeCliente+'" />');							
						
						$("#cliente").show();
						$("#cli").val("");
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
						$("#result_clientes").html('<span class="'+classeCliente+'">'+j.nome+' <button alt="" class="'+classeCliente+' btn btn-danger btn-mini" style="cursor:pointer;" onclick="remuve(\''+classeCliente+'\',2);">remover</button></span><br class="'+classeCliente+'" />');		
												
						$("#cliente").show();
						$("#cli").val("");
					}
				}
			}
			else {
				alert("Adicione um cliente válido.")
			}
	});
}

function remuve(e){
	$("."+e).remove();
	if($("#form").find('input[name="cliente_alocado"]').length == 0){
		$("#cliente").hide();
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
		var idatendimento = $("#idatendimento").val();
		
		for (var l = 0; l < quantidadeUser; l++){
			conteudo_pessoa = $("#form").find('input[name="funcionarios_alocados[]"]')[l].value.split("|&");
			nomes += conteudo_pessoa[0]+"%383";
			idsconsultas += conteudo_pessoa[1]+"%383";
		}

		$.getJSON("<?php print base_url(); ?>atendimento/getValidacaoUser/"+idsconsultas+"/"+dataInformada+"/"+horaInformada+"/"+tempoInformado+"/"+nomes+'/'+idatendimento,{}, function(j){
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
      <h2>Reagendar Atendimento</h2>
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
    <form action="<?php echo base_url('atendimento/update_reagendar'); ?>" method="post" id="form" class="well">
    <input type="hidden" name="idatendimento" id="idatendimento" value="<?php echo $atendimento->idatendimento; ?>" />
      <fieldset>
        <legend>Informações principais</legend>
            <ul>
				<li>
                    <label>Título: </label>
                    <?php print $atendimento->titulo; ?>
                </li>
                         
                <li id="buscar_cliente">
                    <label>Cliente: </label>
					<span id="result_clientes"></span>
                </li>
                <li>
                    <label>Data agendada: </label>
                    <input name="data_agendada" id="data_agendada" type="text" class="datepicker data input-small" value="<?php echo set_value('data_agendada', fdata($atendimento->data_agendada, "/")); ?>" />
                </li> 
                <li>
                    <label>Hora agendada: </label>
                    <input name="hora_agendada" id="hora_agendada" type="text" class="hora input-mini" value="<?php echo set_value('hora_agendada', $atendimento->hora_agendada); ?>" />
                </li> 
                <li>
                    <label>Tempo estimado: </label>
                    <input name="tempo_estimado" id="tempo_estimado" type="text" class="hora input-mini" value="<?php echo set_value('tempo_estimado', $atendimento->tempo_estimado); ?>" />
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
    
   	  <?php if(($this->session->userdata('usuario')->idpermissao == 1) || ($this->session->userdata('usuario')->idpermissao == 2)){ ?>   
      <fieldset>
      <ul>
          <li>
              <label></label>
              <button class="btn btn-success btn-large" type="submit" onclick="validarUsuarios();">Reagendar</button>
        
			  <input type="hidden" name="titulo" value="<?php echo set_value('titulo', $atendimento->titulo); ?>">
              <input type="hidden" name="descricao" value="<?php echo set_value('descricao', $atendimento->descricao); ?>">
              <input type="hidden" name="endereco" value="<?php echo set_value('endereco', $atendimento->endereco); ?>">
			  <input type="hidden" name="endereco_numero" value="<?php echo set_value('endereco_numero', $atendimento->endereco_numero); ?>">
              <input type="hidden" name="endereco_complemento" value="<?php echo set_value('endereco_complemento', $atendimento->endereco_complemento); ?>">
              <input type="hidden" name="bairro" value="<?php echo set_value('bairro', $atendimento->bairro); ?>">

              <input type="hidden" name="latitude" value="<?php echo set_value('latitude', $atendimento->latitude); ?>">
              <input type="hidden" name="longitude" value="<?php echo set_value('longitude', $atendimento->longitude); ?>">
              
              <input type="hidden" name="idestado" value="<?php echo set_value('idestado', $atendimento->idestado); ?>">
              <input type="hidden" name="idcidade" value="<?php echo set_value('idcidade', $atendimento->idcidade); ?>">
              <input type="hidden" name="pontos_referencias" value="<?php echo set_value('pontos_referencias', $atendimento->pontos_referencias); ?>">
              <input type="hidden" name="prioridade" value="<?php echo set_value('prioridade', $atendimento->prioridade); ?>">
              <input type="hidden" name="tem_contra_senha" value="<?php echo set_value('tem_contra_senha', $atendimento->tem_contra_senha); ?>" >                                  
              
              <input type="hidden" name="status" value="em_espera"/>
		      <input type="hidden" name="novo_atendimento" value="sim"/>

          </li>  
      </ul>
      </fieldset>  
      <?php } ?>   
   
	 
  </form>
</div><!--fim da div formstyle-->
