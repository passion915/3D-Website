<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="robots" content="noarchive">
<link href="/{$_lang.admin_path}/template/css/public.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="/{$_lang.admin_path}/template/js/jquery.min.js"></script>
<title>{$title}-{$_lang.title}</title>
</head>
<body>

<div id="dcWrap">

{if $module=='login'}
   {include file="lib/login.lbi"}
{else}
 <div id="dcHead">
 <div id="head">
  <div class="logo"><a href="/{$_lang.admin_path}/">{$_lang.title}</a></div>
  <div class="nav">
   <ul>
    <li class="M"><a href="JavaScript:void(0);" class="topAdd">新建</a>
     <div class="drop mTopad"><a href="/{$_lang.admin_path}/?m=user&act=profile">会员</a> <a href="/{$_lang.admin_path}/?m=tag&act=profile">标签</a></div>
    </li>
    <li><a href="/" target="_blank">查看站点</a></li>
    <li><a onclick="clear_cache(this)" href="javascript:;">清除缓存</a></li>
	{if !$_lang.customvip}<li {if $_lang.customvip}class="noRight"{/if}><a target="_blank" href="http://www.krpano100.com/docs.html">使用文档</a></li>{/if}
    {if !$_lang.customvip}<li class="noRight"><a target="_blank" href="http://www.krpano100.com">krpano100官网</a></li>{/if}
   </ul>
  
   <ul class="navRight">
    <li class="M noLeft"><a href="JavaScript:void(0);">您好，{$admin.admin_name}</a>
     <div class="drop mUser">
      <a href="/{$_lang.admin_path}/?m=passwd">修改登录密码</a>
     </div>
    </li>
    <li class="noRight"><a href="/{$_lang.admin_path}/?m=logout">退出</a></li>
   </ul>
  </div>
 </div>
</div>
<!-- Head 结束 --> 
<div id="dcLeft">
 <div id="menu">
 <ul class="top">
  <li><a href="/{$_lang.admin_path}/"><i class="home"></i><em>管理首页</em></a></li>
 </ul>
 <ul>
  <li {if $module=='system'}class="cur"{/if}><a href="/{$_lang.admin_path}/?m=system"><i class="system"></i><em>系统设置</em></a></li>
  <li {if $module=='plugin'}class="cur"{/if}><a href="/{$_lang.admin_path}/?m=plugin"><i class="case"></i><em>插件管理</em></a></li>
  <li {if $module=='theme'}class="cur"{/if}><a href="/{$_lang.admin_path}/?m=theme"><i class="theme"></i><em>设置模板</em></a></li>
 </ul>
 <ul>
  <li {if $module=='slide' || $module=='ad'}class="cur"{/if}><a href="/{$_lang.admin_path}/?m=slide"><i class="show"></i><em>广告管理</em></a></li>  
  <li {if $module=='voice' || $module=='material'}class="cur"{/if}><a href="/{$_lang.admin_path}/?m=material"><i class="link"></i><em>素材管理</em></a></li>
 </ul>
 <ul>
  <li {if $module=='user'}class="cur"{/if}><a href="/{$_lang.admin_path}/?m=user"><i class="user"></i><em>会员管理</em></a></li>
  <li {if $module=='level'}class="cur"{/if}><a href="/{$_lang.admin_path}/?m=level"><i class="nav"></i><em>组与权限</em></a></li>
 </ul>
 <ul>
  <li {if $module=='pic' || $module=='video'}class="cur"{/if}><a href="/{$_lang.admin_path}/?m=pic"><i class="productCat"></i><em>全景项目</em></a></li>
  <li {if $module=='articlecat' ||  $module=='article'}class="cur"{/if}><a href="/{$_lang.admin_path}/?m=article"><i class="article"></i><em>文章管理</em></a></li>
 </ul>
 <ul> 
  <li {if $module=='tag'}class="cur"{/if}><a href="/{$_lang.admin_path}/?m=tag"><i class="page"></i><em>标签管理</em></a></li>
  <li {if $module=='region'}class="cur"{/if}><a href="/{$_lang.admin_path}/?m=region"><i class="plugin"></i><em>地区管理</em></a></li>
  <li {if $module=='property'}class="cur"{/if}><a href="/{$_lang.admin_path}/?m=property"><i class="articleCat"></i><em>属性管理</em></a></li>
 </ul>
 <ul> 
  <li {if $module=='position'}class="cur"{/if}><a href="/{$_lang.admin_path}/?m=position"><i class="page"></i><em>地图管理</em></a></li>
 </ul>
 <ul class="bot">
  <li {if $module=='upgrade'}class="cur"{/if}><a href="/{$_lang.admin_path}/?m=upgrade&act=step_1"><i class="downloadCat"></i><em>自动升级</em></a></li>
 </ul>
 </div>
