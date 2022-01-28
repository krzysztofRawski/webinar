<?php namespace BCIM;

class Webinars
{
    public function registerCptTemplate()
    {
        add_filter('template_include', array($this, 'singleWebinarTemplate'));
    }

    public function singleWebinarTemplate($template)
    {
        $post_types = ['webinars'];

        if (is_singular($post_types)) {
            $template = WEBINAR_PLUGIN_PATH . 'templates/single-webinars.php';
        }

        return $template;
    }

    public function registerShortcodes()
    {
        add_shortcode('webinar-list', array($this, 'webinarList'));
    }

    public function webinarList()
    {
        $home_url = home_url();
        $security = wp_create_nonce('wp_rest');
        $webinar_id = get_queried_object_id();
        // TODO: dodać docelowe wersjonowanie
        $css_url = WEBINAR_PLUGIN_URL . 'components/webinar-list/public/build/bundle.css?version=' . time();
        $js_url = WEBINAR_PLUGIN_URL . 'components/webinar-list/public/build/bundle.js?version=' . time();

        $html = "
        <link rel='stylesheet' href='https://fonts.googleapis.com/icon?family=Material+Icons'>
        <link rel='stylesheet' href='{$css_url}'>
        <script>
            const webinarData = {
                homeUrl: '{$home_url}',
                security: '{$security}',
                webinarId: {$webinar_id}
            }
        </script>
        <div class='bcim-webinar-list'><div>
        <script src='{$js_url}'></script>

            <div class='bcim-webinar-list'></div>
        ";
        return $html;
    }

    public function register_cpt()
    {
        add_action('init', array($this, 'webinar_create_post_type'));
        add_action('init', array($this, 'webianr_cpt_taxonomies'));
        add_filter('manage_edit-webinars_columns', array($this, 'my_columns'));
        add_filter('manage_edit-webinars_sortable_columns', array($this, 'sort_me'));
        add_action('manage_posts_custom_column', array($this, 'populate_columns'));
    }

    public function webinar_create_post_type()
    {
        $args = [
            'labels' => [
                'name' => 'Webinaria',
                'singular_name' => 'Webinarium',
            ],
            'public' => true,
            'exclude_from_search' => true,
            'menu_icon' => 'dashicons-format-video',
            'has_archive' => false,
            'show_in_rest' => true,
            'taxonomies' => ['status'],
            'capability_type' => 'post',
            'capabilities' => [
                'create_posts' => true,
            ],
            'map_meta_cap' => true,
            'supports' => ['title', 'editor', 'thumbnail'],
        ];
        register_post_type('webinars', $args);
    }

    public function webianr_cpt_taxonomies()
    {
        $labels = array(
            'name' => 'Statusy',
            'singular_name' => 'Status',
            'menu_name' => 'Statusy',
        );

        $args = array(
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'show_tagcloud' => false,
            'show_in_rest' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'webinar_status', 'with_front' => false),
        );

        register_taxonomy('webinar_status', array('webinars'), $args);
    }

    public function my_columns($columns)
    {
        $columns['webinars_status'] = 'Status';
        $columns['webinars_trainer'] = 'Status';
        return $columns;
    }

    public function sort_me($columns)
    {
        $columns['webinars_status'] = 'webinars_status';
        $columns['webinars_trainer'] = 'webinars_trainer';
        return $columns;
    }

    public function populate_columns($column)
    {
        $webinar_id = get_the_ID();

        if ('webinars_status' == $column) {
            $status = get_the_terms($webinar_id, 'webinar_status')[0]->name;
            echo $status;
        } else if ('webinars_trainer' == $column) {
            $trainer = get_field('bcim-webinar-trainer', $webinar_id);
            echo $trainer;
        }
    }

    public function register_custom_fields()
    {
        add_action('acf/init', array($this, 'define_custom_fields'));
    }

    public function define_custom_fields()
    {
        acf_add_local_field_group(array(
            'key' => 'group_5fca09e0ac551',
            'title' => 'Ustawienia',
            'fields' => array(
                array(
                    'key' => 'field_5fca09f4f52da',
                    'label' => 'Limit pytań',
                    'name' => 'bcim-webinar-limit-of-questions',
                    'type' => 'number',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'min' => '',
                    'max' => '',
                    'step' => '',
                ),
                array(
                    'key' => 'field_5fca0a40f229a',
                    'label' => 'Data rozpoczęcia',
                    'name' => 'bcim-webinar-date',
                    'type' => 'date_time_picker',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'display_format' => 'F j Y H:i:s',
                    'return_format' => 'Y-m-d H:i:s',
                    'first_day' => 1,
                ),
                array(
                    'key' => 'field_5fca0a60c1cd6',
                    'label' => 'Prowadzący',
                    'name' => 'bcim-webinar-trainer',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'maxlength' => '',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'webinars',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'side',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
        ));

        acf_add_local_field_group(array(
            'key' => 'group_5fca0aafb9229',
            'title' => 'Webinar',
            'fields' => array(
                array(
                    'key' => 'field_5fca0abc6ef6b',
                    'label' => 'Video Embed Code - YouTube',
                    'name' => 'bcim-webinar-video',
                    'type' => 'textarea',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '',
                    'placeholder' => '',
                    'maxlength' => '',
                    'rows' => '',
                    'new_lines' => '',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'webinars',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
        ));
    }
}
