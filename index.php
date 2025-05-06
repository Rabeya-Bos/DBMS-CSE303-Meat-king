<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Meat King - Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@500;700&display=swap" rel="stylesheet"/>
  <style>
    body {
      background: url('img/bg-2.jpg') no-repeat center center fixed;
      color: #fff;
      font-family: 'Roboto', sans-serif;
    }

    .navbar {
      background-color: #2e2e3e;
    }

    .navbar-brand {
      color: #dc3545 !important;
      font-weight: bold;
    }

    .nav-link {
      color: #fff !important;
    }

    .hero-section {
      text-align: center;
      padding: 100px 20px;
      background: linear-gradient(120deg, #1e1e2f, #2e2e3e);
    }

    .hero-section h1 {
      font-size: 3rem;
      font-weight: bold;
      color: #dc3545;
    }

    .hero-section p {
      font-size: 1.25rem;
      color: #ccc;
    }

    .btn-danger {
      background-color: #dc3545;
      border: none;
    }

    .btn-danger:hover {
      background-color: #c82333;
    }

    .section-title {
      text-align: center;
      margin-top: 60px;
      margin-bottom: 30px;
      color: #dc3545;
      font-weight: bold;
      font-size: 2rem;
    }

    .gallery .card {
      background-color: #2e2e3e;
      border: none;
    }

    .gallery .card img {
      height: 200px;
      object-fit: cover;
    }

    .about-section, .contact-section {
      padding: 60px 20px;
    }

    .contact-section input,
    .contact-section textarea {
      background-color: #2e2e3e;
      border: 1px solid #444;
      color: #fff;
    }

    .contact-section input::placeholder,
    .contact-section textarea::placeholder {
      color: #bbb;
    }

    footer {
      background-color: #2e2e3e;
      text-align: center;
      padding: 20px;
      margin-top: 40px;
      color: #aaa;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark px-4">
  <a class="navbar-brand" href="#">🥩 MEAT KING</a>
  <div class="ms-auto">
    <a href="http://localhost:3000/meatking/loging.php" class="btn btn-danger">Login / Sign Up</a>
  </div>
</nav>

<!-- Hero Section -->
<section class="hero-section">
  <div class="container">
    <h1>Welcome to Meat King</h1>
    <p>Your trusted platform for meat production, retail, and distribution.</p>
    <a href="http://localhost:3000/meatking/loging.php" class="btn btn-danger btn-lg mt-4">Get Started</a>
  </div>
</section>

<!-- Gallery Section -->
<section class="gallery container">
  <h2 class="section-title">Our Meats</h2>
  <div class="row g-4">
    <div class="col-md-4">
      <div class="card">
         <img src="img/use1.jpg" alt="User" class="rounded-circle profile-pic me-2">,beef" class="card-img-top" alt="Beef">
        <div class="card-body">
          <h5 class="card-title text-white">Fresh Beef</h5>
          <p class="card-text text-danger">Sourced from local farms with premium quality standards.</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
         <img src="img/use2.jpg" alt="User" class="rounded-circle profile-pic me-2">,meat" class="card-img-top" alt="Chicken">
        <div class="card-body">
          <h5 class="card-title text-white">Organic Chicken</h5>
          <p class="card-text text-danger">Tender and hormone-free chicken, packed with nutrition.</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
         <img src="img/use3.jpg" alt="User" class="rounded-circle profile-pic me-2">,meat" class="card-img-top" alt="Lamb">
        <div class="card-body">
          <h5 class="card-title text-white">Premium Lamb</h5>
          <p class="card-text text-danger">Finest quality lamb cuts, perfect for all cuisines.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- About Us Section -->
<section class="about-section container">
  <h2 class="section-title">About Us</h2>
  <p class="text-center text-danger">
    Meat King is dedicated to bringing you fresh, high-quality meat from trusted farmers and producers.
    We prioritize health, hygiene, and excellence in our meat supply chain. Our platform connects
    consumers, farmers, and retailers in a seamless ecosystem for better food and better lives.
  </p>
</section>

<!-- Contact Us Section -->
<section class="contact-section container">
  <h2 class="section-title">Contact Us</h2>
  <form>
    <div class="row mb-3">
      <div class="col-md-6">
        <input type="email" class="form-control" placeholder="Your Email">
      </div>
      <div class="col-md-6">
        <input type="text" class="form-control" placeholder="Phone Number">
      </div>
    </div>
    <div class="mb-3">
      <textarea class="form-control" rows="5" placeholder="Your Message"></textarea>
    </div>
    <button type="submit" class="btn btn-danger">Send Message</button>
  </form>
</section>

<!-- Footer -->
<footer>
  © 2025 Meat King. All Rights Reserved. Designed by <strong style="color: #dc3545;">Rabeya</strong>.
</footer>

</body>
</html>
