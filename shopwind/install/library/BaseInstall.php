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
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;

use common\library\Basewind;
use common\library\Language;
use common\library\Arrayfile;
use common\library\Setting;

use install\library\Mysql;

/**
 * @Id BaseInstall.php 2018.10.31 $
 * @author mosir
 */

class BaseInstall
{
	/**
	 * 数据库实例
	 */
	public $db_type = 'mysql';

	/**
	 * 数据库连接信息
	 */
	public $options = [];

	/**
	 * 错误抓取
	 */
	public $errors = null;

	/**
	 * 构造函数
	 * @param string $db_type
	 */
	public function __construct($db_type = '')
	{
		if ($db_type) {
			$this->db_type = $db_type;
		}
	}

	/**
	 * 获取数据库实例
	 * @param string $instance
	 */
	public static function getInstance($instance = '')
	{
		return new self($instance);
	}

	public function build($params = [])
	{
		$instance = new self($this->db_type);
		$instance->options = $params;
		return $instance;
	}

	private function getClient()
	{
		if ($this->db_type == 'mysql') {

			// PHP >= 7
			if (extension_loaded('mysqli')) {
				return new Mysql($this->options);
			}
		}
		// ...eg mssql

		return null;
	}

	/**
	 * 数据库连接
	 */
	public function connect()
	{
		$client = $this->getClient();
		if (!$client || !($link = $client->connect())) {
			$this->errors = $client->errors;
			return false;
		}

		return $link;
	}

	private function getDb()
	{
		$client = $this->getClient();
		$connection = new \yii\db\Connection([
			'dsn' 		=> $client->getDsn(),
			'username' 	=> $client->db_user,
			'password' 	=> $client->db_password,
		]);

		try {
			$connection->open();
			return $connection;
		} catch (\Exception $e) {
			$this->errors = $e->getMessage();
			return false;
		}
	}

	/**
	 * 判断数据库是否存在 
	 */
	public function checkDb()
	{
		$client = $this->getClient();
		if (!$client) {
			return false;
		}

		if (!$client->checkDb()) {
			$this->errors = $client->errors;
			return false;
		}

		return true;
	}

