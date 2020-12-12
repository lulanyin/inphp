<?php
return [
    //站点绑定的域名是HTTPS还是HTTP
    "scheme"            => "https",
    //主域名，
    "host"              => "inphp.app",
    //网站使用的完整地址
    "web_url"           => "{scheme}://fpm.{host}",
    //资源主目录地址，用于固有的资源文件
    "assets_url"        => "{scheme}://assets.{host}",
    //附件主目录地址，主要用于上传的文件
    "attachment_url"    => "{scheme}://attachment.{host}"
];