import{E as h,u as v,r as b,x as w,b as g,d,j as s,o as u,g as y,h as f,f as x,a as l,c as k,t as C,k as _,m as B}from"./index.25ac927e.js";import{r,a as L}from"./blocks.3d7e5021.js";/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 * 
 * @Id webim.js 2022.11.25 $
 * @author mosir
 */function N(o,t,a){r("webim/list",o,e=>{e.code==0&&typeof t=="function"&&t(e.data)},a)}function j(o,t,a){r("webim/logs",o,e=>{e.code==0&&typeof t=="function"&&t(e.data)},a)}function q(o,t,a){r("webim/send",o,e=>{e.code==0?typeof t=="function"&&t(e.data):h.warning(e.message)},a)}function S(o,t,a){r("webim/unread",o,e=>{e.code==0&&typeof t=="function"&&t(e.data)},a)}const E={class:"relative"},I={key:0,class:"absolute unread f-10 f-white center"},O={setup(o){const t=v(),a=b({}),e=w({unread:0,lastid:0});return g(()=>{a.value=JSON.parse(localStorage.getItem("visitor"))||{},a.value.userid&&t.path.indexOf("/webim/chat")<0&&setInterval(()=>{S(null,c=>{c>e.unread&&(N(null,n=>{for(let i=0;i<n.length;i++)if(n[i].unreads>0&&n[i].to){e.lastid=n[i].to.userid;break}}),e.unread=c)})},4e3)}),(c,n)=>{const i=d("ChatLineRound"),m=d("el-icon"),p=d("el-backtop");return s(t).path.indexOf("/webim/chat")<0?(u(),y(p,{key:0,onClick:n[0]||(n[0]=R=>s(B)("/webim/chat"+(s(e).lastid>0?"/"+s(e).lastid:""))),right:10,bottom:150,"visibility-height":0},{default:f(()=>[x("div",E,[l(m,null,{default:f(()=>[l(i)]),_:1}),s(e).unread>0?(u(),k("span",I,C(s(e).unread),1)):_("",!0)])]),_:1})):_("",!0)}}};var D=L(O,[["__scopeId","data-v-3003a77a"]]);export{N as a,j as b,D as c,q as w};