	/**
	 * 创建数据库 
	 */
	public function createDb()
	{
		$client = $this->getClient();
		if (!$client) {
			return false;
		}

		if (!$client->createDb()) {
			$this->errors = $client->errors;
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
		$client = $this->getClient();
		if (!$client) {
			return false;
		}

		if (!$client->checkTable($force)) {
			$this->errors = $client->errors;
			return false;
		}

		return true;
	}

	/**
	 * 建立数据表结构 
	 */
	public function createTable()
	{
		$client = $this->getClient();
		if (!$client) {
			return false;
		}

		// 输出开始创建表进程
		$this->showProcess(Language::get('start_setup_db'));

		$link = $this->getDb();
		$sqls = $this->getSql(Yii::getAlias('@install') . '/structure.sql');
		if (!$sqls) $sqls = [];

		// 待表创建成功后，插入系统数据（系统分类和系统文章）
		if ($sqls && ($syssql = $this->getSql(Yii::getAlias('@install') . '/systemdata.sql'))) {
			foreach ($syssql as $sql) {
				$sqls[] = $sql;
			}
		}

		foreach ($sqls as $sql) {
			list($sql, $command, $table) = $client->getSql($sql);

			$link->createCommand($sql)->execute();
			if ($command == 'CREATE') {
				$this->showProcess(sprintf(Language::get('create_table'), $table));
			}
		}
	}


	/**
	 * 保存数据库配置信息 
	 */
	public function saveConfig()
	{
		$client = $this->getClient();
		if (!$client) {
			return false;
		}

		// 此处可以根据情况配置多个DB
		$config = array(
			'db' => [
				'class' 		=> 'yii\db\Connection',
				'dsn' 			=> $client->getDsn(),
				'username' 		=> $client->db_user,
				'password' 		=> $client->db_password,
				'charset'  		=> strtolower(str_replace('-', '', Yii::$app->charset)), // utf8mb4
				'tablePrefix' 	=> $client->db_prefix,

				// 该处可以避免从数据库读取的部分数值型字段转化为字符串的情况
				// 但也要注意：因为增加了下面两个PDO参数后，Yii2.0的原有嵌套事务执行会报错，主要原因是框架本身的嵌套事务依赖于模拟预处理
				// 所以开启下面2个参数后，要慎重考虑
				// link：https://www.cnblogs.com/itsharehome/p/12275817.html
				'attributes' => [
					\PDO::ATTR_STRINGIFY_FETCHES => false,
					\PDO::ATTR_EMULATE_PREPARES => false
				]
			]
		);

		$setting = new Arrayfile();
		$setting->savefile = Yii::getAlias('@public') . '/data/config.php';
		$setting->setAll($config);
	}

	/**
	 * 保存站点配置 
	 */
	public function saveSetting()
	{
		$config = ArrayHelper::merge([
			'baseUrl' 	=> Basewind::baseUrl(),
			'homeUrl' 	=> Basewind::homeUrl(),
			'adminUrl'	=> Basewind::adminUrl(),
			'mobileUrl'	=> Basewind::baseUrl() . '/h5',
		], Setting::getDefault());

		return Setting::getInstance()->setAll($config);
	}

	/**
	 * 检测是否安装测试数据 
	 */
	public static function isInited()
	{
		$file = Yii::getAlias('@public') . '/data/initdata.lock';

		// 已经安装了
		if (file_exists($file)) {
			return true;
		}
		return false;
	}

	/**
	 * 插入测试数据
	 * 该操作必须再安装站点，取得DB实例后执行
	 */
	public function initData($buyer_id, $seller_id)
	{
		$connection = Yii::$app->db;
		$sqls = $this->getSql(Yii::getAlias('@install') . '/initdata.sql');
		foreach ($sqls as $sql) {
			$sql = $this->replacePrefix('swd_', $connection->tablePrefix, $sql);
			$sql = str_replace('{seller_id}', $seller_id, $sql);
			$sql = str_replace('{buyer_id}', $buyer_id, $sql);
			$sql = str_replace('{base_url}', Basewind::baseUrl(), $sql);
			$connection->createCommand($sql)->execute();
		}
	}

	/**
	 * 安装测试数据结束 
	 */
	public function initend()
	{
		// 清空缓存
		Yii::$app->cache->flush();

		// 锁定文件
		touch(Yii::getAlias('@public') . '/data/initdata.lock');
	}

	/* 安装结束 */
	public function finished($showprocess = true)
	{
		if ($showprocess) {
			$this->showProcess(Language::get('install_done'), true, 'parent.install_successed();');
		}

		// 锁定安装程序
		touch(Yii::getAlias('@public') . '/data/install.lock');

		// 锁定清除文件
		touch(Yii::getAlias('@public') . '/data/cleardata.lock');
	}

	public function replacePrefix($orig, $target, $sql)
	{
		return str_replace('`' . $orig, '`' . $target, $sql);
	}

	public function getSql($file)
	{
		$contents = file_get_contents($file);
		$contents = str_replace("\r\n", "\n", $contents);
		$contents = trim(str_replace("\r", "\n", $contents));
		$result = $items = [];
		$items = explode(";\n", $contents);
		foreach ($items as $item) {
			$string = '';
			$item = trim($item);
			$lines = explode("\n", $item);
			foreach ($lines as $line) {
				if (isset($line[0]) && $line[0] == '#') {
					continue;
				}
				if (isset($line[1]) && $line[0] . $line[1] == '--') {
					continue;
				}

				$string .= $line;
			}
			if ($string) {
				$result[] = $string;
			}
		}
		return $result;
	}

	/**
	 * 检查环境 
	 */
	public function checkEnv($required)
	{
		$result  = array('detail' => [], 'compatible' => true, 'msg' => []);
		foreach ($required as $key => $value) {
			$checker = $value['checker'];
			$method = $this->$checker();
			$result['detail'][$key] = array(
				'required'  => $value['required'],
				'current'   => $method['current'],
				'result'    => $method['result'] ? 'pass' : 'failed',
			);
			if (!$method['result']) {
				$result['compatible'] = false;
				$result['msg'][] = Language::get($key . '_error');
			}
		}
		return $result;
	}

	/**
	 * 检查文件是否可写 
	 */
	public function checkFile($file)
	{
		if (!is_array($file)) $file = array($file);
		$result = array('detail' => [], 'compatible' => true, 'msg' => []);
		foreach ($file as $key => $value) {
			$writabled = $this->isWriteabled(dirname(__DIR__) . '/' . $value);
			$result['detail'][] = [
				'file' 		=> $value,
				'result'	=> $writabled ? 'pass' : 'failed',
				'current'   => $writabled ? Language::get('writable') : Language::get('unwritable'),
			];
			if (!$writabled) {
				$result['compatible'] = false;
				$result['msg'][] = sprintf(Language::get('file_error'), $value);
			}
		}
		return $result;
	}

	/**
	 * 检查文件是否可写 
	 */
	public function isWriteabled($file)
	{
		if (!file_exists($file)) {
			// 不存在，如果创建失败，则不可写
			if (!FileHelper::createDirectory($file)) {
				return false;
			}
		}
		// 非Windows服务器
		if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
			return is_writable($file);
		}

		// 在Windows的服务器上可能会存在问题，待发现
		if (is_dir($file)) {
			// 如果是目录，则尝试创建文件并修改
			$trail = substr($file, -1);
			if ($trail == '/' || $trail == '\\') {
				$tmpfile = $file . '_temp_file.txt';
			} else {
				$tmpfile = $file . '/' . '_temp_file.txt';
			}
			// 尝试创建文件
			if (false === @touch($tmpfile)) {
				// 不可写
				return false;
			}
			// 创建文件成功
			// 尝试修改该文件
			if (false === @touch($tmpfile)) {
				return false;
			}
			// 修改文件成功
			// 删除文件
			@unlink($tmpfile);
			return true;
		} else {
			// 如果是文件，则尝试修改文件
			if (false === @touch($file)) {
				// 修改不成功，不可写
				return false;
			} else {
				// 修改成功，可写
				return true;
			}
		}
	}

