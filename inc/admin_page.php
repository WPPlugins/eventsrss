<?php
$configKey = eventsRss::getConfigKey();

$adminOptions = get_option($configKey);

if(isset($_POST['saveEventsRSSSettings'])) {
	if(isset($_POST['eventsPageID'])) $adminOptions['eventsPageID'] = (int)$_POST['eventsPageID'];
	if(isset($_POST['rssTemplate'])) $adminOptions['rssTemplate'] = htmlspecialchars(trim($_POST['rssTemplate'], "\t\n "), ENT_QUOTES);

	update_option($configKey, $adminOptions);
}
?>

<div class=wrap>

<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">

<h2>Events RSS</h2>

<h3>Page which contains the event list</h3>

Page ID: <input type="text" size="2" name="eventsPageID" value="<?php echo $adminOptions['eventsPageID']; ?>"/>

<h3>Template for the RSS description</h3>
<div><textarea name="rssTemplate" style="width: 80%; height: 100px;"><?php echo stripslashes($adminOptions['rssTemplate']) ?></textarea></div>
<div style="color:gray; font-size:small;">Options: %title% %event% %link% %startdate% %starttime% %enddate% %endtime% %author% %location% %category%</div>


<div class="submit">

<input type="submit" name="saveEventsRSSSettings" value="<?php echo "Save" ?>" /></div>

</form>

</div>
