<?php
namespace Controllers;

class SiteController extends Controller
{
    public function home(): void
    {
        $this->render('pages/index');
    }

    public function character(): void
    {
        $this->render('pages/character');
    }

    public function featured(): void
    {
        $this->render('pages/destacados');
    }

    public function detail(): void
    {
        $this->render('pages/detail');
    }

    public function login(): void
    {
        $this->render('pages/ingresar');
    }

    public function payment(): void
    {
        $this->render('pages/pago');
    }

    public function movies(): void
    {
        $this->render('pages/peliculas');
    }

    public function ranking(): void
    {
        $this->render('pages/ranking');
    }

    public function register(): void
    {
        $this->render('pages/registro');
    }

    public function series(): void
    {
        $this->render('pages/series');
    }
}
