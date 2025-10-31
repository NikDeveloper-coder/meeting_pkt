<?php

namespace App\Controllers;

class Pages extends BaseController
{
    public function about()
    {
        // boleh pass data untuk navbar active, dsb.
        return view('about', [
            'title'   => 'About Meeting Room',
            'section' => 'about'
        ]);
    }
}
