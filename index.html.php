<?php
// index.php - Public Service Catalog, Editorial Front Page & Landing Page
require_once __DIR__ . '/includes/auth.php';

$page_title = "Home & Service Catalog";
require_once __DIR__ . '/includes/header.php';

$pdo = get_db_connection();
$services = $pdo->query("SELECT * FROM services WHERE is_active = 1 ORDER BY id ASC")->fetchAll();
$promotions = $pdo->query("SELECT * FROM promotions WHERE is_active = 1 ORDER BY valid_until ASC")->fetchAll();
$barbers = $pdo->query("SELECT u.id, u.full_name, u.preferred_style, bp.bio FROM users u JOIN barber_profiles bp ON u.id = bp.user_id WHERE u.role = 'barber' AND bp.status = 'Active' AND u.is_active = 1")->fetchAll();
?>

<!-- SECTION 1: HERO EDITORIAL BANNER (PRECISION & STYLE) -->
<div id="home" class="hero-onyx-container">
    <div class="hero-onyx-grid">
        <!-- Left Side: Editorial Typography & Content -->
        <div class="hero-onyx-left">
            <div>
                <span class="eyebrow-label eyebrow-label-dark" style="letter-spacing: 2px;">EST. MASTER CRAFTSMANSHIP</span>
                <h1 class="hero-onyx-title">
                    PRECISION & 
                    <span class="hero-onyx-title-indent">STYLE.</span>
                </h1>
                
                <p class="hero-onyx-subtitle">
                    NOLI AT FILI WAS BUILT FOR THE MODERN GENTLEMAN WHO DEMANDS MORE THAN JUST A CUT. A DARK, REFINED AESTHETIC PAIRED WITH A SEAMLESS BOOKING EXPERIENCE — BECAUSE YOUR TIME AND YOUR LOOK BOTH MATTER.
                </p>
            </div>

            <div>
                <div class="hero-onyx-meta">
                    <strong>LOCATION:</strong> 4M M. JIMENEZ, PASIG, 1600 METRO MANILA · (02) 8888-9999<br>
                    <strong>HOURS:</strong> MON - SUN: 9:00 AM - 8:00 PM
                </div>

                <div class="hero-onyx-actions">
                    <a href="/customer/appointments.php" class="btn btn-primary">BOOK NOW</a>
                    <a href="#services" class="btn btn-secondary">SERVICES</a>
                    
                    <div class="hero-onyx-socials" style="margin-left: auto;">
                        <a href="https://instagram.com" target="_blank" class="hero-onyx-social-link">INSTAGRAM</a>
                        <span>·</span>
                        <a href="https://facebook.com" target="_blank" class="hero-onyx-social-link">FACEBOOK</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Grayscale Editorial Portrait -->
        <div class="hero-onyx-right">
            <img src="/assets/images/hero_portrait.jpg" alt="Noli at Fili Gentleman Style Portrait">
        </div>
    </div>

    <!-- Wide High-Contrast Action Banner -->
    <div class="hero-onyx-banner">
        <img src="/assets/images/hero_banner_barber.jpg" alt="Master Barber at Work - Noli at Fili">
    </div>
</div>

<!-- SECTION 2: ABOUT US -->
<div id="about" class="card mb-lg" style="padding: 36px;">
    <div class="about-section-grid">
        <div>
            <span class="eyebrow-label">OUR PHILOSOPHY</span>
            <h2 class="card-title card-title-lg" style="font-size: 2rem; margin-bottom: 16px;">The Noli at Fili Standard</h2>
            <p style="color: #4A4A4A; line-height: 1.7; font-size: 0.9375rem; margin-bottom: 16px;">
                Founded on the principles of classic Philippine elegance and modern barbering precision, <strong>Noli at Fili</strong> delivers an unparalleled grooming experience for gentlemen who value sharp detail and timeless style.
            </p>
            <p style="color: #4A4A4A; line-height: 1.7; font-size: 0.9375rem;">
                Every cut begins with a consultation, executed with premium tools, razor precision, hot towel finishes, and top-tier styling pomades.
            </p>

            <div class="about-stats-grid">
                <div class="about-stat-box">
                    <div class="about-stat-num">8+</div>
                    <div class="about-stat-label">Years Craftsmanship</div>
                </div>
                <div class="about-stat-box">
                    <div class="about-stat-num">20+</div>
                    <div class="about-stat-label">Cut & Styling Options</div>
                </div>
                <div class="about-stat-box">
                    <div class="about-stat-num">100%</div>
                    <div class="about-stat-label">Satisfaction Guaranteed</div>
                </div>
            </div>
        </div>

        <div style="border-left: 2px solid #0A0A0A; padding-left: 32px;">
            <span class="eyebrow-label">THE EXPERIENCE</span>
            <h3 style="font-family: var(--font-serif); font-size: 1.5rem; margin-bottom: 12px;">More Than a Barber Shop</h3>
            <ul class="list-unstyled" style="line-height: 2;">
                <li>✓ <strong>Custom Tailored Consultations</strong> — Matched to your head shape and style preferences.</li>
                <li>✓ <strong>Hot Towel & Straight Razor Detailing</strong> — Clean neck shaves and beard line sculpting.</li>
                <li>✓ <strong>Seamless Online Booking</strong> — Reserve your preferred barber and time slot instantly.</li>
                <li>✓ <strong>Monochrome Luxury Lounge</strong> — Relaxing environment with complimentary refreshments.</li>
            </ul>
        </div>
    </div>
