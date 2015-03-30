/**
 *************************************************************************
 * @source  : calendar.js
 * @desc    : Included JS
 *------------------------------------------------------------------------
 * VER  DATE         AUTHOR      DESCRIPTION
 * ---  -----------  ----------  -----------------------------------------
 * 1.0  2010.03.22   nova        Initial
 * ---  -----------  ----------  -----------------------------------------
 * Securacle System
 *************************************************************************
 */

TnTimgurl="/securacle/img";
mY=60;
topY=10;


function TnT_get_objTop(thisobj){
	if(typeof(thisobj)!='object') thisobj=document.getElementById(thisobj);
	if (thisobj.offsetParent==document.body) return thisobj.offsetTop;
	else return thisobj.offsetTop + TnT_get_objTop(thisobj.offsetParent);
}

function TnT_get_objLeft(thisobj){
	if(typeof(thisobj)!='object') thisobj=document.getElementById(thisobj);
	if (thisobj.offsetParent==document.body) return thisobj.offsetLeft;
	else return thisobj.offsetLeft + TnT_get_objLeft(thisobj.offsetParent);
}
function calendar_Validation(str){
	thisD=new Date(); 
	str=str.split("/").join("");
	if (!str) return true;
	var regex =/^\d{8}$/;
    if(regex.test(str) == false){
    	return false;
    }
		yy=str.substr(0,4);
		mm=str.substr(4,2);
		dd=str.substr(6,2);
		if (yy>thisD.getFullYear()+topY || yy<thisD.getFullYear()-mY)
			return false;
		if (mm>12 || mm<1)
			return false;
		if (dd<1 || dd>(mm*9-mm*9%8)/8%2+(mm==2?yy%4 || yy%100==0 && yy%400 ? 28:29:30))
			return false;
		return true;
}
function fn_calHi(obj){
	obj.style.visibility = "hidden";
}

function openCalendar(this_click,filed_name_str, move_left, move_top){
	var filed_name = document.getElementById(filed_name_str);
	//filed_name.style.cssText='color:#555';
	if (calendar_Validation(filed_name.value)) {
		if (move_left == undefined || move_top == undefined)
			TnT_open_calendar(this_click, 0, 0, filed_name_str,0,0,-173,-135);
		else
			TnT_open_calendar(this_click, 0, 0, filed_name_str,0,0,move_left,move_top);
	} else {
		eval(filed_name).value="";
		eval(filed_name).focus();
	}
}

