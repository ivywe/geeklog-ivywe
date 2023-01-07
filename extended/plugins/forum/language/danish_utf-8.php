<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Geeklog Forums Plugin 2.9.0                                               |
// +---------------------------------------------------------------------------+
// | danish_utf-8.php                                                          |
// | Language defines for all text                                             |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2004 by the following authors:                              |
// |    Sniper12                                                               |
// |                                                                           |
// | Copyright (C) 2000,2001 by the following authors:                         |
// |    Tony Bibbs       tony AT tonybibbs DOT com                             |
// |                                                                           |
// | Forum Plugin Authors                                                      |
// |    Mr.GxBlock                                        www.gxblock.com      |
// |    Matthew DeWyer   matt AT mycws DOT com            www.cweb.ws          |
// |    Blaine Lang      geeklog AT langfamily DOT ca     www.langfamily.ca    |
// +---------------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or             |
// | modify it under the terms of the GNU General Public License               |
// | as published by the Free Software Foundation; either version 2            |
// | of the License, or (at your option) any later version.                    |
// |                                                                           |
// | This program is distributed in the hope that it will be useful,           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
// | GNU General Public License for more details.                              |
// |                                                                           |
// | You should have received a copy of the GNU General Public License         |
// | along with this program; if not, write to the Free Software Foundation,   |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.           |
// +---------------------------------------------------------------------------+

global $LANG32;

###############################################################################
# Array Format:
# $LANGXX[YY]:  $LANG - variable name
#               XX    - file id number
#               YY    - phrase id number
###############################################################################

$LANG_GF00 = array(
    'pluginlabel' => 'Forum',
    'searchlabel' => 'Forum',
    'statslabel' => 'Samlet antal indlæg',
    'statsheading1' => 'Forum Top 10 Sete Emner',
    'statsheading2' => 'Forum Top 10 Besvarede Emner',
    'statsheading3' => 'Der er ingen relevante emner',
    'useradminmenu' => 'Forum Indstillinger',
    'access_denied' => 'Adgang Nægtet',
    'autotag_desc_forum' => '[forum: id alternate title] - Displays a link to a forum topic using the text \'here\' as the title. An alternate title may be specified but is not required.'
);

