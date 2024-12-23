import{E as b,u as w,r as l,x as g,b as y,D as x,d as u,j as s,o as f,g as k,h as _,f as C,a as m,c as B,t as I,k as p,m as L}from"./index.64a3f412.js";import{r,a as N}from"./blocks.216b8563.js";/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 * 
 * @Id webim.js 2022.11.25 $
 * @author mosir
 */function S(n,t,a){r("webim/list",n,e=>{e.code==0&&typeof t=="function"&&t(e.data)},a)}function j(n,t,a){r("webim/logs",n,e=>{e.code==0&&typeof t=="function"&&t(e.data)},a)}function q(n,t,a){r("webim/send",n,e=>{e.code==0?typeof t=="function"&&t(e.data):b.warning(e.message)},a)}function E(n,t,a){r("webim/unread",n,e=>{e.code==0&&typeof t=="function"&&t(e.data)},a)}const O={class:"relative"},R={key:0,class:"absolute unread f-10 f-white center"},V={setup(n){const t=w(),a=l({}),e=g({unread:0,lastid:0}),d=l(null);return y(()=>{a.value=JSON.parse(localStorage.getItem("visitor"))||{},a.value.userid&&t.path.indexOf("/webim/chat")<0&&(d.value=setInterval(()=>{E(null,c=>{c>e.unread&&(S(null,o=>{for(let i=0;i<o.length;i++)if(o[i].unreads>0&&o[i].to){e.lastid=o[i].to.userid;break}}),e.unread=c)})},5e3))}),x(()=>{clearInterval(d.value)}),(c,o)=>{const i=u("ChatLineRound"),v=u("el-icon"),h=u("el-backtop");return s(t).path.indexOf("/webim/chat")<0?(f(),k(h,{key:0,onClick:o[0]||(o[0]=D=>s(L)("/webim/chat"+(s(e).lastid>0?"/"+s(e).lastid:""))),right:10,bottom:150,"visibility-height":0},{default:_(()=>[C("div",O,[m(v,null,{default:_(()=>[m(i)]),_:1}),s(e).unread>0?(f(),B("span",R,I(s(e).unread),1)):p("",!0)])]),_:1})):p("",!0)}}};var J=N(V,[["__scopeId","data-v-6a2a05ba"]]);export{S as a,j as b,J as c,q as w};
