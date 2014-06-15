<?php
// Transformando a data e hora do banco e do dia atual em mktime para poder fazer as comparações e subtrações de horas.
$formatDataBanco =  explode("-", $atendimento->data_agendada);
$formatHoraBanco = explode(":",$atendimento->hora_agendada);
$mktimeBanco = mktime($formatHoraBanco[0]-1, $formatHoraBanco[1], $formatHoraBanco[2], $formatDataBanco[1], $formatDataBanco[2], $formatDataBanco[0]);
$mktimeAtual = mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y"));
?>

<script>
jQuery(document).ready(function($){

	<?php if($atendimento->tem_contra_senha == 'sim'){?>
		$(".mostra-contrasenha").show();
	<?php }else{?>
		$(".mostra-contrasenha").hide();	
	<?php }?>
	
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
		$valores = explode("-",$ids);
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
		var nome = '<?php echo $funcionario->nome; ?>';
		var id   = <?php echo $funcionario->idusuario; ?>;
		var value_imput = nome.replace(/\s/g,'_')+'-'+id;
		var classeUser = 'user_'+id;
		$("#funcionarios").show();
		$("#form").append('<input type="hidden" name="funcionarios_alocados_antigos[]" value="'+value_imput+'"/>');
		$("#form").append('<input type="hidden" name="funcionarios_alocados[]" value="'+value_imput+'" class="'+classeUser+'" />');
		$("#result_funcionarios").append('<span class="'+classeUser+'"><?php echo $funcionario->nome; ?> <button alt="" class="'+classeUser+' btn btn-danger btn-mini"  style="cursor:pointer;" onclick="remuve(\''+classeUser+'\',<?php echo $atendimento->idatendimento; ?>);">Remover</button></span><br class="'+classeUser+'" />');
<?php		
	}
}
?>

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
		$("#result_clientes").append('<span class="'+classeCliente+'"><?php echo $nome_cliente; ?> <button alt="" class="'+classeCliente+' btn btn-danger btn-mini" style="cursor:pointer;" onclick="remuve(\''+classeCliente+'\',2);">Remover</button></span><br class="'+classeCliente+'" />');		

