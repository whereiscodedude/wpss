function edit_permalink(a){var d,h=0,g=jQuery("#editable-post-name"),j=g.html(),m=jQuery("#post_name"),n=m.html(),k=jQuery("#edit-slug-buttons"),l=k.html(),f=jQuery("#editable-post-name-full").html();k.html('<a href="" class="save button">'+slugL10n.save+'</a> <a class="cancel" href="">'+slugL10n.cancel+"</a>");k.children(".save").click(function(){var b=g.children("input").val();jQuery.post(slugL10n.requestFile,{action:"sample-permalink",post_id:a,new_slug:b,new_title:jQuery("#title").val(),samplepermalinknonce:jQuery("#samplepermalinknonce").val()},function(c){jQuery("#edit-slug-box").html(c);k.html(l);m.attr("value",b);make_slugedit_clickable()});return false});jQuery("#edit-slug-buttons .cancel").click(function(){g.html(j);k.html(l);m.attr("value",n);return false});for(d=0;d<f.length;++d){if("%"==f.charAt(d)){h++}}slug_value=(h>f.length/4)?"":f;g.html('<input type="text" id="new-post-slug" value="'+slug_value+'" />').children("input").keypress(function(c){var b=c.charCode?c.charCode:c.keyCode?c.keyCode:0;if(13==b){k.children(".save").click();return false}if(27==b){k.children(".cancel").click();return false}m.attr("value",this.value)}).focus()}function make_slugedit_clickable(){jQuery("#editable-post-name").click(function(){jQuery("#edit-slug-buttons").children(".edit-slug").click()})};