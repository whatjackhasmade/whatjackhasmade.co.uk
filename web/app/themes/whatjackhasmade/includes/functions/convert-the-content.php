<?php
/* THIS BEAUTY CONVERTS GUTENBERG BLOCKS TO JSON FOR THE API */
function convert_content($content)
{
    $content = str_replace('https://wjhm.noface.app/', '/', $content);
    $content = str_replace('http://local-whatjackhasmade.co.uk/', '/', $content);
    $ACFTitles = getACFTitles($content, 'field_', '"');

    foreach ($ACFTitles as $key => $value) {
        $content = str_replace($key, $value, $content);
    }

    $content = parse_blocks($content);

    $content = getACFImages($content);

    /* Get the intermediate image sizes and add the full size to the array. */
    $sizes = get_intermediate_image_sizes();
    $sizes[] = 'full';

    foreach ($content as &$block) {
        if ($block['attrs']) {
            if ($block['blockName'] === "acf/testimonials"):
                $testimonials = $block['attrs']['data'];
                $testimonialObjects = [];

                if (is_array($testimonials) || is_object($testimonials)):
                    $count = $testimonials['testimonials'];
                    foreach ($testimonials as $key => $value) {
                        for ($i = 0; $i < $count; $i++) {
                            if (strpos($key, '_') !== 0):
                                $key = ltrim($key, 'testimonial_');
                            endif;

                            if (strpos($key, $i . '_') === 0):
                                $key = ltrim($key, $i . '_');

                                if ($key === "logo") {
                                    $value = wp_get_attachment_image_src(intval($value), 'full')[0];
                                }

                                if ($key === "media") {
                                    $imageArray = array();

                                    /* Loop through each of the image sizes. */
                                    foreach ($sizes as $size) {
                                        /* Get the image source, width, height, and whether it's intermediate. */
                                        $image = wp_get_attachment_image_src(intval($value), $size);
                                        /* Add the link to the array if there's an image and if $is_intermediate (4th array value) is true or full size. */
                                        if (!empty($image) && (true == $image[3] || 'full' == $size)) {
                                            $imageArray[$size] = $image[0];
                                        }
                                    }

                                    $value = $imageArray;
                                }

                                $testimonialObjects[$i][$key] = $value;
                            endif;
                        }
                    }

                    $testimonialObject = new stdClass();
                    $testimonialObject->testimonials = $testimonialObjects;

                    $block['attrs']['data'] = $testimonialObject;
                endif;
            endif;
        }
    }

    unset($block);

    return $content;
}
