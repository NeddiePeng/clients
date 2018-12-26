<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/17
 * Time: 11:06
 */
use yii\helpers\Html;
?>
<?=Html::beginForm('','post',['id' => 'form','class' => 'form','data' => 'mmyself']);?>
    <?=Html::input('text','test','',['class' => 'form-control','placeholder'=>'hehe']);?>
    <?=Html::input('email','email','admin@admin.com',['class' => 'form-control']);?>
    <?=Html::input('password','pwd','',['class' => 'form-control']);?>
    <?=Html::input('hidden','hidden','',['class' => 'form-control']);?>
    <?=Html::textInput('test','hehe',['class' => 'form-control']);?>
    <?=Html::textInput('email','admin@admin.com',['class' => 'form-control']);?>
    <?=Html::passwordInput('pwd','',['class' => 'form-control']);?>
    <?=Html::hiddenInput('hidden','',['class' => 'form-control']);?>
    <?=Html::textarea('area','',['class'=>'form-control','rows'=>'3']);?>
    <?php //【四】单选按钮：Html::radio(name值，是否选中，属性数组);?>
    <?=Html::radio('sex',true,['calss'=>'form-control']);?>
    <?php //单选按钮列表：Html:;radioList(name值，选中的值，键值对列表，属性数组); ?>
    <?=Html::radioList('height','1',['1'=>'160','2'=>'170','3'=>'180'],['class'=>'form-control']);?>
    <?php //【五】复选框：Html::checkbox(name值，是否选中，属性数组);?>
    <?=Html::checkbox('haha',true,['calss'=>'form-control']);?>
    <?php //单选按钮列表：Html:;checkboxList(name值，选中的值，键值对列表，属性数组); ?>
    <?=Html::checkboxList('xixi','1',['1'=>'160','2'=>'170','3'=>'180'],['class'=>'form-control']);?>

    <hr />
    <?php //【六】下拉列表：Html:;dropDownList(name值，选中的值，键值对列表，属性数组); ?>
    <?=Html::dropDownList('list','2',['1'=>'160','2'=>'170','3'=>'180'],['class'=>'form-control']);?>

    <hr />
    <?php //【七】控制标签Label：Html::label(显示内容，for值，属性数组); ?>
    <?=Html::label('显示的','test',['style'=>'color:#ff0000']);?>

    <hr />
    <?php //【八】上传控件：Html::fileInput(name值，默认值，属性数组); ?>
    <?=Html::fileInput('img',null,['class'=>'btn btn-default']);?>

    <hr />
    <?php //【九】按钮：; ?>
    <?=Html::button('普通按钮',['class'=>'btn btn-primary']);?>
    <?=Html::submitButton('提交按钮',['class'=>'btn btn-primary']);?>
    <?=Html::resetButton('重置按钮',['class'=>'btn btn-primary']);?>

<?=Html::endForm();?>

