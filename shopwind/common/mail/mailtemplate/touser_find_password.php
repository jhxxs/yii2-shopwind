<?php
return array (
  'version' => '1.0',
  'subject' => '{$site_name}提醒：{$user.username}修改密码设置',
  'content' => '<p class="mb10 bold">尊敬的{$user.username}:</p>
<p>您好, 您刚才在 {$site_name} 申请了重置密码，请点击下面的链接进行重置：</p>
<p><a class="rlink" href="{$base_url}/my/setting/password" target="_blank">{$base_url}/my/setting/password</a></p>
<p>此链接只能使用一次, 如果失效请重新申请. 如果以上链接无法点击，请将它拷贝到浏览器(例如IE)的地址栏中。</p>
<p class="f-gray f-12 bt pt10 mt20">{$site_name}</p>
<p class="f-gray f-12">{$send_time}</p>',
);