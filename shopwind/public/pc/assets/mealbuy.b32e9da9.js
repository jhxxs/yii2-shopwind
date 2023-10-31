import{r as i}from"./blocks.266145c0.js";import{E as n}from"./index.5367eff4.js";/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 * 
 * @Id mealbuy.js 2021.11.05 $
 * @author mosir
 */function d(t,a,f){i("seller/mealbuy/list",t,e=>{e.code==0&&typeof a=="function"&&a(e.data)},f)}function m(t,a,f){i("mealbuy/read",t,e=>{e.code==0&&typeof a=="function"&&a(e.data)},f)}function s(t,a,f){i("mealbuy/add",t,e=>{e.code==0?typeof a=="function"&&a(e.data):n.warning(e.message)},f)}function y(t,a,f){i("mealbuy/update",t,e=>{e.code==0?typeof a=="function"&&a(e.data):n.warning(e.message)},f)}function l(t,a,f){i("mealbuy/delete",t,e=>{e.code==0?typeof a=="function"&&a(e.data):n.warning(e.message)},f)}export{m as a,y as b,s as c,l as m,d as s};
