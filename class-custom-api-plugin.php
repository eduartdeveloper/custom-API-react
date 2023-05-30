<?php
class Custom_API_Plugin {
    public function init() {
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    public function register_routes() {
        // /wp-json/react/v1/posts
        register_rest_route('react/v1', '/posts', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_posts'),
        ));

        // wp-json/react/v1/posts/{id}
        register_rest_route('react/v1', '/posts/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_post'),
            'args' => array(
                'id' => array(
                    'validate_callback' => 'rest_validate_request_arg',
                ),
            ),
        ));
        
        
        register_rest_route('react/v1', '/posts', array(
            'methods' => 'POST',
            'callback' => array($this, 'create_post'),
            'permission_callback' => array($this, 'authenticate_request'),
        ));

        register_rest_route('react/v1', '/posts/(?P<id>\d+)', array(
            'methods' => 'PUT',
            'callback' => array($this, 'update_post'),
            'permission_callback' => array($this, 'authenticate_request'),
            'args' => array(
                'id' => array(
                    'validate_callback' => 'rest_validate_request_arg',
                ),
            ),
        ));

        register_rest_route('react/v1', '/posts/(?P<id>\d+)', array(
            'methods' => 'DELETE',
            'callback' => array($this, 'delete_post'),
            'permission_callback' => array($this, 'authenticate_request'),
            'args' => array(
                'id' => array(
                    'validate_callback' => 'rest_validate_request_arg',
                ),
            ),
        ));
    }

    public function get_posts() {
        $args = array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => -1,
        );
    
        $posts_query = new WP_Query($args);
        $posts = $posts_query->get_posts();
    
        $formatted_posts = array();
    
        foreach ($posts as $post) {
            $post_id = $post->ID;
            $post_title = $post->post_title;
            $post_slug = $post->post_name;
            $post_link = get_permalink($post_id);
            $post_featured_image = get_the_post_thumbnail_url($post_id, 'full');
            $post_content = $post->post_content;
    
            // Retrieve categories
            $post_categories = wp_get_post_categories($post_id);
            $formatted_categories = array();
    
            foreach ($post_categories as $category_id) {
                $category = get_category($category_id);
    
                $formatted_categories[] = array(
                    'id' => $category->cat_ID,
                    'title' => $category->name,
                    'description' => $category->description,
                );
            }
    
            // Retrieve meta fields
            $post_meta_fields = get_post_meta($post_id);
            $formatted_meta_fields = array();
    
            foreach ($post_meta_fields as $meta_key => $meta_values) {
                foreach ($meta_values as $meta_value) {
                    $formatted_meta_fields[] = array(
                        'key' => $meta_key,
                        'value' => $meta_value,
                    );
                }
            }
    
            $formatted_posts[] = array(
                'id' => $post_id,
                'slug' => $post_slug,
                'link' => $post_link,
                'title' => $post_title,
                'featured_image' => $post_featured_image,
                'categories' => $formatted_categories,
                'content' => $post_content,
                'meta_fields' => $formatted_meta_fields,
            );
        }
    
        return $formatted_posts;
    }
    

    public function get_post($request) {
        $post_id = absint($request['id']);
    
        // Get the post object
        $post = get_post($post_id);
    
        // Check if the post exists
        if (!$post) {
            return new WP_Error('post_not_found', __('Post not found'), array('status' => 404));
        }
    
        // Format the post data
        $formatted_post = array(
            'id' => $post->ID,
            'slug' => $post->post_name,
            'link' => get_permalink($post->ID),
            'title' => $post->post_title,
            'featured_image' => get_the_post_thumbnail_url($post->ID),
            'categories' => array(),
            'content' => $post->post_content,
            'meta_fields' => array()
        );
    
        // Get the categories of the post
        $categories = get_the_category($post->ID);
        foreach ($categories as $category) {
            $formatted_category = array(
                'id' => $category->cat_ID,
                'title' => $category->name,
                'description' => $category->description
            );
            $formatted_post['categories'][] = $formatted_category;
        }
    
        // Get the meta fields of the post
        $meta_fields = get_post_meta($post->ID);
        foreach ($meta_fields as $key => $value) {
            $formatted_meta_field = array(
                'key' => $key,
                'value' => $value[0]
            );
            $formatted_post['meta_fields'][] = $formatted_meta_field;
        }
    
        // Return the formatted post as the API response
        return $formatted_post;
    }
    
    

    public function create_post($request) {
        // Authenticate the request
        if (!$this->authenticate_request()) {
            return new WP_Error('unauthorized', 'Unauthorized request.', array('status' => 401));
        }
    
        // Get the request parameters
        $params = $request->get_params();
    
        // Validate and process the parameters to create a new post
        // For example, you can use the 'title', 'content', and 'meta_fields' parameters
        // to create a new post using the wp_insert_post() function
    
        // Create a new post
        $new_post = array(
            'post_title' => $params['title'],
            'post_content' => $params['content'],
            'post_status' => 'publish',
            'post_type' => 'post'
        );
    
        $post_id = wp_insert_post($new_post);
    
        // Set post meta fields
        if (!empty($params['meta_fields'])) {
            foreach ($params['meta_fields'] as $meta_field) {
                update_post_meta($post_id, $meta_field['key'], $meta_field['value']);
            }
        }
    
        // Retrieve the newly created post
        $post = get_post($post_id);
    
        // Format the response
        $response = array(
            'id' => $post->ID,
            'slug' => $post->post_name,
            'link' => get_permalink($post->ID),
            'title' => $post->post_title,
            'featured_image' => get_the_post_thumbnail_url($post->ID),
            'categories' => array(), // Add category information if needed
            'content' => $post->post_content,
            'meta_fields' => array() // Add meta field information if needed
        );
    
        // Return the newly created post as the API response
        return $response;
    }
    

    public function update_post($request) {
        // Authenticate the request
        if (!$this->authenticate_request()) {
            return new WP_Error('unauthorized', 'Unauthorized request.', array('status' => 401));
        }
    
        // Get the post ID from the request parameters
        $post_id = $request->get_param('id');
    
        // Retrieve the post
        $post = get_post($post_id);
    
        // Check if the post exists
        if (!$post) {
            return new WP_Error('not_found', 'Post not found.', array('status' => 404));
        }
    
        // Get the request parameters
        $params = $request->get_params();
    
        // Update the post fields
        $updated_post = array(
            'ID' => $post_id,
            'post_title' => $params['title'],
            'post_content' => $params['content'],
        );
    
        // Update the post
        wp_update_post($updated_post);
    
        // Update post meta fields
        if (!empty($params['meta_fields'])) {
            foreach ($params['meta_fields'] as $meta_field) {
                update_post_meta($post_id, $meta_field['key'], $meta_field['value']);
            }
        }
    
        // Retrieve the updated post
        $updated_post = get_post($post_id);
    
        // Format the response
        $response = array(
            'id' => $updated_post->ID,
            'slug' => $updated_post->post_name,
            'link' => get_permalink($updated_post->ID),
            'title' => $updated_post->post_title,
            'featured_image' => get_the_post_thumbnail_url($updated_post->ID),
            'categories' => array(), // Add category information if needed
            'content' => $updated_post->post_content,
            'meta_fields' => array() // Add meta field information if needed
        );
    
        // Return the updated post as the API response
        return $response;
    }
    

    public function delete_post($request) {
        // Authenticate the request
        if (!$this->authenticate_request()) {
            return new WP_Error('unauthorized', 'Unauthorized request.', array('status' => 401));
        }
    
        // Get the post ID from the request parameters
        $post_id = $request->get_param('id');
    
        // Retrieve the post
        $post = get_post($post_id);
    
        // Check if the post exists
        if (!$post) {
            return new WP_Error('not_found', 'Post not found.', array('status' => 404));
        }
    
        // Delete the post
        wp_delete_post($post_id, true);
    
        // Return a success message as the API response
        return array('message' => 'Post deleted successfully.');
    }
    

    public function authenticate_request() {
        // Get the request headers
        $headers = getallheaders();
    
        // Check if the 'Authorization' header is present
        if (isset($headers['Authorization'])) {
            // Get the token value from the header
            $token = $headers['Authorization'];
    
            // Compare the token with the expected value
            if ($token === 'GGDGFDDRTJ5798123FYFJASDFGMNBVC') {
                return true; // Authentication successful
            }
        }
    
        return false; // Authentication failed
    }
    
}
