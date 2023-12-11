import{r as n}from"./blocks.b3d78275.js";import{E as o}from"./index.521f67fd.js";/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 * 
 * @Id delivery.js 2021.10.25 $
 * @author mosir
 */function y(t,i,f){n("delivery/read",t,e=>{e.code==0&&typeof i=="function"&&i(e.data)},f)}function p(t,i,f){n("delivery/company",t,e=>{e.code==0&&typeof i=="function"&&i(e.data)},f)}function u(t,i,f){n("delivery/template",t,e=>{e.code==0&&typeof i=="function"&&i(e.data)},f)}function r(t,i,f){n("delivery/update",t,e=>{e.code==0?typeof i=="function"&&i(e.data):o.warning(e.message)},f)}function m(t,i,f){n("delivery/delete",t,e=>{e.code==0?typeof i=="function"&&i(e.data):o.warning(e.message)},f)}function s(t,i,f){n("delivery/timer",t,e=>{e.code==0&&typeof i=="function"&&i(e.data)},f)}function v(t,i,f){n("delivery/timerupdate",t,e=>{e.code==0?typeof i=="function"&&i(e.data):o.warning(e.message)},f)}export{y as a,r as b,p as c,m as d,s as e,v as f,u as t};
