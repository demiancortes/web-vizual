<?php

if (!isset($pdo)) {

		$host = 'localhost';
		$db   = 'persianas_vizual';
		$user = 'root';
		$pass = '';
		$charset = 'utf8mb4';

		$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

		$options = [
				PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				PDO::ATTR_EMULATE_PREPARES   => false,
				PDO::ATTR_PERSISTENT         => false
		];

		try {
				$pdo = new PDO($dsn, $user, $pass, $options);
		} catch (PDOException $e) {

				// en producción NO mostrar error real
				die('Error de conexión');
		}
}