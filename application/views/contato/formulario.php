  <div class="pull-left">
      <h2>Contato</h2>
  </div>
  <div class="pull-right">
      <a class="btn btn_voltar">« voltar</a>
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
    <?php endif; 
    if($msg_sucesso)
		echo '<div class="alert alert-success">'.$msg_sucesso.'</div>';	
	if($error)
		echo '<div class="alert alert-error">'.$error.'</div>';			
	?>							

<div class="form-style">
    <form action="<?php echo base_url('contato'); ?>" method="post" id="form" class="well" autocomplete="off" >
    
    	<fieldset>
            <ul>
                <li>
                    <label>Nome: </label>
                    <input name="nome" id="nome" class="input-xlarge" type="text" value="<?php echo set_value('nome'); ?>"/>
                </li>
                <li>
                    <label>E-mail: </label>
                    <input name="email" id="email" type="text" class="input-xlarge" value="<?php echo set_value('email'); ?>"/>
                </li>   
                <li>
                    <label>Mensagem: </label>
                    <textarea name="mensagem" style="width: 270px; height:120px;"><?php echo set_value('mensagem'); ?></textarea>
                </li>                                   
			 </ul>
		</fieldset> 
	
        <fieldset>
          <ul>
              <li>
                  <label></label>
                  <button class="btn btn-success btn-large" type="submit">Enviar Contato</button>
              </li>  
          </ul>
          </fieldset>             

  </form>
</div>