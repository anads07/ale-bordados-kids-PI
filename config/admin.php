<?php

return [
    /* Credenciais administrativas:
     * O painel exige is_admin e o e-mail definido no ambiente.
     * O seeder também exige uma senha configurada.
     */
    'email' => env('ADMIN_EMAIL'),
    'password' => env('ADMIN_PASSWORD'),
];
