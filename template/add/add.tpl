{include file="{$_lang.moban}/library/header.lbi"}
{include file="{$_lang.moban}/library/member_paths.lbi"}
<link rel="stylesheet" href="/static/css/fileinput.min.css">
<link rel="stylesheet" href="/static/css/chosen.min.css">
<link rel="stylesheet" href="/static/css/tag_chosen.css">
<div class="container">
	{if !empty($pic_add_ad)}
		<div class="row" style="padding-left: 10px;padding-right: 10px; margin-bottom: 20px;">
			{$pic_add_ad}
		</div>
	{/if}
	<div class="update_div" style="min-height: 600px;margin-left: auto;margin-right: auto;">
		
		<ul class="nav nav-tabs">
		  <li class="active"><a href="###" data-target="#tab_upimg" data-toggle="tab">全景图片</a></li>
		  <li><a href="###" data-target="#tab_upvideo" data-toggle="tab">全景视频</a></li>
		  <li><a href="###" data-target="#object_around" data-toggle="tab">物体环视</a></li>
		</ul>
		<div class="tab-content" style="background: #fff">
		  <div class="tab-pane fade active in" id="tab_upimg">
		  	{if $limit_num}
		  	 <div class="row">
		  		<div class="col-md-3">
		  			<div class="input-group">
		  			  <span class="input-group-addon">项目名称</span>
		  			  <input type="text" class="form-control" name="pname" id="pname" maxlength="30" placeholder="请输入长度30个字符以内的名称">
		  			</div>
		  		</div>
		  		<div class="col-md-3">
		  			<div class="input-group" >
		  			  <span class="input-group-addon">分类</span>
		  			  <select class="form-control" id="atlas">
						 {foreach $atlas as $v}
	  			   			 <option value="{$v.pk_atlas_main}">{$v.name}</option>
						 {/foreach}
		  			  </select>
		  			</div>
		  		</div>
		
				<div class="col-md-4">
					<!-- <div class="input-group " style="width:100%">
				      <select data-placeholder="请选择3个以内的标签" id="pic_chosen" class="chosen-select form-control" tabindex="-1" multiple="">
				        {foreach $tags as $tag}
				        	{if $tag.type eq 1}
				        		<option value="{$tag.id}">{$tag.name}</option>
				        	{/if}
				        {/foreach}
				      </select>
				    </div> -->
				    <div class="chosen-container chosen-container-multi" style="width: 100%;" title="" id="pic_chosen_chosen">
					    <ul class="chosen-choices">
					    	<li class="search-field">
					    		<input type="text" value="请选择3个以内的标签" class="" autocomplete="off" style="width: 100%;" tabindex="-1" >
					    	</li>
					    </ul>
				        <div class="chosen-drop tag_chose">
				    	  	<div>
				    	  		<span style="font-size: 16px;">常用标签</span>
				    	  		<span style="margin-left: 10px;color: #aaa;font-size: 12px;">点击'x'可以将自定义标签去掉</span>
				    	  	</div>
				    	  	<div class="tag_list">
					    	  	{foreach $tags as $tag}
					    	  		{if $tag.type eq 1}
					    	  			<span class="tag" data-tagid="d_{$tag.id}" data-tagname="{$tag.name}">{$tag.name}</span>
					    	  		{/if}
					    	  	{/foreach}
					    	  	{foreach $custom_tags as $ctag}
									{if $ctag.type eq 1}
						    	  		<span class="tag" data-tagid="c_{$ctag.id}" data-tagname="{$ctag.name}">{$ctag.name}<a class="custom_tag_remove" data-tagid="{$ctag.id}">x</a></span>
						    	  	{/if}
					    	  	{/foreach}
				    	  	</div>
				    	  	<div class="row custom_tag" style="margin-top: 10px;">
				    	  		<div class="col-md-5">
				    	  		 	<input class="form-control custom_tag_input" maxlength="10" type="text" placeholder="输入自定义标签" >
				    	  		</div>
				    	  		<div class="col-md-3">
				    	  			<button class="btn custom_tag_confirm" type="button">添加</button>
				    	  		</div>
				    	  	</div>
				    	</div>
				    </div>
				    
				</div>

				<div class="col-md-2">
					{if $_lang.map_uid neq $user.pk_user_main}
						{include file="plugin/map_sub/template/edit.lbi"}
						{include file="plugin/map_sub/template/edit_resource.lbi"}
					{/if}
				</div>

		  	 </div>

			 <div class="row" style="margin-top:20px">
			 	<div class="col-md-12">
			 	 <input id="imgUpload" name="file" type="file" multiple="" accept="image/jpeg,image/tiff" class="">
			 	 </div>
			 </div>
			 <div class="row" style="margin-top:20px">
			 	<div class="col-md-12">
			 		<button class="btn btn-block btn-primary" type="button" id="publish_img">立即生成</button>
			 	</div>
			 </div>
			{else}
			 <img src="/static/images/ico/warning.png" class="fl"/> 
			 &nbsp;你可发布的作品数量已达上限，无法再发布，请联系客服！
			{/if} 
		  </div>
		  <div class="tab-pane fade" id="tab_upvideo">
		    
		      <div class="row" style="margin-top:20px">
		        <label for="vname" class="col-sm-2">视频名称</label>
		        <div class="col-md-6 col-sm-10">
		          <input type="text" class="form-control" id="vname" name="vname" placeholder="请输入长度30个字符以内的名称">
		        </div>
		      </div>
			  <div class="row" style="margin-top:20px">
			  	<label for="video_chosen" class="col-sm-2">视频标签</label>
				<div class="col-md-6">
				      <div class="chosen-container chosen-container-multi" style="width: 100%;" title="" id="video_chosen">
					    <ul class="chosen-choices">
					    	<li class="search-field">
					    		<input type="text" value="请选择3个以内的标签" class="" autocomplete="off" style="width: 100%;" tabindex="-1" >
					    	</li>
					    </ul>
				        <div class="chosen-drop tag_chose">
				    	  	<div>
				    	  		<span style="font-size: 16px;">常用标签</span>
				    	  		<span style="margin-left: 10px;color: #aaa;font-size: 12px;">点击'x'可以将自定义标签去掉</span>
				    	  	</div>
				    	  	<div class="tag_list">
					    	  	{foreach $tags as $tag}
					    	  		{if $tag.type eq 2}
					    	  			<span class="tag" data-tagid="d_{$tag.id}" data-tagname="{$tag.name}">{$tag.name}</span>
					    	  		{/if}
					    	  	{/foreach}
					    	  	{foreach $custom_tags as $ctag}
									{if $ctag.type eq 2}
						    	  		<span class="tag" data-tagid="c_{$ctag.id}" data-tagname="{$ctag.name}">{$ctag.name}<a class="custom_tag_remove" data-tagid="{$ctag.id}">x</a></span>
						    	  	{/if}
					    	  	{/foreach}
				    	  	</div>
				    	  	<div class="row custom_tag" style="margin-top: 10px;">
				    	  		<div class="col-md-5">
				    	  		 	<input class="form-control custom_tag_input" maxlength="10" type="text" placeholder="输入自定义标签" >
				    	  		</div>
				    	  		<div class="col-md-3">
				    	  			<button class="btn custom_tag_confirm" type="button">添加</button>
				    	  		</div>
				    	  	</div>
				    	</div>
				    </div>
				</div>
			  </div>
		       <div class="row" style="margin-top:20px">
		        <label for="profile" class="col-sm-2">视频简介</label>
		        <div class="col-md-6">
		          <textarea name="profile" id="profile"  rows="5" class="form-control" placeholder="视频项目简介"></textarea>
		        </div>
		      </div>
		      <div class="row" style="margin-top:20px">
				<div class="col-md-8 col-md-offset-2 ">
	                <table class="table table-striped table-hover text-left" id="video_up_table" style="margin-top:40px; display:none;">
	                    <thead>
	                      <tr>
	                        <th class="col-md-4">文件名</th>
	                        <th class="col-md-2">大小</th>
	                        <th class="col-md-6">进度</th>
	                      </tr>
	                    </thead>
	                    <tbody id="fsUploadProgress">
	                    </tbody>
	                </table>
	            </div>
		      </div>
			  <div class="row" style="margin-top:20px">
		        <label for="vcover" class="col-sm-2">全景视频</label>
		        <div class="col-md-1">
		         <button class="btn" id="videoupload" name="video">选择视频</button>
		        </div>
		        <div class="col-md-3">
		        	<span class="text-muted">多个清晰度，请上传多个文件</span>
		        </div>
		      </div>
		       <div class="row" style="margin-top:20px">
				<div class="col-md-8 col-md-offset-2 ">
	                <table class="table table-striped table-hover text-left" id="audio_up_table" style="margin-top:40px; display:none;">
	                    <thead>
	                      <tr>
	                        <th class="col-md-4">文件名</th>
	                        <th class="col-md-2">大小</th>
	                        <th class="col-md-6">进度</th>
	                      </tr>
	                    </thead>
	                    <tbody id="audio_UploadProgress">
	                    </tbody>
	                </table>
	            </div>
		      </div>
		      <div class="row" style="margin-top:20px">
		        <label for="vcover" class="col-sm-2">音频<small class="text-muted"> (可选) </small></label>
		        <div class="col-md-1">
		         <button class="btn" id="audioupload" name="audio">选择音频</button>
		        </div>
		        <div class="col-md-3">
		        	<span class="text-muted">ios下会出现有画面没声音的情况，请上传一个<span style="color: #c40000">mp3</span>或<span style="color: #c40000">m4a</span>格式音频文件</span>
		        </div>
		      </div>
		      <div class="row" style="margin-top:20px">
		        <div class="col-sm-offset-2 col-sm-2">
		          <button id="publish_video" class="btn btn-primary btn-block">发布</button>
		        </div>
		      </div>
		  </div>

	  	  <div class="tab-pane fade" id="object_around">
	  	      <div class="row" style="margin-top:20px">
		  		<div class="col-md-4">
		  			<div class="input-group">
		  			  <span class="input-group-addon">项目名称</span>
		  			  <input type="text" class="form-control" name="oname" id="oname" maxlength="30" placeholder="请输入长度30个字符以内的名称">
		  			</div>
		  		</div>
		  		<div class="col-md-2">
		  			<div class="input-group">
		  			  	<label class="checkbox-inline" style="margin-top: 6px;">
					   		<input type="checkbox" id="flag_publish" checked> 公开作品
						</label>
		  			</div>
		  		</div>
	  	      </div>
		  	   <div class="row" style="margin-top:20px">
		  	    	<div class="col-md-12">
		  	    		<input id="objImgUpload" name="file" type="file" multiple="" accept="image/jpeg,image/png" class="">
		  	    	</div>
		  	   </div> 
		  	   <div class="row" style="margin-top:20px">
			 	<div class="col-md-12">
			 		<button class="btn btn-block btn-primary" type="button" id="publish_obj">立即生成</button>
			 	</div>
			 </div>
	  	  </div>

		</div>
	</div>
