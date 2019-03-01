<?php
return array(
	// 应用ID,您的APPID。
	'app_id' => "2018041502561126",

	// 商户私钥，您的原始格式RSA私钥
	'merchant_private_key' => "MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQDLAZcMMQzFkgWpf9XFZuU46X//pywYEvuvZLA502/QXE2tSYv5otU58jzA6hXpxXUmUDKzr3OI3L9mc+x2FR9ga7/HpUNg3aD8YwZotpRkL5oorI4SvBQSd/0u4/QIsIvx1tAnbej/Igb+U7Rxcn5JNjOYGfBWC11AoDDIPWG+ye/roCSLvDT/UXyzhngAWM6SPkLKhS7Wlmft5yTW2o7wZTR+jBUEH4q0F8/O6QK2ApHqUDT4lmSSku17Ser9m6Q1K7PQA2D4D/EjQnG/63+0+ibORrZckc48m3e8BlnWG6xxMEfFTFzkUDevhiIy8h4tDgPHQr/1eCNsyGyOXf+RAgMBAAECggEAeYPZI24U5Ap1IzGnB0OBQCDdAm3RwhNS3nkZ13NNwd4OjQSyKdqjYc0zOpKkTUtpxWZKAy6prKneU/hd+YzJPFEi+eFdxJTFcW3g/zsS9ilcUK6BWIuGREvYnIHMqIrtKfTYolFzH40BvKjkIOhU2YP9PDWCnC6vQijhKms7oBR1WL0ovTTOxC5/znA2AyYllTxU2EeRZRc07lHaAqPAwx/nTmOfIRdCI+4wRctrGBHoGQ7MOF6g7Un6FTUnwWcxFIY6/wg+xXe7glKgu9F6H4Q6iuGkyZwfCH8i2KUUIrhhNsI9bF3N9WqvAqRTIighW7LZ5Sa6C+dgIE9hUkQrlQKBgQDr/ncLVpuIrKggCELwyZfQ0lcU5zSxEMW51H3OLkDqQO64MJKNq3xbTf7pKDz6Ixz90TmvpofM9wt1DzxAlk2+uuiVUe5kfev4qWy3SzE7yhx53vZYYtCXQ6HzsBP2dIWTGPJbBOY5UmE3+u6w4xVjAf0OCewDYLIvP7UmuIjtvwKBgQDcNzmSrRS+gqTYohn5vt4eFsME9EkI0eCdq6EvG/A84xcqygLCYV9427xFzbsYgbAz+TJ4ChrMzmeinC3y7W3X7pG4AocY7nCTsqdrxzf6eEOqqBceWPkamCuKTL+UcyuB3Fp6/ySV13SB0HyzkKsHAxMrFNNxbsBUEWi/+w8GrwKBgQCH5xHNIMIO8kcbs0ZBfgaEhi0J55/LfDoxqvIMA0z02hHfVnF/TkseFYt/3+FFt2ThslSDF+bKnVYmwnBTTXtWJDL+kqEzk+SJXaAy6gVfRiQh9sls2vL+uT8Ro8vLOQ0XSd1/lxGx3wQHheVhTlSpoKKHLCWTSGM0nH6I3upT5wKBgQCodlrFuUBwSRMAYmmKo5fZ3sHLzXD9nUi+dWh12GKcwYCcjDM2LjVSCI7ru2mQYs2/xLf2GozL9m5TeDSybS2ljWzOHqjoyqc2ZeoryhgptMzXBYXCAoB19skAIJxCXSSAUGirRPng4xTOpgZve5FKaPDF+UWTtgzNgw/mJZImIwKBgGutKaVRh5aasJ1a6Uma8edsKR9ZkjWhk9+uTNExjKayE0+8rG6plO/85jteUrU8Vq4H8HyoTTL/0cE2nQCiZg3QOVgRwdq3DVnjTQWdd6VkgNKrk+JhTByaENOg4tR2XQMGWFA2jGXPnmGj2csaSXcvAJpXJ/zcMOv/k0PuYcnX",

	// 异步通知地址
	'notify_url' => "http://工程公网访问地址/alipay.trade.wap.pay-PHP-UTF-8/notify_url.php",

	// 同步跳转
	'return_url' => "http://mitsein.com/alipay.trade.wap.pay-PHP-UTF-8/return_url.php",

	// 编码格式
	'charset' => "UTF-8",

	// 签名方式
	'sign_type' => "RSA2",

	// 支付宝网关
	'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

	// 支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
	'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAiiAaX4X3cS7mgT/0cyYpLKhP6SG+I/O7euBeG/bjzLr29kHXWlz9mfK/2UDrqnWBzuldCLFC4LE5D/keXs5nLdiy3PbBIVpvrYkvR0XgKeiweopv8fOprxCcMYuX2z2Y81QykNFP11XLgekQ6qo0tuzYO9wYZzXPap+QwUlEj4W90Gqcz4zLON5xytS6sW6gBj1JDpFMWiAOdBFv9gunmStr38fizU4tL3UkgZO2I4EtLrJypasq4LU7n54spofgYHmS5Kq38pGXHODXvsi0E82EueEAog28nAgMtrQbTz4m5WuEDXPWg/uAGihsPpDbR1eNvXk/3n6XROQ6GiBUnQIDAQAB",
);