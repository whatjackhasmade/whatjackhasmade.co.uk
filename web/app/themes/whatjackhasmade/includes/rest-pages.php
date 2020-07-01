<?php
/* Register function to run at rest_api_init hook */
add_action('rest_api_init', function () {
    /* Setup siteurl/wp-json/pages/v2/all */
    register_rest_route('pages/v2', '/all', array(
        'methods' => 'GET',
        'callback' => 'rest_pages',
        'args' => array(
            'slug' => array(
                'validate_callback' => function ($param, $request, $key) {
                    return is_string($param);
                },
            ),
        ),
    ));
});

function rest_pages($data)
{
    $params = $data->get_params();

    $slug = "";

    if (isset($params['slug'])):
        $slug = $params['slug'];
    endif;

    if ($slug != ""):
        $args = array(
            'pagename' => $slug,
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'post_type' => 'page',
        );
    else:
        $args = array(
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'post_type' => 'page',
        );
    endif;

    $no_content = (object) [];
    $no_content->status = "empty";

    $loop = new WP_Query($args);

    if ($loop) {
        $pageItems = array();
        while ($loop->have_posts()): $loop->the_post();
            $the_content = convert_content(get_the_content());
            if ($the_content === []) {
                array_push(
                    $the_content, $no_content
                );
            }
            array_push(
                $pageItems, array(
                    'content' => $the_content,
                    'id' => get_the_ID(),
                    'imageXS' => get_the_post_thumbnail_url(get_the_ID(), 'featured_xs'),
                    'imageSM' => get_the_post_thumbnail_url(get_the_ID(), 'featured_sm'),
                    'imageMD' => get_the_post_thumbnail_url(get_the_ID(), 'featured_md'),
                    'imageLG' => get_the_post_thumbnail_url(get_the_ID(), 'featured_lg'),
                    'imageXL' => get_the_post_thumbnail_url(get_the_ID(), 'featured_xl'),
                    'imageFull' => get_the_post_thumbnail_url(),
                    'slug' => get_post_field('post_name'),
                    'title' => html_entity_decode(get_the_title()),
                    'yoast' => array(
                        'description' => get_post_meta(get_the_ID(), '_yoast_wpseo_metadesc', true),
                        'image' => get_the_post_thumbnail_url(get_the_ID(), 'featured_lg'),
                        'slug' => get_post_field('post_name'),
                        'title' => get_post_meta(get_the_ID(), '_yoast_wpseo_title', true),
                    ),
                )
            );
        endwhile;
        wp_reset_postdata();
    } else {
        return new WP_Error(
            'no_menus',
            'Could not find any pages',
            array(
                'status' => 404,
            )
        );
    }
    return $pageItems;

}
