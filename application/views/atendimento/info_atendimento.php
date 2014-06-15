<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>Gestão de equipes externas e atendimentos - Veritime</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="<?php echo base_url('assets/css/bootstrap.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/bootstrap-responsive.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/estilo-custom-modal.css'); ?>" rel="stylesheet">

    <!-- jQuery -->
    <script type="text/javascript" src="<?php echo base_url('assets/js/jquery.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/bootstrap.min.js'); ?>"></script>        

    <!--[if lt IE 9]>
    <script>
    document.createElement('header');
    document.createElement('nav');
    document.createElement('section');
    document.createElement('article');
    document.createElement('aside');
    document.createElement('footer');
    document.createElement('hgroup');
    </script>
    <![endif]-->
    <!-- Pulled from http://code.google.com/p/html5shiv/ -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

	<style>
    #content_info { padding: 20px; }
    </style>
</head>

<body>    
<div id="content_info">
<h2>Informações do Atendimento</h2>
<p>&nbsp;</p>
<div id="botoes_exportacao" style="margin-bottom: 18px;">
<button class="btn btn-medium" type="button" id="download_xls">Fazer download para XLS</button>
<button class="btn btn-medium" type="button" id="download_pdf">Fazer download para PDF</button>
</div>
<h4>Cliente: <?php echo $atendimento->nome_cliente; ?></h4>
<table class="table table-striped table-condensed">
	<?php if(!empty($atendimento)){ ?>
		<tr>
			<td style="width: 338px;"><strong>Atendimento</strong></td>
			<td style="font-weight:bold;"><?php echo $atendimento->atendimento; ?></td>
		</tr>
		<tr>
			<td><strong>Status</strong></td>
			<td><?php echo $atendimento->status; ?></td>
		</tr>
		<tr>
			<td><strong>Data e Hora</strong></td>
			<td><?php echo $atendimento->data_hora_atendimento; ?>h</td>
		</tr>
		<tr>
			<td><strong>Duração</strong></td>
			<td><?php echo $atendimento->tempo_estimado; ?>h</td>
		</tr>
		<tr>
			<td><strong>Endereço</strong></td>
			<td><?php echo $atendimento->endereco_completo; ?></td>
		</tr>
		<tr>
			<td><strong>Contato</strong></td>
			<td><?php echo $atendimento->nome_contato_principal; ?></td>
		</tr>
		<?php if(!empty($atendimento->telefone_contato_principal)){ ?>
			<tr>
				<td><strong>Telefone do Contato</strong></td>
				<td><?php echo $atendimento->telefone_contato_principal." "; echo (!empty($atendimento->ramal_contato_principal)) ? "Ramal: ".$atendimento->ramal_contato_principal : ""; ?></td>
			</tr>
        <?php } ?>	
		<?php if(!empty($atendimento->nome_contato_responsavel)){ ?>		
		<tr>
			<td><strong>Responsável</strong></td>
			<td><?php echo $atendimento->nome_contato_responsavel; ?></td>
		</tr>
		<?php } ?>	
		<?php if(!empty($atendimento->telefone_contato_responsavel)){ ?>	
		<tr>
			<td><strong>Telefone do Responsável</strong></td>
			<td><?php echo $atendimento->telefone_contato_responsavel." "; echo (!empty($atendimento->ramal_contato_responsavel)) ? "Ramal: ".$atendimento->ramal_contato_responsavel : ""; ?></td>
		</tr>
		<?php } ?>	
		<tr>
			<td><strong>Descrição</strong></td>
			<td><?php echo $atendimento->descricao; ?></td>
		</tr>
		
	<?php }else{ ?>
		<tr>
			<td colspan="2"><strong>Não foi possivel localizar as informações deste atendimento.</strong></td>
		</tr>
	<?php } ?>
</table>
<?php 
	if(!empty($atendimento->usuarios_atendimento)){ 
		foreach($atendimento->usuarios_atendimento as $funcionario){
?>
			<table class="table table-striped table-condensed" style="margin-top: 40px;">
				<tr>
					<td style="width: 338px;"><strong>Funcionário</strong></td>
					<td style="font-weight:bold;"><?php echo $funcionario->nome; ?></td>
				</tr><tr>
					<td><strong>Check-in</strong></td>
					<td><?php echo $funcionario->data_hora_checkin; ?></td>
				</tr>
				<?php if(!empty($funcionario->endereco_completo_checkin)){ ?>	
				<tr>
					<td><strong>Endereço Check-in</strong></td>
					<td><?php echo $funcionario->endereco_completo_checkin; ?></td>
				</tr>
				<?php } ?>	
				<tr>
					<td><strong>Check-out</strong></td>
					<td><?php echo $funcionario->data_hora_checkout; ?></td>
				</tr>
				<?php if(!empty($funcionario->endereco_completo_checkout)){ ?>	
				<tr>
					<td><strong>Endereço Check-out</strong></td>
					<td><?php echo $funcionario->endereco_completo_checkout; ?></td>
				</tr>
				<?php } ?>	
				<?php if(!empty($funcionario->duracao)){ ?>	
				<tr>
					<td><strong>Duração do Atendimento</strong></td>
					<td><?php echo $funcionario->duracao; ?></td>
				</tr>
				<?php } ?>	
				<?php if(!empty($funcionario->observacao)){ ?>	
				<tr>
					<td><strong>Observação</strong></td>
					<td><?php echo $funcionario->observacao; ?></td>
				</tr>
				<?php } ?>	
			</table>	
<?php 
		}
	}
 ?>

</div>
<form id="form_download" method="post" target="_self">
<input type="hidden" name="conteudo_atendimento" id="conteudo_atendimento" value=''>
</form>
</body>
<script>
jQuery(document).ready(function($){

	var conteudo = $("#content_info").clone();
	conteudo.find("#botoes_exportacao").remove();
	$("#conteudo_atendimento").val(conteudo.html());
	
	$("#download_xls").click(function(){
		$("#form_download").attr("action", "<?php echo base_url("atendimento/inf_download_xls"); ?>");
		$("#form_download").submit();
	});
	$("#download_pdf").click(function(){
		$("#form_download").attr("action", "<?php echo base_url("atendimento/inf_download_pdf"); ?>");
		$("#form_download").submit();
	});
});
</script>
</html>