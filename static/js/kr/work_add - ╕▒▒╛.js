var qn_token ={} ; 
var params ={};
$(function(){
	$("#pic_chosen").chosen({
		 no_results_text: "没有找到",
		max_selected_options:3,
		width:"100%"
	});
	$('.update_div').on('shown.zui.tab', function(e) {
		$(".chosen-select").chosen({
			 no_results_text: "没有找到",
			 max_selected_options:3,
			 width:"100%"
		});
	});
	
	$(function() {
			get_token();
	    	var $wrap = $('#uploader'),
	        // 图片容器
	        $queue = $( '<ul class="filelist"></ul>' )
	            .appendTo( $wrap.find( '.queueList' ) ),

	        // 状态栏，包括进度和控制按钮
	        $statusBar = $wrap.find( '.statusBar' ),

	        // 文件总体选择信息。
	        $info = $statusBar.find( '.info' ),

	        // 上传按钮
	        $upload = $wrap.find( '.uploadBtn' ),

	        // 没选择文件之前的内容。
	        $placeHolder = $wrap.find( '.placeholder' ),

	        $progress = $statusBar.find( '.progress' ).hide(),

	        // 添加的文件数量
	        fileCount = 0,

	        // 添加的文件总大小
	        fileSize = 0,

	        // 优化retina, 在retina下这个值是2
	        ratio = window.devicePixelRatio || 1,

	        // 缩略图大小
	        thumbnailWidth = 110 * ratio,
	        thumbnailHeight = 110 * ratio,

	        // 可能有pedding, ready, uploading, confirm, done.
	        state = 'pedding',

	        // 所有文件的进度信息，key为file id
	        percentages = {},
	        // 判断浏览器是否支持图片的base64
	        isSupportBase64 = ( function() {
	            var data = new Image();
	            var support = true;
	            data.onload = data.onerror = function() {
	                if( this.width != 1 || this.height != 1 ) {
	                    support = false;
	                }
	            }
	            data.src = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==";
	            return support;
	        } )(),

	        // 检测是否已经安装flash，检测flash的版本
	        flashVersion = ( function() {
	            var version;

	            try {
	                version = navigator.plugins[ 'Shockwave Flash' ];
	                version = version.description;
	            } catch ( ex ) {
	                try {
	                    version = new ActiveXObject('ShockwaveFlash.ShockwaveFlash')
	                            .GetVariable('$version');
	                } catch ( ex2 ) {
	                    version = '0.0';
	                }
	            }
	            version = version.match( /\d+/g );
	            return parseFloat( version[ 0 ] + '.' + version[ 1 ], 10 );
	        } )(),

	        supportTransition = (function(){
	            var s = document.createElement('p').style,
	                r = 'transition' in s ||
	                        'WebkitTransition' in s ||
	                        'MozTransition' in s ||
	                        'msTransition' in s ||
	                        'OTransition' in s;
	            s = null;
	            return r;
	        })(),

	        // WebUploader实例
	        uploader;

	    if ( !WebUploader.Uploader.support('flash') && WebUploader.browser.ie ) {

	        // flash 安装了但是版本过低。
	        if (flashVersion) {
	            (function(container) {
	                window['expressinstallcallback'] = function( state ) {
	                    switch(state) {
	                        case 'Download.Cancelled':
	                            alert('您取消了更新！')
	                            break;

	                        case 'Download.Failed':
	                            alert('安装失败')
	                            break;

	                        default:
	                            alert('安装已成功，请刷新！');
	                            break;
	                    }
	                    delete window['expressinstallcallback'];
	                };

	                var swf = './expressInstall.swf';
	                // insert flash object
	                var html = '<object type="application/' +
	                        'x-shockwave-flash" data="' +  swf + '" ';

	                if (WebUploader.browser.ie) {
	                    html += 'classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" ';
	                }

	                html += 'width="100%" height="100%" style="outline:0">'  +
	                    '<param name="movie" value="' + swf + '" />' +
	                    '<param name="wmode" value="transparent" />' +
	                    '<param name="allowscriptaccess" value="always" />' +
	                '</object>';

	                container.html(html);

	            })($wrap);

	        // 压根就没有安转。
	        } else {
	            $wrap.html('<a href="http://www.adobe.com/go/getflashplayer" target="_blank" border="0"><img alt="get flash player" src="http://www.adobe.com/macromedia/style_guide/images/160x41_Get_Flash_Player.jpg" /></a>');
	        }

	        return;
	    } else if (!WebUploader.Uploader.support()) {
	        alert( 'Web Uploader 不支持您的浏览器！');
	        return;
	    }

	    // 实例化
	    uploader = WebUploader.create({
	        pick: {
	            id: '#filePicker',
	            label: '点击选择图片'
	        },
	       
	        dnd: '#dndArea',
	        paste: '#uploader',
	        swf: '/static/webUpload/js/Uploader.swf',
	        chunked: false,
	        chunkSize: 512 * 1024,
	        server: up_url,
	        accept: {
	            extensions: 'jpg,jpeg,tif',
	        },
	        formData:qn_token,
	        // 禁掉全局的拖拽功能。这样不会出现图片拖进页面的时候，把图片打开。
	        disableGlobalDnd: true,
	        fileNumLimit: 300,
	        fileSizeLimit: 200 * 1024 * 1024,    // 200 M
	        fileSingleSizeLimit: 100 * 1024 * 1024    // 50 M
	    });

	    // 拖拽时不接受 js, txt 文件。
	    uploader.on( 'dndAccept', function( items ) {
	        var denied = false,
	            len = items.length,
	            i = 0,
	            // 修改js类型
	            unAllowed = 'text/plain;application/javascript ';

	        for ( ; i < len; i++ ) {
	            // 如果在列表里面
	            if (~unAllowed.indexOf( items[ i ].type ) ) {
	                denied = true;
	                break;
	            }
	        }

	        return !denied;
	    });

	    // 添加“添加文件”的按钮，
	    uploader.addButton({
	        id: '#filePicker2',
	        label: '继续添加'
	    });

	    uploader.on('ready', function() {
	        window.uploader = uploader;
	    });

	    // 当有文件添加进来时执行，负责view的创建
	    function addFile( file ) {

	    	 var objUrl = window.URL || window.webkitURL;
		    var url = objUrl.createObjectURL(file);
		    var img = new Image();
		    img.src = url;
		    img.onload = function(){
		        if(img.naturalWidth != 2*img.naturalHeight){
		            alert_notice('请上传2比1的全景图');
		            return false;
		        }
		        objUrl.revokeObjectURL(url);
		    }

	    	return false;
	        var $li = $( '<li id="' + file.id + '">' +
	                '<p class="title">' + file.name + '</p>' +
	                '<p class="imgWrap"></p>'+
	                '<p class="progress"><span></span></p>' +
	                '</li>' ),

	            $btns = $('<div class="file-panel">' +
	                '<span class="cancel">删除</span>' +
	                '<span class="rotateRight">向右旋转</span>' +
	                '<span class="rotateLeft">向左旋转</span></div>').appendTo( $li ),
	            $prgress = $li.find('p.progress span'),
	            $wrap = $li.find( 'p.imgWrap' ),
	            $info = $('<p class="error"></p>'),

	            showError = function( code ) {
	                switch( code ) {
	                    case 'exceed_size':
	                        text = '文件大小超出';
	                        break;

	                    case 'interrupt':
	                        text = '上传暂停';
	                        break;

	                    default:
	                        text = '上传失败，请重试';
	                        break;
	                }

	                $info.text( text ).appendTo( $li );
	            };

	        if ( file.getStatus() === 'invalid' ) {
	            showError( file.statusText );
	        } else {
	            // @todo lazyload
	            $wrap.text( '预览中' );
	            uploader.makeThumb( file, function( error, src ) {
	                var img;

	                if ( error ) {
	                    $wrap.text( '不能预览' );
	                    return;
	                }

	                if( isSupportBase64 ) {
	                    img = $('<img src="'+src+'">');
	                    $wrap.empty().append( img );
	                } else {
	                    $.ajax('../../server/preview.php', {
	                        method: 'POST',
	                        data: src,
	                        dataType:'json'
	                    }).done(function( response ) {
	                        if (response.result) {
	                            img = $('<img src="'+response.result+'">');
	                            $wrap.empty().append( img );
	                        } else {
	                            $wrap.text("预览出错");
	                        }
	                    });
	                }
	            }, thumbnailWidth, thumbnailHeight );

	            percentages[ file.id ] = [ file.size, 0 ];
	            file.rotation = 0;
	        }

	        file.on('statuschange', function( cur, prev ) {
	            if ( prev === 'progress' ) {
	                $prgress.hide().width(0);
	            } else if ( prev === 'queued' ) {
	                $li.off( 'mouseenter mouseleave' );
	                $btns.remove();
	            }

	            // 成功
	            if ( cur === 'error' || cur === 'invalid' ) {
	                console.log( file.statusText );
	                showError( file.statusText );
	                percentages[ file.id ][ 1 ] = 1;
	            } else if ( cur === 'interrupt' ) {
	                showError( 'interrupt' );
	            } else if ( cur === 'queued' ) {
	                percentages[ file.id ][ 1 ] = 0;
	            } else if ( cur === 'progress' ) {
	                $info.remove();
	                $prgress.css('display', 'block');
	            } else if ( cur === 'complete' ) {
	                $li.append( '<span class="success"></span>' );
	            }

	            $li.removeClass( 'state-' + prev ).addClass( 'state-' + cur );
	        });

	        $li.on( 'mouseenter', function() {
	            $btns.stop().animate({height: 30});
	        });

	        $li.on( 'mouseleave', function() {
	            $btns.stop().animate({height: 0});
	        });

	        $btns.on( 'click', 'span', function() {
	            var index = $(this).index(),
	                deg;

	            switch ( index ) {
	                case 0:
	                    uploader.removeFile( file );
	                    return;

	                case 1:
	                    file.rotation += 90;
	                    break;

	                case 2:
	                    file.rotation -= 90;
	                    break;
	            }

	            if ( supportTransition ) {
	                deg = 'rotate(' + file.rotation + 'deg)';
	                $wrap.css({
	                    '-webkit-transform': deg,
	                    '-mos-transform': deg,
	                    '-o-transform': deg,
	                    'transform': deg
	                });
	            } else {
	                $wrap.css( 'filter', 'progid:DXImageTransform.Microsoft.BasicImage(rotation='+ (~~((file.rotation/90)%4 + 4)%4) +')');
	                // use jquery animate to rotation
	                // $({
	                //     rotation: rotation
	                // }).animate({
	                //     rotation: file.rotation
	                // }, {
	                //     easing: 'linear',
	                //     step: function( now ) {
	                //         now = now * Math.PI / 180;

	                //         var cos = Math.cos( now ),
	                //             sin = Math.sin( now );

	                //         $wrap.css( 'filter', "progid:DXImageTransform.Microsoft.Matrix(M11=" + cos + ",M12=" + (-sin) + ",M21=" + sin + ",M22=" + cos + ",SizingMethod='auto expand')");
	                //     }
	                // });
	            }


	        });

	        $li.appendTo( $queue );
	    }

	    // 负责view的销毁
	    function removeFile( file ) {
	        var $li = $('#'+file.id);

	        delete percentages[ file.id ];
	        updateTotalProgress();
	        $li.off().find('.file-panel').off().end().remove();
	    }

	    function updateTotalProgress() {
	        var loaded = 0,
	            total = 0,
	            spans = $progress.children(),
	            percent;

	        $.each( percentages, function( k, v ) {
	            total += v[ 0 ];
	            loaded += v[ 0 ] * v[ 1 ];
	        } );

	        percent = total ? loaded / total : 0;


	        spans.eq( 0 ).text( Math.round( percent * 100 ) + '%' );
	        spans.eq( 1 ).css( 'width', Math.round( percent * 100 ) + '%' );
	        updateStatus();
	    }

	    function updateStatus() {
	        var text = '', stats;

	        if ( state === 'ready' ) {
	            text = '选中' + fileCount + '张图片，共' +
	                    WebUploader.formatSize( fileSize ) + '。';
	        } else if ( state === 'confirm' ) {
	            stats = uploader.getStats();
	            if ( stats.uploadFailNum ) {
	                text = '已成功上传' + stats.successNum+ '张照片至XX相册，'+
	                    stats.uploadFailNum + '张照片上传失败，<a class="retry" href="#">重新上传</a>失败图片或<a class="ignore" href="#">忽略</a>'
	            }

	        } else {
	            stats = uploader.getStats();
	            text = '共' + fileCount + '张（' +
	                    WebUploader.formatSize( fileSize )  +
	                    '），已上传' + stats.successNum + '张';

	            if ( stats.uploadFailNum ) {
	                text += '，失败' + stats.uploadFailNum + '张';
	            }
	        }

	        $info.html( text );
	    }

	    function setState( val ) {
	        var file, stats;

	        if ( val === state ) {
	            return;
	        }

	        $upload.removeClass( 'state-' + state );
	        $upload.addClass( 'state-' + val );
	        state = val;

	        switch ( state ) {
	            case 'pedding':
	                $placeHolder.removeClass( 'element-invisible' );
	                $queue.hide();
	                $statusBar.addClass( 'element-invisible' );
	                uploader.refresh();
	                break;

	            case 'ready':
	                $placeHolder.addClass( 'element-invisible' );
	                $( '#filePicker2' ).removeClass( 'element-invisible');
	                $queue.show();
	                $statusBar.removeClass('element-invisible');
	                uploader.refresh();
	                break;

	            case 'uploading':
	                $( '#filePicker2' ).addClass( 'element-invisible' );
	                $progress.show();
	                $upload.text( '暂停上传' );
	                break;

	            case 'paused':
	                $progress.show();
	                $upload.text( '继续上传' );
	                break;

	            case 'confirm':
	                $progress.hide();
	                $( '#filePicker2' ).removeClass( 'element-invisible' );
	                $upload.text( '开始上传' );

	                stats = uploader.getStats();
	                if ( stats.successNum && !stats.uploadFailNum ) {
	                    setState( 'finish' );
	                    return;
	                }
	                break;
	            case 'finish':
	                stats = uploader.getStats();
	                if ( stats.successNum ) {
	                    alert( '上传成功' );
	                } else {
	                    // 没有成功的图片，重设
	                    state = 'done';
	                    location.reload();
	                }
	                break;
	        }

	        updateStatus();
	    }

	    uploader.onUploadProgress = function( file, percentage ) {
	        var $li = $('#'+file.id),
	            $percent = $li.find('.progress span');

	        $percent.css( 'width', percentage * 100 + '%' );
	        percentages[ file.id ][ 1 ] = percentage;
	        updateTotalProgress();
	    };

	    uploader.onFileQueued = function( file ) {
	        fileCount++;
	        fileSize += file.size;

	        if ( fileCount === 1 ) {
	            $placeHolder.addClass( 'element-invisible' );
	            $statusBar.show();
	        }
	       
	        addFile( file );
	        setState( 'ready' );
	        updateTotalProgress();
	    };
	    uploader.onUploadStart = function(file){
	    	console.log(file);
			var key = qn_token.prefix+generic_name()+'.'+file.ext;
			$("#"+file.id).data("imgname",file.name);
			$("#"+file.id).data("imgsrc",key);
		    uploader.option('formData',{
		            key:key
		    });
	    }
	    uploader.onFileDequeued = function( file ) {
	        fileCount--;
	        fileSize -= file.size;

	        if ( !fileCount ) {
	            setState( 'pedding' );
	        }
	        removeFile( file );
	        updateTotalProgress();
	    };

	    uploader.on( 'all', function( type ) {
	        var stats;
	        switch( type ) {
	            case 'uploadFinished':
	                setState( 'confirm' );
	                break;

	            case 'startUpload':
	                setState( 'uploading' );
	                break;

	            case 'stopUpload':
	                setState( 'paused' );
	                break;

	        }
	    });

	    uploader.onError = function( code ) {
	        alert( 'Eroor: ' + code );
	    };

	    $upload.on('click', function() {
	        if ( $(this).hasClass( 'disabled' ) ) {
	            return false;
	        }

	        if ( state === 'ready' ) {
	            uploader.upload();
	        } else if ( state === 'paused' ) {
	            uploader.upload();
	        } else if ( state === 'uploading' ) {
	            uploader.stop();
	        }
	    });

	    $info.on( 'click', '.retry', function() {
	        uploader.retry();
	    } );

	    $info.on( 'click', '.ignore', function() {
	        alert( 'todo' );
	    } );

	    $upload.addClass( 'state-' + state );
	    updateTotalProgress();
	});
























	$("#imgUpload").fileinput({
	    //elPreviewContainer:'#lyftest',
	    //elPreviewImage:'#lyftest',
	    language: 'zh',
	    showUpload: false,
	    showRemove: false,
	    showCancel: false,
	    showUploadedThumbs:false,
	    maxFileCount: 10,
	    //maxFilePreviewSize:200000,
	    //resizeImage:true,
	    //maxImageWidth: 200,
	    previewFileType: "image",
	    //allowedFileTypes:['image'],
	    allowedFileExtensions: ["jpg","tif", "tiff"],
	    //previewClass:"bg-warning",
	    msgInvalidFileExtension: '不支持文件类型"{name}"。只支持扩展名为"{extensions}"的文件。',
	    browseClass: "btn btn-primary",
	    browseLabel: "选择本地全景图片",
	    browseIcon: "<i class=\"icon icon-picture\"></i> ",
	    removeClass: "btn btn-danger",
	    removeLabel: "删除",
	    removeIcon: "<i class=\"icon icon-trash\"></i> ",
	    uploadUrl: up_url,
	    uploadAsync: true,
	    previewSettings: {
	        image: {width: "auto", height: "100px"}
	    },
	    fileActionSettings: {},
	    // layoutTemplates: {
	    //     main1:'{preview}\n' +
	    //     '<div class="kv-upload-progress hide"></div>\n' +
	    //     '<div class="input-group {class}">\n' +
	    //         //'   {caption}\n' +
	    //     '<div class="input-group-btn text-right">\n' +
	    //     '       {remove}\n' +
	    //     '       {cancel}\n' +
	    //     '       {browse}\n' +
	    //     '<div class="btn btn-primary" style="margin-left:10px;" tabindex="500"><i class="icon icon-cube"></i>&nbsp; <span class="">选择六面体图片</span><input type="file" class="btn-file-custom" accept="image/png,image/jpeg" multiple name="sixImg" id="sixUpload" onchange="sixImgChoose()"></div>'+
	    //     '   </div>\n' +
	    //     '</div>',
	    //     actions: '<div class="file-actions">\n' +
	    //     '    <div class="file-footer-buttons">\n' +
	    //     '        {delete}' +
	    //     '    </div>\n' +
	    //     '    <div class="file-upload-indicator" tabindex="-1" title="{indicatorTitle}">{indicator}</div>\n' +
	    //     '    <div class="clearfix"></div>\n' +
	    //     '</div>'
     //    },
	    dropZoneTitle: "拖拽一组/单幅图片或点击下面按钮上传",
	     // browseOnZoneClick: true,
	    textEncoding: "UTF-8",
	   uploadExtraData: get_token
	}).on('filepreajax',function(event,previewId,index){
		var files =  $('#imgUpload').fileinput('getFileStack');
		//构造每次请求的key
		var extraData = $("#imgUpload").fileinput('uploadExtraData');
		var name =files[index].name;
		extraData.key = qn_token.prefix+generic_name()+name.substr(name.lastIndexOf("."));
		$("#"+previewId).data("imgname",name);
		$("#"+previewId).data("imgsrc",extraData.key);

		// if(qn_token.policy){
		// 	extraData.policy = qn_token.policy;
		// 	extraData.accessid = qn_token.accessid;
		// 	extraData.host = qn_token.host;
		// 	extraData.signature = qn_token.signature;
		// 	extraData.success_action_status = "200";
		// }
	}).on("fileuploaded",function(event, data, previewId, index){
		//单个上传成功，保存key
	}).on("filebatchuploadcomplete",function(){
		//全部上传成功
		qn_token = {};
		commit();
		
	}).on('fileloaded', function(event, file, previewId, index, reader) {
	    if(file.name.substr(file.name.lastIndexOf("."))=='.tiff' || file.name.substr(file.name.lastIndexOf("."))=='.tif'){
	        var tiff = new Tiff({buffer: reader.result});
	        var width = tiff.width();
	        var height = tiff.height();
	        checkImgWidthAndHeight(width,height,previewId);
	        return ;
	    }
	    var objUrl = window.URL || window.webkitURL;
	    var url = objUrl.createObjectURL(file);
	    var img = new Image();
	    img.src = url;
	    img.onload = function(){
	        checkImgWidthAndHeight(img.naturalWidth,img.naturalHeight,previewId);
	        objUrl.revokeObjectURL(url);
	    }
});     

$("#publish_img").click(function(){
	$(".input-group").removeClass("has-error");
	params.pname = $.trim($("#pname").val());
	if (params.pname =="") {
		showerr("项目名称不能为空",$("#pname"));
		return false;
	}
	params.pic_tags = $("#pic_chosen").val();
	if (params.pic_tags == null) {
		showerr("请选择标签");
		return false;
	}
	var files =  $('#imgUpload').fileinput('getFileStack');
	if(files.length<=0){
		$('#imgUpload').fileinput('_showError','请先上传文件');
		return false;
	}
	$(this).addClass("disabled");
	$("#imgUpload").fileinput("upload");
	//分类
	// get_token();
	// var timer = setInterval(function(){
	// 		if (qn_token.prefix) {
	// 			clearInterval(timer);
	// 			$(this).addClass("disabled");
	// 			$("#imgUpload").fileinput("upload");
	// 		}
	// },1000);
})

$("#publish_video").click(function(){
	videoParams.vname = $.trim($("#vname").val());
	if (videoParams.vname==""||videoParams.vname.length>30) {
		alert_notice("视频名称在1到30位之间");
		return false;
	}
	videoParams.video_tags = $("#video_chosen").val();
	if (videoParams.video_tags == null) {
		alert_notice("请选择标签");
		return false;
	}
	videoParams.profile = $("#profile").val();
	video_up.start();
})


})

