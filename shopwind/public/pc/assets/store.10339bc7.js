import{r as i}from"./blocks.3d7e5021.js";import{m as a,E as s}from"./index.25ac927e.js";/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 * 
 * @Id store.js 2022.3.25 $
 * @author mosir
 */function u(e,o,f,t){i("store/read",e,n=>{n.code==0&&(t&&n.data.state==2&&a("/message/result","fail","\u5E97\u94FA\u5173\u95ED","\u8BE5\u5E97\u94FA\u5DF2\u88AB\u5173\u95ED\uFF0C\u8BF7\u7A0D\u540E\u518D\u8BBF\u95EE"),typeof o=="function"&&o(n.data))},f)}function p(e,o,f){i("store/list",e,t=>{t.code==0&&typeof o=="function"&&o(t.data)},f)}function c(e,o,f){i("store/swiper",e,t=>{t.code==0&&typeof o=="function"&&o(t.data)},f)}function y(e,o,f){i("store/privacy",e,t=>{t.code==0&&typeof o=="function"&&o(t.data)},f)}function m(e,o,f){i("store/add",e,t=>{t.code==0?typeof o=="function"&&o(t.data):s.warning(t.message)},f)}function g(e,o,f){i("store/update",e,t=>{t.code==0?typeof o=="function"&&o(t.data):s.warning(t.message)},f)}function v(e,o,f){i("store/grades",e,t=>{t.code==0&&typeof o=="function"&&o(t.data)},f)}function w(e,o,f){i("store/dynamiceval",e,t=>{t.code==0&&typeof o=="function"&&o(t.data)},f)}function E(e,o,f){i("store/fullprefer",e,t=>{t.code==0&&typeof o=="function"&&o(t.data)},f)}export{w as a,g as b,c,v as d,m as e,y as f,E as g,p as h,u as s};
