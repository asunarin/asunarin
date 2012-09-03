function nocache(){
	return ("core.php?"+Math.random());
}
function hideSMBox(){
	$("#smallBox").slideUp("fast");
}
function hideMenu(e){
	var button=e.button;
	if(button<=1)  $("#menu").hide("fast");
}


/* UI */
$(function(){

		   var appVersion=navigator.appVersion;
		   var appName=navigator.appName;
		   var loadingBar=$("<div><img src='images/uploading.gif' /></div>").attr("id","loading").appendTo("body").hide();
	if(appVersion.indexOf("MSIE 7.0")>-1 || appName.indexOf("Netscape")>-1){
	  loadingBar.css("position","fixed");   
	}else{
   	   loadingBar.css("position","absolute");   
   }
   $("<div></div>").attr("id","mainMenu").appendTo("body").hide();   
	$("<div></div>").attr("id","menu").appendTo("body").hide();   
	$("<div></div>").attr("id","smallBox").appendTo("body").hide();   
	$("<div></div>").attr("id","overlay").appendTo("body").hide();
	$("<div></div>").attr("id","frontpage").appendTo("body").hide();

var ajaxCount=0;
 $("#loading").bind("ajaxSend", function(){
	if(ajaxCount==0)	{
	   	$(this).fadeIn(100);
	}
	ajaxCount+=1;
 }).bind("ajaxComplete", function(){
	 ajaxCount-=1;
	 if(ajaxCount==0){
		setTimeout(function(){
							$("#loading").fadeOut(100);
							},300);
	 }
 });
 
 
	$("#overlay").click(function(){
									UI.hideLayer();
								 	});

	window.onscroll=window.onresize=function(){UI.ajustFP.call(UI)};
	$("#iptAddr").keypress(function(e){
									if(e.keyCode==13)doAction("Go");
									})
	
		$.tKey.addKey("x",function(){
		doAction("GoUp");
		}); 
		$.tKey.addKey("v",function(){
		doAction("Paste");
		}); 
		$.tKey.addKey("f",function(){
		doAction('addFav')
		}); 
	
	$("#btnMenu").mouseover(function showMainMenu(e){
				 var  html="<a href=javascript:void(0) onclick=doAction('newDir',event) >新建目录</a><a href=javascript:void(0) onclick=doAction('newFile',event) >新建文件</a><div class='hr'></div>"+
				  "<a href=javascript:doAction('Paste');>粘贴(Z+V)</a><a href=javascript:doAction('GoUp') >向上(Z+X)</a><div class='hr'></div>"+
				  "<a href=javascript:doAction('addFav') >加入收藏(Z+F)</a><div class='hr'></div><a href=javascript:doAction('exit') >安全退出</a>";
				  $("#menu").html(html).css("left",e.pageX)
					.css("top",e.pageY).show(500);
					});
	Default();
});
 
 
var UI={
	drawOverlay:function(){
		$("#overlay").height($("body").height())
		.animate({ 
			height: "show",
			opacity: 0.4
	     }, 500 );
	},
	ajustFP: function(){
		if($("#frontpage").css("display")=="block"){
			this.rePosition();
		}
	},
	rePosition:function(){
		var w=484;
		var h=260;
		var l=($("body").width()-w)/2;
		var t=this.scrollY()+($(window).height()-h)/2;
		return $("#frontpage").css("left",l).css("top",t);
	},
	hideLayer:function(){
		$("#overlay").hide();
		$("#frontpage").hide();
	},
	scrollY: function() {
	var de = document.documentElement;
	return self.pageYOffset ||
		( de && de.scrollTop) ||
		document.body.scrollTop;
	},
	setContent:function(html){
		$("#fp_Content").html(html);
	},	
	// FP //
	showEditBox:function(){
		this.drawOverlay();		
		$("#frontpage").html('<table width="484"height="260"border="0"cellpadding="0"cellspacing="0"><tr><td colspan="3"><img src="images/edit.gif"width="484"height="55"alt=""></td></tr><tr><td rowspan="2"><img src="images/fp_02.gif"width="11"height="205"alt=""></td><td style="width:458px;height:190px;background-image:url(images/fp_03.gif);"><div id="fp_Content"></div></td><td rowspan="2"><img src="images/fp_04.gif"width="15"height="205"alt=""></td></tr><tr><td><img src="images/fp_05.gif"width="458"height="15"alt=""></td></tr></table>')
			.fadeIn("slow");
		this.ajustFP();
		this.setContent("<textarea class='center' style='border:0;float:left;width:380px;height:185px;'></textarea>"+
						"<a style='float:left;width:60px;margin-left:3px;' href=javascript:void(0) onclick=doAction('saveEdit') ><img src='images/ok.gif' /></a><a style='width:60px;float:left;margin-left:3px;' href=javascript:void(0) onclick=UI.hideLayer()><img src='images/cancel.gif' /></a>");
	},
	showProBox:function(){
		this.drawOverlay();		
		$("#frontpage").html('<table width="484"height="260"border="0"cellpadding="0"cellspacing="0"><tr><td colspan="3"><img src="images/property.gif"width="484"height="55"alt=""></td></tr><tr><td rowspan="2"><img src="images/fp_02.gif"width="11"height="205"alt=""></td><td style="width:458px;height:190px;background-image:url(images/fp_03.gif);"><div id="fp_Content"></div></td><td rowspan="2"><img src="images/fp_04.gif"width="15"height="205"alt=""></td></tr><tr><td><img src="images/fp_05.gif"width="458"height="15"alt=""></td></tr></table>')
			.fadeIn("slow");
		this.ajustFP();
		this.setContent("<table cellspacing=5 width=300 class='center'>"+
						"<tr><td align=right width='100'>名称：</td><td><span class='proDetail'></span></td></tr>"+
						"<tr><td align=right>大小：</td><td><span class='proDetail'></span></td></tr>"+
						"<tr><td align=right>创建时间：</td><td><span class='proDetail'></span></td></tr>"+
						"<tr><td align=right>修改时间：</td><td><span class='proDetail'></span></td></tr>"+
						"<tr><td align=right>最后访问：</td><td><span class='proDetail'></span></td></tr>"+
						"<tr><td colspan=2 align=center><img class='button' src='images/ok.gif' /></td></tr>"+
						"</table>");
		$(".button").wrap("<a href=javascript:void(0) onclick=UI.hideLayer()></a>");
	}
}


