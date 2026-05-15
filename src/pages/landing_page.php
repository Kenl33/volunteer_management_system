<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Volunteer Participation System</title>
    <link rel="stylesheet" href="../styles/landing_page.css">
</head>
<body class="lp-body">
    <!-- Navbar Section -->
    <header class="lp-header">
        <div class="lp-container lp-nav">
            <h2 class="lp-logo">💙 VolunteerHub</h2>
            <nav class="lp-nav-links">
                <a href="#home">Home</a>
                <a href="#about">About</a>
                <a href="#features">Features</a>
                <a href="#contact">Contact</a>
            </nav>
            <a class="lp-login-link" href="login.php">Sign In</a>
        </div>
    </header>

    <main>
        <!-- Hero Section -->
        <section id="home" class="lp-hero">
            <div class="lp-blob lp-blob-one"></div>
            <div class="lp-blob lp-blob-two"></div>
            <div class="lp-container lp-hero-grid">
                <div>
                    <p class="lp-label">Volunteer Participation System</p>
                    <h1>Make a Difference in Your <span>Community</span></h1>
                    <p>
                        Join local volunteer programs and help organizations manage events
                        with a simple and modern participation system.
                    </p>
                    <div class="lp-hero-actions">
                        <a class="lp-btn lp-btn-primary" href="login.php?tab=register">Join as Volunteer</a>
                        <a class="lp-btn lp-btn-outline" href="login.php?tab=register">Create Event</a>
                    </div>
                    <div class="lp-hero-proof">
                        <span class="lp-avatars">👩 🧑 👨 👩</span>
                        <small>120+ volunteers already making an impact</small>
                    </div>
                </div>
                <div class="lp-hero-card">
                    <h3>Community Impact</h3>
                    <p>Connect volunteers, organize events, and track progress in one dashboard.</p>
                    <div class="lp-mini-stats">
                        <div><i>👥</i><strong>120+</strong><span>Volunteers</span></div>
                        <div><i>📅</i><strong>35</strong><span>Events</span></div>
                        <div><i>✅</i><strong>90%</strong><span>Attendance</span></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section id="about" class="lp-section lp-about">
            <div class="lp-container">
                <h2>About the System</h2>
                <p class="lp-lead">
                    The Volunteer Participation System is built to support communities by
                    making volunteer coordination easy. Organizations can create events,
                    monitor participation, and manage attendance, while volunteers can join
                    activities that match their interests.
                </p>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="lp-section lp-features">
            <div class="lp-container">
                <h2>Key Features</h2>
                <div class="lp-feature-grid">
                    <article class="lp-feature-card">
                        <div class="lp-icon lp-icon-blue">📝</div>
                        <h3>Volunteer Registration</h3>
                        <p>Simple sign-up and login for volunteers with basic profile details.</p>
                        <a href="login.php?tab=register">Learn more →</a>
                    </article>
                    <article class="lp-feature-card">
                        <div class="lp-icon lp-icon-purple">📅</div>
                        <h3>Event Management</h3>
                        <p>Admins can add, update, and organize events in one place.</p>
                        <a href="login.php?tab=register">Learn more →</a>
                    </article>
                    <article class="lp-feature-card">
                        <div class="lp-icon lp-icon-sky">📊</div>
                        <h3>Participation Tracking</h3>
                        <p>Track status, attendance, and event involvement in a clear table view.</p>
                        <a href="login.php?tab=register">Learn more →</a>
                    </article>
                    <article class="lp-feature-card">
                        <div class="lp-icon lp-icon-cyan">🧭</div>
                        <h3>Admin Dashboard</h3>
                        <p>Dashboard layout with summaries for volunteers, events, and progress.</p>
                        <a href="login.php?tab=register">Learn more →</a>
                    </article>
                </div>
            </div>
        </section>

        <!-- CTA section -->
        <section class="lp-cta-bar">
            <div class="lp-container lp-cta-box">
                <div>
                    <h3>Ready to create an impact?</h3>
                    <p>Join as a volunteer or create an event and bring change to your community.</p>
                </div>
                <a class="lp-btn lp-btn-outline-light" href="login.php?tab=register">Get Started Now →</a>
            </div>
        </section>
    </main>

    <!-- Footer section -->
    <footer id="contact" class="lp-footer">
        <div class="lp-container lp-footer-grid">
            <div>
                <h4>💙 VolunteerHub</h4>
                <p>Building stronger communities through volunteerism and meaningful connections.</p>
            </div>
            <div>
                <h5>Quick Links</h5>
                <a href="#home">Home</a>
                <a href="#about">About</a>
                <a href="#features">Features</a>
                <a href="#contact">Contact</a>
            </div>
            <div>
                <h5>Resources</h5>
                <a href="#">Help Center</a>
                <a href="#">Terms of Service</a>
                <a href="#">Privacy Policy</a>
                <a href="#">FAQs</a>
            </div>
            <div>
                <h5>Contact Us</h5>
                <p>support@volunteerhub.com</p>
                <p>+1 234 567 8900</p>
                <p>123 Community Lane, City, State 12345</p>
            </div>
        </div>
        <p class="lp-footer-copy">© 2026 VolunteerHub. All rights reserved.</p>
    </footer>
</body>
</html>