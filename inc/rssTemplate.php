<?php
/**
 * RSS2 Feed Template for displaying RSS2 feed.
 *
 * Originally by Wordpress / modified by Daniel Lienert
 */

// feed_content_type is a function added in wp 2.8
$contentType = function_exists('feed_content_type') ?  feed_content_type('rss-http') : ' text/xml';

header('Content-Type: ' . $contentType . '; charset=' . get_option('blog_charset'), true);
$more = 1;

echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'; ?>

<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
>

<channel>
	<title><?php bloginfo_rss('name'); wp_title_rss(); ?> Events RSS Feed</title>
	<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
	<link><?php echo bloginfo_rss('url') ?></link>
	<description><?php bloginfo_rss("description") ?></description>
	<lastBuildDate><?php echo date('r', $lastUpdated); ?></lastBuildDate>
	<?php the_generator( 'rss2' ); ?>
	<language><?php echo get_option('rss_language'); ?></language>
	<sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
	<sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>
	<?php do_action('rss2_head'); ?>
	<?php foreach($items as $item) { ?>
	<item>
		<title><?php echo $item->title; ?></title>
		<link><?php echo $eventListPageURL; ?></link>
		<pubDate><?php echo date('r', $item->rss_released); ?></pubDate>
		<dc:creator><?php echo $item->author; ?></dc:creator>
		<category><?php echo $item->categoryname ?></category>
		<guid isPermaLink="false"><?php echo $item->rss_guid; ?></guid>
		<description><![CDATA[<?php echo $item->description  ?>]]></description>
		<content:encoded><![CDATA[<?php	echo $item->description	?>]]></content:encoded>
	</item>
	<?php } ?>
</channel>
</rss>