</div>

<!--上传成功弹框-->
<div class="modal fade" id="myModal">
	<div class="modal-dialog">
	  <div class="modal-content">
	    <div class="modal-header">
	      <!-- <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">关闭</span></button> -->
	      <h4 class="modal-title">提示：</h4>
	    </div>
	    <div class="modal-body">
	      <p class="text-muted"><img src="/static/images/loading.gif" alt="">上传完成，大概需要2~5分钟，请等待后台处理...</p>
	    </div>
	    <div class="modal-footer">
	      <button type="button" class="btn btn-primary"  style="display:none" onclick="javascript:window.location.href='/member/project'">确定</button>
	    </div>
	  </div>
	</div>
</div>
<script src="/static/js/kr/tag_chosen.js"></script>	
<script>
	var up_url = "{$up_url}";
	var qn_video_token;
	var videoParams={} ;
	videoParams.videos = new Array();
	$(function(){
		init_tag_chose("pic_chosen_chosen",1);
		init_tag_chose("video_chosen",2);
	})
</script>
<script language="JavaScript" type="text/javascript" src="/static/js/fileinput-v4.34.js"></script>
<script language="JavaScript" type="text/javascript" src="/static/js/fileinput_locale_zh.js"></script>
<script language="JavaScript" type="text/javascript" src="/static/js/chosen.js"></script>
<script language="JavaScript" type="text/javascript" src="/static/js/plupload/moxie.js"></script>
<script language="JavaScript" type="text/javascript" src="/static/js/plupload/plupload.dev.js"></script>
<script language="JavaScript" type="text/javascript" src="/static/js/kr/work_add.js"></script>