$LANG_GF01 = array(
    'FORUM' => 'Forum',
    'FORUMS' => 'Forums',
    'FORUMCATEGORYNAME' => '%s Forum Category',
    'FORUMNAME' => '%s Forum',
    'ALL' => 'All',
    'YES' => 'Ja',
    'NO' => 'Nej',
    'NEW' => 'Ny',
    'NEXT' => 'Næste',
    'ERROR' => 'Fejl!',
    'CONFIRM' => 'Bekræft',
    'UPDATE' => 'Opdatér',
    'SAVE' => 'Gem indstillinger',
    'CANCEL' => 'Fortryd',
    'ON' => 'On: ',
    'ON2' => '&nbsp;&nbsp;<b>På: </b>',
    'BY' => 'Af: ',
    'RE' => 'Vedr.: ',
    'DATE' => 'Dato',
    'VIEWS' => 'Set',
    'REPLIES' => 'Svar',
    'NAME' => 'Navn:',
    'DESCRIPTION' => 'Beskrivelse: ',
    'TOPIC' => 'Emne',
    'TOPICS' => 'Emner',
    'TOPICSUBJECT' => 'Emne Titel',
    'HOMEPAGE' => 'Hjem',
    'SUBJECT' => 'Titel',
    'HELLO' => 'Hej ',
    'MOVED' => 'Moved',
    'POSTS' => 'Indlæg',
    'LASTPOST' => 'Nyeste Indlæg',
    'POSTEDON' => 'Postet på',
    'POSTEDBY' => 'Posted By',
    'PAGES' => 'Sider',
    'TODAY' => 'I dag kl ',
    'REGISTERED' => 'Bruger fra',
    'ORDERBY' => 'Sortér efter:',
    'ORDER' => 'Sorteret efter:',
    'USER' => 'Bruger',
    'GROUP' => 'Group',
    'ANON' => 'Anonym:',
    'ADMIN' => 'Admin',
    'AUTHOR' => 'Forfatter',
    'NOMOOD' => 'Intet Humør',
    'REQUIRED' => '[Obligatorisk]',
    'OPTIONAL' => '[Frivillig]',
    'SUBMIT' => 'Godkend',
    'PREVIEW' => 'Gennemse',
    'REMOVE' => 'Fjern',
    'EDIT' => 'Redigér',
    'DELETE' => 'Slet',
    'MERGE' => 'Merge',
    'OPTIONS' => 'Indstillinger:',
    'MISSINGSUBJECT' => 'Blankt Emne',
    'MIGRATE_NOW' => 'Migrate Now',
    'FILTERLIST' => 'Filter List',
    'SELECTFORUM' => 'Select Forum',
    'DELETEAFTER' => 'Delete after',
    'TITLE' => 'Title',
    'COMMENTS' => 'Comments',
    'SUBMISSIONS' => 'Submissions',
    'HTML_FILTER_MSG' => 'Filtered HTML Allowed',
    'HTML_FULL_MSG' => 'Full HTML Allowed',
    'HTML_MSG' => 'HTML Allowed',
    'CENSOR_PERM_MSG' => 'Censored Content',
    'ANON_PERM_MSG' => 'View Anonymous Posts',
    'POST_PERM_MSG1' => 'Able to post',
    'POST_PERM_MSG2' => 'Anonymous users can post',
    'GO' => 'GO',
    'STATUS' => 'Status:',
    'ONLINE' => 'online',
    'OFFLINE' => 'offline',
    'back2parent' => 'Overordnet Debat',
    'forumname' => '',
    'category' => 'Kategori: ',
    'loginreqview' => '<b>Beklager, du skal %s registrere en brugerkonto</a> eller %s logge ind</a> for at bruge disse forummer</b>',
    'loginreqpost' => '<b>Beklager, kun registrerede brugere må skrive indlæg.</b>',
    'nolastpostmsg' => 'N/A',
    'no_one' => 'Ingen.',
    'back2top' => 'Tilbage til toppen',
    'TEXTMODE' => 'Text Mode:',
    'HTMLMODE' => 'HTML Mode:',
    'TopicPreview' => 'Gennemse debat indlæg',
    'moderator' => 'Moderator',
    'admin' => 'Admin',
    'DATEADDED' => 'Tilføjet på Dato',
    'PREVTOPIC' => 'Forrige Emne',
    'NEXTTOPIC' => 'Næste Emne',
    'RESYNC' => 'Synkronisér',
    'RESYNCCAT' => 'ReSync Category Forums',
    'EDITICON' => 'Redigér',
    'QUOTEICON' => 'Citér',
    'ProfileLink' => 'Profil',
    'WebsiteLink' => 'Hjemmeside',
    'PMLink' => 'PM',
    'EmailLink' => 'Email',
    'FORUMSUBSCRIBE' => 'Følg dette forum',
    'FORUMUNSUBSCRIBE' => 'Un-Subscribe to this forum',
    'FORUMSUBSCRIBE_TRUE' => 'Subscribe:Enabled',
    'FORUMSUBSCRIBE_FALSE' => 'Subscribe:Disabled',
    'NEWTOPIC' => 'Nyt Emne',
    'POSTREPLY' => 'Svar',
    'SubscribeLink' => 'Subscribe',
    'unSubscribeLink' => 'Un-Subscribe',
    'SubscribeLink_TRUE' => 'Subscribe:Enabled',
    'SubscribeLink_FALSE' => 'Subscribe:Disabled',
    'SUBSCRIPTIONS' => 'Adviseringer',
    'TOP' => 'Gå til toppen',
    'PRINTABLE' => 'Udskriftsvenlig Udgave',
	'printed_subject' => 'Forum Subject: %s',
    'USERPREFS' => 'Bruger Indstillinger',
    'SPEEDLIMIT' => '"Dit sidste indlæg var for %s sekunder siden.<br' . XHTML . '>Denne hjemmeside kræver mindst %s sekunder mellem hvert indlæg."',
    'ACCESSERROR' => 'ADGANG NÆGTET',
    'ACTIONS' => 'Actions',
    'DELETEALL' => 'Delete all selected records',
    'DELCONFIRM' => 'Are you sure you want to Delete selected records?',
    'DELALLCONFIRM' => 'Are you sure you want to Delete ALL selected records?',
	'DELCONFIRM_PARENT' => 'Are you sure you want to Delete this parent topic? It means that any replies it has will also be deleted.',
	'DELALLCONFIRM_PARENT' => 'Are you sure you want to Delete ALL selected records? If you have selected parent topics, then any replies to those topics will also be deleted. Parent Topics are the ones that show Views greater than 0.',	
    'STARTEDBY' => 'Started by:',
    'WARNING' => 'Warning',
    'MODERATED' => 'Moderators: %s',
    'LASTREPLYBY' => 'Last reply by:&nbsp;%s',
    'UID' => 'UID',
    'FORUMMENU' => 'Forum Menu',
    'INDEXPAGE' => 'Forum Index',
    'FEATURE' => 'Feature',
    'SETTING' => 'Setting',
    'MARKALLREAD' => 'Mark All Read',
    'MSG_NO_CAT' => 'No Categories or Forums Defined',
    'FORUMPOSTS' => 'Forum Posts',
    'CODE' => 'Code',
    'FONTCOLOR' => 'Font Color',
    'FONTSIZE' => 'Font Size',
    'CLOSETAGS' => 'Close Tags',
    'CODETIP' => 'Tip: Styles can be applied quickly to selected text',
    'TINY' => 'Tiny',
    'SMALL' => 'Small',
    'NORMAL' => 'Normal',
    'LARGE' => 'Large',
    'HUGE' => 'Huge',
    'DEFAULT' => 'Default',
    'DKRED' => 'Dark Red',
    'RED' => 'Red',
    'ORANGE' => 'Orange',
    'BROWN' => 'Brown',
    'YELLOW' => 'Yellow',
    'GREEN' => 'Green',
    'OLIVE' => 'Olive',
    'CYAN' => 'Cyan',
    'BLUE' => 'Blue',
    'DKBLUE' => 'Dark Blue',
    'INDIGO' => 'Indigo',
    'VIOLET' => 'Violet',
    'WHITE' => 'White',
    'BLACK' => 'Black',
    'b_help' => 'Bold text: [b]text[/b]',
    'i_help' => 'Italic text: [i]text[/i]',
    'u_help' => 'Underline text: [u]text[/u]',
    'q_help' => 'Quote text: [quote]text[/quote]',
    'c_help' => 'Code display: [code]code[/code]',
    'l_help' => 'List: [list]text[/list]',
    'o_help' => 'Ordered list: [olist]text[/olist]',
    'p_help' => '[img]http://image_url[/img]  or [img w=100 h=200][/img]',
    'w_help' => 'Insert URL: [url]http://url[/url] or [url=http://url]URL text[/url]',
    'a_help' => 'Close all open bbCode tags',
    's_help' => 'Font color: [color=red]text[/color]  Tip: you can also use color=#FF0000',
    'f_help' => 'Font size: [size=7]small text[/size]',
    'h_help' => 'Click to view more detailed help'
);

