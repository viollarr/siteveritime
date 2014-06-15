<?php
// Include das mensagens dos controllers. Sucesso ou erro
require('application/views/dashboard/mensagem.php');
?>

<div class="pull-left">
    <h2>Usuários</h2>
</div>

<div class="clearfix"></div>


<table class="table table-condensed table-striped">
    <tr>
        
        <th>Nome</th>
        <th>E-mail (login)</th>
        <th>Celular</th>
        <th>Perfil</th>
        <th style="text-align:center;">Status</th>
		<th style="text-align:right;">Edição</th>  
    </tr>
    <?php foreach ($usuarios as $usuario) { ?>
    <tr class="<?php echo alternator('even', 'odd'); ?>">
        <td><?php echo $usuario->nome; ?></td>
        <td><?php echo $usuario->email; ?></td>
        <td><?php echo $usuario->celular; ?></td>
        <td><?php echo $usuario->perfil; ?></td>
        <td style="text-align:center;">
          <?php
                switch ($usuario->ativo) {
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
            <form class="admin_editar" action="<?php echo base_url('usuario/editar'); ?>" method="post" >
                <input type="hidden" name="idusuario" value="<?php echo $usuario->idusuario; ?>" />
                <button class="btn" title="Editar"><i class="icon-edit"></i></button>
            </form>
        </td>            
    </tr>     
    <?php } //foreach  ?>   
</table>
    
<?php echo $paginacao; ?>



