(function() {
  tinymce.PluginManager.requireLangPack('shortcode_editor');
  
  tinymce.create('tinymce.plugins.shortcode_editor', {
    sc_id: 0,
    sc_info: {},
    init : function(ed, url) {
      gse_transp_url = url+'/img/trans.gif';
      gse_editor = ed;
      _self = this;

      ed.addCommand('editShortcode', function() {
        ed.windowManager.open({
          file : url + '/dialog.php',
          width : 380 + parseInt(ed.getLang('shortcode_editor.delta_width', 0)),
          height : 310 + parseInt(ed.getLang('shortcode_editor.delta_height', 0)),
          inline : 1
        }, {
          plugin_url : url
        });
      });

      ed.addButton('shortcode_editor', {
        title :  gse_editor.getLang('shortcode_editor.button_title'),
        cmd : 'editShortcode',
        image : url + '/img/shortcode.gif'
      });
      
      ed.onInit.add(function() { ed.dom.loadCSS(url + '/css/shortcode_editor.css'); });

      ed.onNodeChange.add(function(ed, cm, n) { cm.setActive('shortcode_editor', n.nodeName === 'IMG' && ed.dom.hasClass(n, 'shortcode-placeholder')); });

      ed.onBeforeSetContent.add(function(ed, o) { o.content = _self.toHTML(o.content); });

      ed.onPostProcess.add(function(ed, o) { if(o.get) o.content = _self.toText(o.content); });
    },
    
    cache : function(key, val) {
      if(key && !val) return gse_editor.plugins.shortcode_editor.sc_info[key] || null;
      if(key && val) {
        gse_editor.plugins.shortcode_editor.sc_info[key] = val;
        return true;
      } 
      return false;
    },
    
    toText : function(str) {
      return str.replace(/<img [^>]*\bclass="[^"]*shortcode-placeholder\b[^"]* scid-([^\s"]+)[^>]+>/g, function(a, id) { return gse_editor.plugins.shortcode_editor.cache(id); });
    },
    
    toHTML : function(str) {
      return str.replace(gse_shortcode_er, 
                         function(str, tag, properties, rawconts, conts) { 
                           var props = gse_editor.plugins.shortcode_editor.parseProperties(properties);
													 if(props.sc_id === undefined) {
														 props.sc_id = gse_editor.plugins.shortcode_editor.getId();
														 properties += ' sc_id="'+props.sc_id+'"';
													 }
													 gse_editor.plugins.shortcode_editor.cache(props.sc_id, '['+tag+' '+properties+(conts ? ']'+conts+'[/'+tag+']' : ']'));
													 var _properties = properties.replace(/ sc_id="[^"]+"/, '').replace(/="([^"]+)"/g,': $1;');
                           return '<img src="'+gse_transp_url+'" class="shortcode-placeholder mceItem scid-'+props.sc_id+'" title="Shortcode: '+tag+' '+_properties+'" />'; 
                         });
    },
    
    parseProperties : function(str) {
      var parts = str.split(/\"/), props = {};
      for(var i = 0; i < parts.length; i += 2) {
				if(typeof parts[i] != 'string' || typeof parts[i+1] != 'string') continue;
        var n = parts[i].replace(/^\s+|\s+$/g,'').replace('=',''), v = parts[i+1];
        if(n && v) props[n] = v;
      }
			return props;
    },
    
    getId : function() {
			gse_editor.plugins.shortcode_editor.sc_id++;
			return 'sc'+String(gse_editor.plugins.shortcode_editor.sc_id);
    },
    
    getInfo : function() {
      return {
        longname:  'TinyMCE Generic WP Shortcode Editor',
        author:    'Cau Guanabara',
        authorurl: 'http://caugb.com.br',
        infourl:   '',
        version:   '1.0'
      };
    }
  });

  tinymce.PluginManager.add('shortcode_editor', tinymce.plugins.shortcode_editor);
})();