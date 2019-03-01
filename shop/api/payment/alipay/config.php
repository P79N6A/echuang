<?php
$config = array(
	// 应用ID,您的APPID。{已修改}
	'app_id' => "2018112362287482",

	// 商户私钥（已修改）
	'merchant_private_key' => "MIIEpQIBAAKCAQEAnP0avJ+npRfEL6A1XVCxmnxA4yvXEKtpesdBQ3yT1IQeuKshXMg8MOuyLORpprkBm2DxlMC5jZbJGVa1CZXTLP7XV8g7e6kqxbcbVbG6u10Or9fkR9Wnzkr7cjsV3FMkmaapLqbISGlKlwi9RR/+6bv8agj1gJr1qA0+32iaBFOelDpaJIDTkszUClcRyE7AR6zSMyR0KiYUxlp+cIKFk5E2HZxQZ/QSwu+bcueufkMki3lVpwUwfzn1MVVjSWdehKViu34QIQks/OZ9Rn4sC1RFxN1URb3Ej/eo5XKgxFUEr5xxFVMhHwuWoCezWPEzaz5N7131ho4sBuG1NqOUWQIDAQABAoIBAFH+K9m6dT/X0uKhBV4wiCJMpUNBIXmWXWf/68lIpplHTBhdYPZpqm5Om+ewgLHQTNOPZX2wzvjxeSTf0XwpIXuNuoEhlle2+hz7zq4KPqIfL02OL6+JGrS3Gu8Fhxxk3GfX0OMJvu969rc9nEUfOe9v/JyRA1BhHs7f5CehJVf+VxmK+M96Pfhcmm8fpgrUyMnjLp0plMPU5I3qKLGy/80YlZp+x/0YoYNYicn22OkmxWU9F1hvW5EXN5hUrYDPk1phHwjHrHV0Lrbs8PZxVeDscCIJrKgVpbMc2AbD3qiPalqwS/d40WLv9j6MgpMc0BNXa+at17/4Nt0Unn5ob1ECgYEAycuXOnGrRRweoblIXFUN6lWjBrhY3hUrxIjA2xdqWOkvHOTIJ4jpjLQEPBenpSknVitYDTnbiB537G8DXgQs+kUjgNbKbTU4qe/RrGeUV7kN+/PnTgKhAQk8voak4t7O4QAWbkdp3te1Z+Zog3DUCU53cn4d68l6ceQdfsQt+DUCgYEAxyhl9SWfQV16sooP46UHDjZ067vzgJ8abPBrKpAHvyv2Ti4yun0yGSI8xfj6ceeFSVLGutY5FkOdHrp3IgBQzhd9rv3krqHjCzD0Dv0gYQuHKT3R41Y75o9Vnk3P5IAKVQBrW7WbHIqQKJ5tgAo5EUA9GVaRHXZTXCjUDnTcWBUCgYEAxm+b8O7iWv2B6crHq3RAUNWRkQQq74imF4fbDheCjpgPqjwXzzjFYYWMPIOOac2ETyokVi01Ai/kAe5Qw10b1aezd/i/6pBRflrENuNMbs8dP0vjg0T2ioxI3ZqN/kLX8U4iwKMdKvO4AcoDsG19NQGiWEZIqhrge6q0jnngMpkCgYEAnOcG7IwnW6f58o8y1XY+iMP+eXop7Ttr5kfDJlUyxTotYoqjDKl1vrdqic4OAY8VQx8j0i7T6uZ58quVEASPpM59BlCgybRg24BFx0lFKS+b3LIHmcNmjc5+9aj4EF2Pxkf+TNJW0bWYvWeTfTyQmTay7DN3QG7oXcMMzhbjQcECgYEAhnrPHI4XTtqeuKptU9V3ztFtGZOwBdvXRPC8Q70Ra/OjTsEJqsy/REibPtA5uByqeT3Gt6Rap2APXg2FQiTWH5rNwUFSh5Q4elhXD5M0d2w8H28hXl1G3oIqAeN61mhZv01KZx6OY+PEuGxLEeTL4BGqu0cn7O8XQO1i3cQRiEE=",

	// 异步通知地址（已修改）
	'notify_url' => "http://shop.divona.xin/shop/api/payment/alipay/notify_url.php",

	// 同步跳转(已修改)
	//'return_url' => "http://alipay1.com/return_url.php",
	'return_url' => "http://shop.divona.xin/shop/api/payment/alipay/return_url.php",

	// 编码格式
	'charset' => "UTF-8",

	// 签名方式
	'sign_type' => "RSA2",

	// 支付宝网关（已修改）
	'gatewayUrl' => "https://openapi.alipaydev.com/gateway.do",

	// 支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。（已修改）
	'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAq5+Kxs3+yNYRvw2NjbWEENsIoy0uehQ+3UZcoQ83/VqmAh5TJPjDRw+pR+8uuvTP46oVq/Wp+LmAfwddyMJilTuY0MnocB7VCtv0GE86tfXCPZutquAVZq7RSdk8xDkPWQZumAc3WPeBLyhi9LW8Eg4fVyH4D+Z/M5mFTzZDEGmKntjF8eF2O1lk3QiHWiyWFXOIvtOm/w2q2fkwbujTmgHQn9SMxVsr2ufvn2SYz6y4FYPsB9fKy/9nXDgG6WYIX6wu82nSzR2rWrfEDEusG2sX/uF0kli3r/O7uz8d8lOwCXGIYMIhiRdenTLPHyqqqW2gMlEBULdzC2VpsNdtmQIDAQAB",
);