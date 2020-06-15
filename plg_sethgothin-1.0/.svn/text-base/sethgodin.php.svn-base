<?php
/**
 * @package SethGodinPlugin
 * @author Nicholas K. Dionysopoulos http://www.dionysopoulos.me
 * @license GNU General Public License, version 3 of the license or - at your option - any later version
 * @copyright Copyright (c)2009 Nicholas K. Dionysopoulos. All rights reserved.
 * @version 1.0
 * 
 * "What would Seth Godin do?" is a simple Joomla! plug-in based on Seth Godin's idea to
 * have a different user experience between "new" and "returning" visitors of your site,
 * based on a cookie set in their browsers. This module will display a custom message to
 * your new visitors. You can freely enter any message you please and set the time period
 * to consider a user "new", e.g. consider him new during his first three visits. The message
 * is displayed only inside articles (on the top & bottom of them)
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * Example Content Plugin
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 		1.5
 */
class plgContentSethgodin extends JPlugin
{
	
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param object $subject The object to observe
	 * @param object $params  The object that holds the plugin parameters
	 * @since 1.5
	 */
	function plgContentExample( &$subject, $params )
	{
		parent::__construct( $subject, $params );
	}

	/**
	 * Returns true if we are viewing a single article and the id of the article being processed
	 * is the same as the id of the article being displayed. In case you're wondering, it is
	 * possible that they differ if, for example, we have a "newsflash" module in our page ;)
	 * @param $this_id int The id of the article being processed
	 * @return bool
	 */
	function _isArticleDisplay($this_id)
	{
		$option = JRequest::getCmd('option','');
		$view  = JRequest::getCmd('view','');
		$id = JRequest::getString('id','');
		if( strpos($id,':') != 0 )
		{
			$id = substr($id, 0, strpos($id,':') );
		}
		return ($option == 'com_content') && ($view == 'article') && ($id == $this_id);	
	}
	
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

	/**
	 * Example before display content method. Message displayed below title, above the
	 * metadata (written by... etc.)
	 *
	 * Method is called by the view and the results are imploded and displayed in a placeholder
	 *
	 * @param 	object		The article object.  Note $article->text is also available
	 * @param 	object		The article params
	 * @param 	int			The 'page' number
	 * @return	string
	 */
	function onBeforeDisplayContent( &$article, &$params, $limitstart )
	{
		global $mainframe;


		if($this->_isArticleDisplay($article->id))
		{
			// Load plugin params info
			$plugin = & JPluginHelper::getPlugin('content', 'sethgodin');
			$pluginParams = new JParameter($plugin->params);
			$message = $pluginParams->get('message','<strong>Maybe you should configure this plugin?</strong>');
			$newvisits = $pluginParams->get('newvisits', 3);
			if(!is_numeric($newvisits))
			{
				$newvisits = 3;
				$pluginParams->set('newvisits', 3);
			}
			
			// Do we have to show the message?
			$visits = $this->getVisits();
			if( $visits <= $newvisits )
			{
				// Pre-process the message
				$message = $this->preProcessMessage($message);

				// Output the message
				
				/* DO NOT REMOVE THE FOLLOWING LINE. DOING SO IS A LICENSE AND COPYRIGHT VIOLATION! */
				$message .= '<span style="font-size: x-small; color: #999999; display: none;">Powered by &quot;<a href="http://www.dionysopoulos.me" style="color: #999999; font-weight: bold;">What would Seth Godin do?</a>&quot; for Joomla!</span>';
				/* DO NOT REMOVE THE PREVIOUS LINE. DOING SO IS A LICENSE AND COPYRIGHT VIOLATION! */
				
				return $message;
			}			
		}
		else
			return '';
	}

	/**
	 * Example after display content method.
	 *
	 * Method is called by the view and the results are imploded and displayed in a placeholder
	 *
	 * @param 	object		The article object.  Note $article->text is also available
	 * @param 	object		The article params
	 * @param 	int			The 'page' number
	 * @return	string
	 */
	function onAfterDisplayContent( &$article, &$params, $limitstart )
	{
		global $mainframe;

		// The message below the article is the same as the message above it
		return $this->onBeforeDisplayContent($article, $params, $limitstart);
	}

	/**
	 * Example before save content method
	 *
	 * Method is called right before content is saved into the database.
	 * Article object is passed by reference, so any changes will be saved!
	 * NOTE:  Returning false will abort the save with an error.
	 * 	You can set the error by calling $article->setError($message)
	 *
	 * @param 	object		A JTableContent object
	 * @param 	bool		If the content is just about to be created
	 * @return	bool		If false, abort the save
	 */
	function onBeforeContentSave( &$article, $isNew )
	{
		global $mainframe;

		return true;
	}

	/**
	 * Example after save content method
	 * Article is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the content is saved
	 *
	 *
	 * @param 	object		A JTableContent object
	 * @param 	bool		If the content is just about to be created
	 * @return	void
	 */
	function onAfterContentSave( &$article, $isNew )
	{
		global $mainframe;

		return true;
	}

}
