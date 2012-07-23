##########
EXT: Piwik
##########

Extension Key: piwik

Copyright 2009, Jörg Winter, <winter@b-net1.de>

Additions 2012 by Ingo Pfennigstorf <i.pfennigstorf@gmail.com>

Please report all bugs and feature request at http://forge.typo3.org/projects/show/extension-piwik

This document is published under the Open Content License available from http://www.opencontent.org/opl.shtml

The content of this document is related to TYPO3 - a GNU/GPL CMS/Framework available from www.typo3.com

.. contents::

************
Introduction
************

The extension implements Piwik tracking in TYPO3-based websites.

Piwik is a new open source (GPL license) web analytics product. It gives interesting reports on your website visitors, your popular pages, the search engines keywords they used, the language they speak… and so much more. To learn more about Piwik, go to http://piwik.org.

This extension is a reimplementation of m1_google_analytics, but for Piwik. It uses documentation and some code resemblance from Frank Naegler's original Piwik extension and from Ulrich Wünsche's Piwika2 extension.

=======
Credits
=======

This extension had some revisions and includes ideas from other extensions to be honest to the guys who had the ideas here some credits – thank you guys

* Frank Nägler **piwik** first version of this extension
* Jörg Winter **bn1_piwik** first piwik extensions with new tracking code
* Ulrich Wuensche **piwika2** force valid (X)HTML in the output
* Ingo Pfennigstorf for cleanups and fixing the tracking of non-javascript visitors.

================
What does it do?
================

The extension inserts the javascript-code used for tracking website users with Piwik, directly before the closing body-tag.

For integrating Piwik as a TYPO3 Backend module please have a look at `EXT:piwikintegration <http://typo3.org/extensions/repository/view/piwikintegration/current/>`_

Piwik implements the Javascript tracking code for the new Piwik Javascript tracking API that is included since Piwik 0.4

The following Piwik Tracking Functions can be configured with Typoscript.

* piwikTracker.setDocumentTitle()
* piwikTracker.trackGoal()
* piwikTracker.setDomains()
* piwikTracker.enableLinkTracking()
* piwikTracker.setIgnoreClasses()
* piwikTracker.setDownloadClasses()
* piwikTracker.setLinkClasses()
* piwikTracker.setLinkTrackingTimer()
* piwikTracker.setDownloadExtensions()
* piwikTracker.addDownloadExtensions()

If you are a logged in Backend-User your Frontend hits will not be tracked by Piwik.

************
Users manual
************

To install Piwik on your website follow these steps:

1. Install Piwik and create your Piwik account, for more information see http://piwik.org/
2.  To enable Piwik tracking in your website, we need to know your site ID and hostname of the piwik installation.
3. Install the plugin from TYPO3 online repository and enable it
4. Add this configuration options to your template (both parameters are required): 

.. code-block:: html

	config.tx_piwik {
		piwik_idsite = (your site id)
		piwik_host = (host/path of your piwik installation without URL-scheme)
	}

That's all you need to start tracking your visitors. See “Configuration” section for more config options and description of the parameters.

*************
Configuration
*************

All configuration parameters for this extension should be in your template TypoScript, inside the config.tx_piwik container. Please see the option descriptions below.

===============
Static Template
===============

Please include the shipped static template as usual

Required  parameters
--------------------

Defines your Piwik site id and host/path to your piwik installation. This parameter needs to be set for the extension to work. If your Sites ID is 3 and the path your piwik installation is stats.myhost.rl/piwik/piwik.php, your TypoScript in the template should look like:

.. code-block:: html

	config.tx_piwik {
		piwik_idsite = 3
		piwik_host = stats.myhost.rl/piwik/
 	}

Once you set up this parameter, you can check if everything worked correctly by looking at your pages HTML source. The piece of code that drives Piwik is inserted right before the closing tag of <body> container:

.. code-block:: html

	<!-- Piwik -->
	<script type="text/javascript" src="http://stats.myhost.rl/piwik/piwik.js"></script>
	<script type="text/javascript">
		/* <![CDATA[ */
			try {
				var piwikTracker=Piwik.getTracker("http://stats.myhost.rl/piwik/piwik.php",3);
				piwikTracker.enableLinkTracking();
				piwikTracker.trackPageView();
			} catch(err) {}

		/* ]]> */
	</script>
	<noscript><p><img src="http://stats.myhost.rl/piwik/piwik.php?idsite=3&rec=1" style="border:0" alt=""/></p></noscript>
	<!-- /Piwik -->
	</body>

