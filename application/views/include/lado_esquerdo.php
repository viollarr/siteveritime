<div id="menu">
    <dl>
        <dt><a href="#1">Conta</a></dt>
        <dd>
            <ul>
                <li><a href="<?php print base_url('usuario')?>">Detalhes da conta</a></li>
            </ul>
        </dd>
		<?php if(($this->session->userdata('usuario')->idpermissao == 1) || ($this->session->userdata('usuario')->idpermissao == 2)){ ?>
            <dt><a href="#1">Usuários</a></dt>
            <dd id="menu_usuarios">
                <ul>
                    <li><a href="<?php print base_url('usuario/lista')?>">Listagem</a></li>
                    <li><a href="<?php echo base_url('usuario/cadastro'); ?>">Cadastrar novo</a></li>
                </ul>
            </dd>        
            <dt><a href="#4">Clientes</a></dt>
            <dd id="menu_clientes">
                <ul>
                    <li><a href="<?php print base_url('cliente')?>">Visualizar clientes</a></li>
                    <li><a href="<?php echo base_url('cliente/cadastro'); ?>">Cadastrar novo</a></li>
                </ul>
            </dd>
        <?php } ?>
        <dt><a href="#7">Atendimentos</a></dt>
        <dd id="menu_atendimentos">
            <ul>
                <li><a href="<?php echo base_url('atendimento'); ?>">Visualizar atendimentos</a></li>
			<?php if(($this->session->userdata('usuario')->idpermissao == 1) || ($this->session->userdata('usuario')->idpermissao == 2)){ ?>
                <li><a href="<?php echo base_url('atendimento/cadastro'); ?>">Cadastrar novo</a></li>
            <?php } ?>
            </ul>
        </dd>
		<?php if(($this->session->userdata('usuario')->idpermissao == 1) || ($this->session->userdata('usuario')->idpermissao == 2)){ ?>
        <dt><a href="#10">Relatórios</a></dt>
        <dd id="menu_relatorios">
            <ul>
                <li><a href="<?php echo base_url('relatorio/atendimentos'); ?>">Atendimentos</a></li>
            </ul>
        </dd>
        <dt><a href="#15">Google Maps</a></dt>
        <dd id="menu_mapa">
            <ul>
                <li><a href="<?php echo base_url('visualizarmapas/visualizar'); ?>">Visualizar mapa</a></li>
            </ul>
        </dd>
        <?php } ?>
    </dl>
</div><!--fim da div menu-->