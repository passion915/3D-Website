<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <title></title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="x-ua-compatible" content="IE=edge,chrome=1" />
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes"> -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta content="no-cache, no-store, must-revalidate" http-equiv="Cache-Control" />
    <meta content="no-cache" http-equiv="Pragma" />
    <meta content="0" http-equiv="Expires" />
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <link rel="stylesheet" href="/template/{$_lang.moban}/css/redefine.css">
    <link rel="stylesheet" href="/static/css/alivideo.css" />
    <link rel="stylesheet" type="text/css" href="/static/css/viewer.css">

    <script language="JavaScript" type="text/javascript" src="/static/js/kr/uhweb.js?v=2.0"></script>
    <script language="JavaScript" type="text/javascript" src="/static/js/kr/vrshow.js?v=1.3"></script>

    <!-- <script type="text/javascript" src="/static/js/viewer.min.js"></script> -->
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <script src="/static/js/Vistor.js"></script>

    <!-- <script language="JavaScript" type="text/javascript" src="/static/js/jquery.js"></script> -->
    <script language="JavaScript" type="text/javascript" src="/static/js/jquery.panzoom.js"></script>
    <script language="JavaScript" type="text/javascript" src="/static/js/jquery.mousewheel.js"></script>
    <script >
      var param = {};
      param.pk_works_main = "{$pro.pk_works_main}";
      Vistor.Vistor_Panos('/ucenter/statistics.php', param);
    </script>
    <style>
        @-ms-viewport { width:device-width; }
        @media only screen and (min-device-width:800px) { html { overflow:hidden; } }
        html { height:100%; }
        body { height:100%; overflow:hidden; margin:0; padding:0; font-family:microsoft yahei, Helvetica, sans-serif;  background-color:#000000; }
      .time { position: absolute; left: -125px; top: 20%; width: 130px; z-index: 500; padding: 0; overflow-y: scroll; height: 500px;}
      .time-open { position: absolute; left: 10px; top: 45%; z-index: 500;  color: white;  background: rgba(0, 0, 0, 0.5);padding: 10px; border-radius: 15px; cursor: pointer;}
      .time .thumb {  text-align: center; display: block; }
      .thumb p{ position: relative;background: none rgba(0, 0, 0, 0.5);color: white; padding: 6px 6px 0px 6px;
      padding-right: 17px;
      width: 121px;}
      .none { display: none }
      .img-active { border: 1px solid rgb(246, 166, 0) }
      .parent-scene { margin-left: 10px; width: 100%; background: rgba(0, 0, 0, 0.5); padding: 6px 6px 0px 6px;
      padding-right: 17px;
      width: 121px;}
      div .view{
        margin-top: 0px !important;
      }
    </style>
</head>
<body>

    <!-- 时光机内容 -->
    <div class="time-open">时<br/>光<br/>机</div>
    <div class="time " id="time">
      {foreach $all_scenes as $value}
      <div class="parent-scene scene_{$value['viewuuid']} " s-title="{$value['sceneTitle']}" >
        {if !empty($value['children'])}
          {foreach $value['children'] as $v}
            <div class="thumb " id="scene_{$v.view_uuid}" atv-offset="{$v.atv_offset}" ath-offset="{$v.ath_offset}" name="scene_{$v.view_uuid}"><img src="{$v.thumb_path}" alt="{$v.filename}" ><p>{$v.filename}</p></div>
          {/foreach}
        {/if}
      </div>
      {/foreach}
    </div>

    <script type="text/javascript">
      window.onload = function(){
          var height = $('body').height();
          //console.log(height);
          var div = $('#time').height();
          //console.log(div);

          $('#time').css('top', (height - div)/2 +'px');

           var krpano = document.getElementById('krpanoSWFObject');

          $(".time").on("click",".thumb",function(){
              var scene_name = $(this).attr('name');
              $('.img-active').removeClass('img-active');
              $(this).find('img').addClass('img-active');

              // 获取切换前视角信息
              getViewInfo(krpano, $(this));
              // console.log('当前：'+scene_name);
              krpano.call('loadscene('+scene_name+')'); 
          });
        }

        function getViewInfo(krpano, el){
          // 获取目标场景偏移值
          var atv_offset = el.attr('atv-offset');
          var ath_offset = el.attr('ath-offset');

          // 
         /* console.log(atv_offset);
          console.log(ath_offset);*/

          // 当前场景值
          var now = {};
          now.ath = Number(krpano.get('view.hlookat').toFixed(1));
          now.atv = Number(krpano.get('view.vlookat').toFixed(1));

          // 当前场景类型
          var scene_name = krpano.get('xml.scene');
          // 当前场景偏移值
          now.ath_offset = Number($('#'+scene_name).attr('ath-offset')).toFixed(1);
          now.atv_offset = Number($('#'+scene_name).attr('atv-offset')).toFixed(1);


          if( krpano.get('scene['+scene_name+']').p_id == '' ){
            // 父场景
            scene_info.ath = (now.ath + Number(ath_offset)).toFixed(1);
            scene_info.atv = (now.atv + Number(atv_offset)).toFixed(1);
          }else{
            //子场景
            // 字场景切换偏移值
            atv_offset = Number(atv_offset - now.atv_offset).toFixed(1);
            ath_offset = Number(ath_offset - now.ath_offset).toFixed(1);

            scene_info.ath = (Number(now.ath) + Number(ath_offset)).toFixed(1);
            scene_info.atv = (Number(now.atv) + Number(atv_offset)).toFixed(1);
          }

          // 保存视角切换前信息，用于从子切换到父场景时
          scene_info.scene_name = scene_name
          scene_info.atv_offset = atv_offset;
          scene_info.ath_offset = ath_offset;
          /*console.log(scene_info);*/
        }
    </script>

    <script language="JavaScript" type="text/javascript" src="/tour/tour.js?v=121901"></script>
    <div id="fullscreenid" style="position:relative;width:100%; height:100%;">
         <div class="vrshow_tour_btn" id="buyObj" onclick="tobuy()" >
                <span class="btn_tour_text">立即购买</span>
         </div>
        <div id="panoBtns" style="display:none">
            <div class="vrshow_container_logo">
                <img id="logoImg" src="/plugin/custom_logo/images/custom_logo.png" style="display: none;"  onclick="javascript:window.open('{$_lang.host}')">

                <div class="vrshow_logo_title" id="user_name_wrap"  >
                    <div id="authorDiv" style="display: none;">作者：<span id="user_nickName">{$pro.nickname}</span></div>
                    <div style="clear:both;"></div>

                </div>
                {foreach $plugins as $k=>$v}
                    {if $v.enable eq 1 AND $v.view_container eq "left_top"}
                        {include file="plugin/$k/template/view.lbi"}
                    {/if}
                {/foreach}

            </div>

            <div class="vrshow_container_1_min">
                <div class="btn_fullscreen" onClick="fullscreen(this)" title="" style="display:none"></div>
                {foreach $plugins as $k=>$v}
                    {if $v.enable eq 1 AND $v.view_container eq "right_top"}
                        {include file="plugin/$k/template/view.lbi"}
                    {/if}
                {/foreach}
            </div>
            <div class="vrshow_radar_btn" onClick="toggleKrpSandTable()">
                <!-- <span class="btn_sand_table_text">沙盘</span> --> <!--kms0128 -->
            </div>
             <div class="map_marker" id="footmarkDiv" onClick="showFootMark()">
            </div>
            <div class="vrshow_tour_btn" onClick="startTourGuide()">
                <span class="btn_tour_text">一键导览</span>
            </div>
            <div class="vrshow_container_2_min">
            
                {foreach $plugins as $k=>$v}
                    {if $v.enable eq 1 AND $v.view_container eq "right_bottom"}
                        {include file="plugin/$k/template/view.lbi"}
                    {/if}
                {/foreach}
            </div>

            <div class="vrshow_container_3_min">
                <div class="img_desc_container_min scene-choose-width" style="display:none">
                    <img src="/static/images/skin1/vr-btn-scene.png" onClick="showthumbs()">
                    <div class="img_desc_min">场景选择</div>
                </div>
            </div>
        </div>
        <div id="pano" style="width:100%; height:100%;">
        </div>
		
		<div class="modal" id="pictextModal" data-backdrop="static" data-keyboard="false" style="z-index:2002">
            <div class="modal-dialog">
                <div class="modal-header text-center" >
                    <button type="button" class="close" onClick="hidePictext()"><span>&times;</span></button>
                    <span style="color: #353535;font-weight:700" id="pictextWorkName"></span>
                </div>
                <div class="modal-body" style="height:400px;overflow-y:scroll ">
                    <div class="row">                   
                        <div class="col-sm-offset-1 col-md-offset-1 col-md-10 col-sm-10 col-xs-12" id="pictextContent">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="logreg">
        </div>
        {foreach $plugins as $k=>$v}
            {if $v.enable eq 1 AND $v.view_resource eq 1}
                {include file="plugin/$k/template/resource.lbi"}
            {/if}
        {/foreach}
    </div>


<style type="text/css">
  #map_viewer{
    display: none;
    width: 100%;
    height: 100%;
    position:absolute;
    top: 0;
    background-color: rgba(0, 0, 0, 0.8);
    z-index: 5000;
  }
  #map_img_wrap{
      width: 1000px;
      margin-left:auto;
      margin-right:auto;
      position: relative;
      cursor: pointer !important;
      z-index: 5;
      -webkit-backface-visibility: initial !important;
      -webkit-transform-origin: 50% 50%;
  }
  .map_img{
    width: 1000px;
    height: 1000px;
  }
  .big_map_spot_wrap img{
    position: absolute;
  }