$LANG_GF02 = array(
    'msg01' => 'Sorry you must register to use these forums',
    'msg02' => 'You should not be here!<br' . XHTML . '>Restricted access to this forum only',
    'msg03' => 'Please wait while you are redirected',
    'msg05' => '<center><em>Beklager, der er endnu ikke lavet nogen emner.</em></center>',
    'msg07' => 'Brugere online:',
    'msg14' => 'Beklager, du har fået forbud mod at poste indlæg.<br' . XHTML . '>',
    'msg15' => 'Hvis du mener at dette er en fejl, skal du kontakte <a href="mailto:%s?subject=Guestbook IP Ban">hjemmesidens Webmaster</a>.',
    'msg18' => 'Fejl! Ikke alle de krævede felter er udfyldt eller også er de for korte.',
    'msg19' => 'Dit indlæg er blevet gemt.',
    'msg22' => '- Forum Indlæg Advisering',
	'reply_to_thread_msg' => "A reply has been made to the thread '%s' by %s.",
	'topic_started_msg' => "This topic was started by %s in the %s forum.",
	'view_reply_at_msg' => "You may view the reply at:",
	'new_topic_msg' => "A new topic '%s' has been posted by %s in the '%s' forum on the %s website.",
	'view_topic_at_msg' => "You may view it at:",
	'edit_to_post_msg' => "An edit has been made to a post in the thread '%s' by %s.",
	'view_edit_at_msg' => "You may view the edited post at:",
	'stop_reply_notify_msg' => "You are receiving this email because you have chosen to be notified when a reply has been made to this topic. To stop receiving notifications on this topic go to:",
	'stop_new_notify_msg' => "You are receiving this email because you have chosen to be notified when a new topic has been posted to this forum. To stop receiving notifications for this forum go to:",
	'great_day_msg' => "Have a great day!",
    'msg33' => 'Forfatter: ',
    'msg36' => 'Humør:',
    'msg38' => "Advisér mig\n ved svar ",
    'msg40' => '<br' . XHTML . '>Beklager, men du har allerede bedt om at blive adviseret, når der kommer svar på dette emne.<br' . XHTML . '><br' . XHTML . '>',
    'msg44' => '<p style="margin:0px; padding:5px;">Du har for nuværende ingen adviseringer.</p>',
    'msg49' => '(Læst %s gange) ',
    'msg55' => 'Indlæg slettet.',
    'msg56' => 'IP-adresse Forbudt.',
	'msg57' => 'IP removed from being Banned.',
    'msg59' => 'Normalt Emne',
    'msg60' => 'Nyt Indlæg',
    'msg61' => 'Fastgjort Emne',
    'msg62' => 'Advisér mig ved svar',
    'msg64' => 'Er du sikker på at du vil slette Emne %s med titlen: %s ?',
    'msg65' => '<br' . XHTML . '>Dette er første indlæg i emnet, så alle andre efterfølgende indlæg vil også blive slettet.',
    'msg68' => 'Do you really want to remove the ban for the ip address: %s?',
    'msg69' => 'Vil du virkeligt forbyde IP-addressen: %s?',
    'msg71' => 'Ingen funktion valgt, vælg et indlæg og dernæst en Moderator funktion.<br' . XHTML . '>Bemærk: Du skal være Moderator for at anvende disse funktioner.',
    'msg72' => 'Hvis dette indlæg læses online, har du ikke ret til at udføre Moderator funktionen.',
    'msg74' => 'Nyeste %s Forum Indlæg',
    'msg75' => 'Top %s Emner Sorteret Efter Læst',
    'msg76' => 'Top %s Emner Sorteret Efter Besvaret',
    'msg77' => '<br' . XHTML . '><p style="padding-left:10px;">Du burde ikke være her!<br' . XHTML . '>Der er begrænset adgang til Forummet.</p>',
    'msg83' => '<br' . XHTML . '><br' . XHTML . '>Du skal være logget ind for at anvende denne Forum facilitet.</p>',
    'msg84' => 'Markér alle emner læst',
    'msg85' => 'Side:',
    'msg86' => '&nbsp;Nyeste %s indlæg af&nbsp;',
    'msg87' => '<br' . XHTML . '>Advarsel: Dette emne er låst af en moderator.<br' . XHTML . '>Det er ikke tilladt at skrive nye poster for emnet',
    'msg88' => 'Brugere med Forum Aktivitet',
    'msg88b' => 'Forum Activity Only',
    'msg89' => 'Mine Aktiverede Adviseringer',
    'msg101' => 'Forum Regler:',
    'msg103' => 'Forum Skift:',
    'msg106' => 'Vælg et Forum',
    'msg108' => 'Normalt Forum',
    'msg109' => 'Låst Emne',
    'msg110' => 'Skifter til rettelse af indlæg..',
    'msg111' => 'Nye indlæg siden dit sidste besøg:',
    'msg112' => 'Se alle nye indlæg',
    'msg113' => 'Se nye indlæg',
    'msg114' => 'Låst Emne',
    'msg115' => 'Fastgjort Emne med Nye Indlæg',
    'msg116' => 'Låst Emne med Nye Indlæg',
    'msg117' => 'Søg i Alle',
    'msg118' => 'Søg i dette Forum',
    'msg119' => 'Forum Søgnings Resusltater for forspørgsel:',
    'msg120' => 'Mest populære indlæg efter',
    'msg121' => 'Alle tider er %s. Klokken er nu %s.',
    'msg122' => 'Populær Grænse:',
    'msg123' => 'Antal indlæg før et emne betegnes som populært',
    'msg126' => 'Søge Linier:',
    'msg127' => 'Antal Linier der vises i søge resultater',
    'msg128' => 'Brugere Pr. Side:',
    'msg129' => 'Gælder for oversigten over aktive brugere',
    'msg130' => 'Se Anonyme Indlæg:',
    'msg131' => 'Hvis du vælger Nej vil anonyme indlæg ikke vises',
    'msg132' => 'Advisér Altid:',
    'msg133' => 'Hvis du vælger Ja vil du altid blive adviseret om svar<br' . XHTML . '>på emner du opretter eller besvarer',
    'msg134' => 'Advisering tilføjet',
    'msg135' => 'Du vil nu blive adviseret, hver gang der kommer et nyt indlæg i dette Forum.',
    'msg136' => 'Du skal vælge et Forum du vil adviseres om.',
    'msg137' => 'Advisering for emne aktiveret',
	'msg138a' => 'Listed below are all the forum topics you have subscribed to. This means for these subscriptions you will receive an email notification when someone replies to one of your subscribed topics.',
	'msg138b' => 'Listed below are all the forums you have subscribed to. This means for these subscriptions you will receive an email notification when a new topic is created in one of these forums, or someone replies to a topic. Please note that deleting a forum subscription will also delete any Topic Exception Notifications associated with the forum (but not any individual topic notifications).',
	'msg138c' => 'Listed below are all the topics that belong to the forum(s) you have subscribed to (see Forum Notifications), but you have unsubscribed from and chosen not to receive any more topic reply email notifications for.',
	'msg139a' => 'Listed below are all the forum topics the user you are viewing has subscribed to. This means for these subscriptions the user will receive an email notification when someone replies to one of their subscribed topics. If "All Users" are selected then the User column contains the name of the account the notification is for.',
	'msg139b' => 'Listed below are all the forums the user you are viewing has subscribed to. This means for these subscriptions the user will receive an email notification when a new topic is created in one of these forums, or someone replies to a topic. Please note that deleting a forum subscription will also delete any Topic Exception Notifications associated with the forum (but not any individual topic notifications).  If "All Users" are selected then the User column contains the name of the account the notification is for.',
	'msg139c' => 'Listed below are all the topics that belong to the forum(s) the user has subscribed to (see Forum Notifications), but they have unsubscribed from and chosen not to receive any more topic reply email notifications for. If "All Users" are selected then the User column contains the name of the account the notification is for.',
    'msg142' => 'Advisering gemt.',
    'msg144' => 'Retur til emne',
    'msg146' => 'Asvisering Slettet',
    'msg147' => 'Forum [udskriftsvenlig udgave af emne %s]',
    'msg148' => 'Klik <a href="javascript:history.back()">HER</a> for at vende tilbage',
    'msg155' => 'Ingen indlæg af bruger.',
    'msg156' => 'Samlet antal Forum indlæg',
    'msg157' => 'Nyeste 10 Forum indlæg',
    'msg158' => 'Nyeste 10 Forum indlæg af ',
    'msg159' => 'Er du sikker på at du vil SLETTE de valgte Moderator poster?',
    'msg160' => 'Gå til Emnets sidste side',
    'msg163' => 'Indlæg flyttet',
    'msg164' => 'Markér alle Kategorier og Emner Læst',
    'msg166' => 'FEJL: Ugyldigt Emne, eller Emne ikke fundet',
    'msg167' => 'Adviseringsvalg',
    'msg168' => 'Hvis du vælger Nej, deaktiverer du alle email adviseringer',
    'msg169' => 'Retur til Brugerliste',
    'msg170' => 'Nyeste Forum Indlæg',
    'msg171' => 'Forum Adgangs Fejl',
    'msg172' => 'Emne findes ikke. Muligvis er det slettet.',
    'msg173' => 'Transferring to Post Message page..',
    'msg174' => 'Unable to BAN Member - Invalid or Empty IP Address',
    'msg175' => 'Return to Forum Listing',
    'msg176' => 'Select a member',
    'msg177' => 'All Members',
    'msg178' => 'Parent Posts Only',
    'msg179' => 'Content generated in: %s seconds',
    'msg180' => 'Forum Posting Alert',
    'msg181' => 'You don\'t have access to any other forum as a moderator',
    'msg182' => 'Moderator Confirmation',
    'msg183' => 'New topic was created from this post in forum: %s',
    'msg184' => 'Notify Once Only',
    'msg185' => 'Notifications will only be sent once for forums and topics which have multiple new posts since your last visit.',
    'msg186' => 'New Topic Title',
    'msg187' => 'Return to topic - click <a href="%s">here</a>',
    'msg188' => 'Click to go directly to last post',
    'msg189' => 'Error: You can not edit this post anymore',
    'msg190' => 'Silent Edit',
	'msg190b' => 'When enabled and the forum post is saved, no notifications will be sent to users subscribed to this topic (or forum) about this update, and the forum post date will not be changed to the current date and time.',
    'msg191' => 'Edit not permitted. Allowable edit timeframe expired or you need moderator rights',
    'msg192' => 'Completed ... Migrated %s topics and %s comments.',
    'msg193' => 'STORY&nbsp;&nbsp;TO&nbsp;&nbsp;FORUM&nbsp;&nbsp;MIGRATION&nbsp;&nbsp;UTILITY',
    'msg194' => 'Quiet Forum',
    'msg195' => 'Click to Jump to Forum',
    'msg196' => 'View the main forum index',
    'msg197' => 'Mark all Categories read',
    'msg198' => 'Update your forum settings',
    'msg199' => 'View or remove forum notifications',
    'msg200' => 'Site members report',
    'msg201' => 'Popular topics',
    'msg202' => 'No new posts',
    'msg300' => 'This Forum Post by an anonymous user has been blocked. To enable see your <a href="/forum/userprefs.php">Forum User Preferences</a>.',
	'msg301' 	=> 'Really mark all topics in all forums and categories read?',
	'msg301a' 	=> 'All topics in all forums and categories have now been marked as read.',
	'msg302' 	=> 'Really mark all topics read in this forum?',
	'msg302a' 	=> 'All topics in this forum have now been marked as read.',
	'msg303' 	=> 'Really mark all topics in all forums in this category read?',
	'msg303a' 	=> 'All topics in all forums from this category have now been marked as read.',
    'PostReply' => 'Opret nyt svar',
    'PostTopic' => 'Opret nyt emne',
    'EditTopic' => 'Ret Emne',
    'quietforum' => 'Forum has no new topics'
);
$LANG_GF02['adminconfirmation']   = 'Administrator Confirmation';
$LANG_GF02['num_forumposts']   = '%s Forum Post(s)';
$LANG_GF02['gl_topics_desc']   = '<em>Important:</em> These are Geeklog Topics (which you have Edit access for) which can be assigned to the root parent forum topic post (which then applies to the entire fourm topic) or the Forum, or Category itself. If Geeklog Topics are assigned to the Category or Forum they will then be inherited by and items below it (unless that item is assigned to another Geeklog Topic).<br' . XHTML . '><br' . XHTML . '>Since Blocks (and their positions) are assigned to Geeklog Topics this allows you to select the Geeklog Topic you want and then have these Blocks display for the forum topic. This also allows the blocks postion "Forum Show Topic" to be used more effectively.<br' . XHTML . '><br' . XHTML . '>The Geeklog Topic assignment(s) for forum topics does not affect the permissions of the forum (like it does with articles). If the visitor has access to view the forum post but not the topic assigned to it then "All Topics" is assumed. If no Geeklog Topics are assigned to the forum topic then the default "All Topics" is assumed.';
$LANG_GF02['gl_topics_inherit_category'] = '%s (inherited from Category)';
$LANG_GF02['gl_topics_inherit_forum'] = '%s (inherited from Forum)';
$LANG_GF02['gl_topics_inherit_config'] = '%s (inherited from Config)';
$LANG_GF02['gl_topics_assigned']   = 'Geeklog Topic Assigned:';