function TnT_open_calendar(thisclick,y,m,yfld,mfld,dfld,move_left,move_top){
		tmpObj = document.getElementById(yfld);
		thisD=new Date(); 
		ty=thisD.getFullYear();
		ts=tmpObj.value.split("/").join("");
		if (!y) y=ts;
		y=""+y;
		if(y) {
			yy=y.substr(0,4);
			if(!m) mm=ts.substr(4,2);
			else mm=parseInt(m);
			dd=ts.substr(6,2);
			if (!dd) dd=thisD.getDate();
			thisD=new Date(yy,mm-1,dd);
		}
		y=thisD.getFullYear(); 
		if(y<1000) y=thisD.getFullYear()-mY;
		m=thisD.getMonth()+1;
		y=parseInt(y);
		m=parseInt(m);
		if (y<ty-mY) y=ty+topY;
		if (y>ty+topY) y=ty-mY;
		tod_y=y; tod_m=m; tod_d=thisD.getDate();
		var tmpHTML;
		var weekarr=new Array('<font color=#ff2222>S</font>','M','T','W','T','F','S');
		var chk_d=(y+(y-y%4)/4-(y-y%100)/100+(y-y%400)/400+m*2+(m*5-m*5%9)/9-(m<3?y%4||y%100==0&&y%400?2:3:4))%7;
		tmpHTML='<table width=162 height=140 bgcolor=#dddddd cellpadding=0 cellspacing=1 style="font-size:8pt; font-family:Tahoma; border:1px solid #aaaaaa;"><tbody style="background-color:#ffffff; padding:1 4 1 4"><tr><td align=right bgcolor=#f1f1f1 colspan="7" style="font-size:10pt; font-family:Tahoma;"><nobr>';
		tmpHTML+='<img style="cursor:pointer;" title="year" src="'+TnTimgurl+'/arrow_left2.gif" align=absmiddle onclick="TnT_open_calendar(0,'+(y-1)+','+m+',\''+yfld+'\',\''+mfld+'\',\''+dfld+'\')"> ';
		tmpHTML+='<img style="cursor:pointer;" title="month" src="'+TnTimgurl+'/arrow_left.gif" align=absmiddle onclick="TnT_open_calendar(0,'+(m==1?(y-1)+','+12:y+','+(m-1))+',\''+yfld+'\',\''+mfld+'\',\''+dfld+'\')"> ';
		tmpHTML+=' <span title="close" style="font-weight:bold; font-size:8pt;">';
		tmpHTML+='<select name="tbSelYear" class="select_n" onChange="TnT_open_calendar(0, this.value,'+m+',\''+yfld+'\',\''+mfld+'\',\''+dfld+'\')"  Victor="Won">';
		for (i=eval(ty-mY); i<=eval(ty+topY); i++)
			if (i==y)
				tmpHTML+='<option selected value="'+i+'" >'+i+'</option>';
			else 
				tmpHTML+='<option value="'+i+'" >'+i+'</option>';
		tmpHTML+=' </select>';
		
		tmpHTML+='<select name="tbSelMonth" class="select_n" onChange="TnT_open_calendar(0, '+ y + ', this.value ,'+'\''+yfld+'\',\''+mfld+'\',\''+dfld+'\')"  Victor="Won">';
		for (i=1; i<=12; i++)
			if (i==m)
				tmpHTML+='<option selected value="'+i+'" >'+i+'</option>';
			else 
				tmpHTML+='<option value="'+i+'" >'+i+'</option>';
		tmpHTML+=' </select>';
		tmpHTML+='</span>  ';
		tmpHTML+='<img style="cursor:pointer;" title="month" src="'+TnTimgurl+'/arrow_right.gif" align=absmiddle onclick="TnT_open_calendar(0,'+(m==12?(y+1)+','+1:y+','+(m+1))+',\''+yfld+'\',\''+mfld+'\',\''+dfld+'\')"> ';
		tmpHTML+='<img style="cursor:pointer;" title="year" src="'+TnTimgurl+'/arrow_right2.gif" align=absmiddle onclick="TnT_open_calendar(0,'+(y+1)+','+m+',\''+yfld+'\',\''+mfld+'\',\''+dfld+'\')"> ';
		tmpHTML+=' <img style="cursor:pointer;" title="close" src="'+TnTimgurl+'/bt_close.gif" align=absmiddle onclick="join_YMD_str()"> ';
		tmpHTML+='</nobr></td></tr><tr>';
		for (i=0; i < 7; i++) tmpHTML+='<td align=center style="font-weight:bold; font-size:7pt; width:23px;">' + weekarr[i] + '</td>';
		for (i=0; i < 42; i++) {
			if (i%7==0) tmpHTML+='</tr><tr>';
			if (i < chk_d || i >= chk_d+(m*9-m*9%8)/8%2+(m==2?y%4 || y%100==0 && y%400 ? 28:29:30)) tmpHTML+='<td></td>';
			else{
				tmpHTML+='<td width="23px"';
				if((i+1-chk_d==tod_d)) tmpHTML+=' style="background-color: #ebeded; font-weight: bold;" ';
				else  tmpHTML += ' onmouseover=this.style.backgroundColor="#eff8fa" onmouseout=this.style.backgroundColor="" ' ;
				tmpHTML+=' onclick="join_YMD_str('+y+','+m+','+(i+1-chk_d)+',\''+yfld+'\','+mfld+','+dfld+')"' + ' align=center style="cursor:pointer; color:' + (i%7?'#000000':'#ff2222') + '; font-size:8pt; font-family:Tahoma;">' + (i+1-chk_d) + '</td>';
			}
		}
		tmpHTML+='</tr></table>';
		calendarTmpLayer=document.getElementById('calendarTmpbox');
		if(!calendarTmpLayer){
			var tmpdiv=document.createElement('div');
			tmpdiv.setAttribute('id','calendarTmpbox');
			document.body.appendChild(tmpdiv);
			calendarTmpLayer=document.getElementById('calendarTmpbox');
			calendarTmpLayer.style.cssText='position:absolute; visibility: hidden; z-Index: 100;';
		}
		calendarTmpLayer.innerHTML=tmpHTML;
		if(thisclick){
			calendarTmpLayer.style.visibility='hidden';
			calend_left=TnT_get_objLeft(thisclick)+(move_left?move_left:30);
			calend_top=TnT_get_objTop(thisclick)+(move_top?move_top:20);
			calendarTmpLayer.style.left=calend_left<1?1:calend_left;
			calendarTmpLayer.style.top=calend_top<1?1:calend_top;
		}
		//if(navigator.userAgent.indexOf("MSIE 6")>-1 && navigator.userAgent.indexOf("MSIE 7")<0){

			document.getElementById("calendarTmpbox").style.zIndex = 100;
		    var objLayer = document.getElementById("calendarTmpbox");
		    if (!document.getElementById("IE6Iframe"))
		    	var ie6_ifm = document.createElement("iframe"); 
		    else 
		    	var ie6_ifm = document.getElementById("iframe");
		    
		    ie6_ifm.setAttribute("id","IE6Iframe");
		    ie6_ifm.style.position = "absolute";
		    ie6_ifm.style.opacity = "0";
		    ie6_ifm.style.filter = "alpha(opacity=0)";
		    ie6_ifm.style.zIndex = "-1";
		    ie6_ifm.style.left = "-2";
		    ie6_ifm.style.top = "-2";
		    ie6_ifm.style.width = objLayer.offsetWidth + 2;
		    ie6_ifm.style.height = objLayer.offsetHeight + 2;
		    objLayer.appendChild(ie6_ifm);
		//}
		setTimeout("calendarTmpLayer.style.visibility = 'visible';", 50);
		ie6_ifm.onmouseout = ie6_ifm.onblur = function () {
			//alert(1);
		}
		
}function join_YMD_str(y,m,d,yfld,mfld,dfld){
	tmpObj = document.getElementById(yfld);
	calendarTmpLayer.style.visibility = 'hidden'; if(!tmpObj) return;
	if(m<10) m='0'+m; if(d<10) d='0'+d;
	if(!mfld || !dfld){tmpObj.value=y+'/'+m+'/'+d; return;}
	tmpObj.value=y; mfld.value=m; dfld.value=d;
}


