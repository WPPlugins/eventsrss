<?php
/***************************************************
* installer.php
* 
* 
* @author Daniel Lienert <daniel@lienert.cc>
****************************************************/

/**
 * Add database table
 */

$qstring = " CREATE TABLE IF NOT EXISTS wp_events_rss (
`id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`event_id` INT( 10 ) NOT NULL ,
`rss_released` INT( 14 ) NOT NULL,
`rss_guid` VARCHAR( 250 ) NOT NULL
) ENGINE = MYISAM COMMENT = 'Saves the first release of a event entry'";

$wpdb->query($qstring);

/*
 * Add config to option table
 */
$adminOptions['eventsPageID'] = 1;
$adminOptions['rssTemplate'] = 
'<h3>%startdate% - %starttime%: %title%</h3>
<div style="font-weight:bold">%location%</div>
<div>%event%</div>
<div>%link%</div>';

update_option(eventsRss::getConfigKey(), $adminOptions);
?>