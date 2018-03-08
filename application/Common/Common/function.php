<?php

function ids_parse($str,$dot_tmp=',')
{
    if(!$str) return '';
    if(is_array($str))
    {
        $idarr = $str;
    }else
    {
        $idarr = explode(',',$str);
    }
    $idarr = array_unique($idarr);
    foreach($idarr as $id)
    {
        $id = intval($id);
        if($id>0)
        {
            $idstr.=$dot.$id;
            $dot = $dot_tmp;
        }
    }
    if(!$idstr) $idstr=0;
    return $idstr;
}