$LANG_GF03 = array(
    'delete' => 'Slet Indlæg',
    'edit' => 'Ret Indlæg',
    'move' => 'Flyt Emne',
    'split' => 'Split Topic',
    'banippost'         => 'Ban IP from Posting',
    'banippostremove'   => 'Remove Ban for IP from Posting',
    'banip'             => 'Ban IP from Site',
    'banipremove'       => 'Remove Ban for IP from Site',
    'banipmsg'      	=> 'IP has been banned from site',
    'banipremovemsg'	=> 'Ban has been removed for IP from Site',  
    'movetopic' => 'Flyt Emne',
    'movetopicmsg' => '<br' . XHTML . '> Du kan flytte emnet <b>%s</b> til følgende forummer:',
    'splittopicmsg' => '<br' . XHTML . '>Create a new Topic with this post: "<b>%s</b>"<br' . XHTML . '><em>By:</em>&nbsp;%s&nbsp <em>On:</em>&nbsp;%s',
    'selectforum' => 'Select new forum:',
    'lockedpost' => 'Add Reply Post',
    'splitheading' => 'Split thread option:',
    'splitopt1' => 'Move all posts from this point',
    'splitopt2' => 'Move only this one post'
);

$LANG_GF04 = array(
    'label_forum' => 'Forum Profil',
    'label_location' => 'Sted',
    'label_aim' => 'AIM Navn',
    'label_yim' => 'YIM Navn',
    'label_icq' => 'ICQ Identitet',
    'label_msnm' => 'MSN Messenger Navn',
    'label_interests' => 'Interesser',
    'label_occupation' => 'Erhverv'
);

