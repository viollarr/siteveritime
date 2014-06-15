function stat(campo, caminhoImagem , caminho, tabela, idCampo, id, nivel){
    var img_atual = $(campo).attr("src");
	
    if($(campo).attr("src") == caminhoImagem+"/images/ico_ativo.png")
    {
        $(campo).attr("src", caminhoImagem+"/images/ico_inativo.png");
        var pag = caminho+"ajax_controller/altera_status";
        var nomeCampo = "status";
        var valorCampo = "nao_concluido";
    }else{
        $(campo).attr("src", caminhoImagem+"/images/ico_ativo.png");
        var pag = caminho+"ajax_controller/altera_status";
        var nomeCampo = "status";
        var valorCampo = "concluido";
    }
    
    $.post(pag,{
        tabela: tabela, 
        nomeCampo: nomeCampo, 
        valorCampo: valorCampo, 
        idCampo: idCampo, 
        id: id,
		nivel: nivel
    }, function(data){
       if(data == false){
            $(campo).attr("src", img_atual);
            $("#msg_controller_erro").css("display", "block");
            $("#msg_controller_erro").html("Não foi possível modificar o status. Favor entrar em contato com o desenvolvedor do sistema. ["+tabela+"/lista]");
        }
    });

};