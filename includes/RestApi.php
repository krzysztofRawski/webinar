<?php namespace BCIM;

class RestApi
{
    public function registerCustomEndpoints()
    {
        add_action('rest_api_init', array($this, 'custom_rest_routes_register'));
    }

    public function custom_rest_routes_register()
    {
        register_rest_route('webinar', 'questions', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_questions'),
            'args' => array(
                'limitOfQuestions' => array(
                    'type' => 'integer',
                    'required' => true,
                ),
                'missingQuestions' => array(
                    'type' => 'integer',
                    'required' => true,
                ),
                'webinarId' => array(
                    'type' => 'integer',
                    'required' => true,
                ),
                'currentQuestions' => array(
                    'type' => 'string',
                    'required' => true,
                    'validate_callback' => array($this, 'validate_current_questions'),
                ),
            ),
            'permission_callback' => function () {
                return current_user_can('edit_others_posts');
            },
        ));

        register_rest_route('webinar', 'questions', array(
            'methods' => 'POST',
            'callback' => array($this, 'add_question'),
            'args' => array(
                'webinarId' => array(
                    'type' => 'integer',
                    'required' => true,
                ),
            ),
            'permission_callback' => function () {
                return current_user_can('read');
            },
        ));

        register_rest_route('webinar', 'questions', array(
            'methods' => 'DELETE',
            'callback' => array($this, 'delete_question'),
            'args' => array(
                'questionId' => array(
                    'type' => 'integer',
                    'required' => true,
                ),
            ),
            'permission_callback' => function () {
                return current_user_can('edit_others_posts');
            },
        ));

