<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <title>黑米流量通</title>
    <link href="{STATIC_PATH}/static/package/lib/ionic/css/ionic.css" rel="stylesheet" />
    <link href="{STATIC_PATH}/static/package/css/main.css" rel="stylesheet" />
    <script src="{STATIC_PATH}/static/package/js/jquery-1.9.1.js"></script>
    <style type="text/css">
        .top-content {
            margin-top: 40px;
        }

            .top-content h3 {
                margin-bottom: 5px;
                font-family: 微软雅黑;
            }

            .top-content h5 {
                font-family: 微软雅黑;
            }

        .list img {
            width: 27%;
        }

        .list p {
            padding-left: 10px;
        }

        .list a {
            color: #333333;
        }
    </style>
</head>
<body>
    <div class="list top-content text-center">
        <img src="{STATIC_PATH}/static/package/img/shibai.png" />
        <h3 class="title color-gray">充值失败</h3>
        <h5 class="title color-gray">请到充值记录重试</h5>
        <input id="code" type="hidden" value="{$code}" />
    </div>
    <div style="padding: 0px 10px;">
        <button id="btn-refer" class="button button-block button-stable" style="font-family: 微软雅黑; background-color: #eaeaea">
            查看充值记录(<span id="time">10</span>s自动跳转)
        </button>
    </div>
</body>
</html>
<script type="text/javascript">
    var param = '';
    var code = $("#code").val();
    if (code != '') {
        param = '?code=' + code;
    }
    $(document).ready(function () {
        //倒计时
        var TimeDown = function (time) {
            var _gametime = setInterval(function () {
                if (time == 1) {
                    clearInterval(_gametime);
                    window.location.href = "/flow/find/record" + param;
                    return;
                }
                time--;
                document.getElementById("time").innerHTML = time;
            }, 1000);
        }
        TimeDown(10);
        $("#btn-refer").click(function(){
            var param = '';
            var code = $("#code").val();
            if (code != '') {
                param = '?code=' + code;
            }
            window.location.href = "/flow/find/record" + param;
        });
    });
</script>

