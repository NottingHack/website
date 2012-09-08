<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-gb">
<head>
<title>Member Account Setup :: Nottingham Hackspace</title>

<link href="{$css_url}main.css" rel="stylesheet" type="text/css" media="screen">

<script src="{$js_url}jquery.js"></script>
<script src="{$js_url}newmember.js"></script>

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
			
			<label for="yourname"{if isset($errors.yourname)} class="error"{/if}>Your name</label>
			<input type="text" name="yourname" value="{$formdata.yourname|default:""}" {if isset($errors.yourname)}class="error" {/if}/><br />
			
			<label for="youremail"{if isset($errors.youremail)} class="error"{/if}>Your email</label>
			<input type="text" name="youremail" value="{$formdata.youremail|default:""}" {if isset($errors.youremail)}class="error" {/if}/><br />
			
			<label for="username"{if isset($errors.username)} class="error"{/if}>Preferred username</label>
			<input type="text" name="username" value="{$formdata.username|default:""}" {if isset($errors.username)}class="error" {/if}/><br />
			
			<p>What system access do you require?</p>
			
			<label for="sysblog"{if isset($errors.sysblog)} class="error"{/if}>Nottinghack Blog</label>
			<input type="checkbox" name="sysblog" id="sysblog" class="radio sysbox" {if isset($formdata.sysblog) and $formdata.sysblog=="on"}checked="checked" {/if}{if isset($errors.sysblog)}class="error" {/if}/><br />
			
			<label for="sysplanet"{if isset($errors.sysplanet)} class="error"{/if}>Planet Nottinghack</label>
			<input type="checkbox" name="sysplanet" id="sysplanet" class="radio sysbox" {if isset($formdata.sysplanet) and $formdata.sysplanet=="on"}checked="checked" {/if}{if isset($errors.sysplanet)}class="error" {/if}/><br />
			
			<label for="syswiki"{if isset($errors.syswiki)} class="error"{/if}>Wiki</label>
			<input type="checkbox" name="syswiki" id="syswiki" class="radio sysbox" {if isset($formdata.syswiki) and $formdata.syswiki=="on"}checked="checked" {/if}{if isset($errors.syswiki)}class="error" {/if}/><br />
			
			<label for="sysgroup"{if isset($errors.sysgroup)} class="error"{/if}>Members Google Group</label>
			<input type="checkbox" name="sysgroup" id="sysgroup" class="radio sysbox" {if isset($formdata.sysgroup) and $formdata.sysgroup=="on"}checked="checked" {/if}{if isset($errors.sysgroup)}class="error" {/if}/><br />
			
		</fieldset>
		</div>
		
		<div class="fieldset hidden" id="sysplanet-extra">
		<fieldset>
			<legend><span>Planet Details</span></legend>
			
			<label for="yourrss"{if isset($errors.yourrss)} class="error"{/if}>Personal blog feed</label>
			<input type="text" name="yourrss" value="{$formdata.yourrss|default:""}" {if isset($errors.yourrss)}class="error" {/if}/><br />
			
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
