<?php
/**
 * Class HomeController
 *
 * @date      31/8/2016
 * @author    KwangChin <kwangchin@gmail.com>
 * @copyright Copyright (c) Kwang Chin
 */

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

/**
 * Class HomeController
 *
 * Frontend home controller.
 */
class HomeController extends Controller
{
    /**
     * Frontend home controller
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        return view('home/index');
    }

    public function getUsers()
    {
        return User::select(['uid', 'email', 'name', 'created_at', 'updated_at'])->get();
    }
}
