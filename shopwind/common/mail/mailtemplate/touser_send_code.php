<?php
return array (
  'version' => '1.0',
  'subject' => '{$site_name}提醒：邮箱验证(系统邮件，请勿回复）',
  'content' => '<p>您好！感谢您使用{$site_name}。</p>
<p>您正在进行账户基础信息维护，校验码：{$word}</p>
<p><b style="color:red">注意：</b>此操作可能会修改您的密码、登录邮箱或绑定手机。如非本人操作，请及时登录并修改密码以保证账户安全。 （工作人员不会向您索取此校验码，请勿泄漏！）</p>
<p class="f-gray f-12 bt pt10 mt20">{$site_name}</p>
<p class="f-gray f-12">{$send_time}</p>',
);