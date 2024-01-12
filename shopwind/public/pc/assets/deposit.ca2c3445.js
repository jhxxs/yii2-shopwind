import{r as d}from"./blocks.691b1705.js";import{E as f}from"./index.568a8f62.js";/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 * 
 * @Id deposit.js 2021.11.30 $
 * @author mosir
 */function a(i,e,o){d("deposit/read",i,t=>{t.code==0&&typeof e=="function"&&e(t.data)},o)}function p(i,e,o){i.verifycodekey=localStorage.getItem("smsverifycodekey"),d("deposit/update",i,t=>{t.code==0?typeof e=="function"&&e(t.data):f.warning(t.message)},o)}function u(i,e,o){d("deposit/trade",i,t=>{t.code==0&&typeof e=="function"&&e(t.data)},o)}function c(i,e,o){d("deposit/tradelist",i,t=>{t.code==0&&typeof e=="function"&&e(t.data)},o)}function g(i,e,o){d("deposit/recordlist",i,t=>{t.code==0&&typeof e=="function"&&e(t.data)},o)}function r(i,e,o){d("deposit/recharge",i,t=>{t.code==0?typeof e=="function"&&e(t.data):f.warning(t.message)},o)}function y(i,e,o){d("deposit/drawal",i,t=>{t.code==0?typeof e=="function"&&e(t.data):f.warning(t.message)},o)}function m(i,e,o){d("deposit/setting",i,t=>{t.code==0&&typeof e=="function"&&e(t.data)},o)}export{c as a,p as b,g as c,a as d,u as e,r as f,m as g,y as h};
