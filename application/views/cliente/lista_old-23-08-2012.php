<h2>Clientes</h2>
<a class="btn_voltar">« voltar</a>

<?php
// Include das mensagens dos controllers. Sucesso ou erro
require('application/views/dashboard/mensagem.php');
?>

<div id="formstyle">
	<table class="table_lista">
    	<tr>
        	<th>Edição</th>
            <th>Nome</th>
            <th>Endereço</th>
            <th>Bairro</th>
            <th>E-mail</th>
        </tr>
        <?php foreach ($clientes as $cliente) { ?>
    	<tr class="<?php echo alternator('even', 'odd'); ?>">
        	<td>
                <form class="admin_editar" action="<?php echo base_url('cliente/editar'); ?>" method="post" >
                    <input type="hidden" name="idcliente" value="<?php echo $cliente->idcliente; ?>" />
                    <input type="image" alt="Editar" class="admin_btn_editar" src="<?php echo base_url('assets/images/ico_editar.png'); ?>" />
                </form>
            </td>
            <td><?php echo $cliente->nome; ?></td>
            <td><?php echo $cliente->endereco.', '.$cliente->endereco_numero.' '.$cliente->endereco_complemento; ?></td>
            <td><?php echo $cliente->bairro; ?></td>
            <td><?php echo '<a href="mailto:'.$cliente->email.'">'.$cliente->email.'</a>'; ?></td>
        </tr>     
        <?php } //foreach  ?>   
    </table>
    <?php echo $paginacao; ?>
</div>



