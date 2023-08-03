import{r as i}from"./blocks.bc527001.js";import{m as f,a0 as c,E as n}from"./index.5aeb4455.js";/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 * 
 * @Id cart.js 2022.5.30 $
 * @author mosir
 */function r(o,t,a){i("cart/list",o,e=>{e.code==0&&typeof t=="function"&&t(e.data)},a)}function u(o,t,a){i("cart/add",o,e=>{e.code==0?typeof t=="function"&&t(e.data):e.code==4004?f("/user/login?redirect="+encodeURIComponent(c())):n.warning(e.message)},a)}function m(o,t,a){i("cart/update",o,e=>{e.code==0?typeof t=="function"&&t(e.data):n.warning(e.message)},a)}function g(o,t,a){i("cart/remove",o,e=>{e.code==0?typeof t=="function"&&t(e.data):n.warning(e.message)},a)}function p(o,t,a){i("cart/chose",o,e=>{e.code==0?typeof t=="function"&&t(e.data):n.warning(e.message)},a)}export{r as a,m as b,u as c,g as d,p as e};
