<?php
require_once 'db/conn.php';
require_once 'include/header.php'; // should open <html><body>

$slug = $_GET['slug'] ?? 'about-us';

$stmt = $pdo->prepare("SELECT title, content, image1, image2 FROM pages WHERE slug = ?");
$stmt->execute([$slug]);
$page = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$page) {
    echo "<h1>Page not found.</h1>";
    exit;
}
?>

<div class="full-page">
    <?php if ($slug === 'about-us'): ?>
        <div class="about-container">
            <?php if (!empty($page['image1'])): ?>
                <img src="images/<?= htmlspecialchars($page['image1']) ?>" alt="<?= htmlspecialchars($page['title']) ?>" class="left-image">
            <?php endif; ?>

            <div class="center-content">
                <h1><?= htmlspecialchars($page['title']) ?></h1>
                <p><?= nl2br(htmlspecialchars($page['content'])) ?></p>
            </div>

            <?php if (!empty($page['image2'])): ?>
                <img src="images/<?= htmlspecialchars($page['image2']) ?>" alt="<?= htmlspecialchars($page['title']) ?>" class="right-image">
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="single-image-container one-image">
            <?php if (!empty($page['image1'])): ?>
                <img src="images/<?= htmlspecialchars($page['image1']) ?>" alt="<?= htmlspecialchars($page['title']) ?>">
            <?php endif; ?>
            <div class="center-text">
                <h1><?= htmlspecialchars($page['title']) ?></h1>
                <p><?= nl2br(htmlspecialchars($page['content'])) ?></p>
            </div>
        </div>
    <?php endif; ?>
</div>
 <?php require_once 'include/footer.php'; ?>
<style>
/* Base */
html, body {
  margin: 0;
  padding: 0;
  height: auto;            /* no forced viewport height */
  font-family: Arial, sans-serif;
  background: #F3E8FF;
}

/* Full-page wrapper: always start at TOP, no extra vertical space */
.full-page {
  display: block;          /* no flex centering */
  padding: 16px 20px;      /* small, consistent padding */
  background: transparent; /* same as body, no extra band */
}

/* Inner width container (optional, keeps content narrow) */
.full-page > div {
  max-width: 1000px;
  margin: 0 auto;          /* center horizontally only */
}

/* ===== About layout (no empty columns) ===== */
.about-container {
  display: flex;
  align-items: center;      /* vertical align within row */
  justify-content: center;  /* images + text grouped */
  gap: 20px;
  flex-wrap: nowrap;        /* desktop: in one row */
  margin-bottom: 24px;
}

/* If only 1 image or none, layout auto-adjusts (no empty space) */
.about-container > img {
  flex: 0 0 220px;
  width: 220px;
  height: 220px;
  border-radius: 50%;
  object-fit: cover;
  box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.center-content {
  flex: 1 1 420px;
  text-align: center;
  padding: 0;              /* remove extra inner padding */
  margin: 0;
}

/* Headings & paragraphs: no extra top/bottom gaps */
.center-content h1,
.center-text h1 {
  margin: 0 0 12px;
  font-size: 28px;
  color: #333;
  font-weight: bold;
}
.center-content p,
.center-text p {
  margin: 0;
  line-height: 1.7;
  color: #555;
}

/* Single-image pages */
.single-image-container.one-image {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  gap: 12px;               /* tight spacing only between items */
  margin: 0 auto;          /* center horizontally */
  max-width: 600px;
}
.single-image-container.one-image img {
  width: 220px;
  height: 220px;
  border-radius: 50%;
  object-fit: cover;
  box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}
/* Fade-in animation for all pages */ @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px);} to { opacity: 1; transform: translateY(0);} } .single-image-container img, .center-text, .about-container img, .center-content { opacity: 0; animation: fadeInUp 0.8s forwards; }
/* Staggered delays */ .left-image { animation-delay: 0.2s; } .center-content { animation-delay: 0.4s; } .right-image { animation-delay: 0.6s; } .single-image-container.one-image img { animation-delay: 0.2s; } .center-text { animation-delay: 0.4s; } 

/* Responsive */
@media (max-width: 900px) {
  .about-container {
    flex-wrap: wrap;       /* stack on small screens */
    justify-content: center;
    text-align: center;
  }
  .about-container > img {
    width: 180px;
    height: 180px;
    flex-basis: 180px;
  }
  .center-content { flex: 1 1 100%; }
}

/* (Optional) remove any unexpected outer gaps from headings globally */
h1:first-child { margin-top: 0; }

</style>
