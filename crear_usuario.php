<?php
require_once 'db.php';

$usuario = 'admin';
$passwordPlano = 'Covid19-';

$hash = password_hash($passwordPlano, PASSWORD_DEFAULT);

$sql = "INSERT INTO usuarios (usuario, password) VALUES (:usuario, :password)";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':usuario' => $usuario,
    ':password' => $hash
]);

echo "Usuario creado correctamente";