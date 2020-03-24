<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;

class FirebasesmsController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function invisiblecaptcha()
    {
        return view('invisiblecaptcha');
    }
}