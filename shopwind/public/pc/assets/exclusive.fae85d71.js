import{r as a}from"./blocks.3cd43e63.js";import{E as f}from"./index.56b7a5ab.js";/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 * 
 * @Id exclusive.js 2021.10.30 $
 * @author mosir
 */function u(t,i,o){a("exclusive/read",t,e=>{e.code==0&&typeof i=="function"&&i(e.data)},o)}function d(t,i,o){a("exclusive/update",t,e=>{e.code==0?typeof i=="function"&&i(e.data):f.warning(e.message)},o)}export{d as a,u as e};
