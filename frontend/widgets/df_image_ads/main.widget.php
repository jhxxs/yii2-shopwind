<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace frontend\widgets\df_image_ads;

use Yii;

use common\widgets\BaseWidget;

/**
 * @Id main.widget.php 2018.9.7 $
 * @author mosir
 */
 
class Df_image_adsWidget extends BaseWidget
{
    var $name = 'df_image_ads';

    public function getData()
    {
        return array(
            'ad_image_url'  		=> $this->options['ad_image_url'],
            'ad_link_url'   		=> $this->options['ad_link_url'],
			'ad_width'      		=> $this->options['ad_width'],
			'ad_height'     		=> $this->options['ad_height'],
			'ad_border'     		=> $this->options['ad_border'],
			'ad_margin'     		=> $this->options['ad_margin'],
			'ad_background_color'  	=> $this->options['ad_background_color'],
			'ad_button_close'		=> $this->options['ad_button_close']
        );
    }

    public function parseConfig($input)
    {
        if (($image = $this->upload('ad_image_file'))) {
            $input['ad_image_url'] = $image;
        }
        return $input;
    }
}
