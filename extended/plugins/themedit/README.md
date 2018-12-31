Geeklog Theme Editor Plugin [v1.2.3]
====================================
by mystral-kk 

Table of Contents
-----------------

* Description
* Version History
* Thanks!
* Installing The Plugin
* Uninstalling The Plugin
* Upgrading The Plugin
* Usage
* License

### Description

The Theme Editor is an online theme editor which allows you to edit template files (*.thtml) and cascading style sheet file (*.css).  With this plugin, you can edit Geeklog themes on the Web, without uploading theme files.

### Version History

Date         | Version | Description                                                                                                                                                                                                                                                                                                                                                                                    
------------ | ------- | -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Dec 30, 2018 | 1.2.3   | **Fixed** a bug where the image uoload form didn't work with Geeklog-2.1.2.                                                                                                                                                                                                                                                                                             
Jan 10, 2017 | 1.2.2   | **Upgraded** to work with PHP-7 and Geeklog-2.1.2.                                                                                                                                                                                                                                                                                                                
Apr 22, 2012 | 1.2.1   | **Fixed** a bug which happens under some conditions while uninstalling the plugin.                                                                                                                                                                                                                                                                                      
Nov 6, 2011  | 1.2.0   | **New** Implemented auto-install feature introduced in Geeklog-1.6.                                                                                                                                                                                                                                                                                                       
Jul 4, 2010  | 1.1.5   | **Fixed** the wrong <meta> tag and <br> tags.                                                                                                                                                                                                                                                                                                                       
Nov 4, 2009  | 1.1.4   | **Fixed** an error when you choose a language other than English or Japanese.                                                                                                                                                                                                                                                                                       
Sep 26, 2008 | 1.1.3   | **Upgraded** to prevent include-files to be directly accessed in some case-insensitive file systems (e.g. MS Windows).                                                                                                                                                                                                                                              
Sep 14, 2008 | 1.1.2   | **Upgraded** to work well with PHP-4.x by not using htmlentities() and html_entity_decode() functions.                                                                                                                                                                                                                                                              
Aug 18, 2008 | 1.1.1   | **Upgraded** to properly work with DokuWiki plugin.                                                                                                                                                                                                                                                                                                                 
Aug 11, 2008 | 1.1.0   | **New** Upgraded to properly work with Geeklog-1.5.0 as well.<br>**New** Upgraded to use Config UI with GL-1.5.0.                                                                                                                                                                                                                                   
Mar 29, 2007 | 1.0.5   | **Fix** to properly handle a path containing an apostrophe.<br>**New** Added a new option to make all themes available automatically.  (See Configuring the plugin below.)<br>**New** Added a new option to make all theme-related files (*.thtml, *.css) available automatically.  (See Configuring the plugin below.)
Dec 21, 2006 | 1.0.4   | **New** Added a functionality of uploading images to the images directory under each theme directory<br>**New** Changed file name dropdown list so that the purposes of the files, instead of file names themselves, will appear.                                                                                                                     
Nov 7, 2006  | 1.0.3   | **Fixed** to deal with the case where default language is not Japanese (Thanks, Kemal and Tsuchi).<br>**New** Added many more template file names(Thanks, Ivy).                                                                                                                                                                                        
Nov 2, 2006  | 1.0.2   | **Fixed** to deal with the case where magic_quotes_gpc is on (Thanks, samstone).                                                                                                                                                                                                                                                                                           
Oct 1, 2006  | 1.0.0   | Initial version                                                                                                                                                                                                                                                                                                                                                                                

### Thanks!

* Geeklog.jp members for useful suggestions
* Geeklog Core Team for developing and maintaining Geeklog, the Secure CMS.

### Install instruction for the Geeklog Theme Editor plugin

In the following descriptions

* <span class="geeklog"><geeklog_dir></span> is the directory where the system config.php file resides
* <span class="admin"><admin></span> is the directory where the administration files reside (usually, under <span class="public"><public_html></span>)

