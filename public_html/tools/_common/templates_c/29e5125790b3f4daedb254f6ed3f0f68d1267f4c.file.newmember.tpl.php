<?php /* Smarty version Smarty-3.0.7, created on 2011-04-21 15:50:43
         compiled from "d:\Inetpub\ukconsumer\web_class\james\nottinghack\_common\templates/newmember.tpl" */ ?>
<?php /*%%SmartyHeaderCode:147164db04443943207-38315668%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '29e5125790b3f4daedb254f6ed3f0f68d1267f4c' => 
    array (
      0 => 'd:\\Inetpub\\ukconsumer\\web_class\\james\\nottinghack\\_common\\templates/newmember.tpl',
      1 => 1303397386,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '147164db04443943207-38315668',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-gb">
<head>
<title>Member Account Setup :: Nottingham Hackspace</title>

<link href="<?php echo $_smarty_tpl->getVariable('css_url')->value;?>
main.css" rel="stylesheet" type="text/css" media="screen">

<script src="<?php echo $_smarty_tpl->getVariable('js_url')->value;?>
jquery.js"></script>
<script src="<?php echo $_smarty_tpl->getVariable('js_url')->value;?>
newmember.js"></script>

</head>
<body>
<div id="maincontainer">
	
<h1>Nottingham Hackspace Tools</h1>

<div id="content">
	<h2>Member Account Setup</h2>
	
	<p>Use this form to request new accounts on the various online member systems.</p>
	
	<p>You only need to request the access you want now - you can use this form again at a later date to set up additional access.</p>
	
	<form action="newmember.php" method="POST">
		
		<div class="fieldset">
		<fieldset>
			<legend><span>General Details</span></legend>
			
			<label for="yourname">Your name</label>
			<input type="text" name="yourname" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('formdata')->value['yourname'])===null||$tmp==='' ? '' : $tmp);?>
" /><br />
			
			<label for="youremail">Your email</label>
			<input type="text" name="youremail" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('formdata')->value['youremail'])===null||$tmp==='' ? '' : $tmp);?>
" /><br />
			
			<label for="username">Preferred username</label>
			<input type="text" name="username" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('formdata')->value['username'])===null||$tmp==='' ? '' : $tmp);?>
" /><br />
			
			<p>What system access do you require?</p>
			
			<label for="sysblog">Nottinghack Blog</label>
			<input type="checkbox" name="sysblog" id="sysblog" class="radio sysbox" <?php if (isset($_smarty_tpl->getVariable('formdata',null,true,false)->value['sysblog'])&&$_smarty_tpl->getVariable('formdata')->value['sysblog']=="yes"){?>checked="checked"<?php }?>/><br />
			
			<label for="sysplanet">Planet Nottinghack</label>
			<input type="checkbox" name="sysplanet" id="sysplanet" class="radio sysbox" <?php if (isset($_smarty_tpl->getVariable('formdata',null,true,false)->value['sysplanet'])&&$_smarty_tpl->getVariable('formdata')->value['sysplanet']=="yes"){?>checked="checked"<?php }?>/><br />
			
			<label for="syswiki">Wiki</label>
			<input type="checkbox" name="syswiki" id="syswiki" class="radio sysbox" <?php if (isset($_smarty_tpl->getVariable('formdata',null,true,false)->value['syswiki'])&&$_smarty_tpl->getVariable('formdata')->value['syswiki']=="yes"){?>checked="checked"<?php }?>/><br />
			
			<label for="sysgroup">Members Google Group</label>
			<input type="checkbox" name="sysgroup" id="sysgroup" class="radio sysbox" <?php if (isset($_smarty_tpl->getVariable('formdata',null,true,false)->value['sysgroup'])&&$_smarty_tpl->getVariable('formdata')->value['sysgroup']=="yes"){?>checked="checked"<?php }?>/><br />
			
		</fieldset>
		</div>
		
		<div class="fieldset hidden" id="sysplanet-extra">
		<fieldset>
			<legend><span>General Details</span></legend>
			
			<label for="yourrss">Personal blog RSS</label>
			<input type="text" name="yourrss" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('formdata')->value['yourrss'])===null||$tmp==='' ? '' : $tmp);?>
" /><br />
		</fieldset>
		</div>
	</form>
	
</div>

</div>
</body>
</html>