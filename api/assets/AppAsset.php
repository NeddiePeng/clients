<?php

namespace api\assets;

use yii\web\AssetBundle;
use yii\web\View;

/**
 * Main api application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
    ];
    public $js = [
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

    /**
     * @inheritdoc
     */
    public $jsOptions = [
        'position' => View::POS_HEAD
    ];
}
