import{r as t}from"./blocks.977b042b.js";import{E as o}from"./index.69005660.js";/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 * 
 * @Id sms.js 2021.10.25 $
 * @author mosir
 */function i(n,s){t("sms/send",n,e=>{e.code==0?(localStorage.setItem("smsverifycodekey",e.data.codekey),typeof s=="function"&&s(e.data)):o.warning(e.message)})}function m(n,s){t("sms/read",n,e=>{e.code==0?typeof s=="function"&&s(e.data):o.warning(e.message)})}function d(n,s){t("sms/update",n,e=>{e.code==0?typeof s=="function"&&s(e.data):o.warning(e.message)})}function c(n,s){t("sms/scene",n,e=>{e.code==0?typeof s=="function"&&s(e.data):o.warning(e.message)})}export{c as a,d as b,m as c,i as s};
