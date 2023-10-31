import{r as i}from"./blocks.266145c0.js";import{E as f}from"./index.5367eff4.js";/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 * 
 * @Id coupon.js 2022.3.4 $
 * @author mosir
 */function a(t,n,e){i("my/coupon/list",t,o=>{o.code==0&&typeof n=="function"&&n(o.data)},e)}function s(t,n,e){i("seller/coupon/list",t,o=>{o.code==0&&typeof n=="function"&&n(o.data)},e)}function d(t,n,e){i("coupon/add",t,o=>{o.code==0?typeof n=="function"&&n(o.data):f.warning(o.message)},e)}function c(t,n,e){i("coupon/update",t,o=>{o.code==0?typeof n=="function"&&n(o.data):f.warning(o.message)},e)}function m(t,n,e){i("coupon/delete",t,o=>{o.code==0?typeof n=="function"&&n(o.data):f.warning(o.message)},e)}export{d as a,m as b,c,a as m,s};
