<?php
return array (
  'version' => '1.0',
  'subject' => '{$site_name}提醒：您的订单{$order.order_sn}已被取消',
  'content' => '<p class="mb10 bold">尊敬的{$order.buyer_name}:</p>
<p>与您交易的店铺{$order.seller_name}已经取消了您的订单{$order.order_sn}。</p>
<p>{if $reason}原因：{$reason|escape}{/if}</p>
<p>查看订单详细信息请点击以下链接</p>
<p><a class="rlink" href="{$base_url}/my/order/detail/{$order.order_id}" target="_blank">{$base_url}/my/order/detail/{$order.order_id}</a></p>
<p class="f-gray f-12 bt pt10 mt20">{$site_name}</p>
<p class="f-gray f-12">{$send_time}</p>',
);