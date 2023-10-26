<?php
return array (
  'version' => '1.0',
  'subject' => '{$site_name}提醒：您的店铺【{$store.store_name}】已被关闭',
  'content' => '<p class="mb10 bold">尊敬的{$store.owner_name}:</p>
<p>您的店铺【{$store.store_name}】因为【{$reason}】被平台关闭，如有疑问，请联系客服。</p>
<p class="f-gray f-12 bt pt10 mt20">{$site_name}</p>
<p class="f-gray f-12">{$send_time}</p>',
);