$LANG_GF05 = array(
    'aim_link' => '&nbsp;<a href="aim:goim?screenname=',
    'aim_linkend' => '>',
    'aim_hello' => '&amp;message=Hi.+Are+you+there?',
    'aim_alttext' => 'AIM:&nbsp;',
    'icq_link' => '&nbsp;',
    'icq_alttext' => 'ICQ #:&nbsp;',
    'msn_link' => '&nbsp;<a href="javascript:MsgrApp.LaunchIMUI(',
    'msn_linkend' => ')">',
    'msn_alttext' => 'Messenger:&nbsp;',
    'yim_link' => '&nbsp;<a href="ymsgr:sendIM?',
    'yim_linkend' => '">',
    'yim_alttext' => 'YIM:&nbsp;'
);

$LANG_GF06 = array(
    1 => 'Statistics',
    2 => 'Settings',
    3 => 'Forums',
    4 => 'Moderator',
    5 => 'Convert',
    6 => 'Messages',
	7 => 'Subscriptions',
    8 => 'IP Mgmt'
);

$LANG_GF07 = array(
    1 => 'View Forums',
    2 => 'Preferences',
    3 => 'Popular Topics',
    4 => 'Subscriptions',
    5 => 'Members'
);

$LANG_GF08 = array(
    1 => 'Topic Notifications',
    2 => 'Track Forum Notifications',
    3 => 'Topic Exception Notifications'
);

