<?php
/* =========================
   CONEXIÓN A BASE DE DATOS
   ========================= */

$host = 'localhost';
$db   = 'persianas_vizual';   // 👉 nombre de tu BD
$user = 'root';               // 👉 usuario (XAMPP)
$pass = '';                   // 👉 contraseña (XAMPP)
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
  PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
  $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
  die('Error de conexión a la base de datos');
}
