<?php
// Configuración de la base de datos
$host = 'cloud-database.cx7h6iyasudb.us-east-1.rds.amazonaws.com';
$usuario = 'admin';
$clave = 'Admin1234';
$bd = 'cloud_galeria';
$s3_base_url = 'https://cloud-bucket-jb.s3.us-east-1.amazonaws.com/';

// Conexión
$conn = new mysqli($host, $usuario, $clave, $bd);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener fotos activas
$sql = "SELECT foto FROM Galeria_fotos WHERE estado = 1";
$resultado = $conn->query($sql);
$fotos = $resultado->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Galería - AgroTec</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f3e5f5; } /* Fondo morado muy claro */
        .navbar, footer { background-color: #ffffff; color: white; } /* Morado oscuro */
        .carousel-item img { height: 500px; object-fit: cover; }
        .carousel-control-prev-icon, .carousel-control-next-icon { background-color: #ffffff; border-radius: 50%; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark mb-4">
    <div class="container">
        <span class="navbar-brand mb-0 h1">Galería de Proyectos</span>
    </div>
</nav>

<div class="container">
    <div id="galeriaCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php if (count($fotos) > 0): ?>
                <?php foreach ($fotos as $index => $item): ?>
                    <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                        <img src="<?php echo $s3_base_url . $item['foto']; ?>" class="d-block w-100" alt="Foto">
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-warning">No hay imágenes disponibles.</div>
            <?php endif; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#galeriaCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#galeriaCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>