1.  Back up your Geeklog Database.  The themedit plugin adds tables to your Geeklog database.  You can do this with the built in admin backup facility.
2.  Uncompress the themedit plugin archive while in the <span class="geeklog"><geeklog_dir></span>/plugins directory.  The archive will create a directory called themedit in the plugins directory.
3.  Create the admin directory.  Under your <span class="admin"><admin></span>/plugins/ directory create a directory called themedit.
4.  Change to your <span class="geeklog"><geeklog_dir></span>/plugins/themedit/ directory.  Copy the files in the admin directory to the <span class="admin"><admin></span>/plugins/themedit/ directory your created in step 3.
5.  Edit the config.php in the themedit directory and confirm the table prefix (the same as Geeklog table prefix by default) and modify the names of themes and files you would like to edit with the Theme Editor plugin.
6.  Log in to your Geeklog as a root user and run install.php in your <span class="admin"><admin></span>/plugins/themedit/ directory.  The install page will tell you if the install was successful or not.  If not, examine Geeklog system errorlog for possible problems.  The themedit plugin should now be installed and functioning.  Clicking on the themedit Icon will take you to the admin page.
7.  Set up security.  On install only the root users have access to themedit administration.  You can delegate control for the functions through the user and group editors.
8.  **[***** EXTRA STEP 1 FOR THEME EDITOR PLUGIN *****]**  Change the permissions of the directories where *.thtml files resides to **757**.  Change the permissions of the *.thtml files to **646**.
9.  **[***** EXTRA STEP 2 FOR THEME EDITOR PLUGIN *****]**  Change the permissions of the files preview.html and preview.css, which reside in the <span class="admin"><admin></span>/plugins/themedit/ directory, to **646**.

NOTE: The Theme Editor plugin uses JavaScript a lot.  For better functionality, it is strongly recommended that you enable JavaScript.

### Uninstall instruction for the Geeklog Theme Editor plugin

1.  Run the install.php page in your <span class="admin"><admin></span>/plugins/themedit directory.  This will remove all the data from your database.
2.  Delete the two plugin directories created in the install process:  <span class="geeklog"><geeklog-dir></span>/plugins/themedit/ and <span class="admin"><admin></span>/plugins/themedit/
3.  **[***** EXTRA STEP FOR THEME EDITOR PLUGIN *****]**  Restore the permissions of the directories and files you changed during the installation.  For example, change the permissions of the directories to 755 and those of the files to 644.

### Upgrading The Plugin

First, after you download and unpack a new archive, upload all the files to the Web server.  Then, log in as admin and go to the Plugin Editor.  Click the edit icon and click "upgrade" button.

### Usage

The Theme Editor plugin is so simple that you can use it almost intuitively.

1.  Choose a theme you want to edit at a dropdown menu.  
    **NOTE**: theme names you can choose from are defined as the 

    <tt>$_THM_CONF['allowed_themes']</tt> array in 

    <tt>config.php</tt>.  Please edit it as you like.

2.  Choose a file you want to edit at a dropdown menu.  
    **NOTE**: file names you can choose from are defined as the 

    <tt>$_THM_CONF['allowed_files']</tt> array in 

    <tt>config.php</tt>.  Please edit it as you like.

3.  A list of template vars available in the current file will be displayed.
4.  When you hit one of the buttons with a template var name on it, the corresponding tag will be inserted into the caret position.  
    **NOTE**: you have to enable JavaScript to use these buttons.
5.  When you hit the "**Preview**" button, the current content will be previewed in another page.
6.  When you hit the "**Save**" button, the current content will be saved on the Web.
7.  When you hit the "**Initialize**" button, the content of the file will be reverted to what it used to be when you installed the Theme Editor plugin.
8.  When you hit the "**Image**" button, a simple image browser will be displayed.  Supported file types are 'jpg', 'jpeg', 'png', and 'gif'.  In the screen, you can choose a theme and a directory from dropdown lists.  When you want to upload an image file to the current directory, hit the 'Browse...' button and select an image file to upload and then hit the 'Upload' button.  If you want to delete an image file(s), check all the files you'd like to and hit the 'Delete' button.  **The deleting process will be carried out WITHOUT CONFIRMATION, so be very careful!**  There is no undoing it.

### License

The Theme Editor plugin is licensed under the GPLv2 or later.

<div id="copyright">Copyright c 2006-2018 mystral-kk</div>
