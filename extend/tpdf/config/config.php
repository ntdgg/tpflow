<?php
/**
*+------------------
* 配置文件夹
*+------------------ 
*/
namespace tpdf;

function tab($step = 1, $string = ' ', $size = 4)
{
    return str_repeat($string, $size * $step);
}
