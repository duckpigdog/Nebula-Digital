# 后台登录解题思路（最新）

- 入口地址 `http://127.0.0.1:3000/index.php` 未提供明显后台入口，需要目录扫描定位后台登录页 `http://127.0.0.1:3000/admin/login.php`
- 参数加密：
  - 前端：`public/assets/crypto.js:1-2`
  - 后端：`lib/config.php:7-8`
  - 密钥 `k9B!1x@Z`，逐字节与密钥异或后统一偏移 `+3`，再 `base64` 编码
- 登录防护：
  - 登录失败页面直接回显“账号或密码错误”
  - 基于 `X-Forwarded-For` 的 IP 限制，连续错误 5 次锁定 1 分钟，可通过遍历 XFF 头绕过
  - 已移除越权数据泄露接口，`/api/admin_access.php` 不再返回管理员凭据
- 默认管理员：`admin / password`

## 步骤

- 第一步：目录扫描
  - 扫描发现 `http://127.0.0.1:3000/admin/login.php` 为后台登录页

- 第二步：逆向加密
  - 阅读或抓包前端脚本，提取函数 `e(s)` 与密钥，用以生成 `enc`

- 第三步：登录尝试
  - 构造加密载荷：`{"u":"admin","p":"password","time":<timestamp>}`
  - 提交 `POST /admin/login.php`，表单字段为 `enc=<ciphertext>`
  - 成功跳转到 `http://127.0.0.1:3000/admin/dashboard.php`
  - 失败则页面提示“账号或密码错误”

## 参考代码

```js
function e(s){var k="k9B!1x@Z";var o=[];for(var i=0;i<s.length;i++){o.push((s.charCodeAt(i)^k.charCodeAt(i%k.length))+3)}return btoa(String.fromCharCode.apply(null,o))}
```

```bash
# 后台登录
# enc = e(JSON.stringify({u:"admin",p:"password",time:Date.now()}))
# POST http://127.0.0.1:3000/admin/login.php
# Body: enc=<enc>
```

## 关键点

- 加密实现与密钥：`public/assets/crypto.js:1-2` 与 `lib/config.php:7-8`
- 登录防护：IP 错误计数与锁定逻辑在 `admin/login.php`
- 后台界面路径：`/admin/dashboard.php`，控制台包含用户与基础统计
