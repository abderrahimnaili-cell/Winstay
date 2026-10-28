<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$userNom = '';
$userEmail = '';

if ($isLoggedIn) {
    require_once __DIR__ . '/backend/config/database.php';
    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare("SELECT nom, email FROM Utilisateurs WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        if ($user) {
            $userNom = htmlspecialchars($user['nom'], ENT_QUOTES, 'UTF-8');
            $userEmail = htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8');
        } else {
            $isLoggedIn = false;
        }
    } catch(Exception $e) {
        $isLoggedIn = false;
    }
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help Center | WINSTAY</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;600&family=Montserrat:wght@200;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Using the main site's CSS -->
    <link rel="stylesheet" href="css/style.css?v=<?= time() ?>">
    
    <style>
        /* Specific Help Center Styles matching the main theme */
        .help-hero {
            padding: 140px 5% 60px;
            text-align: center;
        }
        
        .help-hero h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 3.5rem;
            margin-bottom: 20px;
            color: var(--text-color);
            justify-content: center;
        }
        
        .help-search {
            max-width: 650px;
            margin: 0 auto 40px;
            position: relative;
        }
        
        .help-search input {
            width: 100%;
            padding: 20px 20px 20px 60px;
            font-size: 1.1rem;
            border: 1px solid var(--border-color);
            border-radius: 40px;
            background: var(--card-bg);
            color: var(--text-color);
            backdrop-filter: blur(20px);
            outline: none;
            box-shadow: var(--shadow-md);
            transition: var(--transition-smooth);
            font-family: 'Montserrat', sans-serif;
        }
        
        .help-search input:focus {
            border-color: var(--pink-accent);
            box-shadow: 0 0 20px rgba(0, 224, 198, 0.2);
        }
        
        .help-search i {
            position: absolute;
            left: 25px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--pink-accent);
            font-size: 1.2rem;
        }
        
        .help-tabs {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 40px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .help-tab {
            padding: 15px 24px;
            font-weight: 600;
            color: var(--text-color);
            opacity: 0.6;
            cursor: pointer;
            transition: var(--transition-smooth);
            position: relative;
        }
        
        .help-tab:hover {
            opacity: 1;
            color: var(--pink-accent);
        }
        
        .help-tab.active {
            opacity: 1;
            color: var(--pink-accent);
        }
        
        .help-tab.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: var(--pink-accent);
            box-shadow: 0 0 10px var(--pink-accent);
        }
        
        .help-container {
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            padding: 0 40px 80px;
            box-sizing: border-box;
        }
        
        .guides-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            margin-bottom: 60px;
            width: 100%;
        }

        @media (max-width: 1024px) {
            .guides-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .help-container {
                padding: 0 30px 80px;
            }
        }

        @media (max-width: 600px) {
            .guides-grid {
                grid-template-columns: 1fr;
            }
            .help-container {
                padding: 0 20px 60px;
            }
        }
        
        .guide-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 30px;
            cursor: pointer;
            transition: var(--transition-smooth);
            backdrop-filter: blur(10px);
        }
        
        .guide-card:hover {
            border-color: var(--pink-accent);
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 224, 198, 0.15);
        }
        
        .guide-card i {
            font-size: 2.5rem;
            color: var(--pink-accent);
            margin-bottom: 20px;
        }
        
        .guide-card h3 {
            font-size: 1.2rem;
            margin-bottom: 10px;
            font-family: 'Montserrat', sans-serif;
        }
        
        .guide-card p {
            opacity: 0.7;
            font-size: 0.95rem;
            line-height: 1.5;
        }
        
        .faq-section {
            margin-bottom: 60px;
        }
        
        .faq-section h2 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.5rem;
            margin-bottom: 30px;
        }
        
        .faq-item {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            margin-bottom: 15px;
            overflow: hidden;
            backdrop-filter: blur(10px);
            transition: var(--transition-smooth);
        }
        
        .faq-question {
            padding: 20px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            font-weight: 600;
        }
        
        .faq-question:hover {
            color: var(--pink-accent);
        }
        
        .faq-answer {
            padding: 0 25px;
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            transition: all 0.4s ease;
            line-height: 1.6;
            opacity: 0.8;
        }
        
        .faq-item.active {
            border-color: var(--pink-accent);
        }
        
        .faq-item.active .faq-answer {
            padding: 0 25px 20px;
            max-height: 300px;
            opacity: 1;
        }
        
        .faq-item.active .faq-question i {
            transform: rotate(180deg);
            color: var(--pink-accent);
        }
        
        .contact-box {
            background: linear-gradient(135deg, rgba(0, 224, 198, 0.1) 0%, rgba(30, 30, 30, 0.8) 100%);
            border: 1px solid var(--pink-accent);
            border-radius: var(--radius-lg);
            padding: 50px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .contact-box::before {
            content: '';
            position: absolute;
            top: -50%; left: -50%; width: 200%; height: 200%;
            background: radial-gradient(circle, rgba(0,224,198,0.1) 0%, transparent 70%);
            z-index: 0;
            pointer-events: none;
        }
        
        .contact-content {
            position: relative;
            z-index: 1;
        }
        
        .contact-box h2 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.5rem;
            margin-bottom: 15px;
            justify-content: center;
        }
        
        .btn-glow {
            background: var(--pink-accent);
            color: #000;
            border: none;
            padding: 15px 35px;
            border-radius: 30px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            margin-top: 20px;
            transition: var(--transition-smooth);
            box-shadow: 0 0 15px rgba(0, 224, 198, 0.4);
            font-family: 'Montserrat', sans-serif;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn-glow:hover {
            transform: translateY(-3px);
            box-shadow: 0 0 25px rgba(0, 224, 198, 0.7);
            background: #fff;
        }
        
        /* Modal Style for Contact */
        .contact-modal {
            display: none;
            position: fixed;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            background: var(--menu-bg);
            border: 1px solid var(--pink-accent);
            padding: 40px;
            border-radius: var(--radius-md);
            z-index: 5000;
            width: 90%;
            max-width: 500px;
            box-shadow: var(--shadow-lg), 0 0 30px rgba(0,224,198,0.2);
            backdrop-filter: blur(20px);
            text-align: center;
        }
        
        .contact-modal input, .contact-modal textarea {
            width: 100%;
            background: rgba(0,0,0,0.3);
            border: 1px solid var(--border-color);
            padding: 15px;
            border-radius: 12px;
            color: white;
            margin-bottom: 15px;
            font-family: 'Montserrat', sans-serif;
            box-sizing: border-box;
        }
        
        .contact-modal input:focus, .contact-modal textarea:focus {
            border-color: var(--pink-accent);
            outline: none;
        }

        /* Article Modal */
        .article-modal {
            display: none;
            position: fixed;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            background: var(--menu-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            z-index: 5000;
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: var(--shadow-lg), 0 0 30px rgba(0,0,0,0.5);
            backdrop-filter: blur(20px);
            padding: 0;
        }
        
        .article-header {
            padding: 30px;
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            background: var(--menu-bg);
            z-index: 10;
            display: flex;
            justify-content: space-between;
            align-items: center;
            backdrop-filter: blur(20px);
        }
        
        .article-header h2 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2rem;
            margin: 0;
            color: var(--pink-accent);
        }
        
        .article-body {
            padding: 30px;
            line-height: 1.8;
            font-size: 1.05rem;
            color: rgba(255,255,255,0.85);
        }
        
        .article-body h3 {
            color: white;
            margin: 20px 0 10px;
            font-size: 1.3rem;
        }
    </style>
</head>
<body data-theme="dark">

    <!-- Overlay -->
    <div class="overlay" id="overlay" onclick="closeContactModal(); closeArticle();"></div>

    <!-- Navbar -->
    <nav class="navbar scrolled">
        <div class="logo" onclick="window.location.href='index.php'">
            <img src="images/meho.png" alt="" style="height:45px; width:auto; object-fit:contain;">
            <img src="images/ketba.png" alt="WinStay" style="height:40px; width:auto; object-fit:contain; position:relative; top:10px;">
        </div>
        <div style="display:flex; gap:20px; align-items:center">
            <button onclick="window.location.href='index.php'" style="background: transparent; border: 1px solid var(--border-color); color: var(--text-color); padding: 10px 20px; border-radius: 30px; cursor: pointer; font-weight: 600; transition: 0.3s;" onmouseover="this.style.borderColor='var(--pink-accent)'" onmouseout="this.style.borderColor='var(--border-color)'">
                <i class="fas fa-arrow-left" style="margin-right: 8px;"></i> Back to Home
            </button>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="help-hero">
        <h1>How can we help you?</h1>
        <div class="help-search">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Search for answers, guides, and policies..." onkeyup="filterContent()">
        </div>

        <div class="help-tabs">
            <div class="help-tab active" onclick="switchTab('guest', this)">Guests</div>
            <div class="help-tab" onclick="switchTab('host', this)">Hosts</div>
        </div>
    </section>

    <!-- Main Content -->
    <div class="help-container">
        
        <!-- Guides Grid -->
        <div class="guides-grid" id="guidesGrid">
            <!-- Content injected by JS -->
        </div>

        <!-- FAQ Section -->
        <div class="faq-section">
            <h2>Frequently Asked Questions</h2>
            <div id="faqContainer">
                <!-- Content injected by JS -->
            </div>
        </div>

        <!-- Contact Box -->
        <div class="contact-box">
            <div class="contact-content">
                <h2>Still need help?</h2>
                <p style="opacity: 0.8; margin-bottom: 20px; font-size: 1.1rem;">Our dedicated support team is available 24/7 to assist you with any issues.</p>
                <button class="btn-glow" onclick="openContactModal()">Contact Support</button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer style="background: var(--footer-bg); padding: 45px 5%; border-top: 1px solid var(--border-color); text-align: center; color: rgba(255,255,255,0.5); font-size: 0.9rem;">
        <div style="margin-bottom: 20px; display: flex; justify-content: center; align-items: center; gap: 10px;">
            <img src="images/meho.png" alt="" style="height: 45px; width: auto; object-fit: contain; cursor: pointer;" onclick="window.location.href='index.php'">
            <img src="images/ketba.png" alt="WinStay" style="height: 40px; width: auto; object-fit: contain; position: relative; top: 10px; cursor: pointer;" onclick="window.location.href='index.php'">
        </div>
        <div style="margin-bottom: 15px; display: flex; justify-content: center; gap: 20px; font-weight: 500;">
            <a href="help_center.php" style="color: var(--pink-accent); text-decoration: none; transition: 0.3s;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">Help Center</a>
        </div>
        <p style="margin: 0; opacity: 0.7;">© 2026 Winstay, Inc. All rights reserved.</p>
    </footer>

    <!-- Contact Support Modal -->
    <div class="contact-modal" id="contactModal">
        <i class="fas fa-times" onclick="closeContactModal()" style="position: absolute; top: 20px; right: 20px; cursor: pointer; color: var(--text-color); opacity: 0.6; font-size: 1.2rem;"></i>
        <i class="fas fa-envelope-open-text" style="font-size: 3rem; color: var(--pink-accent); margin-bottom: 20px;"></i>
        <h3 style="font-size: 1.8rem; font-family: 'Cormorant Garamond', serif; margin-bottom: 10px;">Send us a message</h3>
        
        <!-- Logged in state -->
        <div id="contactFormContainer" style="display:none;">
            <p style="opacity: 0.7; margin-bottom: 25px; font-size: 0.9rem;">We typically reply within 2 hours.</p>
            <form onsubmit="submitSupport(event)">
                <input type="text" id="supportName" placeholder="Your Name" readonly style="opacity:0.7; cursor:not-allowed;">
                <input type="email" id="supportEmail" placeholder="Your Email Address" readonly style="opacity:0.7; cursor:not-allowed;">
                <textarea id="supportMessage" rows="4" placeholder="How can we help you?" required></textarea>
                <button type="submit" class="btn-glow" style="width: 100%; margin-top: 10px;" id="submitBtn">Send Message</button>
            </form>
        </div>

        <!-- Not logged in state -->
        <div id="contactLoginPrompt" style="display:none; padding: 20px 0;">
            <p style="opacity: 0.8; margin-bottom: 25px; font-size: 1.1rem; line-height: 1.6;">You need to be logged into your account to contact our support team.</p>
            <button class="btn-glow" style="width: 100%; margin-top: 10px;" onclick="window.location.href='index.php'">Log In / Sign Up</button>
        </div>
    </div>

    <!-- Article Modal -->
    <div class="article-modal" id="articleModal">
        <div class="article-header">
            <h2 id="articleTitle">Article Title</h2>
            <i class="fas fa-times" onclick="closeArticle()" style="cursor: pointer; font-size: 1.5rem; opacity: 0.6; transition: 0.3s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.6'"></i>
        </div>
        <div class="article-body" id="articleBody">
            <!-- Content injected via JS -->
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast" id="helpToast" style="position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%) translateY(100px); background: var(--pink-accent); color: #000; padding: 15px 30px; border-radius: 30px; font-weight: 600; display: flex; align-items: center; gap: 10px; box-shadow: 0 10px 30px rgba(0,224,198,0.4); opacity: 0; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); z-index: 9999;">
        <i class="fas fa-check-circle"></i>
        <span id="toastMessage">Success</span>
    </div>

    <script>
        // State
        const isLoggedIn = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;
        const sessionUser = {
            nom: "<?php echo $userNom; ?>",
            email: "<?php echo $userEmail; ?>"
        };

        // Data
        const data = {
            guest: {
                guides: [
                    { icon: 'fa-search-location', title: 'Finding Stays', desc: 'Tips on searching, filtering, and finding your perfect match.', content: '<h3>Using the Search Bar</h3><p>Enter your destination, dates, and number of guests to start your search. Our smart search will show you the best available properties in Algeria.</p><h3>Applying Filters</h3><p>Once you see the results, you can use the filters to narrow down by price, amenities, and property type to find exactly what you need.</p>' },
                    { icon: 'fa-calendar-check', title: 'Managing Bookings', desc: 'How to modify, cancel, or check details of your reservations.', content: '<h3>Viewing Reservations</h3><p>Go to your Profile and click on "My Trips" to see all your past and upcoming reservations.</p><h3>Modifications</h3><p>To change dates or guest counts, you must contact the host directly. If they agree, the system will update the booking.</p>' },
                    { icon: 'fa-credit-card', title: 'Payments', desc: 'Details about our direct hand-to-hand cash payment policy.', content: '<h3>Hand-to-Hand Payment (Cash)</h3><p>To keep things simple and secure, all payments are conducted directly hand-to-hand upon arrival or check-in. You will pay the host in person.</p><h3>No Online Pre-payment</h3><p>You do not need a credit card to confirm your booking. Simply book your stay online and pay when you arrive at the property.</p>' },
                    { icon: 'fa-shield-halved', title: 'Trust & Safety', desc: 'Our commitment to keeping our community and your data safe.', content: '<h3>Verified Hosts</h3><p>We verify the identity of all our hosts to ensure your safety.</p><h3>24/7 Support</h3><p>If you encounter an emergency during your stay, our Trust & Safety team is available around the clock to assist you.</p>' }
                ],
                faqs: [
                    { q: 'How do I cancel my reservation?', a: 'To cancel a reservation, go to your Profile > My Trips, select the trip you want to cancel, and click "Cancel Reservation". Please review the host\'s cancellation policy before proceeding.' },
                    { q: 'When am I charged for a reservation?', a: 'You are not charged online. Payment is done directly hand-to-hand with the host when you arrive for check-in.' },
                    { q: 'How do I contact my host?', a: 'You can contact your host directly from the Property Details page by clicking the "Contact Host" button, or from your "My Trips" dashboard after booking.' },
                    { q: 'What is the Winstay Guest Refund Policy?', a: 'If a host cancels your reservation, or if you encounter a major travel issue (like an inaccessible or misrepresented listing), you may be eligible for a full or partial refund.' }
                ]
            },
            host: {
                guides: [
                    { icon: 'fa-house-chimney', title: 'Getting Started', desc: 'How to list your property and set up your first calendar.', content: '<h3>Creating a Listing</h3><p>Click "List your home" from your profile. Follow the simple steps to add photos, descriptions, and set your house rules.</p><h3>First Impressions</h3><p>High-quality photos and a detailed description are the best ways to attract your first guests.</p>' },
                    { icon: 'fa-money-bill-wave', title: 'Payouts', desc: 'Understanding how you get paid for your reservations.', content: '<h3>Hand-to-Hand Payouts</h3><p>You will collect payment directly from the guests hand-to-hand upon their arrival. There are no online transfers or waiting periods.</p><h3>Taxes</h3><p>As a host, you are responsible for understanding and complying with local tax laws in your wilaya.</p>' },
                    { icon: 'fa-calendar-days', title: 'Calendar & Pricing', desc: 'Tools to manage your availability and set dynamic pricing.', content: '<h3>Blocking Dates</h3><p>You can block dates you don\'t want to host directly from your dashboard.</p><h3>Smart Pricing</h3><p>Turn on Smart Pricing to automatically adjust your nightly rate based on demand, seasonality, and local events.</p>' },
                    { icon: 'fa-star', title: 'Hosting Standards', desc: 'What we expect from our premium Winstay hosts.', content: '<h3>Cleanliness</h3><p>Every property must meet strict cleanliness guidelines before a guest arrives.</p><h3>Communication</h3><p>Respond to guest inquiries quickly and clearly to maintain a high response rate and earn the Superhost badge.</p>' }
                ],
                faqs: [
                    { q: 'How do I add a new listing?', a: 'Go to your Profile, navigate to "All the home" section, and click on "List your home". Follow the step-by-step wizard to publish your property.' },
                    { q: 'How and when do I get paid?', a: 'You will get paid directly by the guest hand-to-hand upon their arrival. You collect the cash in person.' },
                    { q: 'Can I require a security deposit?', a: 'Yes, you can set a custom security deposit amount in your listing settings under "Pricing".' },
                    { q: 'How do I handle a difficult guest?', a: 'If you encounter an issue during a stay, communicate clearly with the guest first. If the issue persists, contact Winstay Support immediately for mediation.' }
                ]
            }
        };

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            renderContent('guest');
        });

        // Tab Switching
        function switchTab(type, element) {
            document.querySelectorAll('.help-tab').forEach(t => t.classList.remove('active'));
            element.classList.add('active');
            
            // Add a small fade effect
            const container = document.querySelector('.help-container');
            container.style.opacity = '0';
            
            setTimeout(() => {
                renderContent(type);
                container.style.opacity = '1';
                container.style.transition = 'opacity 0.4s ease';
            }, 200);
            
            document.getElementById('searchInput').value = '';
        }

        // Render Content
        function renderContent(type) {
            const guidesHtml = data[type].guides.map((g, index) => `
                <div class="guide-card" onclick="openArticle('${type}', ${index})">
                    <i class="fas ${g.icon}"></i>
                    <h3>${g.title}</h3>
                    <p>${g.desc}</p>
                </div>
            `).join('');
            document.getElementById('guidesGrid').innerHTML = guidesHtml;

            const faqsHtml = data[type].faqs.map((f, index) => `
                <div class="faq-item" onclick="toggleFaq(this)">
                    <div class="faq-question">
                        <span>${f.q}</span>
                        <i class="fas fa-chevron-down" style="transition: 0.3s;"></i>
                    </div>
                    <div class="faq-answer">${f.a}</div>
                </div>
            `).join('');
            document.getElementById('faqContainer').innerHTML = faqsHtml;
        }

        // Toggle FAQ Accordion
        function toggleFaq(element) {
            const isActive = element.classList.contains('active');
            
            // Close all
            document.querySelectorAll('.faq-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Open clicked if it wasn't already open
            if (!isActive) {
                element.classList.add('active');
            }
        }

        // Search Filter
        function filterContent() {
            const query = document.getElementById('searchInput').value.toLowerCase();
            
            // Filter Guides
            const guides = document.querySelectorAll('.guide-card');
            guides.forEach(card => {
                const text = card.innerText.toLowerCase();
                if(text.includes(query)) card.style.display = 'block';
                else card.style.display = 'none';
            });
            
            // Filter FAQs
            const faqs = document.querySelectorAll('.faq-item');
            faqs.forEach(faq => {
                const text = faq.innerText.toLowerCase();
                if(text.includes(query)) faq.style.display = 'block';
                else faq.style.display = 'none';
            });
        }

        // Modal Logic
        function openContactModal() {
            if (isLoggedIn) {
                // User is logged in
                document.getElementById('supportName').value = sessionUser.nom || 'User';
                document.getElementById('supportEmail').value = sessionUser.email || 'user@example.com';
                
                document.getElementById('contactFormContainer').style.display = 'block';
                document.getElementById('contactLoginPrompt').style.display = 'none';
            } else {
                // User is NOT logged in
                document.getElementById('contactFormContainer').style.display = 'none';
                document.getElementById('contactLoginPrompt').style.display = 'block';
            }

            document.getElementById('contactModal').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
        }

        function closeContactModal() {
            document.getElementById('contactModal').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
        }
        
        function openArticle(type, index) {
            const article = data[type].guides[index];
            document.getElementById('articleTitle').innerText = article.title;
            document.getElementById('articleBody').innerHTML = article.content;
            document.getElementById('articleModal').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
        }

        function closeArticle() {
            document.getElementById('articleModal').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
        }

        // Form Submit
        async function submitSupport(e) {
            e.preventDefault();
            
            if (!isLoggedIn) return;

            const btn = document.getElementById('submitBtn');
            const originalText = btn.innerText;
            
            const message = document.getElementById('supportMessage').value;

            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            btn.style.pointerEvents = 'none';
            
            try {
                const response = await fetch('backend/api/messages/contact_support.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'include',
                    body: JSON.stringify({ message })
                });
                
                const data = await response.json();
                
                if (response.ok || data.success) {
                    closeContactModal();
                    e.target.reset();
                    showHelpToast('Message sent to Admin successfully! We will get back to you soon.');
                } else {
                    alert(data.message || 'Failed to send message');
                }
            } catch (err) {
                alert('A network error occurred. Please try again.');
            } finally {
                btn.innerHTML = originalText;
                btn.style.pointerEvents = 'auto';
            }
        }

        // Toast Notification
        function showHelpToast(msg) {
            const toast = document.getElementById('helpToast');
            document.getElementById('toastMessage').innerText = msg;
            
            toast.style.transform = 'translateX(-50%) translateY(0)';
            toast.style.opacity = '1';
            
            setTimeout(() => {
                toast.style.transform = 'translateX(-50%) translateY(100px)';
                toast.style.opacity = '0';
            }, 3000);
        }
    </script>
</body>
</html>
