var MSwitcher=new Class({Implements:[Options,Events],togglers:null,elements:null,current:null,options:{onShow:function(){},onHide:function(){},cookieName:"switcher",togglerSelector:"a",elementSelector:"div.tab",elementPrefix:"page-"},initialize:function(c,b,a){this.setOptions(a);this.togglers=document.id(c).getElements(this.options.togglerSelector);this.elements=document.id(b).getElements(this.options.elementSelector);if((this.togglers.length==0)||(this.togglers.length!=this.elements.length)){return}this.hideAll();this.togglers.each(function(e){e.addEvent("click",this.display.bind(this,e.id))}.bind(this));var d=[Cookie.read(this.options.cookieName),this.togglers[0].id].pick();this.display(d)},display:function(a){var c=document.id(a);var b=document.id(this.options.elementPrefix+a);if(c==null||b==null||c==this.current){return this}if(this.current!=null){this.hide(document.id(this.options.elementPrefix+this.current));document.id(this.current).removeClass("active")}this.show(b);c.addClass("active");this.current=c.id;Cookie.write(this.options.cookieName,this.current)},hide:function(a){this.fireEvent("hide",a);a.setStyle("display","none")},hideAll:function(){this.elements.setStyle("display","none");this.togglers.removeClass("active")},show:function(a){this.fireEvent("show",a);a.setStyle("display","block")}});

document.switcher = null;
jQuery(function($){
    var toggler = document.getElementById('submenu');
    var element = document.getElementById('config-document');
    if (element) {
        document.switcher = new MSwitcher(toggler, element);
    }
});