</div>

<!-- SECTION 3: HAIRCUT & GROOMING SERVICES CATALOG -->
<div id="services" class="card mb-lg">
    <div class="card-header">
        <div>
            <span class="eyebrow-label">REFINED SELECTION</span>
            <h2 class="card-title">Haircut & Grooming Catalog</h2>
        </div>
    </div>

    <?php require_once __DIR__ . '/includes/filter_bar.php'; ?>

    <!-- Empty State Container -->
    <div id="filter-empty" class="card empty-state-card hidden" hidden>
        <p class="empty-state">No haircuts match your filters.</p>
        <button type="button" id="filter-reset-empty" class="btn btn-secondary btn-sm mt-sm">Reset filters</button>
    </div>

    <div class="service-catalog-grid">
        <?php foreach ($services as $index => $service): ?>
            <?php $img_url = get_service_image_url($service['name']); ?>
            <div class="service-card"
                 data-category="<?= htmlspecialchars($service['category'] ?? 'Other') ?>"
                 data-price="<?= $service['price'] ?>"
                 data-duration="<?= $service['duration_minutes'] ?>"
                 data-name="<?= htmlspecialchars($service['name']) ?>"
                 data-order="<?= $index ?>">
                <?php if ($img_url): ?>
                    <div class="service-card-image-wrapper">
                        <img src="<?= $img_url ?>" alt="<?= htmlspecialchars($service['name']) ?>" class="service-card-img">
                    </div>
                <?php endif; ?>
                <div class="service-card-body">
                    <div>
                        <div class="service-header">
                            <h3 class="service-title"><?= htmlspecialchars($service['name']) ?></h3>
                            <span class="service-price"><?= format_currency($service['price']) ?></span>
                        </div>
                        <p class="service-desc">
                            <?= htmlspecialchars($service['description']) ?>
                        </p>
                    </div>
                    <div class="service-footer">
                        <span class="service-duration">
                            ⏱ Duration: <?= htmlspecialchars($service['duration_minutes']) ?> mins
                        </span>
                        <a href="/customer/appointments.php?service_id=<?= $service['id'] ?>" class="btn btn-secondary btn-sm">Select Service</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- SECTION 4: OUR MASTER BARBERS & PROMOTIONS -->
