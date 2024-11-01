<html xmlns="http://www.w3.org/1999/xhtml">
<?php
define('DOING_AJAX', true);
require_once('../../../../../../wp-load.php');
global $shortcode_tags;
$sctags = array();
  foreach($shortcode_tags as $tag => $func) $sctags[] = '<option value="'.$tag.'">'.$tag.'</option>';
?>
  <head>
    <title>{#shortcode_editor.title}</title>
    <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
    <script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
    <script language="javascript" type="text/javascript">
    window.onload = function() {
      document.getElementsByTagName('form')[0].onsubmit = function() {
        tinyMCEPopup.execCommand('mceInsertContent', true, makeShortcode());
        tinyMCEPopup.close();
        return false;
      };
      document.getElementById('gse_new_prop_name').onkeydown = document.getElementById('gse_new_prop_value').onkeydown = function(e) {
				if((e.keyCode ? e.keyCode : e.charCode) == 13) { 
				  if(document.getElementById('gse_npn_label').style.display == 'none') updateProperty(); 
					else addProperty(); 
					return false; 
				}
			};
			document.getElementById('gse_new_prop_add').onclick = function(e) { addProperty(); };
      document.getElementById('shortcode_close').onclick = function() { tinyMCEPopup.close(); };
      document.getElementById('gse_properties').onchange = function() { checkProperty(); };
			document.getElementById('gse_uncheck_prop').onclick = function(e) { uncheckProperties(); };
			document.getElementById('gse_remove_prop').onclick = function(e) { removeProperties(); uncheckProperties(); };

			var scont = tinyMCEPopup.editor.selection.getContent(), tager = parent.gse_shortcode_er;
			if(scont) {
				var test = tager.exec(scont); // the test works fine only one in each two times, why?
				if(!test) test = tager.exec(scont); // this fixes the problem, but not explains ...
				if(test) {
					var tag = test[1], props = test[2] || '', cont = test[4] || '', b = document.getElementById('shortcode_submit');
					selectOption('gse_tag', tag);
					setProperties(props);
					document.getElementById('gse_content').value = cont;
					b.value = tinyMCEPopup.getLang('shortcode_editor.update');
					b.disabled = false;
					document.getElementById('gse_tag').disabled = true; // shortcode tag is not editable
				}
			}
      document.getElementById('gse_tag').focus();
    };
    
    function setProperties(props) {
      var parts = props.split(/\"/), p = [];
      for(var i = 0; i < parts.length; i += 2) {
        var n = parts[i].replace(/^\s+|\s+$/g, '').replace('=', ''), v = parts[i+1];
				if(n == 'sc_id') document.getElementById(n).value = v;
        else if(n && v) addOption('gse_properties', n+': '+v, n+'='+v);
      }
    }
    
    function uncheckProperties() {
			selectOption('gse_properties', '', true);
			document.getElementById('gse_new_prop_name').value = document.getElementById('gse_new_prop_value').value = '';
			document.getElementById('gse_remove_prop').disabled = document.getElementById('gse_uncheck_prop').disabled = true;
			document.getElementById('gse_npa_label').style.display = 'none'; 
			document.getElementById('gse_npn_label').style.display = 'inline'; 
			document.getElementById('gse_new_prop_add').onclick = function() { addProperty(); }; 
			document.getElementById('gse_new_prop_name').focus();
    }
    
    function addProperty() {
      var n = document.getElementById('gse_new_prop_name'), v = document.getElementById('gse_new_prop_value');
      if(!n.value) return n.focus();
      if(!v.value) return v.focus();
      addOption('gse_properties', n.value+': '+v.value, n.value+'='+v.value);
      n.value = v.value = '';
			n.focus();
    }
    
    function updateProperty() {
      var n = document.getElementById('gse_new_prop_name'), v = document.getElementById('gse_new_prop_value'), sel = document.getElementById('gse_properties');
      if(!n.value) return n.focus();
      if(!v.value) return v.focus();
			sel.options[sel.selectedIndex].innerHTML = n.value+': '+v.value;
			sel.options[sel.selectedIndex].value = n.value+'='+v.value;
			uncheckProperties();
    }
    
    function removeProperties() {
      removeSelectedOptions('gse_properties');
      document.getElementById('gse_remove_prop').disabled = true;
    }
    
    function setTag() {
      document.getElementById('shortcode_submit').disabled = document.getElementById('gse_tag').selectedIndex == 0;
    }
    
    function checkProperty() {
			var sel = document.getElementById('gse_properties'), selected = sel.selectedIndex;
      document.getElementById('gse_remove_prop').disabled = document.getElementById('gse_uncheck_prop').disabled = (selected == -1);
			if(selected > -1) {
				var pair = sel.options[selected].value.split('=');
				document.getElementById('gse_new_prop_name').value = pair[0]; 
				document.getElementById('gse_new_prop_value').value = pair[1];
				document.getElementById('gse_npa_label').style.display = 'inline'; 
				document.getElementById('gse_npn_label').style.display = 'none'; 
			  document.getElementById('gse_new_prop_add').onclick = function() { updateProperty(); }; 
			}
    }

    function addOption(sid, label, val) {
      var sel = document.getElementById(sid), opt = document.createElement('option');
      opt.text = label;
      opt.value = val;
      try { sel.add(opt, null); }
      catch(e) { sel.add(opt); } // IE only
    }

    function removeSelectedOptions(sid) {
      var sel = document.getElementById(sid);
      for(var i = sel.length - 1; i >= 0; i--) {
        if(sel.options[i].selected) sel.remove(i);
      }
    }

    function selectOption(sid, val, uncheck) {
      var sel = document.getElementById(sid);
			if(uncheck) sel.selectedIndex = -1;
      for(var i = 0; i < sel.options.length; i++) {
        if(sel.options[i].value == val) sel.options[i].selected = true;
      }
    }

    function makeShortcode(cod) {
      var tag = document.getElementById('gse_tag').value, 
			    props = document.getElementById('gse_properties').options, 
					cont = document.getElementById('gse_content').value, 
					scid = 0, 
					code = '['+tag;
      if(props.length) {
        for(var i = 0; i < props.length; i++) code += ' '+props[i].value.replace('=', '="')+'"';
      }
			var scid = document.getElementById('sc_id').value;
			if(!scid) scid = tinyMCEPopup.editor.plugins.shortcode_editor.getId();
			code += ' sc_id="'+scid+'"]';
      if(cont) code += cont+'[/'+tag+']';
			tinyMCEPopup.editor.plugins.shortcode_editor.cache(scid, code);
      return cod ? code : tinyMCEPopup.editor.plugins.shortcode_editor.toHTML(code);
    }

    </script>
    <style type="text/css">
    body { padding-top:5px; }
    form { padding:0; margin:0; }
    .p { margin:10px 0; }
    .p textarea, #gse_properties { width:100%; height:8em; }
    .small { width:100px; }
    .autow { width:auto; }
    .buttons, #gse_new_prop { text-align:right; }
    #gse_remove_prop, #gse_uncheck_prop { float:right; }
		#gse_tag { font-size:14px; padding:3px; }
    </style>
  </head>
  <body>
    <form action="" method="post" id="shortcode_dialog">
      <div class="p">
        <label for="gse_tag">{#shortcode_editor.tag}</label>
        <select id="gse_tag" name="gse_tag" onChange="setTag()">
          <option value="0">{#shortcode_editor.choose}</option>
          <?php print join("\n", $sctags); ?>
        </select>
      </div>
      <div class="p">
        <input type="button" id="gse_uncheck_prop" value="{#shortcode_editor.uncheck}" disabled="disabled" />
        <input type="button" id="gse_remove_prop" value="{#shortcode_editor.remove}" disabled="disabled" />
        <label for="gse_properties">{#shortcode_editor.properties}</label>
        <select id="gse_properties" size="8"></select>
        <div id="gse_new_prop">
          <label for="gse_new_prop_add" id="gse_npa_label" style="display:none">{#shortcode_editor.edit}</label>
          <label for="gse_new_prop_name" id="gse_npn_label">{#shortcode_editor.add_new}</label>
          <input type="text" id="gse_new_prop_name" class="small" />:
          <input type="text" id="gse_new_prop_value" class="small" />
          <input type="button" id="gse_new_prop_add" value="{#shortcode_editor.add}" class="autow" />
        </div>
      </div>
      <div class="p">
        <label for="gse_content">{#shortcode_editor.content}</label>
        <textarea id="gse_content" name="gse_content"></textarea>
      </div>
      <div class="buttons">
        <input type="button" id="shortcode_close" value="{#shortcode_editor.cancel}" />
        <input type="submit" id="shortcode_submit" value="{#shortcode_editor.insert}" disabled="disabled" />
        <input type="hidden" id="sc_id" />
      </div>
    </form>
  </body>
</html>