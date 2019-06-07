$(function(){
    $('.copyable').zclip({
        path: '/static/zclip/ZeroClipboard.swf',
        copy: function(){//复制内容
            console.log( $(this).parent().prev().find('input').val());
            return $(this).parent().prev().find('input').val();
        },
        afterCopy: function(){//复制成功
            $.zui.messager.show('复制成功', {placement: 'center', type: 'success', time: '3000', icon: 'check'});
        }
    });
    $('[data-toggle="tooltip"]').tooltip();
    // $("#video_chosen").chosen({
    //      no_results_text: "没有找到",
    //     max_selected_options:3,
    //     width:"100%"
    // });

    //初始化标签框
    init_tag_chose("video_edit_tag_chosen",2);
    var chose_list_html = "";
    var chose_html ="";

    for(var i =0 ; i<tag_list.length;i++){
        var flag = false;
        for(var j=0 ;j<tags.length;j++){
            if (tags[j].tag_id==tag_list[i].id) {
                tags[j].name = tag_list[i].name;
                flag = true;
                break;
            }
        }
        chose_list_html+='<span class="tag '+(flag?"tag_active":"")+'" data-tagid="d_'+tag_list[i].id+'" data-tagname="'+tag_list[i].name+'">'+tag_list[i].name+'</span>'
    }
    for(var i=0 ;i<tags.length;i++){
        chose_html+='<li class="search-choice" data-tagid="d_'+tags[i].tag_id+'"><span>'+tags[i].name+'</span><a class="search-choice-close" data-option-array-index="d_'+tags[i].tag_id+'"></a></li>'
    }

    for(var i =0 ; i<custom_tag_list.length;i++){
        var flag = false;
        for(var j=0 ;j<custom_tags.length;j++){
            if (custom_tags[j].tid==custom_tag_list[i].id) {
                custom_tags[j].name = custom_tag_list[i].name;
                flag = true;
                break;
            }
        }
        chose_list_html+='<span class="tag '+(flag?"tag_active":"")+'" data-tagid="c_'+custom_tag_list[i].id+'" data-tagname="'+custom_tag_list[i].name+'">'+custom_tag_list[i].name+
                        '<a class="custom_tag_remove" data-tagid="'+custom_tag_list[i].id+'">x</a></span>'
    }
    for(var i=0 ;i<custom_tags.length;i++){
        chose_html+='<li class="search-choice" data-tagid="c_'+custom_tags[i].tid+'"><span>'+custom_tags[i].name+'</span><a class="search-choice-close" data-option-array-index="c_'+custom_tags[i].tid+'"></a></li>'
    }

    $("#video_edit_tag_chosen .tag_list").html(chose_list_html);
    $("#video_edit_tag_chosen .chosen-choices .search-field").before(chose_html);

})

function delete_source(index){
        bootbox.confirm({
        message:"确定要删除一个分类吗?",
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
               $("#source_"+index).remove();
            }
        }
    });
        
}
function update_videos(vid){
    // $("#save_btn").attr({"disabled":"disabled"});
    var sources = new Array();
    var flag = false;

    var tags = get_chose_tags("video_edit_tag_chosen");
    // var tags = $("#video_chosen").val();
    if (tags.default.length==0 || tags.default.length>3) {
         alert_notice("请选择3个以内的标签");
        return false;
    }
    $("#source_wrap input").each(function(){
        var source = {};
        source.location = $(this).data("location");
        source.progressive = $(this).val();
        if(!source.progressive){
            $(this).parent("div").addClass("has-error");
            flag = true;
            return false;
        }
        sources.push(source);
    });
    if(flag){
        alert_notice("请输入清晰度");
        return false;
    }

    if (sources.length==0) {
        alert_notice("不能没有视频，请重新刷新页面");
        return false;
    }
    $.post("/edit/video",
        {
           "act" : "update",
           "vname": $.trim($("#vname").val()),
           "profile":$.trim($("#profile").val()),
           "sources":JSON.stringify(sources),
           "tags":JSON.stringify(tags),
           "flag_publish":$("#flag_publish").is(':checked')?1:0,
           "thumbpath":$("#thumbpath").attr('src'),
           "vid":vid
        },function(result){
             // $("#save_btn").removeAttr("disabled");
            result = eval("("+result+")");
            if (result.flag) {
                alert_notice("修改成功","success");
            }else{
                alert_notice(result.msg);
            }
        })
}
function preview_videos(vid){
    window.open(cdn_host+"video/play.html?vid="+vid);
}