<div class="grid-2 mb-lg">
    <!-- Our Master Barbers -->
    <div class="card">
        <div class="card-header">
            <div>
                <span class="eyebrow-label">EXPERT CRAFTSMEN</span>
                <h2 class="card-title">Our Master Barbers</h2>
            </div>
        </div>
        <ul class="list-unstyled">
            <?php foreach ($barbers as $b): ?>
                <li class="list-item-bordered">
                    <strong class="service-title"><?= htmlspecialchars($b['full_name']) ?></strong>
                    <div class="barber-specialty-tag">Specialty: <?= htmlspecialchars($b['preferred_style']) ?></div>
                    <p class="text-muted-sm"><?= htmlspecialchars($b['bio']) ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Active Promotions -->
    <div class="card">
        <div class="card-header">
            <div>
                <span class="eyebrow-label">EXCLUSIVE OFFERS</span>
                <h2 class="card-title">Special Offers & Packages</h2>
            </div>
        </div>
        <?php if (empty($promotions)): ?>
            <p class="text-muted-sm">No current active promotions.</p>
        <?php else: ?>
            <ul class="list-unstyled">
                <?php foreach ($promotions as $promo): ?>
                    <li class="list-item-bordered">
                        <div class="flex-between">
                            <strong class="text-accent"><?= htmlspecialchars($promo['title']) ?></strong>
                            <span class="badge badge-confirmed">Code: <?= htmlspecialchars($promo['code']) ?></span>
                        </div>
                        <p class="text-muted-sm"><?= htmlspecialchars($promo['package_details']) ?></p>
                        <span class="text-muted-xs">Valid until <?= format_date($promo['valid_until']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

<!-- SECTION 5: OUR GALLERY -->
<div id="gallery" class="card mb-lg">
    <div class="card-header">
        <div>
            <span class="eyebrow-label">VISUAL ATMOSPHERE</span>
            <h2 class="card-title">Our Gallery & Craftsmanship</h2>
        </div>
    </div>
    
    <div class="gallery-grid">
        <div class="gallery-card">
            <img src="/assets/images/hero_portrait.jpg" alt="Skin Fade Precision Cut">
            <div class="gallery-caption">Skin Fade Precision</div>
        </div>
        <div class="gallery-card">
            <img src="/assets/images/gallery_beard.jpg" alt="Hot Towel & Straight Razor Shave">
            <div class="gallery-caption">Hot Towel & Razor Lineup</div>
        </div>
        <div class="gallery-card">
            <img src="/assets/images/gallery_interior.jpg" alt="Monochrome Luxury Lounge Interior">
            <div class="gallery-caption">Luxury Barbershop Lounge</div>
        </div>
    </div>
</div>

<!-- SECTION 6: CONTACT US & LOCATION -->
<div id="contact" class="card mb-lg" style="padding: 36px;">
    <div class="card-header">
        <div>
            <span class="eyebrow-label">GET IN TOUCH</span>
            <h2 class="card-title card-title-lg" style="font-size: 1.8rem;">Contact Us & Location</h2>
        </div>
    </div>

    <div class="contact-section-grid">
        <!-- Contact Information & Hours -->
        <div>
            <p style="color: #4A4A4A; font-size: 0.9375rem; line-height: 1.6;">
                Have questions or need assistance with your booking? Visit our shop in Pasig, Metro Manila or reach out to our desk team directly.
            </p>

            <ul class="contact-info-list">
                <li class="contact-info-item">
                    <div class="contact-info-icon">📍</div>
                    <div class="contact-info-text">
                        <strong>Barbershop Location</strong>
                        <p>4M M. Jimenez, Pasig, 1600 Metro Manila</p>
                    </div>
                </li>
                <li class="contact-info-item">
                    <div class="contact-info-icon">📞</div>
                    <div class="contact-info-text">
                        <strong>Phone & Appointments</strong>
                        <p>(02) 8888-9999 · 0917-000-8888</p>
                    </div>
                </li>
                <li class="contact-info-item">
                    <div class="contact-info-icon">✉️</div>
                    <div class="contact-info-text">
                        <strong>Email Enquiries</strong>
                        <p>info@noliatfili.com · support@noliatfili.com</p>
                    </div>
                </li>
                <li class="contact-info-item">
                    <div class="contact-info-icon">🕒</div>
                    <div class="contact-info-text">
                        <strong>Operating Hours</strong>
                        <p>Monday – Sunday: 9:00 AM – 8:00 PM</p>
                    </div>
                </li>
            </ul>
        </div>

        <!-- Quick Contact Form / Inquiry -->
        <div>
            <div style="background-color: #F8F9FA; border: 1px solid #E5E5E5; padding: 24px; border-radius: 4px;">
                <h3 style="font-family: var(--font-serif); font-size: 1.25rem; margin-bottom: 16px;">Send Us a Message</h3>
                <form action="/index.php#contact" method="POST" onsubmit="alert('Thank you! Your message has been sent to Noli at Fili desk.'); return false;">
                    <div class="form-group">
                        <label for="contact_name" class="form-label">Your Name *</label>
                        <input type="text" id="contact_name" class="form-control" required placeholder="e.g. Juan Dela Cruz">
                    </div>
                    <div class="form-group">
                        <label for="contact_email" class="form-label">Email Address *</label>
                        <input type="email" id="contact_email" class="form-control" required placeholder="you@example.com">
                    </div>
                    <div class="form-group">
                        <label for="contact_message" class="form-label">Message *</label>
                        <textarea id="contact_message" class="form-control" rows="3" required placeholder="How can we help you?"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
