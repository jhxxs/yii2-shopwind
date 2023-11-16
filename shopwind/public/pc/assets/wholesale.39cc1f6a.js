import{r as f}from"./blocks.ccf7c496.js";import{E as i}from"./index.d9c42d52.js";/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 * 
 * @Id wholesale.js 2021.11.25 $
 * @author mosir
 */function l(t,o,a){f("seller/wholesale/list",t,e=>{e.code==0&&typeof o=="function"&&o(e.data)},a)}function d(t,o,a){f("wholesale/read",t,e=>{e.code==0&&typeof o=="function"&&o(e.data)},a)}function u(t,o,a){f("wholesale/update",t,e=>{e.code==0?typeof o=="function"&&o(e.data):i.warning(e.message)},a)}function w(t,o,a){f("wholesale/delete",t,e=>{e.code==0?typeof o=="function"&&o(e.data):i.warning(e.message)},a)}export{d as a,u as b,l as s,w};