{if $img_store_type eq 'qiniu'}
<script language="JavaScript" type="text/javascript" src="/static/js/qiniu.min.js"></script>
<script language="JavaScript" type="text/javascript" src="/static/js/qiniu_ui.js"></script>
<link rel="stylesheet" href="/static/css/qiniu_main.css">
<script>
{literal}

var video_up = Qiniu.uploader({
        runtimes: 'html5,flash,html4',
        browse_button: 'videoupload',
        max_retries: 1,
        max_file_size: '900mb',
        flash_swf_url: '/static/js/plupload/Moxie.swf',
        dragdrop: true,
        chunk_size: '4mb',
        save_key: false,
        unique_names: false,
        filters : {
            max_file_size : '900mb',
            prevent_duplicates: true,
            mime_types: [
                {title : "视频文件", extensions : "mp4"} 
            ]
        },
        multi_selection: false,
        get_new_uptoken: false, 
       // uptoken_url:'/get_token.php?act=video',
        uptoken_func: function (file) {
        	qn_video_token ={};
        	$.ajax({
        		url:"/get_token.php",
        		data:{"act":"video"},
	    		async: false,
	    		success:function(result){
	    		 	result = eval("("+result+")");
	    		 	qn_video_token.prefix= result.prefix;
    		 		qn_video_token.token = result.token;
	    		}
	    	})
	    	return qn_video_token.token;
        },
        domain:up_url,
        auto_start: false,
        log_level: 5,
        init: {
            'FilesAdded': function (up, files) {
            	$("#video_up_table").show();
                plupload.each(files, function (file) {
                    var progress = new FileProgress(file, 'fsUploadProgress');
                    progress.setStatus("点击 \"发布\" 按钮开始上传...");
                    progress.bindUploadCancel(up);
                });
            },
            'BeforeUpload': function (up, file) {
                var progress = new FileProgress(file, 'fsUploadProgress');
                var chunk_size = plupload.parseSize(this.getOption('chunk_size'));
                if (up.runtime === 'html5' && chunk_size) {
                    progress.setChunkProgess(chunk_size);
                }
            },
            'UploadProgress': function (up, file) {
                var progress = new FileProgress(file, 'fsUploadProgress');
                var chunk_size = plupload.parseSize(this.getOption('chunk_size'));
                progress.setProgress(file.percent + "%", file.speed, chunk_size);
            },
            'UploadComplete': function () {

            },
            'FileUploaded': function (up, file, info) {
                var progress = new FileProgress(file, 'fsUploadProgress');
                progress.setComplete(up, info);
                progress.setStatus("");
                var video = {};
                video.location = eval("("+info+")").key;
                video.name = file.name;
                video.size = file.size;
                videoParams.videos.push(video);
                var files = up.files;
                if(files[files.length-1].id == file.id){
					if (audio_uploader.files.length>0) 
						audio_uploader.start();
					else
						doVideoCommint();
                }
            },
            'Error': function (up, err, errTip) {

                $("#video_up_table").show();
                var progress = new FileProgress(err.file, 'fsUploadProgress');
                progress.setError();
                progress.setStatus(errTip);
            }
            ,
            'Key': function (up, file) {

            	var name =file.name;
                var key = qn_video_token.prefix+generic_name()+name.substr(name.lastIndexOf("."));
                // do something with key
                return key;
            }
        }
    });
