# Host: localhost  (Version: 5.7.17)
# Date: 2019-05-26 18:14:27
# Generator: MySQL-Front 5.3  (Build 4.234)

/*!40101 SET NAMES utf8 */;

#
# Structure for table "adminer"
#

DROP TABLE IF EXISTS `adminer`;
CREATE TABLE `adminer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account` varchar(255) NOT NULL DEFAULT '' COMMENT '帐号',
  `password` varchar(255) NOT NULL DEFAULT '' COMMENT '密码',
  `name` varchar(255) DEFAULT NULL COMMENT '真实姓名',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态：1=》正常；0=》禁用',
  `permissions_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '权限组ID',
  `is_admin` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否是超级管理员：1=》是；0=》非',
  `remark` text COMMENT '备注说明',
  `last_ip` varchar(255) DEFAULT NULL COMMENT '最后登录IP地址',
  `last_at` int(10) unsigned DEFAULT NULL COMMENT '最后登录时间（时间戳）',
  `count` int(11) unsigned DEFAULT '0' COMMENT '登录总次数',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `adminer_key` (`id`,`account`,`permissions_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='管理员表';

#
# Data for table "adminer"
#

INSERT INTO `adminer` VALUES (1,'admin','96e79218965eb72c92a549dd5a330112','Amdin',1,1,1,'admin12sdf','127.0.0.1',1558406030,19,'2018-10-23 13:54:17'),(2,'guest','96e79218965eb72c92a549dd5a330112','guest',1,2,0,'guest','127.0.0.1',1542330568,5,'2018-10-23 15:16:25'),(3,'tests','96e79218965eb72c92a549dd5a330112','Test',1,2,0,'TEST',NULL,NULL,0,'2018-11-14 13:13:10');

#
# Structure for table "choices"
#

DROP TABLE IF EXISTS `choices`;
CREATE TABLE `choices` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `questions_id` int(11) unsigned DEFAULT NULL COMMENT '考题ID',
  `opts` varchar(255) DEFAULT NULL COMMENT '选项',
  `delete_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除：0=》未删除；时间戳=》删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='选择题项';

#
# Data for table "choices"
#


#
# Structure for table "classify"
#

DROP TABLE IF EXISTS `classify`;
CREATE TABLE `classify` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL COMMENT '分类名称',
  `thumbnail` varchar(255) DEFAULT NULL COMMENT '缩略图地址',
  `pid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '父级ID，一级分类PID为0',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='分类';

#
# Data for table "classify"
#

INSERT INTO `classify` VALUES (1,'计算机管理','/uploads/20190521/5ce3ab0837780.jpg',0,'2019-05-21 14:55:34'),(2,'行政管理','/uploads/20190521/5ce3ad71461e0.jpg',0,'2019-05-21 15:49:06'),(3,'商业法律','/uploads/20190521/5ce3ad8a60f90.jpg',0,'2019-05-21 15:49:31'),(4,'汉语言文学','/uploads/20190521/5ce3ada7e6078.jpg',0,'2019-05-21 15:50:00'),(5,'工商企业管理','/uploads/20190521/5ce3adb76cef8.jpg',0,'2019-05-21 15:50:16');

#
# Structure for table "config"
#

DROP TABLE IF EXISTS `config`;
CREATE TABLE `config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL COMMENT '参数标题',
  `param` varchar(255) DEFAULT NULL COMMENT '参数值',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='参数配置表';

#
# Data for table "config"
#

INSERT INTO `config` VALUES (1,'平台标识','AOOGI后台管理系统'),(2,'应用名称','AOOGI');

#
# Structure for table "course"
#

DROP TABLE IF EXISTS `course`;
CREATE TABLE `course` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` char(7) DEFAULT NULL COMMENT '课程代码',
  `title` varchar(255) DEFAULT NULL COMMENT '课程名称',
  `classify_id` int(11) unsigned DEFAULT NULL COMMENT '分类ID',
  `mold_id` varchar(255) DEFAULT NULL COMMENT '考题类型',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='课程';

#
# Data for table "course"
#