<?php		
}
else{
	foreach ($clientes as $cliente) {
		if($cliente->idcliente == $atendimento->idcliente){
?>
		var nome = '<?php echo $cliente->nome; ?>';
		var id   = <?php echo $cliente->idcliente; ?>;
		var value_imput = nome.replace(/\s/g,'_')+'-'+id;
		var classeCliente = 'cli_'+id;
		$("#cliente").show();
		$("#form").append('<input type="hidden" id="cliente_alocado" name="cliente_alocado" value="'+value_imput+'" class="'+classeCliente+'" />');
		$("#result_clientes").append('<span class="'+classeCliente+'"><?php echo $cliente->nome; ?> <button alt="" class="'+classeCliente+' btn btn-danger btn-mini" style="cursor:pointer;" onclick="remuve(\''+classeCliente+'\',2);">Remover</button></span><br class="'+classeCliente+'" />');		
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
				var value_imput = j.nome.replace(/\s/g,'_')+'-'+j.idusuario;
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
				var value_imput = j.nome.replace(/\s/g,'_')+'-'+j.idcliente;
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
						$("#result_clientes").html('<span class="'+classeCliente+'">'+j.nome+' <button alt="" class="'+classeCliente+' btn btn-danger btn-mini" style="cursor:pointer;" onclick="remuve(\''+classeCliente+'\',2);">Remover</button></span><br class="'+classeCliente+'" />');		
												
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
			conteudo_pessoa = $("#form").find('input[name="funcionarios_alocados[]"]')[l].value.split("-");
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
      <h2>Editar Atendimento</h2>
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
    <form action="<?php echo base_url('atendimento/update'); ?>" method="post" id="form" class="well">
    <input type="hidden" name="idatendimento" id="idatendimento" value="<?php echo $atendimento->idatendimento; ?>" />
      <fieldset>
        <legend>Informações principais</legend>
            <ul>
                <li>
                    <label>Título: </label>
                    <input name="titulo" class="input-xxlarge" type="text" value="<?php echo set_value('titulo', $atendimento->titulo); ?>" />
                    <img src="/admin/assets/images/ico_duvida.png" class="tooltip-custom" title="Este campo é livre para você identificar o atendimento do seu jeito. Utilize nomes, números ou qualquer outro padrão já adotado na sua empresa." width="16" height="16" /></li>
                <!--<li>
                    <label>Tag: </label>
                    <input name="idtag" size="40" type="text" class="idtag" value="<?php echo set_value('idtag', $atendimento->idtag); ?>" />
                </li>-->                                   
                <li id="buscar_cliente">
                    <label>Cliente: </label>
					<input type="text" name="q" id="cli" class="input-xlarge" autocomplete="off" /> <input type="button" id="incluir_cliente" class="btn btn-success btn-small" value="Incluir" style="vertical-align:top" />
                <img src="/admin/assets/images/ico_duvida.png" width="16" height="16" class="tooltip-custom" title="Basta digitar qualquer letra do nome do cliente que logo surgirá uma listagem para selecioná-lo. Só é possível inserir um cliente. Para substituí-lo basta fazer uma nova seleção/inclusão que a troca será automática." /></li>
                
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
                <img src="/admin/assets/images/ico_duvida.png" class="tooltip-custom" title="Coloque aqui tudo que possa orientar a sua equipe externa na execução do serviço. Podem ser instruções, lembretes, checklists entre outros." width="16" height="16" /></li>  
                <li>
                    <label>Data agendada: </label>
                    <input name="data_agendada" id="data_agendada" type="text" class="datepicker data input-small" value="<?php echo set_value('data_agendada', fdata($atendimento->data_agendada, "/")); ?>" />
                <img src="/admin/assets/images/ico_duvida.png" class="tooltip-custom" title="Selecione no calendário o dia pretendido para o agendamento ou digite a data no formato dd/mm/aaaa. " width="16" height="16" /></li> 
                <li>
                    <label>Hora agendada: </label>
                    <input name="hora_agendada" id="hora_agendada" type="text" class="hora input-mini" value="<?php echo set_value('hora_agendada', $atendimento->hora_agendada); ?>" />
                <img src="/admin/assets/images/ico_duvida.png" class="tooltip-custom" title="Digite a hora de início do atendimento no formato hh:mm. " width="16" height="16" /></li> 
                <li>
                    <label>Tempo estimado: </label>
                    <input name="tempo_estimado" id="tempo_estimado" type="text" class="hora input-mini" value="<?php echo set_value('tempo_estimado', $atendimento->tempo_estimado); ?>" />
                <img src="/admin/assets/images/ico_duvida.png" class="tooltip-custom" title="Digite no formato hh:mm o tempo estimado para este atendimento. " width="16" height="16" /></li>            

                                                     
            </ul>
      </fieldset>


	  <fieldset>
        <legend>Equipe Externa</legend>
            <ul>
                <li id="buscar">
                    <label>Adicionar colaborador: </label>
					<input type="text" name="q" id="auto" class="input-xlarge" autocomplete="off" /> <input type="button" id="incluir_funcionario" class="btn btn-success btn-small" value="Incluir" style="vertical-align:top" />
                <img src="/admin/assets/images/ico_duvida.png" class="tooltip-custom" title="Basta digitar qualquer letra do nome do colaborador que logo sugirá uma listagem para selecioná-lo. Você pode incluir quantos forem necessário para compor a equipe externa deste atendimento." width="16" height="16" /></li>
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
        <legend>Informações de Localização
        <img src="/admin/assets/images/ico_duvida.png" class="tooltip-custom" title="Você pode editar estas informações, mas atenção, elas valerão apenas para este atendimento. Qualquer alteração feita aqui não modificará o cadastro original de localização do cliente." width="16" height="16" /></legend>
<ul>
          <li>
                    <label>Endereço: </label>
                    <input name="endereco" class="input-xlarge" type="text" value="<?php echo set_value('endereco', $atendimento->endereco); ?>" />
        </li>
                <li>
                    <label>Número: </label>
                    <input name="endereco_numero" type="text" class="numero input-mini" value="<?php echo set_value('endereco_numero', $atendimento->endereco_numero); ?>" />
                </li>   
                <li>
                    <label>Complemento: </label>
                    <input name="endereco_complemento"  type="text" class="complemento input-small" value="<?php echo set_value('endereco_complemento', $atendimento->endereco_complemento); ?>" />
                </li>                                   
                <li>
                    <label>Bairro: </label>
                    <input name="bairro" class="input-medium" type="text" value="<?php echo set_value('bairro', $atendimento->bairro); ?>" />
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
                    <label>Pontos de Referência: </label>
                    <textarea name="pontos_referencias" rows="5" class="span5"><?php echo set_value('pontos_referencias', $atendimento->pontos_referencias); ?></textarea>
                <img src="/admin/assets/images/ico_duvida.png" class="tooltip-custom" title="Você pode complementar com dicas temporárias para ajudar a chegada ao local, principalmente em situações atípicas. Alternativas de transportes e melhores rotas são de grande utilidade." width="16" height="16" /></li>            
      </ul>
      </fieldset>      
	
      
	  <fieldset>
        <legend>Controle de Status</legend>
            <ul>
                <li>
                    <label>Prioridade: </label>
                    <input type="radio" name="prioridade" value="sim" <?php echo set_radio('prioridade', 'sim', ($atendimento->prioridade=='sim')); ?>> Sim&nbsp;&nbsp;
                    <input type="radio" name="prioridade" value="nao" <?php echo set_radio('prioridade', 'nao', ($atendimento->prioridade=='nao')); ?>> Não&nbsp;&nbsp;
                <img src="/admin/assets/images/ico_duvida.png" class="tooltip-custom" title="Caso opte por 'Sim', o atendimento será assinalado com uma estrela." width="16" height="16" /></li>
                <li>
                    <label>Status: </label>
                    <input type="radio" name="status" value="nao_concluido" <?php echo set_radio('status', 'nao_concluido', ($atendimento->status=='nao_concluido')); ?>> Não Concluído&nbsp;&nbsp;
                    <input type="radio" name="status" value="em_andamento" <?php echo set_radio('status', 'em_andamento', ($atendimento->status=='em_andamento')); ?>> Em Andamento&nbsp;&nbsp;
                    <input type="radio" name="status" value="finalizado" <?php echo set_radio('status', 'finalizado', ($atendimento->status=='finalizado')); ?>> Finalizado&nbsp;&nbsp;
                    <input type="radio" name="status" value="em_espera" <?php echo set_radio('status', 'em_espera', ($atendimento->status=='em_espera')); ?>> Em Espera&nbsp;&nbsp;
                    <input type="radio" name="status" value="em_atraso" <?php echo set_radio('status', 'em_atraso', ($atendimento->status=='em_atraso')); ?>> Em Atraso&nbsp;&nbsp;
                <img src="/admin/assets/images/ico_duvida.png" class="tooltip-custom" title="Você tem a liberdade de escolher o status que mais se adequar a este atendimento." width="16" height="16" /></li> 
                <li>
                    <label>Possui assinatura? </label>
                    <input type="radio" name="tem_contra_senha" value="sim" <?php echo set_radio('tem_contra_senha', 'sim', ($atendimento->tem_contra_senha=='sim')); ?>> Sim&nbsp;&nbsp;
                    <input type="radio" name="tem_contra_senha" value="nao" <?php echo set_radio('tem_contra_senha', 'nao', ($atendimento->tem_contra_senha=='nao')); ?>> Não&nbsp;&nbsp;<img src="/admin/assets/images/ico_duvida.png" class="tooltip-custom" title="Esta opção pode ser utilizada caso o atendimento exija uma confirmação local, como a assinatura de um recibo. Escolhendo esta opção, abaixo será exibida um campo para cadastrar a assinatura, que será enviada para o email registrado como contato do cliente." width="16" height="16" /></li> 
                <li class="mostra-contrasenha">
                    <label>Assinatura: </label>
                    <input type="text" name="contra_senha" class="input-small" value="<?php echo set_value('contra_senha', $atendimento->contra_senha); ?>"> Obs: Até 10 caracteres
                <img src="/admin/assets/images/ico_duvida.png" class="tooltip-custom" title="Aqui você cadastra a assinatura que é enviada diretamente para o email registrado como contato do cliente. Na finalização do atendimento, o cliente deverá digitá-la no campo 'Assinatura' na tela de checkout no aplicativo do celular da equipe externa." width="16" height="16" /></li>   </ul></fieldset>
            <fieldset>
              <legend>Notificação da Equipe Externa</legend>
               <ul> <li>
                    <label>Enviar notificação? </label>
                    <input type="radio" name="novo_atendimento" value="sim" <?php echo set_radio('novo_atendimento', 'sim'); ?>> Sim&nbsp;&nbsp;
                    <input type="radio" name="novo_atendimento" value="nao" <?php echo set_radio('novo_atendimento', 'nao'); ?>> Não&nbsp;&nbsp;<span class="mostra-contrasenha"><img src="/admin/assets/images/ico_duvida.png" class="tooltip-custom" title="Ao marcar esta opção como 'Sim', a sua equipe externa receberá uma notificação no aplicativo do celular ao sincronizá-lo com o sistema." width="16" height="16" /></span>
                   
                    <p style="font-size:12px; margin-left:146px;">Obs: Ao marcar esta opção como "Sim", a sua equipe externa receberá uma notificação no aplicativo do celular ao sincronizá-lo.</p>
              </li> 
        </ul>
      </fieldset>  

	  <hr>
    
      <fieldset>
      <ul>
          <li>
              <label></label>
              <button class="btn btn-success btn-large" type="submit" onclick="validarUsuarios();">Atualizar</button>
          </li>  
      </ul>
      </fieldset>  
   
	 
  </form>
</div><!--fim da div formstyle-->