function nameHandle(e){
	//ie do not support e.which
	if((e.keyCode || e.charCode)==13)
	$("#btn_ok").click();	
}


function GoTo($path){
	$("#iptAddr").val($path);
	doAction("Go");
}

var CopyCut=new Object;
CopyCut.Action="";//cut or copyt
CopyCut.Path="";
CopyCut.Type="";
function doAction(type,e){
	var currNav=$("#iptAddr").val();
	switch(type){
		case "enterFolder":		
		LoadDir(currNav+folderName);
	break;
		case "Go":
		LoadDir(currNav);
	break;
		case "GoUp":
		//eg:   /abc/de/
		var tmp=currNav.slice(0,currNav.lastIndexOf("/"));
		LoadDir(tmp.slice(0,tmp.lastIndexOf("/")));
	break;
		case "unzip":
		$.post(nocache(),{
			 	action:"unzip",
				path:currNav+fileName
			  },function(rsps){
				  if(rsps==1)LoadDir(currNav);
				  },"text");
	break;
		case "newDir":
		$("#smallBox").html("新文件夹名称<br /><input id='newName' onkeypress='nameHandle(event)' type='text'  /><br /><input id='btn_ok' type='button' value='确定' onclick=doAction('sendNewDirName') /><input type='button' value='取消' onclick='hideSMBox()' />").css("left",e.pageX || e.x)
										  	.css("top",e.pageY || e.y).slideDown("fast",function(){$("#newName").focus();});
		
	break;
		case "sendNewDirName":
		hideSMBox();
		$.post(nocache(),{
			 	action:"newDir",
				path:currNav+$("#newName").val()
			  },function(rsps){
				  LoadDir(currNav);
				  },"text");
	break;
		case "rmDir":
		if(!confirm("真的要删除吗？"))break;
		$.post(nocache(),{
			 	action:"rmDir",
				path:currNav+folderName
			  },function(rsps){
				  LoadDir(currNav);
				  },"text");
	break;
		case "renameDir":
		$("#smallBox").html("新名称<br /><input id='newName' value=\""+folderName+"\" onkeypress='nameHandle(event)' type='text' /><br /><input id='btn_ok' type='button' value='确定' onclick=doAction('sendRenameDir') /><input type='button' value='取消' onclick='hideSMBox()' />").css("left",e.pageX || e.x)
										  	.css("top",e.pageY || e.y).slideDown("fast",function(){$("#newName").focus();});
	break;
		case "sendRenameDir":
		hideSMBox();
		$.post(nocache(),{
			 	action:"renameDir",
				pathFrom:currNav+folderName,
				pathTo:currNav+$("#newName").val()
			  },function(rsps){
				  LoadDir(currNav);
				  },"text");
	break;
		case "copyDir":
		CopyCut.Action="copy";
		CopyCut.Path=currNav+folderName;
		CopyCut.FolderName=folderName;
		CopyCut.Type="folder";
	break;
		case "cutDir":
		CopyCut.Action="cut";
		CopyCut.Path=currNav+folderName;
		CopyCut.FolderName=folderName;
		CopyCut.Type="folder";
	break;
		case "Paste":
		if(CopyCut.Type=="folder"){
			pathTo=currNav+"/"+CopyCut.FolderName;
		}else{
			pathTo=currNav+fileName
		}
		$.post(nocache(),{
			 	action : CopyCut.Action,
				pathFrom:CopyCut.Path,
				pathTo:pathTo
			  },function(rsps){
				  LoadDir(currNav);
				  },"text");
	break;
		case "proDir":
		UI.showProBox();
		$.post(nocache(),{
			 	action: "property",
				path:currNav+folderName
			  },function(rsps){
				  $(".proDetail").eq(0).text(folderName);
				  $(".proDetail").eq(1).text(rsps.size);
				  $(".proDetail").eq(2).text("无");
				  $(".proDetail").eq(3).text("无");
				  $(".proDetail").eq(4).text("无");
				  },"json");
	break;
		case "addFav":
		$.post(nocache(),{
			 	action: "addFav",
				path:currNav
			  },showFav,"json");
	break;
		case "rmFav":
		if(!confirm("真的要删除吗？"))break;
		$.post(nocache(),{
			 	action: "rmFav",
				name:favName
			  },showFav,"json");
	break;
		case "renameFav":
		$("#smallBox").html("新名称<br /><input id='newName' value=\""+favName+"\" onkeypress='nameHandle(event)' type='text' /><br /><input id='btn_ok' type='button' value='确定' onclick=doAction('dorenameFav') /><input type='button' value='取消' onclick='hideSMBox()' />").css("left",e.pageX || e.x)
										  	.css("top",e.pageY || e.y).slideDown("fast",function(){$("#newName").focus();});
	break;
		case "dorenameFav":
		hideSMBox();
		$.post(nocache(),{
			 	action: "renameFav",
				oldname:favName,
				newname:$("#newName").val()
			  },showFav,"json");
	/*
	file
	*/
	break;
		case "download":
		location.replace("core.php?action=download&path="+encodeURIComponent(currNav+fileName));
	break;
		case "newFile":
		$("#smallBox").html("文件名称<br /><input id='newName' onkeypress='nameHandle(event)' type='text' /><br /><input id=btn_ok type='button' value='确定' onclick=doAction('donewFile') /><input type='button' value='取消' onclick='hideSMBox()' />").css("left",e.pageX || e.x)
										  	.css("top",e.pageY || e.y).slideDown("fast",function(){$("#newName").focus();});
	break;
		case "donewFile":
		hideSMBox();
		$.post(nocache(),{
			 	action: "newFile",
				path:currNav+$("#newName").val()
			  },function(rsps){
				  LoadDir(currNav);
				  },"text");
	break;
		case "copyFile":
		CopyCut.Action="copy";
		CopyCut.Path=currNav+fileName;
		CopyCut.Type="file";
	break;
		case "cutFile":
		CopyCut.Action="cut";
		CopyCut.Path=currNav+fileName;
		CopyCut.Type="file";
	break;
		case "rmFile":
		if(!confirm("真的要删除吗？"))break;
		$.post(nocache(),{
			 	action: "rmFile",
				path:currNav+fileName
			  },function(rsps){
				  LoadDir(currNav);
				  },"text");
	break;
		case "renameFile":
		$("#smallBox").html("新名称<br /><input id='newName' value=\""+fileName+"\" onkeypress='nameHandle(event)' type='text' /><br /><input id=btn_ok type='button' value='确定' onclick=doAction('dorenameFile') /><input type='button' value='取消' onclick='hideSMBox()' />").css("left",e.pageX || e.x)
										  	.css("top",e.pageY || e.y).slideDown("fast",function(){$("#newName").focus();});
	break;
		case "dorenameFile":
		hideSMBox();
		$.post(nocache(),{
			 	action: "renameFile",
				PathFrom:currNav+fileName,
				PathTo:currNav+$("#newName").val()
			  },function(rsps){
				  LoadDir(currNav);
				  },"text");
	break;
		case "editFile":
		UI.showEditBox();
		$.post(nocache(),{
			 	action: "edit",
				path:currNav+fileName
			  },function(rsps){
					$("textarea").val(rsps.content).attr("encoding",rsps.encoding);
				  },"json");
	break;
		case "saveEdit":
		UI.hideLayer();
			$.post(nocache(),{
 			    action:"saveEdit",
				path:currNav+fileName,
				content:$("textarea").val(),
				encoding:$("textarea").attr("encoding")
			  },function(rsps){
					LoadDir(currNav);
				  },"text");
	break;
		case "saveSetting":
		UI.hideLayer();
			$.post(nocache(),{
				action:"saveEdit",
				path:"config",
				content:$("textarea").val(),
				encoding:$("textarea").attr("encoding")
			  },function(rsps){
					LoadDir(currNav);
				  },"text");
	break;
		case "proFile":
		UI.showProBox();
		$.post(nocache(),{
			 	action: "property",
				path:currNav+fileName
			  },function(rsps){
				  $(".proDetail").eq(0).text(fileName);
				  $(".proDetail").eq(1).text(rsps.size);
				  $(".proDetail").eq(2).text(rsps.ctime);
				  $(".proDetail").eq(3).text(rsps.mtime);
				  $(".proDetail").eq(4).text(rsps.atime);
				  },"json");
	break;
		case "exit":
		location.replace("login.php?action=logout");
	break;
}}









