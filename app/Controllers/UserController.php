<?php
namespace Controllers;

class UserController extends Controller
{
    public function profile(): void
    {
        $this->render('pages/user');
    }
}
