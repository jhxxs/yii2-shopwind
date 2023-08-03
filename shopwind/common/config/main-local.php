<?php

$abcdefile = Yii::getAlias('@public') . '/data/config.php';
return [
    'components' => array_merge(file_exists($abcdefile) ? require($abcdefile) : [], [])
];