<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>Gestão de equipes externas e atendimentos - Veritime</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">

    <link href="<?php echo base_url('assets/css/bootstrap.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/bootstrap-responsive.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/estilo-custom-login.css'); ?>" rel="stylesheet">

    <!-- jQuery -->
    <script type="text/javascript" src="<?php echo base_url('assets/js/jquery.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/bootstrap.min.js'); ?>"></script>        
    <!-- Script ColorBox -->
    <script type="text/javascript" src="<?php echo base_url('assets/js/jquery.colorbox-min.js'); ?>"></script>
    <link href="<?php echo base_url('assets/css/colorbox.css'); ?>" rel="stylesheet" type="text/css" />
    <!-- Script ColorBox -->

    <script type="text/javascript">
        jQuery(document).ready(function($){ 
            // Colocar o focus no input do login.
            $('#login').focus();
            $(".esqueci_minha_senha").colorbox({iframe:true, width:"540px", height:"320px"});
        }); 
    </script>

<!--[if lt IE 9]>
<script>
document.createElement('header');
document.createElement('nav');
document.createElement('section');
document.createElement('article');
document.createElement('aside');
document.createElement('footer');
document.createElement('hgroup');
</script>
<![endif]-->
<!-- Pulled from http://code.google.com/p/html5shiv/ -->
<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

</head>

<body>
	<div class="box_login">
      <form action="<?php echo base_url('acesso/logar'); ?>" method="post" id="form" class="well" >
          <fieldset>
          <legend><img src="<?php echo base_url(); ?>assets/images/veritime-gestao-de-equipe-externa-logo.png"/></legend>
		  <?php
          // Mensagem do controller. Por exemplo: em caso de login/senha inválidos exibe uma mensagem de erro.
          if (!empty($msg_controller)) :
              ?>
                <div class="alert">
                  <button type="button" class="close" data-dismiss="alert">×</button>
                  <?php echo $msg_controller; ?>
                </div>              
          <?php endif; ?>          
          <ul>
              <li>
                  <label>Login/E-mail: </label>
                  <input name="login" id="nome" type="text"  />
              </li>
              <li>
                  <label>Senha: </label>
                  <input name="senha" id="nome" type="password"  />
              </li>    
              <li>
                  <div class="buttons">
                      <p><button type="submit" class="btn btn-entrar">Entrar</button></p> 
                      <br/>
                      <a href="<?php echo base_url('acesso/esqueci_minha_senha'); ?>" class="esqueci_minha_senha">Esqueceu sua senha? Clique aqui.</a>
                  </div>
              </li>     
      
                                 
           </ul>
                                
           
           </fieldset>
      </form>
	</div><!--fim da div tudo-->   

</body>
</html>