<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class DemoController extends Controller
{

    public function actionIndex()
    {

        Console::startProgress(0, 500);
        for ($n = 1; $n <= 500; $n++)
        {
            usleep(1000);
            Console::updateProgress($n, 500);
        }
        Console::endProgress("done." . PHP_EOL);
    }

}
