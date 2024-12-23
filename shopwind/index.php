<!doctype html>
<html lang="zh-CN">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <meta name="viewport" content="width=device-width" />
  <style type="text/css">
    body,
    p,
    h4 {
      padding: 0;
      margin: 0;
      font-size: 14px;
    }

    body {
      background-color: #f1f1f1;
    }

    div,
    pre {
      padding: 30px;
      border-radius: 10px;
    }

    div {
      background-color: #fff;
      width: 800px;
      margin: 30px auto;
      line-height: 25px;
    }

    pre {
      line-height: 18px;
      background-color: #f1f1f1;
      margin-bottom: 5px;
      font-size: 12px;
      font-family: 'yahei';
    }

    span {
      color: #999;
      font-size: 13px;
    }

    h4 {
      color: red;
    }
  </style>
</head>

<body>
  <div>
    <h4>请将域名解析至目录：public/</h4>
    <p>如果是Nginx服务器，请设置伪静态规则，规则如下：</p>
    <pre>location /admin {
  try_files $uri $uri/ /admin/index.php$is_args$args;
}

location /home {
  try_files $uri $uri/ /home/index.php$is_args$args;
}

location /mob {
  try_files $uri $uri/ /mob/index.php$is_args$args;
}

location /install {
  try_files $uri $uri/ /install/index.php$is_args$args;
}

location /api {
  try_files $uri $uri/ /api/index.php$is_args$args;
}

location /h5 {
  try_files $uri $uri/ /h5/index.html;
}

location / {
  try_files $uri $uri/ /index.php$is_args$args;
}</pre><span>注：其他Web环境的伪静态规则查看 根目录/*.rewrite</span>
  </div>
</body>

</html>