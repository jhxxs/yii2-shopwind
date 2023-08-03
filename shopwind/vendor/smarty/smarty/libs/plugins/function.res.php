<?php

/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage PluginsFunction
 */

/**
 * Smarty {res} function plugin
 *
 * @author  shopwind <shopwind.net>
 * @version 1.0
 *
 * @param array $params parameters
 * @param Smarty_Internal_Template $template template object
 *
 * @throws SmartyException
 * @return string
 */

use common\library\Resource;

function smarty_function_res($params, $template)
{
	return Resource::getThemeAssetsUrl($params);
}
