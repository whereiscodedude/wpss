!function(){var a={};tinymce.create("tinymce.plugins.wpEditImage",{url:"",editor:{},init:function(b,c){var d=this;d.url=c,d.editor=b,d._createButtons(),b.addCommand("WP_EditImage",d._editImage),b.onInit.add(function(a){a.dom.events.add(a.getBody(),"mousedown",function(b){var c;"IMG"==b.target.nodeName&&(c=a.dom.getParent(b.target,"div.mceTemp"))&&(tinymce.isGecko?a.selection.select(c):tinymce.isWebKit&&a.dom.events.prevent(b))}),a.dom.events.add(a.getBody(),"keydown",function(b){var c,d,e,f;return 13==b.keyCode&&(c=a.selection.getNode(),d=a.dom.getParent(c,"dl.wp-caption"),d&&(e=a.dom.getParent(d,"div.mceTemp")),e)?(a.dom.events.cancel(b),f=a.dom.create("p",{},"\ufeff"),a.dom.insertAfter(f,e),a.selection.setCursorLocation(f,0),!1):void 0}),"ontouchstart"in window&&a.dom.events.add(a.getBody(),"touchstart",function(a){d._showButtons(a)})}),b.onMouseUp.add(function(b,c){if(!tinymce.isWebKit&&!tinymce.isOpera){if(a.x&&(c.clientX!=a.x||c.clientY!=a.y)){var d=b.selection.getNode();"IMG"==d.nodeName&&window.setTimeout(function(){var c,e=b.dom.getParent(d,"dl.wp-caption");(d.width!=a.img_w||d.height!=a.img_h)&&(d.className=d.className.replace(/size-[^ "']+/,"")),e&&(c=b.dom.getAttrib(d,"width")||d.width,c=parseInt(c,10),b.dom.setStyle(e,"width",10+c),b.execCommand("mceRepaint"))},100)}a={}}}),b.onMouseDown.add(function(a,b){d._showButtons(b)}),b.onBeforeSetContent.add(function(a,b){b.content=a.wpSetImgCaption(b.content)}),b.onPostProcess.add(function(a,b){b.get&&(b.content=a.wpGetImgCaption(b.content))}),b.wpSetImgCaption=function(a){return d._do_shcode(a)},b.wpGetImgCaption=function(a){return d._get_shcode(a)},b.onBeforeExecCommand.add(function(a,b){var c,d;if("mceInsertContent"==b){if(c=a.dom.getParent(a.selection.getNode(),"div.mceTemp"),!c)return;d=a.dom.create("p"),a.dom.insertAfter(d,c),a.selection.setCursorLocation(d,0)}})},_do_shcode:function(a){return a.replace(/(?:<p>)?\[(?:wp_)?caption([^\]]+)\]([\s\S]+?)\[\/(?:wp_)?caption\](?:<\/p>)?/g,function(a,b,c){var d,e,f,g,h,i,j=tinymce.trim;return d=b.match(/id=['"]([^'"]*)['"] ?/),d&&(b=b.replace(d[0],"")),e=b.match(/align=['"]([^'"]*)['"] ?/),e&&(b=b.replace(e[0],"")),f=b.match(/width=['"]([0-9]*)['"] ?/),f&&(b=b.replace(f[0],"")),c=j(c),i=c.match(/((?:<a [^>]+>)?<img [^>]+>(?:<\/a>)?)([\s\S]*)/i),i&&i[2]?(g=j(i[2]),i=j(i[1])):(g=j(b).replace(/caption=['"]/,"").replace(/['"]$/,""),i=c),d=d&&d[1]?d[1]:"",e=e&&e[1]?e[1]:"alignnone",f=f&&f[1]?f[1]:"",f&&g?(h="mceTemp","aligncenter"==e&&(h+=" mceIEcenter"),f=parseInt(f,10)+10,'<div class="'+h+'"><dl id="'+d+'" class="wp-caption '+e+'" style="width: '+f+'px"><dt class="wp-caption-dt">'+i+'</dt><dd class="wp-caption-dd">'+g+"</dd></dl></div>"):c})},_get_shcode:function(a){return a.replace(/<div (?:id="attachment_|class="mceTemp)[^>]*>([\s\S]+?)<\/div>/g,function(a,b){var c=b.replace(/<dl ([^>]+)>\s*<dt [^>]+>([\s\S]+?)<\/dt>\s*<dd [^>]+>([\s\S]*?)<\/dd>\s*<\/dl>/gi,function(a,b,c,d){var e,f,g;return g=c.match(/width="([0-9]*)"/),g=g&&g[1]?g[1]:"",g&&d?(e=b.match(/id="([^"]*)"/),e=e&&e[1]?e[1]:"",f=b.match(/class="([^"]*)"/),f=f&&f[1]?f[1]:"",f=f.match(/align[a-z]+/)||"alignnone",d=d.replace(/\r\n|\r/g,"\n").replace(/<[a-zA-Z0-9]+( [^<>]+)?>/g,function(a){return a.replace(/[\r\n\t]+/," ")}),d=d.replace(/\s*\n\s*/g,"<br />"),'[caption id="'+e+'" align="'+f+'" width="'+g+'"]'+c+" "+d+"[/caption]"):c});return 0!==c.indexOf("[caption")&&(c=b.replace(/[\s\S]*?((?:<a [^>]+>)?<img [^>]+>(?:<\/a>)?)(<p>[\s\S]*<\/p>)?[\s\S]*/gi,"<p>$1</p>$2")),c})},_createButtons:function(){var a,b,c,d=this,e=tinymce.activeEditor,f=tinymce.DOM;f.get("wp_editbtns")||(c=window.devicePixelRatio&&window.devicePixelRatio>1||window.matchMedia&&window.matchMedia("(min-resolution:130dpi)").matches,f.add(document.body,"div",{id:"wp_editbtns",style:"display:none;"}),a=f.add("wp_editbtns","img",{src:c?d.url+"/img/image-2x.png":d.url+"/img/image.png",id:"wp_editimgbtn",width:"24",height:"24",title:e.getLang("wpeditimage.edit_img")}),tinymce.dom.Event.add(a,"mousedown",function(){d._editImage(),e.plugins.wordpress._hideButtons()}),b=f.add("wp_editbtns","img",{src:c?d.url+"/img/delete-2x.png":d.url+"/img/delete.png",id:"wp_delimgbtn",width:"24",height:"24",title:e.getLang("wpeditimage.del_img")}),tinymce.dom.Event.add(b,"mousedown",function(){var a,b=tinymce.activeEditor,c=b.selection.getNode();return"IMG"==c.nodeName&&-1==b.dom.getAttrib(c,"class").indexOf("mceItem")?((a=b.dom.getParent(c,"div"))&&b.dom.hasClass(a,"mceTemp")?b.dom.remove(a):("A"==c.parentNode.nodeName&&1==c.parentNode.childNodes.length&&(c=c.parentNode),"P"==c.parentNode.nodeName&&1==c.parentNode.childNodes.length&&(c=c.parentNode),b.dom.remove(c)),b.execCommand("mceRepaint"),!1):void b.plugins.wordpress._hideButtons()}))},_editImage:function(){var a,b,c,d=tinymce.activeEditor,e=this.url,f=d.selection.getNode(),g=f.className;-1==g.indexOf("mceItem")&&-1==g.indexOf("wpGallery")&&"IMG"==f.nodeName&&(a=tinymce.DOM.getViewPort(),b=680<a.h-70?680:a.h-70,c=650<a.w?650:a.w,d.windowManager.open({file:e+"/editimage.html",width:c+"px",height:b+"px",inline:!0}))},_showButtons:function(b){var c=this.editor,d=b.target;if("IMG"!=d.nodeName){if(!d.firstChild||"IMG"!=d.firstChild.nodeName||1!=d.childNodes.length)return void c.plugins.wordpress._hideButtons();d=d.firstChild}-1==c.dom.getAttrib(d,"class").indexOf("mceItem")&&(a={x:b.clientX,y:b.clientY,img_w:d.clientWidth,img_h:d.clientHeight},"touchstart"==b.type&&(c.selection.select(d),c.dom.events.cancel(b)),c.plugins.wordpress._hideButtons(),c.plugins.wordpress._showButtons(d,"wp_editbtns"))},getInfo:function(){return{longname:"Edit Image",author:"WordPress",authorurl:"http://wordpress.org",infourl:"",version:"1.0"}}}),tinymce.PluginManager.add("wpeditimage",tinymce.plugins.wpEditImage)}();