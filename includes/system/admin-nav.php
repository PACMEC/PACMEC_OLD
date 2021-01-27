<?php
/**
 * Displays the site navigation.
 *
 * @package PACMEC
 * @subpackage Tiny
 * @since 0.0.1
 */
?>
<?php if(isAdmin()){
	/*
*/
	?>
<div class="pacmec-bar" style="margin-top:0px;left:0;position: static;z-index: 99999999999999999;" id="admin-navbar">
	<div class="pacmec-bar pacmec-black" style="margin-top:0px;left" >
		<a href="<?= siteinfo('homeurl'); ?>" class="navbar-brand"><?= siteinfo('sitename'); ?></a>
		<!-- // Menu Pages Plugin -->
		<div class="pacmec-dropdown-hover pacmec-hide-small pacmec-light-gray-s">
			<button class="pacmec-button"><i class="fa fa-list-alt"></i> Páginas <i class="fa fa-caret-down"></i></button>
			<div class="pacmec-dropdown-content pacmec-bar-block pacmec-light-gray pacmec-card-4">
				<a href="javascript:void(0)" class="pacmec-bar-item pacmec-button pacmec-text-black">Tabla</a>
				<a href="javascript:void(0)" class="pacmec-bar-item pacmec-button pacmec-text-black">Nueva página</a>
			</div>
		</div>
		
		<a href="<?= siteinfo('siteurl'); ?><?= "/?logout"; ?>" class="pacmec-bar-item pacmec-button pacmec-right"><i class="fa fa-sign-out"></i></a>
	</div>
	<div class="pacmec-bar pacmec-black" style="padding-right: calc(3vw);zoom:0.8">
		<!-- //
		<a href="javascript:void(0)" class="pacmec-bar-item pacmec-button pacmec-black"><span>Plugin: </span> <?php#$this->plugin; ?></a>
		<a href="javascript:void(0)" class="pacmec-bar-item pacmec-button pacmec-black"><span>Action: </span> <?php#$this->action; ?></a>
		-->
		
		<a href="javascript:editThisRoute()" class="pacmec-bar-item pacmec-button pacmec-black pacmec-right"><i class="fa fa-pencil"></i> Editar Pagina</a>
		
		<?php if (!is_home()) : ?>
			<a href="javascript:void(0)" class="pacmec-bar-item pacmec-button pacmec-black pacmec-right"><i class="fa fa-trash"></i> Hacer principal</a>
			<a href="javascript:void(0)" class="pacmec-bar-item pacmec-button pacmec-black pacmec-right"><i class="fa fa-trash"></i> Eliminar Pagina</a>
		<?php endif; ?>
	</div>
</div>
<?php 
} ?>