<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * Controller da página institucional Sobre Nós.
 */
class SobreController extends Controller
{
    /**
     * Renderiza a página institucional.
     */
    public function index(): View
    {
        return view('user.sobre');
    }
}
