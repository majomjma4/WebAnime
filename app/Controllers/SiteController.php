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

        $detailRef = trim((string) ($_GET['_detail_ref'] ?? ''));
        $legacyId = trim((string) ($_GET['mal_id'] ?? $_GET['id'] ?? ''));
        $legacyTitle = trim((string) ($_GET['q'] ?? ''));

        if ($detailRef === '') {
            if ($legacyId !== '' && ctype_digit($legacyId)) {
                $detailRef = $legacyId;
            } elseif ($legacyTitle !== '') {
                $detailRef = $this->slugifyDetailRef($legacyTitle);
            }
        }

        $isLoggedIn = isset($_SESSION['user_id']);
        $sessionRole = $_SESSION['role'] ?? 'Invitado';
        $sessionPremium = !empty($_SESSION['premium']) || $sessionRole === 'Admin';
        $detailQuery = $detailRef !== '' && !ctype_digit($detailRef) ? str_replace('-', ' ', $detailRef) : '';

        $this->render('pages/detail', [
            'isLoggedIn' => $isLoggedIn,
            'sessionRole' => $sessionRole,
            'sessionPremium' => $sessionPremium,
            'detailRef' => $detailRef,
            'detailQuery' => $detailQuery,
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

    private function buildDetailPath(string $value): string
    {
        $trimmed = trim($value);
        if ($trimmed !== '' && ctype_digit($trimmed)) {
            return asset_path('detail/' . $trimmed);
        }

        return asset_path('detail/' . $this->slugifyDetailRef($trimmed));
    }

    private function slugifyDetailRef(string $value): string
    {
        $trimmed = trim($value);
        $slug = strtolower(trim((string) iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $trimmed)));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?? '';
        $slug = trim($slug, '-');
        if ($slug === '') {
            $slug = 'anime';
        }

        return $slug;
    }
}

