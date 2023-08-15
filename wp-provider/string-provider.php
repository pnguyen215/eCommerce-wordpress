<?php

function split_by($string, $delimiter): array|null
{
    if (empty($string) || is_null($string)) {
        return array(
            "" => ""
        );
    }
    $token = strtok($string, $delimiter);
    $array = [];
    while ($token !== false) {
        $array[] = $token;
        $token = strtok($delimiter);
    }
    return $array;
}


?>