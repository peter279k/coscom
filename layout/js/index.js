$(function() {
	var result = "";
	
	$("#find-btn").click(function() {
		$("#result").html("<h2>Loading...</h2>");
		if($("#input-link").val() !== "") {
			$.post("../index.php", {"input-link": $("#input-link").val()}, function(response) {
				$("#result").html("");
				response = $.parseJSON(response);
				result = response["result"];
				if(result.indexOf("please") !== -1) {
					alert("invalid link");
				}
				else {
					var str = "";
					if(response["file_arr"]) {
						result = response["file_arr"];
						for(var count=0;count<result.length;count++) {
							str += "<p><a class='link-text' href='"+result[count]["file_path"]+"' download>"+'audio file'+(count+1)+"</a></p>";
						}
					
						$("#result").append(str);
					}
					else {
						$("#result").append("<b>cannot find audio file</b>");
					}
				}
			});
		}
	});
});