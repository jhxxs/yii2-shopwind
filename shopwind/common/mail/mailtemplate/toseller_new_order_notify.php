<?php
return array (
  'version' => '1.0',
  'subject' => '{$site_name}提醒：您有一个新订单需要处理',
  'content' => '<p class="mb10 bold">尊敬的{$order.seller_name}:</p>
<p>您有一个新的订单需要处理，订单号{$order.order_sn}，请尽快处理。</p>
<p>查看订单详细信息请点击以下链接</p>
<p><a class="rlink" href="{$base_url}/seller/order/detail/{$order.order_id}" target="_blank">{$base_url}/seller/order/detail/{$order.order_id}</a></p>
<p>查看您的订单列表管理页请点击以下链接</p>
<p><a class="rlink" href="{$base_url}/seller/order/list" target="_blank">{$base_url}/seller/order/list</a></p>
<p class="f-gray f-12 bt pt10 mt20">{$site_name}</p>
<p class="f-gray f-12">{$send_time}</p>',
);