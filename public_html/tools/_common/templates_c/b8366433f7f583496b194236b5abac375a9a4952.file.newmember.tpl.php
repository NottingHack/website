<?php /* Smarty version Smarty-3.0.7, created on 2011-04-26 18:10:37
         compiled from "/home/nottinghack/public_html/tools/_common/templates/newmember.tpl" */ ?>
<?php /*%%SmartyHeaderCode:9440824594db742dd1f7bc2-50556550%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b8366433f7f583496b194236b5abac375a9a4952' => 
    array (
      0 => '/home/nottinghack/public_html/tools/_common/templates/newmember.tpl',
      1 => 1303855539,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '9440824594db742dd1f7bc2-50556550',
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
	
	<form enctype="multipart/form-data" action="newmember.php" method="POST">
		<input type="hidden" name="MAX_FILE_SIZE" value="5242880" />
		
		<div class="fieldset">
		<fieldset>
			<legend><span>General Details</span></legend>
			
			<label for="yourname"<?php if (isset($_smarty_tpl->getVariable('errors',null,true,false)->value['yourname'])){?> class="error"<?php }?>>Your name</label>
			<input type="text" name="yourname" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('formdata')->value['yourname'])===null||$tmp==='' ? '' : $tmp);?>
" <?php if (isset($_smarty_tpl->getVariable('errors',null,true,false)->value['yourname'])){?>class="error" <?php }?>/><br />
			
			<label for="youremail"<?php if (isset($_smarty_tpl->getVariable('errors',null,true,false)->value['youremail'])){?> class="error"<?php }?>>Your email</label>
			<input type="text" name="youremail" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('formdata')->value['youremail'])===null||$tmp==='' ? '' : $tmp);?>
" <?php if (isset($_smarty_tpl->getVariable('errors',null,true,false)->value['youremail'])){?>class="error" <?php }?>/><br />
			
			<label for="username"<?php if (isset($_smarty_tpl->getVariable('errors',null,true,false)->value['username'])){?> class="error"<?php }?>>Preferred username</label>
			<input type="text" name="username" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('formdata')->value['username'])===null||$tmp==='' ? '' : $tmp);?>
" <?php if (isset($_smarty_tpl->getVariable('errors',null,true,false)->value['username'])){?>class="error" <?php }?>/><br />
			
			<p>What system access do you require?</p>
			
			<label for="sysblog"<?php if (isset($_smarty_tpl->getVariable('errors',null,true,false)->value['sysblog'])){?> class="error"<?php }?>>Nottinghack Blog</label>
			<input type="checkbox" name="sysblog" id="sysblog" class="radio sysbox" <?php if (isset($_smarty_tpl->getVariable('formdata',null,true,false)->value['sysblog'])&&$_smarty_tpl->getVariable('formdata')->value['sysblog']=="on"){?>checked="checked" <?php }?><?php if (isset($_smarty_tpl->getVariable('errors',null,true,false)->value['sysblog'])){?>class="error" <?php }?>/><br />
			
			<label for="sysplanet"<?php if (isset($_smarty_tpl->getVariable('errors',null,true,false)->value['sysplanet'])){?> class="error"<?php }?>>Planet Nottinghack</label>
			<input type="checkbox" name="sysplanet" id="sysplanet" class="radio sysbox" <?php if (isset($_smarty_tpl->getVariable('formdata',null,true,false)->value['sysplanet'])&&$_smarty_tpl->getVariable('formdata')->value['sysplanet']=="on"){?>checked="checked" <?php }?><?php if (isset($_smarty_tpl->getVariable('errors',null,true,false)->value['sysplanet'])){?>class="error" <?php }?>/><br />
			
			<label for="syswiki"<?php if (isset($_smarty_tpl->getVariable('errors',null,true,false)->value['syswiki'])){?> class="error"<?php }?>>Wiki</label>
			<input type="checkbox" name="syswiki" id="syswiki" class="radio sysbox" <?php if (isset($_smarty_tpl->getVariable('formdata',null,true,false)->value['syswiki'])&&$_smarty_tpl->getVariable('formdata')->value['syswiki']=="on"){?>checked="checked" <?php }?><?php if (isset($_smarty_tpl->getVariable('errors',null,true,false)->value['syswiki'])){?>class="error" <?php }?>/><br />
			
			<label for="sysgroup"<?php if (isset($_smarty_tpl->getVariable('errors',null,true,false)->value['sysgroup'])){?> class="error"<?php }?>>Members Google Group</label>
			<input type="checkbox" name="sysgroup" id="sysgroup" class="radio sysbox" <?php if (isset($_smarty_tpl->getVariable('formdata',null,true,false)->value['sysgroup'])&&$_smarty_tpl->getVariable('formdata')->value['sysgroup']=="on"){?>checked="checked" <?php }?><?php if (isset($_smarty_tpl->getVariable('errors',null,true,false)->value['sysgroup'])){?>class="error" <?php }?>/><br />
			
		</fieldset>
		</div>
		
		<div class="fieldset hidden" id="sysplanet-extra">
		<fieldset>
			<legend><span>Planet Details</span></legend>
			
			<label for="yourrss"<?php if (isset($_smarty_tpl->getVariable('errors',null,true,false)->value['yourrss'])){?> class="error"<?php }?>>Personal blog feed</label>
			<input type="text" name="yourrss" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('formdata')->value['yourrss'])===null||$tmp==='' ? '' : $tmp);?>
" <?php if (isset($_smarty_tpl->getVariable('errors',null,true,false)->value['yourrss'])){?>class="error" <?php }?>/><br />
			
			<label for="avatar">Headshot (5Mb max)</label>
			<input type="file" name="avatar" />
			<p>Your headshot will appear on your posts on Planet Nottinghack, and will be edited to form a <a href="http://en.wikipedia.org/wiki/Hackergotchi">Hackergotchi</a>.  Make sure we can cut out just your head!  Alternatively, you can supply an avatar.</p> 
		</fieldset>
		</div>
		
		<div class="fieldset">
		<fieldset>
			<legend><span>Actions</span></legend>
			
			<label for="submit">&nbsp;</label>
			<input type="submit" name="submit" value="Submit" class="button" />
			
		</fieldset>
		</div>
	</form>
			
	
</div>

</div>
</body>
</html>
