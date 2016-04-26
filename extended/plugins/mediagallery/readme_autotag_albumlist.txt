 --------------------------------------------------------------------------------
 Add Media album list and Media list autotags to Media Gallery WKZ plugin.
 Based on Media Gallery WKZ plugin (Ver 1.7.0) 
 2012/12/17 Ver 1.6.13 Based on Media Gallery WKZ plugin
 2013/04/22 Ver 1.6.14 Based on Media Gallery WKZ plugin
 2013/05/13 Ver 1.6.16 Based on Media Gallery WKZ plugin
 2013/11/11 Chenage autotag Medialist format.
 2013/12/09 Change autotags media - width:-1 then do not set image size
 2015/04/07 Change autotags albumlist medialist limitcnt default
 2015/04/07 Change autotags medialist sort
 2015/04/09 Add medialist template: admin for display HTML
 2016/01/08 Ver 1.7.0 Based on Media Gallery WKZ plugin
 2016/04/12 Change autotags medialist src
 -------------------------------------------------------------------------------
  Format:　[albumlist:albumid limitcnt:xx order:xx XXXX]
		Display sub album of album:albumid.
		limitcnt: number. default:0 (no limit)
		order:RANDOM ... random. 
		      or Album id order.
		<ul>
		<li>Sub albun 1 thumbnail of album<br />Description</li>
		<li>Sub albun 2 thumbnail of album<br />Description</li>
		<li>Sub albun 3 thumbnail of album<br />Description</li>
		...
		</ul>
           e.g. [albumlist:3 limitcnt:10 order:RANDOM class1 class2 class3]
		
  Format:　[medialist:albumid theme:xx limitcnt:xx sort:asc src:xx {class}]
		Display Media list by using templates.
		theme:default:
			templates/medialist/default/autotag_medialist.thtml
			templates/medialist/default/autotag_medialist_col.thtml
		limitcnt: number. default:0 (no limit)
		sort: asc or desc default:asc

           e.g. [medialist:3 theme:list limitcnt:10 class1 class2 class3]
			theme:list:
				templates/medialist/list/autotag_medialist.thtml
				templates/medialist/list/autotag_medialist_col.thtml

 -------------------------------------------------------------------------------
 directory
 mediagalley
   ├ function.php  (change)
   │ 
   ├ include
   │ ├ autotags.php  (change)
   │ │ 
   │ ├ autotags_add.inc  (add) 
   │ │ 
   ├ templates
   │ ├ medialist
   │ │ │ 
   │ │ ├ admin
   │ │ │ │
   │ │ │ ├ autotag_medialist.thtml  (add) ...for autotag:medialist
   │ │ │ ├ autotag_medialist_col.thtml  (add) ...for autotag:medialist
   │ │ │ │
   │ │ ├ default
   │ │ │ │
   │ │ │ ├ autotag_medialist.thtml  (add) ...for autotag:medialist
   │ │ │ ├ autotag_medialist_col.thtml  (add) ...for autotag:medialist
   │ │ │ 
   │ │ ├ (theme name)


