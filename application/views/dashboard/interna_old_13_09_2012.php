<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8">
        <title>Veritime</title>

        <link rel="stylesheet" href="<?php echo base_url('assets/css/template.css'); ?>" type="text/css" />
        <!-- jQuery -->
        <script src="<?php echo base_url('assets/js/jquery.js'); ?>" type="text/javascript"></script>
        <!-- Script JColorBox -->
        <script type="text/javascript" src="<?php echo base_url('assets/js/jquery.colorbox-min.js'); ?>"></script>
        <link href="<?php echo base_url('assets/css/colorbox.css'); ?>" rel="stylesheet" type="text/css" />
        <!-- Script JColorBox -->
        <!-- Script de máscara de campo -->
        <script src="<?php echo base_url('assets/js/jquery.maskedinput.js'); ?>" type="text/javascript"></script>
        <!-- Script de máscara de campo -->
        <script src="<?php echo base_url('assets/js/atualizaStatus.js'); ?>" type="text/javascript"></script>
        
		<!-- datapickup-->
        <link rel="stylesheet" href="<?php echo base_url('assets/js/datepiker/themes/base/jquery.ui.all.css'); ?>" type="text/css" />
        <script src="<?php echo base_url('assets/js/datepiker/ui/jquery.ui.core.js'); ?>" type="text/javascript"></script>
        <script src="<?php echo base_url('assets/js/datepiker/ui/jquery.ui.datepicker.js'); ?>" type="text/javascript"></script>
        <script src="<?php echo base_url('assets/js/datepiker/ui/i18n/jquery.ui.datepicker-pt-BR.js'); ?>" type="text/javascript"></script>
		<script src="<?php echo base_url('assets/js/autocomplete/jquery.autocomplete.js'); ?>" type="text/javascript"></script>

        <script type="text/javascript"> 
           //função que inicia o datepicker
           jQuery(function($) {
                $( ".datepicker" ).datepicker( $.datepicker.regional[ "pt-BR" ] );
           });
        </script>


        <script type="text/javascript">
            jQuery(document).ready(function($){
                
                // Menu
                var $menu_selecionado = '<?php echo (!empty($menu_selecionado)) ? $menu_selecionado : ''; ?>';
                //alert('menu_selecionado: ' + $menu_selecionado);
                
                //Definimos que todos as tags dd terão display:none menos o primeiro filho
                // $('dd:not(:first)').hide();
                
                // Esse par if-else serve para manter o item pai do subitem selecionado aberto.
                if($menu_selecionado == "") $('dd:not(:first)').hide();
                else $('dd:not(#'+$menu_selecionado+')').hide();
                //Ao clicar no link, executamos a funcão
                $('dt a').click(function(){
                    //As tags dd's visíveis agora ficam com display:none
                    $("dd:visible").slideUp("slow");
                    //Após, a funcão é transferida para seu pai, que procura o próximo irmão no código o tonando visível
                    $(this).parent().next().slideDown("slow");
                    return false;
                });

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


    </head>

    <body>
        <div id="tudo">

            <div id="header">
                <?php
                // Header
                require('application/views/include/header.php');
                ?>
            </div><!--fim da div header-->


            <div id="container">

                <div id="lado_esquerdo">
                    <?php
                    // Lado Esquerdo
                    require('application/views/include/lado_esquerdo.php');
                    ?>               
                </div><!--fim da div lado_esquerdo-->

                <div id="content">
                    <?php
                    // Exibe a view gerada pelo controller.
                    if (!empty($view)) {
                        echo "<!-- INÍCIO VIEW gerada pelo controller. -->";
                        echo $view;
                        echo "<!-- FIM VIEW gerada pelo controller. -->";
                    }
                    ?>
                </div><!--fim da div content_interno-->

                <div class="clear"></div>

            </div><!--fim da div container-->      

        </div><!--fim da div tudo-->

        <div id="footer">
            <?php
            // Footer
            require('application/views/include/footer.php');
            ?>
        </div><!--fim da div footer-->

    </body>
</html>