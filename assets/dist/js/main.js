(b=>{b(function(){var C=window.swsL10n||{};if(C)for(var e in C.widgets)if(b(C.widgets[e].selector).length){let s=C.widgets[e],i={id:e,serviceUrl:C.restUrl+"id="+encodeURIComponent(e),layout:C.widgets[e].layout,columns:C.widgets[e].columns,productSlug:C.widgets[e].productSlug,maxHeight:C.widgets[e].popupHeight,maxHeightMobile:C.widgets[e].popupHeightMobile,minChars:C.widgets[e].charCount,disableAjax:C.widgets[e].disableAjax,no_results_text:C.widgets[e].noResultsText,loaderIcon:C.widgets[e].loaderIcon,preventBadQueries:C.widgets[e].preventBadQueries,fullScreenMode:C.widgets[e].fullScreenMode,placeholder:C.widgets[e].placeholder,recentSearches:C.widgets[e].recentSearches,recentSearchesTitle:C.widgets[e].recentSearchesTitle};b(s.selector).each(function(){var e,n,o,t,r,l,a,c,h,w=s.fullScreenMode,S=768<=b(window).width();if("desktop_only"==w&&S||"mobile_only"==w&&!S||"enable"==w){var S=this;var w=i;let g=w.placeholder||"",f=b(S),v="FORM"===S.tagName||"form"===S.tagName?f:f.find("form"),m=v.find(".smart-search-popup");if(!f.hasClass("ysm-active")&&!v.hasClass("ysm-active")){f.addClass("ysm-active").addClass("ysm-hide"),v.addClass("ysm-active"),b('<div class="smart-search-fullscreen"><div class="smart-search-fullscreen-backdrop"></div><div class="smart-search-fullscreen-inner"><div class="smart-search-input-wrapper"><input type="search" class="ssf-search-input" placeholder="'+g+'" name="s" id="smart-search-fullscreen-'+w.id+'"><span class="ssf-search-icon-search"><svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24" fill="currentColor"><path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg></span><span class="ssf-search-icon-close" aria-label="close"><svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24" fill="currentColor"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/></svg></span></div><div class="smart-search-results-main"><div class="smart-search-results">\t<div class="smart-search-results-inner"></div></div></div></div></div>').appendTo(v);let e=v.find(".smart-search-fullscreen"),t=v.find(".smart-search-fullscreen-backdrop"),s=f.find('input[type="search"]').length?f.find('input[type="search"]'):f.find('input[type="text"]'),n=(f.find(".ssf-search-input").length,f.find(".ssf-search-input")),o=v.find(".smart-search-results-main"),r=v.find(".smart-search-results"),l=r.find(".smart-search-results-inner"),i=v.find(".ssf-search-icon-close"),a=f.find(".search-submit").length?f.find(".search-submit"):"",c=JSON.parse(localStorage.getItem("latestSearches"))||[];S={id:"",serviceUrl:C.restUrl,layout:"",columns:1,productSlug:"product",maxHeight:500,maxHeightMobile:400,minChars:3,disableAjax:!1,no_results_text:"",loaderIcon:"",preventBadQueries:!0,cache:!0};let h=b.extend({},S,w),u=e=>{e=e.slice(-5);b(".sws-search-recent-list").empty(),e.forEach(e=>{b(".sws-search-recent-list").append(`
						<li class="sws-search-recent-list-item">
							<span class="sws-search-recent-list-item-trigger">${e}</span>
							<span class="sws-search-recent-list-item-delete" data-item="${e}" aria-label="close"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/></svg></span>
						</li>
					`)})},d=(c.length&&(0==b(".sws-search-recent-wrapper").length&&b('<div class="sws-search-recent-wrapper"><h4 class="sws-search-recent-title">'+h.recentSearchesTitle+'</h4><ul class="sws-search-recent-list"></ul></div>').prependTo(o),u(c),b(document).on("click",".sws-search-recent-list-item-trigger",e=>{e=b(e.target).text();n.val(e).focus()}),b(document).on("click",".sws-search-recent-list-item-delete",function(){let t=b(this).data("item");c=c.filter(e=>e!==t),localStorage.setItem("latestSearches",JSON.stringify(c)),u(c)})),()=>{e.addClass("ssf-active"),setTimeout(()=>{e.addClass("ssf-animated"),b(".ssf-search-input").focus()},100)}),p=()=>{e.removeClass("ssf-active ssf-animated"),setTimeout(()=>{b(".ssf-search-input").val(""),r.css({maxHeight:0}),b(".smart-search-view-all-holder").hide()},500)};s.on("click",()=>{d()}),a.length&&a.on("click",()=>{d()}),b(document).on("keydown",e=>{"Tab"===e.key&&setTimeout(()=>{b(document.activeElement).is(s)&&d()},0),"Escape"===e.key&&p()}),t.on("click",()=>{p()}),0<i.length&&i.on("click",function(){p()});var x,y=Math.min(window.screen.width,window.screen.height)<768?h.maxHeightMobile:h.maxHeight;v.on("submit",function(e){var t=n.val();if(""===t||t.length<h.minChars)return!1;var s=C.searchPageUrl;t=(t=t.replace(/\+/g,"%2b")).replace(/\s/g,"+"),s=(s+=-1!==s.indexOf("?")?"&":"?")+"s="+t+"&search_id="+h.id,"product"===h.layout&&(s+="&post_type="+h.productSlug),e.preventDefault(),location.href=s}),h.disableAjax||(x=0,n.outerWidth()&&(x=n.outerWidth(),m.css({width:x+"px"})),-1!==navigator.userAgent.indexOf("Windows")&&-1!==navigator.userAgent.indexOf("Firefox")&&r.addClass("smart-search-firefox"),b(window).on("resize",function(){m.css({width:n.outerWidth()+"px"})}),n.devbridgeAutocomplete({minChars:h.minChars,appendTo:l,serviceUrl:h.serviceUrl,maxHeight:1e5,dataType:"json",deferRequestBy:100,noCache:!h.cache,containerClass:"smart-search-suggestions",triggerSelectOnValidInput:!1,showNoSuggestionNotice:!!h.no_results_text.length,noSuggestionNotice:h.no_results_text,preventBadQueries:h.preventBadQueries,ajaxSettings:{beforeSend:function(e){C.nonce&&e.setRequestHeader("X-WP-Nonce",C.nonce)}},formatResult:function(e,t){return e.data},onSearchStart:function(e){-1!==this.value.indexOf("  ")&&(this.value=this.value.replace(/\s+/g," ")),e.query=e.query.replace(/%20/g," "),n.css({"background-image":"url("+h.loaderIcon+")","background-repeat":"no-repeat","background-position":"50% 50%"}),f.addClass("ysm-hide").removeClass("sws-no-results"),r.css({maxHeight:y+"px"})},onSelect:function(e){-1!=e.id&&e.url&&!e.addToCart&&(window.location.href=e.url)},transformResult:function(e){e="string"==typeof e?b.parseJSON(e):e;n.val();return e&&e.view_all_link&&""!=e.view_all_link&&(o.find(".smart-search-view-all-holder").length||o.addClass("has-viewall-button").append('<div class="smart-search-view-all-holder"></div>'),o.find(".smart-search-view-all-holder").html(e.view_all_link)),e},onSearchComplete:function(i,e){i===n.val()&&(x||(x=n.outerWidth(),m.css({width:x+"px"})),n.css("background-image","none"),0<e.length?(f.removeClass("ysm-hide").removeClass("sws-no-results"),setTimeout(function(){var e=r.outerWidth(),t=h.columns,s=o.find(".smart-search-view-all-holder");0===e&&(e=n.outerWidth(),r.width(e+"px")),e<200*t&&(t=Math.floor(e/200)),r.outerHeight()||(e=l.find(".smart-search-suggestions").outerHeight())&&(e=parseInt(e,10),r.height(y<e?y:e)),r.attr("data-columns",t).nanoScroller({contentClass:"smart-search-results-inner",alwaysVisible:!1,iOSNativeScrolling:!0}),s.length&&(n.val().length<h.minChars?s.hide():(e=h.serviceUrl,t=n.devbridgeAutocomplete(),e=(e=b.isFunction(e)?e.call(t.element,i):e)+"?"+b.param({query:i}),t.cachedResponse&&t.cachedResponse[e]&&s.html(t.cachedResponse[e].view_all_link),s.show()))},100),h.recentSearches&&(e=i,c.includes(e)||(c.push(e),10<c.length&&c.shift(),localStorage.setItem("latestSearches",JSON.stringify(c)))),c.length&&(e=i,c.includes(e)||(c.push(e),10<c.length&&c.shift(),localStorage.setItem("latestSearches",JSON.stringify(c))),0==b(".sws-search-recent-wrapper").length&&b('<div class="sws-search-recent-wrapper"><h4 class="sws-search-recent-title">+ options.recentSearchesTitle+</h4><ul class="sws-search-recent-list"></ul></div>').prependTo(o),u(c),b(document).on("click",".sws-search-recent-list-item-trigger",e=>{e=b(e.target).text();n.val(e).focus()}),b(document).on("click",".sws-search-recent-list-item-delete",function(){let t=b(this).data("item");c=c.filter(e=>e!==t),localStorage.setItem("latestSearches",JSON.stringify(c)),u(c)}))):(h.no_results_text.length?f.removeClass("ysm-hide"):f.addClass("ysm-hide")).addClass("sws-no-results"))},onSearchError:function(e,t,s,i){"error"===s&&((s=t.getResponseHeader("X-Wp-Nonce"))&&C.nonce!==s?(window.swsL10n.nonce=s,n.devbridgeAutocomplete().onValueChange()):C.nonce&&(window.swsL10n.nonce="",n.devbridgeAutocomplete().onValueChange()))},onInvalidateSelection:function(){},onHide:function(){}}).on("focus",function(){n.devbridgeAutocomplete().onValueChange()}))}}else S=this,w=i,c=b(S),h=c.find('input[type="search"]').length?c.find('input[type="search"]'):c.find('input[type="text"]'),S="FORM"===S.tagName||"form"===S.tagName?c:c.find("form"),!h.length||c.hasClass("ysm-active")||S.hasClass("ysm-active")||(c.addClass("ysm-active").addClass("ysm-hide"),S.addClass("ysm-active"),e={id:"",serviceUrl:C.restUrl,layout:"",columns:1,productSlug:"product",maxHeight:500,maxHeightMobile:400,minChars:3,disableAjax:!1,no_results_text:"",loaderIcon:"",preventBadQueries:!0,cache:!0},n=b.extend({},e,w),S.on("submit",function(e){var t=h.val();if(""===t||t.length<n.minChars)return!1;var s=C.searchPageUrl;t=(t=t.replace(/\+/g,"%2b")).replace(/\s/g,"+"),s=(s+=-1!==s.indexOf("?")?"&":"?")+"s="+t+"&search_id="+n.id,"product"===n.layout&&(s+="&post_type="+n.productSlug),e.preventDefault(),location.href=s}),n.disableAjax)||(b('<div class="smart-search-popup"><div class="smart-search-results"><div class="smart-search-results-inner"></div></div></div>').appendTo(S),o=S.find(".smart-search-popup"),t=0,h.outerWidth()&&(t=h.outerWidth(),o.css({width:t+"px"})),r=S.find(".smart-search-results"),l=r.find(".smart-search-results-inner"),a=Math.min(window.screen.width,window.screen.height)<768?n.maxHeightMobile:n.maxHeight,r.css({maxHeight:a+"px"}),-1!==navigator.userAgent.indexOf("Windows")&&-1!==navigator.userAgent.indexOf("Firefox")&&r.addClass("smart-search-firefox"),b(window).on("resize",function(){o.css({width:h.outerWidth()+"px"})}),b(window).on("touchstart",function(e){(b(e.target).hasClass("ysm-active")?b(e.target):b(e.target).parents(".ysm-active")).length||b(".ysm-active").addClass("ysm-hide")}),h.devbridgeAutocomplete({minChars:n.minChars,appendTo:l,serviceUrl:n.serviceUrl,maxHeight:1e5,dataType:"json",deferRequestBy:100,noCache:!n.cache,containerClass:"smart-search-suggestions",triggerSelectOnValidInput:!1,showNoSuggestionNotice:!!n.no_results_text.length,noSuggestionNotice:n.no_results_text,preventBadQueries:n.preventBadQueries,ajaxSettings:{beforeSend:function(e){C.nonce&&e.setRequestHeader("X-WP-Nonce",C.nonce)}},formatResult:function(e,t){return e.data},onSearchStart:function(e){-1!==this.value.indexOf("  ")&&(this.value=this.value.replace(/\s+/g," ")),e.query=e.query.replace(/%20/g," "),h.css({"background-image":"url("+n.loaderIcon+")","background-repeat":"no-repeat","background-position":"50% 50%"}),c.addClass("ysm-hide").removeClass("sws-no-results")},onSelect:function(e){-1!=e.id&&e.url&&!e.addToCart&&(window.location.href=e.url)},transformResult:function(e){e="string"==typeof e?b.parseJSON(e):e;h.val();return e&&e.view_all_link&&""!=e.view_all_link&&(o.find(".smart-search-view-all-holder").length||o.addClass("has-viewall-button").append('<div class="smart-search-view-all-holder"></div>'),o.find(".smart-search-view-all-holder").html(e.view_all_link)),e},onSearchComplete:function(i,e){i===h.val()&&(t||(t=h.outerWidth(),o.css({width:t+"px"})),h.css("background-image","none"),0<e.length?(c.removeClass("ysm-hide").removeClass("sws-no-results"),setTimeout(function(){var e=r.outerWidth(),t=n.columns,s=o.find(".smart-search-view-all-holder");0===e&&(e=h.outerWidth(),r.width(e+"px")),e<200*t&&(t=Math.floor(e/200)),r.outerHeight()||(e=l.find(".smart-search-suggestions").outerHeight())&&(e=parseInt(e,10),r.height(a<e?a:e)),r.attr("data-columns",t).nanoScroller({contentClass:"smart-search-results-inner",alwaysVisible:!1,iOSNativeScrolling:!0}),s.length&&(h.val().length<n.minChars?s.hide():(e=n.serviceUrl,t=h.devbridgeAutocomplete(),e=(e=b.isFunction(e)?e.call(t.element,i):e)+"?"+b.param({query:i}),t.cachedResponse&&t.cachedResponse[e]&&s.html(t.cachedResponse[e].view_all_link),s.show()))},100)):(n.no_results_text.length?c.removeClass("ysm-hide"):c.addClass("ysm-hide")).addClass("sws-no-results"))},onSearchError:function(e,t,s,i){if("error"===s){s=t.getResponseHeader("X-Wp-Nonce");if(s&&C.nonce!==s)return window.swsL10n.nonce=s,void h.devbridgeAutocomplete().onValueChange();if(C.nonce)return window.swsL10n.nonce="",void h.devbridgeAutocomplete().onValueChange()}c.addClass("ysm-hide").removeClass("sws-no-results")},onInvalidateSelection:function(){},onHide:function(){c.addClass("ysm-hide").removeClass("sws-no-results")}}).on("focus",function(){h.devbridgeAutocomplete().onValueChange()}))})}})})(jQuery),(e=>{"function"==typeof define&&define.amd?define(["jquery"],e):"object"==typeof exports&&"function"==typeof require?e(require("jquery")):e(jQuery)})(function(d){var s={escapeRegExChars:function(e){return e.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g,"\\$&")},createNode:function(e){var t=document.createElement("div");return t.className=e,t.style.position="absolute",t.style.display="none",t}},i=27,n=9,o=13,r=38,l=39,a=40;function c(e,t){function s(){}var i=this,n={ajaxSettings:{},autoSelectFirst:!1,appendTo:document.body,serviceUrl:null,lookup:null,onSelect:null,width:"auto",minChars:1,maxHeight:300,deferRequestBy:0,params:{},formatResult:c.formatResult,delimiter:null,zIndex:9999,type:"GET",noCache:!1,onSearchStart:s,onSearchComplete:s,onSearchError:s,preserveInput:!1,containerClass:"autocomplete-suggestions",tabDisabled:!1,dataType:"text",currentRequest:null,triggerSelectOnValidInput:!0,preventBadQueries:!0,lookupFilter:function(e,t,s){return-1!==e.value.toLowerCase().indexOf(s)},paramName:"query",transformResult:function(e){return"string"==typeof e?d.parseJSON(e):e},showNoSuggestionNotice:!1,noSuggestionNotice:"No results",orientation:"bottom",forceFixPosition:!1};i.element=e,i.el=d(e),i.suggestions=[],i.badQueries=[],i.selectedIndex=-1,i.currentValue=i.element.value,i.intervalId=0,i.cachedResponse={},i.onChangeInterval=null,i.onChange=null,i.isLocal=!1,i.suggestionsContainer=null,i.noSuggestionsContainer=null,i.options=d.extend({},n,t),i.classes={selected:"autocomplete-selected",suggestion:"autocomplete-suggestion"},i.hint=null,i.hintValue="",i.selection=null,i.initialize(),i.setOptions(t)}c.utils=s,(d.Autocomplete=c).formatResult=function(e,t){t="("+s.escapeRegExChars(t)+")";return e.value.replace(new RegExp(t,"gi"),"<strong>$1</strong>").replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/"/g,"&quot;").replace(/&lt;(\/?strong)&gt;/g,"<$1>")},c.prototype={killerFn:null,initialize:function(){var e,t=this,s="."+t.classes.suggestion,i=t.classes.selected,n=t.options;t.element.setAttribute("autocomplete","off"),t.killerFn=function(e){0===d(e.target).closest(n.appendTo).length&&(t.killSuggestions(),t.disableKillerFn())},t.noSuggestionsContainer=d('<div class="autocomplete-no-suggestion"></div>').html(this.options.noSuggestionNotice).get(0),t.suggestionsContainer=c.utils.createNode(n.containerClass),(e=d(t.suggestionsContainer)).appendTo(n.appendTo),"auto"!==n.width&&e.width(n.width),e.on("mouseover.autocomplete",s,function(){t.activate(d(this).data("index"))}),e.on("mouseout.autocomplete",function(){t.selectedIndex=-1,e.children("."+i).removeClass(i)}),e.on("click.autocomplete",s,function(e){t.select(d(this).data("index"),e)}),t.fixPositionCapture=function(){t.visible&&t.fixPosition()},d(window).on("resize.autocomplete",t.fixPositionCapture),t.el.on("keydown.autocomplete",function(e){t.onKeyPress(e)}),t.el.on("keyup.autocomplete",function(e){t.onKeyUp(e)}),t.el.on("blur.autocomplete",function(){t.onBlur()}),t.el.on("focus.autocomplete",function(){t.onFocus()}),t.el.on("change.autocomplete",function(e){t.onKeyUp(e)}),t.el.on("input.autocomplete",function(e){t.onKeyUp(e)})},onFocus:function(){this.fixPosition(),0===this.options.minChars&&0===this.el.val().length&&this.onValueChange()},onBlur:function(){this.enableKillerFn()},abortAjax:function(){this.currentRequest&&(this.currentRequest.abort(),this.currentRequest=null)},setOptions:function(e){var t=this,s=t.options;d.extend(s,e),t.isLocal=d.isArray(s.lookup),t.isLocal&&(s.lookup=t.verifySuggestionsFormat(s.lookup)),s.orientation=t.validateOrientation(s.orientation,"bottom"),d(t.suggestionsContainer).css({"max-height":s.maxHeight+"px",width:s.width+"px","z-index":s.zIndex})},clearCache:function(){this.cachedResponse={},this.badQueries=[]},clear:function(){this.clearCache(),this.currentValue="",this.suggestions=[]},disable:function(){this.disabled=!0,clearInterval(this.onChangeInterval),this.abortAjax()},enable:function(){this.disabled=!1},fixPosition:function(){var e,t,s,i,n,o,r,l,a=this,c=d(a.suggestionsContainer),h=c.parent().get(0);h!==document.body&&!a.options.forceFixPosition||(o=a.options.orientation,e=c.outerHeight(),t=a.el.outerHeight(),s={top:(l=a.el.offset()).top,left:l.left},"auto"===o&&(r=d(window).height(),i=-(n=d(window).scrollTop())+l.top-e,n=n+r-(l.top+t+e),o=Math.max(i,n)===i?"top":"bottom"),s.top+="top"===o?-e:t,h!==document.body&&(r=c.css("opacity"),a.visible||c.css("opacity",0).show(),l=c.offsetParent().offset(),s.top-=l.top,s.left-=l.left,a.visible||c.css("opacity",r).hide()),"auto"===a.options.width&&(s.width=a.el.outerWidth()-2+"px"),c.css(s))},enableKillerFn:function(){d(document).on("click.autocomplete",this.killerFn)},disableKillerFn:function(){d(document).off("click.autocomplete",this.killerFn)},killSuggestions:function(){var e=this;e.stopKillSuggestions(),e.intervalId=window.setInterval(function(){e.visible&&(e.el.val(e.currentValue),e.hide()),e.stopKillSuggestions()},50)},stopKillSuggestions:function(){window.clearInterval(this.intervalId)},isCursorAtEnd:function(){var e=this.el.val().length,t=this.element.selectionStart;return"number"==typeof t?t===e:!document.selection||((t=document.selection.createRange()).moveStart("character",-e),e===t.text.length)},onKeyPress:function(e){var t=this;if(t.disabled||t.visible||e.which!==a||!t.currentValue){if(!t.disabled&&t.visible){switch(e.which){case i:t.el.val(t.currentValue),t.hide();break;case l:if(t.hint&&t.options.onHint&&t.isCursorAtEnd()){t.selectHint();break}return;case n:if(t.hint&&t.options.onHint)return void t.selectHint();if(-1===t.selectedIndex)return void t.hide();if(t.select(t.selectedIndex),!1===t.options.tabDisabled)return;break;case o:if(-1===t.selectedIndex)return void t.hide();t.select(t.selectedIndex);break;case r:t.moveUp();break;case a:t.moveDown();break;default:return}e.stopImmediatePropagation(),e.preventDefault()}}else t.suggest()},onKeyUp:function(e){var t=this;if(!t.disabled){switch(e.which){case r:case a:return}clearInterval(t.onChangeInterval),t.currentValue!==t.el.val()&&(t.findBestHint(),0<t.options.deferRequestBy?t.onChangeInterval=setInterval(function(){t.onValueChange()},t.options.deferRequestBy):t.onValueChange())}},onValueChange:function(){var e=this,t=e.options,s=e.el.val(),i=e.getQuery(s);e.selection&&e.currentValue!==i&&(e.selection=null,(t.onInvalidateSelection||d.noop).call(e.element)),clearInterval(e.onChangeInterval),e.currentValue=s,e.selectedIndex=-1,t.triggerSelectOnValidInput&&e.isExactMatch(i)?e.select(0):i.length<t.minChars?e.hide():e.getSuggestions(i)},isExactMatch:function(e){var t=this.suggestions;return 1===t.length&&t[0].value.toLowerCase()===e.toLowerCase()},getQuery:function(e){return d.trim(e)},getSuggestionsLocal:function(t){var e=this.options,s=t.toLowerCase(),i=e.lookupFilter,n=parseInt(e.lookupLimit,10),e={suggestions:d.grep(e.lookup,function(e){return i(e,t,s)})};return n&&e.suggestions.length>n&&(e.suggestions=e.suggestions.slice(0,n)),e},getSuggestions:function(i){var e,t,s,n=this,o=n.options,r=o.serviceUrl;o.params[o.paramName]=i,e=o.ignoreParams?null:o.params,!1!==o.onSearchStart.call(n.element,o.params)&&(d.isFunction(o.lookup)?o.lookup(i,function(e){n.suggestions=e.suggestions,n.suggest(),o.onSearchComplete.call(n.element,i,e.suggestions)}):(s=n.isLocal?n.getSuggestionsLocal(i):(d.isFunction(r)&&(r=r.call(n.element,i)),t=r+"?"+d.param(e||{}),n.cachedResponse[t]))&&d.isArray(s.suggestions)?(n.suggestions=s.suggestions,n.suggest(),o.onSearchComplete.call(n.element,i,s.suggestions)):n.isBadQuery(i)?o.onSearchComplete.call(n.element,i,[]):(n.abortAjax(),s={url:r,data:e,type:o.type,dataType:o.dataType},d.extend(s,o.ajaxSettings),n.currentRequest=d.ajax(s).done(function(e){n.currentRequest=null,e=o.transformResult(e,i),n.processResponse(e,i,t),o.onSearchComplete.call(n.element,i,e.suggestions)}).fail(function(e,t,s){o.onSearchError.call(n.element,i,e,t,s)})))},isBadQuery:function(e){if(this.options.preventBadQueries)for(var t=this.badQueries,s=t.length;s--;)if(0===e.indexOf(t[s]))return!0;return!1},hide:function(){var e=this,t=d(e.suggestionsContainer);d.isFunction(e.options.onHide)&&e.visible&&e.options.onHide.call(e.element,t),e.visible=!1,e.selectedIndex=-1,clearInterval(e.onChangeInterval),d(e.suggestionsContainer).hide(),e.signalHint(null)},suggest:function(){var e,t,s,i,n,o,r,l,a,c,h,u;0===this.suggestions.length?this.options.showNoSuggestionNotice?this.noSuggestions():this.hide():(t=(e=this).options,s=t.groupBy,i=t.formatResult,n=e.getQuery(e.currentValue),o=e.classes.suggestion,r=e.classes.selected,l=d(e.suggestionsContainer),a=d(e.noSuggestionsContainer),c=t.beforeRender,h="",t.triggerSelectOnValidInput&&e.isExactMatch(n)?e.select(0):(d.each(e.suggestions,function(e,t){s&&(h+=(e=>(e=e.data[s],u===e?"":'<div class="autocomplete-group"><strong>'+(u=e)+"</strong></div>"))(t)),h+='<div class="'+o+'" data-index="'+e+'">'+i(t,n)+"</div>"}),this.adjustContainerWidth(),a.detach(),l.html(h),d.isFunction(c)&&c.call(e.element,l),e.fixPosition(),l.show(),t.autoSelectFirst&&(e.selectedIndex=0,l.scrollTop(0),l.children("."+o).first().addClass(r)),e.visible=!0,e.findBestHint()))},noSuggestions:function(){var e=d(this.suggestionsContainer),t=d(this.noSuggestionsContainer);this.adjustContainerWidth(),t.detach(),e.empty(),e.append(t),this.fixPosition(),e.show(),this.visible=!0},adjustContainerWidth:function(){var e=this.options,t=d(this.suggestionsContainer);"auto"===e.width&&(e=this.el.outerWidth()-2,t.width(0<e?e:300))},findBestHint:function(){var i=this.el.val().toLowerCase(),n=null;i&&(d.each(this.suggestions,function(e,t){var s=0===t.value.toLowerCase().indexOf(i);return s&&(n=t),!s}),this.signalHint(n))},signalHint:function(e){var t="",s=this;e&&(t=s.currentValue+e.value.substr(s.currentValue.length)),s.hintValue!==t&&(s.hintValue=t,s.hint=e,(this.options.onHint||d.noop)(t))},verifySuggestionsFormat:function(e){return e.length&&"string"==typeof e[0]?d.map(e,function(e){return{value:e,data:null}}):e},validateOrientation:function(e,t){return e=d.trim(e||"").toLowerCase(),e=-1===d.inArray(e,["auto","bottom","top"])?t:e},processResponse:function(e,t,s){var i=this,n=i.options;e.suggestions=i.verifySuggestionsFormat(e.suggestions),n.noCache||(i.cachedResponse[s]=e,n.preventBadQueries&&0===e.suggestions.length&&i.badQueries.push(t)),t===i.getQuery(i.currentValue)&&(i.suggestions=e.suggestions,i.suggest())},activate:function(e){var t=this,s=t.classes.selected,i=d(t.suggestionsContainer),n=i.find("."+t.classes.suggestion);return i.find("."+s).removeClass(s),t.selectedIndex=e,-1!==t.selectedIndex&&n.length>t.selectedIndex?(i=n.get(t.selectedIndex),d(i).addClass(s),i):null},selectHint:function(){var e=d.inArray(this.hint,this.suggestions);this.select(e)},select:function(e,t){d(t.target).hasClass("smart-search-add_to_cart")||(this.hide(),this.onSelect(e))},moveUp:function(){var e=this;-1!==e.selectedIndex&&(0===e.selectedIndex?(d(e.suggestionsContainer).children().first().removeClass(e.classes.selected),e.selectedIndex=-1,e.el.val(e.currentValue),e.findBestHint()):e.adjustScroll(e.selectedIndex-1))},moveDown:function(){this.selectedIndex!==this.suggestions.length-1&&this.adjustScroll(this.selectedIndex+1)},adjustScroll:function(e){var t,s,i,n=this,o=n.activate(e);o&&(t=d(o).outerHeight(),o=o.offsetTop,i=(s=d(n.suggestionsContainer).scrollTop())+n.options.maxHeight-t,o<s?d(n.suggestionsContainer).scrollTop(o):i<o&&d(n.suggestionsContainer).scrollTop(o-n.options.maxHeight+t),n.options.preserveInput||n.el.val(n.getValue(n.suggestions[e].value)),n.signalHint(null))},onSelect:function(e){var t=this,s=t.options.onSelect,e=t.suggestions[e];t.currentValue=t.getValue(e.value),t.currentValue===t.el.val()||t.options.preserveInput||t.el.val(t.currentValue),t.signalHint(null),t.suggestions=[],t.selection=e,d.isFunction(s)&&s.call(t.element,e)},getValue:function(e){var t,s=this.options.delimiter;return!s||1===(s=(t=this.currentValue).split(s)).length?e:t.substr(0,t.length-s[s.length-1].length)+e},dispose:function(){this.el.off(".autocomplete").removeData("autocomplete"),this.disableKillerFn(),d(window).off("resize.autocomplete",this.fixPositionCapture),d(this.suggestionsContainer).remove()}},d.fn.autocomplete=d.fn.devbridgeAutocomplete=function(s,i){var n="autocomplete";return 0===arguments.length?this.first().data(n):this.each(function(){var e=d(this),t=e.data(n);"string"==typeof s?t&&"function"==typeof t[s]&&t[s](i):(t&&t.dispose&&t.dispose(),t=new c(this,s),e.data(n,t))})}}),(t=>{"function"==typeof define&&define.amd?define(["jquery"],function(e){return t(e,window,document)}):"object"==typeof exports?module.exports=t(require("jquery"),window,document):t(jQuery,window,document)})(function(i,o,s){var a,c,n,h,t,u,d,p,r,l,g,f,v,m,w,S,x,y,C,b,T,H,I;function e(e,t){this.el=e,this.options=t,c=c||x(),this.$el=i(this.el),this.doc=i(this.options.documentContext||s),this.win=i(this.options.windowContext||o),this.body=this.doc.find("body"),this.$content=this.$el.children("."+this.options.contentClass),this.$content.attr("tabindex",this.options.tabIndex||0),this.content=this.$content[0],this.previousPosition=0,this.options.iOSNativeScrolling&&null!=this.el.style.WebkitOverflowScrolling?this.nativeScrolling():this.generate(),this.createEvents(),this.addEvents(),this.reset()}S={paneClass:"nano-pane",sliderClass:"nano-slider",contentClass:"nano-content",enabledClass:"has-scrollbar",flashedClass:"flashed",activeClass:"active",iOSNativeScrolling:!1,preventPageScrolling:!1,disableResize:!1,alwaysVisible:!1,flashDelay:1500,sliderMinHeight:20,sliderMaxHeight:null,documentContext:null,windowContext:null},f="scroll",t="mousedown",u="mouseenter",d="mousemove",r="mousewheel",p="mouseup",g="resize",m="up",n="DOMMouseScroll",h="down",v="touchmove",a="Microsoft Internet Explorer"===o.navigator.appName&&/msie 7./i.test(o.navigator.appVersion)&&o.ActiveXObject,c=null,b=o.requestAnimationFrame,w=o.cancelAnimationFrame,H=s.createElement("div").style,I=(()=>{for(var e,t=["t","webkitT","MozT","msT","OT"],s=e=0,i=t.length;e<i;s=++e)if(t[s],t[s]+"ransform"in H)return t[s].substr(0,t[s].length-1);return!1})(),T=function(e){return!1!==I&&(""===I?e:I+e.charAt(0).toUpperCase()+e.substr(1))}("transform"),y=!1!==T,x=function(){var e=s.createElement("div"),t=e.style;return t.position="absolute",t.width="100px",t.height="100px",t.overflow=f,t.top="-9999px",s.body.appendChild(e),t=e.offsetWidth-e.clientWidth,s.body.removeChild(e),t},C=function(){var e=o.navigator.userAgent,t=/(?=.+Mac OS X)(?=.+Firefox)/.test(e);return!!t&&(e=(e=/Firefox\/\d{2}\./.exec(e))&&e[0].replace(/\D+/g,""),t)&&23<+e},e.prototype.preventScrolling=function(e,t){this.isActive&&(e.type===n?(t===h&&0<e.originalEvent.detail||t===m&&e.originalEvent.detail<0)&&e.preventDefault():e.type===r&&e.originalEvent&&e.originalEvent.wheelDelta&&(t===h&&e.originalEvent.wheelDelta<0||t===m&&0<e.originalEvent.wheelDelta)&&e.preventDefault())},e.prototype.nativeScrolling=function(){this.$content.css({WebkitOverflowScrolling:"touch"}),this.iOSNativeScrolling=!0,this.isActive=!0},e.prototype.updateScrollValues=function(){var e=this.content;this.maxScrollTop=e.scrollHeight-e.clientHeight,this.prevScrollTop=this.contentScrollTop||0,this.contentScrollTop=e.scrollTop,e=this.contentScrollTop>this.previousPosition?"down":this.contentScrollTop<this.previousPosition?"up":"same",this.previousPosition=this.contentScrollTop,"same"!=e&&this.$el.trigger("update",{position:this.contentScrollTop,maximum:this.maxScrollTop,direction:e}),this.iOSNativeScrolling||(this.maxSliderTop=this.paneHeight-this.sliderHeight,this.sliderTop=0===this.maxScrollTop?0:this.contentScrollTop*this.maxSliderTop/this.maxScrollTop)},e.prototype.setOnScrollStyles=function(){var e,t;y?(e={})[T]="translate(0, "+this.sliderTop+"px)":e={top:this.sliderTop},b?(w&&this.scrollRAF&&w(this.scrollRAF),this.scrollRAF=b((t=this,function(){return t.scrollRAF=null,t.slider.css(e)}))):this.slider.css(e)},e.prototype.createEvents=function(){var t,s,i,n,o,r,l,a;this.events={down:function(e){return a.isBeingDragged=!0,a.offsetY=e.pageY-a.slider.offset().top,a.slider.is(e.target)||(a.offsetY=0),a.pane.addClass(a.options.activeClass),a.doc.bind(d,a.events.drag).bind(p,a.events.up),a.body.bind(u,a.events.enter),!1},drag:function(e){return l.sliderY=e.pageY-l.$el.offset().top-l.paneTop-(l.offsetY||.5*l.sliderHeight),l.scroll(),l.contentScrollTop>=l.maxScrollTop&&l.prevScrollTop!==l.maxScrollTop?l.$el.trigger("scrollend"):0===l.contentScrollTop&&0!==l.prevScrollTop&&l.$el.trigger("scrolltop"),!1},up:function(e){return r.isBeingDragged=!1,r.pane.removeClass(r.options.activeClass),r.doc.unbind(d,r.events.drag).unbind(p,r.events.up),r.body.unbind(u,r.events.enter),!1},resize:function(e){o.reset()},panedown:function(e){return n.sliderY=(e.offsetY||e.originalEvent.layerY)-.5*n.sliderHeight,n.scroll(),n.events.down(e),!1},scroll:function(e){i.updateScrollValues(),i.isBeingDragged||(i.iOSNativeScrolling||(i.sliderY=i.sliderTop,i.setOnScrollStyles()),null!=e&&(i.maxScrollTop<=i.contentScrollTop?(i.options.preventPageScrolling&&i.preventScrolling(e,h),i.prevScrollTop!==i.maxScrollTop&&i.$el.trigger("scrollend")):0===i.contentScrollTop&&(i.options.preventPageScrolling&&i.preventScrolling(e,m),0!==i.prevScrollTop)&&i.$el.trigger("scrolltop")))},wheel:function(e){if(null!=e)return(e=e.delta||e.wheelDelta||e.originalEvent&&e.originalEvent.wheelDelta||-e.detail||e.originalEvent&&-e.originalEvent.detail)&&(s.sliderY+=-e/3),s.scroll(),!1},enter:(t=s=i=n=o=r=l=a=this,function(e){return t.isBeingDragged&&1!==(e.buttons||e.which)?(e=t.events).up.apply(e,arguments):void 0})}},e.prototype.addEvents=function(){var e;this.removeEvents(),e=this.events,this.options.disableResize||this.win.bind(g,e[g]),this.iOSNativeScrolling||(this.slider.bind(t,e[h]),this.pane.bind(t,e.panedown).bind(r+" "+n,e.wheel)),this.$content.bind(f+" "+r+" "+n+" "+v,e[f])},e.prototype.removeEvents=function(){var e=this.events;this.win.unbind(g,e[g]),this.iOSNativeScrolling||(this.slider.unbind(),this.pane.unbind()),this.$content.unbind(f+" "+r+" "+n+" "+v,e[f])},e.prototype.generate=function(){var e,t,s=this.options,i=s.paneClass,n=s.sliderClass;return(t=this.$el.children("."+i)).length||t.children("."+n).length||this.$el.append('<div class="'+i+'"><div class="'+n+'" /></div>'),this.pane=this.$el.children("."+i),this.slider=this.pane.find("."+n),0===c&&C()?e={right:-14,paddingRight:+o.getComputedStyle(this.content,null).getPropertyValue("padding-right").replace(/[^0-9.]+/g,"")+14}:c&&(e={right:-c},this.$el.addClass(s.enabledClass)),null!=e&&this.$content.css(e),this},e.prototype.restore=function(){this.stopped=!1,this.iOSNativeScrolling||this.pane.show(),this.addEvents()},e.prototype.reset=function(){var e,t,s,i,n,o,r,l;if(!this.iOSNativeScrolling)return this.$el.find("."+this.options.paneClass).length||this.generate().stop(),this.stopped&&this.restore(),s=(t=(e=this.content).style).overflowY,a&&this.$content.css({height:this.$content.height()}),r=e.scrollHeight+c,0<(o=parseInt(this.$el.css("max-height"),10))&&(this.$el.height(""),this.$el.height(e.scrollHeight>o?o:e.scrollHeight)),i=(o=this.pane.outerHeight(!1))+(n=parseInt(this.pane.css("top"),10))+parseInt(this.pane.css("bottom"),10),(l=Math.round(i/r*o))<this.options.sliderMinHeight?l=this.options.sliderMinHeight:null!=this.options.sliderMaxHeight&&l>this.options.sliderMaxHeight&&(l=this.options.sliderMaxHeight),s===f&&t.overflowX!==f&&(l+=c),this.maxSliderTop=i-l,this.contentHeight=r,this.paneHeight=o,this.paneOuterHeight=i,this.sliderHeight=l,this.paneTop=n,this.slider.height(l),this.events.scroll(),this.pane.show(),this.isActive=!0,e.scrollHeight===e.clientHeight||this.pane.outerHeight(!0)>=e.scrollHeight&&s!==f?(this.pane.hide(),this.isActive=!1):this.el.clientHeight===e.scrollHeight&&s===f?this.slider.hide():this.slider.show(),this.pane.css({opacity:this.options.alwaysVisible?1:"",visibility:this.options.alwaysVisible?"visible":""}),"static"!==(t=this.$content.css("position"))&&"relative"!==t||(r=parseInt(this.$content.css("right"),10))&&this.$content.css({right:"",marginRight:r}),this;this.contentHeight=this.content.scrollHeight},e.prototype.scroll=function(){if(this.isActive)return this.sliderY=Math.max(0,this.sliderY),this.sliderY=Math.min(this.maxSliderTop,this.sliderY),this.$content.scrollTop(this.maxScrollTop*this.sliderY/this.maxSliderTop),this.iOSNativeScrolling||(this.updateScrollValues(),this.setOnScrollStyles()),this},e.prototype.scrollBottom=function(e){if(this.isActive)return this.$content.scrollTop(this.contentHeight-this.$content.height()-e).trigger(r),this.stop().restore(),this},e.prototype.scrollTop=function(e){if(this.isActive)return this.$content.scrollTop(+e).trigger(r),this.stop().restore(),this},e.prototype.scrollTo=function(e){if(this.isActive)return this.scrollTop(this.$el.find(e).get(0).offsetTop),this},e.prototype.stop=function(){return w&&this.scrollRAF&&(w(this.scrollRAF),this.scrollRAF=null),this.stopped=!0,this.removeEvents(),this.iOSNativeScrolling||this.pane.hide(),this},e.prototype.destroy=function(){return this.stopped||this.stop(),!this.iOSNativeScrolling&&this.pane.length&&this.pane.remove(),a&&this.$content.height(""),this.$content.removeAttr("tabindex"),this.$el.hasClass(this.options.enabledClass)&&(this.$el.removeClass(this.options.enabledClass),this.$content.css({right:""})),this},e.prototype.flash=function(){var e;if(!this.iOSNativeScrolling&&this.isActive)return this.reset(),this.pane.addClass(this.options.flashedClass),setTimeout(function(){e.pane.removeClass(e.options.flashedClass)},(e=this).options.flashDelay),this},l=e,i.fn.nanoScroller=function(s){return this.each(function(){var e,t;if((t=this.nanoscroller)||(e=i.extend({},S,s),this.nanoscroller=t=new l(this,e)),s&&"object"==typeof s){if(i.extend(t.options,s),null!=s.scrollBottom)return t.scrollBottom(s.scrollBottom);if(null!=s.scrollTop)return t.scrollTop(s.scrollTop);if(s.scrollTo)return t.scrollTo(s.scrollTo);if("bottom"===s.scroll)return t.scrollBottom(0);if("top"===s.scroll)return t.scrollTop(0);if(s.scroll&&s.scroll instanceof i)return t.scrollTo(s.scroll);if(s.stop)return t.stop();if(s.destroy)return t.destroy();if(s.flash)return t.flash()}return t.reset()})},i.fn.nanoScroller.Constructor=l});