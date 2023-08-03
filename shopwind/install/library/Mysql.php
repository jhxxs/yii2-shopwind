<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace install\library;

use yii;

use common\library\Timezone;
use common\library\Language;

/**
 * @Id Mysqli7.php 2018.10.31 $
 * @author mosir
 */

class Mysql
{
	/**
	 * 数据库连接地址
	 */
	public $db_host = '127.0.0.1';

	/**
	 * 数据库连接端口
	 */
	public $db_port = '3306';

	/**
	 * 数据库名
	 */
	public $db_name = 'shopwind';

	/**
	 * 数据库用户
	 */
	public $db_user = 'root';

	/**
	 * 数据库密码
	 */
	public $db_password = '';

	/**
	 * 数据库表前缀
	 */
	public $db_prefix = 'swd_';

	/**
	 * 错误抓取
	 */
	public $errors = null;

	/**
	 * 构造函数
	 * @param array $options
	 */
	public function __construct($options = [])
	{
		if (is_array($options)) {
			foreach ($options as $key => $value) {
				$this->$key = $value;
			}
		}
	}

	/** 
	 * sql连接
	 * @param string $db_name 当服务器没有数据库的情况下，执行connect不会自动创建
	 * @desc mysql_connect 自 PHP 5.5.0 起已废弃，并在自 PHP 7.0.0 开始被移除
	 */
	public function connect($db_name = null)
	{
		// 此处指检查连接是否正常，此时有可能还没有创建数据库
		try {
			$link = mysqli_connect($this->db_host, $this->db_user, $this->db_password, $db_name, $this->db_port);
			return $link;
		} catch (\Exception $e) {
			$this->errors = $e->getMessage();
			return false;
		}

		// 对象方式连接，由于无法有效抓取错误，暂不采纳
		//$link = new mysqli($this->db_host, $this->db_user, $this->db_password, $db_name, $this->db_port);

		/*
		$link = mysqli_connect($this->db_host, $this->db_user, $this->db_password, $db_name, $this->db_port);
		if (!$link) {
			$this->errors = iconv('gbk', 'utf-8', mysqli_connect_error()); // 如果不用ICONV，停止MYSQL后的报错字符无法正常输出
			return false;
		}

		return $link;
		*/
	}

	/**
	 * 获取Yii连接数据库的DSN
	 */
	public function getDsn()
	{
		return 'mysql:host=' . $this->db_host . ';port=' . $this->db_port . ';dbname=' . $this->db_name;
	}

	/**
	 * 判断数据库是否存在 
	 */
	public function checkDb()
	{
		if (!$this->db_name) {
			$this->errors = sprintf('database `%s` empty', $this->db_name);
			return false;
		}

		$link = $this->connect();
		if (!@mysqli_select_db($link, $this->db_name)) {
			$this->errors = mysqli_error($link);
			return false;
		}
		return true;
	}

	/* 创建数据库 */
	public function createDb()
	{
		$link = $this->connect();
		$sql = "CREATE DATABASE IF NOT EXISTS `{$this->db_name}` DEFAULT CHARACTER SET " . str_replace('-', '', Yii::$app->charset);
		if (!@mysqli_query($link, $sql)) {
			$this->errors = mysqli_error($link);
			return false;
		}
		return true;
	}

	/**
	 * 检查表是否存在（避免覆盖表） 
	 * @param bool $force 当有同名数据表的时候，是否强制创建
	 */
	public function checkTable($force = false)
	{
		$link = $this->connect($this->db_name);
		$query = mysqli_query($link, "SHOW TABLES LIKE '{$this->db_prefix}%'");

		$sameTable = false;
		while ($row = mysqli_fetch_assoc($query)) {
			$sameTable = true;
			break;
		}

		// 不同意强制安装，则显示错误
		if ($sameTable && !$force) {
			$this->errors = Language::get('table_existed');
			return false;
		}
		return true;
	}

	public function getSql($sql)
	{
		$sql = $this->replacePrefix('swd_', $this->db_prefix, $sql);
		if (stripos($sql, 'IF NOT EXISTS ') >= 0) $sql = str_replace('IF NOT EXISTS ', '', $sql);

		if (substr($sql, 0, 12) == 'CREATE TABLE') {
			$name = preg_replace("/CREATE TABLE `{$this->db_prefix}([a-z0-9_]+)` .*/is", "\\1", $sql);
			return [$this->formatSql($sql, $name), 'CREATE', $name];
		}

		return [$sql, '', ''];
	}

	/**
	 * 有可能SQL文件并没有删除表的语句，所以在创建表的时候，最好先删表再创建表 
	 */
	private function formatSql($sql = '', $name = '')
	{
		$type = strtoupper(preg_replace("/^\s*CREATE TABLE\s+.+\s+\(.+?\).*(ENGINE|TYPE)\s*=\s*([a-z]+?).*$/isU", "\\2", $sql));
		$type = in_array($type, array('MYISAM', 'HEAP')) ? $type : 'MYISAM';

		$dropSql = "DROP TABLE IF EXISTS `{$this->db_prefix}{$name}`;";
		return $dropSql . preg_replace("/^\s*(CREATE TABLE\s+.+\s+\(.+?\)).*$/isU", "\\1", $sql) . " ENGINE={$type} DEFAULT CHARSET=" . str_replace('-', '', Yii::$app->charset);
	}

	public function replacePrefix($orig, $target, $sql)
	{
		return str_replace('`' . $orig, '`' . $target, $sql);
	}

	/**
	 * 创建网站管理员账号
	 */
	public function createCommand($link, $post = null)
	{
		$admin = $post->admin_name;
		$password = Yii::$app->security->generatePasswordHash($post->admin_pass);
		$sql = "INSERT INTO `{$this->db_prefix}user`(username,password,create_time) VALUES('{$admin}','{$password}'," . Timezone::gmtime() . ")";
		$insertId = $link->createCommand($sql)->execute();

		if ($insertId > 0) {
			$sql = "REPLACE INTO `{$this->db_prefix}user_priv`(userid,store_id,privs) VALUES({$insertId},0,'all')";
			$link->createCommand($sql)->execute();
		}
	}
}
