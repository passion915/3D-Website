$(function(){
	// plugins_init_function.push(map_plane_init);
	$('#map_palne_cover').on('click', function (e) {
	    chooseMediaResCallBack = resetMapPlaneCover;
	});
})
// function map_plane_init(data){
// 	var map_plane_data = panoConfig.custom_logo;
// }
function open_plane_map_annotation(){
	$.post('/edit/pic',{
		'act':'get_position',
		'pid':pk_works_main
	},function(res){
		showWindow(res.position);
		$("#PositionModal #markerCover").attr('src',res.position_cover);
		$("#PositionModal #map_plane_title").val(res.position_title);
		$("#PositionModal #map_plane_desc").html(res.position_desc);

	},'JSON')
	
}
function resetMapPlaneCover(data){
	$("#PositionModal #markerCover").attr('src',data.src);
}