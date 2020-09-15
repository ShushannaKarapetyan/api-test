<?php

/**
 * @param array $array
 * @return array
 */
function data_keys(array $array): array
{
    if (empty($array)) {
        return [];
    }

    return array_keys(array_values($array)[0]);
}



