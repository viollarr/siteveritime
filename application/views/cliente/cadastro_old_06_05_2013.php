  <div class="pull-left">
      <h2>Adicionar novo cliente</h2>
  </div>
  <div class="pull-right">
      <a href="#" class="btn btn_voltar">« voltar</a>
  </div>
  
  <div class="clearfix"></div>

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
      <form action="<?php echo base_url('cliente/insert'); ?>" method="post" id="form" class="well">
        <fieldset>
            <legend>Informações de Identificação</legend>
            <ul>
                <li>
                     <label>Nome do Cliente: </label>
                     <input name="nome" id="nome" class="input-xlarge" type="text" value="<?php echo set_value('nome'); ?>"  />
                </li>  
                <li>
                    <label for="razao_social">Razão Social: </label>
                    <input name="razao_social" id="razao_social" type="text" class="input-xxlarge" value="<?php echo set_value('razao_social'); ?>" />
                </li>   
                <li>
                    <label for="cnpj">CNPJ: </label>
                    <input name="cnpj" id="cnpj" type="text" class="cnpj input-large" value="<?php echo set_value('cnpj'); ?>" />
                </li> 
                <li>
                    <label for="endereco">Endereço: </label>
                    <input name="endereco" id="endereco" type="text" class="input-xlarge" value="<?php echo set_value('endereco'); ?>"/>
                </li>
                <li>
                    <label for="endereco_numero">Número: </label>
                    <input name="endereco_numero" id="endereco_numero" type="text" class="input-mini" value="<?php echo set_value('endereco_numero'); ?>"/>
                </li>
                <li>
                    <label for="endereco_complemento">Complemento: </label>
                    <input name="endereco_complemento" id="endereco_complemento" type="text" class="input-small" value="<?php echo set_value('endereco_complemento'); ?>"/>
                </li>
                <li>
                    <label for="bairro">Bairro: </label>
                    <input name="bairro" id="bairro" type="text" class="input-medium" value="<?php echo set_value('bairro'); ?>"/>
                </li>                
                <li>
                    <label for="idestado">Estado: </label>
                    <select name="idestado" id="idestado" class="estado">
                        <option value="">selecione</option>
                        <?php foreach ($estados as $estado) { ?>
                            <option value="<?php echo $estado->idestado; ?>" <?php echo set_select("idestado", $estado->idestado); ?>>
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
                                <option value="<?php echo $cidade->idcidade; ?>" <?php echo set_select("idcidade", $cidade->idcidade, TRUE); ?>><?php echo $cidade->nome; ?></option>
                                <?php
                            } //foreach
                        } else {
                            ?>
                            <option value="">escolha o estado primeiro</option>
                        <?php } // else ?>
                    </select>      
                </li>  
                <li>
                    <label>Pontos de Referência: </label>
                    <textarea name="pontos_referencias" rows="5" class="span5"><?php echo set_value('pontos_referencias'); ?></textarea>
                </li>            
            </ul>
        </fieldset>                               


          <fieldset>
              <legend>Informações adicionais</legend>
              <ul>
                  <li>
                      <label for="filial_pontos_referencia">Observações:</label>
                      <textarea name="observacao" id="observacao" rows="5" class="span5"><?php echo set_value('observacao'); ?></textarea>
                  </li>
              </ul>
          </fieldset> 


		<fieldset>
            <legend>Contatos</legend>
            <ul>
                <li>
                    <label for="telefone">Telefone: </label>
                    <input name="telefone" id="telefone" type="text" class="telefone input-large" value="<?php echo set_value('telefone'); ?>" />
                </li>
                <li>
                    <label for="celular">Celular: </label>
                    <input name="celular" id="celular" type="text" class="celular input-large" value="<?php echo set_value('celular'); ?>" />
                </li>
                <li>
                    <label for="email">E-mail: </label>
                    <input name="email" id="email" class="input-xlarge" type="email" value="<?php echo set_value('email'); ?>" />
                </li>     
            </ul>
        </fieldset>      


       <fieldset>
            <legend>Endereços das Filiais</legend>
            <div id="filiais">

                <?php

                // Se retornar de uma validação, pegar os endereços das filiais.
                $filiais = (!empty($this->cliente_model)) ? $this->cliente_model->form_enderecos_filiais() : array();
                
                // Variável de controle.
                $contador_filial = 0;
                ?>

                <?php
                // Se estiver retornando de uma validação e tiver algum endereço de filial preenchido, entrará aqui.
                // Se o usuário acabou de entrar nesta view, não exibirá esta parte.
                if (!empty($filiais) && is_array($filiais)) {
                    foreach ($filiais as $filial) {
                        $contador_filial++;
                        ?>
                        <ul class="filial">
                            <li class="title">
                                <div class="pull-left"><strong>Filial <span><?php echo $contador_filial; ?></span></strong></div>
                                <button class="excluir_filial_cliente btn btn-mini btn-danger pull-right" type="button"><i class="icon-remove icon-white"></i> Remover filial</button>
                            </li>
                            
                            <div class="clearfix"></div>
                                                    
                            </li>
                            <li>
                                <label for="filial_endereco">Endereço: </label>
                                <input name="filial_endereco[]" id="filial_endereco" type="text" class="input-xlarge" value="<?php echo $filial->endereco; ?>" />
                            </li>
                            <li>
                                <label for="filial_endereco_numero">Número: </label>
                                <input name="filial_endereco_numero[]" id="filial_endereco_numero" type="text" class="input-mini" value="<?php echo $filial->endereco_numero; ?>"/>
                            </li>
                            <li>
                                <label for="filial_endereco_complemento">Complemento: </label>
                                <input name="filial_endereco_complemento[]" id="filial_endereco_complemento" type="text" class="input-small" value="<?php echo $filial->endereco_complemento; ?>"/>
                            </li>
                            <li>
                                <label for="filial_bairro">Bairro: </label>
                                <input name="filial_bairro[]" id="filial_bairro" type="text" class="input-medium" value="<?php echo $filial->bairro; ?>"/>
                            </li>                
                            <li>
                                <label for="filial_idestado">Estado: </label>
                                <select name="filial_idestado[]" id="filial_idestado" class="estado">
                                    <option value="">selecione</option>
                                    <?php foreach ($estados as $estado) { ?>
                                        <option value="<?php echo $estado->idestado; ?>" <?php echo ($filial->idestado == $estado->idestado) ? " selected='selected' " : ""; ?> >
                                            <?php echo $estado->nome; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </li>
                            <li>
                                <label for="filial_idcidade">Cidade: </label>
                                <select name="filial_idcidade[]" id="filial_idcidade" class="cidade">
                                    <?php
                                    if (!empty($filial->idestado)) {
                                        // Pegar as cidades deste estado.
                                        $cidades = $this->db->query("SELECT idcidade, nome FROM " . $this->db->dbprefix . "cidade WHERE idestado = {$filial->idestado} ORDER BY nome")->result();

                                        // Exibir todas as cidades deste estado.
                                        // Exibir como selected a cidade selecionada antes da validação.
                                        foreach ($cidades as $cidade) {
                                            ?>
                                            <option value="<?php echo $cidade->idcidade; ?>" <?php echo ($filial->idcidade == $cidade->idcidade) ? " selected='selected' " : ""; ?> ><?php echo $cidade->nome; ?></option>
                                            <?php
                                        }//foreach
                                    }//if
                                    else {
                                        ?>
                                        <option value="">escolha o estado primeiro</option>
                                    <?php } //else   ?>
                                </select>
                            </li>
                            <li>
                                <label for="filial_pontos_referencia">Pontos de Referência: </label>
                                <textarea name="filial_pontos_referencias[]" id="filial_pontos_referencias" rows="5" class="span5"><?php echo $filial->pontos_referencias; ?></textarea>
                            </li>            
                        </ul>
                        <?php
                    }//for
                }//if 
                ?>

                <?php
                // Se estiver retornando de uma validação e tiver algum endereço de filial preenchido, não entrará aqui.
                // Se o usuário acabou de entrar nesta view, exibirá esta parte: campos vazios para a inclusão do enedereço de uma filial.
                if ($contador_filial == 0) {
                    ?>
                    <ul class="filial">
                    
                        <li class="title">
                            <div class="pull-left"><strong>Filial <span>1</span></strong></div>
                            <button class="excluir_filial_cliente btn btn-mini btn-danger pull-right" type="button"><i class="icon-remove icon-white"></i> Remover filial</button>
                        </li>

                        <div class="clearfix"></div>                    

                        <li>
                            <label for="filial_endereco">Endereço: </label>
                            <input name="filial_endereco[]" id="filial_endereco" type="text" class="input-xlarge" value="" />
                        </li>
                        <li>
                            <label for="filial_endereco_numero">Número: </label>
                            <input name="filial_endereco_numero[]" id="filial_endereco_numero" type="text" class="input-mini" value=""/>
                        </li>
                        <li>
                            <label for="filial_endereco_complemento">Complemento: </label>
                            <input name="filial_endereco_complemento[]" id="filial_endereco_complemento" type="text" class="input-small" value=""/>
                        </li>
                        <li>
                            <label for="filial_bairro">Bairro: </label>
                            <input name="filial_bairro[]" id="filial_bairro" type="text" class="input-medium" value=""/>
                        </li>                
                        <li>
                            <label for="filial_idestado">Estado: </label>
                            <select name="filial_idestado[]" id="filial_idestado" class="estado">
                                <option value="">selecione</option>
                                <?php foreach ($estados as $estado) { ?>
                                    <option value="<?php echo $estado->idestado; ?>" >
                                        <?php echo $estado->nome; ?>
                                    </option>
                                <?php }//foreach        ?>
                            </select>
                        </li>
                        <li>
                            <label for="filial_idcidade">Cidade: </label>
                            <select name="filial_idcidade[]" id="filial_idcidade" class="cidade">
                                <option value="">escolha o estado primeiro</option>
                            </select>      
                        </li> 
                        <li>
                            <label for="filial_pontos_referencia">Pontos de Referência: </label>
                            <textarea name="filial_pontos_referencias[]" id="filial_pontos_referencias" rows="5" class="span5"></textarea>
                        </li>            
                    </ul>
                <?php } //if  ?>
            </div>
            <button class="btn btn-mini btn-success" id="mais_filial_cliente" type="button"><i class="icon-plus icon-white"></i> Adicionar mais filial</button>
            <input type="hidden" name="quantidade_filiais" value="" />
        </fieldset>        
                                  
          <hr>
                          
          <fieldset>
          <ul>
              <li>
                  <label></label>
                  <button class="btn btn-success btn-large" type="submit">Cadastrar</button>
              </li>  
          </ul>
          </fieldset> 

      </form>
  </div><!--fim da div formstyle-->






<!-- Códigos JS -->
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
            var $ultimo_indice = parseInt($('#filiais').find('.filial:last').find('.title').find('span').html());
            var $novo_indice = ($ultimo_indice > 0 ) ? ($ultimo_indice + 1) : 1;

            // Template (campos em branco) para a nova filial.
            var $filial_template = "<ul class='filial' style='display:none;'><li class='title'><div class='pull-left'><strong>Filial <span>"+$novo_indice+"</span></strong></div><button class='excluir_filial_cliente btn btn-mini btn-danger pull-right' type='button'><i class='icon-remove icon-white'></i> Remover filial</button></li><div class='clearfix'></div><li><label for='filial_endereco'>Endereço: </label><input name='filial_endereco[]' id='filial_endereco' type='text' class='input-xlarge' value='' /></li><li><label for='filial_endereco_numero'>Número: </label><input name='filial_endereco_numero[]' id='filial_endereco_numero' type='text' class='input-mini' value=''/></li><li><label for='filial_endereco_complemento'>Complemento: </label><input name='filial_endereco_complemento[]' id='filial_endereco_complemento' type='text' class='input-small' value=''/></li><li><label for='filial_bairro'>Bairro: </label><input name='filial_bairro[]' id='filial_bairro' type='text' class='input-medium' value=''/></li><li><label for='filial_idestado'>Estado: </label><select name='filial_idestado[]' id='filial_idestado' class='estado'><option value=''>selecione</option><?php foreach ($estados as $estado) { ?><option value='<?php echo $estado->idestado; ?>' ><?php echo $estado->nome; ?></option><?php } /* foreach */ ?></select></li><li><label for='filial_idcidade'>Cidade: </label><select name='filial_idcidade[]' id='filial_idcidade' class='cidade'><option value=''>escolha o estado primeiro</option></select></li><li><label for='filial_pontos_referencia'>Pontos de Referência: </label><textarea name='filial_pontos_referencias[]' id='filial_pontos_referencias' rows='5' class='span5'></textarea></li></ul>";
            
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
<!-- Códigos JS -->