</script>
{/literal}
	{else if $img_store_type eq 'oss'}
{literal}
	<script>
		var key ;
		function set_upload_param(up, filename, ret)
		{
		    if (ret == false)
		    {
		        qn_video_token ={};
            	$.ajax({
            		url:"/get_token.php",
            		data:{"act":"video"},
    	    		async: false,
    	    		success:function(result){
    	    		 	result = eval("("+result+")");
    	    		 	qn_video_token.prefix= result.prefix;
	    		 		qn_video_token.policy = result.policy;
	    		 		qn_video_token.OSSAccessKeyId = result.accessid;
	    		 		qn_video_token.host = result.host;
	    		 		qn_video_token.signature = result.signature;
    	    		}
    	    	})
		    }else{
	            key = qn_video_token.prefix+generic_name()+filename.substr(filename.lastIndexOf("."));
			    new_multipart_params = {
			        'key' : key,
			        'policy': qn_video_token.policy,
			        'OSSAccessKeyId': qn_video_token.OSSAccessKeyId, 
			        'success_action_status' : '200', //让服务端返回200,不然，默认会返回204
			        'host' : qn_video_token.host,
			        'signature': qn_video_token.signature,
			    };

			    up.setOption({
			        'url': up_url,
			        'multipart_params': new_multipart_params
			    });
			}
		}
		var video_up = new plupload.Uploader({
		    runtimes : 'html5,flash,silverlight,html4',
		    browse_button : 'videoupload', 
		    multi_selection: false,
		    flash_swf_url: '/static/js/plupload/Moxie.swf',
		    silverlight_xap_url : '/static/js/plupload/Moxie.xap',
		    url : "http://oss.aliyuncs.com",
		    filters: {
	            mime_types : [ //只允许上传图片
	            { title : "Video files", extensions : "mp4" }, 
	            ],
	            max_file_size : '900mb', //
	            prevent_duplicates : true //不允许选取重复文件
	        },
		    init: {
		        PostInit: function() {
		        	set_upload_param(video_up, '', false);
		        },
		        FilesAdded: function(up, files) {
					var file= files[files.length-1];
					$("#fsUploadProgress").append('<tr>'+
	                        '<th class="col-md-4">'+file.name+'</th>'+
	                        '<th class="col-md-2">'+(file.size/1024).toFixed(1)+' KB</th>'+
	                        '<th class="col-md-6"><div class="progress progress-striped" id="'+file.id+'"><div class="progress-bar progress-bar-success" style="width: 0%"></div><span class="text-muted" style="font-size:11px;font-weight:normal;">点击下方发布按钮开始上传</span></div></th>'+
	                      	'</tr>');
					$("#video_up_table").show();
		            return false;
		        },
		        BeforeUpload: function(up, file) {
		            $("#videoupload").css('pointer-events','none');
		            set_upload_param(up, file.name, true);
		        },

		        UploadProgress: function(up, file) {
		            var d = document.getElementById(file.id);
		            d.getElementsByTagName('span')[0].innerHTML = '  ' + file.percent + "%";
		            var progBar = d.getElementsByTagName('div')[0];
		            // var progBar = prog.getElementsByTagName('div')[0]
		            progBar.style.width= file.percent+'%';
		            progBar.setAttribute('aria-valuenow', file.percent);
		        },

		        FileUploaded: function(up, file, info) {
		            if (info.status == 200)
		            {   
    	                var video = {};
    	                video.location = key;
    	                video.name = file.name;
    	                video.size = file.size;
    	                videoParams.videos.push(video);
    	                var files = up.files;
    	                if(files[files.length-1].id == file.id){
    						if (audio_uploader.files.length>0)
    							audio_uploader.start();
    						else
    							doVideoCommint();
    	                }
		            }
		            else
		            {
		                alert_notice("上传失败");
		            } 
		            $("#selectfiles").css('pointer-events','');
		        },

		        Error: function(up, err) {
		            if (err.code == -600) {
		                alert_notice("选择的文件太大了,不能超过900M");
		            }
		            else if (err.code == -601) {
		                 alert_notice("只能上传jpg格式大小的图片");
		            }
		            else if (err.code == -602) {
		                 alert_notice("这个文件已经上传过一遍了");
		            }
		            else 
		            {   
		                alert_notice("上传异常");
		            }
		        }
		    }
		});
		video_up.init();
	</script>
{/literal}

