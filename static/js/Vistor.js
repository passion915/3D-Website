/*
*访问
*/
function Vistor(){
	
}

function getBrowserInfo()
{
	var agent = navigator.userAgent.toLowerCase() ;

	var regStr_ie = /msie [\d.]+;/gi ;
	var regStr_ff = /firefox+/gi
	var regStr_chrome = /chrome+/gi ;
	var regStr_saf = /safari+/gi ;
	//IE
	if(agent.indexOf("msie") > 0)
	{
		return agent.match(regStr_ie) ;
	}

	//firefox
	if(agent.indexOf("firefox") > 0)
	{
		return agent.match(regStr_ff) ;
	}

	//Chrome
	if(agent.indexOf("chrome") > 0)
	{
		return agent.match(regStr_chrome) ;
	}

	//Safari
	if(agent.indexOf("safari") > 0 && agent.indexOf("chrome") < 0)
	{
		return agent.match(regStr_saf) ;
	}

}
/*
*独立访客（UV）：1天内相同访客多次访问网站，只计算为1个独立访客。
*@param url 服务器地址 必填
*@param params 参数列表  选填
*@param callback 回调函数  选填
*/
Vistor.Vistor_UV=function(url,params,callback){
	if(!params)
		params={};
	params['act']="UV";
	AjaxSubmit_Vistor(url,params,callback);
}
/*
*网站浏览量（PV）：用户每打开一个页面便记录1次PV
*@param url 服务器地址 必填
*@param params 参数列表  选填
*@param callback 回调函数  选填
*/
Vistor.Vistor_PV = function(url,params,callback){
	if(!params)
		params={};
	params['act']="PV";
	AjaxSubmit_Vistor(url,params,callback);
};

/*
*独立IP（IP）：同一IP无论访问了几个页面，独立IP数均为1
*@param url 服务器地址 必填
*@param params 参数列表  选填
*@param callback 回调函数  选填
*/
Vistor.Vistor_IP = function(url,params,callback){
	// AjaxSubmit_Vistor(url,params,callback);
};

/*
*统计全景作品
*@param url 服务器地址 必填
*@param params 参数列表  选填
*@param callback 回调函数  选填
*/
Vistor.Vistor_Panos = function(url,params,callback){
	if(!params)
		params={};
	params['act']="panos";
	params['browser']= getBrowserInfo();
	AjaxSubmit_Vistor(url,params,callback);
};

/*
*Ajax提交
*@param url 服务器地址 必填
*@param params 参数列表  选填
*@param callback 回调函数  选填
*/
function AjaxSubmit_Vistor(url,params,callback){
	$.post(url,params,function(res){
		console.log(res);
		if(callback) callback(res);
	},"JSON");
}