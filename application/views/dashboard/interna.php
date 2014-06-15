<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>Gestão de equipes externas e atendimentos - Veritime</title>
<meta name="robots" content="noindex, nofollow">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="<?php echo base_url('assets/css/bootstrap.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/bootstrap-responsive.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/estilo-custom.css'); ?>" rel="stylesheet">

    
    <!-- jQuery -->
    <script type="text/javascript" src="<?php echo base_url('assets/js/jquery.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/funcoes.js'); ?>"></script>        
    <script src="<?php echo base_url('assets/js/bootstrap.min.js'); ?>"></script>        
    <script src="<?php echo base_url('assets/js/bootstrap-tab.js'); ?>"></script>        
    <!-- Script ColorBox -->
    <script type="text/javascript" src="<?php echo base_url('assets/js/jquery.colorbox-min.js'); ?>"></script>
    <link href="<?php echo base_url('assets/css/colorbox.css'); ?>" rel="stylesheet" type="text/css" />
    <!-- Script ColorBox -->

    <!-- Script de máscara de campo -->
    <script src="<?php echo base_url('assets/js/jquery.maskedinput.js'); ?>" type="text/javascript"></script>
    <!-- Script de máscara de campo -->
    <script src="<?php echo base_url('assets/js/atualizaStatus.js'); ?>" type="text/javascript"></script>
    
    <!-- datapickup-->
    <link rel="stylesheet" href="<?php echo base_url('assets/js/datepiker/themes/base/jquery.ui.all.css'); ?>" type="text/css" />
    <script src="<?php echo base_url('assets/js/datepiker/ui/jquery.ui.core.js'); ?>" type="text/javascript"></script>
    <script src="<?php echo base_url('assets/js/datepiker/ui/jquery.ui.datepicker.js'); ?>" type="text/javascript"></script>
    <script src="<?php echo base_url('assets/js/datepiker/ui/i18n/jquery.ui.datepicker-pt-BR.js'); ?>" type="text/javascript"></script>

    <!-- Script para gerar o autocomplete-->
    <link href="<?php echo base_url('assets/css/autocomplete/jquery.autocomplete.css'); ?>" rel="stylesheet" type="text/css" />
    <script src="<?php echo base_url('assets/js/autocomplete/jquery.autocomplete.js'); ?>"></script>
    
    <!-- Script para ordenar as tabelas do relatorios -->
    <!--<script src="<?php echo base_url('assets/js/tablesorter/jquery-latest.js'); ?>"></script>-->
    <script src="<?php echo base_url('assets/js/tablesorter/jquery.tablesorter.js'); ?>"></script>

    

    <script type="text/javascript"> 
		
		// Tooltip
		/*
		$(document).ready(function() {
				// Tooltip only Text
				$('.tooltip-custom').hover(function(){
						// Hover over code
						var title = $(this).attr('title');
						$(this).data('tipText', title).removeAttr('title');
						$('<p class="tooltip_custom"></p>')
						.text(title)
						.appendTo('body')
						.fadeIn('slow');
				}, function() {
						// Hover out code
						$(this).attr('title', $(this).data('tipText'));
						$('.tooltip_custom').remove();
				}).mousemove(function(e) {
						var mousex = e.pageX + 8; //Get X coordinates
						var mousey = e.pageY + 8; //Get Y coordinates
						$('.tooltip_custom')
						.css({ top: mousey, left: mousex })
				});
		});
		*/
		$('.btn_voltar').click(function() {
			 history.back()
		});
	
       //função que inicia o datepicker
       jQuery(function($) {
            $( ".datepicker" ).datepicker( $.datepicker.regional[ "pt-BR" ] );
       });
    </script>
    <script type="text/javascript">
        jQuery(document).ready(function($){
            // Botão voltar
            $('.btn_voltar').click(function() {
                history.back();
            });
            
            // Máscaras
            $(".data").mask("99/99/9999");
            $(".hora").mask("99:99");
            $(".cnpj").mask("99.999.999/9999-99");
            $(".telefone").mask("(99) 9999-9999");
            $(".celular").mask("(99) 9999-9999?9").live('focusout', function(event) {
                var target, phone, element;
                target = (event.currentTarget) ? event.currentTarget : event.srcElement;
                phone = target.value.replace(/\D/g, '');
                element = $(target);
                element.unmask();
                if(phone.length > 10) {
                    element.mask("(99) 99999-999?9");
                } else {
                    element.mask("(99) 9999-9999?9");  
                }
            });
    });		
    </script>

	<?php
        if((!empty($headerjs))&&(!empty($headermap))){ 
            echo $headerjs; 
            echo $headermap; 
        }
    ?>
</head>

<body>
        <div class="container-fluid">

            <div class="header">
                <?php require('application/views/include/header.php');// Header ?>
            </div><!--fim da div header-->

            <div class="clearfix"></div>
            
            <div class="content container-fluid">
                    <?php
                    // Exibe a view gerada pelo controller.
                    if (!empty($view)) {
                        echo "<!-- INÍCIO VIEW gerada pelo controller. -->";
                        echo $view;
                        echo "<!-- FIM VIEW gerada pelo controller. -->";
                    }
                    ?>
             </div><!--fim da div content-->

             <div class="clearfix"></div>

	    </div><!--fim da div container-->   

        <div class="modal-footer">
                 <?php require('application/views/include/footer.php');// Footer?>
        </div><!--fim da div footer-->  

</body>
</html>