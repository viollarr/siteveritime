<script type="text/javascript">
jQuery(document).ready(function($){
	<?php if($this->session->userdata('usuario')->idpermissao == 3){ ?>
			$(".status").removeAttr("onclick");
	<?php } ?>
});
</script>
<script type="text/javascript">
	jQuery(document).ready(function($){ 
		$(".modal_mapa").colorbox({iframe:true, width:"80%", height:"90%"});
		$(".modal_info").colorbox({iframe:true, width:"60%", height:"90%"});
	}); 

	jQuery(document).ready(function($){
		$('.delete').click(function(){
			var answer = confirm('Tem certeza que deseja excluir este atendimento?');
			return answer;
		}); 
		$('.dispara_notificacao').click(function(){
			var answer = confirm('Tem certeza que deseja enviar uma notificação? Esta notificação será enviada para os celulares dos funcionários alocados neste atendimento');
			return answer;
		}); 		
	}); 

</script>



<script>
  $(function() {
    $( "#from" ).datepicker({
      defaultDate: "+1w",
      changeMonth: false,
      numberOfMonths: 3,
      onClose: function( selectedDate ) {
        $( "#to" ).datepicker( "option", "minDate", selectedDate );
      }
    });
    $( "#to" ).datepicker({
      defaultDate: "+1w",
      changeMonth: false,
      numberOfMonths: 3,
      onClose: function( selectedDate ) {
        $( "#from" ).datepicker( "option", "maxDate", selectedDate );
      }
    });
  });
  </script>
		
<?php
// Include das mensagens dos controllers. Sucesso ou erro
require('application/views/dashboard/mensagem.php');
?>

<div class="pull-left">
    <h2>Atendimentos</h2>
</div>

<div class="clearfix"></div>

<?php if(($this->session->userdata('usuario')->idpermissao == 1) or ($this->session->userdata('usuario')->idpermissao == 2)){?>
<div class="pull-right" style="margin-left: 5px;">
    <a href="<?php echo base_url('mapa/visualizar'); ?>" class="btn btn-inverse btn-large"><i class="icon-map-marker icon-white"></i> Mapa</a>
</div>
<div class="pull-right">
    <a href="<?php echo base_url('atendimento/cadastro'); ?>" class="btn btn-success btn-large"><i class="icon-plus icon-white"></i> Atendimentos</a>
</div>
<?php } ?>

<div class="pesquisa pull-left">
<form name="busca" action="<?php echo base_url('atendimento/busca'); ?>" method="post" style="float:left;">
    <input type="text" name="campo_busca" placeholder="Insira o Nome ou Cliente ou Atendimento" class="input-xlarge">
    <button type="submit" class="btn"><i class="icon-search"></i></button>
    <!--<input name="exibe_finalizados" value="sim" type="checkbox" checked> Exibir Finalizados-->    
</form>
    <div class="filtro_periodo">
        <div class="btn-group">
          <form name="busca" action="<?php echo base_url('atendimento/busca'); ?>" method="post" style="float:left;">
          	<input type="hidden" name="campo_dias" value="1">
          	<button class="btn">Hoje</button>
          </form>
          <form name="busca" action="<?php echo base_url('atendimento/busca'); ?>" method="post" style="float:left;">
          	<input type="hidden" name="campo_dias" value="2">
            <button class="btn">2 dias</button>
          </form>
          <form name="busca" action="<?php echo base_url('atendimento/busca'); ?>" method="post" style="float:left;">
          	<input type="hidden" name="campo_dias" value="7">
            <button class="btn">Semana</button>
          </form>
          <form name="busca" action="<?php echo base_url('atendimento/lista'); ?>" method="post" style="float:left;">
            <button class="btn">Todos</button>
          </form>          
        </div>
        <div class="datas">
        <form name="busca" action="<?php echo base_url('atendimento/busca'); ?>" method="post" style="float:left;">
	        <input type="hidden" name="periodo_data_range" value="true">
            <label for="from">de: </label>
            <input type="text" id="from" name="from" class="input-small data" style="margin-right:5px;">
            <label for="to">até: </label>
            <input type="text" id="to" name="to" class="input-small data">
            <button type="submit" class="btn" style="margin-left:5px;"><i class="icon-search"></i></button>
        </form>    
        </div>
    </div>

</div>

<div class="clearfix"></div>

