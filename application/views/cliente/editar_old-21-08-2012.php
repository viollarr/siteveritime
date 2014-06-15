<script>
    jQuery(document).ready(function($){

        $("select[name=idestado]").change(function(){
            $("select[name=idcidade]").html('<option value="0">Carregando...</option>');
		
            $.post("<?php print base_url(); ?>cidade/getCidades/",
            {idestado:$(this).val()},
            function(valor){
                $("select[name=idcidade]").html(valor);
            }
        )
		
        });           
    });
</script>

<h2>Clientes » Edição</h2>
<a class="btn_voltar">« voltar</a>

<?php
// Include das mensagens dos controllers. Sucesso ou erro
require('application/views/dashboard/mensagem.php');
?>

<?php
// Em caso de erro no preenchimento do formulário, os erros irão aparecer aqui.
if (validation_errors()) :
    ?>
    <div id="erros_validacao_form">
        <ul>
            <?php echo validation_errors('<li>', '</li>'); ?>
        </ul>
    </div>
<?php endif; ?>
<div id="formstyle">
    <form action="<?php echo base_url('cliente/update'); ?>" method="post" id="form">
        <input type="hidden" name="idcliente" value="<?php echo $cliente->idcliente; ?>" />
        <fieldset>
            <legend>Principal</legend>
            <ul>
                <li>
                    <label for="nome">Nome: </label>
                    <input name="nome" id="nome" size="40" type="text" value="<?php echo set_value('nome', $cliente->nome); ?>"  />
                </li>
            </ul>
        </fieldset>        

        <fieldset>
            <legend>Informações de Identificação</legend>
            <ul>
                <li>
                    <label for="razao_social">Razão Social: </label>
                    <input name="razao_social" id="razao_social" size="40" type="text" value="<?php echo set_value('razao_social', $cliente->razao_social); ?>" />
                </li>   
                <li>
                    <label for="cnpj">CNPJ: </label>
                    <input name="cnpj" id="cnpj" size="40" type="text" class="cnpj" value="<?php echo set_value('cnpj', $cliente->cnpj); ?>" />
                </li> 
                <li>
                    <label for="endereco">Endereço: </label>
                    <input name="endereco" id="endereco" type="text" size="40" value="<?php echo set_value('endereco', $cliente->endereco); ?>"/>
                </li>
                <li>
                    <label for="endereco_numero">Número: </label>
                    <input name="endereco_numero" id="endereco_numero" type="text" size="4" value="<?php echo set_value('endereco_numero', $cliente->endereco_numero); ?>"/>
                </li>
                <li>
                    <label for="endereco_complemento">Complemento: </label>
                    <input name="endereco_complemento" id="endereco_complemento" type="text" size="15" value="<?php echo set_value('endereco_complemento', $cliente->endereco_complemento); ?>"/>
                </li>
                <li>
                    <label for="bairro">Bairro: </label>
                    <input name="bairro" id="bairro" type="text" size="15" value="<?php echo set_value('bairro', $cliente->bairro); ?>"/>
                </li>                
                <li>
                    <label for="idestado">Estado: </label>
                    <select name="idestado" id="idestado" valida="true">
                        <option value="">selecione</option>
                        <?php foreach ($estados as $estado) { ?>
                            <option value="<?php echo $estado->idestado; ?>"
                                    <?php echo set_select("idestado", $estado->idestado, ($estado->idestado == $cliente->idestado)); ?>>
                                        <?php echo $estado->nome; ?>
                            </option>
                        <?php } ?>
                    </select>
                </li>
                <li>
                    <label for="idcidade">Cidade: </label>
                    <select name="idcidade" id="idcidade">
                        <?php foreach ($cidades as $cidade) { ?>
                            <option value="<?php echo $cidade->idcidade; ?>"
                                    <?php echo set_select("idcidade", $cidade->idcidade, ($cidade->idcidade == $cliente->idcidade)); ?>><?php echo $cidade->nome; ?></option>
                                <?php }
                                ?>
                    </select>      
                </li>  
            </ul>
        </fieldset>

        <fieldset>
            <legend>Contatos</legend>
            <ul>
                <li>
                    <label for="telefone">Telefone: </label>
                    <input name="telefone" id="telefone" size="40" type="text" class="telefone" value="<?php echo set_value('telefone', $cliente->telefone); ?>" />
                </li>
                <li>
                    <label for="celular">Celular: </label>
                    <input name="celular" id="celular" size="40" type="text" class="celular" value="<?php echo set_value('celular', $cliente->celular); ?>" />
                </li>
                <li>
                    <label for="email">E-mail: </label>
                    <input name="email" id="email" size="40" type="email" value="<?php echo set_value('email', $cliente->email); ?>" />
                </li>     
            </ul>
        </fieldset>        

        <fieldset>
            <legend><label for="observacao">Observações</label></legend>
            <ul>
                <li>
                    <textarea name="observacao" id="observacao" style="width: 675px; height: 150px;"><?php echo set_value('observacao', $cliente->observacao); ?></textarea>
                </li>
            </ul>
        </fieldset>        

        <fieldset>
            <ul>
                <li>
                    <label></label>
                    <div class="buttons">
                        <button type="submit" class="positive">
                            <img src="<?php echo base_url('assets/images/ico_tick.png'); ?>" alt=""/> Atualizar
                        </button>  
                    </div>
                </li>  
            </ul>
        </fieldset>  

    </form>
</div><!--fim da div formstyle-->