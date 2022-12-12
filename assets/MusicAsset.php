<?php

namespace app\assets;

use yii\web\AssetBundle;

class MusicAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css',
        'css/site.css'
    ];
    public $js = [
        'https://unpkg.com/wavesurfer.js',
        'https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js'
    ];
    public $depends = [
        'yii\web\YiiAsset'    
    ];
}

?>