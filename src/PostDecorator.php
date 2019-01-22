<?php

namespace CompleteOpenGraph;

class PostDecorator
{

    /**
     * WP_Post object.
     *
     * @var obj
     */
    public $post;

    /**
     * Constructs decorator based on WP_Post object.
     *
     * @param WP_Post|null $post
     */
    public function __construct($post = null)
    {
        $this->post = is_null($post) ? $GLOBALS['post'] : $post;
    }

    /**
     * Returns false if property doesn't exist.
     *
     * @param  string $key
     * @return mixed
     */
    public function __get($key)
    {
        if (! isset($this->post->$key)) {
            return null;
        }

        return $this->post->$key;
    }
}