{/if}
{literal}
<script>
	var audio_key="";
	function doVideoCommint(){
		var obj = alert_notice("等待执行...","success",'top',0);
        	$.post("/add/video",{
				"act":"doAdd",
				"params":JSON.stringify(videoParams)
				},function(result){
					obj.hide();
					result = eval("("+result+")");
					if (result.flag) {
						alert_notice("发布成功","success");
						window.location.href ="/member/project?act=videos";
					}else{
						alert_notice(result.msg);
					}
		})
	}
	//音频文件上传
	function set_audio_upload_param(up, filename, ret)
	{
	    if (ret == false)
	    {
	        audio_token ={};
        	$.ajax({
        		url:"/get_token.php",
        		data:{"act":"video"},
	    		async: false,
	    		success:function(result){
	    		 	result = eval("("+result+")");
	    	    		audio_token.prefix= result.prefix;
	    		 	if (result.policy) {
		    		 		audio_token.policy = result.policy;
		    		 		audio_token.OSSAccessKeyId = result.accessid;
		    		 		audio_token.host = result.host;
		    		 		audio_token.signature = result.signature;
	    		 	}else{
	    		 		audio_token.token = result.token;
	    		 	}
	    		}
	    	})
	    }else{
            audio_key = audio_token.prefix+generic_name()+filename.substr(filename.lastIndexOf("."));
            var new_multipart_params;
            if (audio_token.policy) {
            	new_multipart_params = {
			        'key' : audio_key,
			        'policy': audio_token.policy,
			        'OSSAccessKeyId': audio_token.OSSAccessKeyId,
			        'success_action_status' : '200', //让服务端返回200,不然，默认会返回204
			        'host' : audio_token.host,
			        'signature': audio_token.signature,
		    	}
            }else{
            	 new_multipart_params = {
		        	'key' : audio_key,
		        	'token':audio_token.token
		    	}
            }
		    up.setOption({
		        'url': up_url,
		        'multipart_params': new_multipart_params
		    });
		}
	}
	var audio_uploader = new plupload.Uploader({
		    runtimes : 'html5,flash,silverlight,html4',
		    browse_button : 'audioupload',
		    multi_selection: false,
		    max_file_count:1,
		    flash_swf_url: '/static/js/plupload/Moxie.swf',
		    silverlight_xap_url : '/static/js/plupload/Moxie.xap',
		    url : up_url,
		    filters: {
	            mime_types : [ //只允许上传图片
	            { title : "audio files", extensions : "m4a,mp3" },
	            ],
	            max_file_size : '10M', //
	            prevent_duplicates : true //不允许选取重复文件
	        },
		    init: {
		        PostInit: function() {
		        	set_audio_upload_param(audio_uploader, '', false);
		        },
		        FilesAdded: function(up, files) {
		        	if (files.length>1) {
		        		alert_notice("最多上传一个音频");
		        		return false;
		        	}
					var file= files[0];
					$("#audio_UploadProgress").append('<tr>'+
	                        '<th class="col-md-4">'+file.name+'</th>'+
	                        '<th class="col-md-2">'+(file.size/1024).toFixed(1)+' KB</th>'+
	                        '<th class="col-md-6"><div class="progress progress-striped" id="'+file.id+'"><div class="progress-bar progress-bar-success" style="width: 0%"></div><span class="text-muted" style="font-size:11px;font-weight:normal;">点击下方发布按钮开始上传</span></div></th>'+
	                      	'</tr>');
					$("#audio_up_table").show();
		            return false;
		        },
		        BeforeUpload: function(up, file) {
		            $("#audioupload").css('pointer-events','none');
		            set_audio_upload_param(up, file.name, true);
		        },

		        UploadProgress: function(up, file) {
		            var d = document.getElementById(file.id);
		            d.getElementsByTagName('span')[0].innerHTML = '  ' + file.percent + "%";
		            var progBar = d.getElementsByTagName('div')[0];
		            progBar.style.width= file.percent+'%';
		            progBar.setAttribute('aria-valuenow', file.percent);
		        },
		        FileUploaded: function(up, file, info) {
		        	if (info.status == 200 )
		            {
		            	videoParams.audio = audio_key; 
		            	doVideoCommint();
		            }else{
		            	alert_notice('上传失败');
		            }
		        },
		        Error: function(up, err) {
		            if (err.code == -600) {
		                alert("选择的文件太大了,不能超过10M");
		            }
		            else if (err.code == -601) {
		                 alert("只能上传mp3或m4a格式的文件");
		            }
		            else if (err.code == -602) {
		                 alert("这个文件已经上传过一遍了");
		            }
		            else
		            {
		                alert("上传异常");
		            }
		        }
		    }
		 });
		 audio_uploader.init();	
</script>
{/literal}

{include file="{$_lang.moban}/library/footer.lbi"}