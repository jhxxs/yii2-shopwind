<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace common\plugins\other\cleanup;


use yii;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;

use common\models\UserModel;
use common\models\UserPrivModel;
use common\models\BindModel;

use common\library\Language;
use common\plugins\BaseOther;

/**
 * @Id cleanup.plugin.php 2023.3.5 $
 * @author mosir
 */

class Cleanup extends BaseOther
{
    /**
     * 插件实例
     * @var string $code
     */
    protected $code = 'cleanup';

    /**
     * SDK实例
     * @var object $client
     */
    private $client = null;

    public $result;

    public function index()
    {
        if (file_exists(Yii::getAlias('@public/data/cleardata.lock'))) {
            $this->errors = '请先删除文件cleardata.lock';
            return false;
        }

        $post = empty($this->params->post) ? [] : ArrayHelper::toArray($this->params->post);

        $list = Yii::$app->db->createCommand('SHOW TABLE STATUS')->queryAll();
        foreach ($list as $value) {

            $table = str_replace(Yii::$app->db->tablePrefix, '', $value['Name']);
            if (!empty($post)) {
                if (in_array($table, $post)) {
                    continue;
                }
            }

            // 先不删除用户，用户基础表做特殊处理
            if (in_array($table, ['user', 'bind', 'user_priv'])) {
                continue;
            }

            Yii::$app->db->createCommand('TRUNCATE TABLE ' . $value['Name'])->execute();
        }

        // 如果选择不保留用户，那么把用户删除，仅仅保留超管账户
        if (empty($post) || !in_array('user', $post)) {
            $this->reserveUser();
        }

        // 删除文件
        $this->deleteFiles($post);
        $this->result = ['message' => Language::get('handle_ok')];

        return true;
    }

    private function reserveUser()
    {
        $query = UserPrivModel::find()
            ->Where(['privs' => 'all', 'store_id' => 0])
            ->orderBy(['userid' => SORT_ASC])
            ->one();

        UserModel::deleteAll(['!=', 'userid', $query->userid]);
        BindModel::deleteAll(['!=', 'userid', $query->userid]);
        UserPrivModel::deleteAll(['!=', 'userid', $query->userid]);
    }

    private function deleteFiles($post = [])
    {
        $dir = Yii::getAlias('@public/data/files');
        $list = FileHelper::findDirectories($dir, ['recursive' => false]);

        $folders = [];
        foreach ($list as $item) {
            $folders[] = substr($item, strripos($item, DIRECTORY_SEPARATOR) + 1);
        }

        foreach ($folders as $folder) {
            if ($folder == 'mall') {
                foreach (array_merge(['mall/article', 'mall/brand', 'mall/webim'], in_array('gcategory', $post) ? ['mall/gcategory'] : []) as $value) {
                    FileHelper::removeDirectory($dir . '/' . $value);
                }
                continue;
            }
            FileHelper::removeDirectory($dir . '/' . $folder);
        }
    }
}
