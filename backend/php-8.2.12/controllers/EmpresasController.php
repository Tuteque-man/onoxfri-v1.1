<?php
include_once 'utils/DBConnection.php';

class EmpresasController
{
    public static function getAll()
    {
        try {
            $db = DBConnection::getInstance()->getConnection();
            $sql = "SELECT id, nombre_empresa, descripcion, usuario_id, fecha_creacion, fecha_actualizacion FROM empresas ORDER BY id DESC";
            $result = $db->query($sql);

            $empresas = [];
            while ($row = $result->fetch_assoc()) {
                $empresas[] = $row;
            }

            Responses::json($empresas, 200);
        } catch (Exception $e) {
            Responses::json(['errors' => ['Error al obtener empresas.']], 500);
        }
    }

    public static function getMine()
    {
        try {
            $db = DBConnection::getInstance()->getConnection();
            // Nota: Si existe un usuario autenticado, idealmente filtrar por su id.
            // Por ahora, devolveremos todas para no romper el frontend.
            $sql = "SELECT id, nombre_empresa, descripcion, usuario_id, fecha_creacion, fecha_actualizacion FROM empresas ORDER BY id DESC";
            $result = $db->query($sql);

            $empresas = [];
            while ($row = $result->fetch_assoc()) {
                $empresas[] = $row;
            }

            Responses::json($empresas, 200);
        } catch (Exception $e) {
            Responses::json(['errors' => ['Error al obtener mis empresas.']], 500);
        }
    }

    public static function create()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        Validator::ensureFields($data, ['nombre_empresa']);

        $nombre = $data['nombre_empresa'];
        $descripcion = isset($data['descripcion']) ? $data['descripcion'] : null;

        $nombreV = new Validator($nombre, 'nombre_empresa');
        $nombreV->required()->minLength(2)->maxLength(255);
        $errors = $nombreV->getErrors();
        if (!empty($errors)) {
            Responses::json(['errors' => $errors], 400);
        }

        try {
            $db = DBConnection::getInstance()->getConnection();
            $stmt = $db->prepare("INSERT INTO empresas (nombre_empresa, descripcion, usuario_id, fecha_creacion) VALUES (?, ?, ?, NOW())");
            $usuarioId = 1; // TODO: reemplazar por el id del usuario autenticado
            $stmt->bind_param('ssi', $nombre, $descripcion, $usuarioId);
            $stmt->execute();

            $id = $db->insert_id;
            Responses::json(['success' => true, 'data' => ['id' => $id]], 201);
        } catch (Exception $e) {
            Responses::json(['errors' => ['Error al crear la empresa.']], 500);
        }
    }

    public static function update()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        Validator::ensureFields($data, ['id', 'nombre_empresa']);

        $id = intval($data['id']);
        $nombre = $data['nombre_empresa'];
        $descripcion = isset($data['descripcion']) ? $data['descripcion'] : null;

        $idV = new Validator($id, 'id');
        $idV->required()->numeric();
        $nombreV = new Validator($nombre, 'nombre_empresa');
        $nombreV->required()->minLength(2)->maxLength(255);
        $errors = array_merge($idV->getErrors(), $nombreV->getErrors());
        if (!empty($errors)) {
            Responses::json(['errors' => $errors], 400);
        }

        try {
            $db = DBConnection::getInstance()->getConnection();
            $stmt = $db->prepare("UPDATE empresas SET nombre_empresa = ?, descripcion = ?, fecha_actualizacion = NOW() WHERE id = ?");
            $stmt->bind_param('ssi', $nombre, $descripcion, $id);
            $stmt->execute();

            Responses::json(['success' => true], 200);
        } catch (Exception $e) {
            Responses::json(['errors' => ['Error al actualizar la empresa.']], 500);
        }
    }

    public static function delete()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        Validator::ensureFields($data, ['id']);

        $id = intval($data['id']);
        $idV = new Validator($id, 'id');
        $idV->required()->numeric();
        $errors = $idV->getErrors();
        if (!empty($errors)) {
            Responses::json(['errors' => $errors], 400);
        }

        try {
            $db = DBConnection::getInstance()->getConnection();
            $stmt = $db->prepare("DELETE FROM empresas WHERE id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();

            Responses::json(['success' => true], 200);
        } catch (Exception $e) {
            Responses::json(['errors' => ['Error al eliminar la empresa.']], 500);
        }
    }
}
