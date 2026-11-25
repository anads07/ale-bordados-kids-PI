<?php

return [
    // O painel exige is_admin e o e-mail definido no ambiente.
    'email' => env('ADMIN_EMAIL'),
    'password' => env('ADMIN_PASSWORD'),
];
