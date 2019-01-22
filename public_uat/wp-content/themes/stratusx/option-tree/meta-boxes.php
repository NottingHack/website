<?php

add_action( 'admin_init', '_themo_general_meta_boxes' );

//======================================================================
// General Meta Boxes
//======================================================================

function _themo_general_meta_boxes()
{


//-----------------------------------------------------
// Blog Category Filter
//-----------------------------------------------------
    $themo_blog_category_meta_box = array(
        'id' => 'themo_blog_category_meta_box',
        'title' => __('Category Filter', 'stratus'),
        'pages' => array('page'),
        'context' => 'normal',
        'priority' => 'default',
        'fields' => array(
            // START PAGE LAYOUT META BOX
            array(
                'id' => 'themo_category_checkbox',
                'std' => '',
                'type' => 'category-checkbox',
            ),
            // END PAGE LAYOUT META BOX
        )
    );
    ot_register_meta_box($themo_blog_category_meta_box);

//-----------------------------------------------------
// Page Layout, Sidebar, Content Editor Sort Order
//-----------------------------------------------------
    $themo_page_layout_meta_box = array(
        'id' => 'themo_page_layout_meta_box',
        'title' => __('Page Layout', 'stratus'),
        'pages' => array('page','themo_tour','themo_portfolio'),
        'context' => 'side',
        'priority' => 'default',
        'fields' => array(
            // START PAGE LAYOUT META BOX
            array(
                'id' => 'themo_transparent_header',
                'label' => 'Transparent Header',
                'std' => 'off',
                'type' => 'on-off',
            ),
            array(
                'id' => 'themo_hide_title',
                'label' => 'Hide Page Title',
                'std' => 'off',
                'type' => 'on-off',
            ),
            array(
                'id' => 'themo_page_layout',
                'label' => 'Sidebar',
                'std' => 'full',
                'type' => 'radio',
                'section' => 'themo_home_page_meta',
                'choices' => array(
                    array(
                        'value' => 'left',
                        'label' => __('Left Sidebar', 'stratus'),

                    ),
                    array(
                        'value' => 'right',
                        'label' => __('Right Sidebar', 'stratus'),

                    ),
                    array(
                        'value' => 'full',
                        'label' => __('No Sidebar', 'stratus'),

                    )
                )
            ),

            // END PAGE LAYOUT META BOX
        )
    );
    ot_register_meta_box($themo_page_layout_meta_box);

    //-----------------------------------------------------
    // Page Layout, Sidebar, Content Editor Sort Order
    //-----------------------------------------------------
    $themo_holes_meta_box = array(
        'id' => 'themo_holes_meta_box',
        'title' => __('Hole Page Options', 'stratus'),
        'pages' => array('themo_portfolio'),
        'context' => 'normal',
        'priority' => 'default',
        'fields' => array(
            // START PAGE LAYOUT META BOX

            array(
                'id' => '_holes_number',
                'label' => 'Hole #',
                'type' => 'text',
            ),
            array(
                'id' => '_holes_par',
                'label' => 'Par',
                'type' => 'text',
            ),
            array(
                'id' => '_holes_yards',
                'label' => 'Yards',
                'type' => 'text',
            ),
            array(
                'id' => '_holes_handicap',
                'label' => 'Handicap',
                'type' => 'text',
            ),
            array(
                'id'          => "_holes_image",
                'label'       => __( 'Thumbnail Image', 'stratus'),
                'type'        => 'upload',
                'class'       => 'ot-upload-attachment-id',
                'desc' => 'Sets the thumbnail image for Image post format only.',
            ),
            // END PAGE LAYOUT META BOX
        )
    );
    //ot_register_meta_box($themo_holes_meta_box);

}


