<?php
namespace Controllers\Api;

use Controllers\Controller;
use Exception;
use PDO;

class AdminController extends Controller
{
    public function handle(): void
    {
        header('Content-Type: application/json; charset=UTF-8');
        app_start_session();

        $action = (string) ($_GET['action'] ?? '');
        $writeActions = ['add_anime', 'update_studio', 'update_anime', 'delete_anime'];
        if (in_array($action, $writeActions, true)) {
            app_require_method('POST');
            app_verify_csrf();
        } else {
            app_require_method(['GET', 'POST']);
        }

        $isAdmin = isset($_SESSION['user_id'], $_SESSION['role']) && $_SESSION['role'] === 'Admin';
        if (!$isAdmin) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Acceso denegado']);
            exit;
        }

        $data = app_get_json_input();
        if ($action === '' || (!in_array($action, ['list'], true) && empty($data))) {
            echo json_encode(['success' => false, 'error' => 'Peticion invalida']);
            exit;
        }

        $dbConn = (new \Models\Database())->getConnection();
        if (!$dbConn) {
            echo json_encode(['success' => false, 'error' => 'Error de conexion a la base de datos']);
            exit;
        }

        if ($action === 'add_anime') {
            $titulo = trim((string) ($data['titulo'] ?? ''));
            $sinopsis = trim((string) ($data['sinopsis'] ?? ''));
            $estadoRaw = trim((string) ($data['estado'] ?? ''));
            $estadoLower = strtolower($estadoRaw);
            if ($estadoLower === 'finished airing') {
                $estado = 'Finalizado';
            } elseif ($estadoLower === 'currently airing' || $estadoLower === 'en emision') {
                $estado = 'En emision';
            } elseif ($estadoLower === 'not yet aired') {
                $estado = 'Proximamente';
            } else {
                $estado = $estadoRaw;
            }
            $tipoContenido = strtolower(trim((string) ($data['tipo_contenido'] ?? 'anime')));
            $tipoFormato = strtoupper(trim((string) ($data['tipo_formato'] ?? 'ALL')));
            if ($tipoContenido === 'pelicula') {
                $tipo = 'MOVIE';
            } elseif (in_array($tipoFormato, ['TV', 'OVA', 'ONA', 'SPECIAL', 'SHORT'], true)) {
                $tipo = $tipoFormato;
            } else {
                $tipo = 'TV';
            }
            $estudio = trim((string) ($data['estudio'] ?? ''));
            $temporada = trim((string) ($data['temporada'] ?? ''));
            $anio = (int) ($data['anio'] ?? 0);
            $episodios = (int) ($data['episodios'] ?? 0);
            $imagen_url = trim((string) ($data['imagen_url'] ?? ''));
            $generos = is_array($data['generos'] ?? null) ? $data['generos'] : [];

            if ($titulo === '') {
                echo json_encode(['success' => false, 'error' => 'El titulo es obligatorio']);
                exit;
            }

            try {
                $dbConn->beginTransaction();
                $stmt = $dbConn->prepare("INSERT INTO anime (titulo, tipo, estudio, estado, episodios, temporada, anio, sinopsis, imagen_url, puntuacion, activo, creado_por, creado_en) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0.0, 1, 1, NOW())");
                $stmt->execute([$titulo, $tipo, $estudio, $estado, $episodios, $temporada, $anio, $sinopsis, $imagen_url]);
                $anime_id = $dbConn->lastInsertId();

                foreach ($generos as $g_name) {
                    $gStmt = $dbConn->prepare("SELECT id FROM generos WHERE nombre = ?");
                    $gStmt->execute([$g_name]);
                    $g_row = $gStmt->fetch(PDO::FETCH_ASSOC);
                    $g_id = $g_row ? $g_row['id'] : null;
                    if (!$g_id) {
                        $iStmt = $dbConn->prepare("INSERT INTO generos (nombre) VALUES (?)");
                        $iStmt->execute([$g_name]);
                        $g_id = $dbConn->lastInsertId();
                    }
                    $lStmt = $dbConn->prepare("INSERT INTO anime_generos (anime_id, genero_id) VALUES (?, ?)");
                    $lStmt->execute([$anime_id, $g_id]);
                }

                $dbConn->commit();
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                if ($dbConn->inTransaction()) {
                    $dbConn->rollBack();
                }
                echo json_encode(['success' => false, 'error' => 'Error de BD: ' . $e->getMessage()]);
            }
            exit;
        }

