<?php
/*
Plugin Name: EventsRss
Plugin URI: http://daniel.lienert.cc/projekte/events-rss/
Description: RSS feed for Events Plugin by Arnan de Gans. The output of the feed description is fully customizable. To view the feed, use a link like http://yourblog.tld/?eventsrss.
Version: 0.0.2
Author: Daniel Lienert
Author URI: http://daniel.lienert.cc
Min WP Version: 2.7
Max WP Version: 2.8
*/


/*
   LICENCE
 
    Copyright 2009  Daniel Lienert  (email : daniel@lienert.cc)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

define('DEBUG', false);

if (DEBUG) {
	ini_set('display_errors','1');
	ini_set('display_startup_errors','1');
	error_reporting(E_ALL ^ E_NOTICE);
} else {
	error_reporting(E_ERROR);
}

/*
 * Installer
 * 
 */
function eventsRSSInstaller(){
    global $wpdb;
    include(dirname(__FILE__).'/inc/installer.php');
}

/*
 * Admin Page
 * 
 */

if (!function_exists("eventsRSSAdmin")) {
    function eventsRSSAdmin() {
        /*
    	global $dl_pluginSeries;
        if (!isset($dl_pluginSeries)) {
            return;
        }
        */
        if (function_exists('add_options_page')) {
   			 add_options_page('Events RSS', 'Events RSS', 10, basename(__FILE__), 'printEventsRSSAdminPage');
        }
    }   
}

function printEventsRSSAdminPage() {
	include(dirname(__FILE__).'/inc/admin_page.php');
}

/*
 * Frontend XML Creator
 */

function getEventsRSSFeed() {
	$dl_eventsRSS = new eventsRss();
	$dl_eventsRSS->displayRSS();
}

class eventsRss {
	
	static $eventRSSConfigName = "eventRSSConfig";
	protected $eventRSSConfig;
	protected $eventsLanguage;
	
	/**
	 * 
	 * @return unknown_type
	 * @author Daniel Lienert <daniel@lienert.cc>
	 */
	public function __construct() {
		$this->loadConfig();
	}
	
	/**
	 * Creates and displays the RSS
	 * 
	 * @return unknown_type
	 * @author Daniel Lienert <daniel@lienert.cc>
	 * @since 15.07.2009
	 */
	public function displayRSS() {
		$items = $this->loadData();
		$lastUpdated = 0;
		
		foreach($items as $item) {
			if((int)$item->rss_released == 0) { 
				
				$item->rss_released = time();
				$guid = md5($item->id . $item->title . $item->pre_message . $item->thetime);
				$item->guid = $guid;
				$this->saveRSSReleaseDate($item->id, $guid);
			}
			
			$item->description  = $this->parseTemplate(htmlspecialchars_decode($this->eventRSSConfig['rssTemplate']), $item);
			$lastUpdated = $item->rss_released > $lastUpdated ? $item->rss_released : $lastUpdated;
		}
		
		$eventListPageURL = get_permalink($this->eventRSSConfig['eventsPageID']);
		
		include(dirname(__FILE__).'/inc/rssTemplate.php');
		
		exit();
	}
		
	/**
	 * 
	 * @return unknown_type
	 * @author Daniel Lienert <daniel@lienert.cc>
	 * @since 15.07.2009
	 */
	protected function loadData() {
		global $wpdb;
		
		$qstring = sprintf("
		SELECT 
		events.*,
		wr.rss_released, wr.rss_guid,
		ec.name as categoryname
		FROM wp_events events
		left outer join wp_events_categories ec on events.category = ec.id 
		left outer join wp_events_rss wr on wr.event_id = events.id
		where thetime > %s
		order by rss_released desc", time());
		
		return $wpdb->get_results($qstring);
	}
	
	/**
	 * set the release date to now
	 * @param $eventID
	 * @return unknown_type
	 * @author Daniel Lienert <daniel@lienert.cc>
	 * @since 15.07.2009
	 */
	protected function saveRSSReleaseDate($eventID, $guid) {
		global $wpdb;
		
		$qstring =  sprintf('insert into wp_events_rss (event_id, rss_released, rss_guid) VALUES (%s,%s,"%s");', $eventID, time(), $guid);
		$wpdb->query($qstring);
	}
	
	/**
	 * load the admin options from the database
	 * @return unknown_type
	 * @author Daniel Lienert <daniel@lienert.cc>
	 */
	protected function loadConfig() {
		$this->eventRSSConfig = get_option(self::$eventRSSConfigName); 
		$this->eventsLanguage = get_option('events_language');
	}
	
	/**
	 * Parse the template and insert the values
	 * 
	 * @param $template
	 * @return unknown_type
	 * @author Daniel Lienert <daniel@lienert.cc>
	 */
	protected function parseTemplate($template, $item) {
		$template = str_replace('%title%', $item->title, $template);
		$template = str_replace('%event%', $item->pre_message, $template);
		$template = str_replace('%link%', '<a href="'.$item->link.'">' . $item->link . '</a>', $template);
		$template = str_replace('%startdate%', date('d.m.Y', $item->thetime), $template);
		$template = str_replace('%starttime%', date('H:i', $item->thetime), $template);
		$template = str_replace('%enddate%', date('d.m.Y', $item->theend), $template);
		$template = str_replace('%endtime%', date('H:i', $item->theend), $template);
		$template = str_replace('%author%', $item->author, $template);
		$template = str_replace('%location%', $item->location, $template);
		$template = str_replace('%category%', $item->category, $template);
		
		return $template;
	}
	
	/**
	 * Static function to return the config key
	 * @return unknown_type
	 * @author Daniel Lienert <daniel@lienert.cc>
	 */
	static function getConfigKey() {
		return self::$eventRSSConfigName;
	}
	
}

/**
 * Actions and Filters
 */

add_action('activate_' . dirname(plugin_basename(__FILE__)).'/events_rss.php', 'eventsRSSInstaller');

add_action('admin_menu', 'eventsRSSAdmin'); 

if (isset($_GET['eventsrss'])) {
    add_action('init', 'getEventsRSSFeed');
}


?>