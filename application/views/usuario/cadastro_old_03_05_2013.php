<script type="text/javascript">
jQuery(document).ready(function($){
	
	$(".idpermissao").bind("click",function(){
		vinculo(this);
	});
	
// ADD Usuários Adm/Gerentes

	$("#users").autocomplete("<?php print base_url(); ?>autocomplete/usuariosAutocomplete/"+1+"-"+2,
	{
	  minLength: 2,
	  scrollHeight: 220,
	  selectFirst: false
	});
	
	$("#incluir_cliente").bind("click",function(){
		if($("#users").val() != ""){
			addRelacionamento();
		}
	});
	 
// FIM

	<?php
		if(!empty($_POST['idpermissao'])){
	?>	
			for(var t = 0; t < $(".idpermissao").length; t++){
				if(<?php echo $_POST['idpermissao']; ?> == $(".idpermissao")[t].value){
					$("#per_"+t).attr({checked: true});	
				}
				if(<?php echo $_POST['idpermissao']; ?> == 3){
					$("#perfil").show();
				}
			}
			if(<?php echo $_POST['idpermissao']; ?> == 3){
				<?php
				if(!empty($_POST['funcionarios_alocados'])){
					foreach($_POST["funcionarios_alocados"] as $ids){
						$valores = explode("-",$ids);
						$nome_funcionario = str_replace("_"," ",$valores[0]);
						$ids_funcionario = end($valores);
						
				?>		
						var classeUser = 'user_<?php echo $ids_funcionario ;?>';
						$("#perfil").show();
						$("#user_escolhido").show();
						$("#form").append('<input type="hidden" name="funcionarios_alocados[]" value="<?php echo $ids; ?>" class="'+classeUser+'" />');
						$("#result_users").append('<span class="'+classeUser+'"><?php echo $nome_funcionario; ?> <button src="" alt="" class="'+classeUser+' btn btn-danger btn-mini"  style="cursor:pointer;" onclick="remuve(\''+classeUser+'\',1);">Remover</button></span><br class="'+classeUser+'" />');
				<?php		
					}
				}
				?>
			}
	<?php } ?>

	
});


function vinculo(don){
			if(don.value == 3){
				$("#perfil").show();
			}
			else{
				$("#perfil").hide();
				$("#user_escolhido").hide();
				$("#result_users").html("");
				$("input[name='funcionarios_alocados[]']").remove();
			}
}

function remuve(e){
	$("."+e).remove();
	if($("#form").find('input[name="funcionarios_alocados[]"]').length == 0){
		$("#user_escolhido").hide();
	}
	
}

function addRelacionamento(){
	$.getJSON("<?php print base_url(); ?>atendimento/getIdNomeUsuario/"+$("#users").val(),{}, function(j){
					
			if(j != ''){
				var conteudo = $("#form").find('input[name="funcionarios_alocados[]"]').length;
				var value_imput = j.nome.replace(/\s/g,'_')+'-'+j.idusuario;
				var classeUser = 'user_'+j.idusuario;
				
				if(conteudo == 0){
						$("#form").append('<input type="hidden" name="funcionarios_alocados[]" value="'+value_imput+'" class="'+classeUser+'" />');
						$("#result_users").append('<span class="'+classeUser+'">'+j.nome+' <button src="" alt="" class="'+classeUser+' btn btn-danger btn-mini"  style="cursor:pointer;" onclick="remuve(\''+classeUser+'\');">Remover</button></span><br class="'+classeUser+'" />');
						$("#user_escolhido").show();
						$("#users").val("");
				}
				else{
					var encontrado = 0;
					for(var i = 0; i < conteudo; i++){
						var id_consulta = $("#form").find('input[name="funcionarios_alocados[]"]')[i].value.split("-");
						if(id_consulta[1] == j.idusuario){
								encontrado++;
						}
					}
					if(encontrado >= 1){
						alert("Você já adicionou esse Administrador/Gerente");
					}
					else{
						$("#form").append('<input type="hidden" name="funcionarios_alocados[]" value="'+value_imput+'" class="'+classeUser+'" />');
						$("#result_users").append('<span class="'+classeUser+'">'+j.nome+' <button src="" alt="" class="'+j.idusuario+' btn btn-danger btn-mini"  style="cursor:pointer;" onclick="remuve(\''+classeUser+'\');">Remover</button></span><br class="'+classeUser+'" />');
						$("#user_escolhido").show();
						$("#users").val("");
					}
				}
			}
			else {
				alert("Adicione um Administrador/Gerente válido.")
			}
	});
}
</script>


  <div class="pull-left">
      <h2>Adicionar novo Usuário</h2>
  </div>
  <div class="pull-right">
      <a href="#" class="btn">« voltar</a>
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
    <form action="<?php echo base_url('usuario/insert'); ?>" method="post" id="form" class="well">
      <fieldset>
        <legend>Informações de Identificação</legend>
            <ul>
                <li>
                    <label>Nome: </label>
                    <input name="nome" class="input-xlarge" type="text" value="<?php echo set_value('nome'); ?>" />
                </li>
                <li>
                    <label>Celular: </label>
                    <input name="celular" class="celular input-medium" type="text" value="<?php echo set_value('celular'); ?>" />
                </li>                   
                <li>
                    <label>E-mail: </label>
                    <input name="email" class="email input-xlarge" type="email" value="<?php echo set_value('email'); ?>" />
                </li>
                <li>
                    <label>Senha: </label>
                    <input name="senha" class="input-medium" type="password" value="<?php echo set_value('password'); ?>" />                
                </li>  
                <li>
                    <label>Perfil: </label>
                    <?php foreach ($permissoes as $k => $permissao) { ?>
	                    <?php if (($this->session->userdata('usuario')->idpermissao) <= $permissao->idpermissao){ ?>
	                    <input type="radio" name="idpermissao" id="per_<?php echo $k;?>" class="idpermissao" value="<?php echo $permissao->idpermissao; ?>" <?php echo set_radio('idpermisao', $permissao->idpermissao, 'TRUE'); ?>> <?php echo $permissao->nome; ?>&nbsp;&nbsp;&nbsp;
                    <?php
					    }
			         } ?>  
                </li>
                <li id="perfil" style="display:none;">
                	<label>Relacionar Gerente: </label>
                    <input type="text" name="q" id="users" size="40" autocomplete="off" /> <input type="button" class="btn btn-small btn-success" id="incluir_cliente" value="Incluir" style="vertical-align: top;" />
                </li>
                <li id="user_escolhido" style="display:none;">
                	<div style="width:150px; float:left;">
                    	<label>Relacionamento: </label>
                    </div>
                    <div style="width:auto; float:left;">
						<span id="result_users"></span>
                    </div>
                </li>
            </ul>
		    <input name="ativo" type="hidden" value="sim" />
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
