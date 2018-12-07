<?php
$config = array (
		//签名方式,默认为RSA2(RSA2048)
		'sign_type' => "RSA2",

		//支付宝公钥
		'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEApIZIAQsDT+lw7oRq/Tdg/9kOx7PYsXxdo5hFDj0dF1fs04F+Kv3u9U0wlOyACyDnpSk5x+UG/Im+8NoZA82wiEGv2L2H/PnFONDud+4zMa1gvtg/6I1pIMRBE1L+idcjvT99MTidpLa0xI7pWmuxeItETllzRIBwd2GYyZ4SkLHVi49dfgM7CGDwwmWQCHgNF76LYAi8sitkrh0CtO9fAcf1mlKo2EJPi9X1Jm6/0/VaMs42msHpCNBdAIzrqdKRll9jQoFx1LwssStJBTe5K+3bJA2IMuQy+GhP8gbbCIb4DtPzZNpdr1+NVuM6SLqzf/joRXWA5ZSY19hAXcZrBQIDAQAB",

		//商户私钥
		'merchant_private_key' => "MIIEogIBAAKCAQEAt89PExW4oVvnJG8DPlMsQwgEpPSpQlFdh528mKN6RkTZAapkVWgMmMPgrBoF/RecmJ0x/kWbNZQhrqgK7Fw+zwHEY14F62ZXspSzo+BijPuwdpQuRbQCT2Nobhm4tK7ZX5bVXBk/UaI9NAtLDNl62Y5rwl5r0eaq4DVKc18S1C7z6OKBJBOMZVEw/vUDIoaBJt9TUtTM0Urv4qj3wLrCPDhh3VWrwVmJ2dNKiPnsGGNA0Hupi4cIKxGqVm/pyVGB0H0VHN3aGMEUw4XfF781fjpzdPeZjPJFtzBaAdC2O+KIn1vMrLolbEpxu2E1ig9syyjuNA44NVP9il4b538jXwIDAQABAoIBAH2S1p3eOA2cwLPGV7vrjJCa2LltIHlbJv+whpjtDmsVPAAETZl/hSOUplhNSwwWZnho5C+nlBqtgblVumixuIMp3OZZ5MdmWsF5D6UEda+Ff4/zOg2Kpg1gh4a4cdSWo5DHdin+YaC+qvt0P6iep2wb/YiDgzuaT+Du51cce7uS0b4dceu8W8veGnaI5M9DFQxjq30wR6r9kIDbG/fpeLZ0TDHLxpuANm+5/EIDtY8q/qATdWOl5vbjD4pmP4PbNLkpgY8vVL8CFj52z/0lYjP+WiEBlNQX98ZZpsqA+aZ13zHI+xRJ8e+yhqd3RlXxcz9dkb72oIw7+Dz6aB7V0ikCgYEA9CJX3/Gxh0MeLPyDBhR4h6vbKmewM+7qdegHDReLQHqVT2UAPiy/RCWF/KKip/5tiv68HKFNrFJGp9rOLCv47x6txmVztMRS1M5kmG8Hb0LIjcP6thMlCVKdjvE+XiKgaCzq/FHNw/iraDHz77/5DsWN0Z2FHKrXQ/NTpJV6zCUCgYEAwL5gIqI/q6wz+W8OqHi7HfrWduRc8MgLneaw9gQkBx4ATUDmhNawbz/rvKzTj32K2NWVmMdTuy4G708KWNm3JBgpXntlb09Bz2gUNVoKLSHPJCw05QtXjecs5wYHjRCzm8lDcFGQNC5BvTAgsjge4UAoEf1yZ79oVqr2p1HBGDMCgYByEyYah1YbzRnpjWgvzBrx0jTLoL2t1qKJy4yX6ntv+peQDLLLWp9Y2Wu9O8VjWDiZbSQ7AIhJz/wh7NTPwRBFs4EhpkAlpGLL+1D4BVFlBMCvtXaN42435/mlVEZ/OBDZ/LskgZjzTFvTiRvh2EMpSthUrRUI6y9BGg7oZcyGXQKBgFXD4K9IlyBizfXODy20Gz8p4MiisSCLQ3ANuOyfxxBLr0KxAGJXzcaTIOih1rng2SnHUHvdJksCHh/agfYrWqz6+12JdwdisxwBagybdi/C/ZNRAHBy7ZC9L2PVcQK6TdGiaxnNkWdGtgXjJolnI4aDr9DhgEjeCSWXiY3GeS+1AoGAZSql0HqOcL7NyMiITl3FbXeTAnjjiivFbNpdKXMmSI1raT9w6MMqPSf0rzoObifsYMvB1CUgwTBwqaQ0ylSSo+/sWLN9AGpyN/YWcILqYW5QlTmXLfH4glz4Lm9FhZGkZZ+M5XIPYaKD2iTGrh+P5W7KSywnGjXTUrnWP1UodsI=",

		//编码格式
		'charset' => "UTF-8",

		//支付宝网关
		'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

		//应用ID
		'app_id' => "2018060260310553",

		//异步通知地址,只有扫码支付预下单可用
		'notify_url' => "http://www.baidu.com",

		//最大查询重试次数
		'MaxQueryRetry' => "10",

		//查询间隔
		'QueryDuration' => "3"
);