<?php

return [
    ['/', 'HomeController@index'],
    ['/posts', 'PostController@index'],
    ['/posts/{post}', 'PostController@show']
];
