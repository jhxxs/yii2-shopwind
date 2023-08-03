<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;

use common\library\Language;

/**
 * @Id ChannelForm.php 2018.9.10 $
 * @author mosir
 */
class ChannelForm extends Model
{
	public $page = '';
	public $client = null;
	private $confpath = null;
	public $template = 'default';
	public $errors = null;

	public function __construct($options = null)
	{
		parent::__construct($options);

		if (!$this->client) $this->client = 'h5';
		$this->confpath = Yii::getAlias('@public') . '/data/pagediy/' . $this->client;
	}

	public function valid($post)
	{
		if (empty($post->title)) {
			$this->errors = Language::get('title_empty');
			return false;
		}
		if (empty($post->page)) {
			$this->errors = Language::get('pageid_empty');
			return false;
		}

		// 页面唯一性检测
		$thisfile = $this->getFile($post->page, true);
		if ($list = FileHelper::findFiles($this->confpath, ['recursive' => false, 'only' => ['*.config.php']])) {
			if (in_array($thisfile, $list)) {
				if (!$this->page || ($this->page != $post->page)) {
					$this->errors = Language::get('pageid_existed');
					return false;
				}
			}
		}

		return true;
	}

	public function save($post, $valid = true)
	{
		if ($valid === true && !$this->valid($post)) {
			return false;
		}

		$array = [];
		if ($this->page) {
			$file = $this->getFile($this->page, true);
			$array = require($file);

			if ($this->page != $post->page) {
				unlink($file);
			}
		}

		$file = $this->getFile($post->page, true);
		$phpcode = sprintf(
			"<?php \n\n
			return array(\n\t
			'page' => array('id' => '%s', 'title' => '%s', 'enabled' => %s),\n\t
			'widgets' => %s,\n\t
			'config' => %s\n
			);",
			$post->page,
			$post->title,
			intval($post->enabled),
			$array ? var_export($array['widgets'], true) : 'array()',
			$array ? var_export($array['config'], true) : 'array()'
		);

		if (!file_put_contents($file, $phpcode)) {
			$this->errors = Language::get('create_fail');
			return false;
		}

		return true;
	}

	public function delete()
	{
		if (!$this->page) {
			$this->errors = Language::get('no_such_page');
			return false;
		}

		$file = $this->getFile($this->page, true);
		if (!file_exists($file)) {
			$this->errors = Language::get('no_such_page');
			return false;
		}

		return unlink($file);
	}

	public function getFile($page, $fullpath = false)
	{
		$file = $this->template . '.' . $page . '.config.php';
		if ($fullpath) {
			return $this->confpath . DIRECTORY_SEPARATOR . $file;
		}
		return $file;
	}
}
