<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class WelcomeController extends Controller
{
    /**
     * Display the welcome page with README.md content
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('welcome');
    }

    /**
     * Serve the README.md file
     *
     * @return \Illuminate\Http\Response
     */
    public function readme()
    {
        $readmePath = base_path('README.md');

        if (!File::exists($readmePath)) {
            abort(404, 'README.md file not found');
        }

        return response(File::get($readmePath), 200, [
            'Content-Type' => 'text/markdown; charset=utf-8',
            'Cache-Control' => 'public, max-age=3600'
        ]);
    }
}