</style>
<div id="map_viewer" class="parent">
    <img src="/static/images/close.png" id="close-btn" style="width: 50px;position: fixed;top:10px;right: 10px;cursor: pointer;z-index: 9999">
    <div id="map_img_wrap" draggable="true" onclick="startup()">
      <img src="" class="map_img" >
      <div id="big_map_spot_wrap">
      </div>
       <div id="big_map_hat_wrap">
      </div>
      <div class="buttons" style="text-align: center ">
        <button class="del_flag">删除标志</button>
      </div>
    </div>
    
    
</div>
<script type="text/javascript">
   var hat_img_top = '';
   var hat_img_left = '';
   var hatname;
   var zoom_size = 1;

   dragElement(document.getElementById("map_img_wrap"));
   //object of the element to be moved
   $(function(){
    $(".del_flag").on("click touchstart",del_flag);
    $("#close-btn").on("click touchstart",close_map_viewer);

      if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
         $(".buttons").append("<button class='zoom-in'>Zoom In</button><button class='zoom-out'>Zoom Out</button><button class='reset'>Reset</button>");
          var $section = $('#map_img_wrap');
          $section.panzoom({
            $zoomIn: $section.find(".zoom-in"),
            $zoomOut: $section.find(".zoom-out"),
            /*$zoomRange: $section.find(".zoom-range"),*/
            $reset: $section.find(".reset")
          });
      }else{
        var sq = {};
        sq.e =document.getElementById("map_img_wrap");
        if (sq.e.addEventListener) {
          sq.e.addEventListener("mousewheel", MouseWheelHandler, false);
          sq.e.addEventListener("DOMMouseScroll", MouseWheelHandler, false);
        }else {
          sq.e.attachEvent("onmousewheel", MouseWheelHandler);
        }
        function MouseWheelHandler(e) {
          var $panzoom = $('#map_img_wrap').panzoom();
          e.preventDefault();
          var delta = e.delta || e.wheelDelta;
          var zoomOut = delta ? delta < 0 : e.deltaY > 0;
          var data = $panzoom[0];
          $panzoom.panzoom('zoom', zoomOut, {
            increment: 0.1,
            animate: false,
            focal: {
              clientX : e.clientX-data.offsetLeft,
              clientY : e.clientY-data.offsetTop
            }
          });
        }
      }
        
      $("#map_viewer .map_img").on("click touchstart",function(e){
          //alert(BrowserDetect.version + " :: " + BrowserDetect.browser);
          var window_w = $(window).width();
          var window_h = $(window).height();
          var map_length = window_w>window_h?window_h:window_w;
          $("#map_img_wrap").css('width',map_length+"px");
          $(".map_img").css('width',map_length+"px").css('height',(map_length-50)+"px");
          hatname = "hat_img";

          var elmnt_rec = $("#map_img_wrap").get(0).getBoundingClientRect();
          if(e.type != "touchstart"){
            radar_img_left = e.offsetX-12;
            radar_img_top = e.offsetY-12;

            hat_img_left = (((e.offsetX+3)/(map_length)).toFixed(10)*100)+"%";
            hat_img_top = (((e.offsetY-5)/(map_length-50)).toFixed(10)*100)+"%";
          }else{
            var touch = e.originalEvent.touches[0];
            zoom_size = window_w/elmnt_rec.width;
            radar_img_left = (touch.clientX-elmnt_rec.left-25)*zoom_size;
            radar_img_top = (touch.clientY-elmnt_rec.top-35)*zoom_size;

            hat_img_left = (((touch.clientX-elmnt_rec.left+5)/(map_length)).toFixed(10)*100)*zoom_size+"%";
            hat_img_top = (((touch.clientY-elmnt_rec.top-10)/(map_length-50)).toFixed(10)*100)*zoom_size+"%";
          }
          if($("#"+hatname).length==0){
            $("#big_map_hat_wrap").append('<img src="/static/images/hat.gif" id="'+hatname+'" style="width:25px;position:absolute;top:'+radar_img_top+'px;left:'+radar_img_left+'px">');
            addRadarHatSpot(hatname,hat_img_left,hat_img_top);
          }else{
            $("#"+hatname).css('top',radar_img_top).css('left',radar_img_left);
            var krpano = document.getElementById('krpanoSWFObject');
             krpano.set('layer['+hatname+'].x',hat_img_left);
             krpano.set('layer['+hatname+'].y',hat_img_top);
          }
      })
   });
   function addRadarHatSpot(name,x,y){
        var krpano = document.getElementById('krpanoSWFObject');
        krpano.call('addlayer('+name+');');
        krpano.set('layer['+name+'].url','/static/images/hat.gif');
        krpano.set('layer['+name+'].width','25px');
        krpano.set('layer['+name+'].height','25px');
        krpano.set('layer['+name+'].scale','1.0');
        krpano.set('layer['+name+'].oy','0');
        krpano.set('layer['+name+'].align','lefttop');
        krpano.set('layer['+name+'].edge','center');
        krpano.set('layer['+name+'].zorder','3');
        krpano.set('layer['+name+'].x',x);
        krpano.set('layer['+name+'].y',y);
        krpano.set('layer['+name+'].parent','radarmask');
    }
   function showMapViewer(){
      var window_w = $(window).width();
      var window_h = $(window).height();
      var map_length = window_w>window_h?window_h:window_w;
      $("#map_img_wrap").css('width',map_length+"px");
      $(".map_img").css('width',map_length+"px").css('height',(map_length-50)+"px");

      var krpano = document.getElementById('krpanoSWFObject');
      var sceneName = krpano.get('xml.scene');
      var sandTableObj = $("body").data("panoData").sand_table;
      var existFlag = false;
      var width =  $(".map_img").width();
      $(sandTableObj.sandTables).each(function(idx){
          if(this.sceneOpt[sceneName]){
              //设置背景图片
              var h = "";
              $.each(this.sceneOpt,function(name,value){
                 if(sceneName == name)
                   h+='<img src="/static/images/kr/radar-center.png" style="position:absolute;top:'+value.krpTop+';left:'+value.krpLeft+'">';
                 else
                   h+='<img src="/static/images/kr/radar-out.png" style="position:absolute;top:'+value.krpTop+';left:'+value.krpLeft+'">';
              });
              $("#big_map_spot_wrap").html(h);
              return false;
          }
      }); 
      $("#map_viewer").fadeIn();
   }
   function del_flag(){
      $("#big_map_hat_wrap").html("");
      var krpano = document.getElementById('krpanoSWFObject');
      krpano.call('removelayer('+hatname+')'); 
   }
   function close_map_viewer(){
      $("#big_map_spot_wrap").html("");
      $("#map_viewer").hide();
   }
   var startX = 0; startY = 0;
   function dragElement(elmnt) {
      var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
        elmnt.onmousedown = dragMouseDown;
    }

  function dragMouseDown(e) {
     document.addEventListener('contextmenu', function (e) {
          e.preventDefault();
      }, false);
    e = e || window.event;
    e.preventDefault();
    // get the mouse cursor position at startup:
    pos3 = e.clientX;
    pos4 = e.clientY;

    var elmnt = document.getElementById("map_img_wrap");
    startX = elmnt.offsetLeft;
    startY = elmnt.offsetTop;
    document.onmouseup = closeDragElement;
    // call a function whenever the cursor moves:
    document.onmousemove = elementDrag;
  }

  function elementDrag(e) {
    e = e || window.event;
    e.preventDefault();
    var elmnt = document.getElementById("map_img_wrap");
    pos1 = e.clientX - pos3;
    pos2 = e.clientY - pos4;
    elmnt.style.marginLeft =startX + pos1 + "px";
    elmnt.style.marginTop = startY + pos2 + "px";

  }

  function closeDragElement() {
    document.onmouseup = null;
    document.onmousemove = null;
  }    
</script>

</body>
<script type="text/javascript">

var pk_user_main = '{$pro.pk_user_main}';
 var flag = "{$flag}";
 var work_view_uuid ="{$viewid}"
 var atlas = "{$atlas}";
//组装分享的参数
//title
_title = '{$pro.name}';
_content = '{$pro.profile}';
_imgUrl = '{$pro.thumb_path}';
</script>

<script language="JavaScript" type="text/javascript" src="/static/js/kr/object.js"></script>
<script language="JavaScript" type="text/javascript" src="/static/js/kr/jssdk.js"></script>
<script type="text/javascript" src="/static/js/alivideo.js"></script>
<script language="JavaScript" type="text/javascript" src="/static/js/jquery.touchy.js"></script>
<script type="text/javascript">
  $('.time-open').click(function(){
    var time = $('.time');
    if(time.css('left')=='-125px'){
        time.animate({ left:'70px'}, "fast");
        time.animate({ left:'50px'}, "fast");
    }else{
        time.animate({ left:'70px'}, "fast");
        time.animate({ left: '-125px' }, "fast");
    }
  })
</script>
</html>