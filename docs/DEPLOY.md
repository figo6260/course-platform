# 部署步骤

## 1. 底座

```bash
git clone https://github.com/crmeb/CRMEB.git
# 环境：PHP 7.4 + MySQL 8.0 + Redis + Nginx
```

## 2. 放入定制代码

将 `backend/` 下所有文件按目录结构复制到 CRMEB 的 `crmeb/app/` 下；
将 `frontend/` 下文件复制到 `template/uni-app/` 对应位置。

## 3. 数据库

在 crmeb 库执行 `db/tables.sql` 建 6 张表：
eb_teacher / eb_course / eb_course_chapter / eb_course_lesson / eb_course_study / eb_course_order

## 4. 应用补丁

```bash
git apply docs/patches.diff   # 路由 + 事件注册
```

## 5. 配置

- `crmeb/config/database.php`：填数据库账号
- 微信支付：小程序后台配置商户号后填入系统设置
- 腾讯云 VOD：上传课程视频，课时 video_url 填播放地址

## 6. 小程序

HBuilderX 打开 `template/uni-app`，发行 → 微信小程序。
