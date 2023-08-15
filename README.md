# TelePic

这是一个基于 Telegraph 网站的图床项目。它使用 PHP 和 jQuery 编写，可以将图片上传到 Telegraph 服务器并获取代理后的链接。

通过Nginx Basic HTTP authentication实现了只有特定用户才能上传图片，通过SQLite3实现只有被从本域名上传的图片才能被访问，从而避免了被滥用和被偷盗域名链接。

- 滥用：任意用户都可以上传任意图片
- 偷盗域名链接：将telegra.ph的图片URL改成此图床的域名，无法管控，如下例子

```
https://telegra.ph/file/731ee38f95a6eb0f69833.jpg # 这是直接上传到telegra.ph的图片
https://image.196629.xyz/file/731ee38f95a6eb0f69833.jpg # 修改域名即可访问，域名被盗窃
```


## 如何使用

1. 将代码部署到 Web 服务器上。
2. 配置服务器环境（Apache 或 Nginx），使 `/file/` 路径映射到 `proxy.php` 文件。
3. 检查PHP拓展 cURL （即 `php-curl`）是否开启。
4. 按照下列SQLite 配置教程进行设置
5. 按照下列Nginx Basic HTTP authentication配置教程进行设置
6. 打开 `index.html` 文件即可使用。

### SQLite 配置教程

以下教程基于Debian和Nginx为例。注意备份数据库，由于采用了sqlite3，数据库为单文件，直接复制到安全位置进行保存即可。

首先，安装sqlite3和php-sqlite3
```
apt install sqlite3
apt install php-sqlite3
```

新建一个sqlite数据库
```
sqlite3 uploads.db # 新建并进入数据库
.exit # 在数据库内输入以退出
```
设置权限，示例路径为`/path/to`，需要更改为实际路径
```
chmod 664 /path/to/uploads.db
chmod 775 /path/to
chown www-data:www-data /path/to/uploads.db
chown www-data:www-data /path/to
```
修改`upload.php`和`proxy.php`中的数据库路径。此两个文件中，文件靠前位置包含如下，需将`path/to/`修改为实际位置。
```
// Connect to the SQLite database
$db = new SQLite3('/path/to/uploads.db'); 
```
如果需要查看数据库内的内容，可以使用`SELECT * FROM uploads;`查看。

### Nginx Basic HTTP authentication 配置教程

安装htpasswd软件
```
apt install apache2-utils
```
生成访问密码
```
htpasswd -c /etc/nginx/password username # 此处的username可任意设置
```
首次生成需要添加-c，此后如果想添加更多的用户，则直接：
```
htpasswd /etc/nginx/password another_username
```
编辑网站的Nginx配置文件
个人习惯将网站的Nginx配置文件放到`/etc/nginx/sites-available`中，并将其软链接到`/etc/nginx/sites-enabled`中，此处以默认配置文件`/etc/nginx/sites-available/default`为例。
找到`location /`位置，添加`auth_basic "Restricted Content";`和`auth_basic_user_file /etc/nginx/.htpasswd;`两行配置，实际如下：
```
location / {
    auth_basic "Restricted Content";
    auth_basic_user_file /etc/nginx/password;
    try_files $uri $uri/ =404;
}
```

### 权限设置

修改`upload.php`和`proxy.php`中的权限，用户需要为`www-data`，权限需要为`755`，即

```
chown -R www-data:www-data /path/to # 这里的/path/to一般是/var/ww/html，必须按照实际情况进行配置
chmod -R 755 /path/to # 注意/path/to
```


### Nginx 反代配置示例

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
