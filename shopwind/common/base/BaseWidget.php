<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace common\base;

use Yii;

use common\models\UploadedFileModel;
use common\library\Basewind;

/**
 * @Id BaseWidget.php 2018.9.6 $
 * @author mosir
 */

class BaseWidget
{
    var $instance = null;
    var $clientPath = null;
    var $id = null; //在页面中的唯一标识

    var $options = null;    //显示选项
    var $name = null;     //挂件标识
    var $ttl = 3600;     //缓存时间

    var $params = null;
    var $errors = null;

    public function __construct($instance, $clientPath, $id, $options = [])
    {
        $this->BaseWidget($instance, $clientPath, $id, $options);
    }
    public function BaseWidget($instance, $clientPath, $id, $options = [])
    {
        $this->instance = $instance;
        $this->clientPath = $clientPath;
        $this->id = $id;

        $this->setOptions($options);
        $this->params['id'] = $this->id;
        $this->params['name'] = $this->name;
    }

    /* 设置选项 */
    public function setOptions($options)
    {
        if (!$options) {
            $options = [];
        }
        $this->options = $options;
        $this->params['homeUrl'] = Basewind::homeUrl();
        $this->params['mobileUrl'] = Basewind::mobileUrl();
        $this->params['options'] = $this->options;
    }

    /* 将取得的数据按模板的样式输出 */
    public function getContents()
    {
        // 获取挂件数据
        $this->params['widget_data'] = array_merge(['uniqueid' => mt_rand()], $this->getData());
        $this->params['options'] = $this->options;
        return $this->wrapContents($this->fetch('widget'));
    }

    /* 获取标准的挂件HTML */
    public function wrapContents($html)
    {
        return "\r\n<div id=\"{$this->id}\" name=\"{$this->name}\" widget_type=\"widget\" class=\"widget\">\r\n" . $html . "\r\n</div>\r\n";
    }

    /* 获取指定模板的数据 */
    public function fetch($tpl)
    {
        return Yii::$app->controller->renderFile($this->getTpl($tpl), $this->params);
    }

    /* 取模板 */
    public function getTpl($tpl)
    {
        return $this->clientPath . '/widgets/' . $this->name . "/{$tpl}.html";
    }

    public function display()
    {
        echo $this->getContents();
    }

    /* 获取配置表单 */
    public function getConfigForm()
    {
        $this->getConfigDataSrc();
        return $this->fetch('config');
    }

    public function getData()
    {
    }

    /* 传递配置页面需要的一些变量 */
    public function getConfigDataSrc()
    {
    }

    /* 显示配置表单 */
    public function displayConfig()
    {
        echo $this->getConfigForm();
        exit();
    }

    /* 处理配置项 */
    public function parseConfig($input)
    {
        return $input;
    }

    /* 取缓存id */
    public function getCacheId()
    {
        $config = array('widget_name' => $this->name);
        if ($this->options) {
            $config = array_merge($config, $this->options);
        }
        return md5('widget.' . var_export($config, true));
    }

    /* 挂件上传图片 */
    public function upload($fileval = '')
    {
        if (!($filePath = UploadedFileModel::getInstance()->upload($fileval, 'template/'))) {
            return false;
        }
        return $filePath;
    }
}
