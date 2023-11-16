import{r as i}from"./blocks.ccf7c496.js";import{E as n}from"./index.d9c42d52.js";/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 * 
 * @Id teambuy.js 2021.11.05 $
 * @author mosir
 */function d(a,t,f){i("seller/teambuy/list",a,e=>{e.code==0&&typeof t=="function"&&t(e.data)},f)}function m(a,t,f){i("teambuy/read",a,e=>{e.code==0&&typeof t=="function"&&t(e.data)},f)}function s(a,t,f){i("teambuy/add",a,e=>{e.code==0?typeof t=="function"&&t(e.data):n.warning(e.message)},f)}function y(a,t,f){i("teambuy/update",a,e=>{e.code==0?typeof t=="function"&&t(e.data):n.warning(e.message)},f)}function p(a,t,f){i("teambuy/delete",a,e=>{e.code==0?typeof t=="function"&&t(e.data):n.warning(e.message)},f)}export{m as a,y as b,s as c,d as s,p as t};
