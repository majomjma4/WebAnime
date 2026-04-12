<?php
namespace Controllers;

class SiteController extends Controller
{
    public function home()
    {
        app_start_session();
        session_write_close();
        $this->render('pages/index');
    }

    public function character()
    {
        $this->render('pages/character');
    }

    public function featured()
    {
        app_start_session();
        session_write_close();
        $this->render('pages/destacados');
    }

    public function detail()
    {
        $this->ensureSessionStarted();

        $detailRef = trim((string) ($_GET['_detail_ref'] ?? ''));
        $legacyId = trim((string) ($_GET['mal_id'] ?? $_GET['id'] ?? ''));
        $legacyTitle = trim((string) ($_GET['q'] ?? ''));

        if ($detailRef === '') {
            $detailRef = app_detail_ref_from_input($legacyId, $legacyTitle);
        }

        if ($detailRef !== '' && ctype_digit($detailRef)) {
            $animeModel = new \Models\Anime();
            $resolvedAnime = $animeModel->getById((int) $detailRef);
            if (!$resolvedAnime || empty($resolvedAnime['mal_id'])) {
                $this->renderNotFound('detail/' . $detailRef);
                return;
            }
        }

        $isLoggedIn = isset($_SESSION['user_id']);
        $sessionRole = $_SESSION['role'] ?? 'Invitado';
        $sessionPremium = !empty($_SESSION['premium']) || $sessionRole === 'Admin';
        $detailQuery = $detailRef !== '' && !ctype_digit($detailRef) ? str_replace('-', ' ', $detailRef) : '';

        // Release session lock before view if feasible
        session_write_close();

        $this->render('pages/detail', [
            'isLoggedIn' => $isLoggedIn,
            'sessionRole' => $sessionRole,
            'sessionPremium' => $sessionPremium,
            'detailRef' => $detailRef,
            'detailQuery' => $detailQuery,
        ]);
    }

    public function login()
    {
        $this->render('pages/ingresar');
    }

    public function payment()
    {
        $this->render('pages/pago');
    }

    public function movies()
    {
        $animeModel = new \Models\Anime();
        $dbGenres = $animeModel->getFilteredGenres();
        $animes = $animeModel->getMovies();

        app_start_session();
        session_write_close();

        $this->render('pages/peliculas', [
            'dbGenres' => $dbGenres,
            'animes' => $animes,
        ]);
    }

    public function ranking()
    {
        app_start_session();
        session_write_close();
        $this->render('pages/ranking');
    }

    public function register()
    {
        $this->render('pages/registro');
    }

    public function series()
    {
        $animeModel = new \Models\Anime();
        $dbGenres = $animeModel->getFilteredGenres();
        $animes = $animeModel->getSeries();

        app_start_session();
        session_write_close();

        $this->render('pages/series', [
            'dbGenres' => $dbGenres,
            'animes' => $animes,
        ]);
    }
}
