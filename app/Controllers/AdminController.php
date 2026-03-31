<?php
namespace Controllers;

class AdminController extends Controller
{
    public function dashboard(): void
    {
        $this->render('pages/admin');
    }

    public function addAnime(): void
    {
        $this->render('pages/añadir');
    }

    public function manageCatalog(): void
    {
        $this->render('pages/gestion');
    }

    public function manageUsers(): void
    {
        $this->render('pages/gesUs');
    }

    public function manageComments(): void
    {
        $this->render('pages/gesCom');
    }
}
