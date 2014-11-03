<?php

/**
 * Function to pretend xss attacks
 *
 * @param $array
 * @return array
 */
function array_htmlspecialchars($array)
{
    $result = array();
    foreach ($array as $key => $value) {

        $key = htmlspecialchars($key);

        if (is_array($value))
        {
            $result[$key] = array_htmlspecialchars($value);
        }
        else
        {
            $result[$key] = htmlspecialchars($value);
        }
    }

    return $result;
}
