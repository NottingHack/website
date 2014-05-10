<?php /* Smarty version 2.6.18-dev, created on 2014-01-18 13:07:56
         compiled from wiki:Flickr+gallery */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'counter', 'wiki:Flickr gallery', 2, false),array('modifier', 'escape', 'wiki:Flickr gallery', 3, false),array('modifier', 'default', 'wiki:Flickr gallery', 10, false),)), $this); ?>
<!-- Kishlery code starts here -->
<?php echo smarty_function_counter(array('name' => 'kishleryDivID','assign' => 'kishleryDivID'), $this);?>
<?php if ($this->_tpl_vars['kishleryDivID'] == 1): ?><script type="text/javascript" src="http://www.kishnel.com/kishlery/js/global.js"></script>
<?php endif; ?><div id="kishlery<?php echo ((is_array($_tmp=$this->_tpl_vars['kishleryDivID'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" class="KLmainContainer" style="height:442px!important"></div>
<script type="text/javascript">
	var myKishlery = new Kishlery('kishlery<?php echo ((is_array($_tmp=$this->_tpl_vars['kishleryDivID'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'quotes') : smarty_modifier_escape($_tmp, 'quotes')); ?>
', {
		mode: '<?php echo ((is_array($_tmp=$this->_tpl_vars['mode'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'quotes') : smarty_modifier_escape($_tmp, 'quotes')); ?>
',
		id: '<?php echo ((is_array($_tmp=$this->_tpl_vars['id'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'quotes') : smarty_modifier_escape($_tmp, 'quotes')); ?>
',
		limit: '30',
		tags: '<?php echo ((is_array($_tmp=$this->_tpl_vars['tags'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'quotes') : smarty_modifier_escape($_tmp, 'quotes')); ?>
',
		maxSize: '<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['maxSize'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'quotes') : smarty_modifier_escape($_tmp, 'quotes')))) ? $this->_run_mod_handler('default', true, $_tmp, 300) : smarty_modifier_default($_tmp, 300)); ?>
',
		forceHeight: true,
		linkToFlickr: true,
		showOwner: true,
		showTitle: true,
		showButtons: true,
		showThumbnails: true,
		showMosaic: true,
		activateKeyboard: false,
		singleMode: false,
		sort: '<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['id'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'sort') : smarty_modifier_escape($_tmp, 'sort')))) ? $this->_run_mod_handler('default', true, $_tmp, 'date-posted-asc') : smarty_modifier_default($_tmp, 'date-posted-asc')); ?>
',
		showMosaicImmediately: false,
		playImmediately: false,
		centeringMargin: true,
		showLog: false,
		quickView: true,
		bigMosaic: true,
		showFooter: true
	});
</script>
<!-- Kishlery code ends here -->