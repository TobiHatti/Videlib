<?php

function Img($path, $initialWord = null){

    if(file_exists($_SERVER['DOCUMENT_ROOT'].$path) && !is_null($path)) return $path;
    if($initialWord != null){
        $letter = strtolower(mb_substr($initialWord, 0, 1));
        if(ctype_alpha($letter)) return "/files/placeholders/".$letter.".webp";
    }
    return "/files/placeholders/question.webp";
}

function GUID()
{
    if (function_exists('com_create_guid') === true) return trim(com_create_guid(), '{}');
    return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
}

function RandomColor() {
    return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
}