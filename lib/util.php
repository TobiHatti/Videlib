<?php

function Img($path, $initialWord = null){

    if(file_exists($_SERVER['DOCUMENT_ROOT'].$path) && !is_null($path)) return $path;
    if($initialWord != null){
        $letter = strtolower(mb_substr($initialWord, 0, 1));
        if(ctype_alpha($letter)) return "/files/placeholders/".$letter.".webp";
    }
    return "/files/placeholders/question.webp";
}

class AgingData{
    public int $age;
    public int $daysPerYear;
}

function Age($birthday, array $agingSpeeds) {
    
    
    foreach($agingSpeeds as $as){

    }
}