# TelePic

这是一个基于 Telegraph 网站的图床项目。它使用 PHP 和 jQuery 编写，可以将图片上传到 Telegraph 服务器并获取代理后的链接。

## 如何使用

1. 将代码部署到 Web 服务器上。
2. 配置服务器环境（Apache 或 Nginx），使 `/file/` 路径映射到 `proxy.php` 文件。
3. 打开 `index.html` 文件即可使用。

### Apache 配置示例

在 Apache 的配置文件或者 `.htaccess` 文件中添加以下代码：

```apacheconf
RewriteEngine On
RewriteRule ^file/(.*)$ proxy.php [L]
```


### Nginx 配置示例

在 Nginx 的配置文件中添加以下代码：

```nginx
location /file/ {
    rewrite ^/file/(.*)$ /proxy.php?$query_string;
}
```

其中 `$query_string` 是 GET 请求参数部分，保证在代理时能够正确传递参数。

## 功能介绍

- 支持选择本地图片上传或复制粘贴板中的图片。
- 支持代理 HTTP/HTTPS 协议的图片。
- 支持复制原始图片链接和代理后的图片链接到粘贴板。

## 注意事项

- 图片上传至 Telegraph 服务器，无法保证私密性及安全性
- 图片上传成功后，需要等待 Telegraph 服务器处理图片才能获取到代理后的图片链接。


## Stargazers over time

[![Stargazers over time](https://starchart.cc/Fangsongs/TelePic.svg)](https://starchart.cc/Fangsongs/TelePic)
