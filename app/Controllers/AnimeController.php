<?php
namespace Controllers;

use Models\Anime;

class AnimeController extends PageController {

    public function detail($id) {
        if (!$id) {
            // Si no hay ID, redirigimos al inicio o mostramos 404
            header('Location: index');
            exit;
        }

        $animeModel = new Anime();
        $animeInfo = $animeModel->getById($id);

        if (!$animeInfo) {
            // Anime no encontrado en BD
            http_response_code(404);
            $this->render('pages/404'); // Suponiendo que luego exista
            exit;
        }

        // Renderizamos la vista enviándole los datos del modelo
        $this->render('pages/detail', ['anime' => $animeInfo]);
    }

    public function detailByTitle($title) {
        if (!$title) {
            header('Location: index');
            exit;
        }

        $animeModel = new Anime();
        $animeInfo = $animeModel->getByTitle($title);

        if (!$animeInfo) {
            http_response_code(404);
            $this->render('pages/404'); 
            exit;
        }

        $this->render('pages/detail', ['anime' => $animeInfo]);
    }
}
