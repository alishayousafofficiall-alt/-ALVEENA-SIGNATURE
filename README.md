Alvina E-Commerce Website
Overview

A full-stack PHP e-commerce website featuring dynamic product sections, trending products, collections, seasonal sales, and newsletter subscription. Fully responsive with modern UI, Swiper.js carousels, and animations.

Features

Homepage: Sliders, "What's New", Eid Sales, Trending Products

Collections: Alvina Collection (e.g., Watches, Shoes)

Product Pages: Detailed product info with price and add-to-cart functionality

User System: Registration, login, orders, and cart management

Newsletter Subscription: Save emails in database with feedback messages

Responsive Design: Works across devices

Animations & Carousels: Swiper.js and Intersection Observer for smooth effects

Technologies

PHP, PDO

MySQL database

HTML5, CSS3, Bootstrap 5

JavaScript (Swiper.js, Intersection Observer, AOS)

Bootstrap Icons

Setup

Clone or copy the project folder into your web server root (htdocs for XAMPP).

Import the database using phpMyAdmin (tables: sliders, products_sections, products, categories, newsletter).

Update db/conn.php with your database credentials.

Run the project:
http://localhost/project1/

Folder Structure
project1/
│
├─ admin/                   # Admin panel scripts
├─ db/                      # Database connection
│   └─ conn.php
├─ images/                  # All images (products, banners, sections)
├─ include/
│   ├─ header.php
│   └─ footer.php
├─ user/
│   ├─ add-to-cart.php
│   ├─ cart.php
│   ├─ checkout.php
│   ├─ orders.php
│   └─ ...
├─ index.php                # Homepage
├─ category.php
├─ product-detail.php
├─ view_section.php
├─ newsletter-save.php
└─ ...

Usage

Browse homepage sections (sliders, trending products, collections).

Click products or categories to view details.

Add products to cart, proceed to checkout.

Subscribe to newsletter to save email.
