<?php

function getACFImages($content)
{
    if (!empty($content)):

        /* Set up an empty array for the links. */
        $links = array();
        $imageIDs = array();
        $videoIDs = array();

        /* Get the intermediate image sizes and add the full size to the array. */
        $sizes = get_intermediate_image_sizes();
        $sizes[] = 'full';

        array_walk_recursive($content, function ($value, $key) use (&$imageIDs, &$videoIDs) {
            $number = intval($value);
            if (intval($number) > 0):
                if (wp_get_attachment_image_src($number, 'full')):
                    $imageIDs[] = $number;
                endif;
                if (!wp_get_attachment_image_src($number, 'full') && wp_get_attachment_url($number)):
                    $videoIDs[] = $number;
                endif;
            endif;
        });

        $videoIDs = array_unique($videoIDs);

        foreach ($imageIDs as $media) {
            /* Loop through each of the image sizes. */
            foreach ($sizes as $size) {

                /* Get the image source, width, height, and whether it's intermediate. */
                $image = wp_get_attachment_image_src($media, $size);

                /* Add the link to the array if there's an image and if $is_intermediate (4th array value) is true or full size. */
                if (!empty($image) && (true == $image[3] || 'full' == $size)) {
                    $links[$media][$size] = $image[0];
                }

            }
        }

        array_walk_recursive($content, function (&$value, $key) use ($links, $videoIDs) {
            if (!is_array($value) && $key === 'media' || $key === 'logo') {
                foreach ($links as $imageKey => $imageValue) {
                    if ($value == $imageKey):
                        $value = $imageValue;
                    endif;
                }
                foreach ($videoIDs as $videoKey => $videoValue) {
                    if ($value == $videoValue) {
                        $videoFull = [];
                        $videoFull["full"] = wp_get_attachment_url($videoValue);
                        $value = $videoFull;
                    }
                }
            }
        });

        return $content;

    endif;

    return $content;
}
