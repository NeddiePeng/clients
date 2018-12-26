<?php
// users.php file under template path (by default @tests/unit/templates/fixtures)
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */


// 使用工厂模式生成 Faker\Generator 实例
$faker = Faker\Factory::create('zh_CN');
return [
    'title' => $faker->title,
    'content' => $faker->realText,
    'views' => $faker->firstName,
    'is_delete' => $faker->boolean,
    'created_at' => $faker->date($format = 'Y-m-d', $max = 'now'),
    'updated_at' => $faker->date($format = 'Y-m-d', $max = 'now'),
    'file' => $faker->imageUrl($width = 640, $height = 480),
    'file2' => $faker->imageUrl($width = 640, $height = 480),
];