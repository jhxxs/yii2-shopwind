<?php
return array (
  'version' => '1.0',
  'subject' => '{$site_name}提醒：您的商品【{$goods.goods_name}】已被删除',
  'content' => '<p class="mb10 bold">尊敬的{$store.store_name}:</p>
<p>您的商品【{$goods.goods_name}】因为【{$reason}】被平台删除，如有疑问，请联系客服。</p>
<p>查看您目前在售的商品请点击以下链接</p>
<p><a class="rlink" href="{$base_url}/seller/goods/list" target="_blank">{$base_url}/seller/goods/list</a></p>
<p class="f-gray f-12 bt pt10 mt20">{$site_name}</p>
<p class="f-gray f-12">{$send_time}</p>',
);