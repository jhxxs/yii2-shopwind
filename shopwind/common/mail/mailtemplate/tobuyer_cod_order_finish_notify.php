<?php
return array (
  'version' => '1.0',
  'subject' => '{$site_name}提醒：店铺{$order.seller_name}确认收到了您的货款，交易完成！',
  'content' => '<p class="mb10 bold">尊敬的{$order.buyer_name}:</p>
<p>与您交易的店铺{$order.seller_name}已经确认收到了您的货到付款订单{$order.order_sn}的付款，交易完成！您可以到用户中心-&gt;我的订单中对该交易进行评价。</p>
<p>查看订单详细信息请点击以下链接</p>
<p><a href="{$base_url}/my/order/detail/{$order.order_id}" target="_blank">{$base_url}/my/order/detail/{$order.order_id}</a></p>
<p>查看我的订单列表请点击以下链接</p>
<p><a class="rlink" href="{$base_url}/my/order/list" target="_blank">{$base_url}/my/order/list</a></p>
<p class="f-gray f-12 bt pt10 mt20">{$site_name}</p>
<p class="f-gray f-12">{$send_time}</p>',
);