$LANG_GF09 = array(
    'edit' => 'Edit',
    'email' => 'Email',
    'home' => 'Home',
    'lastpost' => 'Last Post',
    'pm' => 'PM',
    'profile' => 'Profile',
    'quote' => 'Quote',
    'website' => 'Website',
    'newtopic' => 'New Topic',
    'replytopic' => 'Post Reply'
);

$LANG_GF91 = array(
    'gfstats' => 'Forum Statistik',
    'statsmsg' => 'Her er de nuværende statistikker for dit forum:',
    'totalcats' => 'Antal Kategorier:',
    'totalforums' => 'Antal Forummer:',
    'totaltopics' => 'Antal Emner:',
    'totalposts' => 'Antal Indlæg:',
    'totalviews' => 'Antal Læst:',
    'avgpmsg' => 'Gennemsnitligt antal indlæg pr.:',
    'category' => 'Kategori:',
    'forum' => 'Forum:',
    'topic' => 'Emne:',
    'avgvmsg' => 'Gennemsnitlig antal læst pr.:'
);

$LANG_GF92 = array(
    'gfsettings' => 'Forum Indstillinger',
    'showiframe' => 'Vis Emne gennemsyn:',
    'showiframedscp' => 'Vis emne gennemsyn (i en ramme) i bunden ved svar på et indlæg',
    'topicspp' => 'Emner pr. Side:',
    'topicsppdscp' => 'Antallet af emner der skal vises på forum startsiden',
    'postspp' => 'Indlæg per Side:',
    'postsppdscp' => 'Antallet af indlæg der skal vises pr. side',
    'setsavemsg' => 'Indstillinger gemt.'
);

$LANG_GF93 = array(
    'gfboard' => 'Forum Administrator',
    'addcat' => 'Tilføj Kategori',
    'addforum' => 'Tilføj Forum',
    'catorder' => 'Kategori rækkefølge',
    'catadded' => 'Kategori tilføjet.',
    'catdeleted' => 'Kategori slettet',
    'catedited' => 'Kategori slettet.',
    'forumadded' => 'Forum tilføjet.',
    'forumaddError' => 'Fejl ved oprettelse af forum.',
    'forumdeleted' => 'Forum slettet',
    'forummerged' => 'Forum Merged',
    'forumnotmerged' => 'Forum cannot be merged since no other forums available to be merged with.',
    'forumedited' => 'Forum rettet',
    'forumordered' => 'Forum rækkefølge ændret',
    'back' => 'Tilbage',
    'addnote' => 'Bemærk: Du kan rette disse værdier.',
    'editforumnote' => 'Ret Forum Detaljer for: <b>"%s"</b>',
    'deleteforumnote1' => 'Ønsker du at slette forummet <b>"%s"</b>&nbsp;?',
    'deleteforumnote2' => 'Alle emner under den vil også blive slettet.',
    'mergeforumnote1' => 'Merge the forum <b>"%s"</b> with?',
    'mergeforumnote2' => 'Forum to merge into:',
    'editcatnote' => 'Ret Kategori detaljer for: <b>"%s"</b>',
    'deletecatnote1' => 'Ønsker du at slette kategorien <b>"%s"</b>&nbsp;?',
    'deletecatnote2' => 'Alle forummer og emner postet under denne kategori vil også blive slettet.',
    'undercat' => 'Under kategori',
    'groupaccess' => 'Gruppe Adgang: ',
    'action' => 'Handlinger',
    'forumdescription' => 'Forum Beskrivelse',
    'posts' => 'Indlæg',
    'ordertitle' => 'Rækkefølge',
    'ModEdit' => 'Ret',
    'ModMove' => 'Flyt',
    'ModStick' => 'Fastgør',
    'ModBan' => 'Forbyd',
    'addmoderator' => "Tilføj\nModerator",
    'delmoderator' => " Slet\nValgte",
    'moderatorwarning' => '<b>Advarsel: Ingen forummer defineret</b><br' . XHTML . '><br' . XHTML . '>Opret Forum Kategorier og opret mindst ét forum<br' . XHTML . '>før du tilføjer Moderatorer',
    'private' => 'Private Forum',
    'filtertitle' => 'Select Moderator records to view',
    'addmessage' => 'Add new Moderator',
    'allowedfunctions' => 'Allowed Functions',
    'userrecords' => 'User Records',
    'grouprecords' => 'Group Records',
    'filterview' => 'Filter View',
    'readonly' => 'Readonly Forum',
    'readonlydscp' => 'Only the Moderator can post to this forum',
    'hidden' => 'Hidden Forum',
    'hiddendscp' => 'Forum does not show in the forum index',
    'hideposts' => 'Hide New posts',
    'hidepostsdscp' => 'Updates will not show in the New Posts Blocks or RSS Feeds',
    'mod_title' => 'Forum Moderatorer',
    'allforums' => 'All Forums',
    'namerequired' => 'Name is required.',
	'resyncedmsg' => 'ReSynch and Clean completed for selected category or forum.<br><ul><li>%s topic posts re-synced.</li><li>%s orphan topic records (those without a parent topic) found and fixed.</li><li>%s orphan records found and cleaned from all other Forum tables.</li></ul>'
);