=====================
Configuration Options
=====================

The following table shows you all configuration options for the Piwik JavaScript API. All these parameters you have to set in  your TypoScript Template like the required parameters piwik_idsite and piwik_host.

.. list-table:: Configuration options
   :widths: 25 15 25 35
   :header-rows: 1

   * - Property
     - Data type
     - Default
     - Description
   * - piwik_host
     - string
     -
     - The host / path to your piwik installation without URL-scheme
   * - piwik_idsite
     - int
     -
     - The site id of your Piwik account
   * - actionName
     - string/ stdWrap
     - Empty String
     - This parameter controls the action name, which will be tracked by Piwik. This parameter has one specialkeyword: “TYPO3” this means, that the page title will be used for this parameter. All other values will be rendered directly to this JavaScript variable. If you want to use other JavaScript objects like document.title you can do so. If you want to overwrite the parameter with an static string, like from TS, you have to quote the value with single quotes. This extension will not quote the value of this parameter.
   * - trackGoal
     - int
     -
     - ID of the goal to be triggered
   * - setDownloadExtensions
     - string
     - 7z|aac|avi|csv|doc|exe|
       flv|gif|gz|jpe?g|js|
       mp(3|4|e?g)|mov|pdf|
       phps|png|ppt|rar|sit|tar|
       torrent|txt|wma|wmv|xls|
       xml|zip
     - A list of file extensions, divided by a pipe symbol.
   * - addDownloadExtensions
     - string
     - 7z|aac|avi|csv|doc|exe|
       flv|gif|gz|jpe?g|js|
       mp(3|4|e?g)|mov|pdf|
       phps|png|ppt|rar|sit|tar|
       torrent|txt|wma|wmv|xls|
       xml|zip
     - A list of file extensions, divided by a pipe symbol.
   * - setDomains
     - string / list
     - By default all links to domains other than the current domain are considered outlinks. If you have multiple domains and don’t want to consider links to these websites as “outlinks” you can add this new javascript variable.
     - A comma separated list of host aliases for your site.
   * - setLinkTrackingTimer
     - int
     - 500
     - When a user clicks to download a file, or when he clicks on an outbound link, Piwik records it: it adds a small delay before the user is redirected to the requested file or link. We use a default value of 500ms, but you can set it shorter, with the risk that this time is not long enough to record the data in Piwik.
   * - enableLinkTracking
     - boolean
     - 1
     - To disable all the automatic downloads and outlinks tracking, you must set this parameter to 0
   * - setIgnoreClasses
     - string
     -
     - You can disable automatic download and outlink tracking for links with this CSS classes
   * - setDownloadClasses
     - string
     - If you want Piwik to consider a given link as a download, you can add the 'piwik_download' css class to the link.
     - With this parameter you can customize and rename the CSS class used to force a click to being recorded as a download
   * - setLinkClasses
     - string
     - With this parameter you can customize and rename the CSS class used to force a click to being recorded as an outlink.
     - If you want Piwik to consider a given link as an outlink (links to the current domain or to one of the alias domains), you can add the 'piwik_link' css class to the link.

*******************
TypoScript Examples
*******************

Using the stdWrap feature of the “actionName” property, to build a actionName hierarchy like a rootline navigation.

.. code-block:: html

	config.tx_piwik {
		piwik_idsite = 3
		piwik_host = stats.myhost.rl/piwik/
		actionName= TYPO3
		actionName {
			stdWrap {
				cObject = HMENU
				cObject {
					special=rootline
					special.range= 1 | -1
					includeNotInMenu = 1
					wrap = |/index
					1=TMENU
					1.itemArrayProcFunc = user_UrteileItemArrayProcFunc
					1.NO.allWrap=  |   /   |*| |   /   |*| |
					1.NO.doNotLinkIt = 1
				}
			}
		}
	}

**************
Known problems
**************

The function to prevent FE-Pagehits from tracking for logged in BE-Users will only work if FE and BE are on the same Domain.

**********
To-Do list
**********
http://forge.typo3.org/projects/extension-piwik/issues

*********
Changelog
*********

http://forge.typo3.org/projects/extension-piwik/repository