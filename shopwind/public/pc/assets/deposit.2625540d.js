import{r as d}from"./blocks.8ce53700.js";import{E as f}from"./index.62034e5b.js";/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 * 
 * @Id deposit.js 2021.11.30 $
 * @author mosir
 */function s(i,t,o){d("deposit/read",i,e=>{e.code==0&&typeof t=="function"&&t(e.data)},o)}function p(i,t,o){i.verifycodekey=localStorage.getItem("smsverifycodekey"),d("deposit/update",i,e=>{e.code==0?typeof t=="function"&&t(e.data):f.warning(e.message)},o)}function u(i,t,o){d("deposit/trade",i,e=>{e.code==0&&typeof t=="function"&&t(e.data)},o)}function c(i,t,o){d("deposit/tradelist",i,e=>{e.code==0&&typeof t=="function"&&t(e.data)},o)}function r(i,t,o){d("deposit/recordlist",i,e=>{e.code==0&&typeof t=="function"&&t(e.data)},o)}function g(i,t,o){d("deposit/recharge",i,e=>{e.code==0?typeof t=="function"&&t(e.data):f.warning(e.message)},o)}function y(i,t,o){d("deposit/drawal",i,e=>{e.code==0?typeof t=="function"&&t(e.data):f.warning(e.message)},o)}export{c as a,p as b,r as c,s as d,u as e,g as f,y as g};
