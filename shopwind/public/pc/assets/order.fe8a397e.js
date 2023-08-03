import{r as f}from"./blocks.bc527001.js";import{E as n}from"./index.5aeb4455.js";/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 * 
 * @Id order.js 2021.11.5 $
 * @author mosir
 */function a(t,o,i){f("order/read",t,e=>{e.code==0&&typeof o=="function"&&o(e.data)},i)}function u(t,o,i){f("order/goods",t,e=>{e.code==0&&typeof o=="function"&&o(e.data)},i)}function s(t,o,i){f("order/extm",t,e=>{e.code==0&&typeof o=="function"&&o(e.data)},i)}function y(t,o,i){f("order/logistic",t,e=>{e.code==0&&typeof o=="function"&&o(e.data)},i)}function c(t,o,i){f("order/qrcode",t,e=>{console.log(e),e.code==0&&typeof o=="function"&&o(e.data)},i)}function p(t,o,i){f("my/order/list",t,e=>{e.code==0&&typeof o=="function"&&o(e.data)},i)}function m(t,o,i){f("my/order/remind",t,e=>{e.code==0&&typeof o=="function"&&o(e.data)},i)}function g(t,o,i){f("seller/order/list",t,e=>{e.code==0?typeof o=="function"&&o(e.data):n.warning(e.message)},i)}function v(t,o,i){f("seller/order/remind",t,e=>{e.code==0&&typeof o=="function"&&o(e.data)},i)}function E(t,o,i){f("order/update",t,e=>{e.code==0?typeof o=="function"&&o(e.data):n.warning(e.message)},i)}function O(t,o,i){f("order/evaluate",t,e=>{e.code==0?typeof o=="function"&&o(e.data):n.warning(e.message)},i)}function w(t,o,i){f("order/replyevaluate",t,e=>{e.code==0?typeof o=="function"&&o(e.data):n.warning(e.message)},i)}function l(t,o,i){f("my/order/evaluates",t,e=>{e.code==0&&typeof o=="function"&&o(e.data)},i)}function R(t,o,i){f("seller/order/evaluates",t,e=>{e.code==0&&typeof o=="function"&&o(e.data)},i)}function x(t,o,i){f("order/build",t,e=>{e.code==0&&typeof o=="function"&&o(e.data)},i)}function L(t,o,i){f("order/create",t,e=>{e.code==0?typeof o=="function"&&o(e.data):n.warning(e.message)},i)}export{p as a,a as b,s as c,u as d,c as e,y as f,O as g,l as h,g as i,R as j,w as k,x as l,m,L as n,E as o,v as s};
