import{r as o}from"./blocks.266145c0.js";import{E as n}from"./index.5367eff4.js";/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 * 
 * @Id cashier.js 2022.4.30 $
 * @author mosir
 */function c(a,e,f){o("cashier/build",a,i=>{i.code==0?typeof e=="function"&&e(i.data):n.warning(i.message)},f)}function r(a,e,f){o("cashier/pay",a,i=>{i.code==0?typeof e=="function"&&e(i.data):n.warning(i.message)},f)}function p(a,e,f){o("cashier/checkpay",a,i=>{i.code==0&&typeof e=="function"&&e(i.data)},f)}export{c as a,r as b,p as c};
