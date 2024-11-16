import{r as i}from"./blocks.3d7e5021.js";import{E as n}from"./index.25ac927e.js";/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 * 
 * @Id favorite.js 2022.3.30 $
 * @author mosir
 */function d(e,t,f){i("favorite/goods",e,o=>{o.code==0&&typeof t=="function"&&t(o.data)},f)}function a(e,t,f){i("favorite/store",e,o=>{o.code==0&&typeof t=="function"&&t(o.data)},f)}function u(e,t,f){i("goods/collect",e,o=>{o.code==0?typeof t=="function"&&t(o.data):n.warning(o.message)},f)}function r(e,t,f){e.remove=!0,i("goods/collect",e,o=>{o.code==0?typeof t=="function"&&t(o.data):n.warning(o.message)},f)}function g(e,t,f){i("store/collect",e,o=>{o.code==0?typeof t=="function"&&t(o.data):n.warning(o.message)},f)}function l(e,t,f){e.remove=!0,i("store/collect",e,o=>{o.code==0?typeof t=="function"&&t(o.data):n.warning(o.message)},f)}export{a,l as b,g as c,u as d,d as f,r as u};