        register_rest_route('webinar', 'data', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_data'),
            'args' => array(
                'webinarId' => array(
                    'type' => 'integer',
                    'required' => true,
                ),
            ),
            'permission_callback' => function () {
                return current_user_can('read');
            },
        ));

        register_rest_route('webinar', 'user', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_user'),
            'permission_callback' => function () {
                return current_user_can('read');
            },
        ));

        register_rest_route('webinar', 'answers', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_answers'),
            'args' => array(
                'webinarId' => array(
                    'type' => 'integer',
                    'required' => true,
                ),
            ),
            'permission_callback' => function () {
                return current_user_can('read');
            },
        ));

        register_rest_route('webinar', 'answers', array(
            'methods' => 'PUT',
            'callback' => array($this, 'add_answer'),
            'args' => array(
                'questionId' => array(
                    'type' => 'integer',
                    'required' => true,
                ),
            ),
            'permission_callback' => function () {
                return current_user_can('edit_others_posts');
            },
        ));

        register_rest_route('webinar', 'statuses', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_statuses'),
            'permission_callback' => function () {
                return current_user_can('read');
            },
        ));

        register_rest_route('webinar', 'list', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_webinars_list'),
            'args' => array(
                'webinarStatus' => array(
                    'type' => 'integer',
                    'required' => true,
                ),
            ),
            'permission_callback' => function () {
                return current_user_can('read');
            },
        ));

        register_rest_route('webinar', 'stats', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_stats'),
            'args' => array(
                'webinarId' => array(
                    'type' => 'integer',
                    'required' => true,
                ),
            ),
            'permission_callback' => function () {
                return current_user_can('read');
            },
        ));
    }

    public function validate_current_questions($param, $request, $key)
    {
        $param = json_decode($param);

        $param_valid = false;

        if (sizeof($param) == 0) {
            $param_valid = true;
        } else if (sizeof($param) > 0) {
            foreach ($param as $integer) {
                if (is_numeric($integer)) {
                    $param_valid = true;
                } else if (!is_numeric($integer)) {
                    $param_valid = false;
                    break;
                }
            }
        }

        return $param_valid;
    }

    public function get_questions($request)
    {
        $limit = $request->get_param('limitOfQuestions');
        $missing_questions = $request->get_param('missingQuestions');
        $webinar_id = $request->get_param('webinarId');
        $current_questions = json_decode($request->get_param('currentQuestions'));

        if ($missing_questions > 0) {
            $limit = $missing_questions;
        }

        if (sizeof($current_questions) == 0) {
            $current_questions = 0;
        } else if (sizeof($current_questions) > 0) {
            $current_questions = implode(", ", $current_questions);
        }

        global $wpdb;

        $query_questions = "
            SELECT *
            FROM {$wpdb->prefix}bcim_webinar_questions
            WHERE question_reply = ''
            AND webinar_id = {$webinar_id}
            AND question_id NOT IN ({$current_questions})
            ORDER BY question_time ASC
            LIMIT {$limit}
        ";

        $results = $wpdb->get_results($query_questions, ARRAY_A);

        return $results;
    }

    public function add_question($request)
    {
        $webinar_id = $request->get_param('webinarId');
        $user_id = get_current_user_id();

        global $wpdb;

        $query = "
            SELECT COUNT(user_id)
            FROM {$wpdb->prefix}bcim_webinar_questions
            WHERE question_reply = ''
            AND user_id = {$user_id}
            AND webinar_id = {$webinar_id}
        ";

        $number_of_asked_questions = $wpdb->get_var($query);

        if ($number_of_asked_questions < 5) {
            $body = $request->get_body();
            $question_content = wp_kses_post(json_decode($body)->questionContent);

            date_default_timezone_set("Europe/Warsaw");
            $question_time = date('Y-m-d H:i:s', time());

            global $wpdb;

            $table = $wpdb->prefix . 'bcim_webinar_questions';

            $insert = $wpdb->insert(
                $table,
                [
                    'user_id' => $user_id,
                    'webinar_id' => $webinar_id,
                    'question_content' => $question_content,
                    'question_time' => $question_time,
                ]
            );

            if ($insert == 1) {
                $result = [
                    'status' => 200,
                    'message' => 'Pytanie zostało dodane',
                ];
            } else if ($insert == false) {
                $result = [
                    'status' => 400,
                    'message' => 'Bład zapisu danych',
                ];
            }

            return $result;
        } else if ($number_of_asked_questions >= 5) {
            $result = [
                'status' => 403,
                'message' => 'Przekroczono limit pytań',
            ];

            return $result;
        }
    }

    public function delete_question($request)
    {
        $question_id = $request->get_param('questionId');

        global $wpdb;

        $table = $wpdb->prefix . 'bcim_webinar_questions';

        $delete = $wpdb->delete(
            $table,
            [
                'question_id' => $question_id,
            ]
        );

        if ($delete == 1) {
            $result = [
                'status' => 200,
                'message' => 'Pytanie zostało usunięte',
            ];
        } else if ($delete == false) {
            $result = [
                'status' => 400,
                'message' => 'Bład zapisu danych',
            ];
        }

        return $result;
    }

    public function get_data($request)
    {
        $webinar_Id = $request->get_param('webinarId');

        $result = [
            'basicData' => [
                'limitOfQuestions' => get_field('bcim-webinar-limit-of-questions', $webinar_Id),
                'webinarTitle' => get_the_title($webinar_Id),
                'webinarImage' => get_the_post_thumbnail_url($webinar_Id, 'full'),
                'webinarDate' => get_field('bcim-webinar-date', $webinar_Id),
                'webinarTrainer' => get_field('bcim-webinar-trainer', $webinar_Id),
                'viedoEmbedCode' => get_field('bcim-webinar-video', $webinar_Id),
                'webinarStatusName' => wp_get_post_terms($webinar_Id, 'webinar_status')[0]->name,
                'webinarStatus' => wp_get_post_terms($webinar_Id, 'webinar_status')[0]->slug,
                'description' => get_the_content(null, false, $webinar_Id),
            ],
            'translations' => [
                'noNewQuestions' => __('Nie odnaleziono żadnych nowych pytań', 'bcim-webinar'),
                'addDate' => _x('Dodano', 'Data dodania pytania', 'bcim-webinar'),
                'answer' => __('Odpowiedz', 'bcim-webinar'),
                'delete' => __('Usuń', 'bcim-webinar'),
                'questions' => __('Pytania', 'bcim-webinar'),
                'answerQuestion' => __('Odpowiedz na pytanie', 'bcim-webinar'),
                'paragraph' => __('Akapit', 'bcim-webinar'),
                'header' => __('Nagłówek', 'bcim-webinar'),
                'clear' => __('Wyczyść', 'bcim-webinar'),
                'webinar' => __('Webinar', 'bcim-webinar'),
                'ask' => __('Zapytaj', 'bcim-webinar'),
                'askQuestion' => __('Zadaj pytanie', 'bcim-webinar'),
                'answerAdded' => __('Odpowiedź została dodana.', 'bcim-webinar'),
                'typeAnswer' => __('Najpierw wpisz odpowiedź', 'bcim-webinar'),
                'questionAdded' => __('Twoje pytanie zostało dodane do kolejki.', 'bcim-webinar'),
                'questionAddedError' => __('Pytanie nie zostało przesłane, spróbuj ponownie za chwilę.', 'bcim-webinar'),
                'typeQuestion' => __('Najpierw musisz wpisać pytanie', 'bcim-webinar'),
                'details' => __('Informacje', 'bcim-webinar'),
                'answers' => __('Odpowiedzi', 'bcim-webinar'),
                'noNewAnswers' => __('Nie ma jeszcze żadnych odpowiedzi.', 'bcim-webinar'),
                'myAnswer' => __('Gratulacje. To jest odpowiedź na Twoje pytanie.', 'bcim-webinar'),
                'questionLimit' => __('Przekroczono limit pytań. Zaczekaj, aż prowadzący odniesie się do Twoich dotychczas zadanych pytań.', 'bcim-webinar'),
                'description' => __('Opis', 'bcim-webinar'),
            ],
        ];

        return $result;
    }

    public function get_user($request)
    {
        $user_id = wp_get_current_user()->data->ID;
        $user_name = wp_get_current_user()->data->display_name;
        $user_role = wp_get_current_user()->roles[0];

        $result = [
            "userId" => $user_id,
            "userName" => $user_name,
        ];

        if ($user_role == 'administrator') {
            $result['userRole'] = 'trainer';
        }

        return $result;
    }

    public function get_answers($request)
    {
        $webinar_id = $request->get_param('webinarId');

        global $wpdb;

        $query = "
            SELECT *
            FROM {$wpdb->prefix}bcim_webinar_questions
            WHERE question_reply != ''
            AND webinar_id = {$webinar_id}
            ORDER BY question_reply_time DESC
        ";

        $results = $wpdb->get_results($query, ARRAY_A);

        return $results;
    }

    public function add_answer($request)
    {
        $question_id = $request->get_param('questionId');

        $body = $request->get_body();
        $question_reply = wp_kses_post(json_decode($body)->questionReply);
        date_default_timezone_set("Europe/Warsaw");
        $question_reply_time = date('Y-m-d H:i:s', time());

        global $wpdb;

        $table = $wpdb->prefix . 'bcim_webinar_questions';

        $insert = $wpdb->update(
            $table,
            [
                'question_reply' => $question_reply,
                'question_reply_time' => $question_reply_time,
            ],
            [
                'question_id' => $question_id,
            ]
        );

        if ($insert == 1) {
            $result = [
                'status' => 200,
                'message' => 'Odpowiedź została dodana',
            ];
        } else if ($insert == false) {
            $result = [
                'status' => 400,
                'message' => 'Bład zapisu danych',
            ];
        }

        return $result;
    }

    public function get_statuses($request)
    {
        $statuses = [];

        $terms = get_terms(array(
            'taxonomy' => 'webinar_status',
            'hide_empty' => true,
            'orderby' => 'term_id',
        ));

        foreach ($terms as $term) {
            $status = [
                'term_id' => $term->term_id,
                'name' => $term->name,
                'slug' => $term->slug,
                'count' => $term->count,
            ];
            $statuses[] = $status;
        }

        return $statuses;
    }

    public function get_webinars_list($request)
    {
        $term_id = $request->get_param('webinarStatus');

        $posts = get_posts(array(
            'post_type' => 'webinars',
            'tax_query' => array(
                array(
                    'taxonomy' => 'webinar_status',
                    'field' => 'term_id',
                    'terms' => $term_id),
            ))
        );

        $results = [];

        foreach ($posts as $post) {
            $thumbnail = get_the_post_thumbnail_url($post->ID, 'full');
            $link = get_post_permalink($post->ID);
            $result = [
                'postName' => $post->post_title,
                'postThumbnail' => $thumbnail,
                'postLink' => $link,
                'webinarDate' => get_field('bcim-webinar-date', $post->ID),
                'webinarTrainer' => get_field('bcim-webinar-trainer', $post->ID),
            ];
            $results[] = $result;
        }

        return $results;
    }

    public function get_stats($request)
    {
        $webinar_id = $request->get_param('webinarId');
        global $wpdb;

        $query_total = "
            SELECT COUNT(question_id)
            FROM {$wpdb->prefix}bcim_webinar_questions
            WHERE question_reply = ''
            AND webinar_id = {$webinar_id}
        ";

        $result = $wpdb->get_var($query_total);

        return $result;
    }
}