function showFav(rsps){
	var html="";
	try{
	for(var i=0;i<rsps.length;i++){
		html+="<a href=javascript:GoTo('"+rsps[i].path+"')>"+rsps[i].name+"</a>";
	}
	}catch(e){}
	$("#divFav").html(html);
	$("#divFav>a").mousedown(function callback(e) {
							   favName=$(this).text();
								  if(e.button==2){
									  var html="";
									  html="<a href=javascript:doAction('rmFav') >删除</a>"+
									  "<a href=javascript:void(0) onclick=doAction('renameFav',event) >重命名</a>";
									  $("#menu").html(html).css("left",e.pageX)
										.css("top",e.pageY).slideDown("fast");
								  }
								});
}





var folderName,fileName;
function Default(){
	LoadDir("");
	LoadFav();
}
function LoadFav(){
		$.post(nocache(), {
		   action:"getFav"
		   },showFav,"json");
}
function LoadDir(path){
		$.post(nocache(), {
			  action:"get",
			  path:path
		   }, parseRsps, "json" ) ;
		//
}
function parseRsps(rsps){
		$("#divDir").html("");
		$("#divFile").html("");
		
		
		var dirHTML="",favHTML="",fileHTML="";

		$("#iptAddr").val(rsps.currDir);
		if(rsps.dirs){
			for(var i=0;i<rsps.dirs.length;i++){
				dirHTML+="<a href=javascript:doAction('enterFolder') >"+rsps.dirs[i]+"</a>";
			}
		}
		if(rsps.files){
			for(var i=0;i<rsps.files.length;i++){
				fileHTML+="<a target='_blank' href='"+rsps.files[i].link+"'>"+rsps.files[i].name+"&nbsp;&nbsp;&nbsp;&nbsp;["+rsps.files[i].size+" KB]</a>";
			}
		}
		$("#divDir").html(dirHTML);
		$("#divFile").html(fileHTML);
		
// MENU
		$("#divDir>a").mousedown(function callback(e) {
								   folderName=$(this).html();
									  if(e.button==2){
										  var html="";
										  html="<a href=javascript:doAction('enterFolder') >打开目录</a><a href=javascript:void(0) onclick=doAction('newDir',event) >新建目录</a><div class='hr'></div>"+
										  "<a href=javascript:void(0) onclick=doAction('copyDir') >复制</a><a href=javascript:doAction('cutDir') >剪切</a><div class='hr'></div>"+
										  "<a href=javascript:doAction('rmDir') >删除</a><a href=javascript:void(0) onclick=doAction('renameDir',event)>重命名</a><div class='hr'></div>"+
										  "<a href=javascript:doAction('proDir') >属性</a>";
										  $("#menu").html(html).css("left",e.pageX)
										  	.css("top",e.pageY).show("fast");
									  }
									});
		$("#divFile>a").mousedown(function callback(e) {
										fileName=$(this).html().split("&nbsp;&nbsp;&nbsp;&nbsp;")[0];
									  if(e.button==2){
										  var html="";
										  html="<a href=javascript:doAction('download')>下载</a><a href=javascript:void(0) onclick=doAction('newFile',event) >新建文件</a><div class='hr'></div>"+
										  "<a href=javascript:doAction('unzip') >Zip文件解压到当前目录 </a><a href=javascript:doAction('editFile') >文本编辑</a>"+
										  "<a href=javascript:doAction('copyFile') >复制</a><a href=javascript:doAction('cutFile') >剪切</a><div class='hr'></div>"+
										  "<a href=javascript:doAction('rmFile') >删除</a><a href=javascript:void(0) onclick=doAction('renameFile',event) >重命名</a><div class='hr'></div>"+
										  "<a href=javascript:doAction('proFile') >属性</a>";
										  $("#menu").html(html).css("left",e.pageX)
										  	.css("top",e.pageY).show("fast");
									  }
		});
		
}
