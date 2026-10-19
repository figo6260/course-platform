# 卖课平台 course-platform

基于 CRMEB 6.0 开源版（Apache 2.0）二次开发的卖课平台：微信小程序卖课，支持多老师扩展。

## 功能（v1.0.0 MVP）

- 课程：列表 / 详情（含章节课时）/ 试看 / 购买（走商城订单+微信支付）
- 播放：课时播放地址鉴权（已购或试看才返回），学习进度记录
- 老师：老师列表 / 详情；课程关联老师（teacher_id 预留多老师扩展）
- 管理端：老师 / 课程 / 章节 / 课时的增删改查
- 支付：订单支付成功自动发放课程学习权限（CoursePaySuccessListener）

## 目录结构

```
backend/   后端定制代码（按 CRMEB 的 app/ 目录结构放置）
  app/model/course/      6 个 Model
  app/dao/course/        6 个 DAO
  app/services/course/   CourseServices + TeacherServices
  app/api/controller/v1/course/   移动端 API（7 个接口）
  app/adminapi/...       管理端 API（16 个接口）
  app/listener/course/   支付成功发放课程监听器
frontend/  小程序前端（uni-app）
  api/course.js          7 个接口封装
  pages/course/          列表 / 详情 / 播放 / 我的课程
db/
  tables.sql             6 张新表建表语句
docs/
  patches.diff           对 CRMEB 原文件的修改（路由、事件注册）
  DEPLOY.md              部署步骤
```

## 版本

- v1.0.0 (2026-10-19)：MVP——课程售卖闭环（浏览/购买/支付/看课/进度）

## 二期（规划中）

- 多老师简易版：老师后台、数据隔离、手动结算
- 抖音小程序：直播挂载，手机号统一登录
- 私域直播：小程序内直播间（接口已预留）
