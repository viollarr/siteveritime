<div class="pull-left">
      <h2>Detalhes da Conta</h2>
  </div>
  <div class="pull-right">
      <a class="btn btn_voltar">« voltar</a>
  </div>
  
  <div class="clearfix"></div>

<div class="form-style">
	
    <form id="form" class="well">
		<?php /*<input type="hidden" name="idusuario" value="<?php print $this->session->userdata('usuario')->idusuario;?>" />*/?>
        <fieldset>
        <legend>Informações do usuário</legend>
        	<ul>
            	<li>
                    <label><strong>Nome: </strong></label> 
                    <?php echo $usuario->nome_usuario;?>
                </li>
            	<li>
                    <label><strong>E-mail: </strong></label> 
                    <?php echo $usuario->email;?>
                </li>
            	<li>
                    <label><strong>Celular: </strong></label> 
                    <?php echo $usuario->celular;?>
                </li>
            	<li>
                    <label><strong>Status: </strong></label> 
                     <?php
					  switch ($status_usuario) {
						  case "Ativo";
							  $alt_status = 'Ativo';
							  $estilo_status = 'success';
							  break;
						  case "Inativo";
							  $alt_status = 'Não Ativo';	
							  $estilo_status = '';
							  break;
					  } // switch
					  ?>
                    <span title="<?php print $alt_status;?>" class="label label-<?php print $estilo_status;?>"><?php print $status_usuario;?></span>
                </li>
            	<li>
                    <label><strong>Perfil: </strong></label> 
					<?php echo $usuario->perfil;?>
                </li>                  
            </ul>
 
        </fieldset>        
<?php if($this->session->userdata('usuario')->idpermissao == 1){?>
		<fieldset>
        <legend>Informações da Conta</legend>
        	<ul>
            	<li>
                    <label><strong>Empresa: </strong></label> 
					<?php echo $usuario->nome_empresa;?>
                </li> 
            	<li>
                    <label><strong>Plano: </strong></label> 
                    <?php print $plano;?>
                </li>                
            	<li>
                    <label><strong>Criação da conta: </strong></label> 
                    <?php print $usuario->data_criado_empresa;?>&nbsp;&nbsp; 
					<?php 
						if($plano == 'Teste'){
							if($diferenca_dias > 30){ 
								print ' <strong>Esta conta já estorou o período de 30 dias de teste</strong>';
							}else{ 
								$restantes = 30-$diferenca_dias;	
								if ($restantes == 0){
									print '<strong>O plano de Teste expira hoje</strong>';
								}elseif ($restantes == 1){
									print '<strong>1 dia restante para o plano de teste expirar</strong>';
								}
								else{
									print '<strong>'.$restantes.' dias restantes para o plano de teste expirar</strong>';
								}
							}
						}
					?>
                </li>                
            </ul>
        </fieldset>                
        <?php } ?>
	</form>
</div><!--fim da div formstyle-->

