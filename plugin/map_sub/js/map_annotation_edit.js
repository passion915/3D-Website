var map_annotation_data;
var map_annotation_param ={};

function openMap_annotation(){
    // var tag = $("#pic_chosen").val();
    // if (tag == null) {
    //     alert_notice("请先选择您的项目标签");
    //     return false;
    // }
    // tag = tag[0];
    $("#map_annotation_target").html("");
    $.ajax({
         type: 'POST',
         url: '/plugin/map_annotation/map_annotation.php' ,
        data: {'act':'get_map_by_tag'} ,
        async: false,
        dataType: 'json',
        success : function(data){
                map_annotation_data = data;
        }
    });
    if(!map_annotation_data){
        alert_notice("未查询到地图");
        return false;
    }
    // $.ajax({
    //      type: 'POST',
    //      url: '/plugin/map_annotation/map_annotation.php' ,
    //     data: {'act':'get_pano_config'} ,
    //      async: false,
    //     dataType: 'json',
    //     success : function(data){
    //      map_annotation_data = data;
    //     }
    // });
    var settings ={};
    settings["events[skin_events].onnewscene"] = "js(map_a_scene_init(get(xml.scene)))";
    embedpano({
        id: "map_annotation_object",
        swf: "/tour/tour.swf",
        xml: "/tour/tour.xml.php?view="+map_annotation_data.view_uuid+"&flag=map",
        target: "map_annotation_target",
        html5:'auto',
        wmode:'opaque-flash',
        mobilescale:0.7,
        vars:settings,
        webglsettings:{preserveDrawingBuffer:true}
    });
    $("#map_annotation_modal").modal('show');
}
function map_a_scene_init(sceneName){
	var krpano = document.getElementById('map_annotation_object');
    var map_annotation_hotspots = eval("("+map_annotation_data.hotspot+")");
    var hotspotObj = map_annotation_hotspots[sceneName];
    if(hotspotObj){
        $.each(hotspotObj,function(key,value){
            if(key == 'scene'){
                $(value).each(function(idx){
                    krpano.call('addSceneChangeHotSpot("'+this.imgPath+'","'+ (this.name) +'",'+this.linkedscene+','+(this.ath)+','+(this.atv)+','+this.isDynamic+',false,true)');
                });
            }else if(key == 'link'){
                $(value).each(function(idx){
                    krpano.call('addLinkHotSpot("'+this.imgPath+'","'+ (this.name) +'",'+this.hotspotTitle+','+(this.ath)+','+(this.atv)+','+this.isDynamic+',false,true,'+this.link+','+this.isShowSpotName+')');
                });
            }else if(key == 'image'){
                $(value).each(function(idx){
                    krpano.call('addImgHotSpot("'+this.imgPath+'","'+ (this.name) +'",'+this.hotspotTitle+','+(this.ath)+','+(this.atv)+','+this.isDynamic+',false,true,'+this.galleryName+','+this.isShowSpotName+')');
                });
            }else if(key == 'text'){
                $(value).each(function(idx){
                    krpano.call('addWordHotSpot("'+this.imgPath+'","'+ (this.name) +'",'+this.hotspotTitle+','+(this.ath)+','+(this.atv)+','+this.isDynamic+',false,true,'+this.wordContent+','+this.isShowSpotName+')');
                });
            }else if(key == 'voice'){
                $(value).each(function(idx){
                    krpano.call('addVoiceHotSpot("'+this.imgPath+'","'+ (this.name) +'",'+this.hotspotTitle+','+(this.ath)+','+(this.atv)+','+this.isDynamic+',false,true,'+this.musicSrc+','+this.isShowSpotName+')');
                });
            }else if(key == 'around'){
                $(value).each(function(idx){
                    krpano.call('addAroundHotSpot("'+this.imgPath+'","'+ (this.name) +'",'+this.hotspotTitle+','+(this.ath)+','+(this.atv)+','+this.isDynamic+',false,true,'+this.aroundPath+','+this.fileCount+','+this.isShowSpotName+')');
                });
            }else if(key == 'imgtext'){
                $(value).each(function(idx){
                    krpano.call('addImgTextHotSpot("'+this.imgPath+'","'+ (this.name) +'",'+this.hotspotTitle+','+(this.ath)+','+(this.atv)+','+this.isDynamic+',false,true,'+this.wordContent+','+this.isShowSpotName+')');
                });
            }
        });
    }
    if (map_annotation_param.sys_scene == sceneName) {
        map_annotation_addHostspot(krpano);
        var curFov = krpano.get('view.fov');
        krpano.call('looktohotspot('+map_annotation_param.cus_hotspot_name+','+curFov+')');
    }
}
function map_annotation_showthumbs(){
	var krpano = document.getElementById('map_annotation_object');
	krpano.call("skin_showthumbs();");
}