INSERT INTO `course` VALUES (1,'00182','公共关系学',2,'1-2-4-5-6','2019-05-21 17:19:37'),(2,'00183','法学概论',2,'1-2-3-4-5-6','2019-05-21 17:51:19');

#
# Structure for table "mold"
#

DROP TABLE IF EXISTS `mold`;
CREATE TABLE `mold` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` tinyint(3) unsigned DEFAULT NULL COMMENT '类型代码-不允许重复',
  `title` varchar(255) DEFAULT NULL COMMENT '类型标题',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='考题类型';

#
# Data for table "mold"
#

INSERT INTO `mold` VALUES (1,1,'单项选择题','2019-05-21 16:38:25'),(2,2,'多项选择题','2019-05-21 16:38:33'),(3,3,'判断题','2019-05-21 16:40:34'),(4,4,'简答题','2019-05-21 16:40:47'),(5,5,'论述题','2019-05-21 16:41:10'),(6,6,'材料分析题','2019-05-21 16:41:54');

#
# Structure for table "permissions"
#

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL COMMENT '权限组名称',
  `router_id` text COMMENT '权限组权限集',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态，1=》正常；0=》禁用',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `permission-index` (`id`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='权限组表';

#
# Data for table "permissions"
#

INSERT INTO `permissions` VALUES (1,'超级管理员组','1-29-30-31-2-3-4-5-6-7-8-9-18-28-10-11-12-13-14-15-16-19-27-17-20-21-22-23-24-25-26-32',1,'2018-11-06 16:15:51'),(2,'GUEST','1-29-30-31-2-3-4-5-6-7-8-33-34-35-36',1,'2018-11-07 16:04:06');

#
# Structure for table "questions"
#

DROP TABLE IF EXISTS `questions`;
CREATE TABLE `questions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` char(7) DEFAULT NULL COMMENT '课程代码',
  `course_id` int(11) unsigned DEFAULT NULL COMMENT '课程ID',
  `mold_id` int(11) unsigned DEFAULT NULL COMMENT '类型ID',
  `title` varchar(255) DEFAULT NULL COMMENT '考试题目',
  `correct` varchar(255) DEFAULT NULL COMMENT '正确答案',
  `analysis` text COMMENT '答案解析',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '考题状态：1=》等审核；2=》已通过；3=》已驳回',
  `delete_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除：0=》未删除；时间戳=》删除时间',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='考题库';

#
# Data for table "questions"
#


#
# Structure for table "router"
#

