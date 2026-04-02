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
        $animeModel = new \Models\Anime();
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 50;
        $searchQuery = trim((string) ($_GET['q'] ?? ''));
        $statusFilter = trim((string) ($_GET['status'] ?? 'ALL'));
        $typeFilter = trim((string) ($_GET['type'] ?? 'ALL'));
        $yearFilter = trim((string) ($_GET['year'] ?? ''));

        $catalog = $animeModel->getCatalog($page, $perPage, $searchQuery, $statusFilter, $typeFilter, $yearFilter);

        $animes = $catalog['data'];
        $totalAnimes = $catalog['total'];
        $totalPages = $catalog['totalPages'];
        $page = $catalog['currentPage'];
        $airingCount = $catalog['airingCount'];

        $offset = ($page - 1) * $perPage;
        $rangeStart = $totalAnimes > 0 ? $offset + 1 : 0;
        $rangeEnd = $totalAnimes > 0 ? min($offset + count($animes), $totalAnimes) : 0;
        $pageStart = max(1, $page - 2);
        $pageEnd = min($totalPages, $page + 2);

        $queryParams = [
            'q' => $searchQuery,
            'status' => $statusFilter,
            'type' => $typeFilter,
            'year' => $yearFilter,
        ];
        $buildPageUrl = static function (int $targetPage) use ($queryParams): string {
            $params = array_filter(array_merge($queryParams, ['page' => $targetPage]), static fn ($value) => $value !== '' && $value !== 'ALL' && $value !== null);
            return '?' . http_build_query($params);
        };

        $this->render('pages/gestion', [
            'page' => $page,
            'perPage' => $perPage,
            'searchQuery' => $searchQuery,
            'statusFilter' => $statusFilter,
            'typeFilter' => $typeFilter,
            'yearFilter' => $yearFilter,
            'totalAnimes' => $totalAnimes,
            'totalPages' => $totalPages,
            'animes' => $animes,
            'airingCount' => $airingCount,
            'rangeStart' => $rangeStart,
            'rangeEnd' => $rangeEnd,
            'pageStart' => $pageStart,
            'pageEnd' => $pageEnd,
            'buildPageUrl' => $buildPageUrl,
        ]);
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
