<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace common\library;

use yii;

use common\library\Basewind;

/**
 * @Id Resource.php 2018.3.13 $
 * @author mosir
 */

class Resource
{
    /**
     * 获取模板样式图片/文件绝对路径
     * @desc 注意不要返回相对路径，因为在视图中可能会使用|url_fromat标签，导致转义路径不对
     * @param array|string $params
     */
    public static function getThemeAssetsUrl($params = null)
    {
        $file = is_array($params) ? $params['file'] : $params;
        if (is_array($params)) {
            if (isset($params['baseUrl'])) {
                return $params['baseUrl'] . '/static/' . $file;
            }
        }

        return  Basewind::siteUrl() . '/static/' . $file;
    }

    /**
     * 获取公共资源
     * 可拓展解决以下需求：
     * 发布资源到WEB可访问的目录，并返回资源在发布目录的文件路径[AssetManager]
     * 需要发布资源的场景为：前后台需要公用资源且前后台为独立域名访问，解决资源跨域无法访问问题
     * @param array|string $params
     * @demo in PHP: import('javascript/dialog/dialog.js,javascript/shopwind.js')
     *       in VIEW: {lib file='javascript/dialog/dialog.js'}
     */
    public static function getResourceUrl($params = [])
    {
        $file = is_array($params) ? $params['file'] : $params;
        $jsRootPath = Yii::getAlias('@public') . '/static/';

        // 直接返回访问的资源文件
        if (is_dir(($jsRootPath))) {
            return Basewind::baseUrl() . '/static/' . $file;
        }

        return '';
    }

    /**
     * 导入资源到视图
     * @param string $spec_type 支持多个资源文件JS/CSS
     */
    public static function import($resources, $spec_type = null)
    {
        $headtag = '';
        if (is_string($resources) || $spec_type) {
            !$spec_type && $spec_type = 'script';
            $resources = self::getResourceData($resources);
            foreach ($resources as $params) {
                $headtag .= self::getResourceCode($spec_type, $params) . PHP_EOL;
            }
        } elseif (is_array($resources)) {
            foreach ($resources as $type => $res) {
                $headtag .= self::import($res, $type);
            }
        }
        return $headtag ?  rtrim($headtag, PHP_EOL) : null;
    }

    /**
     * 获取资源数据
     * @param mixed $resources
     */
    public static function getResourceData($resources)
    {
        $result = array();
        if (is_string($resources)) {
            $items = explode(',', $resources);
            //array_walk($items, create_function('&$val, $key', '$val = trim($val);'));

            // 去掉所有项目的空格 for PHP >= 7
            array_walk($items, function (&$val, $key) {
                $val = trim($val);
            });

            foreach ($items as $value) {
                list($path, $depends) = stripos($value, '|') > -1 ? explode('|', $value) : [$value, ''];
                if ($depends) {
                    $array = explode(':', $depends);
                    !empty($array[1]) && $depends = $array[1];
                }
                $result[] = array('file' => $path, 'depends' => $depends);
            }
        }
        return $result;
    }

    /**
     * 获取资源文件的HTML代
     * @param string $type
     * @param array  $params
     */
    public static function getResourceCode($type, $params)
    {
        switch ($type) {
                // 资源目录下的JS文件
            case 'script':
                $pre = '<script type="text/javascript"';
                $path = ' src="' . self::getResourceUrl($params) . '"';
                $attr = ' charset="' . Yii::$app->charset . '" ';
                $tail = '></script>';
                break;
                // 加载远程的JS
            case 'remote':
                $pre = '<script type="text/javascript"';
                $path = ' src="' . $params['file'] . '"';
                $attr = ' charset="' . Yii::$app->charset . '" ';
                $tail = '></script>';
                break;
                // 资源目录下的CSS文件
            case 'style':
                $pre = '<link type="text/css" ';
                $path = ' href="' . self::getResourceUrl($params) . '"';
                $attr = ' rel="stylesheet" ';
                $tail = ' />';
                break;
        }
        $html = $pre . $path . $attr . $tail;

        return $html;
    }
}
