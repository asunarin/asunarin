/*
 * jQuery tKey
 * By: Strongwillow || http://strongwillow.org.cn
 * Version 1.0
 * Last Modified: 17, Aug, 2008
 * Homepage: http://twinklous.net/?c=tkey
 * 
 * This plugin will make it easier to enhance accessbility of your web product
 *
*/
jQuery.tKey = {
	mk:"z",
	kstart:false,
	list:{},
	setMainKey:function(k){
		this.mk=k;
	},
	reset:function(){
		this.kstart=false;
	},
	addKey:function(k,fn){
		this.list["k"+k.charCodeAt()]=fn;
	},
	trigger:function(kcode){
		if(kcode==this.mk.charCodeAt()){
			this.kstart=true;
		}else{
			if(this.kstart){
				try{
				eval("this.list.k"+kcode+"();");
				this.reset();
				}catch(e){};
			}
		}
	}
};
$(document).keydown(function(e){
							 $.tKey.trigger(e.keyCode+32);
							 });
$(document).keyup(function(e){
							 $.tKey.reset();
							 });