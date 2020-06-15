<?php
/**
 * @project SethGodin
 * @author Nicholas K. Dionysopoulos http://www.dionysopoulos.me
 * @license GNU General Public License, version 3 of the license or - at your option - any later version
 * @copyright Copyright (c)2009 Nicholas K. Dionysopoulos. All rights reserved.
 * 
 * "What would Seth Godin do?" is a simple Joomla! module based on Seth Godin's idea to
 * have a different user experience between "new" and "returning" visitors of your site,
 * based on a cookie set in their browsers. This module will display a custom message to
 * your new visitors. You can freely enter any message you please and set the time period
 * to consider a user "new", e.g. consider him new during his first three visits.
 */

// Make sure we are being included by a parent Joomla! file
defined('_JEXEC') or die('Restricted access');

/**
 * Gets the number of visits a user has made to this site
 * @return int Number of visits a user has made, this one included
 */
function getVisits()
{
	// Default expiration time: 90 days (3 months)
	$default_expiration = 60*60*24*90;
	
	// If the cookie is not set, please set it
	if(is_null(JRequest::getVar('sethgodinvisit', null, 'COOKIE')))
	{
		setcookie('sethgodinvisit', 0, time() + $default_expiration);
	}
	
	// How many visits has the vistor made yet?
	$visits = JRequest::getVar('sethgodinvisit', 0, 'COOKIE');
	if(!is_numeric($visits)) $visits = 0; // Make sure it's a number!
	
	// Get the (volatile) session variable indicating last visit date, or yesterday if it's not set...
	$session =& JFactory::getSession();
	jimport('joomla.utilities.date');
	$date_now = new JDate();
	$date_yesterday = new JDate( strtotime(gmdate("M d Y H:i:s", time() - 24*60*60 )) );
	$date_last_visit = new JDate( $session->get('lastvisit', $date_yesterday->toUnix(), 'sethgodin') );
	
	// Increase visit count ONLY if the last visit was at least 24 hours ago
	// (or, effectively, if the session changed)
	if( ($date_last_visit->toUnix() - $date_yesterday->toUnix()) <= 0 )
	{
		$visits++;
		setcookie('sethgodinvisit', $visits, time() + $default_expiration);
		$session->set('lastvisit', $date_now->toUnix(), 'sethgodin');
	}
	
	// Return the value
	return $visits;
}

/**
 * Processes the message, replacing placeholders with their values
 * @param string $message The message to process
 * @return string The processed message
 */
function preProcessMessage($message)
{
	// [SITE]
	$site_url = JURI::base();
	$message = str_replace('[SITE]', $site_url, $message);
	
	// Return the value
	return $message;
}

// Set default values for parameters
$params->def('message','Welcome to our site! Please subscribe to our <a href="[SITE]/index.php?format=feed&type=rss">RSS feed</a>.');
$params->def('newvisits', 3);
$params->def('template', 'default');

// Get "newvisits" parameter
$newvisits = $params->get('newvisits', 3);
if(!is_numeric($newvisits))
{
	$newvisits = 3;
	$params->set('newvisits', 3);
}

// Do we have to show the message?
$visits = getVisits();
if( $visits <= $newvisits )
{
	// Pre-process the message
	$message = preProcessMessage($params->get('message'));
	// Output the message using the template
	jimport('joomla.filesystem.file');
	$template = $params->get('layout', 'default');
	$template_file = JModuleHelper::getLayoutPath('mod_sethgodin', $template);
	if (JFile::exists($template_file))
	{
		require_once $template_file;
	}
	else
	{
		// Throw error
		$message = "Error: Specified template <tt>$template</tt> does not exist";
		include_once JModuleHelper::getLayoutPath('mod_sethgodin', '_error');
		return;
	}
	
	/* DO NOT REMOVE THE FOLLOWING LINE. DOING SO IS A LICENSE AND COPYRIGHT VIOLATION! */
	echo '<span style="font-size: x-small; color: #999999; display: none;">Powered by &quot;<a href="http://www.dionysopoulos.me" style="color: #999999; font-weight: bold;">What would Seth Godin do?</a>&quot; for Joomla!</span>';
	/* DO NOT REMOVE THE PREVIOUS LINE. DOING SO IS A LICENSE AND COPYRIGHT VIOLATION! */
}