function map_annotation_add(){
	if (map_annotation_param.cus_hotspot_name) {
		alert_notice("您已经添加过标注了");
		return false;
	}
    map_annotation_param ={};
	var krpano = document.getElementById('map_annotation_object');
	map_annotation_param.cus_hotspot_title=$.trim($("#pname").val());
	if (map_annotation_param.cus_hotspot_title.length<=0) {
		alert_notice("请先填写您的项目名称");
		return false;
	}
    
	map_annotation_param.cus_img = '/plugin/map_annotation/images/dyn_fj.png';
	map_annotation_param.ath = krpano.get('view.hlookat');
	map_annotation_param.atv = krpano.get('view.vlookat');
	map_annotation_param.cus_hotspot_name = 'c_ma_'+new Date().getTime()+generic_radom_str(3);
    map_annotation_param.sys_scene = krpano.get('xml.scene');
    map_annotation_param.sys_wid = map_annotation_data.pk_works_main;
    map_annotation_param.sys_view_id = map_annotation_data.view_uuid;
    // map_annotation_param.cus_view_id = works_view_uuid;
    //map_annotation_param.cus_wid = pk_works_main;

   map_annotation_addHostspot(krpano);

}

function map_annotation_cus_ok(){
    $("#map_annotation_modal").modal('hide');
}

function map_annotation_addHostspot(krpano){
    var hotspotTitle = map_annotation_param.cus_hotspot_title;
    if (hotspotTitle.length>10) {
        hotspotTitle = hotspotTitle.substring(0,10)+"...";
    }
     krpano.call(
        "set(schp_name,"+map_annotation_param.cus_hotspot_name+");"+
        "addhotspot(get(schp_name));"+
        "set(hotspot[get(schp_name)].url,"+map_annotation_param.cus_img+");"+
        "set(hotspot[get(schp_name)].ath,"+map_annotation_param.ath+");"+
        "set(hotspot[get(schp_name)].atv,"+map_annotation_param.atv+");"+
        "set(hotspot[get(schp_name)].edge,bottom);"+
        "set(hotspot[get(schp_name)].zoom,false);"+   
        "set(hotspot[get(schp_name)].ondown,dragcommenthotspot());"+
        "set(hotspot[get(schp_name)].onup,js(map_a_update(get(xml.scene),get(ath),get(atv))));"+
        "set(hotspot[get(schp_name)].hotspottitle,"+hotspotTitle+");"+
        "set(hotspot[get(schp_name)].width,'prop'); set(hotspot[get(schp_name)].height,'50');"+
        "set(hotspot[get(schp_name)].visible,true);"+
        "txtadd(hotspot[get(schp_name)].onloaded,'do_crop_animation(128,128, 60);');"+
        "txtadd(hotspot[get(schp_name)].onloaded,'add_all_the_time_tooltip(hotspot[get(schp_name)].hotspottitle);');"
    );
}

function map_a_update(scene,ath,atv){
     map_annotation_param.sys_scene =scene;
    map_annotation_param.ath = ath;
    map_annotation_param.atv = atv;
}
function map_a_fouce2a(){
    if (!map_annotation_param.sys_scene) {
        alert_notice("您还未添加过标注");
        return false;
    }
    var krpano = document.getElementById('map_annotation_object');
    if (krpano.get('xml.scene')==map_annotation_param.sys_scene) {
        var curFov = krpano.get('view.fov');
        krpano.call('looktohotspot('+map_annotation_param.cus_hotspot_name+','+curFov+')');
    }else
        krpano.call('loadscene('+map_annotation_param.sys_scene+'), null, MERGE);');
     
}

function map_a_remove(){
    if(!map_annotation_param.cus_hotspot_name){
        alert_notice("您还未添加过标注");
        return false;
    }
    var krpano = document.getElementById('map_annotation_object');
    krpano.call('removehotspot('+map_annotation_param.cus_hotspot_name+')');
    krpano.call('removeplugin(' + ('tooltip_' + map_annotation_param.cus_hotspot_name) + ',true);');
    map_annotation_param = {};
    alert_notice('移除成功','success');
}