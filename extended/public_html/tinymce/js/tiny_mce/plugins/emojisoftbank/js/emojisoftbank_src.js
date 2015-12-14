var EmojisoftbankDialog = {
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

tinyMCEPopup.onInit.add(EmojisoftbankDialog.init, EmojisoftbankDialog);

var Emojisoftbank_pallet = {
	
	init : function() {
		var t = this;
		t.hex_range = [
				[
					[0xe001,0xe05a],
					[0xe101,0xe15a]
				],
				[
					[0xe201,0xe253],
					[0xe301,0xe34d]
				],
				[
					[0xe401,0xe44c],
					[0xe501,0xe537]
				]
			];
		t.pallet_html = new Array();
		t.pallet_id = 0;
		t.pallet_container = document.getElementById('emojisoftbank_pallet_container');
		var i, j, k, filename, n;
		for(i = 0; i < t.hex_range.length; i++){
			n = 1;
			t.pallet_html[i] = '<table class="emojisoftbank" border="0" cellspacing="0" cellpadding="0" style="background-image:url(img/pallet'+i+'.png);">';
			for(k = 0; k < t.hex_range[i].length; k++){
				if(t.hex_range[i][k].length == 1){
					j = t.hex_range[i][k][0];
					var filename = j.toString(16);
					if(n % 16 == 1)
						t.pallet_html[i] += '<tr>';
					t.pallet_html[i] += '<td><p><img src="img/15px.gif" border="0" onclick="EmojisoftbankDialog.insert(\''+filename+'.gif\');" onmouseover="this.style.border=\'solid 1px #33f\';" onmouseout="this.style.border=\'\';" /></p></td>';
					if(n % 16 == 0)
						t.pallet_html[i] += '</tr>';
					n++;
				}else{
					for(j = t.hex_range[i][k][0]; j <= t.hex_range[i][k][1]; j++,n++){
						var filename = j.toString(16);
						if(n % 16 == 1)
							t.pallet_html[i] += '<tr>';
						t.pallet_html[i] += '<td><p><img src="img/15px.gif" border="0" onclick="EmojisoftbankDialog.insert(\''+filename+'.gif\');" onmouseover="this.style.border=\'solid 1px #33f\';" onmouseout="this.style.border=\'\';" /></p></td>';
						if(n % 16 == 0)
							t.pallet_html[i] += '</tr>';
					}
				}
			}
			t.pallet_html[i] += '</table>';
		}

		t.pallet_container.innerHTML = t.pallet_html[0];
	},
	
	prev_pallet : function() {
		var t = this;
		if(t.pallet_id == 0){
			t.pallet_id = t.hex_range.length - 1;
		}else{
			t.pallet_id--;
		}
		pallet_container = document.getElementById('emojisoftbank_pallet_container');
		pallet_container.innerHTML = t.pallet_html[t.pallet_id];
	},
	
	next_pallet : function() {
		var t = this;
		if(t.pallet_id == t.hex_range.length - 1){
			t.pallet_id = 0;
		}else{
			t.pallet_id++;
		}
		pallet_container = document.getElementById('emojisoftbank_pallet_container');
		pallet_container.innerHTML = t.pallet_html[t.pallet_id];
	}
};
