import{r as i}from"./blocks.862945f8.js";import{E as f}from"./index.537ec318.js";/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 * 
 * @Id bank.js 2022.3.30 $
 * @author mosir
 */function s(t,e,a){i("bank/list",t,n=>{n.code==0&&typeof e=="function"&&e(n.data)},a)}function u(t,e,a){i("bank/add",t,n=>{n.code==0?typeof e=="function"&&e(n.data):f.warning(n.message)},a)}function m(t,e,a){i("bank/delete",t,n=>{n.code==0?typeof e=="function"&&e(n.data):f.warning(n.message)},a)}export{m as a,s as b,u as c};
