<h2>Cliente » Cadastro</h2>
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
    <form action="<?php echo base_url('cliente/insert'); ?>" method="post" id="form">
        <fieldset>
            <legend>Principal</legend>
            <ul>
                <li>
                    <label for="nome">Nome: </label>
                    <input name="nome" id="nome" size="40" type="text" value="<?php echo set_value('nome'); ?>"  />
                </li>
            </ul>
        </fieldset>        

        <fieldset>
            <legend>Informações de Identificação</legend>
            <ul>
                <li>
                    <label for="razao_social">Razão Social: </label>
                    <input name="razao_social" id="razao_social" size="40" type="text" value="<?php echo set_value('razao_social'); ?>" />
                </li>   
                <li>
                    <label for="cnpj">CNPJ: </label>
                    <input name="cnpj" id="cnpj" size="40" type="text" class="cnpj" value="<?php echo set_value('cnpj'); ?>" />
                </li> 
                <li>
                    <label for="endereco">Endereço: </label>
                    <input name="endereco" id="endereco" type="text" size="40" value="<?php echo set_value('endereco'); ?>"/>
                </li>
                <li>
                    <label for="endereco_numero">Número: </label>
                    <input name="endereco_numero" id="endereco_numero" type="text" size="4" value="<?php echo set_value('endereco_numero'); ?>"/>
                </li>
                <li>
                    <label for="endereco_complemento">Complemento: </label>
                    <input name="endereco_complemento" id="endereco_complemento" type="text" size="15" value="<?php echo set_value('endereco_complemento'); ?>"/>
                </li>
                <li>
                    <label for="bairro">Bairro: </label>
                    <input name="bairro" id="bairro" type="text" size="15" value="<?php echo set_value('bairro'); ?>"/>
                </li>                
                <li>
                    <label for="idestado">Estado: </label>
                    <select name="idestado" id="idestado" class="estado">
                        <option value="">selecione</option>
                        <?php foreach ($estados as $estado) { ?>
                            <option value="<?php echo $estado->idestado; ?>"
                                    <?php echo set_select("idestado", $estado->idestado); ?>>
                                        <?php echo $estado->nome; ?>
                            </option>
                        <?php } ?>
                    </select>
                </li>
                <li>
                    <label for="idcidade">Cidade: </label>
                    <select name="idcidade" id="idcidade" class="cidade">
                        <?php
                        if (set_value('idestado')) {
                            foreach ($cidades as $cidade) {
                                ?>
                                <option value="<?php echo $cidade->idcidade; ?>"
                                        <?php echo set_select("idcidade", $cidade->idcidade, TRUE); ?>><?php echo $cidade->nome; ?></option>
                                        <?php
                                    }
                                } else {
                                    ?>
                            <option value="0">escolha o estado primeiro</option>
                        <?php } ?>
                    </select>      
                </li>  
            </ul>
        </fieldset>

        <fieldset>
            <legend>Contatos</legend>
            <ul>
                <li>
                    <label for="telefone">Telefone: </label>
                    <input name="telefone" id="telefone" size="40" type="text" class="telefone" value="<?php echo set_value('telefone'); ?>" />
                </li>
                <li>
                    <label for="celular">Celular: </label>
                    <input name="celular" id="celular" size="40" type="text" class="celular" value="<?php echo set_value('celular'); ?>" />
                </li>
                <li>
                    <label for="email">E-mail: </label>
                    <input name="email" id="email" size="40" type="email" value="<?php echo set_value('email'); ?>" />
                </li>     
            </ul>
        </fieldset>        

        <fieldset>
            <legend>Endereços das Filiais</legend>
            <div id="filiais">
                <?php
                // Só consegui pegar colocando numa sessão...
                $filiais = $this->session->userdata('enderecos_filiais');
                // ... mas neste caso é preciso zerar a sessão logo depois para não ter problemas. 
                // Se não zerar, quando o uusário for cadastrar outro cliente, esses dados serão exibidos! [Daniel Costa - 17/08/2012]
                $this->session->set_userdata('enderecos_filiais', '');
                //pr('filiais retornadas depois da validação', $filiais);

                $contador_filial = 0;
                if (!empty($filiais)) {
                    foreach ($filiais as $filial) {
                        $contador_filial++;
                        ?>
                        <ul class="filial">
                            <li class="header">Filial <span><?php echo $contador_filial; ?></span><div class="excluir_filial_cliente">Filial</div></li>
                            <li>
                                <label for="filial_endereco">Endereço: </label>
                                <input name="filial_endereco[]" id="filial_endereco" type="text" size="40" value="<?php echo $filial->endereco; ?>" />
                            </li>
                            <li>
                                <label for="filial_endereco_numero">Número: </label>
                                <input name="filial_endereco_numero[]" id="filial_endereco_numero" type="text" size="4" value="<?php echo $filial->endereco_numero; ?>"/>
                            </li>
                            <li>
                                <label for="filial_endereco_complemento">Complemento: </label>
                                <input name="filial_endereco_complemento[]" id="filial_endereco_complemento" type="text" size="15" value="<?php echo $filial->endereco_complemento; ?>"/>
                            </li>
                            <li>
                                <label for="filial_bairro">Bairro: </label>
                                <input name="filial_bairro[]" id="filial_bairro" type="text" size="15" value="<?php echo $filial->bairro; ?>"/>
                            </li>                
                            <li>
                                <label for="filial_idestado">Estado: </label>
                                <select name="filial_idestado[]" id="filial_idestado" class="estado">
                                    <option value="">selecione</option>
                                    <?php foreach ($estados as $estado) { ?>
                                        <option value="<?php echo $estado->idestado; ?>"
                                                <?php echo set_select("idestado", $estado->idestado); ?>>
                                                    <?php echo $estado->nome; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </li>
                            <li>
                                <label for="filial_idcidade">Cidade: </label>
                                <select name="filial_idcidade[]" id="filial_idcidade" class="cidade">
                                    <option value="0">escolha o estado primeiro</option>
                                </select>      
                            </li>  
                        </ul>
                        <?php
                    }//for
                }//if 
                ?>
                <ul class="filial">
                    <li class="header">Filial <span><?php echo ($contador_filial > 0) ? ($contador_filial + 1) : 1; ?></span><div class="excluir_filial_cliente">Filial</div></li>
                    <li>
                        <label for="filial_endereco">Endereço: </label>
                        <input name="filial_endereco[]" id="filial_endereco" type="text" size="40" value="" />
                    </li>
                    <li>
                        <label for="filial_endereco_numero">Número: </label>
                        <input name="filial_endereco_numero[]" id="filial_endereco_numero" type="text" size="4" value=""/>
                    </li>
                    <li>
                        <label for="filial_endereco_complemento">Complemento: </label>
                        <input name="filial_endereco_complemento[]" id="filial_endereco_complemento" type="text" size="15" value=""/>
                    </li>
                    <li>
                        <label for="filial_bairro">Bairro: </label>
                        <input name="filial_bairro[]" id="filial_bairro" type="text" size="15" value=""/>
                    </li>                
                    <li>
                        <label for="filial_idestado">Estado: </label>
                        <select name="filial_idestado[]" id="filial_idestado" class="estado">
                            <option value="">selecione</option>
                            <?php foreach ($estados as $estado) { ?>
                                <option value="<?php echo $estado->idestado; ?>"
                                        <?php echo set_select("idestado", $estado->idestado); ?>>
                                            <?php echo $estado->nome; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </li>
                    <li>
                        <label for="filial_idcidade">Cidade: </label>
                        <select name="filial_idcidade[]" id="filial_idcidade" class="cidade">
                            <option value="0">escolha o estado primeiro</option>
                        </select>      
                    </li>  
                </ul>
            </div>
            <div id="mais_filial_cliente">Filial</div>
            <input type="hidden" name="quantidade_filiais" value="" />
        </fieldset>        

        <fieldset>
            <legend><label for="observacao">Observações</label></legend>
            <ul>
                <li>
                    <textarea name="observacao" id="observacao" style="width: 675px; height: 150px;"><?php echo set_value('observacao'); ?></textarea>
                </li>
            </ul>
        </fieldset>        

        <fieldset>
            <ul>
                <li>
                    <label></label>
                    <div class="buttons">
                        <button type="submit" class="positive">
                            <img src="<?php echo base_url('assets/images/ico_tick.png'); ?>" alt=""/> cadastrar
                        </button>  
                    </div>
                </li>  
            </ul>
        </fieldset>  

    </form>
</div><!--fim da div formstyle-->
<script>
    jQuery(document).ready(function($){

        // Carregar as cidades quando selecionar o estado no endereço principal do cliente.
        $("select[name=idestado]").change(function(){
            $("select[name=idcidade]").html('<option value="0">Carregando...</option>');
		
            // Carregando as cidades via AJAX.
            $.post("<?php print base_url(); ?>cidade/getCidades/", {idestado:$(this).val()}, function(valor){
                $("select[name=idcidade]").html(valor);
            }
        )});
        
        // Carregar as cidades quando selecionar o estado no endereço de uma filial.
        $("#filiais").on('change', "select[name='filial_idestado[]']", function(event) {
            var $select_cidade = $(this).closest('.filial').find("select[name='filial_idcidade[]']");
            $select_cidade.html('<option value="0">Carregando...</option>');
	
            // Carregando as cidades via AJAX.
            $.post("<?php print base_url(); ?>cidade/getCidades/", {idestado:$(this).val()}, function(valor){
                $select_cidade.html(valor);
            });
        });
        
        // Mecanismo para duplicar os endereços de filiais.
        $('#mais_filial_cliente').click(function(){
            // Gerar o índice do novo elemento.
            var $ultimo_indice = parseInt($('#filiais').find('.filial:last').find('.header').find('span').html());
            var $novo_indice = ($ultimo_indice > 0 ) ? ($ultimo_indice + 1) : 1;

            // Template (campos em branco) para a nova filial.
            var $filial_template = "<ul class='filial' style='display:none;'><li class='header'>Filial <span>"+$novo_indice+"</span><div class='excluir_filial_cliente'>Filial</div></li><li><label for='filial_endereco'>Endereço: </label><input name='filial_endereco[]' id='filial_endereco' type='text' size='40' value='' /></li><li><label for='filial_endereco_numero'>Número: </label><input name='filial_endereco_numero[]' id='filial_endereco_numero' type='text' size='4' value=''/></li><li><label for='filial_endereco_complemento'>Complemento: </label><input name='filial_endereco_complemento[]' id='filial_endereco_complemento' type='text' size='15' value=''/></li><li><label for='filial_bairro'>Bairro: </label><input name='filial_bairro[]' id='filial_bairro' type='text' size='15' value=''/></li><li><label for='filial_idestado'>Estado: </label><select name='filial_idestado[]' id='filial_idestado' class='estado'><option value=''>selecione</option><?php foreach ($estados as $estado) { ?><option value='<?php echo $estado->idestado; ?>'<?php echo set_select('idestado', $estado->idestado); ?>><?php echo $estado->nome; ?></option><?php } ?></select></li><li><label for='filial_idcidade'>Cidade: </label><select name='filial_idcidade[]' id='filial_idcidade' class='cidade'><option value='0'>escolha o estado primeiro</option></select></li></ul>";
            
            // Acrescentar os campos em branco para a nova filial.
            $('#filiais').append($filial_template);

            // Exibir os campos para a nova filial.
            $('#filiais').find('.filial:last').slideDown("slow");
            
        });
        
        // Excluir filial
        $("#filiais").on('click', ".excluir_filial_cliente", function(event) {
            
            // Perguntar ao usuário se ele tem certeza.
            if(!confirm("Você tem certeza?")) return false;
            
            // Excluir a filial.
            var $filial = $(this).closest('.filial');
            $filial.animate({height: 'toggle'}, 700, function(){
                $filial.remove();
                var $i = 1;
                
                // Renumerar cada filial.
                $(".header span").each(function(){
                    $(this).html($i);
                    $i++;
                });
            });

        });
    });
</script>
