# inphp
php服务框架


## Nginx 站点配置
### 1. 使用PHP-FPM
```apacheconfig
server{
    #端口
    listen 80;
    #绑定域名
    server_name www.xxxx.com;
    #站点位置
    root /框架根目录/public;
    #默认文件
    index index.php;
    #静态文件
    location ~ \.(jpg|png|jpeg|gif|bmp|woff2|zip|doc|docx|xsl|xslx|pdf|ppt|pptx)$ {
        try_files $uri @s404;
    }
    #404
    location @s404{
        return 404;
    }
    #伪静态
    if (!-e $request_filename) {
        rewrite  ^(.*)$  /index.php/$1 last;
        break;
    }
    #PHP
    location ~ \.php {
        include snippets/fastcgi-php.conf;
        fastcgi_split_path_info ^(.+\.php)(.*)$;     #增加这一句
        fastcgi_param PATH_INFO $fastcgi_path_info;    #增加这一句
        # 使用 fast-cgi
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_index  index.php;
        # 使用 php7.2-fpm:
        #fastcgi_pass unix:/run/php/php7.2-fpm.sock;
        fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
        include        fastcgi_params;
    }
    #跨域
    location ~ (.*)\.(jpg|png|jpeg|gif|bmp|woff2) {
        add_header Access-Control-Allow-Origin *;
        add_header Access-Control-Allow-Methods GET;
    }
}
```
### 2. 使用swoole启动的HTTP服务，nginx作代理
```apacheconfig
server{
    #监听端口
    listen 80;
    #域名绑定
    server_name www.xxx.com api.xxx.com;
    #跨域
    location ~ (.*)\.(jpg|png|jpeg|gif|bmp|woff2) {
        add_header Access-Control-Allow-Origin *;
        add_header Access-Control-Allow-Methods GET;
    }
    #静态文件
    location ~ \.(jpg|png|jpeg|gif|bmp|woff2|zip|doc|docx|xsl|xslx|pdf|ppt|pptx)$ {
        root /框架根目录/public;
        try_files $uri @s404;
    }
    #常规请求
    location / {
        root /框架根目录/public;
        try_files $uri @server;
    }
    #404
    location @s404{
        return 404;
    }
    
    # 转发自定义header
    underscores_in_headers on;

    #转发到swoole服务
    location @server {
        proxy_redirect off;
        # 转发真实域名host，方便在 header 中获取
        proxy_set_header Host $host;
        # 转发真实IP
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        # 引用服务地址
        proxy_pass http://127.0.0.1:1990;
        proxy_connect_timeout 300s;
        proxy_read_timeout 300s;
        proxy_send_timeout 300s;
        proxy_buffer_size 64k;
        proxy_buffers 4 32k;
        proxy_busy_buffers_size 64k;
        proxy_temp_file_write_size 64k;
        proxy_ignore_client_abort on;
    }
}
```

### 3. 使用swoole的websocket服务，nginx作代理
```apacheconfig
server{
    listen 80;
    server_name ws.xxx.com;
    location / {
        # 转发真实域名host
        proxy_set_header Host $host;
        # 转发真实IP
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        # websocket IP和端口，或其它域名....
        proxy_pass http://127.0.0.1:1991;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
    }
}
```