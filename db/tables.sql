-- 卖课平台课程表结构（在 crmeb 库执行）
       Table: eb_teacher
Create Table: CREATE TABLE `eb_teacher` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT '老师姓名',
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '头像',
  `title` varchar(64) NOT NULL DEFAULT '' COMMENT '头衔',
  `bio` text COMMENT '简介',
  `phone` varchar(20) NOT NULL DEFAULT '' COMMENT '联系电话',
  `share_ratio` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT '分成比例%',
  `sort` int NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '1' COMMENT '1启用 0禁用',
  `add_time` int unsigned NOT NULL DEFAULT '0',
  `update_time` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='老师表'
       Table: eb_course
Create Table: CREATE TABLE `eb_course` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `teacher_id` int unsigned NOT NULL DEFAULT '0' COMMENT '老师ID',
  `product_id` int unsigned NOT NULL DEFAULT '0' COMMENT '关联商城虚拟商品ID',
  `title` varchar(128) NOT NULL DEFAULT '' COMMENT '课程标题',
  `cover` varchar(255) NOT NULL DEFAULT '' COMMENT '封面图',
  `intro` text COMMENT '课程介绍',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '售价',
  `market_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '原价',
  `lesson_count` int unsigned NOT NULL DEFAULT '0' COMMENT '课时数',
  `student_count` int unsigned NOT NULL DEFAULT '0' COMMENT '学习人数',
  `sort` int NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '1' COMMENT '1上架 0下架',
  `add_time` int unsigned NOT NULL DEFAULT '0',
  `update_time` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `teacher_id` (`teacher_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='课程表'
       Table: eb_course_chapter
Create Table: CREATE TABLE `eb_course_chapter` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `course_id` int unsigned NOT NULL DEFAULT '0',
  `title` varchar(128) NOT NULL DEFAULT '' COMMENT '章节标题',
  `sort` int NOT NULL DEFAULT '0',
  `add_time` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `course_id` (`course_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='课程章节表'
       Table: eb_course_lesson
Create Table: CREATE TABLE `eb_course_lesson` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `course_id` int unsigned NOT NULL DEFAULT '0',
  `chapter_id` int unsigned NOT NULL DEFAULT '0',
  `title` varchar(128) NOT NULL DEFAULT '' COMMENT '课时标题',
  `video_id` varchar(64) NOT NULL DEFAULT '' COMMENT '腾讯云VOD fileId',
  `video_url` varchar(512) NOT NULL DEFAULT '' COMMENT '播放地址',
  `duration` int unsigned NOT NULL DEFAULT '0' COMMENT '时长秒',
  `is_try` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1可试看',
  `sort` int NOT NULL DEFAULT '0',
  `add_time` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `course_id` (`course_id`),
  KEY `chapter_id` (`chapter_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='课程课时表'
       Table: eb_course_study
Create Table: CREATE TABLE `eb_course_study` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `uid` int unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `course_id` int unsigned NOT NULL DEFAULT '0',
  `lesson_id` int unsigned NOT NULL DEFAULT '0',
  `progress` int unsigned NOT NULL DEFAULT '0' COMMENT '进度秒',
  `finished` tinyint(1) NOT NULL DEFAULT '0',
  `update_time` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid_lesson` (`uid`,`lesson_id`),
  KEY `uid_course` (`uid`,`course_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='学习记录表'
       Table: eb_course_order
Create Table: CREATE TABLE `eb_course_order` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` varchar(32) NOT NULL DEFAULT '' COMMENT '关联商城订单号',
  `uid` int unsigned NOT NULL DEFAULT '0',
  `course_id` int unsigned NOT NULL DEFAULT '0',
  `teacher_id` int unsigned NOT NULL DEFAULT '0',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `paid` tinyint(1) NOT NULL DEFAULT '0',
  `pay_time` int unsigned NOT NULL DEFAULT '0',
  `add_time` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_id` (`order_id`),
  KEY `uid_course` (`uid`,`course_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='课程订单表'
