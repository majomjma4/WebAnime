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
        $this->ensureSessionStarted();
        $isLoggedIn = isset($_SESSION['user_id']);
        $sessionRole = $_SESSION['role'] ?? 'Invitado';
        $sessionPremium = !empty($_SESSION['premium']) || $sessionRole === 'Admin';

        $this->render('pages/detail', [
            'isLoggedIn' => $isLoggedIn,
            'sessionRole' => $sessionRole,
            'sessionPremium' => $sessionPremium,
        ]);
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
        $animeModel = new \Models\Anime();
        $dbGenres = $animeModel->getFilteredGenres();
        $animes = $animeModel->getMovies();

        $this->render('pages/peliculas', [
            'dbGenres' => $dbGenres,
            'animes' => $animes,
        ]);
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
        $animeModel = new \Models\Anime();
        $dbGenres = $animeModel->getFilteredGenres();
        $animes = $animeModel->getSeries();

        $this->render('pages/series', [
            'dbGenres' => $dbGenres,
            'animes' => $animes,
        ]);
    }
}
