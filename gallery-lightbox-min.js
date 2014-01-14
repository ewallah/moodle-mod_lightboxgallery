YUI.add("gallery-lightbox",function(B){var N=B.Lang,J=B.Node,F="px",K="click",E="anim",D="activeImage",P="imageArray",O="overlayOpacity",H="overlayDuration",G="lightbox",Q="overlay",R="prevLink",A="nextLink",I="hoverNav",C=null;B.mix(J.prototype,{show:function(){this.setStyle("display","");this.show();return this;},hide:function(){this.setStyle("display","none");return this;},displayed:function(){return this.getStyle("display")!="none";},toggle:function(){this[this.displayed()?"hide":"show"]();return this;}});var M=function(L){M.superclass.constructor.apply(this,arguments);};M.NAME=G;M.ATTRS={selector:{value:"a[rel^=lightbox]",validator:N.isString},borderWidth:{value:10,validator:N.isNumber},overlayDuration:{value:0.2,validator:N.isNumber},overlayOpacity:{value:0.8,validator:N.isNumber},resizeDuration:{value:0.5,validator:N.isNumber},anim:{value:!N.isUndefined(B.Anim),validator:N.isBoolean},imageArray:{validator:N.isArray},activeImage:{validator:N.isNumber},strings:{value:{labelImage:"Image",labelOf:"of"}}};B.extend(M,B.Base,{initializer:function(L){var U=B.one(document.body),T=J.create;U.append(T('<div id="overlay"></div>'));U.append(T('<div id="lightbox" style="display:none;"></div>').append(T('<div id="outerImageContainer"></div>').append(T('<div id="imageContainer"></div>').append(T('<img id="lightboxImage" />')).append(T('<div id="hoverNav"></div>').append(T('<a id="prevLink" href="#"></a>')).append(T('<a id="nextLink" href="#"></a>'))).append(T('<div id="loading"></div>')))).append(T('<div id="imageDataContainer"></div>').append(T('<div id="imageData"></div>').append(T('<div id="imageDetails"></div>').append(T('<span id="caption"></span>')).append(T('<span id="numberDisplay"></span>'))).append(T('<div id="bottomNav"></div>').append(T('<a id="bottomNavClose" href="#"></a>'))))));this._bindStartListener();B.one("#overlay").hide().on(K,function(){this.end();},this);B.one("#lightbox").on(K,function(V){if(V.currentTarget.get("id")===G){this.end();}},this);var S=(this.get(E)?250:1)+F;B.one("#outerImageContainer").setStyles({width:S,height:S});B.one("#prevLink").on(K,function(V){V.halt();this._changeImage(this.get(D)-1);},this);B.one("#nextLink").on(K,function(V){V.halt();this._changeImage(this.get(D)+1);},this);B.one("#bottomNavClose").on(K,function(V){V.halt();this.end();},this);N.later(0,this,function(){var V="overlay lightbox outerImageContainer imageContainer lightboxImage hoverNav prevLink nextLink loading "+"imageDataContainer imageData imageDetails caption numberDisplay bottomNav bottomNavClose";B.Array.each(V.split(" "),function(X,W,Y){this.addAttr(X,{value:B.one("#"+X)});},this);});},start:function(W){B.all("select, object, embed").each(function(){/*this.setStyle("visibility","hidden");*/});var S=this.get(Q).setStyles({height:B.DOM.docHeight()+F,width:B.DOM.docWidth()+F}).show();if(this.get(E)){var V=new B.Anim({node:S,from:{opacity:0},to:{opacity:this.get(O)},duration:this.get(H)});V.run();}else{S.setStyle("opacity",this.get(O));}var U=[],L=0;if(W.get("rel")===G){U.push([W.get("href"),W.get("title")]);}else{B.all('.lightbox-gallery a[href][rel="lightbox_gallery"]').each(function(){U.push([this.get("href"),this.get("title")]);});while(U[L][0]!==W.get("href")){L++;}}this.set(P,U);var X=B.DOM.docScrollY()+(B.DOM.winHeight()/10),T=B.DOM.docScrollX();this.get(G).setStyles({display:"",top:X+F,left:T+F});this.get(G).show();this._changeImage(L);},end:function(){this._disableKeyboardNav();this.get(G).hide();var L=this.get(Q);if(this.get(E)){var S=new B.Anim({node:L,from:{opacity:this.get(O)},to:{opacity:0},duration:this.get(H)});S.on("end",function(){L.hide();});S.run();}else{L.setStyles({opacity:0}).hide();}B.all("select, object, embed").each(function(){this.setStyle("visibility","visible");});},_bindStartListener:function(){B.delegate(K,B.bind(function(L){L.halt();this.start(L.currentTarget);},this),B.one(".lightbox-gallery"),this.get("selector"));},_changeImage:function(S){this.set(D,S);if(this.get(E)){this.get("loading").show();}this.get("lightboxImage").hide();this.get(I).hide();this.get(R).hide();this.get(A).hide();this.get("imageDataContainer").setStyle("opacity",0.0001);this.get("numberDisplay").hide();var L=new Image();L.onload=B.bind(function(){this.get("lightboxImage").set("src",this.get(P)[S][0]);this._resizeImageContainer(L.width,L.height);},this);L.src=this.get(P)[S][0];},_resizeImageContainer:function(X,Y){var U=this.get("outerImageContainer"),a=U.get("offsetWidth"),W=U.get("offsetHeight"),Z=X+this.get("borderWidth")*2,c=Y+this.get("borderWidth")*2,b=a-Z,T=W-c,d=B.bind(function(){this.get(R).setStyles({height:Y+F});this.get(A).setStyles({height:Y+F});this.get("imageDataContainer").setStyles({width:Z+F});this._showImage();},this);if(b!==0||T!==0){if(this.get(E)){var S=this.get("resizeDuration"),V=new B.Anim({node:U,from:{width:a+F},to:{width:Z+F},duration:S}),L=function(){V.getEvent("end").detach(L);this.setAttrs({from:{height:W+F},to:{height:c+F},duration:S});this.on("end",d);this.run();};V.on("end",L);V.run();}else{U.setStyles({width:Z+F,height:c+F});N.later(0,this,d);}}else{N.later(100,this,d);}},_showImage:function(){this.get("loading").hide();var T=this.get("lightboxImage");if(this.get(E)){var L=T.getStyle("display")==="none"?0:T.getStyle("opacity")||0,S=new B.Anim({node:T,from:{opacity:L},to:{opacity:1}});S.on("end",this._updateDetails,this);T.setStyle("opacity",L).show();S.run();}else{T.setStyle("opacity",1).show();this._updateDetails();}this._preloadNeighborImages();},_updateDetails:function(){var W=this.get(P),U=this.get(D),L=W[U][1];if(L!==""){this.get("caption").setContent(L).show();}if(W.length>1){this.get("numberDisplay").setContent(this.get("strings.labelImage")+" "+(U+1)+" "+this.get("strings.labelOf")+"  "+W.length).show();}var S=this.get("imageDataContainer");if(this.get(E)){var T=S.getStyle("display")==="none"?0:S.getStyle("opacity")||0,V=new B.Anim({node:S,from:{opacity:T},to:{opacity:1},duration:this.get("resizeDuration")});V.on("end",function(){this.get(Q).setStyle("height",B.DOM.docHeight()+F);this._updateNav();},this);
S.setStyle("opacity",T).show();V.run();}else{S.setStyle("opacity",1).show();this.get(Q).setStyle("height",B.DOM.docHeight()+F);this._updateNav();}},_updateNav:function(){var L=this.get(D);this.get(I).show();if(L>0){this.get(R).show();}if(L<(this.get(P).length-1)){this.get(A).show();}this._enableKeyboardNav();},_enableKeyboardNav:function(){B.one(document.body).on("keydown",this._keyboardAction,this);},_disableKeyboardNav:function(){B.one(document.body).unsubscribe("keydown",this._keyboardAction);},_keyboardAction:function(S){var U=S.keyCode,L=27,T=String.fromCharCode(U).toLowerCase();if(T.match(/x|o|c/)||(U===L)){this.end();}else{if((T==="p")||(U===37)){if(this.get(D)!==0){this._disableKeyboardNav();this._changeImage(this.get(D)-1);}}else{if((T==="n")||(U===39)){if(this.get(D)!==(this.get(P).length-1)){this._disableKeyboardNav();this._changeImage(this.get(D)+1);}}}}},_preloadNeighborImages:function(){var S=this.get(D),U=this.get(P),L,T;if(U.length>S+1){L=new Image();L.src=U[S+1][0];}if(S>0){T=new Image();T.src=U[S-1][0];}}});B.Lightbox={init:function(L){if(C===null){C=new M(L);}return C;}};},"gallery-2010.04.08-12-35",{requires:["base","node","anim","selector-css3"]});