</div>
<div id="dcMain">
   <!-- 当前位置 -->
<div id="urHere">{$_lang.title} 管理中心{if !empty($nav)}<b>></b><strong>{$nav}</strong>{/if} </div>   

<div {if $module=='slide' || $module=='ad' || $module=='material' || $module=='voice' || $module=='articlecat' || $module=='article' || $module=='pic' || $module=='video'}id="mobileBox"{/if}>
	{if $module=='slide' || $module=='ad'}
		<div id="mMenu">
			<h3>广告管理</h3>
			<ul>
			 <li><a {if $module=='slide'}class="cur"{/if} href="/{$_lang.admin_path}/?m=slide">首页焦点图</a></li>
  			 <li><a {if $module=='ad'}class="cur"{/if} href="/{$_lang.admin_path}/?m=ad">图文广告</a></li>
			</ul>
		</div>
	{/if}
	
	{if $module=='material' || $module=='voice'}
		<div id="mMenu">
			<h3>素材管理</h3>
			<ul>
			 <li><a {if $module=='material'}class="cur"{/if} href="/{$_lang.admin_path}/?m=material">图片素材</a></li>
             <li><a {if $module=='voice'}class="cur"{/if} href="/{$_lang.admin_path}/?m=voice">音频素材</a></li>
			</ul>
		</div>
	{/if}
	
	{if $module=='articlecat' || $module=='article'}
		<div id="mMenu">
			<h3>文章管理</h3>
			<ul>
			 <li><a {if $module=='articlecat'}class="cur"{/if} href="/{$_lang.admin_path}/?m=articlecat">文章分类</a></li>
             <li><a {if $module=='article'}class="cur"{/if} href="/{$_lang.admin_path}/?m=article">文章管理</a></li>
			</ul>
		</div>
	{/if}
	
	{if $module=='pic' || $module=='video'}
		<div id="mMenu">
			<h3>全景项目</h3>
			<ul>
			 <li><a {if $module=='pic'}class="cur"{/if} href="/{$_lang.admin_path}/?m=pic">全景图片</a></li>
  			 <li><a {if $module=='video'}class="cur"{/if} href="/{$_lang.admin_path}/?m=video">全景视频</a></li>
			</ul>
		</div>
	{/if}
	
	<div id="mMain">
		<div class="mainBox">
			{include file="lib/{$module}.lbi"}
		</div>
	</div>
</div>
</div>
{/if}

<div class="clear"></div>
<div id="dcFooter">
 <div id="footer">
  <div class="line"></div>
  <ul>
   Copyright&copy;2015　{$_lang.title}
  </ul>
 </div>
</div><!-- Footer 结束 -->
<div class="clear"></div> 
</div>

<script type="text/javascript" src="/{$_lang.admin_path}/template/js/global.js"></script>
<script type="text/javascript" src="/{$_lang.admin_path}/template/js/jquery.tab.js"></script>
<script type="text/javascript" src="/{$_lang.admin_path}/template/js/common.js"></script>
<script type="text/javascript" src="/static/js/jquery.form.js"></script>
<script type="text/javascript" src="/{$_lang.admin_path}/template/js/jquery.form.submit.js"></script>
<script type="text/javascript" src="/{$_lang.admin_path}/template/js/calendar.js"></script>
<script>
function clear_cache(ele) {
    if(confirm("该操作不会删除/temp/krpano目录下生成的临时切图文件，如果要删除请使用ftp手动删除！")){
        $(ele).css({
		            "background-image":"url(/static/images/tm_loading.gif)",
					"background-position":"left center ",
					"background-repeat":"no-repeat",
					"background-size":"15px",
					"padding-left":"20px",
				  });
		$.get('/{$_lang.admin_path}/?m=clear_cache',{
        },function(res){
          if (res.status==1) {
            $(ele).css({
			            "background-image":"none",
						"padding-left":"15px",
					  });
          }
        },'json');
    }
}
</script>
</body>
</html>