<table class="table table-condensed">
    <tr>
      <th>ID</th>
      <th></th>
      <th>Cliente</th>
      <th>Atendimento</th>
      <th>Status</th>
      <th>Colaborador</th>
      <th>Data / Hora</th>
      <th>Bairro</th>
      <th></th>
      <?php if(($this->session->userdata('usuario')->idpermissao == 1) or ($this->session->userdata('usuario')->idpermissao == 2)){	?>
      <th style="text-align:right; width: 10%;">Ações</th>
      <?php } ?>
    </tr>        
    <?php 
	if(!empty($atendimentos)){
		foreach ($atendimentos as $atendimento) { 
		
			//'em_andamento','em_espera','em_atraso','nao_concluido','finalizado'
			/***************************************
			ATENÇÃO: Os status agora passarão a ser 5
			Em Andamento - Verde : success
			Em espera - Azul : info
			Em Atraso - Vermelho : important
			Não Concluído - Laranja : warning
			Finalizado / concluído - Cinza : ''
			
			***************************************/
			
			
			if ($atendimento->status == 'em_andamento') {
				$estilo_status = 'success'; 
				$estilo_status_tr = 'success';
			} else if ($atendimento->status == 'em_espera') {	
				$estilo_status = 'info'; 
				$estilo_status_tr = 'info';
			} else if ($atendimento->status == 'em_atraso') {
				$estilo_status = 'important';
				$estilo_status_tr = 'error';
			} else if ($atendimento->status == 'nao_concluido') {
				$estilo_status = 'warning';
				$estilo_status_tr = 'warning';
			}else{ // finalizado
				$estilo_status = 'padrao'; 
				$estilo_status_tr = 'padrao';
			}
	
	?>        
    
			<tr class="<?php print $estilo_status_tr;?>">
			  <td><?php echo $atendimento->idatendimento; ?></td>
			  <td><?php if ($atendimento->prioridade == 'nao'){ ?><i class="icon-star icon-white"></i><?php }else{?><i class="icon-star"></i><?php }?></td>
			  <td><?php echo $atendimento->nome_cliente; ?></td>
			  <td><?php echo $atendimento->titulo; ?></td>
			  <td><span class="label label-<?php print $estilo_status;?>"></span></td>
			  <td><?php print $atendimento->nome_usuario;?></td>
			  <?php 
			  $hora = explode(":", $atendimento->hora_agendada);
			  $hora_agendada = $hora[0].':'.$hora[1].'h';
			  ?>
			  <td><?php echo fdata($atendimento->data_agendada, "/") .' - '.$hora_agendada; ?></td>
			  <td><a href="<?php echo base_url('atendimento/mapa_local/'.$atendimento->idatendimento); ?>" class="modal_mapa"><?php echo $atendimento->bairro; ?></a></td>
			  <td style="width: 58px;">
				  <a class="modal_info" href="<?php echo base_url('atendimento/info_atendimento/'.$atendimento->idatendimento); ?>">
					  <button class="btn btn-mini btn-primary" type="button">Mais info</button>
				  </a>   
			  </td>
			  <?php if(($this->session->userdata('usuario')->idpermissao == 1) or ($this->session->userdata('usuario')->idpermissao == 2)){	?>
				  <td style="text-align:right;">
				  <?php	
					if (($atendimento->status == 'em_atraso') or ($atendimento->status == 'nao_concluido')){
				  ?>
				  <form class="admin_regendar" action="<?php echo base_url('atendimento/reagendar'); ?>" method="post" >
					  <input type="hidden" name="idatendimento" value="<?php echo $atendimento->idatendimento; ?>" />
					  <button class="btn" title="Reagendar"><i class="icon-retweet"></i></button>
				  </form>      
				  <?php }?>  
				  
				  <form class="admin_editar" action="<?php echo base_url('atendimento/editar'); ?>" method="post" >
					  <input type="hidden" name="idatendimento" value="<?php echo $atendimento->idatendimento; ?>" />
					  <button class="btn" title="Editar"><i class="icon-edit"></i></button>
				  </form>
				  <form class="admin_excluir" action="<?php echo base_url('atendimento/excluir'); ?>" method="post">
					  <input type="hidden" name="idatendimento" value="<?php echo $atendimento->idatendimento; ?>" />
					  <button class="btn delete" title="Excluir"><i class="icon-remove"></i></button>
				  </form>      
				  <form class="admin_dispara_notificacao" action="<?php echo base_url('atendimento/enviar_notificacao'); ?>" method="post" >
					  <input type="hidden" name="idatendimento" value="<?php echo $atendimento->idatendimento; ?>" />
					  <button class="btn dispara_notificacao" title="Enviar Notificação"><i class="icon-refresh"></i></button>
				  </form>           
				  </td>                    
				<?php }// if permissao ?>
			</tr>                    
   <?php 

		} //foreach
   }else{
   ?>
		<tr class="padrao">
			<td colspan="10" style="text-align:center; padding-top: 12px;">
				<p style="font-weight:bold;">Nenhum atendimento para a sua equipe no momento.</p>
				<p>Dica: verifique se você cadastrou os funcionários corretamente. <br /> E não se esqueça de relacioná-los aos respectivos gerentes.</p>
			</td>
		</tr>
   <?php
   }
   ?>
</table>

<div class="legendas">                
    <i class="icon-star"></i> Prioridade
    <span class="label label-success">Em Andamento</span>
    <span class="label label-info">Em Espera</span>
    <span class="label label-important">Em Atraso</span>
    <span class="label label-warning">Não Concluído</span>
	<span class="label">Finalizado</span>      
</div>

<?php print $paginacao;?>

