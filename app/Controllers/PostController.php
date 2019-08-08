<?php

namespace App\Controllers;

class PostController
{
    public function index()
    {
        echo 'Post Works!';
    }

    public function show($post)
    {
        echo "Post id {$post}.";
    }
}
