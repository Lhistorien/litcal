<?php namespace App\Libraries;

// Fait le lien entre la méthode stockée dans Components/display_book et les Views
class DisplayBook
{
    public function displayBook($param)
    {
        return view('Components/display_book', $param);
    }
}