DROP TABLE IF EXISTS `router`;
CREATE TABLE `router` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `router` varchar(255) DEFAULT NULL COMMENT '系统路由',
  `menu` varchar(255) DEFAULT NULL COMMENT '菜单路由',
  `path` varchar(255) DEFAULT NULL COMMENT '系统控制器/方法 路径',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '名称',
  `icon` varchar(255) DEFAULT NULL COMMENT 'icon图标',
  `pid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '父级路由ID（一根目录路由为0）',
  `open` tinyint(3) unsigned DEFAULT '0' COMMENT '是否展开；默认为1=>true【展开】，0=>false【不展开】',
  `main` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否为主目录：0【非】，1【是】',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '路由状态;0=>禁用；1=>启用',
  `opts` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否为配置项路由：1=>是；0=>否',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `router_key` (`id`,`pid`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8 COMMENT='路由规则表';

#
# Data for table "router"
#

INSERT INTO `router` VALUES (1,'/aoogi/main','/','','控制面板','#xe679;',0,0,1,1,1,'2018-10-26 14:44:54'),(2,'','','','系统设置','#xe66a;',0,0,1,1,1,'2018-10-26 16:57:59'),(3,'/aoogi/adminer','/aoogi/adminer','admin/admin/index','管理员设置','#xe653;',2,0,1,1,1,'2018-10-26 16:59:16'),(4,'/aoogi/adminer/data','/aoogi/adminer/data','admin/admin/getData','管理员列表','',3,0,0,1,0,'2018-10-26 17:02:06'),(5,'/aoogi/adminer/create','/aoogi/adminer/create','admin/admin/create','新增管理员','',3,0,0,1,1,'2018-10-26 17:03:30'),(6,'/aoogi/adminer','/aoogi/adminer/save','admin/admin/save','保存管理员','',3,0,0,1,0,'2018-10-26 17:04:09'),(7,'/aoogi/adminer/edit/:id','/aoogi/adminer/edit/*','admin/admin/edit','编辑管理员','',3,0,0,1,1,'2018-10-26 17:05:38'),(8,'/aoogi/adminer/:id','/aoogi/adminer/update/*','admin/admin/update','更新管理员','',3,0,0,1,0,'2018-10-26 17:06:30'),(9,'/aoogi/adminer/:id','/aoogi/adminer/del/*','admin/admin/delete','删除管理员','',3,0,0,1,1,'2018-10-26 17:07:14'),(10,'/aoogi/router','/aoogi/router','admin/router/index','路由设置','#xe653;',2,0,1,1,1,'2018-10-30 10:41:29'),(11,'/aoogi/router/data','/aoogi/router/data','admin/router/getData','路由列表','',10,0,0,1,0,'2018-10-30 10:44:59'),(12,'/aoogi/router/create','/aoogi/router/create','admin/router/create','新增路由','',10,0,0,1,1,'2018-10-30 10:47:36'),(13,'/aoogi/router','/aoogi/router/save','admin/router/save','保存路由','',10,0,0,1,0,'2018-11-05 11:00:01'),(14,'/aoogi/router/edit/:id','/aoogi/router/edit/*','admin/router/edit','编辑路由','',10,0,0,1,1,'2018-11-05 11:44:50'),(15,'/aoogi/router/:id','/aoogi/router/update/*','admin/router/update','更新路由','',10,0,0,1,0,'2018-11-05 11:47:04'),(16,'/aoogi/router/:id','/aoogi/router/del/*','admin/router/delete','删除路由','',10,0,0,1,0,'2018-11-05 11:47:34'),(17,'/aoogi/permission','/aoogi/permission','admin/permission/index','权限设置','#xe653;',2,0,1,1,1,'2018-11-05 15:03:03'),(18,'/aoogi/adminer/status','/aoogi/adminer/status','admin/admin/setStatus','管理员状态','',3,0,0,1,0,'2018-11-07 09:12:01'),(19,'/aoogi/router/status','/aoogi/router/status','admin/router/setStatus','路由状态','',10,0,0,1,0,'2018-11-07 09:12:35'),(20,'/aoogi/permission/data','/aoogi/permission/data','admin/permission/getData','权限组列表','',17,0,0,1,0,'2018-11-07 09:14:05'),(21,'/aoogi/permission/status','/aoogi/permission/status','admin/permission/setStatus','权限组状态','',17,0,0,1,0,'2018-11-07 09:15:14'),(22,'/aoogi/permission/create','/aoogi/permission/create','admin/permission/create','新增权限组','',17,0,0,1,1,'2018-11-07 09:15:50'),(23,'/aoogi/permission','/aoogi/permission/save','admin/permission/save','保存权限组','',17,0,0,1,0,'2018-11-07 09:16:24'),(24,'/aoogi/permission/edit/:id','/aoogi/permission/edit/*','admin/permission/edit','编辑权限组','',17,0,0,1,1,'2018-11-07 09:16:52'),(25,'/aoogi/permission/:id','/aoogi/permission/update*','admin/permission/update','更新权限组','',17,0,0,1,0,'2018-11-07 09:17:36'),(26,'/aoogi/permission/:id','/aoogi/permission/del/*','admin/permission/delete','删除权限组','',17,0,0,1,0,'2018-11-07 09:18:20'),(27,'/aoogi/router/:id','/aoogi/router/read/*','admin/router/read','查看路由','',10,0,0,1,1,'2018-11-08 10:48:00'),(28,'/aoogi/adminer/:id','/aoogi/adminer/read/*','admin/admin/read','查看管理员','',3,0,0,1,1,'2018-11-09 14:07:38'),(29,'/aoogi/logout','/aoogi/logout','admin/home/logout','退出登录','',1,0,0,1,1,'2018-11-12 14:00:40'),(30,'/aoogi/error','/aoogi/error','admin/error/index','403错误','',1,0,0,1,1,'2018-11-13 14:07:32'),(31,'/aoogi/main','/','admin/home/main','主页','',1,0,0,1,1,'2018-11-15 12:02:01'),(32,'/aoogi/permission/:id','/aoogi/permission/read/*','admin/permission/read','查看权限组','',17,0,0,1,1,'2018-11-15 15:19:31'),(33,'/aoogi/config','/aoogi/config','admin/config/index','参数设置','#xe653;',2,0,1,1,1,'2018-11-15 16:35:12'),(34,'/aoogi/config/create','/aoogi/config/create','admin/config/create','新增参数','',33,0,0,1,1,'2018-11-15 16:44:30'),(35,'/aoogi/config','/aoogi/config/save','admin/config/save','保存参数','',33,0,0,1,0,'2018-11-15 17:05:08'),(36,'/aoogi/config/edit/:id','/aoogi/config/edit/*','admin/config/edit','编辑参数','',33,0,0,1,1,'2018-11-15 17:09:08'),(37,'/aoogi/config/:id','/aoogi/config/update/*','admin/config/update','更新参数','',33,0,0,1,0,'2018-11-16 10:34:21'),(38,'/aoogi/admin/*','/aoogi/admin/del/*','admin/config/delete','删除参数','',33,0,0,1,0,'2018-11-23 11:51:30'),(39,'/aoogi/router/create_modular','/aoogi/router/create_modular','admin/router/createModular','新增模块路由',NULL,10,0,0,1,1,'2019-05-20 21:53:04'),(40,'/aoogi/router/save_modular','/aoogi/router/save_modular','admin/router/saveModular','保存模块路由','',10,0,0,1,0,'2019-05-20 21:54:17'),(41,'','','','题库设置','#xe66a;',0,0,1,1,1,'2019-05-21 13:30:40'),(42,'/aoogi/classify','/aoogi/classify','admin/classify/index','分类管理','#xe653;',41,0,1,1,1,'2019-05-21 13:34:27'),(43,'/aoogi/classify/data','/aoogi/classify/data','admin/classify/getData','分类列表',NULL,42,0,0,1,0,'2019-05-21 13:34:27'),(45,'/aoogi/classify/create','/aoogi/classify/create','admin/classify/create','新增分类',NULL,42,0,0,1,1,'2019-05-21 13:34:27'),(46,'/aoogi/classify','/aoogi/classify/save','admin/classify/save','保存分类',NULL,42,0,0,1,0,'2019-05-21 13:34:27'),(47,'/aoogi/classify/edit/:id','/aoogi/classify/edit/*','admin/classify/edit','编辑分类',NULL,42,0,0,1,1,'2019-05-21 13:34:27'),(48,'/aoogi/classify/:id','/aoogi/classify/update/*','admin/classify/update','更新分类',NULL,42,0,0,1,0,'2019-05-21 13:34:27'),(49,'/aoogi/classify/:id','/aoogi/classify/del/*','admin/classify/delete','删除分类',NULL,42,0,0,1,0,'2019-05-21 13:34:27'),(50,'/aoogi/course','/aoogi/course','admin/course/index','课程管理','#xe653;',41,0,1,1,1,'2019-05-21 15:52:42'),(51,'/aoogi/course/data','/aoogi/course/data','admin/course/getData','课程列表',NULL,50,0,0,1,0,'2019-05-21 15:52:42'),(52,'/aoogi/course/status','/aoogi/course/status','admin/course/setStatus','课程状态',NULL,50,0,0,1,0,'2019-05-21 15:52:42'),(53,'/aoogi/course/create','/aoogi/course/create','admin/course/create','新增课程',NULL,50,0,0,1,1,'2019-05-21 15:52:42'),(54,'/aoogi/course','/aoogi/course/save','admin/course/save','保存课程',NULL,50,0,0,1,0,'2019-05-21 15:52:42'),(55,'/aoogi/course/edit/:id','/aoogi/course/edit/*','admin/course/edit','编辑课程',NULL,50,0,0,1,1,'2019-05-21 15:52:42'),(56,'/aoogi/course/:id','/aoogi/course/update/*','admin/course/update','更新课程',NULL,50,0,0,1,0,'2019-05-21 15:52:42'),(57,'/aoogi/course/:id','/aoogi/course/del/*','admin/course/delete','删除课程',NULL,50,0,0,1,0,'2019-05-21 15:52:42'),(58,'/aoogi/mold','/aoogi/mold','admin/mold/index','类型管理','#xe653;',41,0,1,1,1,'2019-05-21 16:08:05'),(59,'/aoogi/mold/data','/aoogi/mold/data','admin/mold/getData','类型列表',NULL,58,0,0,1,0,'2019-05-21 16:08:05'),(60,'/aoogi/mold/create','/aoogi/mold/create','admin/mold/create','新增类型',NULL,58,0,0,1,1,'2019-05-21 16:08:05'),(61,'/aoogi/mold','/aoogi/mold/save','admin/mold/save','保存类型',NULL,58,0,0,1,0,'2019-05-21 16:08:05'),(62,'/aoogi/mold/edit/:id','/aoogi/mold/edit/*','admin/mold/edit','编辑类型',NULL,58,0,0,1,1,'2019-05-21 16:08:05'),(63,'/aoogi/mold/:id','/aoogi/mold/update/*','admin/mold/update','更新类型',NULL,58,0,0,1,0,'2019-05-21 16:08:05'),(64,'/aoogi/mold/:id','/aoogi/mold/del/*','admin/mold/delete','删除类型',NULL,58,0,0,1,0,'2019-05-21 16:08:05'),(65,'/aoogi/course/:id','/aoogi/course/read/*','admin/course/read','查看课程','',50,0,0,1,1,'2019-05-21 17:57:51'),(66,'/aoogi/questions','/aoogi/questions','admin/questions/index','题库管理','#xe653;',41,0,1,1,1,'2019-05-21 18:13:20'),(67,'/aoogi/questions/data','/aoogi/questions/data','admin/questions/getData','题库列表',NULL,66,0,0,1,0,'2019-05-21 18:13:20'),(68,'/aoogi/questions/status','/aoogi/questions/status','admin/questions/setStatus','题库状态',NULL,66,0,0,1,0,'2019-05-21 18:13:20'),(69,'/aoogi/questions/create','/aoogi/questions/create','admin/questions/create','新增题库',NULL,66,0,0,1,1,'2019-05-21 18:13:21'),(70,'/aoogi/questions','/aoogi/questions/save','admin/questions/save','保存题库',NULL,66,0,0,1,0,'2019-05-21 18:13:21'),(71,'/aoogi/questions/edit/:id','/aoogi/questions/edit/*','admin/questions/edit','编辑题库',NULL,66,0,0,1,1,'2019-05-21 18:13:21'),(72,'/aoogi/questions/:id','/aoogi/questions/update/*','admin/questions/update','更新题库',NULL,66,0,0,1,0,'2019-05-21 18:13:21'),(73,'/aoogi/questions/:id','/aoogi/questions/del/*','admin/questions/delete','删除题库',NULL,66,0,0,1,0,'2019-05-21 18:13:21'),(74,'/aoogi/questions/:id','/aoogi/questions/read/*','admin/questions/read','查看题库',NULL,66,0,0,1,1,'2019-05-21 18:13:21');

#
# Structure for table "users"
#

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `mobile` varchar(11) DEFAULT NULL,
  `delete_time` timestamp NULL DEFAULT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

#
# Data for table "users"
#

INSERT INTO `users` VALUES (1,'Libai','96e79218965eb72c92a549dd5a330112','18297905432',NULL,'2018-10-10 15:08:34'),(2,'Wangwei','96e79218965eb72c92a549dd5a330112','13564078415',NULL,'2018-10-10 15:08:55'),(3,'Dufu','96e79218965eb72c92a549dd5a330112','17333007330',NULL,'2018-10-10 15:09:11');
