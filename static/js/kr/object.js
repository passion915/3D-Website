//flag 标识是否从预览页打开 true
var buylink="";
function obj_buildframes(oid){
	$.post('/obj.php',{
		'act':'init_obj',
		'oid':oid
	},function(res){
		var krpano = document.getElementById('krpanoSWFObject');
		var imgs = res.imgs;
		var timestamp=new Date().getTime();
		for(var i=0 ; i<imgs.length; i++){
			var fname = 'frame'+i;
			krpano.call('addplugin('+fname+');'+
					 'plugin['+fname+'].loadstyle(frame);'+
					 'set(plugin['+fname+'].url,'+imgs[i].imgsrc+'?'+timestamp+');');
		}
		
		 toggleBtns(false);
		 if (res.buy_link != "") {
			buylink = res.buy_link;
			$("#buyObj").show();
		}
		krpano.call("set(currentframe,0);set(framecount,"+imgs.length+");set(oldmousex,0);showframe(0);");
	},'json')
}
function hide_buy_link(){
	buylink = "";
	$("#buyObj").hide();
}
function tobuy(){
	if (buylink!="") {
		window.open(buylink);
	}
}