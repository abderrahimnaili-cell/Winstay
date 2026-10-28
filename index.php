<!DOCTYPE html>
<html lang="en" dir="ltr">

<!-- رأس الصفحة: يحتوي على الميتاداتا، الخطوط، وأكواد CSS وJS الخارجية والمحلية -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WINSTAY | Premium Stays in Algeria</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;600&family=Montserrat:wght@200;400;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

    <link rel="stylesheet" href="css/style.css?v=<?= time() ?>">
    

    <!-- Google Identity Services SDK -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>

<body data-theme="dark">

    <!-- الغطاء الخلفي الشفاف عند فتح النوافذ المنبثقة -->
    <div class="overlay" id="overlay" onclick="closeAllPopovers()"></div>

    <!-- تهيئة خدمات تسجيل الدخول بواسطة جوجل (Google Identity Services) -->
    <div id="g_id_onload"
         data-client_id="YOUR_GOOGLE_CLIENT_ID.apps.googleusercontent.com"
         data-callback="handleGoogleLogin"
         data-auto_prompt="false">
    </div>

    <!-- نافذة تسجيل الدخول وإنشاء الحساب المنبثقة (Login & Sign Up Modal) -->
    <!-- LOGIN MODAL -->
    <div id="loginModal" class="auth-modal"
        style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); z-index:5000; text-align:center;">
        <div onclick="closeLoginModal()"
            style="position:absolute; top:15px; right:20px; cursor:pointer; opacity:0.6; font-size:18px;"><i
                class="fas fa-times"></i></div>

        <div class="logo" style="margin-bottom:20px;">
            <img src="images/ketba.png" alt="WinStay" style="height:35px; width:auto; object-fit:contain;">
        </div>
        <div style="margin-bottom:10px;">
            <img src="images/meho.png" alt="Logo" style="height:55px; width:auto; object-fit:contain;">
        </div>
        <h2 id="modalTitle"
            style="margin-bottom:5px; font-family:'Montserrat', sans-serif; font-size:20px; font-weight:800; text-transform:uppercase; letter-spacing:1px; color:#fff;">
            Welcome Back</h2>
        <p id="modalSubtext" style="color:var(--text-color); opacity:0.6; font-size:12px; margin-bottom:15px;">Sign in
            to continue to your account</p>

        <div id="loginErrorMsg" style="display:none; color:#ff5a5f; background:rgba(255,90,95,0.1); border:1px solid rgba(255,90,95,0.3); padding:12px; border-radius:8px; margin-bottom:15px; font-size:13px; text-align:left; font-weight:600;"><i class="fas fa-exclamation-circle" style="margin-right:8px;"></i><span></span></div>

        <input type="text" id="loginName" class="auth-input" placeholder="Full Name"
            style="display:none; margin-bottom:10px;">
        <input type="email" id="loginEmail" class="auth-input" placeholder="Email Address" style="margin-bottom:10px;">
        <div style="position:relative; margin-bottom:10px;">
            <input type="password" id="loginPassword" class="auth-input" placeholder="Password">
        </div>
        <input type="password" id="confirmPassword" class="auth-input" placeholder="Confirm Password"
            style="display:none; margin-bottom:15px;">

        <div id="forgotPassword" style="text-align:right; margin-bottom:15px;">
            <a href="javascript:void(0)" onclick="openForgotPassword()"
                style="color:var(--pink-accent); font-size:12px; text-decoration:none;">Forgot password?</a>
        </div>

        <button onclick="performLogin()" id="mainSubmitBtn" class="premium-btn"
            style="width:100%; margin-bottom:15px;">Log in</button>
        <div style="margin:15px 0; color:rgba(255,255,255,0.2); font-size:11px;">or</div>

        <button class="social-btn" onclick="initSocialLogin('google')"
            style="width:100%; padding:8px; border-radius:8px; border:1px solid rgba(255,255,255,0.1); background:rgba(255,255,255,0.05); color:var(--text-color); margin-bottom:8px; display:flex; align-items:center; justify-content:center; gap:8px;">
            <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" width="14"
                alt="Google Logo"> <span id="googleText" style="font-size:12px;">Continue with Google</span>
        </button>
        <p style="margin-top:25px; font-size:13px; color:rgba(255,255,255,0.6);">
            <span id="footerText">Don't have an account?</span>
            <span id="switchBtn" onclick="toggleAuthMode()"
                style="color:var(--pink-accent); cursor:pointer; font-weight:700;">Sign up</span>
        </p>
    </div>

    <!-- نافذة تأكيد حذف العقار (Confirmation Modal) -->
    <!-- CONFIRMATION MODAL -->
    <div id="confirmModal" class="auth-modal"
        style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); padding:24px; width:95%; max-width:320px; z-index:5100; text-align:center;">
        <i class="fas fa-exclamation-triangle" style="font-size: 40px; color: #ff5a5f; margin-bottom: 15px;"></i>
        <h3 style="margin:0 0 10px; font-family:'Cormorant Garamond', serif; font-size:24px;">Delete this home?</h3>
        <p style="opacity:0.7; margin:0 0 25px; font-size:14px;">This action cannot be undone.</p>
        <div style="display:flex; gap:12px; justify-content:center;">
            <button onclick="closeConfirmModal()"
                style="flex:1; padding:12px; border-radius:12px; border:1px solid var(--border-color); background:transparent; color:var(--text-color); cursor:pointer;">Cancel</button>
            <button onclick="confirmDelete()"
                style="flex:1; padding:12px; border-radius:12px; border:none; background:#ff5a5f; color:white; cursor:pointer; font-weight:600;">Delete</button>
        </div>
    </div>

    <!-- نافذة استرجاع الحساب ورمز التحقق (OTP) وتحديث كلمة المرور -->
    <!-- AUTH MODAL (Recovery/Verification/Reset + Main Auth View) -->
    <div id="authModal" class="auth-modal"
        style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); z-index:5100; text-align:center;">
        <div onclick="closeAllPopovers()" style="position:absolute; top:15px; right:20px; cursor:pointer; opacity:0.6;">
            <i class="fas fa-times"></i>
        </div>

        <!-- Main Auth View (Alternative Signup/Login - Hidden by default but required by JS) -->
        <div id="auth-view-main" style="display:none;">
            <div class="logo" style="margin-bottom:20px;">
                <img src="images/ketba.png" alt="WinStay" style="height:35px; width:auto; object-fit:contain;">
            </div>
            <div style="margin-bottom:10px;">
                <img src="images/meho.png" alt="Logo" style="height:55px; width:auto; object-fit:contain;">
            </div>
            <h2 id="authModalTitle"
                style="font-family:'Montserrat', sans-serif; font-size:20px; font-weight:800; text-transform:uppercase; letter-spacing:1px; color:#fff;">
                Create Account</h2>
            <p id="authModalSub" style="color:var(--text-color); opacity:0.6; font-size:12px; margin-bottom:15px;">Join
                us and find your perfect match</p>

            <div id="authErrorMsg" style="display:none; color:#ff5a5f; background:rgba(255,90,95,0.1); border:1px solid rgba(255,90,95,0.3); padding:12px; border-radius:8px; margin-bottom:15px; font-size:13px; text-align:left; font-weight:600;"><i class="fas fa-exclamation-circle" style="margin-right:8px;"></i><span></span></div>

            <input id="regName" class="auth-input" placeholder="Enter your Full Name"
                style="margin-bottom:10px; display:block;">
            <input id="regEmail" class="auth-input" placeholder="Enter your Email"
                style="margin-bottom:10px; display:block;">
            <input id="regPass" type="password" class="auth-input" placeholder="Password"
                style="margin-bottom:10px; display:block;">
            <input id="regConfirm" type="password" class="auth-input" placeholder="Confirm your password"
                style="margin-bottom:10px; display:block;">

            <button onclick="performAuthLogin()" id="authSubmitBtn" class="premium-btn"
                style="width:100%; margin-top:5px; padding:10px;">Create Account</button>
            <div style="margin:10px 0; color:rgba(255,255,255,0.2); font-size:10px;">or</div>

            <button onclick="initSocialLogin('google')"
                style="width:100%; padding:8px; border-radius:8px; border:1px solid rgba(255,255,255,0.1); background:transparent; color:var(--text-color); display:flex; align-items:center; justify-content:center; gap:8px;">
                <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" width="14"> <span
                    id="googleLabelSmall" style="font-size:12px;">Continue with Google</span>
            </button>

            <p style="margin-top:14px; font-size:13px; color:rgba(255,255,255,0.6);">Already Have an Account? <span
                    onclick="toggleAuthModeAuth()" id="authSwitchBtn"
                    style="color:var(--pink-accent); cursor:pointer; font-weight:700;">sign in</span></p>
        </div>

        <!-- Recovery View -->
        <div id="auth-view-recovery">
            <div style="margin-bottom:15px;"><i class="fas fa-lock"
                    style="font-size:50px; color: var(--pink-accent);"></i></div>
            <h2
                style="font-family:'Montserrat', sans-serif; font-size:24px; font-weight:800; text-transform:uppercase; letter-spacing:1.5px; margin-bottom:10px; color:#fff;">
                Recover Password</h2>
            <p style="color:var(--text-color); opacity:0.7; font-size:13px; margin-bottom:20px;">Enter your email
                address, we will send you a link to reset your password</p>
            <input id="recoveryEmail" class="auth-input" placeholder="Enter your email"
                style="width:100%; margin-bottom:20px; display:block;">
            <button onclick="requestPasswordReset()" class="premium-btn" style="width:100%;">Send Reset link</button>
            <p style="margin-top:20px; font-size:13px;">Remember your password? <span onclick="handleAuthBack()"
                    style="color:var(--pink-accent); cursor:pointer; font-weight:700">Log In</span></p>
        </div>

        <!-- Verification View -->
        <div id="auth-view-verification" style="display:none;">
            <div style="margin-bottom:15px;"><i class="fas fa-envelope-open-text"
                    style="font-size:40px; color: var(--pink-accent);"></i></div>
            <h2
                style="font-family:'Montserrat', sans-serif; font-size:24px; font-weight:800; text-transform:uppercase; letter-spacing:1.5px; margin-bottom:10px; color:#fff;">
                Verification</h2>
            <p style="color:var(--text-color); opacity:0.6; font-size:13px; margin-bottom:15px;">Enter verification
                code<br><small>We've sent a 6-digit code to your email</small></p>
            <div style="display:flex; justify-content:center; gap:8px; margin-bottom:25px;">
                <input class="auth-input otp-box" maxlength="1"
                    style="width:40px; height:40px; text-align:center; padding:0 !important; border-radius:8px !important;">
                <input class="auth-input otp-box" maxlength="1"
                    style="width:40px; height:40px; text-align:center; padding:0 !important; border-radius:8px !important;">
                <input class="auth-input otp-box" maxlength="1"
                    style="width:40px; height:40px; text-align:center; padding:0 !important; border-radius:8px !important;">
                <input class="auth-input otp-box" maxlength="1"
                    style="width:40px; height:40px; text-align:center; padding:0 !important; border-radius:8px !important;">
                <input class="auth-input otp-box" maxlength="1"
                    style="width:40px; height:40px; text-align:center; padding:0 !important; border-radius:8px !important;">
                <input class="auth-input otp-box" maxlength="1"
                    style="width:40px; height:40px; text-align:center; padding:0 !important; border-radius:8px !important;">
            </div>
            <button onclick="verifyOTP()" class="premium-btn" style="width:100%;">Verify Code</button>
            <p style="margin-top:20px; font-size:13px; color:rgba(255,255,255,0.6);">Didn't receive the code? <span
                    onclick="resendOTP()" style="color:var(--pink-accent); cursor:pointer; font-weight:bold;">Resend Code</span></p>
            <p style="margin-top:12px; font-size:12px; color:rgba(255,255,255,0.5);">Code expires in <span
                    id="otpTimer" style="color:var(--pink-accent); font-weight:bold;">05:00</span></p>
        </div>

        <div id="auth-view-reset" style="display:none;">
            <h2
                style="font-family:'Montserrat', sans-serif; font-size:24px; font-weight:800; text-transform:uppercase; letter-spacing:1.5px; margin-bottom:10px; color:#fff;">
                Update Password</h2>
            <p style="color:var(--text-color); opacity:0.7; font-size:13px; margin-bottom:20px;">Set New
                Password<br><small>Please create a new password for your account</small></p>
            <input type="password" id="newPass" class="auth-input" placeholder="Enter your Password"
                style="width:100%; margin-bottom:15px; display:block;">
            <input type="password" id="newPassConfirm" class="auth-input" placeholder="Confirm your Password"
                style="width:100%; margin-bottom:15px; display:block;">
            <div style="text-align:left; font-size:12px; color:rgba(255,255,255,0.6); margin:15px 0;">
                <p><i class="fas fa-check-circle" style="color:var(--pink-accent)"></i> At least 8 characters</p>
                <p><i class="fas fa-check-circle" style="color:var(--pink-accent)"></i> At least one special character
                </p>
            </div>
            <button onclick="resetPassword()" class="premium-btn" style="width:100%;">Update Password</button>
        </div>
    </div>

    <div class="toast" id="toast">
        <i class="fas fa-check-circle"></i>
        <span id="toastMsg">Changes saved successfully</span>
    </div>

    <!-- شريط التنقل العلوي للموقع (Navbar): يحتوي على اللوغو والقائمة المنسدلة للمستخدم -->
    <nav class="navbar" id="nav">
        <div class="logo" onclick="handleMenuClick('Home')">
            <img src="images/meho.png" alt="" style="height:45px; width:auto; object-fit:contain;">
            <img src="images/ketba.png" alt="WinStay" style="height:40px; width:auto; object-fit:contain; position:relative; top:10px;">
        </div>
        <div style="display:flex; gap:20px; align-items:center">
            <div class="user-menu-container">
                <div class="user-trigger" onclick="toggleProfileMenu(event)">
                    <i class="fas fa-bars"></i>
                    <i class="fas fa-user-circle" id="userIcon"
                        style="font-size: 28px; color: var(--text-color); opacity: 0.7;"></i>
                    <div id="userBadge"
                        style="display:none; position:absolute; top:0; right:0; width:10px; height:10px; background:var(--primary-color); border-radius:50%;">
                    </div>
                </div>
                <div class="profile-dropdown" id="profileDropdown">
                    <div class="dropdown-item" onclick="handleMenuClick('Home')"><i class="fas fa-home"></i> <b>Home</b>
                    </div>
                    <div class="dropdown-item" onclick="handleMenuClick('Listings')"><i class="fas fa-list-ul"></i> My
                        Listings</div>
                    <div class="dropdown-item" onclick="handleMenuClick('Profile')"><i class="fas fa-user-edit"></i>
                        Profile Settings</div>
                    <div class="dropdown-item" onclick="handleMenuClick('Favorites')"><i class="fas fa-heart"></i>
                        Favorites</div>
                    <div class="dropdown-item" onclick="handleMenuClick('Help')"><i class="fas fa-question-circle"></i>
                        Help Center</div>
                    <div class="dropdown-item logout" onclick="handleMenuClick('Logout')"><i
                            class="fas fa-power-off"></i> Logout</div>
                </div>
            </div>
        </div>
    </nav>

    <!-- الواجهة الرئيسية للموقع (Home View) -->
    <!-- Home View Sections -->
    <div id="homeView">
        <!-- قسم الواجهة الترحيبية والبحث (Hero Section) -->
        <section class="hero" id="heroSection">
            <div style="text-align: center; margin-bottom: 20px;" data-aos="fade-down">
                <h2 id="heroTitle"
                    style="font-family: 'Cormorant Garamond'; font-size: 3rem; margin: 0; font-weight: 600;">Explore
                    Dzayer</h2>
                <p id="heroSub" style="opacity: 0.6; letter-spacing: 1px; font-weight: 200;">Discover luxury stays
                    across 69 wilayas</p>
            </div>
        </section>

        <!-- محرك البحث التفاعلي (الوجهة، التواريخ، عدد الضيوف) -->
        <div class="search-wrapper" id="searchWrapper">
            <div class="search-container" id="searchBar" data-aos="zoom-in" data-aos-delay="200">
                <div class="search-section" id="section-dest" onclick="setActive('dest', event)">
                    <label>Where</label>
                    <input type="text" id="destInput" placeholder="Explore destinations" readonly>
                    <div class="popover" id="pop-dest">
                        <input type="text" class="wilaya-search" id="popoverSearchInput" placeholder="Search wilaya..."
                            onclick="event.stopPropagation()" onkeyup="filterWilayas()">
                        <div class="wilaya-list" id="wilayaList"></div>
                    </div>
                </div>
                <div class="divider"></div>
                <div class="search-section" id="section-dates" onclick="setActive('dates', event)">
                    <label>When</label>
                    <input type="text" id="dateInput" placeholder="Add dates" readonly>
                    <div class="popover" id="pop-dates" style="min-width: 320px;">
                        <input type="text" id="agendaInput" style="display:none">
                    </div>
                </div>
                <div class="divider"></div>
                <div class="search-section" id="section-guests" onclick="setActive('guests', event)">
                    <label>Who</label>
                    <input type="text" id="guestInput" placeholder="Add guests" readonly>
                    <div class="popover" id="pop-guests" style="right:0; left:auto; min-width: 320px;">
                        <div class="guest-row"
                            style="display:flex; justify-content:space-between; align-items:center; padding:15px 0; border-bottom:1px solid var(--border-color)">
                            <div><b>Adults</b><br><small style="opacity:0.6">Ages 13+</small></div>
                            <div style="display:flex; align-items:center; gap:15px">
                                <button class="btn-round" onclick="updateGuest('adults', -1, event)"
                                    style="width:32px; height:32px; border-radius:50%; border:1px solid var(--border-color); background:none; color:var(--text-color); cursor:pointer">-</button>
                                <span id="cnt-adults">0</span>
                                <button class="btn-round" onclick="updateGuest('adults', 1, event)"
                                    style="width:32px; height:32px; border-radius:50%; border:1px solid var(--border-color); background:none; color:var(--text-color); cursor:pointer">+</button>
                            </div>
                        </div>
                        <div class="guest-row"
                            style="display:flex; justify-content:space-between; align-items:center; padding:15px 0">
                            <div><b>Children</b><br><small style="opacity:0.6">Ages 2–12</small></div>
                            <div style="display:flex; align-items:center; gap:15px">
                                <button class="btn-round" onclick="updateGuest('children', -1, event)"
                                    style="width:32px; height:32px; border-radius:50%; border:1px solid var(--border-color); background:none; color:var(--text-color); cursor:pointer">-</button>
                                <span id="cnt-children">0</span>
                                <button class="btn-round" onclick="updateGuest('children', 1, event)"
                                    style="width:32px; height:32px; border-radius:50%; border:1px solid var(--border-color); background:none; color:var(--text-color); cursor:pointer">+</button>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="search-btn" onclick="handleSearch()"><i class="fas fa-search"></i></button>
            </div>
        </div>

        <!-- حالة المفضلة الفارغة (تظهر عند عدم وجود عقارات محفوظة في المفضلة) -->
        <div id="emptyState">
            <i class="far fa-heart"></i>
            <h2>No Favorites Yet</h2>
            <p>Tap the heart on any property to save it here.</p>
            <button class="save-btn" onclick="handleMenuClick('Home')">Explore Stays</button>
        </div>

        <!-- عنوان صفحة المفضلة (يظهر عند عرض العقارات المحفوظة) -->
        <div id="favoritesHeader" style="display:none; padding: 140px 5% 40px; text-align: left;">
            <h1 style="font-family: 'Cormorant Garamond', serif; font-size: 3.5rem; margin: 0; color: var(--text-color); line-height: 1.2;">Favorites</h1>
            <p style="opacity: 0.6; margin-top: 15px; font-size: 1.1rem;">Your saved homes and places</p>
        </div>

        <!-- الشبكة الرئيسية لعرض بطاقات العقارات المدرجة (Grid) -->
        <main>
            <div class="grid" id="mainGrid"></div>
        </main>
    </div>


    <!-- صفحة رحلاتي وحجوزاتي كضيف (My Trips View) -->
    <!-- ================= MY TRIPS VIEW ================= -->
    <div id="tripsView" style="display: none; padding: 40px; max-width: 1200px; margin: 0 auto;">
        <h1 style="font-family: 'Cormorant Garamond', serif; font-size: 3rem; margin-bottom: 30px;">My Trips</h1>
        <div id="tripsList" style="display: grid; gap: 20px;">
            <!-- Trips injected via JS -->
        </div>
    </div>

    <!-- صفحة طلبات الحجز الواردة لعقاراتي كمستضيف (Host Reservations View) -->
    <!-- ================= HOST RESERVATIONS VIEW ================= -->
    <div id="reservationsView" style="display: none; padding: 40px; max-width: 1200px; margin: 0 auto;">
        <h1 style="font-family: 'Cormorant Garamond', serif; font-size: 3rem; margin-bottom: 30px;">Reservations (Host)</h1>
        <div id="reservationsList" style="display: grid; gap: 20px;">
            <!-- Reservations injected via JS -->
        </div>
    </div>

    <!-- صفحة الملف الشخصي وإعدادات الحساب (My Profile View) -->
    <!-- ================= MY PROFILE VIEW ================= -->
    <div id="profileView">
        <div class="profile-layout">

            <!-- الشريط الجانبي للملف الشخصي (الصورة الشخصية، الاسم، إحصائيات التوثيق) -->
            <!-- SIDEBAR -->
            <div class="profile-sidebar">
                <div class="profile-avatar" id="sidebarAvatar" onclick="triggerProfileUpload()">J</div>
                <div id="removePhotoLink" onclick="removeProfilePic()" style="display:none; color:#ff5a5f; cursor:pointer; font-size:12px; margin-top:5px; text-decoration:underline;">Remove Photo</div>
                <input type="file" id="profilePicUpload" accept="image/*" style="display:none"
                    onchange="handleProfilePic(this)">
                <h2 id="sidebarName">John Doe</h2>
                <p id="sidebarStatus" style="opacity:0.6">Guest</p>

                <div class="sidebar-stats">
                    <div class="stat-row"><i class="fas fa-check-circle"></i> Identity verified</div>
                    <div class="stat-row"><i class="fas fa-shield-alt"></i> Secure account</div>
                    <div class="stat-row" id="sidebarJoined"><i class="fas fa-calendar-alt"></i> Joined in 2024</div>
                </div>
            </div>

            <!-- CONTENT -->
            <div class="profile-content">

                <!-- MAIN -->
                <div id="profileMain">
                    <h1>About you</h1>
                    <p style="opacity:0.7">Manage your personal information and hosting settings.</p>

                    <!-- القسم الرئيسي لإعدادات الحساب (بيانات الحساب، الأمان، المسكن) -->
                    <div class="profile-section">
                        <h3>Account settings</h3>

                        <div class="info-card" onclick="openProfileSub('personal')">
                            <div class="info-text">
                                <b>Personal info</b>
                                <span>Your name and contact details</span>
                            </div>
                            <i class="fas fa-chevron-right"></i>
                        </div>

                        <div class="info-card" onclick="openProfileSub('security')">
                            <div class="info-text">
                                <b>Login & security</b>
                                <span>Password and account safety</span>
                            </div>
                            <i class="fas fa-chevron-right"></i>
                        </div>

                        <div class="info-card" onclick="openProfileSub('all-homes')">
                            <div class="info-text">
                                <b>All the home</b>
                                <span>Manage your listed properties</span>
                            </div>
                            <i class="fas fa-chevron-right"></i>
                        </div>


                    </div>
                </div>

                <!-- واجهة تعديل البيانات الشخصية (الاسم والبريد الإلكتروني) -->
                <!-- PERSONAL INFO -->
                <div id="view-personal" class="sub-view">
                    <div class="back-link" onclick="closeProfileSub()">← Back</div>
                    <h1>Personal Info</h1>

                    <div class="settings-input-group">
                        <label>Full name</label>
                        <input type="text" id="inputName" placeholder="John Doe">
                    </div>

                    <div class="settings-input-group">
                        <label>Email</label>
                        <input type="email" id="inputEmail" placeholder="john@mail.com">
                    </div>

                    <button class="save-btn" onclick="savePersonalInfo()">Save changes</button>
                </div>

                <!-- واجهة الحماية والأمان (تغيير كلمة المرور الشخصية) -->
                <!-- SECURITY -->
                <div id="view-security" class="sub-view">
                    <div class="back-link" onclick="closeProfileSub()">← Back</div>
                    <h1>Login & Security</h1>

                    <div class="settings-input-group">
                        <label>Current password</label>
                        <input type="password" id="inputCurrentPass">
                    </div>

                    <div class="settings-input-group">
                        <label>New password</label>
                        <input type="password" id="inputNewPass">
                    </div>

                    <div class="settings-input-group">
                        <label>Confirm new password</label>
                        <input type="password" id="inputConfirmPass">
                    </div>

                    <button class="save-btn" onclick="saveSecurity()">Update password</button>
                </div>

                <!-- واجهة إدراج مسكن جديد خطوة بخطوة للمستضيف (Host Wizard) -->
                <!-- ADD YOUR HOME (Airbnb Style Multi-Step Overhaul) -->
                <div id="view-host" class="sub-view">
                    <div class="step-progress-container" id="hostProgressContainer">
                        <div class="step-progress-bar" id="hostProgressBar"></div>
                    </div>

                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:40px;">
                        <button class="close-btn-simple" onclick="openProfileSub('all-homes')">✕</button>
                        <span
                            style="font-size:12px; font-weight:700; letter-spacing:1px; color:var(--primary-color)">STEP-BY-STEP
                            HOSTING</span>
                    </div>

                    <!-- STEP 1: Category Selection -->
                    <div class="host-step active" id="hostStep1">
                        <div class="host-step-header">
                            <h1>What type of place is this?</h1>
                            <p>Choose the category that best describes your home.</p>
                        </div>
                        <div class="selection-grid">
                            <div class="selection-card" onclick="selectHostCategory(this, 'House')">
                                <i class="fas fa-home"></i>
                                <span>House</span>
                            </div>
                            <div class="selection-card" onclick="selectHostCategory(this, 'Apartment')">
                                <i class="fas fa-building"></i>
                                <span>Apartment</span>
                            </div>
                            <div class="selection-card" onclick="selectHostCategory(this, 'Guesthouse')">
                                <i class="fas fa-hotel"></i>
                                <span>Guesthouse</span>
                            </div>
                            <div class="selection-card" onclick="selectHostCategory(this, 'Studio')">
                                <i class="fas fa-door-open"></i>
                                <span>Studio</span>
                            </div>
                            <div class="selection-card" onclick="selectHostCategory(this, 'Cabin')">
                                <i class="fas fa-mountain"></i>
                                <span>Cabin</span>
                            </div>
                            <div class="selection-card" onclick="selectHostCategory(this, 'Villa')">
                                <i class="fas fa-swimming-pool"></i>
                                <span>Villa</span>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2: Location Selection -->
                    <div class="host-step" id="hostStep2">
                        <div class="host-step-header">
                            <h1>Where's your place located?</h1>
                            <p>Pin your exact location on the map. This helps guests find your place.</p>
                        </div>
                        <div class="host-section">
                            <div id="hostMap" style="height: 400px; border-radius: 20px; border: 1px solid var(--border-color); margin-bottom: 15px; z-index: 1; position: relative; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                                <button type="button" onclick="locateMe()" class="locate-me-btn" title="Precise Location">
                                    <i class="fas fa-location-arrow"></i>
                                </button>
                            </div>
                            <p style="font-size: 14px; opacity: 0.7; text-align: center;"><i class="fas fa-info-circle"></i> Drag the pin or click on the map to set your location.</p>
                            
                            <input type="hidden" id="hostLat">
                            <input type="hidden" id="hostLng">
                        </div>
                    </div>

                    <!-- STEP 3: Confirm Address -->
                    <div class="host-step" id="hostStep3">
                        <div class="host-step-header">
                            <h1>Confirm your address</h1>
                            <p>We've pre-filled these details based on your map selection. Feel free to correct them.</p>
                        </div>
                        <div class="host-section">
                            <div class="settings-input-group" style="position:relative; margin-bottom: 20px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Wilaya</label>
                                <div style="position: relative;">
                                    <i class="fas fa-map-marker-alt"
                                        style="position:absolute; left:18px; top:16px; color:var(--primary-color); z-index: 10;"></i>
                                    <input type="text" id="hostWilaya" placeholder="Search Wilaya (e.g. Oran)"
                                        onkeyup="filterHostWilayas()" onclick="toggleHostWilayaList(event)"
                                        autocomplete="off" style="padding-left: 45px; height: 50px; font-size: 15px; width: 100%; border-radius: 12px; border: 1px solid var(--border-color); background: rgba(255,255,255,0.05); color: var(--text-color); outline: none; backdrop-filter: blur(10px);">
                                    <div class="popover" id="hostWilayaListPopover"
                                        style="width: 100%; top: 100%; margin-top: 5px; box-sizing: border-box; max-height: 250px; display: none;">
                                        <div class="wilaya-list" id="hostWilayaList"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="settings-input-group" style="margin-bottom: 15px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Detailed Address</label>
                                <textarea id="hostAddress" placeholder="e.g. 123 Rue de la Liberté, Oran" 
                                    style="width:100%; padding:15px; border-radius:12px; border: 1px solid var(--border-color); background: rgba(255,255,255,0.05); color:var(--text-color); min-height:120px; resize:none; font-family:inherit; outline:none; box-sizing: border-box; font-size: 15px; line-height: 1.5; backdrop-filter: blur(10px);"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 4: Basics -->
                    <div class="host-step" id="hostStep4">
                        <div class="host-step-header">
                            <h1>Share some basics about your place</h1>
                            <p>You'll add more details later, like bed types.</p>
                        </div>
                        <div class="host-section">
                            <div class="host-counter-row">
                                <h4>Guests</h4>
                                <div class="premium-counter">
                                    <button class="premium-counter-btn"
                                        onclick="updateHostCounter('guests', -1)">-</button>
                                    <span id="host-guests-val">4</span>
                                    <button class="premium-counter-btn"
                                        onclick="updateHostCounter('guests', 1)">+</button>
                                </div>
                            </div>
                            <div class="host-counter-row">
                                <h4>Bedrooms</h4>
                                <div class="premium-counter">
                                    <button class="premium-counter-btn"
                                        onclick="updateHostCounter('bedrooms', -1)">-</button>
                                    <span id="host-bedrooms-val">2</span>
                                    <button class="premium-counter-btn"
                                        onclick="updateHostCounter('bedrooms', 1)">+</button>
                                </div>
                            </div>
                            <div class="host-counter-row">
                                <h4>Beds</h4>
                                <div class="premium-counter">
                                    <button class="premium-counter-btn"
                                        onclick="updateHostCounter('beds', -1)">-</button>
                                    <span id="host-beds-val">2</span>
                                    <button class="premium-counter-btn"
                                        onclick="updateHostCounter('beds', 1)">+</button>
                                </div>
                            </div>
                            <div class="host-counter-row" style="border-bottom:none">
                                <h4>Bathrooms</h4>
                                <div class="premium-counter">
                                    <button class="premium-counter-btn"
                                        onclick="updateHostCounter('baths', -1)">-</button>
                                    <span id="host-baths-val">1</span>
                                    <button class="premium-counter-btn"
                                        onclick="updateHostCounter('baths', 1)">+</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 5: Amenities -->
                    <div class="host-step" id="hostStep5">
                        <div class="host-step-header">
                            <h1>Tell guests what your place has to offer</h1>
                            <p>You can add more amenities after you publish your listing.</p>
                        </div>
                        <div class="selection-grid" style="display:flex; flex-wrap:wrap; gap:12px;">
                            <!-- Top 25 Essential Amenities -->
                            <div class="selection-card" onclick="toggleAmenity(this, 'wifi')"><i
                                    class="fas fa-wifi"></i><span>Wifi</span></div>
                            <div class="selection-card" onclick="toggleAmenity(this, 'kitchen')"><i
                                    class="fas fa-utensils"></i><span>Kitchen</span></div>
                            <div class="selection-card" onclick="toggleAmenity(this, 'ac')"><i
                                    class="fas fa-snowflake"></i><span>Air conditioning</span></div>
                            <div class="selection-card" onclick="toggleAmenity(this, 'parking')"><i
                                    class="fas fa-car"></i><span>Free parking</span></div>
                            <div class="selection-card" onclick="toggleAmenity(this, 'workspace')"><i
                                    class="fas fa-laptop"></i><span>Workspace</span></div>
                            <div class="selection-card" onclick="toggleAmenity(this, 'tv')"><i
                                    class="fas fa-tv"></i><span>TV / Netflix</span></div>
                            <div class="selection-card" onclick="toggleAmenity(this, 'washer')"><i
                                    class="fas fa-soap"></i><span>Washer</span></div>
                            <div class="selection-card" onclick="toggleAmenity(this, 'dryer')"><i
                                    class="fas fa-tshirt"></i><span>Dryer</span></div>
                            <div class="selection-card" onclick="toggleAmenity(this, 'iron')"><i
                                    class="fas fa-hand-holding-heart"></i><span>Iron</span></div>
                            <div class="selection-card" onclick="toggleAmenity(this, 'hair-dryer')"><i
                                    class="fas fa-wind"></i><span>Hair dryer</span></div>
                            <div class="selection-card" onclick="toggleAmenity(this, 'shampoo')"><i
                                    class="fas fa-pump-soap"></i><span>Shampoo</span></div>
                            <div class="selection-card" onclick="toggleAmenity(this, 'hangers')"><i
                                    class="fas fa-tshirt"></i><span>Hangers</span></div>
                            <div class="selection-card" onclick="toggleAmenity(this, 'heating')"><i
                                    class="fas fa-fire-alt"></i><span>Heating</span></div>
                            <div class="selection-card" onclick="toggleAmenity(this, 'smoke-alarm')"><i
                                    class="fas fa-bell"></i><span>Smoke alarm</span></div>
                            <div class="selection-card" onclick="toggleAmenity(this, 'co-alarm')"><i
                                    class="fas fa-biohazard"></i><span>CO alarm</span></div>
                            <div class="selection-card" onclick="toggleAmenity(this, 'self-checkin')"><i
                                    class="fas fa-key"></i><span>Self check-in</span></div>
                            <div class="selection-card" onclick="toggleAmenity(this, 'refrigerator')"><i
                                    class="fas fa-temperature-low"></i><span>Refrigerator</span></div>
                            <div class="selection-card" onclick="toggleAmenity(this, 'microwave')"><i
                                    class="fas fa-bolt"></i><span>Microwave</span></div>
                            <div class="selection-card" onclick="toggleAmenity(this, 'stove')"><i
                                    class="fas fa-fire-burner"></i><span>Stove</span></div>
                            <div class="selection-card" onclick="toggleAmenity(this, 'coffee')"><i
                                    class="fas fa-coffee"></i><span>Coffee maker</span></div>
                            <div class="selection-card" onclick="toggleAmenity(this, 'dishes')"><i
                                    class="fas fa-glass-martini"></i><span>Dishes</span></div>
                            <div class="selection-card" onclick="toggleAmenity(this, 'garden')"><i
                                    class="fas fa-tree"></i><span>Garden</span></div>
                            <div class="selection-card" onclick="toggleAmenity(this, 'patio')"><i
                                    class="fas fa-columns"></i><span>Patio / Balcony</span></div>
                            <div class="selection-card" onclick="toggleAmenity(this, 'linens')"><i
                                    class="fas fa-bed"></i><span>Bed linens</span></div>
                            <div class="selection-card" onclick="toggleAmenity(this, 'pillows')"><i
                                    class="fas fa-couch"></i><span>Extra pillows</span></div>
                        </div>
                    </div>
                                       <!-- STEP 6: Photos -->
                    <div class="host-step" id="hostStep6">
                        <div class="host-step-header">
                            <h1>Add some photos of your place</h1>
                            <p>You'll need at least 1 photo to get started.</p>
                        </div>
                        <div class="host-section">
                            <div class="photo-upload-area" onclick="document.getElementById('hostPhotoInput').click()"
                                style="background:rgba(255,255,255,0.02); border:1px dashed var(--border-color); border-radius: 16px; height:220px; display:flex; flex-direction:column; justify-content:center; align-items:center; cursor:pointer;">
                                <i class="fas fa-images"
                                    style="font-size: 48px; margin-bottom: 20px; color: var(--primary-color);"></i>
                                <span style="font-weight:600; font-size:16px; text-decoration:underline;">Upload from
                                    your device</span>
                            </div>
                            <input type="file" id="hostPhotoInput" multiple accept="image/*" style="display: none;"
                                onchange="handlePhotoUpload(this)">
                            <div id="photoPreviews"
                                style="display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 16px; margin-top: 24px;">
                            </div>
                        </div>
                    </div>

                    <!-- STEP 7: Description -->
                    <div class="host-step" id="hostStep7">
                        <div class="host-step-header">
                            <h1>Now, let's describe your place</h1>
                            <p>Keep it short and sweet. You can always edit it later.</p>
                        </div>
                        <div class="host-section">
                            <textarea id="hostDesc"
                                placeholder="Create a description that shares what makes your place special."
                                style="width:100%; padding:24px; border-radius:16px; border:1px solid var(--border-color); background:rgba(255,255,255,0.03); color:var(--text-color); min-height:180px; resize:vertical; font-family:inherit; font-size: 16px; line-height: 1.6; outline:none; box-sizing: border-box;"></textarea>
                        </div>
                    </div>

                    <!-- STEP 8: Price -->
                    <div class="host-step" id="hostStep8">
                        <div class="host-step-header">
                            <h1>Finally, set your price</h1>
                            <p>You can change it at any time.</p>
                        </div>
                        <div class="host-section" style="text-align: center;">
                            <div
                                style="display:inline-flex; align-items:center; background:rgba(255,255,255,0.03); padding:40px; border:1px solid var(--border-color); border-radius:24px; width: 100%; justify-content: center;">
                                <span
                                    style="font-size:48px; font-weight:700; margin-right:15px; opacity: 0.8;">DZD</span>
                                <input type="number" id="hostPrice" placeholder="15000"
                                    style="font-size:64px; font-weight:700; width:300px; border:none; background:transparent; color:var(--text-color); outline:none; text-align:left;">
                            </div>
                            <p style="margin-top: 20px; opacity: 0.6;">Guest price (including taxes and fees) is usually
                                14% higher.</p>
                        </div>
                    </div>

                    <!-- STEP 9: Host Intro -->
                    <div class="host-step" id="hostStep9">
                        <div class="host-step-header" style="text-align: center; max-width: 600px; margin: 60px auto;">
                            <span style="color: var(--primary-color); font-weight: 700; text-transform: uppercase; letter-spacing: 2px; font-size: 14px;">Last Part</span>
                            <h1 style="font-size: 48px; margin: 24px 0; font-family: 'Outfit', sans-serif;">Now, let's talk about you</h1>
                            <p style="font-size: 18px; opacity: 0.7; line-height: 1.6;">Guests want to know who they are staying with. Sharing a bit about yourself helps build trust and makes your listing stand out from the rest.</p>
                            <div style="margin-top: 60px; position: relative; display: inline-block;">
                                <i class="fas fa-user-circle" style="font-size: 100px; color: var(--primary-color); opacity: 0.2;"></i>
                                <div style="position: absolute; bottom: 0; right: 0; background: var(--primary-color); width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 4px solid var(--bg-color);">
                                    <i class="fas fa-check" style="color: white; font-size: 14px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 10: Host Details -->
                    <div class="host-step" id="hostStep10">
                        <div class="host-step-header">
                            <h1>Tell guests about yourself</h1>
                            <p>This information will appear on your public profile and listing page.</p>
                        </div>
                        <div class="host-section">
                            <div class="settings-input-group" style="margin-bottom: 24px;">
                                <label style="display: block; margin-bottom: 10px; font-weight: 600; font-size: 15px;">About you (Bio)</label>
                                <textarea id="hostBio" placeholder="I love hosting and meeting people from all over the world. My goal is to provide a home away from home..." 
                                    style="width:100%; padding:20px; border-radius:16px; border: 1px solid var(--border-color); background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); color:var(--text-color); min-height:140px; resize:vertical; font-family:inherit; outline:none; font-size: 16px; line-height: 1.6; box-sizing: border-box;"></textarea>
                            </div>
                            
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                                <div class="settings-input-group">
                                    <label style="display: block; margin-bottom: 10px; font-weight: 600; font-size: 15px;">Profession</label>
                                    <input type="text" id="hostProfession" placeholder="e.g. Designer / Teacher" 
                                        style="width: 100%; padding: 16px; border: 1px solid var(--border-color); border-radius: 12px; background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); color: var(--text-color); outline: none; box-sizing: border-box; font-size: 16px;">
                                </div>
                                <div class="settings-input-group">
                                    <label style="display: block; margin-bottom: 10px; font-weight: 600; font-size: 15px;">Hosting since</label>
                                    <input type="text" id="hostSince" placeholder="e.g. 2023" 
                                        style="width: 100%; padding: 16px; border: 1px solid var(--border-color); border-radius: 12px; background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); color: var(--text-color); outline: none; box-sizing: border-box; font-size: 16px;">
                                </div>
                            </div>

                            <div class="settings-input-group" style="margin-top: 24px;">
                                <label style="display: block; margin-bottom: 10px; font-weight: 600; font-size: 15px;">Languages you speak</label>
                                <input type="text" id="hostLanguages" placeholder="e.g. English, French, Arabic" 
                                    style="width: 100%; padding: 16px; border: 1px solid var(--border-color); border-radius: 12px; background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); color: var(--text-color); outline: none; box-sizing: border-box; font-size: 16px;">
                            </div>
                        </div>
                    </div>

                    <!-- STEP 11: Things to Know -->
                    <div class="host-step" id="hostStep11">
                        <div class="host-step-header">
                            <h1>Things to know</h1>
                            <p>Set your house rules, safety features, and cancellation policy.</p>
                        </div>
                        <div class="host-section">
                            <!-- Cancellation Policy -->
                            <div class="settings-input-group" style="margin-bottom: 24px;">
                                <label style="display: block; margin-bottom: 10px; font-weight: 600; font-size: 15px;">Cancellation Policy</label>
                                <textarea id="hostCancelPolicy" placeholder="e.g. Free cancellation before Sept 2..." 
                                    style="width:100%; padding:20px; border-radius:16px; border: 1px solid var(--border-color); background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); color:var(--text-color); min-height:100px; resize:vertical; font-family:inherit; outline:none; font-size: 16px; line-height: 1.6; box-sizing: border-box;"></textarea>
                            </div>
                            
                            <!-- House Rules -->
                            <div class="settings-input-group" style="margin-bottom: 24px;">
                                <label style="display: block; margin-bottom: 10px; font-weight: 600; font-size: 15px;">House Rules</label>
                                <textarea id="hostHouseRules" placeholder="e.g. Check-in after 4:00 PM, Check-out before 11:00 AM, Maximum 4 guests" 
                                    style="width:100%; padding:20px; border-radius:16px; border: 1px solid var(--border-color); background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); color:var(--text-color); min-height:100px; resize:vertical; font-family:inherit; outline:none; font-size: 16px; line-height: 1.6; box-sizing: border-box;"></textarea>
                            </div>

                            <!-- Safety & Property -->
                            <div class="settings-input-group">
                                <label style="display: block; margin-bottom: 10px; font-weight: 600; font-size: 15px;">Safety & Property</label>
                                <textarea id="hostSafety" placeholder="e.g. Carbon monoxide alarm, Smoke alarm" 
                                    style="width:100%; padding:20px; border-radius:16px; border: 1px solid var(--border-color); background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); color:var(--text-color); min-height:100px; resize:vertical; font-family:inherit; outline:none; font-size: 16px; line-height: 1.6; box-sizing: border-box;"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Fixed Footer Navigation -->
                    <div class="host-footer">
                        <button class="btn-back" id="hostBackBtn" onclick="navigateHostStep(-1)"
                            style="visibility: hidden;">Back</button>
                        <button class="save-btn" id="hostNextBtn" onclick="navigateHostStep(1)"
                            style="padding: 14px 40px; border-radius: 12px; font-size: 16px;">Next</button>
                    </div>
                </div>

                <!-- واجهة إدارة ومتابعة العقارات الخاصة بي المدرجة (My Listings) -->
                <!-- ALL HOMES VIEW -->
                <div id="view-all-homes" class="sub-view">
                    <div class="back-link" onclick="closeProfileSub()">← Back</div>
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 30px">
                        <div>
                            <h1 style="font-size: 32px;">My Listings</h1>
                            <p style="opacity:0.7;">Create and manage your listed properties with ease.</p>
                        </div>
                        <button class="save-btn" style="padding: 12px 24px; font-size:14px; border-radius: 8px;"
                            onclick="initAddHome()">+ Create New Listing</button>
                    </div>

                    <div id="my-homes-list">
                        <!-- Dynamic List will appear here -->
                    </div>
                </div>



                <!-- واجهة طلب الانضمام كمستضيف وتوثيق بطاقة التعريف الوطنية (Become a Host) -->
                <!-- HOST APPLICATION -->
                <div id="view-host-apply" class="sub-view">
                    <div class="back-link" onclick="closeProfileSub()">← Back</div>
                    <div style="max-width: 600px; margin: 0 auto; text-align: center;">
                        <h1 style="font-size: 32px; margin-bottom: 10px;">Become a Host</h1>
                        <p style="opacity: 0.7; margin-bottom: 30px;">Join our community of hosts. Please provide your details so we can verify your account.</p>
                        
                        <div style="text-align: left; background: var(--card-bg); padding: 30px; border-radius: 16px; border: 1px solid var(--border-color);">
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Full Name</label>
                                <input type="text" id="applyHostName" style="width: 100%; padding: 14px; border: 1px solid var(--border-color); border-radius: 8px; background: transparent; color: var(--text-color);" placeholder="Enter your full name">
                            </div>
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Phone Number</label>
                                <input type="tel" id="applyHostPhone" style="width: 100%; padding: 14px; border: 1px solid var(--border-color); border-radius: 8px; background: transparent; color: var(--text-color);" placeholder="Enter your phone number">
                            </div>
                            <div class="form-group" style="margin-bottom: 30px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 600;">National ID Card (Carte Nationale)</label>
                                
                                <div style="position: relative; overflow: hidden; display: flex; align-items: center; justify-content: space-between; border: 1px dashed var(--pink-accent); border-radius: 12px; padding: 20px; background: rgba(0, 224, 198, 0.05); transition: all 0.3s ease;">
                                    <div style="display: flex; align-items: center; gap: 15px;">
                                        <div style="background: rgba(0, 224, 198, 0.15); width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--pink-accent);">
                                            <i class="fas fa-file-upload" style="font-size: 20px;"></i>
                                        </div>
                                        <div style="text-align: left;">
                                            <span id="carteNationalFileName" style="display: block; font-weight: 600; color: var(--text-color); font-size: 15px; max-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Upload your ID</span>
                                            <span style="font-size: 12px; color: rgba(255,255,255,0.5);">PNG, JPG or PDF</span>
                                        </div>
                                    </div>
                                    <div style="background: var(--card-bg); border: 1px solid var(--border-color); color: #fff; padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 14px;">Browse</div>
                                    
                                    <input type="file" id="applyHostCarteNational" accept="image/*,application/pdf" 
                                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;"
                                        onchange="document.getElementById('carteNationalFileName').innerText = this.files[0] ? this.files[0].name : 'Upload your ID';">
                                </div>
                            </div>
                            <button class="save-btn" style="width: 100%; padding: 16px;" onclick="submitHostApplication()">Submit Application</button>
                        </div>
                    </div>
                </div>            </div>
        </div>
    </div>


    <!-- تذييل الصفحة الرئيسي (Footer) -->
    <footer style="background: var(--footer-bg); padding: 45px 5%; border-top: 1px solid var(--border-color); text-align: center; color: rgba(255,255,255,0.5); font-size: 0.9rem;">
        <div style="margin-bottom: 20px; display: flex; justify-content: center; align-items: center; gap: 10px;">
            <img src="images/meho.png" alt="" style="height: 45px; width: auto; object-fit: contain; cursor: pointer;" onclick="handleMenuClick('Home')">
            <img src="images/ketba.png" alt="WinStay" style="height: 40px; width: auto; object-fit: contain; position: relative; top: 10px; cursor: pointer;" onclick="handleMenuClick('Home')">
        </div>
        <div style="margin-bottom: 15px; display: flex; justify-content: center; gap: 20px; font-weight: 500;">
            <a href="help_center.php" style="color: var(--pink-accent); text-decoration: none; transition: 0.3s;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">Help Center</a>
        </div>
        <p style="margin: 0; opacity: 0.7;">© 2026 Winstay, Inc. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <!-- ========================================== -->
    <!-- NEW: PROPERTY DETAILS MODAL (ARABIC)       -->
    <!-- ========================================== -->

    <!-- نافذة تفاصيل العقار بالكامل (Property Details Modal) -->
    <div class="property-modal" id="arPropertyModal" style="display: none;">
        <div class="modal-content-wrapper">

            <!-- Top Actions -->
            <div class="modal-top-bar">
                <button class="close-btn-simple" onclick="closeArModal()">✕</button>
                <div class="modal-actions">
                    <div class="action-link" onclick="shareListing()"><i class="fas fa-share-square"></i> Share</div>
                    <div class="action-link" onclick="toggleLikeDetails()"><i id="detailsHeart"
                            class="far fa-heart"></i> Save</div>
                </div>
            </div>

            <!-- Title -->
            <div class="listing-title-section">
                <h1 id="arTitle"></h1>
            </div>

            <!-- Gallery -->
            <div class="modal-gallery-grid">
                <div class="gallery-item gallery-main"><img id="arImgMain" src=""></div>
                <div class="gallery-item gallery-sub-top"><img id="arImg2" src=""></div>
                <div class="gallery-item gallery-sub-bot"><img id="arImg3" src=""></div>
                <div class="gallery-item gallery-end-top"><img id="arImg4" src=""></div>
                <div class="gallery-item gallery-end-bot"><img id="arImg5" src="">
                    <button class="show-photos-btn" onclick="openGalleryModal()"><i class="fas fa-th"></i> Show all photos</button>
                </div>
            </div>

            <div class="modal-body">
                <!-- Details Column -->
                <div class="details-col">

                    <div class="listing-subtitle">
                        <span><span id="arCity"></span></span>
                        <span id="arGuests"></span>
                        <span id="arBedrooms"></span>
                        <span id="arBeds"></span>
                        <span id="arBaths"></span>
                    </div>

                    <!-- Things to Know -->
                    <div class="things-to-know-section" style="padding: 24px 0; border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color); margin-bottom: 24px;">
                        <h2 style="font-size: 22px; font-weight: 600; margin-bottom: 24px; color: var(--text-color);">Things to know</h2>
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 24px;">
                            
                            <!-- Cancellation Policy -->
                            <div>
                                <i class="far fa-calendar-times" style="font-size: 24px; margin-bottom: 16px; color: var(--text-color);"></i>
                                <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 12px; color: var(--text-color);">Cancellation policy</h3>
                                <p id="arCancelPolicy" style="font-size: 14px; opacity: 0.8; margin-bottom: 12px; line-height: 1.5; color: var(--text-color); white-space: pre-wrap;"></p>
                            </div>

                            <!-- House Rules -->
                            <div>
                                <i class="far fa-clock" style="font-size: 24px; margin-bottom: 16px; color: var(--text-color);"></i>
                                <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 12px; color: var(--text-color);">House rules</h3>
                                <p id="arHouseRules" style="font-size: 14px; opacity: 0.8; margin-bottom: 12px; line-height: 1.5; color: var(--text-color); white-space: pre-wrap;"></p>
                            </div>

                            <!-- Safety & Property -->
                            <div>
                                <i class="fas fa-shield-alt" style="font-size: 24px; margin-bottom: 16px; color: var(--text-color);"></i>
                                <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 12px; color: var(--text-color);">Safety & property</h3>
                                <p id="arSafetyRules" style="font-size: 14px; opacity: 0.8; margin-bottom: 12px; line-height: 1.5; color: var(--text-color); white-space: pre-wrap;"></p>
                            </div>

                        </div>
                    </div>

                    <!-- Host Mini -->
                    <div class="host-mini-row">
                        <img src="" class="host-avatar" id="arHostImg">
                        <div class="host-info-text">
                            <h3>Host: <span id="arHostName"></span></h3>
                            <p id="arHostTenure"></p>
                        </div>
                    </div>
                    
                    <!-- Welcome Message -->
                    <div class="feature-row" style="margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid var(--border-color);">
                        <div class="feature-icon"><i class="fas fa-heart" style="color: var(--pink-accent);"></i></div>
                        <div class="feature-content">
                            <h3 style="font-size: 18px; margin-bottom: 8px;">Your home away from home</h3>
                            <p style="font-size: 15px; line-height: 1.6; opacity: 0.85;">Welcome to your second home! We hope you find comfort and tranquility here, and that your stay is filled with peace and beautiful moments. This place was created to give you a warm feeling, just like being at home. You are always welcome.</p>
                        </div>
                    </div>

                    <!-- Description Title -->
                    <h2 style="font-size: 22px; font-weight: 600; margin-bottom: 16px; margin-top: 32px; color: var(--text-color);">About this space</h2>

                    <!-- Description -->
                    <div class="description-section">
                        <p id="arDesc" class="description-text">
                        </p>
                        <div class="show-more-link">Show more <i class="fas fa-chevron-left"
                                style="font-size:12px; transform: rotate(-90deg);"></i></div>
                    </div>

                    <!-- Amenities -->
                    <div class="amenities-section">
                        <h2>What this place offers</h2>
                        <div class="amenity-grid">
                            <div class="amenity-item"><i class="fas fa-wifi"></i> Wifi</div>
                            <div class="amenity-item"><i class="fas fa-utensils"></i> Kitchen</div>
                            <div class="amenity-item"><i class="fas fa-car"></i> Free parking on premises</div>
                            <div class="amenity-item"><i class="fas fa-tshirt"></i> Free dryer – In unit</div>
                            <div class="amenity-item"><i class="fas fa-columns"></i> Private patio or balcony</div>
                            <div class="amenity-item"><i class="fas fa-horse-head"></i> Children’s books and toys</div>
                            <div class="amenity-item"><i class="fas fa-elevator"></i> Elevator</div>
                            <div class="amenity-item"><i class="fas fa-slash"></i> Free washer – In unit</div>
                            <div class="amenity-item"><i class="far fa-snowflake"></i> Air conditioning</div>
                            <div class="amenity-item"><i class="fas fa-baby-carriage"></i> Crib</div>
                        </div>
                        <button class="btn-outline"
                            onclick="document.getElementById('arAmenitiesModal').style.display='flex'">Show all
                            amenities</button>
                    </div>

                    <!-- REVIEWS AND LOCATION REMOVED FROM HERE -->

                    <!-- Reviews Section -->
                    <div class="reviews-section" style="padding: 32px 0; border-top: 1px solid var(--border-color);">
                        <h2 id="arReviewsSectionHeader"
                            style="font-size: 22px; font-weight: 600; margin-bottom: 25px; display:flex; align-items:center; gap:8px">
                            0 reviews
                        </h2>
                        
                        <!-- Add Review Form -->
                        <div id="arAddReviewContainer" style="display:none; margin-bottom: 30px; padding: 24px; border: 1px solid var(--border-color); border-radius: 16px; background: rgba(255, 255, 255, 0.02); text-align: left;">
                            <h3 style="font-size: 20px; margin-bottom: 16px; font-weight: 600; color: var(--text-color);">Share your experience</h3>
                            
                            <div style="display:flex; gap:8px; margin-bottom: 20px;" id="arReviewStars">
                                <i class="far fa-star" data-rating="1" style="font-size: 28px; cursor: pointer; color: #555; transition: 0.2s;" onclick="setReviewRating(1)"></i>
                                <i class="far fa-star" data-rating="2" style="font-size: 28px; cursor: pointer; color: #555; transition: 0.2s;" onclick="setReviewRating(2)"></i>
                                <i class="far fa-star" data-rating="3" style="font-size: 28px; cursor: pointer; color: #555; transition: 0.2s;" onclick="setReviewRating(3)"></i>
                                <i class="far fa-star" data-rating="4" style="font-size: 28px; cursor: pointer; color: #555; transition: 0.2s;" onclick="setReviewRating(4)"></i>
                                <i class="far fa-star" data-rating="5" style="font-size: 28px; cursor: pointer; color: #555; transition: 0.2s;" onclick="setReviewRating(5)"></i>
                            </div>
                            
                            <textarea id="arReviewText" placeholder="What did you love about this place?" style="width: 100%; min-height: 120px; padding: 16px; border: 1px solid var(--border-color); border-radius: 12px; margin-bottom: 20px; font-family: inherit; font-size: 15px; resize: vertical; outline: none; background: transparent; color: var(--text-color); line-height: 1.5;"></textarea>
                            
                            <div style="display: flex; justify-content: flex-end;">
                                <button id="arSubmitReviewBtn" class="save-btn" style="padding: 12px 28px; font-size: 16px; font-weight: 600; border-radius: 8px;">Submit Review</button>
                            </div>
                        </div>

                        <!-- Individual Reviews -->
                        <div id="arReviewsPreviewGrid" class="reviews-grid" style="display:grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                            <!-- Reviews will be injected here -->
                        </div>
                        <button id="arReviewsShowAllBtn" class="btn-outline" style="margin-top:24px; display:none;"
                            onclick="document.getElementById('arReviewsModal').style.display='flex'">Show all reviews</button>
                    </div>


                </div>

                <!-- Sticky Booking Card -->
                <div class="booking-sidebar">
                    <div class="booking-card">
                        <!-- Title -->
                        <h3 style="font-size: 18px; font-weight: 600; margin: 0 0 20px 0; color: var(--text-color);">Add
                            dates for prices</h3>

                        <!-- Dates Row (Side by Side) -->
                        <div
                            style="display: grid; grid-template-columns: 1fr 1fr; gap: 0; border: 1px solid var(--border-color); border-radius: 12px; margin-bottom: 12px; overflow: hidden;">
                            <div class="b-date-input editable-date" id="checkInInput"
                                style="padding: 12px 16px; cursor: pointer; border-right: 1px solid var(--border-color); border-radius: 0;">
                                <div
                                    style="font-size: 10px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px; color: var(--text-color); opacity: 0.7;">
                                    CHECK-IN</div>
                                <div id="arCheckIn" style="font-size: 14px; color: var(--text-color); opacity: 0.5;">Add
                                    date</div>
                            </div>
                            <div class="b-date-input editable-date" id="checkOutInput"
                                style="padding: 12px 16px; cursor: pointer; border-radius: 0;">
                                <div
                                    style="font-size: 10px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px; color: var(--text-color); opacity: 0.7;">
                                    CHECKOUT</div>
                                <div id="arCheckOut" style="font-size: 14px; color: var(--text-color); opacity: 0.5;">
                                    Add date</div>
                            </div>
                        </div>

                        <!-- Booked Dates Notice -->
                        <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--text-color); opacity: 0.85; margin: 12px 0 16px 4px;">
                            <i class="fas fa-info-circle" style="color: var(--pink-accent);"></i>
                            <span>Dates marked in grey are already booked</span>
                        </div>

                        <!-- Guests Row -->
                        <div id="guestsDropdownTrigger"
                            style="border: 1px solid var(--border-color); border-radius: 12px; padding: 12px 16px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                            <div>
                                <div
                                    style="font-size: 10px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px; color: var(--text-color); opacity: 0.7;">
                                    GUESTS</div>
                                <div id="arGuestCountPreview" style="font-size: 14px; color: var(--text-color);">1 guest
                                </div>
                            </div>
                            <i class="fas fa-chevron-down"
                                style="font-size: 12px; color: var(--text-color); opacity: 0.7;"></i>
                        </div>

                        <!-- Guest Counter Dropdown (Hidden by default) -->
                        <div id="guestCounterDropdown"
                            style="display: none; padding: 20px; border: 1px solid var(--border-color); border-radius: 12px; margin-bottom: 16px; background: var(--menu-bg);">
                            <div
                                style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                                <div>
                                    <div style="font-weight: 600; margin-bottom: 4px;">Adults</div>
                                    <div style="font-size: 13px; color: #717171;">Age 13+</div>
                                </div>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <button onclick="updateGuestCount('adults', -1)"
                                        style="width: 32px; height: 32px; border-radius: 50%; border: 1px solid var(--border-color); background: transparent; cursor: pointer; font-size: 18px; color: var(--text-color);">−</button>
                                    <span id="adultsCount"
                                        style="min-width: 20px; text-align: center; font-weight: 600; color: var(--text-color);">1</span>
                                    <button onclick="updateGuestCount('adults', 1)"
                                        style="width: 32px; height: 32px; border-radius: 50%; border: 1px solid var(--border-color); background: transparent; cursor: pointer; font-size: 18px; color: var(--text-color);">+</button>
                                </div>
                            </div>
                            <div
                                style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                                <div>
                                    <div style="font-weight: 600; margin-bottom: 4px;">Children</div>
                                    <div style="font-size: 13px; color: #717171;">Ages 2-12</div>
                                </div>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <button onclick="updateGuestCount('children', -1)"
                                        style="width: 32px; height: 32px; border-radius: 50%; border: 1px solid var(--border-color); background: transparent; cursor: pointer; font-size: 18px; color: var(--text-color);">−</button>
                                    <span id="childrenCount"
                                        style="min-width: 20px; text-align: center; font-weight: 600; color: var(--text-color);">0</span>
                                    <button onclick="updateGuestCount('children', 1)"
                                        style="width: 32px; height: 32px; border-radius: 50%; border: 1px solid var(--border-color); background: transparent; cursor: pointer; font-size: 18px; color: var(--text-color);">+</button>
                                </div>
                            </div>
                        </div>

                        <!-- Check Availability Button -->
                        <button class="reserve-btn-new" id="btnReserve" onclick="initiateBooking()"
                            style="width: 100%; padding: 16px; background: linear-gradient(135deg, #e91e63 0%, #d81b60 100%); color: white; border: none; border-radius: 12px; font-size: 16px; font-weight: 700; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(233, 30, 99, 0.3); margin-top: 20px;">
                            Check availability
                        </button>

                        <!-- Price Details Section -->
                        <div id="priceDetailsSection"
                            style="display: none; margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border-color);">
                            <div
                                style="margin-bottom: 12px; display: flex; justify-content: space-between; font-size: 14px;">
                                <span id="arCalcLabel">14,000 DZD × 1 night</span>
                                <span id="arCalcSubtotal">14,000 DZD</span>
                            </div>
                            <div
                                style="margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; font-weight: 700; font-size: 18px; color: var(--text-color);">
                                <span>Total</span>
                                <span id="arCalcTotal">17,500 DZD</span>
                            </div>
                        </div>


                        <!-- Report listing link -->

                        <!-- Report listing link -->
                        <div style="margin-top:20px; text-align:center; opacity:0.7; font-size:14px;">
                            <i class="fas fa-flag"></i> Report this listing
                        </div>
                    </div>
                </div> <!-- End booking-sidebar -->
            </div> <!-- End modal-body -->

            <!-- قسم خريطة موقع العقار التفاعلية (Map Section) -->
            <!-- Map Section Full Width -->
            <div style="padding: 0 40px; margin-bottom: 40px;">
                <div class="location-section" style="padding: 32px 0; border-top: 1px solid var(--border-color);">
                    <h2 style="font-size: 20px; font-weight: 600; margin-bottom: 8px;">Where you’ll be</h2>
                    <p style="opacity:0.7; font-size:14px; margin-bottom:14px;"><span id="arCityMap">Algiers</span>, Algeria &mdash; Exact location provided after booking.</p>
                    <div id="detailMap" style="width:100%; height:250px; border-radius:12px; position:relative; overflow:hidden; border: 1px solid var(--border-color); z-index: 1;"></div>
                </div>
            </div> <!-- End Map Section -->

            <!-- بطاقة معلومات المضيف وتاريخ انضمامه والتعريف به (Host Info Section) -->
            <!-- Unified Host Information Card -->
            <div class="host-info-section" style="padding: 64px 40px; border-top: 1px solid var(--border-color); max-width: 1400px; margin: 0 auto;">
                <h2 style="font-size: 32px; font-weight: 700; margin-bottom: 48px; text-align: center; justify-content: center;">Meet your Host</h2>

                <div class="host-profile-card"
                    style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 24px; padding: 48px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); max-width: 1100px; margin: 0 auto;">
                    
                    <div style="display: grid; grid-template-columns: 500px 1fr; gap: 48px; align-items: start;">
                        
                        <!-- Left Part: Photo & Name (Significantly Wider) -->
                        <div style="text-align: center; border-right: 1px solid rgba(255,255,255,0.1); padding-right: 48px;">
                            <img id="arHostImgLarge" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23666'%3E%3Cpath d='M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z'/%3E%3C/svg%3E"
                                style="width: 180px; height: 180px; border-radius: 50%; object-fit: cover; border: 3px solid var(--primary-color); display: block; margin: 0 auto 24px;">
                            <h3 style="font-size: 40px; font-weight: 700; margin-bottom: 4px; justify-content: center;" id="arHostNameLarge"></h3>
                            <p style="font-size: 22px; opacity: 0.7;">Host</p>
                        </div>

                        <!-- Right Part: Details Stack (Vertical with Space) -->
                        <div style="display: flex; flex-direction: column; gap: 48px;">
                            <!-- Highlights List (No Lines, More Space) -->
                            <div style="display: flex; flex-direction: column; gap: 40px; padding-bottom: 8px;">
                                <!-- Profession -->
                                <div id="arHostProfessionRow" style="display: flex; gap: 20px; align-items: center;">
                                    <i class="fas fa-briefcase" style="font-size: 28px; color: var(--primary-color);"></i>
                                    <div style="display: flex; flex-direction: column;">
                                        <strong style="font-size: 22px;">My profession</strong>
                                        <span id="arHostProfession" style="font-size: 19px; opacity: 0.8; margin-top: 4px;"></span>
                                    </div>
                                </div>
                                <!-- Languages -->
                                <div id="arHostLanguagesRow" style="display: flex; gap: 20px; align-items: center;">
                                    <i class="fas fa-globe-americas" style="font-size: 28px; color: var(--primary-color);"></i>
                                    <div style="display: flex; flex-direction: column;">
                                        <strong style="font-size: 22px;">Languages I speak</strong>
                                        <span id="arHostLanguages" style="font-size: 19px; opacity: 0.8; margin-top: 4px;"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Biography (No Top Line, Larger Text) -->
                            <div style="padding-top: 8px;">
                                <div style="display: flex; gap: 20px; align-items: center; margin-bottom: 16px;">
                                    <i class="fas fa-user-circle" style="font-size: 28px; color: var(--primary-color);"></i>
                                    <strong style="font-size: 22px;">About the host</strong>
                                </div>
                                <p style="opacity: 0.9; line-height: 1.8; font-size: 19px; color: #ffffff; margin: 0; padding-left: 48px;" id="arHostBio"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Footer: Verification & Button -->
                    <div style="text-align: center; margin-top: 48px; padding-top: 40px; border-top: 1px solid rgba(255,255,255,0.1);">
                        <div style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 24px;">
                            <i class="fas fa-shield-alt" style="color: var(--primary-color); font-size: 20px;"></i>
                            <span style="font-size: 18px; font-weight: 700;">Identity Verified</span>
                        </div>
                        


                        <button type="button" id="arContactHostBtnNew" onclick="toggleContactHostForm(event)"
                            style="background: var(--primary-color); border: none; color: #ffffff; padding: 18px 80px; border-radius: 12px; font-size: 20px; font-weight: 700; cursor: pointer; transition: all 0.3s ease; width: 100%; max-width: 400px; box-shadow: 0 8px 20px rgba(0,0,0,0.3); display: inline-flex; align-items: center; justify-content: center; gap: 12px;">
                            <i class="far fa-envelope"></i> Contact Host
                        </button>

                        <!-- نموذج إرسال رسالة مباشرة إلى المضيف (Contact Host Form) -->
                        <!-- New Contact Host Form (Hidden by default) -->
                        <div id="arContactHostForm" style="display: none;">
                            <div class="contact-form-container">
                                <div class="contact-form-title">
                                    <i class="fas fa-paper-plane" style="color: var(--primary-color);"></i>
                                    <span>Send a message to the host</span>
                                </div>
                                <div class="quick-replies-container">
                                    <button type="button" class="quick-reply-btn" onclick="insertQuickReply('Greetings! Your beautiful property caught my eye. I am planning a trip and would love to introduce myself to discuss a potential booking.')">
                                        <i class="fas fa-hand-sparkles" style="margin-right: 5px;"></i> Express Interest
                                    </button>
                                </div>
                                <textarea id="arContactHostMessage" class="contact-textarea" placeholder="Hi! I have a question about your place..."></textarea>
                                <button type="button" id="arSendHostMessageBtn" class="contact-send-btn" onclick="sendHostMessage(event)">
                                    <span>Send Message</span>
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div style="padding: 0 40px; margin-bottom: 48px;">
                <div
                    style="padding: 24px; background: rgba(225, 157, 164, 0.05); border-radius: 16px; border: 1px solid var(--border-color); display: flex; align-items: center; gap: 24px;">
                    <i class="fas fa-shield-alt" style="font-size: 32px; color: var(--primary-color);"></i>
                    <div>
                        <h4 style="font-size: 18px; font-weight: 600; margin-bottom: 8px;">WINSTAY Payment Protection
                        </h4>
                        <p style="font-size: 14px; opacity: 0.8; margin: 0;">To protect your payment, never transfer
                            money outside of WINSTAY.</p>
                    </div>
                </div>
            </div>







        </div> <!-- End modal-content-wrapper -->
    </div> <!-- End arPropertyModal -->


    <!-- النوافذ الفرعية الإضافية المنبثقة (Sub-Modals) -->
    <!-- Sub-Modals (Moved to Body Root for proper centering) -->

    <!-- نافذة عرض جميع ميزات العقار (Amenities Sub-Modal) -->
    <div id="arAmenitiesModal" class="sub-modal"
        style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; padding:20px; background:rgba(0,0,0,0.6); z-index:20000; align-items:center; justify-content:center; direction:ltr; backdrop-filter: blur(4px);">
        <div
            style="background:var(--bg-color); width:95%; max-width:780px; max-height:85vh; overflow-y:auto; border-radius:12px; padding:32px; box-shadow:0 8px 28px rgba(0,0,0,0.28); position:relative;">
            <!-- Content injected via JS -->
        </div>

    </div>

    <!-- نافذة عرض جميع تقييمات العقار (Reviews Sub-Modal) -->
    <!-- Reviews Sub-Modal -->
    <div id="arReviewsModal" class="sub-modal"
        style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; padding:20px; background:rgba(0,0,0,0.6); z-index:20000; align-items:center; justify-content:center; direction:ltr; backdrop-filter: blur(4px);">
        <div
            style="background:var(--bg-color); width:95%; max-width:780px; max-height:85vh; overflow-y:auto; border-radius:12px; padding:32px; box-shadow:0 8px 28px rgba(0,0,0,0.28); position:relative;">
            <!-- Header -->
            <div
                style="display:flex; justify-content:space-between; align-items:center; margin-bottom:40px; padding-bottom:24px; border-bottom:1px solid var(--border-color);">
                <h2 style="font-size:28px; font-weight:700; margin:0; color:var(--text-color);" id="arReviewsHeader">
                    Reviews</h2>
                <button onclick="document.getElementById('arReviewsModal').style.display='none'"
                    style="background:none; border:none; font-size:28px; cursor:pointer; color:var(--text-color); padding:8px; border-radius:50%; transition:0.2s;">✕</button>
            </div>

            <!-- Reviews Grid -->
            <div id="arReviewsModalGrid" style="display:grid; grid-template-columns:1fr 1fr; gap:40px;">
                <!-- Modal reviews injected here -->
            </div>
        </div>
    </div>

    <!-- نافذة الترحيب بالزائر عند أول زيارة للموقع (Welcome Modal) -->
    <!-- Welcome Modal -->
    <div id="welcomeModalOverlay" class="sub-modal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; padding:20px; background:rgba(0,0,0,0.7); z-index:99999; align-items:center; justify-content:center; backdrop-filter: blur(8px); opacity: 0; transition: opacity 0.5s ease;">
        <div style="background:var(--menu-bg); width:100%; max-width:500px; border-radius:24px; padding:40px; text-align:center; box-shadow:var(--shadow-premium); border:1px solid var(--border-color); transform: translateY(20px); transition: transform 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
            <div style="font-size: 50px; margin-bottom: 20px;">✨</div>
            <h2 style="font-size: 32px; font-weight: 700; color: var(--text-color); margin-bottom: 15px; font-family: 'Cormorant Garamond', serif;">Welcome to WINSTAY</h2>
            <p style="color: rgba(255,255,255,0.7); font-size: 16px; line-height: 1.6; margin-bottom: 20px;">
                Discover the perfect place to stay. Whether you're looking for a cozy apartment in the city or a luxurious villa by the sea, we have the best properties waiting for you.
            </p>
            <div style="background: rgba(0, 224, 198, 0.1); border: 1px solid var(--pink-accent); padding: 12px; border-radius: 8px; margin-bottom: 30px; display: inline-flex; align-items: center; gap: 10px; color: var(--pink-accent); font-weight: 600; font-size: 14px; text-align: left;">
                <i class="fas fa-calendar-alt" style="font-size: 18px;"></i>
                <span>Please note: All reservations must be made at least 3 days in advance.</span>
            </div>
            <button class="save-btn" style="width: 100%; padding: 16px; font-size: 16px; font-weight: 600; border-radius: 12px; letter-spacing: 1px; text-transform: uppercase;" onclick="closeWelcomeModal()">Explore Now</button>
        </div>
    </div>

    <!-- نافذة معرض الصور الكامل للعقار (Full Gallery Modal) -->
    <!-- Full Gallery Modal -->
    <div id="arGalleryModal" style="display:none; position:fixed; inset:0; background:var(--bg-color); z-index:9000; overflow-y:auto; padding:40px 0;">
        <div style="position:fixed; top:20px; left:20px; z-index:9001;">
            <button onclick="closeGalleryModal()" style="background:rgba(0,0,0,0.5); border:1px solid rgba(255,255,255,0.2); color:white; padding:10px 20px; border-radius:30px; cursor:pointer; display:flex; align-items:center; gap:10px; font-weight:600; backdrop-filter:blur(10px);">
                <i class="fas fa-chevron-left"></i> Back
            </button>
        </div>
        <div style="max-width:800px; margin:0 auto; padding:40px 20px;">
            <h2 style="font-family:'Cormorant Garamond', serif; font-size:32px; margin-bottom:30px; color:var(--pink-accent);">All Photos</h2>
            <div id="galleryFullGrid" style="display:grid; grid-template-columns:1fr; gap:16px;">
                <!-- Images injected via JS -->
            </div>
        </div>
    </div>

    <!-- Note: confirmModal & overlay are defined once above (lines 31 and 92) -->

        <script src="js/api.js?v=<?php echo time(); ?>"></script>
        <script src="js/script.js?v=<?php echo time(); ?>"></script>
</body>
</html>