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
	.nav-tabs > li > a { font-size: 17px; }
	.form-style legend { font-size: 15px; font-weight: bold;}
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
      <h2>Relatórios</h2>
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

<div class="well">
<ul class="nav nav-tabs" id="myTab">
  <li class="<?php echo ($aba == "atendimento") ? "active" : ""; ?>"><a href="#atendimento">Relatório Atendimento</a></li>
  <li class="<?php echo ($aba == "funcionario") ? "active" : ""; ?>"><a href="#funcionario">Relatório Funcionário</a></li>
</ul>

<div class="tab-content">
  <div class="tab-pane <?php echo ($aba == "atendimento") ? "active" : ""; ?>" id="atendimento"><?php include("include/relatorio_atendimento.php"); ?></div>
  <div class="tab-pane <?php echo ($aba == "funcionario") ? "active" : ""; ?>" id="funcionario"><?php include("include/relatorio_funcionario.php"); ?></div>
</div>
</div>
</div><!--fim da div formstyle-->

<script>
/*
$(function () {
	$('#myTab #<?php echo $aba; ?>').tab('show');
});
*/
$('#myTab a').click(function (e) {
	e.preventDefault();
	$(this).tab('show');
});
</script>