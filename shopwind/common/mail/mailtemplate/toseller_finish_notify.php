<?php
return array (
  'version' => '1.0',
  'subject' => '{$site_name}提醒：买家确认了与您交易的订单{$order.order_sn}，交易完成',
  'content' => '<p class="mb10 bold">尊敬的{$order.seller_name}:</p>
<p>买家{$order.buyer_name}已经确认了与您交易的订单{$order.order_sn}。交易完成</p>
<p>查看订单详细信息请点击以下链接</p>
<p><a class="rlink" href="{$base_url}/seller/order/detail/{$order.order_id}" target="_blank">{$base_url}/seller/order/detail/{$order.order_id}</a></p>
<p>查看您的订单列表管理页请点击以下链接</p>
<p><a class="rlink" href="{$base_url}/seller/order/list" target="_blank">{$base_url}/seller/order/list</a></p>
<p class="f-gray f-12 bt pt10 mt20">{$site_name}</p>
<p class="f-gray f-12">{$send_time}</p>',
);