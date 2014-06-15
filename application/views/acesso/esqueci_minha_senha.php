<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>Gestão de equipes externas e atendimentos - Veritime</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="<?php echo base_url('assets/css/bootstrap.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/bootstrap-responsive.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/estilo-custom-modal.css'); ?>" rel="stylesheet">

    <!-- jQuery -->
    <script type="text/javascript" src="<?php echo base_url('assets/js/jquery.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/bootstrap.min.js'); ?>"></script>        

	<script>
        if($("#login").val() == ""){
            $("#login").val(parent.$("#login").val());
        }
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
    <div id="content_modal">
            <h4>Esqueci Minha Senha</h4>
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
            <?php
            // Include das mensagens dos controllers. Sucesso ou erro
            require('application/views/dashboard/mensagem.php');
            ?>
            <?php
                if(empty($nova_senha_cadastrada)){
            ?>
                    <form id="form_login" method="post" action="<?php echo base_url('acesso/nova_senha'); ?>">
                        <p>
                        <label for="email">E-mail:</label>
                        <input type="text" name="email" id="email" style="width:280px;" value="<?php echo set_value('email'); ?>"/>
                        <br/>
                        <button type="submit" id="enviar" value="Enviar" class="btn">Enviar</button>
                        </p>
                    </form>
            <?php
                }else{
                    echo "<div class='alert alert-success' style='margin-top:20px;'>Você receberá em instantes um e-mail com instruções para recuperar sua senha.</div>";
                }
            ?>
    </div>
</body>
</html>



