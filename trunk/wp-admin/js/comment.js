jQuery(document).ready(function(b){var a=b("#timestamp").html();b(".edit-timestamp").click(function(){if(b("#timestampdiv").is(":hidden")){b("#timestampdiv").slideDown("normal");b(".edit-timestamp").hide()}return false});b(".cancel-timestamp").click(function(){b("#timestampdiv").slideUp("normal");b("#mm").val(b("#hidden_mm").val());b("#jj").val(b("#hidden_jj").val());b("#aa").val(b("#hidden_aa").val());b("#hh").val(b("#hidden_hh").val());b("#mn").val(b("#hidden_mn").val());b("#timestamp").html(a);b(".edit-timestamp").show();return false});b(".save-timestamp").click(function(){b("#timestampdiv").slideUp("normal");b(".edit-timestamp").show();b("#timestamp").html(commentL10n.submittedOn+" <b>"+b("#mm option[value="+b("#mm").val()+"]").text()+" "+b("#jj").val()+", "+b("#aa").val()+" @ "+b("#hh").val()+":"+b("#mn").val()+"</b> ");return false})});