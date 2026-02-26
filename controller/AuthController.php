<?php

require_once dirname(__DIR__) . '/Conexion.php';

class AuthController
{
    public function login()
    {
        $correo = $_POST['correo'] ?? null;
        $password = $_POST['password'] ?? null;
        $rol = $_POST['rol'] ?? null;

        if (!$correo || !$password || !$rol) {
            $this->sendResponse(['error' => 'Correo, contraseña y rol son requeridos'], 400);
            return;
        }

        $db = Conexion::getConnect();

        if ($rol === 'coordinador') {
            $stmt = $db->prepare("SELECT * FROM coordinacion WHERE coord_correo = :correo");
            $stmt->bindParam(':correo', $correo);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['coord_password'])) {
                $this->initSession($user['coord_id'], 'coordinador', $user['coord_nombre_coordinador']);
                $this->sendResponse(['message' => 'Login exitoso', 'role' => 'coordinador', 'redirect' => '../dashboard/index.php']);
                return;
            }
        } elseif ($rol === 'instructor') {
            $stmt = $db->prepare("SELECT * FROM instructor WHERE inst_correo = :correo");
            $stmt->bindParam(':correo', $correo);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['inst_password'])) {
                $this->initSession($user['inst_id'], 'instructor', $user['inst_nombres'] . ' ' . $user['inst_apellidos']);
                $this->sendResponse(['message' => 'Login exitoso', 'role' => 'instructor', 'redirect' => '../dashboard/index.php']);
                return;
            }
        } elseif ($rol === 'centro') {
            $stmt = $db->prepare("SELECT * FROM centro_formacion WHERE cent_correo = :correo");
            $stmt->bindParam(':correo', $correo);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['cent_password'])) {
                $this->initSession($user['cent_id'], 'centro', $user['cent_nombre']);
                $this->sendResponse(['message' => 'Login exitoso', 'role' => 'centro', 'redirect' => '../dashboard/index.php']);
                return;
            }
        }

        $this->sendResponse(['error' => 'Credenciales inválidas para el rol seleccionado'], 401);
    }

    private function initSession($id, $role, $name)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user_id'] = $id;
        $_SESSION['user_role'] = $role;
        $_SESSION['user_name'] = $name;
    }

    public function logout()
    {
        session_start();
        session_destroy();
        $this->sendResponse(['message' => 'Logout exitoso']);
    }

    private function sendResponse($data, $statusCode = 200)
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
