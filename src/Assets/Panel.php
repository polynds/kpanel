<?php

declare(strict_types=1);
/**
 * happy coding!!!
 */

namespace Polynds\KPanel\Assets;

class Panel
{
    public function display(string $host, int $port): string
    {
        $host = $host == '0.0.0.0' ? '127.0.0.1' : $host;
        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hyperf Controll Pannel.</title>
</head>
<body>
<div>Welcome！</div>
<h1>Swoole WebSocket Server</h1>
<div id="container"></div>
<script>
let lockReconnect = false; //避免ws重复连接
let ws = null; // 判断当前浏览器是否支持WebSocket
let wsUrl = null;
let config = {
    url: "ws://{$host}:{$port}/websocket",
    open: (ws) => {
        function sendNumber() {
            if (ws.readyState === ws.OPEN) {
                ws.send('getData');
                setTimeout(sendNumber, 1000);
            }
        }
        sendNumber()
    },
    msg: (data) => {
        console.log(data, "msg")
    }
}
socketLink(config)

function socketLink(set) {
    config = set;
    wsUrl = config.url;
    createWebSocket(wsUrl); //连接ws
}

function createWebSocket(url) {
    try {
        if ('WebSocket' in window) {
            ws = new WebSocket(url);
        } else if ('MozWebSocket' in window) {
            ws = new MozWebSocket(url);
        } else {
            alert("您的浏览器不支持websocket");
        }
         console.log(ws);
        initEventHandle();
    } catch (e) {
        reconnect(url);
        console.log(e);
    }
}

function initEventHandle() {
    ws.onclose = function() {
        reconnect(wsUrl);
        console.log("llws连接关闭!" + new Date().toUTCString());
    };
    ws.onerror = function() {
        reconnect(wsUrl);
        console.log("llws连接错误!");
    };
    ws.onopen = function() {
        ws.send('getData');
        heartCheck.reset().start();
        console.log("llws连接成功!" + new Date().toUTCString());
        config.open(ws);
    };
    ws.onmessage = function(event) {
        heartCheck.reset().start();
        config.msg(event.data,ws);
        document.getElementById("container").innerHTML = event.data;
    };
}
// 监听窗口关闭事件，当窗口关闭时，主动去关闭websocket连接，防止连接还没断开就关闭窗口，server端会抛异常。
window.onbeforeunload = function() {
    ws.close();
}

function reconnect(url) {
    if (lockReconnect) return;
    lockReconnect = true;
    setTimeout(function() { //没连接上会一直重连，设置延迟避免请求过多
        createWebSocket(url);
        lockReconnect = false;
    }, 2000);
}

//心跳检测
var heartCheck = {
    timeout: 10000, //9分钟发一次心跳
    timeoutObj: null,
    serverTimeoutObj: null,
    reset: function() {
        clearTimeout(this.timeoutObj);
        clearTimeout(this.serverTimeoutObj);
        return this;
    },
    start: function() {
        var self = this;
        this.timeoutObj = setTimeout(function() {
            ws.send("ping");
            console.log("ping!");
            self.serverTimeoutObj = setTimeout(function() {
                console.log("try=close")
                ws.close();
                }, self.timeout);
        }, this.timeout);
    }
}

</script>
</body>
</html>
HTML;
    }
}
