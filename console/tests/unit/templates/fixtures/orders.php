<?php
/**
 * 生成加订假数据.
 * User: Pengfan
 * Date: 2018/12/22
 * Time: 16:26
 */


# console cli 命令"php yii fixture/generate orders --count=15

# 生成15条数据
/**
 * @var $faker \Faker\Generator
 */
$faker = Faker\Factory::create('zh_CN');
return [
    'order_id' => $faker->randomNumber(),
    'uid' => 1,
    'x_id' => 31,
    'order_total_price' => $faker->numberBetween(100,300),
    'actual_price' => $faker->numberBetween(100,200),
    'other_price' => $faker->numberBetween(10,20),
    'service_price' => $faker->numberBetween(1, 20),
    'dis_price' => $faker->numberBetween(20,100),
    'activity_price' => $faker->numberBetween(10,20),
    'type' => mt_rand(1,4),
    'create_order_time' => $faker->unixTime,
    'pay_time' => $faker->unixTime,
    'is_pay_success' => 1,
    'is_with' => 1,
    'pay_method' => 1
];