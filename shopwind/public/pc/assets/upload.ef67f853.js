import{u as n}from"./blocks.b3d78275.js";import{E as d}from"./index.521f67fd.js";/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 * 
 * @Id upload.js 2021.10.25 $
 * @author mosir
 */function s(e,o,i,f){n("upload/file",e,Object.assign({fileval:"file"},o),a=>{a.code==0?typeof i=="function"&&i(a.data):d.warning(a.message)},f)}function u(e,o,i,f){n("upload/add",e,Object.assign({fileval:"file"},o),a=>{a.code==0?typeof i=="function"&&i(a.data):d.warning(a.message)},f)}export{u as a,s as u};