var cubeGroupIdx = 0;
var cubeGroupFiles = {};
function sixImgChoose(){
    var files = $('#sixUpload')[0].files;
    $(files).each(function(idx){
        checkImgWidthAndHeight1v1(this);
    });
    if(!_alreadyShowError){
        var sixImgGroups = {};
        if(files.length >= 6){
            $(files).each(function(idx){
                var fullName = this.name.substring(0,this.name.lastIndexOf('.'));
                var nameArr = fullName.split('_');
                if(nameArr.length == 2){
                    if(!sixImgGroups[nameArr[0]]){
                        sixImgGroups[nameArr[0]] = {};
                    }
                    sixImgGroups[nameArr[0]][nameArr[1]] = this;
                }else{
                     if(!sixImgGroups['']){
                        sixImgGroups[''] = {};
                    }
                    sixImgGroups[''][nameArr[0]] = this;
                }
            });
            //校验每个分组中的六个面命名是否规范
            var validateMsg = '';
            $.each(sixImgGroups,function(groupName,groupObj){
                var size = Object.getOwnPropertyNames(groupObj).length;
                if(size == 6){
                    if(validateCubeImgs(groupObj)){
                        //var cubeGroupIdx = Object.getOwnPropertyNames($('#imgUpload').fileinput('_getCubeFiles')).length;
                        var cubeGroupId = 'cube'+cubeGroupIdx++;
                        $('#imgUpload').fileinput('_appendCubeFiles',cubeGroupId,groupObj);
                        addCubePreview(cubeGroupId,groupName,groupObj);
                    }else{
                        validateMsg += '组（'+groupName+')的图片方位命名不符合规范，请参考命名规范。\n';
                        return false;
                    }
                }else{
                    validateMsg += '组（'+groupName+')的图片数量不为6，当前数量'+size+'张\n';
                    return false;
                }
            });
            if(validateMsg != ''){
                $.zui.messager.show(validateMsg, {placement: 'center',  time: '5000', icon: 'exclamation-sign'});
            }
        }else{
            $.zui.messager.show('需要6张1:1图片组成六面体，当前只有'+files.length+'张', {placement: 'center',  time: '5000', icon: 'exclamation-sign'});
        }
    }
}
var _alreadyShowError = false;
function checkImgWidthAndHeight1v1(file){
    var objUrl = window.URL || window.webkitURL;
    var url = objUrl.createObjectURL(file);
    var img = new Image();
    img.onload = function(){
        if(img.naturalWidth != img.naturalHeight){
            //$('#imgUpload').fileinput('_showError','六面体全景图片必须为1:1比例');
            $.zui.messager.show('六面体全景图片必须为1:1比例', {placement: 'center',  time: '5000', icon: 'exclamation-sign'});
            _alreadyShowError = true;
            objUrl.revokeObjectURL(url);
        }else{
            _alreadyShowError = false;
        }
    };
    img.src = url;
}
function validateCubeImgs(groupObj){
    if(groupObj.f && groupObj.b && groupObj.l && groupObj.r && groupObj.u && groupObj.d){
        return true;
    }
    return false;
}
function addCubePreview(cubeGroupId,groupName,groupObj){
    var html = '';
    var imgFile = groupObj.f ? groupObj.f : '';
    var objUrl = window.URL || window.webkitURL;
    var url = objUrl.createObjectURL(imgFile);
    html += '<div class="file-preview-frame" data-fileindex="1" data-cubeid="'+cubeGroupId+'">'+
        '<img class="file-preview-image" style="width:auto;height:100px;" alt="'+groupName+'"' +
        ' title="'+groupName+'" src="'+url+'">'+
        '<div class="file-thumbnail-footer">'+
        '<div class="file-footer-caption" title="'+groupName+'">'+groupName+'</div>'+
        '<div class="file-thumb-progress hide">'+
        '<div class="progress">'+
        '<div class="progress-bar progress-bar-success progress-bar-striped active" style="width:0%;" aria-valuemax="100" aria-valuemin="0" aria-valuenow="0" role="progressbar"> 0% </div>'+
        '</div>'+
        '</div>'+
        '<div class="file-actions">'+
        '<div class="file-footer-buttons">'+
        '<button class="kv-file-remove2 btn btn-xs btn-default" title="删除文件" type="button">'+
        '<i class="icon icon-trash text-danger"></i>'+
        '</button>'+
        '</div>'+
        '<div class="clearfix"></div>'+
        '</div>'+
        '</div>'+
        '</div>';
    var files = $('#imgUpload').fileinput('getFileStack');
    if (files.length == 0){
        $(".file-preview").find(".file-drop-zone-title").remove();
        if($("#myContainer").length == 0){
            $(".file-preview").find(".file-preview-thumbnails").before("<div id = 'myContainer'></div>");
        }
        $("#myContainer").append(html);
    }else{
        $(".file-preview").find(".file-preview-thumbnails").append(html);
    }
}

