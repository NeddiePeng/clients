<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/26
 * Time: 16:37
 */
$arra = [
    [
        'id' => 1,
        'name' => 'waht'
    ],
    [
        'id' => 1,
        'name' => 'success'
    ],
    [
        'id' => 2,
        'name' => 'waht'
    ]
];
$data = \api\modules\Base::array2dUnqied($arra,'name');
var_dump($data);

?>

