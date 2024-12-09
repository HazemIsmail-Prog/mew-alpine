<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class DocumentGeneratorController extends Controller
{
    public function index()
    {
        return view('document-generator.index');
    }

    public function generateDocument()
    {

        return view('document-generator.pdf');

    }
}
