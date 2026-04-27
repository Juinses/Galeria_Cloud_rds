<?php
// =======================
// CONEXIÓN A RDS - MYSQL
// =======================
$conn = new mysqli(
    "cloud-database.cx7h6iyasudb.us-east-1.rds.amazonaws.com",
    "admin",
    "Admin1234",
    "cloud_galeria",
    3306
);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Trae TODAS las imágenes (estado 0 o 1)
$sql = "SELECT * FROM Galeria_fotos ORDER BY idGaleria_fotos ASC";
$result = $conn->query($sql);
$imagenes = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
$total = count($imagenes);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Super Galería</title>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600&family=Raleway:wght@300;400;500&display=swap" rel="stylesheet">
<style>
  :root {
    --deep:    #0d0618;
    --dark:    #1a0d2e;
    --mid:     #2d1554;
    --purple:  #5b2d9e;
    --violet:  #8a4fd4;
    --lavender:#c4a0f5;
    --glow:    #b388ff;
    --white:   #f0eaff;
  }

  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  body {
    min-height: 100vh;
    background: var(--deep);
    color: var(--white);
    font-family: 'Raleway', sans-serif;
    overflow-x: hidden;
  }

  /* ── FONDO ANIMADO ── */
  body::before {
    content: '';
    position: fixed;
    inset: 0;
    background:
      radial-gradient(ellipse 80% 60% at 20% 10%, rgba(91,45,158,0.35) 0%, transparent 60%),
      radial-gradient(ellipse 60% 80% at 80% 90%, rgba(138,79,212,0.25) 0%, transparent 60%);
    pointer-events: none;
    z-index: 0;
  }

  /* ── HEADER ── */
  header {
    position: relative;
    z-index: 10;
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 22px 40px;
    background: linear-gradient(90deg, rgba(45,21,84,0.95), rgba(13,6,24,0.95));
    border-bottom: 1px solid rgba(179,136,255,0.2);
    backdrop-filter: blur(10px);
  }

  header .icon { font-size: 28px; }

  header h1 {
    font-family: 'Cinzel', serif;
    font-size: 1.5rem;
    letter-spacing: 3px;
    background: linear-gradient(90deg, var(--lavender), var(--glow));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }

  header .counter {
    margin-left: auto;
    font-size: 0.78rem;
    color: var(--lavender);
    opacity: 0.7;
    letter-spacing: 1px;
  }

  /* ── LAYOUT ── */
  main {
    position: relative;
    z-index: 1;
    max-width: 960px;
    margin: 50px auto;
    padding: 0 20px;
  }

  /* ── CARRUSEL ── */
  .carousel-wrap {
    background: rgba(45,21,84,0.3);
    border: 1px solid rgba(179,136,255,0.15);
    border-radius: 24px;
    padding: 30px;
    backdrop-filter: blur(20px);
    box-shadow: 0 0 60px rgba(91,45,158,0.3), inset 0 0 40px rgba(0,0,0,0.2);
  }

  .carousel {
    position: relative;
    width: 100%;
    height: 420px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    border-radius: 16px;
    background: rgba(13,6,24,0.5);
  }

  .carousel-slide {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.5s ease, transform 0.5s ease;
    transform: scale(0.97);
  }

  .carousel-slide.active {
    opacity: 1;
    transform: scale(1);
  }

  .carousel-slide img {
    max-height: 100%;
    max-width: 100%;
    object-fit: contain;
    border-radius: 12px;
    display: block;
  }

  /* ── FLECHAS ── */
  .arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 10;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    border: 1px solid rgba(179,136,255,0.4);
    background: rgba(45,21,84,0.7);
    color: var(--glow);
    font-size: 20px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    backdrop-filter: blur(8px);
  }

  .arrow:hover {
    background: var(--purple);
    border-color: var(--glow);
    box-shadow: 0 0 20px rgba(179,136,255,0.4);
  }

  .arrow.left  { left:  12px; }
  .arrow.right { right: 12px; }

  /* ── INDICADOR ── */
  .slide-info {
    text-align: center;
    margin-top: 14px;
    font-size: 0.78rem;
    color: var(--lavender);
    letter-spacing: 2px;
    opacity: 0.7;
  }

  /* ── PUNTOS ── */
  .dots {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 16px;
  }

  .dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: rgba(179,136,255,0.3);
    cursor: pointer;
    transition: all 0.3s;
    border: 1px solid rgba(179,136,255,0.3);
  }

  .dot.active {
    background: var(--glow);
    box-shadow: 0 0 10px var(--glow);
    transform: scale(1.3);
  }

  /* ── MINIATURAS ── */
  .thumbnails {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 24px;
    justify-content: center;
  }

  .thumb {
    width: 80px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
    cursor: pointer;
    opacity: 0.45;
    border: 2px solid transparent;
    transition: all 0.25s;
    filter: grayscale(20%);
  }

  .thumb:hover {
    opacity: 0.8;
    transform: translateY(-2px);
  }

  .thumb.active {
    opacity: 1;
    border-color: var(--glow);
    box-shadow: 0 0 12px rgba(179,136,255,0.5);
    filter: none;
  }

  /* ── EMPTY ── */
  .empty {
    text-align: center;
    padding: 80px 20px;
    color: var(--lavender);
    opacity: 0.5;
    font-size: 1.1rem;
    letter-spacing: 2px;
  }

  /* ── KEYBOARD HINT ── */
  .hint {
    text-align: center;
    margin-top: 18px;
    font-size: 0.72rem;
    color: var(--lavender);
    opacity: 0.4;
    letter-spacing: 1px;
  }