$LANG_GF95 = array(
    'header1' => 'Discussion Board Messages',
    'header2' => 'Discussion Board Messages for forum&nbsp;&raquo;&nbsp;%s',
    'notyet' => 'Faciliteten findes endnu ikke',
    'delall' => 'Slet Alle',
    'delallmsg' => 'Er du sikker på at du vil slette alle indlæg i: %s?',
    'underforum' => '<b>Under Forum: %s (ID #%s)',
    'moderate' => 'Moderatorfunktioner',
    'nomess' => 'Der er endnu ikke postet noget! '
);

$LANG_GF96 = array(
    'ip' => 'IP',
    'enterip' => 'Enter below an IP address to ban',
    'gfipman' => 'IP-adresse Administration',
    'ban' => 'Forbyd',
    'noips' => 'Ingen IP-adresser er blevet forbudt endnu!',
    'unban' => 'Tillad',
    'ipbanned' => 'IP-addresse Forbudt',
    'banip' => 'Bekræft IP-adresse Forbud',
    'banipmsg' => 'Er du sikker på at du vil forbyde disse ip-adresser %s?',
    'specip' => 'Vælg hvilken IP-addresse du vil forbyde!',
    'ipunbanned' => 'IP-address Tilladt.',
    'noip' => 'You did not provide an IP address!'
);

$LANG_GF97 = array (
    'gfsubscriptions' => 'Forum Subscriptions'
);

$LANG_GF_SMILIES = array(
    'biggrin' => 'Big Grin',
    'smile' => 'Smile',
    'frown' => 'Frown',
    'eek' => 'Geek',
    'confused' => 'Confused',
    'cool' => 'Cool',
    'lol' => 'LOL',
    'angry' => 'Angry',
    'razz' => 'Razz',
    'oops' => 'Oops!',
    'surprise' => 'Surprised!',
    'cry' => 'Cry',
    'evil' => 'Evil',
    'twisted' => 'Twisted',
    'rolleye' => 'Rolling Eyes',
    'wink' => 'Wink',
    'exclaim' => 'Exclaimation',
    'question' => 'Question',
    'idea' => 'Idea',
    'arrow' => 'Arrow',
    'neutral' => 'Neutral',
    'green' => 'Mr. Green',
    'sick' => 'Sick',
    'tired' => 'Tired',
    'monkey' => 'Monkey'
);
$PLG_forum_MESSAGE1 = 'Forum Plugin Upgrade: Update completed successfully.';
$PLG_forum_MESSAGE2 = 'Forum Plugin upgrade: We are unable to update this version automatically. Refer to the plugin documentation.';
$PLG_forum_MESSAGE5 = 'Forum Plugin Upgrade failed - check error.log';

// Messages for the plugin upgrade
$PLG_forum_MESSAGE3001 = '';
$PLG_forum_MESSAGE3002 = $LANG32[9];

// Localization of the Admin Configuration UI
$LANG_configsections['forum'] = array(
    'label' => 'Forum',
    'title' => 'Forum Configuration'
);

