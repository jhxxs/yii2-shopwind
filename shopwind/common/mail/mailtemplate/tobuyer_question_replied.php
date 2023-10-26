<?php
return array (
  'version' => '1.0',
  'subject' => '{$site_name}提醒：{$user.username}您的{$type}咨询已得到回复',
  'content' => '<p class="mb10 bold">尊敬的用户:</p>
<p>您好, 您在 {$site_name} 中的“{$item_name}”咨询已得到回复，请点击下面的链接查看：</p>
<p><a class="rlink" href="{$base_url}/my/mailbox/list" target="_blank">{$base_url}/my/mailbox/list</a></p>
<p> 如果以上链接无法点击，请将它拷贝到浏览器(例如IE)的地址栏中。</p>
<p class="f-gray f-12 bt pt10 mt20">{$site_name}</p>
<p class="f-gray f-12">{$send_time}</p>',
);