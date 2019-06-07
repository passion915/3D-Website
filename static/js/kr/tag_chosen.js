function init_tag_chose(id,type){
	$("#"+id+" .search-field input").focus(function(){
		$(this).val("");
		$(this).parent().parent().siblings(".tag_chose").show();
	})
	$(document).on('mousedown', function(e) {
          if($(e.target).closest('#'+id).length === 0) {
              $("#"+id+" .tag_chose").hide();
          }
	 });
	$("#"+id+" .tag_chose .tag_list").on('click','span',function(){
		var tagid = $(this).data("tagid");
		if ($(this).hasClass("tag_active")) {
			$(this).removeClass("tag_active");
			$("#"+id+" .chosen-choices .search-choice").each(function(){
				var _tid = $(this).data("tagid");
				if (_tid==tagid) {
					$(this).remove();
				}
			});
			count_tag(id);
		}else{
			if (count_tag(id)) {
				$(this).addClass("tag_active");
				$("#"+id+" .search-field input").css("width","25px");
				$("#"+id+" .chosen-choices .search-field").before('<li class="search-choice" data-tagid="'+tagid+'"><span>'+$(this).data('tagname')+'</span><a class="search-choice-close" data-option-array-index="'+tagid+'"></a></li>');
				count_tag(id);
			}
		}
	});
	$("#"+id+" .chosen-choices").on("click",".search-choice .search-choice-close",function(){
		var _tid = $(this).data('option-array-index');
		$(this).parent().remove();
		$("#"+id+" .tag_chose .tag_list span").each(function(){
			var span_tid = $(this).data("tagid");
			if (_tid==span_tid) {
				$(this).removeClass("tag_active");
				count_tag(id);
			}
		})
	});
	$("#"+id+" .tag_chose .custom_tag_confirm").click(function(){
			var tag_name = $.trim($("#"+id+" .tag_chose .custom_tag_input").val());
			if (tag_name==""||tag_name.length>10) {
				alert_notice("请输入长度10个字符以内的标签");
				return false;
			}
			var obj = alert_notice('正在添加标签','success');
			$.post('/member/tag',{
				'name':tag_name,
				'type':type,
				'act':'add'
			},function(res){
				obj.hide();
				if (res.status == 1) {
					$("#"+id+" .chosen-choices .search-field").before('<li class="search-choice" data-tagid="c_'+res.tid+'"><span>'+tag_name+'</span><a class="search-choice-close" data-option-array-index="c_'+res.tid+'"></a></li>');
					$("#"+id+" .tag_chose .tag_list").append('<span class="tag tag_active" data-tagid="c_'+res.tid+'" data-tagname="'+tag_name+'">'+tag_name+'<a class="custom_tag_remove" data-tagid="'+res.tid+'">x</a></span>');
					$("#"+id+" .tag_chose .custom_tag_input").val("");
					count_tag(id);
				}else{
					alert_notice(res.msg);
				}
			},'json')
			
	});
	$("#"+id+" .tag_chose .tag_list").on('click','span .custom_tag_remove',function(){
			var obj = alert_notice("正在执行删除");
			var tid = $(this).data('tagid');
			$.post('/member/tag',{
				'tid':tid,
				'act':'delete'
			},function(res){
				obj.hide();
				if(res.status == 1){
					tid = 'c_'+tid;
					$("#"+id+" .chosen-choices .search-choice").each(function(){
						var _tid = $(this).data("tagid");
						if (_tid==tid) {
							$(this).remove();
						}
					});
					$("#"+id+" .tag_chose .tag_list span").each(function(){
						var span_tid = $(this).data("tagid");
						if (span_tid==tid) {
							$(this).remove();
						}
					})
					alert_notice('删除成功','success');
				}else{
					alert_notice(res.msg);
				}
			},'json')
			count_tag(id);
	});
}
function count_tag(id){
	var length = $("#"+id+" .chosen-choices .search-choice").length;
	if (length<3) {
		$("#"+id+" .tag_chose .custom_tag_confirm").removeClass("disabled");
		return true;
	}else{
		$("#"+id+" .tag_chose .custom_tag_confirm").addClass("disabled");
		return false;
	}
}
function get_chose_tags(id){
	var tags = {};
	tags.default = new Array();
	tags.custom = new Array();
	$("#"+id+" .chosen-choices .search-choice").each(function(){
		var tid = $(this).data("tagid");
		var arr = tid.split('_');
		arr[0]=='d'?tags.default.push(arr[1]):tags.custom.push(arr[1]);
	});
	return tags;
}