	/**
	 * 复制文件 
	 */
	public function copyFiles()
	{
		FileHelper::copyDirectory(Yii::getAlias('@install') . '/initdata', Yii::getAlias('@public') . '/data');
	}

	/**
	 * 检查PHP版本 
	 */
	public function phpChecker()
	{
		return array(
			'current' => PHP_VERSION,
			'result'  => (PHP_VERSION >= 5.4),
		);
	}

	/* 检查GD版本 */
	public function gdChecker()
	{
		$result = array('current' => null, 'result' => false);
		$gd_info = function_exists('gd_info') ? gd_info() : [];
		$result['current'] = empty($gd_info['GD Version']) ? Language::get('gd_missing') : $gd_info['GD Version'];
		$result['result']  = empty($gd_info['GD Version']) ? false : true;

		return $result;
	}

	/* 显示进程 */
	public function showProcess($msg, $result = true, $script = '')
	{
		ob_implicit_flush(true);

		$class = $result ? 'successed' : 'failed';
		$status = $result ? Language::get('successed') : Language::get('failed');
		$html = "<p>{$msg} <span class=\"{$class}\">{$status}</span></p>";

		echo '<script type="text/javascript">parent.show_process(\'' . $html . '\');' . $script . '</script>';
		ob_flush();
		flush();
	}

	/**
	 * 创建网站管理员账号
	 */
	public function createAdmin($post = null)
	{
		$link = $this->getDb();
		$client = $this->getClient();
		$client->createCommand($link, $post);
	}

	/**
	 * 初始数据，创建用户
	 * @param string $username
	 * @param string $password
	 */
	public function createUser($username, $password = '')
	{
		$user = new \common\models\UserModel();
		$user->username = $username;
		$user->phone_mob = '168' . mt_rand(10000000, 99999999);
		$user->setPassword($password);
		$user->generateAuthKey();
		if (!$user->save()) {
			return false;
		}
		return $user->userid;
	}
}
