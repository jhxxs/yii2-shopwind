(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-article-detail"],{"0a9d":function(t,a,e){"use strict";var i=e("d0a0"),n=e.n(i);n.a},"0b0a":function(t,a,e){"use strict";e.d(a,"b",(function(){return n})),e.d(a,"c",(function(){return r})),e.d(a,"a",(function(){return i}));var i={mpHtml:e("5eef").default},n=function(){var t=this.$createElement,a=this._self._c||t;return a("v-uni-view",{staticClass:"pd10 ml10 mr10 mt10 mb10"},[a("v-uni-view",[a("mp-html",{staticClass:"detail-info",attrs:{content:this.article.description}})],1)],1)},r=[]},"877b":function(t,a,e){"use strict";e.r(a);var i=e("c103"),n=e.n(i);for(var r in i)["default"].indexOf(r)<0&&function(t){e.d(a,t,(function(){return i[t]}))}(r);a["default"]=n.a},aa16:function(t,a,e){"use strict";var i=e("f5bd").default,n=i(e("8e1d"));t.exports={article:
/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 *
 * @Id detail.js 2019.11.20 $
 * @author winder
 */
function(t){n.default.request("article/read",{article_id:t.id},(function(a){0==a.code&&(t.article=a.data||{},uni.setNavigationBarTitle({title:t.article.title}))}))}}},c103:function(t,a,e){"use strict";e("6a54");var i=e("f5bd").default;Object.defineProperty(a,"__esModule",{value:!0}),a.default=void 0;var n=i(e("aa16")),r={data:function(){return{id:0,article:{}}},onLoad:function(t){this.id=t.id,n.default.article(this)}};a.default=r},d0a0:function(t,a,e){var i=e("f09f");i.__esModule&&(i=i.default),"string"===typeof i&&(i=[[t.i,i,""]]),i.locals&&(t.exports=i.locals);var n=e("967d").default;n("746ebf4c",i,!0,{sourceMap:!1,shadowMode:!1})},f09f:function(t,a,e){var i=e("c86c");a=i(!1),a.push([t.i,"uni-page-body[data-v-e18ae27c]{background-color:#fff}body.?%PAGE?%[data-v-e18ae27c]{background-color:#fff}\n\n/* 消除描述中图片上下间距 */[data-v-e18ae27c] .detail-info img{display:block}",""]),t.exports=a},f79d:function(t,a,e){"use strict";e.r(a);var i=e("0b0a"),n=e("877b");for(var r in n)["default"].indexOf(r)<0&&function(t){e.d(a,t,(function(){return n[t]}))}(r);e("0a9d");var c=e("828b"),d=Object(c["a"])(n["default"],i["b"],i["c"],!1,null,"e18ae27c",null,!1,i["a"],void 0);a["default"]=d.exports}}]);