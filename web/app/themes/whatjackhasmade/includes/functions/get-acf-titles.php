<?php
function getACFTitles($str, $startDelimiter, $endDelimiter)
{
    $contents = array();
    $startDelimiterLength = strlen($startDelimiter);
    $endDelimiterLength = strlen($endDelimiter);
    $startFrom = $contentStart = $contentEnd = 0;
    while (false !== ($contentStart = strpos($str, $startDelimiter, $startFrom))) {
        $contentStart += $startDelimiterLength;
        $contentEnd = strpos($str, $endDelimiter, $contentStart);
        if (false === $contentEnd) {
            break;
        }
        $fieldTitle = 'field_' . substr($str, $contentStart, $contentEnd - $contentStart);
        $field = get_field_object($fieldTitle);
        $contents[$fieldTitle] = $field['name'];
        $startFrom = $contentEnd + $endDelimiterLength;
    }

    return $contents;
}