$LANG_confignames['forum'] = array(
    'registration_required' => 'Login Required to View Posts?',
    'registered_to_post' => 'Login Required to Post?',
    'allow_notification' => 'Allow Notification?',
    'show_topicreview' => 'Show Topic Review when Replying?',
    'allow_user_dateformat' => 'Allow User defined Date Format?',
    'use_pm_plugin' => 'Use Private Message Plugin?',
    'show_topics_perpage' => 'Number of Topics to Show per Page',
    'show_posts_perpage' => 'Number of Posts to Show per Page',
    'show_messages_perpage' => 'Number of Message Lines per Page',
    'show_searches_perpage' => 'Number of Search Results per Page',
    'showblocks' => 'Block Columns to Show with Forum',
    'usermenu' => 'Type of User Menu',
    'likes_forum' => 'Forum Likes',
    'recaptcha' => 'reCAPTCHA',
    'show_subject_length' => 'Max Length of Subject',
    'min_username_length' => 'Min Length of Username',
    'min_subject_length' => 'Min Length of Subject',
    'min_comment_length' => 'Min Length of Post Content',
    'views_tobe_popular' => 'Number of Views to have Popular',
    'post_speedlimit' => 'Posting Speedlimit(sec)',
    'allowed_editwindow' => 'Timeframe(sec) to Allow Edit Posts',
    'allow_html' => 'Allow HTML Mode?',
    'post_htmlmode' => 'Set HTML Mode as Default?',
    'convert_break' => 'Convert Newlines to HTML &lt;BR&gt;?',
    'use_censor' => 'Use Geeklog Censoring?',
    'use_glfilter' => 'Use Geeklog Filtering?',
    'use_geshi' => 'Use Geshi Code Formatting?',
    'use_spamx_filter' => 'Use Spam-X Plugin?',
    'show_moods' => 'Enable Moods?',
    'allow_smilies' => 'Enable Smilies?',
    'use_smilies_plugin' => 'Use Smilies Plugin?',
    'avatar_width' => 'Width of Member Avatar',
    'show_centerblock' => 'Enable Centerblock?',
    'centerblock_homepage' => 'Enable Homepage Only?',
    'centerblock_numposts' => 'Number of Posts to Show',
    'cb_subject_size' => 'Max Length of Subject',
    'centerblock_where' => 'Placement on Page',
    'sideblock_numposts' => 'Number of Posts to Show',
    'sb_subject_size' => 'Max Length of Subject',
    'sb_latestpostonly' => 'Show Latest Post Only?',
    'sideblock_enable' => 'Enabled',
    'sideblock_isleft' => 'Display Block on Left',
    'sideblock_order' => 'Block Order',
    'sideblock_topic_option' => 'Topic Options',
    'sideblock_topic' => 'Topic',
    'sideblock_group_id' => 'Group',
    'sideblock_permissions' => 'Permissions',
    'level1' => 'Number of Posts of Level1',
    'level2' => 'Number of Posts of Level2',
    'level3' => 'Number of Posts of Level3',
    'level4' => 'Number of Posts of Level4',
    'level5' => 'Number of Posts of Level5',
    'level1name' => 'Name of Level1',
    'level2name' => 'Name of Level2',
    'level3name' => 'Name of Level3',
    'level4name' => 'Name of Level4',
    'level5name' => 'Name of Level5',
    'menublock_enable' => 'Enabled',
    'menublock_isleft' => 'Display Block on Left',
    'menublock_order' => 'Block Order',
    'menublock_topic_option' => 'Topic Options',
    'menublock_topic' => 'Topic',
    'menublock_group_id' => 'Group',
    'menublock_permissions' => 'Permissions'
);

$LANG_configsubgroups['forum'] = array(
    'sg_main' => 'Main Settings'
);

$LANG_tab['forum'] = array(
    'tab_main' => 'General Forum Settings',
    'tab_topicposting' => 'Topic Posting',
    'tab_centerblock' => 'Centerblock',
    'tab_sideblock' => 'Sideblock',
    'tab_rank' => 'Rank',
    'tab_menublock' => 'Menu Block'
);

$LANG_fs['forum'] = array(
    'fs_main' => 'General Forum Settings',
    'fs_topicposting' => 'Topic Posting',
    'fs_centerblock' => 'Centerblock',
    'fs_sideblock' => 'Sideblock',
    'fs_sideblock_settings' => 'Block Settings',
    'fs_sideblock_permissions' => 'Block Permissions',
    'fs_rank' => 'Rank',
    'fs_menublock' => 'Menu Block',
    'fs_menublock_settings' => 'Block Settings',
    'fs_menublock_permissions' => 'Block Permissions'
);

// Note: entries 0, 1, and 12 are the same as in $LANG_configselects['Core']
$LANG_configselects['forum'] = array(
    0 => array('True' => 1, 'False' => 0),
    1 => array('True' => true, 'False' => false),
    5 => array('Top Of Page' => 1, 'After Featured Story' => 2, 'Bottom Of Page' => 3),
    6 => array('Left Blocks' => 'leftblocks', 'Right Blocks' => 'rightblocks', 'All Blocks' => 'allblocks', 'No Blocks' => 'noblocks'),
    7 => array('Block Menu' => 'blockmenu', 'Navigation Bar' => 'navbar', 'None' => 'none'),
    12 => array('No access' => 0, 'Read-Only' => 2, 'Read-Write' => 3),
    13 => array('No access' => 0, 'Use' => 2),
    14 => array('No access' => 0, 'Read-Only' => 2),
    15 => array('All' => 'TOPIC_ALL_OPTION', 'Homepage Only' => 'TOPIC_HOMEONLY_OPTION', 'Select Topics' => 'TOPIC_SELECTED_OPTION'),
    16 => array('Disabled' => RECAPTCHA_NO_SUPPORT, 'reCAPTCHA V2' => RECAPTCHA_SUPPORT_V2, 'reCAPTCHA V2 Invisible' => RECAPTCHA_SUPPORT_V2_INVISIBLE),
    41 => array('False' => 0, 'Likes and Dislikes' => 1, 'Likes Only' => 2)
);

?>
