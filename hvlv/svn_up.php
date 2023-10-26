<h1>SVN update</h1>
<form method="POST" action="">
Rev #: <input type="text" name="rev" size="5" /> &nbsp; Path: /<input type="text" name="path" size="55" /><br />
Password: <input type="password" name="pwd" size="20" /><br />
<input type="submit" name="submit" value="SVN UP" />
</form>
<?php
//echo dirname(__FILE__);
if(!empty($_POST['pwd']) && $_POST['pwd'] == 'pcaEx168'){
	echo '<pre>';
	passthru('/usr/local/bin/svn --non-interactive --no-auth-cache --username pcae --password PcAe2@% up -r '.$_POST['rev'].' '.dirname(__FILE__).DIRECTORY_SEPARATOR.$_POST['path']);
	echo '</pre>';
}
