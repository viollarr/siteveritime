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

<?php echo $onload; ?>
<?php echo $map; ?>

<?php
	echo "<div style='display:none;'>";
	echo $sidebar; 
	echo "</div>";
?>    
</div>
<p>Obs: No mapa acima são sinalizados somente os atendimentos do dia e a última ação de cada funcionário (checkin ou checkout)</p>

<div class="clearfix"></div>