function showCubeError(msg){
    $.zui.messager.show('六面体全景图片必须为1:1比例', {placement: 'center', type: 'danger', time: '5000', icon: 'exclamation-sign'});

}

function checkImgWidthAndHeight(width,height,previewId){
	if(width != 2*height){
	    $('#imgUpload').fileinput('_showError','球面全景图片必须为2:1比例');
	    return false;
	}
	return true;
}
function get_token() {
	if (qn_token.prefix) {
		return qn_token;
	}
	  $.ajax({
	  	url:'/get_token.php',
	  	method:'post',
	  	async: false,
	  	data:{'act':"qj_img"},
	  	success:function(res){
			var obj = eval ("(" + res + ")");
			qn_token.prefix= obj.prefix;
			if (obj.token) 
				qn_token.token = obj.token;
			else if(obj.policy){
				qn_token.policy = obj.policy;
				qn_token.OSSAccessKeyId = obj.accessid;
				qn_token.host = obj.host;
				qn_token.signature = obj.signature;
			}
	 	return qn_token;
	  	}
	  })

}
function generic_name() {
　　var $chars = 'abcdefghijklmnopqrstwxyz0123456789';  
　　var maxPos = $chars.length;
　　var pwd = '';
　　for (i = 0; i < 3; i++) {
　　　　pwd += $chars.charAt(Math.floor(Math.random() * maxPos));
　　}
　　return new Date().getTime()+pwd;
}
function commit(){
	$("#myModal").modal({
		  keyboard : false,
		  show     : true,
		  position : 'center',
		  backdrop : 'static'
		});
	params.imgs=new Array();
	 $(".file-preview-success").each(function(){
	 	var img={};
	 	img.imgsrc = $(this).data("imgsrc");
	 	img.imgname = $(this).data("imgname");
	 	params.imgs.push(img);
	 });
	 params.atlas_id = $("#atlas").val();
	 params.allow_recomm = $("#allow_recomm").is(':checked')?1:0;
	 var heartTimer = setInterval(function(){
				$.get("/add/pic",{'act':'keep_alive'});
		},60000);
	 $.post("/add/pic",{
	 	"act":"doAdd",
	 	"params":JSON.stringify(params)
	 },function(data){
	 	clearInterval(heartTimer);
	 	var data = eval("("+data+")");
	 	if(data.flag){
	 		$("#myModal .modal-body").html("<p class='text-success'><img src='/static/images/right.gif'>　图片上传成功</p>");
	 	}else{
	 		$("#myModal .modal-body").html("<p class='text-danger'>"+data.msg+"</p>");
	 	}
	 	$("#myModal .modal-footer button").show();
	 	$("#publish_img").removeClass("disabled");
	 })

}
function showerr(content,obj){
	alert_notice(content,'default','top');
	if(obj!=null){
		$(obj).parent(".input-group").addClass("has-error");
	}
}