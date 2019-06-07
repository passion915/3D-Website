$(function(){
	plugins_init_function.push(top_ad_init);
    plugins_config_get_function.push(build_top_ad);
})
function top_ad_init(){
	data = panoConfig.top_ad;
	if(data&&data.show!="0"){
	    if (data.top_ad_type == "0") {
	    	$('#top_ad_type_wrap input[name="top_ad_type"] :first').attr('checked','true');
	    }else{
	    	$('#top_ad_type_wrap input[name="top_ad_type"] :last').attr('checked','true');
	    }
	    $('#topAdModal input[name="adcontent"]').val(data.adcontent);
	}else{
		$('#top_ad_type_wrap input[name="top_ad_type"] :first').attr('checked','true');
	}
}
function openTopAdModal(){
    $("#topAdModal").modal('show');
}

function build_top_ad(panoConfig){
	var topAdObj = {};
	var adcontent = $.trim($('#topAdModal input[name="adcontent"]').val());
	if(adcontent.length < 1){
		topAdObj.show = 0;
	}else{
		if (adcontent.length>255) 
			adcontent = adcontent.substring(0,255);
		topAdObj.show = 1;
		topAdObj.top_ad_type = $('#top_ad_type_wrap input[name="top_ad_type"]:checked').val();
		topAdObj.adcontent = adcontent;
	}
	 panoConfig.top_ad = topAdObj;
	
}