</style>
</head>
<body>

<header>
  <span class="icon">🔮</span>
  <h1>SUPER GALERÍA</h1>
  <?php if ($total > 0): ?>
    <span class="counter"><?= $total ?> IMÁGENES</span>
  <?php endif; ?>
</header>

<main>
  <div class="carousel-wrap">

    <?php if ($total === 0): ?>
      <div class="empty">✦ No hay imágenes disponibles ✦</div>

    <?php else: ?>

      <!-- CARRUSEL PRINCIPAL -->
      <div class="carousel" id="carousel">
        <button class="arrow left"  onclick="prev()" title="Anterior">&#10094;</button>

        <?php foreach ($imagenes as $i => $img): ?>
          <div class="carousel-slide <?= $i === 0 ? 'active' : '' ?>" data-index="<?= $i ?>">
            <img
              src="https://cloud-bucket-jb.s3.us-east-1.amazonaws.com/<?= htmlspecialchars($img['foto']) ?>"
              alt="Imagen <?= $i + 1 ?>"
              loading="<?= $i === 0 ? 'eager' : 'lazy' ?>"
            >
          </div>
        <?php endforeach; ?>

        <button class="arrow right" onclick="next()" title="Siguiente">&#10095;</button>
      </div>

      <!-- INDICADOR NUMÉRICO -->
      <div class="slide-info" id="slideInfo">1 / <?= $total ?></div>

      <!-- PUNTOS -->
      <div class="dots" id="dots">
        <?php for ($i = 0; $i < $total; $i++): ?>
          <div class="dot <?= $i === 0 ? 'active' : '' ?>" onclick="show(<?= $i ?>)"></div>
        <?php endfor; ?>
      </div>

      <!-- MINIATURAS -->
      <div class="thumbnails">
        <?php foreach ($imagenes as $i => $img): ?>
          <img
            class="thumb <?= $i === 0 ? 'active' : '' ?>"
            src="https://cloud-bucket-jb.s3.us-east-1.amazonaws.com/<?= htmlspecialchars($img['foto']) ?>"
            alt="Miniatura <?= $i + 1 ?>"
            onclick="show(<?= $i ?>)"
            loading="lazy"
          >
        <?php endforeach; ?>
      </div>

      <p class="hint">← → para navegar con teclado</p>

    <?php endif; ?>

  </div>
</main>

<script>
let current = 0;
const slides = document.querySelectorAll('.carousel-slide');
const thumbs  = document.querySelectorAll('.thumb');
const dots    = document.querySelectorAll('.dot');
const info    = document.getElementById('slideInfo');
const total   = slides.length;

function show(i) {
  slides[current].classList.remove('active');
  thumbs[current]?.classList.remove('active');
  dots[current]?.classList.remove('active');

  current = (i + total) % total;

  slides[current].classList.add('active');
  thumbs[current]?.classList.add('active');
  dots[current]?.classList.add('active');

  if (info) info.textContent = (current + 1) + ' / ' + total;

  // scroll miniatura activa a la vista
  thumbs[current]?.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
}

function next() { show(current + 1); }
function prev() { show(current - 1); }

// Teclado
document.addEventListener('keydown', e => {
  if (e.key === 'ArrowRight') next();
  if (e.key === 'ArrowLeft')  prev();
});

// Swipe táctil
let startX = 0;
const carousel = document.getElementById('carousel');
if (carousel) {
  carousel.addEventListener('touchstart', e => { startX = e.touches[0].clientX; });
  carousel.addEventListener('touchend',   e => {
    const diff = startX - e.changedTouches[0].clientX;
    if (Math.abs(diff) > 40) diff > 0 ? next() : prev();
  });
}
</script>

</body>
</html>