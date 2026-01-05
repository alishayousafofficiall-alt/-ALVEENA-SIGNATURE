<?php
// Change this to your actual project folder name (in htdocs)
$base_url = '/New folder/project1/';

?>

<footer class="pt-4" style="background-color: #6f42c1; color: black;">
    <div class="container px-3 px-md-5">
        <div class="row justify-content-between">

            <!-- Left: Store Info -->
            <div class="col-md-4 col-12 mb-4 text-center text-md-start">
                <h4 class="mb-3" style="font-weight: bold; color: black;">ALVEENA SIGNATURE</h4>
                <p class="mb-1">
                    ALVEENA SIGNATURE Office,<br>
                    Sector 5, Industrial Area,<br>
                    House No: 24, Near City Market,<br>
                    Lahore 54000, Pakistan
                </p>
                <p class="mb-0">
                    <strong>Contact:</strong> +92 300 1234567<br>
                    <strong>WhatsApp:</strong> +92 310 1234567, +92 301 2345678<br>
                    <strong>Email:</strong> support@alvinasignature.com
                </p>
            </div>

            <!-- Center: Quick Links -->
            <div class="col-md-4 col-12 mb-4 text-center text-md-start">
                <h5 class="mb-3">Quick Links</h5>
                <ul class="list-unstyled mb-0">
                    <li><a href="<?= $base_url ?>index.php">Home</a></li>
                    <li><a href="<?= $base_url ?>page.php?slug=services">Services</a></li>
                    <li><a href="<?= $base_url ?>page.php?slug=privacy_policy">Privacy Policy</a></li>
                    <li><a href="<?= $base_url ?>page.php?slug=terms_conditions">Terms & Conditions</a></li>
                    <li><a href="<?= $base_url ?>review.php?slug=reviews">Reviews</a></li>
                    <li><a href="<?= $base_url ?>page.php?slug=return_exchange">Return & Exchange</a></li>
                    <li><a href="<?= $base_url ?>page.php?slug=delivery_info">Delivery Information</a></li>
                </ul>
            </div>




            <!-- Right: Follow Us -->
            <div class="col-md-4 col-12 mb-4 text-center text-md-start">
                <h5 class="mb-3">Follow Us</h5>
                <a href="#" class="text-dark me-3"><i class="bi bi-facebook"></i></a>
                <a href="#" class="text-dark me-3"><i class="bi bi-instagram"></i></a>
                <a href="#" class="text-dark me-3"><i class="bi bi-whatsapp"></i></a>
                <a href="#" class="text-dark"><i class="bi bi-envelope-fill"></i></a>
            </div>

        </div>

        <div class="text-center py-3 border-top border-secondary">
            &copy; <?= date("Y") ?> ALVEENA SIGNATURE. All rights reserved.
        </div>
    </div>
</footer>

<!-- Bootstrap 5 & Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<style>
    /* Footer hover effects */
    footer a:hover {
        color: #6f42c1;
        text-decoration: underline;
    }

    footer i {
        font-size: 1.4rem;
        transition: 0.3s;
    }

    footer i:hover {
        color: #6f42c1;
    }

    /* Ensure footer stays full width and aligned */
    footer .container {
        max-width: 1200px;
        /* same as desktop container */
        padding-left: 15px;
        padding-right: 15px;
    }

    /* Responsive adjustments */
    @media (max-width: 767px) {
        footer .row {
            text-align: left;
            /* columns aligned left instead of center */
            flex-direction: column;
            gap: 15px;
            /* spacing between sections */
        }

        footer .col-md-4,
        footer .col-6,
        footer .col-12 {
            width: 100%;
            text-align: left;
            padding-left: 0;
            padding-right: 0;
        }

        footer ul {
            padding-left: 0;
            margin-left: 0;
        }

        footer ul li {
            display: block;
            margin-bottom: 5px;
        }
    }

    @media (max-width: 480px) {

        footer h4,
        footer h5 {
            font-size: 18px;
        }

        footer p,
        footer a {
            font-size: 14px;
        }
    }
</style>