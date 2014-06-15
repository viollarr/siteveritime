<?php
// Include das mensagens dos controllers. Sucesso ou erro
require('application/views/dashboard/mensagem.php');
?>

<div class="pull-left">
    <h2>Clientes</h2>
</div>

<div class="clearfix"></div>

<div class="pesquisa pull-left">
<form name="busca" action="<?php echo base_url('cliente/busca'); ?>" method="post">
    <input type="text" name="campo_busca" placeholder="Insira o nome do Cliente" class="input-xxlarge">
    <button type="submit" class="btn"><i class="icon-search"></i></button>
    <!--<input name="exibe_finalizados" value="sim" type="checkbox" checked> Exibir Finalizados-->    
</form>
</div>
<table class="table table-condensed table-striped">
    <tr>
        
        <th>Nome</th>
        <th>Endereço</th>
        <th>Bairro</th>
        <th>Cidade/UF</th>
        <th>E-mail</th>
        <th style="text-align:center;">Status</th>
		<th style="text-align:right;">Ações</th>        
    </tr>        
 	<?php foreach ($clientes as $cliente) { ?>
            <tr>
                <td><?php echo $cliente->nome; ?></td>
                
                <?php
					$endereco_completo='';
					$endereco_completo.=trim($cliente->endereco);
			
					if($cliente->endereco_numero){
						$endereco_completo.= ', '.$cliente->endereco_numero;
					}
					if($cliente->endereco_complemento){
						$endereco_completo.= ', '.$cliente->endereco_complemento;
					}
				?>
                
                <td><?php echo $endereco_completo; ?></td>
                <td><?php echo $cliente->bairro; ?></td>
                <td><?php echo $cliente->nomecidade.'/'.$cliente->uf; ?></td>
                <td><?php echo '<a href="mailto:' . $cliente->email . '">' . $cliente->email . '</a>'; ?></td>
                <td style="text-align:center;">
                    <?php
                    switch ($cliente->ativo) {
                        case "sim";
                            $alt_status = 'Ativo';
							$estilo_status = 'success';
                            break;
                        case "nao";
							$alt_status = 'Não Ativo';	
                            $estilo_status = '';
                            break;
                    } // switch
                    ?>
                    <span title="<?php print $alt_status;?>" class="label label-<?php print $estilo_status;?>"><?php print $alt_status;?></span>
                </td>
                <td style="text-align:right;">
                  <form class="admin_editar" action="<?php echo base_url('cliente/editar'); ?>" method="post" >
                      <input type="hidden" name="idcliente" value="<?php echo $cliente->idcliente; ?>" />
                      <button class="btn" title="Editar"><i class="icon-edit"></i></button>
                  </form>                
                </td>                
            </tr>     
     <?php } //foreach  ?> 
</table>

<?php echo $paginacao; ?>

<!--<div class="pagination">
  <ul>
    <li><a href="#"><</a></li>
    <li><a href="#">1</a></li>
    <li><a href="#">2</a></li>
    <li><a href="#">3</a></li>
    <li><a href="#">4</a></li>
    <li><a href="#">></a></li>
  </ul>
</div>-->





