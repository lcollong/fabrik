/*! fabrik 2015-03-23 */
var SliderField=new Class({initialize:function(a,b){this.field=document.id(a),this.slider=b,this.field.addEvent("change",function(a){this.update(a)}.bind(this))},destroy:function(){this.field.removeEvent("change",function(a){this.update(a)}.bind(this))},update:function(){return this.options.editable?void this.slider.set(this.field.value.toInt()):void this.element.set("html",val)}}),ColourPicker=new Class({Extends:FbElement,options:{red:0,green:0,blue:0,value:[0,0,0,1],showPicker:!0,swatchSizeWidth:"10px",swatchSizeHeight:"10px",swatchWidth:"160px"},initialize:function(a,b){this.plugin="colourpicker",("null"===typeOf(b.value)||"undefined"===b.value[0])&&(b.value=[0,0,0,1]),this.parent(a,b),b.outputs=this.outputs,this.element=document.id(a),this.ini()},ini:function(){this.options.callback=function(a,b){a=this.update(a),b!==this.grad&&this.grad&&this.grad.update(a)}.bind(this),this.widget=this.element.getParent(".fabrikSubElementContainer").getElement(".colourpicker-widget"),this.setOutputs();new Drag.Move(this.widget,{handle:this.widget.getElement(".draggable")});this.options.showPicker&&this.createSliders(this.strElement),this.swatch=new ColourPickerSwatch(this.options.element,this.options,this),this.widget.getElement("#"+this.options.element+"-swatch").empty().adopt(this.swatch),this.widget.hide(),this.options.showPicker&&(this.grad=new ColourPickerGradient(this.options.element,this.options,this),this.widget.getElement("#"+this.options.element+"-picker").empty().adopt(this.grad.square)),this.update(this.options.value);var a=this.widget.getElement(".modal-header a");a&&a.addEvent("click",function(a){a.stop(),this.widget.hide()}.bind(this))},cloned:function(a){this.parent(a);var b=this.element.getParent(".fabrikSubElementContainer").getElement(".colourpicker-widget"),c=b.getElements(".tab-pane"),d=b.getElements("a[data-toggle=tab]");d.each(function(b){var c=b.get("href").split("-"),d=c[0].split("_");d[d.length-1]=a,d=d.join("_"),d+="-"+c[1],b.href=d}),c.each(function(b){var c=b.get("id").split("-"),d=c[0].split("_");d[d.length-1]=a,d=d.join("_"),d+="-"+c[1],b.id=d}),d.each(function(a){a.addEvent("click",function(b){b.stop(),jQuery(a).tab("show")})}),this.ini()},setOutputs:function(){this.outputs={},this.outputs.backgrounds=this.getContainer().getElements(".colourpicker_bgoutput"),this.outputs.foregrounds=this.getContainer().getElements(".colourpicker_output"),this.outputs.backgrounds.each(function(a){a.removeEvents("click"),a.addEvent("click",function(a){this.toggleWidget(a)}.bind(this))}.bind(this)),this.outputs.foregrounds.each(function(a){a.removeEvents("click"),a.addEvent("click",function(a){this.toggleWidget(a)}.bind(this))}.bind(this))},createSliders:function(a){this.sliderRefs=[],this.table=new Element("table"),this.tbody=new Element("tbody"),this.createColourSlideHTML(a,"red","Red:",this.options.red),this.createColourSlideHTML(a,"green","Green:",this.options.green),this.createColourSlideHTML(a,"blue","Blue:",this.options.blue),this.table.appendChild(this.tbody),this.widget.getElement(".sliders").empty().appendChild(this.table),Fabrik.addEvent("fabrik.colourpicker.slider",function(a,b,c){this.sliderRefs.contains(a.element.id)&&(this.options.colour[b]=c,this.update(this.options.colour.red+","+this.options.colour.green+","+this.options.colour.blue))}.bind(this)),this.redField.addEvent("change",function(a){this.updateFromField(a,"red")}.bind(this)),this.greenField.addEvent("change",function(a){this.updateFromField(a,"green")}.bind(this)),this.blueField.addEvent("change",function(a){this.updateFromField(a,"blue")}.bind(this))},createColourSlideHTML:function(a,b,c,d){var e=new Element("input.input-mini input "+b+"SliderField",{type:"text",id:a+b+"redField",size:"3",value:d}),f=[new Element("td").set("text",c),new Element("td").adopt(e)],g=new Element("tr").adopt(f);this.tbody.appendChild(g),this[b+"Field"]=e},updateAll:function(a,b,c){a=a?a.toInt():0,b=b?b.toInt():0,c=c?c.toInt():0,this.options.showPicker&&(this.redField.value=a,this.greenField.value=b,this.blueField.value=c),this.options.colour.red=a,this.options.colour.green=b,this.options.colour.blue=c,this.updateOutputs()},updateOutputs:function(){var a=new Color([this.options.colour.red,this.options.colour.green,this.options.colour.blue,1]);this.outputs.backgrounds.each(function(b){b.setStyle("background-color",a)}),this.outputs.foregrounds.each(function(b){b.setStyle("background-color",a)}),this.element.value=a.red?a.red+","+a.green+","+a.blue:a.rgb.join(",")},update:function(a){return this.options.editable===!1?void this.element.set("html",a):("null"===typeOf(a)?a=[0,0,0]:"string"===typeOf(a)&&(a=a.split(",")),this.updateAll(a[0],a[1],a[2]),a)},updateFromField:function(a,b){var c=Math.min(255,a.target.value.toInt());a.target.value=c,isNaN(c)?c=0:(this.options.colour[b]=c,this.options.callback(this.options.colour.red+","+this.options.colour.green+","+this.options.colour.blue))},toggleWidget:function(a){a.stop(),this.widget.toggle()}}),ColourPickerSwatch=new Class({Extends:Options,options:{},initialize:function(a,b){return this.element=document.id(a),this.setOptions(b),this.callback=this.options.callback,this.outputs=this.options.outputs,this.redField=null,this.widget=new Element("div"),this.colourNameOutput=new Element("span",{stlye:"padding:3px"}).inject(this.widget),this.createColourSwatch(a),this.widget},createColourSwatch:function(a){for(var b,c=new Element("div",{styles:{"float":"left","margin-left":"5px","class":"swatchBackground"}}),d=0;d<this.options.swatch.length;d++){var e=new Element("div",{styles:{width:this.options.swatchWidth}}),f=this.options.swatch[d];b=0,$H(f).each(function(c,f){var g=a+"swatch-"+d+"-"+b;e.adopt(new Element("div",{id:g,styles:{"float":"left",width:this.options.swatchSizeWidth,cursor:"crosshair",height:this.options.swatchSizeHeight,"background-color":"rgb("+f+")"},"class":c,events:{click:function(a){this.updateFromSwatch(a)}.bind(this),mouseenter:function(a){this.showColourName(a)}.bind(this),mouseleave:function(a){this.clearColourName(a)}.bind(this)}})),b++}.bind(this)),c.adopt(e)}this.widget.adopt(c)},updateFromSwatch:function(a){a.stop();var b=new Color(a.target.getStyle("background-color"));this.options.colour.red=b[0],this.options.colour.green=b[1],this.options.colour.blue=b[2],this.showColourName(a),this.callback(b,this)},showColourName:function(a){this.colourName=a.target.className,this.colourNameOutput.set("text",this.colourName)},clearColourName:function(){this.colourNameOutput.set("text","")}}),ColourPickerGradient=new Class({Extends:Options,options:{size:125},initialize:function(a,b){this.brightness=0,this.saturation=0,this.setOptions(b),this.callback=this.options.callback,this.container=document.id(a),"null"!==typeOf(this.container)&&(this.offset=0,this.margin=10,this.borderColour="rgba(155, 155, 155, 0.6)",this.hueWidth=40,this.colour=new Color(this.options.value),this.square=new Element("canvas",{width:this.options.size+65+"px",height:this.options.size+"px"}),this.square.inject(this.container),this.square.addEvent("click",function(a){this.doIt(a)}.bind(this)),this.down=!1,this.square.addEvent("mousedown",function(){this.down=!0}.bind(this)),this.square.addEvent("mouseup",function(){this.down=!1}.bind(this)),document.addEvent("mousemove",function(a){this.down&&this.doIt(a)}.bind(this)),this.drawCircle(),this.drawHue(),this.arrow=this.drawArrow(),this.positionCircle(this.options.size,0),this.update(this.options.value))},doIt:function(a){var b={x:0,y:0,w:this.options.size,h:this.options.size},c=this.square.getPosition(),d=a.page.x-c.x,e=a.page.y-c.y;d<b.w&&e<b.h?this.setColourFromSquareSelection(d,e):d>this.options.size+this.margin&&d<=this.options.size+this.hueWidth&&this.setHueFromSelection(d,e)},update:function(a){colour=new Color(a),this.brightness=colour.hsb[2],this.saturation=colour.hsb[1],this.colour=this.colour.setHue(colour.hsb[0]),this.colour=this.colour.setSaturation(100),this.colour=this.colour.setBrightness(100),this.render(),this.positionCircleFromColour(colour)},positionCircleFromColour:function(a){this.saturarion=a.hsb[1],this.brightness=a.hsb[2];var b=Math.floor(this.options.size*(this.saturarion/100)),c=Math.floor(this.options.size-this.options.size*(this.brightness/100));this.positionCircle(b,c)},drawCircle:function(){this.circle=new Element("canvas",{width:"10px",height:"10px"});var a=this.circle.getContext("2d");a.lineWidth=1,a.beginPath();var b=this.circle.width/2,c=this.circle.width/2;a.arc(b,c,4.5,0,2*Math.PI,!0),a.strokeStyle="#000",a.stroke(),a.beginPath(),a.arc(b,c,3.5,0,2*Math.PI,!0),a.strokeStyle="#FFF",a.stroke()},setHueFromSelection:function(a,b){b=Math.min(1,b/this.options.size),b=Math.max(0,b);var c=360-360*b;this.colour=this.colour.setHue(c),this.render(),this.positionCircle();var d=this.colour;d=d.setBrightness(this.brightness),d=d.setSaturation(this.saturation),this.callback(d,this)},setColourFromSquareSelection:function(a,b){var c=this.square.getContext("2d");this.positionCircle(a,b);var d=c.getImageData(a,b,1,1).data,e=new Color([d[0],d[1],d[2]]);this.brightness=e.hsb[2],this.saturation=e.hsb[1],this.callback(e,this)},positionCircle:function(a,b){a=a?a:this.circleX,this.circleX=a,b=b?b:this.circleY,this.circleY=b,this.render();var c=this.square.getContext("2d"),d=this.offset-5;a=Math.max(-5,Math.round(a)+d),b=Math.max(-5,Math.round(b)+d),c.drawImage(this.circle,a,b)},drawHue:function(){var a=this.square.getContext("2d"),b=this.options.size+this.margin+this.offset,c=a.createLinearGradient(0,0,0,this.options.size+this.offset);c.addColorStop(0,"rgba(255, 0, 0, 1)"),c.addColorStop(5/6,"rgba(255, 255, 0, 1)"),c.addColorStop(4/6,"rgba(0, 255, 0, 1)"),c.addColorStop(.5,"rgba(0, 255, 255, 1)"),c.addColorStop(2/6,"rgba(0, 0, 255, 1)"),c.addColorStop(1/6,"rgba(255, 0, 255, 1)"),c.addColorStop(1,"rgba(255, 0, 0, 1)"),a.fillStyle=c,a.fillRect(b,this.offset,this.hueWidth-10,this.options.size),a.strokeStyle=this.borderColour,a.strokeRect(b+.5,this.offset+.5,this.hueWidth-11,this.options.size-1)},render:function(){var a=this.square.getContext("2d"),b=this.offset;a.clearRect(0,0,this.square.width,this.square.height);var c=this.options.size;a.fillStyle=this.colour.hex,a.fillRect(b,b,c,c);var d=a.createLinearGradient(b,b,c+b,0);d.addColorStop(0,"rgba(255, 255, 255, 1)"),d.addColorStop(1,"rgba(255, 255, 255, 0)"),a.fillStyle=d,a.fillRect(b,b,c,c),d=a.createLinearGradient(0,b,0,c+b),d.addColorStop(0,"rgba(0, 0, 0, 0)"),d.addColorStop(1,"rgba(0, 0, 0, 1)"),a.fillStyle=d,a.fillRect(b,b,c,c),a.strokeStyle=this.borderColour,a.strokeRect(b+.5,b+.5,c-1,c-1),this.drawHue();var e=(360-this.colour.hsb[0])/362*this.options.size-2,f=c+this.hueWidth+b+2,g=Math.max(0,Math.round(e)+b-1);a.drawImage(this.arrow,f,g)},drawArrow:function(){var a=new Element("canvas"),b=a.getContext("2d"),c=16,d=c/3;a.width=c,a.height=c;for(var e=-c/4,f=0,g=0;20>g;g++)b.beginPath(),b.fillStyle="#000",b.moveTo(f,c/2+e),b.lineTo(f+c/4,c/4+e),b.lineTo(f+c/4,c/4*3+e),b.fill();return b.translate(-d,-c),a}});