        if ($action === 'update_studio') {
            $animeId = (int) ($data['id'] ?? 0);
            $estudio = trim((string) ($data['estudio'] ?? ''));
            if ($animeId <= 0) {
                echo json_encode(['success' => false, 'error' => 'ID invalido']);
                exit;
            }
            try {
                $stmt = $dbConn->prepare("UPDATE anime SET estudio = ? WHERE id = ?");
                $stmt->execute([$estudio, $animeId]);
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'error' => 'Error de BD: ' . $e->getMessage()]);
            }
            exit;
        }

        if ($action === 'update_anime') {
            $animeId = (int) ($data['id'] ?? 0);
            $titulo = trim((string) ($data['titulo'] ?? ''));
            $tipo = trim((string) ($data['tipo'] ?? ''));
            $estudio = trim((string) ($data['estudio'] ?? ''));
            $anio = trim((string) ($data['anio'] ?? ''));
            $estadoRaw = trim((string) ($data['estado'] ?? ''));
            $estadoLower = strtolower($estadoRaw);
            if ($estadoLower === 'finished airing') {
                $estado = 'Finalizado';
            } elseif ($estadoLower === 'currently airing' || $estadoLower === 'en emision') {
                $estado = 'En emision';
            } elseif ($estadoLower === 'not yet aired') {
                $estado = 'Proximamente';
            } else {
                $estado = $estadoRaw;
            }
            $imagen_url = trim((string) ($data['imagen_url'] ?? ''));
            $sinopsis = trim((string) ($data['sinopsis'] ?? ''));
            $temporada = trim((string) ($data['temporada'] ?? ''));
            $episodios = trim((string) ($data['episodios'] ?? ''));

            if ($animeId <= 0 || $titulo === '') {
                echo json_encode(['success' => false, 'error' => 'Datos invalidos']);
                exit;
            }

            try {
                $stmt = $dbConn->prepare("UPDATE anime SET titulo = ?, tipo = ?, estudio = ?, anio = ?, estado = ?, imagen_url = ?, sinopsis = ?, temporada = ?, episodios = ? WHERE id = ?");
                $stmt->execute([$titulo, $tipo, $estudio, $anio !== '' ? (int) $anio : null, $estado, $imagen_url, $sinopsis, $temporada, $episodios !== '' ? (int) $episodios : null, $animeId]);
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'error' => 'Error de BD: ' . $e->getMessage()]);
            }
            exit;
        }

        if ($action === 'delete_anime') {
            $animeId = (int) ($data['id'] ?? 0);
            if ($animeId <= 0) {
                echo json_encode(['success' => false, 'error' => 'ID invalido']);
                exit;
            }
            try {
                $dbConn->beginTransaction();
                $dbConn->prepare("DELETE FROM anime_generos WHERE anime_id = ?")->execute([$animeId]);
                $dbConn->prepare("DELETE FROM anime_characters WHERE anime_id = ?")->execute([$animeId]);
                $dbConn->prepare("DELETE FROM anime_pictures WHERE anime_id = ?")->execute([$animeId]);
                $dbConn->prepare("DELETE FROM anime_videos WHERE anime_id = ?")->execute([$animeId]);
                $dbConn->prepare("DELETE FROM anime WHERE id = ?")->execute([$animeId]);
                $dbConn->commit();
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                if ($dbConn->inTransaction()) {
                    $dbConn->rollBack();
                }
                echo json_encode(['success' => false, 'error' => 'Error de BD: ' . $e->getMessage()]);
            }
            exit;
        }

        echo json_encode(['success' => false, 'error' => 'Accion desconocida']);
    }
}
