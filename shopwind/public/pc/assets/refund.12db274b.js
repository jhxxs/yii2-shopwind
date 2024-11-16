import{r as t}from"./blocks.3d7e5021.js";import{E as d}from"./index.25ac927e.js";/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 * 
 * @Id refund.js 2021.12.8 $
 * @author mosir
 */function u(f,n,i){t("refund/read",f,e=>{e.code==0&&(e.data.shipped&&(e.data.shipped=parseInt(e.data.shipped)),typeof n=="function"&&n(e.data))},i)}function s(f,n,i){t("refund/logs",f,e=>{e.code==0&&typeof n=="function"&&n(e.data)},i)}function p(f,n,i){t("my/refund/list",f,e=>{e.code==0&&typeof n=="function"&&n(e.data)},i)}function r(f,n,i){t("seller/refund/list",f,e=>{e.code==0?typeof n=="function"&&n(e.data):d.warning(e.message)},i)}function g(f,n,i){t("refund/create",f,e=>{e.code==0?typeof n=="function"&&n(e.data):d.warning(e.message)},i)}function c(f,n,i){t("refund/update",f,e=>{e.code==0?typeof n=="function"&&n(e.data):d.warning(e.message)},i)}function m(f,n,i){t("refund/cancel",f,e=>{e.code==0?typeof n=="function"&&n(e.data):d.warning(e.message)},i)}function y(f,n,i){t("refund/refuse",f,e=>{e.code==0?typeof n=="function"&&n(e.data):d.warning(e.message)},i)}function w(f,n,i){t("refund/agree",f,e=>{console.log(e),e.code==0?typeof n=="function"&&n(e.data):d.warning(e.message)},i)}export{g as a,c as b,m as c,s as d,y as e,w as f,p as m,u as r,r as s};
