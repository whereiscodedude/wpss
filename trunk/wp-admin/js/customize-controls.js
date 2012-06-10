(function(a,c){var b=wp.customize;b.Setting=b.Value.extend({initialize:function(g,f,d){var e;b.Value.prototype.initialize.call(this,f,d);this.id=g;this.transport=this.transport||"refresh";this.bind(this.preview)},preview:function(){switch(this.transport){case"refresh":return this.previewer.refresh();case"postMessage":return this.previewer.send("setting",[this.id,this()])}}});b.Control=b.Class.extend({initialize:function(i,e){var g=this,d,h,f;this.params={};c.extend(this,e||{});this.id=i;this.selector="#customize-control-"+i.replace("]","").replace("[","-");this.container=c(this.selector);f=c.map(this.params.settings,function(j){return j});b.apply(b,f.concat(function(){var j;g.settings={};for(j in g.params.settings){g.settings[j]=b(g.params.settings[j])}g.setting=g.settings["default"]||null;g.ready()}));g.elements=[];d=this.container.find("[data-customize-setting-link]");h={};d.each(function(){var k=c(this),j;if(k.is(":radio")){j=k.prop("name");if(h[j]){return}h[j]=true;k=d.filter('[name="'+j+'"]')}b(k.data("customizeSettingLink"),function(m){var l=new b.Element(k);g.elements.push(l);l.sync(m);l.set(m())})})},ready:function(){},dropdownInit:function(){var e=this,d=this.container.find(".dropdown-status"),f=this.params,g=function(h){if(typeof h==="string"&&f.statuses&&f.statuses[h]){d.html(f.statuses[h]).show()}else{d.hide()}};this.container.on("click",".dropdown",function(h){h.preventDefault();e.container.toggleClass("open")});this.setting.bind(g);g(this.setting())}});b.ColorControl=b.Control.extend({ready:function(){var g=this,f,e,d,h,i;f=/^#([A-Fa-f0-9]{3}){0,2}$/;e=this.container.find(".dropdown-content");d=new b.Element(this.container.find(".color-picker-hex"));i=function(j){e.css("background",j);g.farbtastic.setColor(j)};this.farbtastic=c.farbtastic(this.container.find(".farbtastic-placeholder"),g.setting.set);d.sync(this.setting).validate=function(j){return f.test(j)?j:null};this.setting.bind(i);i(this.setting());this.dropdownInit()}});b.UploadControl=b.Control.extend({ready:function(){var d=this;this.params.removed=this.params.removed||"";this.success=c.proxy(this.success,this);this.uploader=c.extend({container:this.container,browser:this.container.find(".upload"),dropzone:this.container.find(".upload-dropzone"),success:this.success},this.uploader||{});if(this.uploader.supported){if(d.params.context){d.uploader.param("post_data[context]",this.params.context)}d.uploader.param("post_data[theme]",b.settings.theme.stylesheet)}this.uploader=new wp.Uploader(this.uploader);this.remover=this.container.find(".remove");this.remover.click(function(e){d.setting.set(d.params.removed);e.preventDefault()});this.removerVisibility=c.proxy(this.removerVisibility,this);this.setting.bind(this.removerVisibility);this.removerVisibility(this.setting.get())},success:function(d){this.setting.set(d.url)},removerVisibility:function(d){this.remover.toggle(d!=this.params.removed)}});b.ImageControl=b.UploadControl.extend({ready:function(){var e=this,d;this.uploader={init:function(f){var h,g;if(this.supports.dragdrop){return}h=e.container.find(".upload-fallback");g=h.children().detach();this.browser.detach().empty().append(g);h.append(this.browser).show()}};b.UploadControl.prototype.ready.call(this);this.thumbnail=this.container.find(".preview-thumbnail img");this.thumbnailSrc=c.proxy(this.thumbnailSrc,this);this.setting.bind(this.thumbnailSrc);this.library=this.container.find(".library");this.tabs={};d=this.library.find(".library-content");this.library.children("ul").children("li").each(function(){var g=c(this),h=g.data("customizeTab"),f=d.filter('[data-customize-tab="'+h+'"]');e.tabs[h]={both:g.add(f),link:g,panel:f}});this.selected=this.tabs[d.first().data("customizeTab")];this.selected.both.addClass("library-selected");this.library.children("ul").on("click","li",function(g){var h=c(this).data("customizeTab"),f=e.tabs[h];g.preventDefault();if(f.link.hasClass("library-selected")){return}e.selected.both.removeClass("library-selected");e.selected=f;e.selected.both.addClass("library-selected")});this.library.on("click","a",function(f){var g=c(this).data("customizeImageValue");if(g){e.setting.set(g);f.preventDefault()}});if(this.tabs.uploaded){this.tabs.uploaded.target=this.library.find(".uploaded-target");if(!this.tabs.uploaded.panel.find(".thumbnail").length){this.tabs.uploaded.both.addClass("hidden")}}this.dropdownInit()},success:function(d){b.UploadControl.prototype.success.call(this,d);if(this.tabs.uploaded&&this.tabs.uploaded.target.length){this.tabs.uploaded.both.removeClass("hidden");d.element=c('<a href="#" class="thumbnail"></a>').data("customizeImageValue",d.url).append('<img src="'+d.url+'" />').appendTo(this.tabs.uploaded.target)}},thumbnailSrc:function(d){if(/^(https?:)?\/\//.test(d)){this.thumbnail.prop("src",d).show()}else{this.thumbnail.hide()}}});b.defaultConstructor=b.Setting;b.control=new b.Values({defaultConstructor:b.Control});b.PreviewFrame=b.Messenger.extend({sensitivity:2000,initialize:function(i,g){var f=false,h=false,e=c.Deferred(),d=this;e.promise(this);this.previewer=i.previewer;c.extend(i,{channel:b.PreviewFrame.uuid()});b.Messenger.prototype.initialize.call(this,i,g);this.add("previewUrl",i.previewUrl);this.bind("ready",function(){h=true;if(f){e.resolveWith(d)}});i.query=c.extend(i.query||{},{customize_messenger_channel:this.channel()});this.request=c.ajax(this.previewUrl(),{type:"POST",data:i.query,xhrFields:{withCredentials:true}});this.request.fail(function(){e.rejectWith(d,["request failure"])});this.request.done(function(l){var k=d.request.getResponseHeader("Location"),j="WP_CUSTOMIZER_SIGNATURE",m;if(k&&k!=d.previewUrl()){e.rejectWith(d,["redirect",k]);return}if("0"===l){e.rejectWith(d,["logged out"]);return}if("-1"===l){e.rejectWith(d,["cheatin"]);return}m=l.lastIndexOf(j);if(-1===m||m<l.lastIndexOf("</html>")){e.rejectWith(d,["unsigned"]);return}l=l.slice(0,m)+l.slice(m+j.length);l=l.slice(0,m)+l.slice(m+j.length);d.iframe=c("<iframe />").appendTo(d.previewer.container);d.iframe.one("load",function(){f=true;if(h){e.resolveWith(d)}else{setTimeout(function(){e.rejectWith(d,["ready timeout"])},d.sensitivity)}});d.targetWindow(d.iframe[0].contentWindow);d.targetWindow().document.open();d.targetWindow().document.write(l);d.targetWindow().document.close()})},destroy:function(){b.Messenger.prototype.destroy.call(this);this.request.abort();if(this.iframe){this.iframe.remove()}delete this.request;delete this.iframe;delete this.targetWindow}});(function(){var d=0;b.PreviewFrame.uuid=function(){return"preview-"+d++}}());b.Previewer=b.Messenger.extend({refreshBuffer:250,initialize:function(h,f){var d=this,g=/^https?/,e;c.extend(this,f||{});this.refresh=(function(i){var j=i.refresh,l=function(){k=null;j.call(i)},k;return function(){if(typeof k!=="number"){if(i.loading){i.abort()}else{return l()}}clearTimeout(k);k=setTimeout(l,i.refreshBuffer)}})(this);this.container=b.ensure(h.container);this.allowedUrls=h.allowedUrls;h.url=window.location.href;b.Messenger.prototype.initialize.call(this,h);this.add("scheme",this.origin()).link(this.origin).setter(function(j){var i=j.match(g);return i?i[0]:""});this.add("previewUrl",h.previewUrl).setter(function(j){var i;if(/\/wp-admin(\/|$)/.test(j.replace(/[#?].*$/,""))){return null}c.each([j.replace(g,d.scheme()),j],function(l,k){c.each(d.allowedUrls,function(m,n){if(0===k.indexOf(n)){i=k;return false}});if(i){return false}});return i?i:null});this.previewUrl.bind(this.refresh);this.scroll=0;this.bind("scroll",function(i){this.scroll=i});this.bind("url",this.previewUrl)},query:function(){},abort:function(){if(this.loading){this.loading.destroy();delete this.loading}},refresh:function(){var d=this;this.abort();this.loading=new b.PreviewFrame({url:this.url(),previewUrl:this.previewUrl(),query:this.query()||{},previewer:this});this.loading.done(function(){this.bind("synced",function(){if(d.preview){d.preview.destroy()}d.preview=this;delete d.loading;d.targetWindow(this.targetWindow());d.channel(this.channel())});this.send("sync",{scroll:d.scroll,settings:b.get()})});this.loading.fail(function(f,e){if("redirect"===f&&e){d.previewUrl(e)}if("logged out"===f){if(d.preview){d.preview.destroy();delete d.preview}d.login().done(d.refresh)}if("cheatin"===f){d.cheatin()}})},login:function(){var g=this,d,f,e;if(this._login){return this._login}d=c.Deferred();this._login=d.promise();f=new b.Messenger({channel:"login",url:b.settings.url.login});e=c('<iframe src="'+b.settings.url.login+'" />').appendTo(this.container);f.targetWindow(e[0].contentWindow);f.bind("login",function(){e.remove();f.destroy();delete g._login;d.resolve()});return this._login},cheatin:function(){c(document.body).empty().addClass("cheatin").append("<p>"+b.l10n.cheatin+"</p>")}});b.controlConstructor={color:b.ColorControl,upload:b.UploadControl,image:b.ImageControl};c(function(){b.settings=window._wpCustomizeSettings;b.l10n=window._wpCustomizeControlsL10n;if(!b.settings){return}if(!c.support.postMessage||(!c.support.cors&&b.settings.isCrossDomain)){return window.location=b.settings.url.fallback}var d=c(document.body),e=d.children(".wp-full-overlay"),g,h,f;c("#customize-controls").on("keydown",function(i){if(c(i.target).is("textarea")){return}if(13===i.which){i.preventDefault()}});h=new b.Previewer({container:"#customize-preview",form:"#customize-controls",previewUrl:b.settings.url.preview,allowedUrls:b.settings.url.allowed},{query:function(){return{wp_customize:"on",theme:b.settings.theme.stylesheet,customized:JSON.stringify(b.get())}},nonce:c("#_wpnonce").val(),save:function(){var i=this,k=c.extend(this.query(),{action:"customize_save",nonce:this.nonce}),j=c.post(b.settings.url.ajax,k);b.trigger("save",j);d.addClass("saving");j.always(function(){d.removeClass("saving")});j.done(function(l){if("0"===l){i.preview.iframe.hide();i.login().done(function(){i.save();i.preview.iframe.show()});return}if("-1"===l){i.cheatin();return}b.trigger("saved")})}});c.each(b.settings.settings,function(j,i){b.create(j,j,i.value,{transport:i.transport,previewer:h})});c.each(b.settings.controls,function(l,j){var i=b.controlConstructor[j.type]||b.Control,k;k=b.control.add(l,new i(l,{params:j,previewer:h}))});if(h.previewUrl()){h.refresh()}else{h.previewUrl(b.settings.url.home)}(function(){var k=new b.Values(),j=k.create("saved"),i=k.create("activated");k.bind("change",function(){var m=c("#save"),l=c(".back");if(!i()){m.val(b.l10n.activate).prop("disabled",false);l.text(b.l10n.cancel)}else{if(j()){m.val(b.l10n.saved).prop("disabled",true);l.text(b.l10n.close)}else{m.val(b.l10n.save).prop("disabled",false);l.text(b.l10n.cancel)}}});j(true);i(b.settings.theme.active);b.bind("change",function(){k("saved").set(false)});b.bind("saved",function(){k("saved").set(true);k("activated").set(true)});i.bind(function(l){if(l){b.trigger("activated")}});b.state=k}());c(".customize-section-title").click(function(j){var i=c(this).parents(".customize-section");if(i.hasClass("cannot-expand")){return}c(".customize-section").not(i).removeClass("open");i.toggleClass("open");j.preventDefault()});c("#save").click(function(i){h.save();i.preventDefault()});c(".collapse-sidebar").click(function(i){e.toggleClass("collapsed").toggleClass("expanded");i.preventDefault()});f=new b.Messenger({url:b.settings.url.parent,channel:"loader"});f.bind("back",function(){c(".back").on("click.back",function(i){i.preventDefault();f.send("close")})});b.bind("saved",function(){f.send("saved")});b.bind("activated",function(){if(f.targetWindow()){f.send("activated",b.settings.url.activated)}else{if(b.settings.url.activated){window.location=b.settings.url.activated}}});f.send("ready");c.each({background_image:{controls:["background_repeat","background_position_x","background_attachment"],callback:function(i){return !!i}},show_on_front:{controls:["page_on_front","page_for_posts"],callback:function(i){return"page"===i}},header_textcolor:{controls:["header_textcolor"],callback:function(i){return"blank"!==i}}},function(i,j){b(i,function(k){c.each(j.controls,function(l,m){b.control(m,function(o){var n=function(p){o.container.toggle(j.callback(p))};n(k.get());k.bind(n)})})})});b.control("display_header_text",function(j){var i="";j.elements[0].unsync(b("header_textcolor"));j.element=new b.Element(j.container.find("input"));j.element.set("blank"!==j.setting());j.element.bind(function(k){if(!k){i=b("header_textcolor").get()}j.setting.set(k?i:"blank")});j.setting.bind(function(k){j.element.set("blank"!==k)})});b.control("header_image",function(i){i.setting.bind(function(j){if(j===i.params.removed){i.settings.data.set(false)}});i.library.on("click","a",function(j){i.settings.data.set(c(this).data("customizeHeaderImageData"))});i.uploader.success=function(k){var j;b.ImageControl.prototype.success.call(i,k);j={attachment_id:k.id,url:k.url,thumbnail_url:k.url,height:k.meta.height,width:k.meta.width};k.element.data("customizeHeaderImageData",j);i.settings.data.set(j)}});b.trigger("ready")})})(wp,jQuery);