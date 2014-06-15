        	<?php 
			//print '<pre>';
			//print_r($this->session->userdata('usuario'));
			//print '</pre>';
			?>
			<style>
			.dropdown-menu form:hover{background-color: #f2f2f2;}
			</style>
            
            <div class="configuracao-conta">
			 <div class="btn-group pull-right">
                <button class="btn dropdown-toggle" data-toggle="dropdown"><i class="icon-user"></i> <?php echo $this->session->userdata('usuario')->nome_usuario; ?> <b class="caret"></b></button>
                <ul class="dropdown-menu">
                  <li><a href="<?php print base_url('usuario')?>">Detalhes da conta</a></li>
                  <?php if(($this->session->userdata('usuario')->idpermissao == 1) || ($this->session->userdata('usuario')->idpermissao == 2)){ ?>
                  <li>
                  <form action="<?php echo base_url('usuario/editar'); ?>" method="post" accept-charset="utf-8" name="admin_editar" style="cursor:pointer; margin: 0;">
                     <input type="hidden" name="idusuario" value="<?php echo $this->session->userdata('usuario')->idusuario; ?>" />
                     <input type="hidden" name="edicao_conta" value="sim" />
                  	 <a href="javascript:document.admin_editar.submit();" style="text-decoration:none"><?php print ($this->session->userdata('usuario')->idpermissao == 1) ? "Editar conta" : "Editar perfil";  ?></a>
                  </form>
                  </li>
                  <?php } ?>
                  <li class="divider"></li>
                  <li><a href="<?php echo base_url('acesso/sair'); ?>">Sair</a></li>
                </ul>
              </div>
            </div>  
              
              
        	<div class="clearfix"></div>	
            <div class="span4">
            <a href="<?php 
			if(($this->session->userdata('usuario')->idpermissao == 1) || ($this->session->userdata('usuario')->idpermissao == 2)){
				echo base_url('mapa/visualizar'); 
			}else{
				echo base_url('atendimento'); 
			}
			?>">
            <img src="<?php echo base_url('assets'); ?>/images/veritime-gestao-de-equipe-externa-logo.png" title="Veritime"/>
            </a></div>
            
            <div class="span7 pull-right">
                <ul class="nav nav-pills pull-right">
                    <?php if(($this->session->userdata('usuario')->idpermissao == 1) or ($this->session->userdata('usuario')->idpermissao == 2)){?>
                    <li>
                        <a href="<?php echo base_url('mapa/visualizar'); ?>" class="btn-inverse">Painel</a>            
                    </li>
                    <?php }?>
                    <li class="dropdown">
                        <a class="dropdown-toggle btn-inverse" data-toggle="dropdown" href="#">Atendimentos<b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="<?php echo base_url('atendimento'); ?>">Listar</a></li>
                            <?php if(($this->session->userdata('usuario')->idpermissao == 1) || ($this->session->userdata('usuario')->idpermissao == 2)){ ?>
                            <li><a href="<?php echo base_url('atendimento/cadastro'); ?>">Adicionar</a></li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php if(($this->session->userdata('usuario')->idpermissao == 1) || ($this->session->userdata('usuario')->idpermissao == 2)){ ?>
                    <li>            
                        <a href="<?php echo base_url('relatorio/atendimentos'); ?>" class="btn-inverse">Relatórios</a>            
                    </li>
                    <li class="dropdown">
                        <a class="dropdown-toggle btn-inverse" data-toggle="dropdown" href="#">Usuários<b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="<?php print base_url('usuario/lista')?>">Listar</a></li>
                            <li><a href="<?php echo base_url('usuario/cadastro'); ?>">Adicionar</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a class="dropdown-toggle btn-inverse" data-toggle="dropdown" href="#">Clientes<b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="<?php print base_url('cliente')?>">Listar</a></li>
                            <li><a href="<?php print base_url('cliente/cadastro')?>">Adicionar</a></li>
                        </ul>
                    </li>
                    <?php } ?>
                    <?php /*<li>    
                        <a href="<?php print base_url('faq')?>" class="btn-inverse">FAQ</a>            
                    </li>*/
                    ?>
                    <li>    
                        <a href="<?php print base_url('contato')?>" class="btn-inverse">Contato</a>            
                    </li>
                </ul>   
             </div>    
