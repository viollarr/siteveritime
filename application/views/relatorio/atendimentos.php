  <div class="pull-left">
      <h2>Relatório de Atendimentos</h2>
  </div>
  <div class="pull-right">
      <a href="#" class="btn btn_voltar">« voltar</a>
  </div>
  
  <div class="clearfix"></div>

	<?php
    // Include das mensagens dos controllers. Sucesso ou erro
    require('application/views/dashboard/mensagem.php');
    ?>

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
    <form action="<?php echo base_url('relatorio/gerar'); ?>" method="post" id="form">
      <input type="hidden" name="tipo" value="3" />
      <div class="well"> 
          <strong>Opções do relatório:</strong> 
          <ul style="margin-top:10px;">
              <li><input type="checkbox" name="data" id="data" <?php echo set_checkbox('data', 'data', true); ?> value="data_agendada" style="vertical-align:top;" /> Data</li>
              <li><input type="checkbox" name="hora" id="hora" <?php echo set_checkbox('hora', 'hora', true); ?> value="hora_agendada" style="vertical-align:top;" /> Hora</li>	 
              <li><input type="checkbox" name="tempo" id="tempo" <?php echo set_checkbox('tempo', 'tempo', true); ?> value="tempo_estimado" style="vertical-align:top;" /> Duração</li>     
              <li><input type="checkbox" name="prioridade" id="prioridade" <?php echo set_checkbox('prioridade', 'prioridade', true); ?> value="prioridade" style="vertical-align:top;" /> Prioridade</li>   
          </ul>
          <button class="btn btn-success btn-small" type="submit">Gerar relatório</button>
      </div>
  </form>
</div><!--fim da div formstyle-->
