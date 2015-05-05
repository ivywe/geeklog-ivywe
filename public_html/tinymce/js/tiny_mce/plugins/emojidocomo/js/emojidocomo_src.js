var EmojidocomoDialog = {
	init : function(ed) {
		tinyMCEPopup.resizeToInnerSize();
	},

	insert : function(file) {
		var ed = tinyMCEPopup.editor, dom = ed.dom;
		ed.execCommand('mceInsertContent', false, dom.createHTML('img', {
			src : tinyMCEPopup.getWindowArg('plugin_url') + '/img/' + file,
			border : 0
		}));
	}
};

tinyMCEPopup.onInit.add(EmojidocomoDialog.init, EmojidocomoDialog);

var Emojidocomo_pallet = {
	
	init : function() {
		var pallet_container = document.getElementById('emojidocomo_pallet_container');
		var hex_range = [
				[0xf89f, 0xf8fc],
				[0xf94f, 0xf949],
				[0xf950, 0xf957],
				[0xf95b, 0xf95e],
				[0xf972, 0xf97e],
				[0xf980, 0xf9fc]
			];
		var i, j, filename, table_html, n = 1;
		table_html = '<table class="emojidocomo" border="0" cellspacing="0" cellpadding="0" style="background-image:url(img/pallet.png);">';
		for(i = 0; i < hex_range.length; i++){
			for(j = hex_range[i][0]; j <= hex_range[i][1]; j++,n++){
				filename = j.toString(16) + '.gif';
				if(n % 16 == 1)
					table_html += '<tr>';
				table_html += '<td><p><img src="img/12px.gif" border="0" onclick="EmojidocomoDialog.insert(\'' + filename + '\');" onmouseover="this.style.border=\'solid 1px #33f\';" onmouseout="this.style.border=\'\';" /></p></td>';
				if(n % 16 == 0)
					table_html += '</tr>';
			}
		}
		table_html += '</table>';
		pallet_container .innerHTML = table_html;
	}
};