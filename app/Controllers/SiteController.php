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

    public function detail($detailRef = null)
    {
        $this->ensureSessionStarted();

        if ($detailRef === null) {
            $detailRef = isset($_GET['_detail_ref']) ? trim((string)$_GET['_detail_ref']) : '';
        }
        $legacyId = isset($_GET['mal_id']) ? trim((string)$_GET['mal_id']) : (isset($_GET['id']) ? trim((string)$_GET['id']) : '');
        $legacyTitle = isset($_GET['q']) ? trim((string)$_GET['q']) : '';

        if ($detailRef === '') {
            $detailRef = app_detail_ref_from_input($legacyId, $legacyTitle);
        }

        // Validación estricta: Si la referencia es inválida o sospechosa (ej: 59978abc), damos 404.
        if ($detailRef === '' || !app_is_valid_detail_ref($detailRef)) {
            $this->renderNotFound($detailRef);
            return;
        }

        if (ctype_digit($detailRef)) {
            $animeModel = new \Models\Anime();
            $resolvedAnime = $animeModel->getById((int) $detailRef);
            // Seguimos permitiendo el renderizado si es un ID válido para el fallback de Jikan.
        }

        $isLoggedIn = isset($_SESSION['user_id']);
        $sessionRole = isset($_SESSION['role']) ? $_SESSION['role'] : 'Invitado';
        $sessionPremium = !empty($_SESSION['premium']) || $sessionRole === 'Admin';
        
        $detailQuery = '';
        if ($detailRef !== '' && !ctype_digit($detailRef)) {
            $detailQuery = str_replace('-', ' ', $detailRef);
        }

        // Release session lock before view if feasible
        session_write_close();

        $this->render('pages/detail', array(
            'isLoggedIn' => $isLoggedIn,
            'sessionRole' => $sessionRole,
            'sessionPremium' => $sessionPremium,
            'detailRef' => $detailRef,
            'detailQuery' => $detailQuery,
        ));
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

        $this->render('pages/peliculas', array(
            'dbGenres' => $dbGenres,
            'animes' => $animes,
        ));
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

        $this->render('pages/series', array(
            'dbGenres' => $dbGenres,
            'animes' => $animes,
        ));
    }
}
