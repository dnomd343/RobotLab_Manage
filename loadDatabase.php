<?php

// 作用：初始化数据库
// $demo = new loadDatabase($host, $user, $passwd, $database);
// $demo->init();

class loadDatabase {
    private $loadSQL; // 初始化SQL语句
    private $mysqlServer; // MySQL数据库地址
    private $mysqlUser; // MySQL用户名
    private $mysqlPasswd; // MySQL用户密码
    private $databaseName; // 目标数据库名称
    
    private function loadSqlString() { // 载入SQL建表语句
        // 人员信息表
        $this->loadSQL['_user'] = "DROP TABLE IF EXISTS `user`;";
        $this->loadSQL['user'] = "CREATE TABLE `user` (
            `id` int NOT NULL COMMENT '人员唯一ID',
            `account` varchar(255) NOT NULL COMMENT '账户名称',
            `passwd` varchar(255) NOT NULL COMMENT '密码哈希值',
            `group_id` int NOT NULL COMMENT '人员所属群组',
            `status` int NOT NULL COMMENT '用户当前状态',
            `is_caption` int NOT NULL COMMENT '是否为队长',
            `data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '包括名字、邮箱、电话号码等（数组）',
            PRIMARY KEY (`id`),
            UNIQUE KEY `account` (`account`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;";

        // 群组信息表
        $this->loadSQL['_group'] = "DROP TABLE IF EXISTS `group`;";
        $this->loadSQL['group'] = "CREATE TABLE `group` (
            `id` int NOT NULL COMMENT '群组唯一ID',
            `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '群组名称',
            `remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci COMMENT '备注信息',
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;";

        // A类物品种类记录表
        $this->loadSQL['_a_kind'] = "DROP TABLE IF EXISTS `a_kind`;";
        $this->loadSQL['a_kind'] = "CREATE TABLE `a_kind` (
            `id` int NOT NULL COMMENT '物品种类唯一ID',
            `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '物品名称',
            `owner` int NOT NULL COMMENT '所属群组',
            `remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci COMMENT '备注信息',
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;";

        // A类物品记录表
        $this->loadSQL['_a_status'] = "DROP TABLE IF EXISTS `a_status`;";
        $this->loadSQL['a_status'] = "CREATE TABLE `a_status` (
            `id` int NOT NULL COMMENT '物品唯一ID',
            `kind_id` int NOT NULL COMMENT '所属种类ID',
            `time` datetime NOT NULL COMMENT '购入时间',
            `purchaser` int NOT NULL COMMENT '购入人员',
            `principal` int NOT NULL COMMENT '物品负责人',
            `status` int NOT NULL COMMENT '物品当前状态',
            `is_approve` int NOT NULL COMMENT '物品借出是否需要审批',
            `remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci COMMENT '备注信息',
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;";

        // A类物品借出记录表
        $this->loadSQL['_a_lend'] = "DROP TABLE IF EXISTS `a_lend`;";
        $this->loadSQL['a_lend'] = "CREATE TABLE `a_lend` (
            `id` int NOT NULL COMMENT '借出记录的唯一ID',
            `time` datetime NOT NULL COMMENT '借出或移交时间',
            `lender` int NOT NULL COMMENT '借出或移交后物品所在人员',
            `approver` int NOT NULL COMMENT '审批人员',
            `method` int NOT NULL COMMENT '物品借出方式',
            `remark` text CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT '备注信息',
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;";

        // A类物品审批状态表
        $this->loadSQL['_a_approve'] = "DROP TABLE IF EXISTS `a_approve`;";
        $this->loadSQL['a_approve'] = "CREATE TABLE `a_approve` (
            `id` int NOT NULL COMMENT '请求借出或移交的物品ID',
            `submitter` int NOT NULL COMMENT '申请审批的人员',
            `time` datetime NOT NULL COMMENT '申请提交的时间',
            `approver` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '标记请求目标人员（数组）',
            `ignore` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '标记目标人员忽略请求（数组）',
            `remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci COMMENT '备注信息',
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;";

        // B类耗材状态表
        $this->loadSQL['_b_status'] = "DROP TABLE IF EXISTS `b_status`;";
        $this->loadSQL['b_status'] = "CREATE TABLE `b_status` (
            `id` int NOT NULL COMMENT '耗材唯一ID',
            `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '耗材名称',
            `num` int NOT NULL COMMENT '耗材数量',
            `owner` int NOT NULL COMMENT '所属群组',
            `principal` int NOT NULL COMMENT '耗材负责人',
            `is_approve` int NOT NULL COMMENT '物品借出是否需要审批',
            `remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci COMMENT '备注信息',
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;";

        // B类耗材审批状态表
        $this->loadSQL['_b_approve'] = "DROP TABLE IF EXISTS `b_approve`;";
        $this->loadSQL['b_approve'] = "CREATE TABLE `b_approve` (
        `id` int NOT NULL COMMENT '请求借出的物品ID',
        `submitter` int NOT NULL COMMENT '申请审批的人员',
        `time` datetime NOT NULL COMMENT '申请提交的时间',
        `approver` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '标记请求目标人员（数组）',
        `ignore` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '标记目标人员忽略请求（数组）',
        `remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci COMMENT '备注信息',
        PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;";

        // C类时耗品状态表
        $this->loadSQL['_c_status'] = "DROP TABLE IF EXISTS `c_status`;";
        $this->loadSQL['c_status'] = "CREATE TABLE `c_status` (
            `id` int NOT NULL COMMENT '时耗品唯一ID',
            `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '时耗品名称',
            `owner` int NOT NULL COMMENT '时耗品所属群组',
            `principal` int NOT NULL COMMENT '时耗品负责人',
            `end_time` datetime NOT NULL COMMENT '时耗品到期时间',
            `remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci COMMENT '备注信息',
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;";
    }

    public function __construct($mysqlServer, $mysqlUser, $mysqlPasswd, $databaseName) { // 构造函数
        $this->mysqlServer = $mysqlServer;
        $this->mysqlUser = $mysqlUser;
        $this->mysqlPasswd = $mysqlPasswd;
        $this->databaseName = $databaseName;
        $this->loadSqlString();
    }

    public function init() { // 执行数据库初始化
        $conn = new mysqli($this->mysqlServer, $this->mysqlUser, $this->mysqlPasswd, $this->databaseName);
        if ($conn->connect_error) {
            die("连接失败: " . $conn->connect_error);
        }
        foreach ($this->loadSQL as $sql) { // 遍历初始化SQL语句
            if (!$conn->query($sql)) {
                die("执行错误: " . $conn->error);
            }
        }
        $conn->close();
    }
}

?>