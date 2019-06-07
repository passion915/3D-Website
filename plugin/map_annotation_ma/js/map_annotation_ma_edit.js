var map_ma_data;
var map_ma_param =new Object();
var map_ma_temp;
function openMap_annotation_ma(){
	$("#map_ma_target").html("");

    var sb = _U.getSubmit("/plugin/map_annotation_ma/map_annotation_ma.php", null, "ajax", true);
    sb.pushData("act","get_pano_config");
    sb.pushData("wid",pk_works_main);
    sb.submit(function () {

    }, function (data) {
        map_ma_data = data;
    });
	var settings ={};
	settings["events[skin_events].onnewscene"] = "js(map_ma_scene_init(get(xml.scene)));js(mm_list_annotations(get(xml.scene)));";
	embedpano({
        id: "map_ma_obj",
        swf: "/tour/tour.swf",
        xml: "/tour/tour.xml.php?view="+map_ma_data.view_uuid+"&flag=map",
        target: "map_ma_target",
        html5:'auto',
        wmode:'opaque-flash',
        mobilescale:0.7,
        vars:settings,
        webglsettings:{preserveDrawingBuffer:true}
    });
	$("#map_ma_modal").modal('show');
}
function map_ma_scene_init(sceneName){
	var krpano = document.getElementById('map_ma_obj');
    var map_annotation_hotspots = eval("("+map_ma_data.hotspot+")");
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
function mm_list_annotations(scene){
        $.post('/plugin/map_annotation/map_annotation.php',{'scene':scene,'sys_wid':pk_works_main ,'act':'list'},function(list){
            var krpano =  document.getElementById('map_ma_obj');
            for(var i =0;i<list.length;i++){
                var p = list[i];
                var url = "/tour/"+p.cus_view_id;
                var schp_name  = p.cus_hotspot_name;
                krpano.call(
                    "addhotspot("+schp_name+");"+
                    "set(hotspot["+schp_name+"].url,"+p.cus_img+");"+
                    "set(hotspot["+schp_name+"].ath,"+p.ath+");"+
                    "set(hotspot["+schp_name+"].atv,"+p.atv+");"+
                    "set(hotspot["+schp_name+"].edge,bottom);"+
                    "set(hotspot["+schp_name+"].zoom,false);"+
                    "set(hotspot["+schp_name+"].onclick,js(map_ma_show_a("+p.id+",'"+p.cus_hotspot_title+"','"+p.cus_hotspot_name+"')););"+
                    "set(hotspot["+schp_name+"].ondown,dragcommenthotspot());"+
                    "set(hotspot["+schp_name+"].onup,js(map_ma_update("+p.id+",get(ath),get(atv))));"+
                    "set(hotspot["+schp_name+"].hotspottitle,"+p.cus_hotspot_title+");"+
                    "set(hotspot["+schp_name+"].width,'prop'); set(hotspot["+schp_name+"].height,'50');"+
                    "set(hotspot["+schp_name+"].visible,true);"+
                    "txtadd(hotspot["+schp_name+"].onloaded,'do_crop_animation(128,128, 60);');"+
                    "txtadd(hotspot["+schp_name+"].onloaded,'add_all_the_time_tooltip(hotspot["+schp_name+"].hotspottitle);');"
                );
            }
        },'json');
}
function map_ma_showthumbs(){
	var krpano = document.getElementById('map_ma_obj');
	krpano.call("skin_showthumbs();");
}
function map_ma_update(pid,ath,atv){
    var obj = map_ma_param[pid];
    if (obj==null) obj = {};
    obj.ath = ath;
    obj.atv = atv;
    map_ma_param[pid] = obj;
}
function map_ma_show_a(pid,title,hname){
    $("#map_ma_id").val(pid);
    $("#map_ma_name").val(title);
}
function map_ma_confirm_update(){
    var pid = $.trim($("#map_ma_id").val());
    if (pid=="") {
        alert_notice("请先选择要编辑的标注");
        return false;
    }
    var title = $.trim($("#map_ma_name").val());
    if(title==""){
        alert_notice("请填写标注的名称");
        return false;
    }
    

    var sb = _U.getSubmit("/plugin/map_annotation_ma/map_annotation_ma.php", null, "ajax", false);
    sb.pushData("act","update");
    sb.pushData("pid",pid);
    sb.pushData("title",title);
    sb.submit(function () {

    }, function (data) {
        if(data.status==1){
            var p = data.obj;
            var krpano = document.getElementById('map_ma_obj');
            krpano.call('removehotspot('+p.cus_hotspot_name+')');
            krpano.call('removeplugin(' + ('tooltip_' + p.cus_hotspot_name) + ',true);');
            krpano.call(
                "set(schp_name,"+p.cus_hotspot_name+");"+
                "addhotspot(get(schp_name));"+
                "set(hotspot[get(schp_name)].url,"+p.cus_img+");"+
                "set(hotspot[get(schp_name)].ath,"+p.ath+");"+
                "set(hotspot[get(schp_name)].atv,"+p.atv+");"+
                "set(hotspot[get(schp_name)].edge,bottom);"+
                "set(hotspot[get(schp_name)].zoom,false);"+
                "set(hotspot[get(schp_name)].onclick,js(map_ma_show_a("+p.id+",'"+title+"','"+p.cus_hotspot_name+"')););"+
                "set(hotspot[get(schp_name)].ondown,dragcommenthotspot());"+
                "set(hotspot[get(schp_name)].onup,js(map_ma_update("+p.id+",get(xml.scene),get(ath),get(atv))));"+
                "set(hotspot[get(schp_name)].hotspottitle,"+title+");"+
                "set(hotspot[get(schp_name)].width,'prop'); set(hotspot[get(schp_name)].height,'50');"+
                "set(hotspot[get(schp_name)].visible,true);"+
                "txtadd(hotspot[get(schp_name)].onloaded,'do_crop_animation(128,128, 60);');"+
                "txtadd(hotspot[get(schp_name)].onloaded,'add_all_the_time_tooltip(hotspot[get(schp_name)].hotspottitle);');"
            );
             alert_notice('修改成功','success');
        }else{
            alert_notice(data.msg);
        }
    });

}
function map_ma_cut(){
    var pid = $.trim($("#map_ma_id").val());
    if (pid=="") {
        alert_notice("请先选择要编辑的标注");
        return false;
    }else{
       var sb = _U.getSubmit("/plugin/map_annotation_ma/map_annotation_ma.php", null, "ajax", false);
            sb.pushData("act","get");
            sb.pushData("pid",pid);
            sb.submit(function () {

            }, function (data) {
                if (data.status==1) {
                    var krpano = document.getElementById('map_ma_obj');
                     var p = data.obj;
                    map_ma_temp=p;
                     krpano.call('removehotspot('+p.cus_hotspot_name+')');
                    krpano.call('removeplugin(' + ('tooltip_' + p.cus_hotspot_name) + ',true);');
                    alert_notice("请到相应的地图页粘贴");
                }else{
                    alert_notice(data.msg);
                    return false;
                }
            });
      
    }
}
function map_ma_paste(){
    if (map_ma_temp) {
        var krpano = document.getElementById('map_ma_obj');
        map_ma_temp.ath = krpano.get('view.hlookat');
        map_ma_temp.atv = krpano.get('view.vlookat');
        map_ma_temp.sys_scene = krpano.get('xml.scene');
        alert_notice('等待复制数据');
        var sb = _U.getSubmit("/plugin/map_annotation_ma/map_annotation_ma.php", null, "ajax", false);
        sb.pushData("act","paste");
        sb.pushData("param",map_ma_temp);
        sb.submit(function () {

        }, function (data) {
            if (data.status==1) {
                map_annotation_ma_addHostspot(krpano);
            }else{
                alert_notice(data.msg);
            }
        });
       
    }else{
        alert_notice("请先剪切对应标注");
    }
     
}
function map_ma_remove(){
    var pid = $.trim($("#map_ma_id").val());
    if (pid=="") {
        alert_notice("请先选择要编辑的标注");
        return false;
    }
    bootbox.confirm({
        message:"确定要删除该标注吗?",
        buttons: {  
            confirm: {  
                label: '确认',  
                className: 'btn-primary'  
            },  
            cancel: {  
                label: '取消',  
                className: 'btn-default'  
            }  
        },
        title:"提示：",
        callback:function(result) {
            if(result){
               var sb = _U.getSubmit("/plugin/map_annotation_ma/map_annotation_ma.php", null, "ajax", false);
                sb.pushData("act","remove");
                sb.pushData("pid",pid);
                sb.submit(function () {

                }, function (data) {
                    if (data.status==1) {
                        var p = data.obj;
                        var krpano = document.getElementById('map_ma_obj');
                        krpano.call('removehotspot('+p.cus_hotspot_name+')');
                        krpano.call('removeplugin(' + ('tooltip_' + p.cus_hotspot_name) + ',true);');
                        alert_notice('删除成功','success');
                    }else{
                        alert_notice(data.msg);
                    }
                });
            }
        }
    });
    
}
function map_ma_submit(){
     var sb = _U.getSubmit("/plugin/map_annotation_ma/map_annotation_ma.php", null, "ajax", false);
    sb.pushData("act","submit");
    sb.pushData("params",map_ma_param);
    sb.submit(function () {

    }, function (data) {
        if (data.status==1) {
            alert_notice('提交成功','success');
        }else{
            alert_notice(data.msg);
        }
    });
}
function map_annotation_ma_addHostspot(krpano){
    var hotspotTitle = map_ma_temp.cus_hotspot_title;
    if (hotspotTitle.length>10) {
        hotspotTitle = hotspotTitle.substring(0,10)+"...";
    }
     krpano.call(
        "set(schp_name,"+map_ma_temp.cus_hotspot_name+");"+
        "addhotspot(get(schp_name));"+
        "set(hotspot[get(schp_name)].url,"+map_ma_temp.cus_img+");"+
        "set(hotspot[get(schp_name)].ath,"+map_ma_temp.ath+");"+
        "set(hotspot[get(schp_name)].atv,"+map_ma_temp.atv+");"+
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