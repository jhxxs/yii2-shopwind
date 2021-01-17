<?php

/**
 * @link http://www.shopwind.net/
 * @copyright Copyright (c) 2018 shopwind, Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license http://www.shopwind.net/license/
 */

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

use common\library\Basewind;
use common\library\Language;
use common\library\Tree;

/**
 * @Id RegionModel.php 2018.4.22 $
 * @author mosir
 */

class RegionModel extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%region}}';
    }
	
	/**
     * 取得地区列表
     *
     * @param int $parent_id 大于等于0表示取某个地区的下级地区，小于0表示取所有地区
	 * @param bool $shown    只取显示的地区
     * @return array
     */
    public static function getList($parent_id = -1, $shown = true, $cached = true)
    {
		$cache = Yii::$app->cache;
		$cachekey = md5((__METHOD__).var_export(func_get_args(), true));
		$data = $cache->get($cachekey);
		if($data === false || !$cached) 
		{
			$query = parent::find()->orderBy(['sort_order' => SORT_ASC, 'region_id' => SORT_ASC]);
			if($shown) $query->andWhere(['if_show' => 1]);
			
			if ($parent_id >= 0) {
				$query->where(['parent_id' => $parent_id]);
			
				// 处理第一级有多条记录的情况，比如：中国，日本，只取第一个顶级分类的地区（如果第一级是国家，请把注释去掉）
				//($parent_id == 0) && $query->limit(1);
			}
			$data = $query->asArray()->all();
			
			$cache->set($cachekey, $data, 3600);
		}
        return $data;
    }
	
	/* 取得所有地区 
	 * 保留级别缩进效果，一般用于select
	 * @return array(21 => 'abc', 22 => '&nbsp;&nbsp;');
	 */
    public static function getOptions($parent_id = -1, $except = null, $layer = 0, $shown = true, $space = '&nbsp;&nbsp;')
    {
		$regions = self::getList($parent_id, $shown);
		
		$tree = new Tree();
		$tree->setTree($regions, 'region_id', 'parent_id', 'region_name');
			
        return $tree->getOptions($layer, 0, $except, $space);
    }
	
	/* 寻找某ID的所有父级 */
	public static function getParents($id = 0, $selfIn = true)
	{
		$result = array();
		if(!$id) return $result;
		
		if($selfIn) $result[] = $id;
		while(($query = parent::find()->select('region_id,parent_id,region_name')->where(['region_id' => $id])->one())) {
			if($query->parent_id) $result[] = $query->parent_id;
			$id = $query->parent_id;
		}
		return array_reverse($result);
	}
	
	/**
     * 取得某分类的子孙分类id
     * @param int  $id     分类id
     * @param bool $cached 是否缓存
	 * @param bool $shown  只取要显示的地区
	 * @param bool $selfin 是否包含自身id
	 * @return array(1,2,3,4...)
	 */
	public static function getDescendantIds($id = 0, $cached = true, $shown = false, $selfin = true)
	{
		$cache = Yii::$app->cache;
		$cachekey = md5((__METHOD__).var_export(func_get_args(), true));
		$data = $cache->get($cachekey);
		if($data === false || !$cached) 
		{
			$conditions = $shown ? ['if_show' => 1] : null;
		
			$tree = new Tree();
			$data = $tree->recursive(new RegionModel(), $conditions)->getArrayList($id, 'region_id', 'parent_id', 'region_name')->fields($selfin);
						
			$cache->set($cachekey, $data, 3600);
		}
		return $data;
	}
	
	/*
	 * 获取省市数据
	 */
	public static function getProvinceCity()
	{
		$provinceParentId = self::getProvinceParentId();
		$provinces = self::getList($provinceParentId);
		
		foreach($provinces as $key => $province) {
			$provinces[$key]['cities'] = self::getList($province['region_id']);
		}
		return $provinces;
	}
	
	/*
	 * 获取省市区地址数组
	 */
	public static function getArrayRegion($region_id = 0, $region_name = '')
	{
		$result = array();
		
		// 先通过地区名称来获取
		if($region_name) {
			$array = explode(' ', preg_replace("/\s/"," ", $region_name));
			
		}
		if(empty($array)) {
			$string = self::getRegionName($region_id, true);
			$array = explode(' ', $string);
		}
		
		$fields = ['province', 'city', 'district', 'town'];
		foreach($array as $key => $value) {
			$result[$fields[$key]] = $value;
		}
		return $result;
	}
	
	/* 
	 * 获取省ID的上级ID，考虑第一级是中国的情况 
	 */
	public static function getProvinceParentId($topIsCountry = false)
	{
		if(!$topIsCountry) return 0;
		return parent::find()->select('region_id')->where(['parent_id' => 0])->limit(1)->orderBy(['region_id' => SORT_ASC])->scalar();
	}
	
	/*
	 * 获取多个/一个地区名称路径
	 * 主要用于运费模板功能
	 */
	public static function getRegionName($region_id = 0, $full = false, $split = ',')
	{
		if(in_array($region_id, [0])) {
			return '全国';
		}
		$str = '';
		foreach(explode('|', $region_id) as $id)
		{
			$query = parent::find()->select('region_id,region_name,parent_id')->where(['region_id' => $id])->one();
			if($query) 
			{
				$str1 = $query->region_name;
				while($full && $query->parent_id != 0) {
					$query = parent::find()->select('region_id,region_name,parent_id')->where(['region_id' => $query->parent_id])->one();
					$str1 = $query->region_name .' '. $str1;
				}
				$str .= $split . $str1;
			}
		}
		return $str ? substr($str, 1) : $str;
	}
	
	/*
	 * 通过IP自动获取本地城市id
	 */
	public static function getCityIdByIp($cached = true)
	{
		$cache = Yii::$app->cache;
		$cachekey = md5((__METHOD__).var_export(func_get_args(), true));
		$data = $cache->get($cachekey);
		if($data === false || !$cached)
		{
			$ip = Yii::$app->request->userIP;
			$address = self::getAddressByIp($ip);
			if($address && !$address['local'])
			{
				$province 	= $address['province'];
				$city 		= $address['city'];
				
				$provinceParentId = self::getProvinceParentId();
				$regionProvince = parent::find()->select('region_id,region_name')->where(['parent_id' => $provinceParentId])->andWhere(['in', 'region_name', [$province, str_replace('省', '', $province)]])->one();
			
				if($regionProvince) {
					$regionCity = parent::find()->select('region_id,region_name')->where(['parent_id' => $regionProvince->region_id])->andWhere(['in', 'region_name', [$city, str_replace('市', '', $city)]])->one();
					if($regionCity) {
						$data = $regionCity->region_id;
						$cache->set($cachekey, $data, 3600);
					}
				}
			}
		}
		return $data ? $data : 0;
	}
	
	/**
	 * 使用淘宝的IP库
	 * @api http://ip.taobao.com
	 */
	public static function getAddressByIp($ip = '')
	{
		if(empty($ip) || in_array($ip, ['127.0.0.1', 'localhost'])) {
			return ['city' => Language::get('local')];
		}

		$result = Basewind::curl('http://ip.taobao.com/outGetIpInfo.php?ip='.$ip);
		$result = json_decode($result);
		if($result->code == 0 && $result->data->city) {
			return array_merge(
				['province' => $result->data->region, 'city' => $result->data->city], 
				['local' => $result->data->city_id == 'local' ? true : false]
			);
		}
		return array();
	}
}

