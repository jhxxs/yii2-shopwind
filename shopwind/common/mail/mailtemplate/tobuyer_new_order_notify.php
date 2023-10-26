<?php
return array(
  'version' => '1.0',
  'subject' => '{$site_name}提醒：您的订单已生成',
  'content' => '<p class="mb10 bold">尊敬的{$order.buyer_name}:</p>
<p>您在{$site_name}上下的订单已生成，订单号{$order.order_sn}。</p>
<p>查看订单详细信息请点击以下链接</p>
<p><a class="rlink" href="{$base_url}/my/order/detail/{$order.order_id}" target="_blank">{$base_url}/my/order/detail/{$order.order_id}</a></p>
<p class="f-gray f-12 bt pt10 mt20">{$site_name}</p>
<p class="f-gray f-12">{$send_time}</p>',
);
