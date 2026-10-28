AOS.init({ duration: 800, once: true });

// Data
const highlightsMap = {
    quiet: { label: "Peace and quiet", icon: "fas fa-volume-mute", desc: "Guests say this home is located in a quiet area." },
    family: { label: "Family-friendly", icon: "fas fa-baby", desc: "This place is perfect for families with children." },
    distinctive: { label: "Distinctive", icon: "fas fa-lighthouse", desc: "This property is one of a kind and stands out from the rest." },
    elegant: { label: "Elegant", icon: "fas fa-couch", desc: "Professionally designed with attention to every detail." },
    spacious: { label: "Spacious", icon: "fas fa-arrows-alt-h", desc: "Plenty of room for everyone to relax comfortably." },
    central: { label: "Central", icon: "fas fa-map-marker-alt", desc: "Located in the heart of the city, close to everything." }
};




const AMENITIES_MASTER_LIST = [
    { key: 'wifi', label: 'Wifi', icon: 'fas fa-wifi', category: 'Basics' },
    { key: 'tv', label: 'TV / Netflix', icon: 'fas fa-tv', category: 'Basics' },
    { key: 'workspace', label: 'Workspace', icon: 'fas fa-laptop', category: 'Basics' },
    { key: 'self-checkin', label: 'Self check-in', icon: 'fas fa-key', category: 'Basics' },
    { key: 'ac', label: 'Air conditioning', icon: 'fas fa-snowflake', category: 'Basics' },
    { key: 'heating', label: 'Heating', icon: 'fas fa-fire-alt', category: 'Basics' },

    { key: 'shampoo', label: 'Shampoo', icon: 'fas fa-pump-soap', category: 'Bathroom & Bedroom' },
    { key: 'hair-dryer', label: 'Hair dryer', icon: 'fas fa-wind', category: 'Bathroom & Bedroom' },
    { key: 'linens', label: 'Bed linens', icon: 'fas fa-bed', category: 'Bathroom & Bedroom' },
    { key: 'pillows', label: 'Extra pillows', icon: 'fas fa-couch', category: 'Bathroom & Bedroom' },

    { key: 'washer', label: 'Washer', icon: 'fas fa-soap', category: 'Laundry' },
    { key: 'dryer', label: 'Dryer', icon: 'fas fa-tshirt', category: 'Laundry' },
    { key: 'iron', label: 'Iron', icon: 'fas fa-hand-holding-heart', category: 'Laundry' },
    { key: 'hangers', label: 'Hangers', icon: 'fas fa-tshirt', category: 'Laundry' },

    { key: 'kitchen', label: 'Kitchen', icon: 'fas fa-utensils', category: 'Kitchen & Dining' },
    { key: 'refrigerator', label: 'Refrigerator', icon: 'fas fa-temperature-low', category: 'Kitchen & Dining' },
    { key: 'microwave', label: 'Microwave', icon: 'fas fa-bolt', category: 'Kitchen & Dining' },
    { key: 'stove', label: 'Stove', icon: 'fas fa-fire-burner', category: 'Kitchen & Dining' },
    { key: 'coffee', label: 'Coffee maker', icon: 'fas fa-coffee', category: 'Kitchen & Dining' },
    { key: 'dishes', label: 'Dishes', icon: 'fas fa-glass-martini', category: 'Kitchen & Dining' },

    { key: 'parking', label: 'Free parking', icon: 'fas fa-car', category: 'Location & Outdoor' },
    { key: 'garden', label: 'Garden', icon: 'fas fa-tree', category: 'Location & Outdoor' },
    { key: 'patio', label: 'Patio / Balcony', icon: 'fas fa-columns', category: 'Location & Outdoor' },

    { key: 'smoke-alarm', label: 'Smoke alarm', icon: 'fas fa-bell', category: 'Safety' },
    { key: 'co-alarm', label: 'CO alarm', icon: 'fas fa-biohazard', category: 'Safety' },
];

const wilayas = [
    "Adrar", "Chlef", "Laghouat", "Oum El Bouaghi", "Batna", "Béjaïa", "Biskra", "Béchar", "Blida", "Bouira",
    "Tamanrasset", "Tébessa", "Tlemcen", "Tiaret", "Tizi Ouzou", "Algiers", "Djelfa", "Jijel", "Sétif", "Saïda",
    "Skikda", "Sidi Bel Abbès", "Annaba", "Guelma", "Constantine", "Médéa", "Mostaganem", "M'Sila", "Mascara", "Ouargla",
    "Oran", "El Bayadh", "Illizi", "Bordj Bou Arréridj", "Boumerdès", "El Tarf", "Tindouf", "Tissemsilt", "El Oued", "Khenchela",
    "Souk Ahras", "Tipaza", "Mila", "Aïn Defla", "Naâma", "Aïn Témouchent", "Ghardaïa", "Relizane", "El M'Ghair", "El Meniaa",
    "Ouled Djellal", "Bordj Baji Mokhtar", "Béni Abbès", "Timimoun", "Touggourt", "Djanet", "In Salah", "In Guezzam",
    "Aflou", "Barika", "Ksar Chellala", "Messaad", "Aïn Oussera", "Boussaâda", "El Abiodh Sidi Cheikh", "El Kantara", "Bir El Ater", "Ksar El Boukhari", "El Aricha"
];

const defaultStays = [
    // Static data removed as per user request to use database only
];

let mockStays = []; // JSON.parse(localStorage.getItem('winstay_data')) || defaultStays;

// Default Placeholders
const DEFAULT_HOUSE_IMAGE = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23eee'%3E%3Crect width='24' height='24' fill='%23ccc'/%3E%3Cpath d='M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z' fill='%23999'/%3E%3C/svg%3E";

function getImageSrc(src) {
    if (!src) return DEFAULT_HOUSE_IMAGE;
    // If it's a local upload (starts with 'uploads/'), prepend base path
    if (src && !src.startsWith('http') && !src.startsWith('data:')) {
        return `${window.location.origin}/memoire/${src}`;
    }
    return src;
}

function getInitialsAvatar(name) {
    if (!name) name = "User";
    const initial = name.charAt(0).toUpperCase();
    const colors = [
        '#00a8b5', // Teal
        '#e91e63', // Pink
        '#9c27b0', // Purple
        '#673ab7', // Deep Purple
        '#3f51b5', // Indigo
        '#2196f3', // Blue
        '#009688', // Teal Green
        '#4caf50', // Green
        '#ff9800', // Orange
        '#f44336'  // Red
    ];
    // Simple hash for consistent color per name
    let hash = 0;
    for (let i = 0; i < name.length; i++) {
        hash = name.charCodeAt(i) + ((hash << 5) - hash);
    }
    const color = colors[Math.abs(hash) % colors.length];

    return `data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect width='100' height='100' fill='${encodeURIComponent(color)}'/%3E%3Ctext x='50%25' y='50%25' dominant-baseline='central' text-anchor='middle' fill='white' font-family='Montserrat, sans-serif' font-size='50' font-weight='700'%3E${initial}%3C/text%3E%3C/svg%3E`;
}

function getAvatarColor(name) {
    if (!name) return '#00a8b5';
    const colors = ['#00a8b5', '#e91e63', '#9c27b0', '#673ab7', '#3f51b5', '#2196f3', '#009688', '#4caf50', '#ff9800', '#f44336'];
    let hash = 0;
    for (let i = 0; i < name.length; i++) hash = name.charCodeAt(i) + ((hash << 5) - hash);
    return colors[Math.abs(hash) % colors.length];
}

// Welcome Modal Logic
if (!sessionStorage.getItem('winstay_welcome_shown')) {
    setTimeout(() => {
        const welcomeOverlay = document.getElementById('welcomeModalOverlay');
        if (welcomeOverlay) {
            welcomeOverlay.style.display = 'flex';
            // Trigger reflow for transition
            welcomeOverlay.offsetHeight;
            welcomeOverlay.style.opacity = '1';
            const modalBox = welcomeOverlay.querySelector('div');
            if (modalBox) {
                modalBox.style.transform = 'translateY(0)';
            }
            document.body.style.overflow = 'hidden';
        }
    }, 1500); // Show after 1.5 seconds
}

function closeWelcomeModal() {
    const welcomeOverlay = document.getElementById('welcomeModalOverlay');
    if (welcomeOverlay) {
        welcomeOverlay.style.opacity = '0';
        const modalBox = welcomeOverlay.querySelector('div');
        if (modalBox) {
            modalBox.style.transform = 'translateY(20px)';
        }
        setTimeout(() => {
            welcomeOverlay.style.display = 'none';
            document.body.style.overflow = 'auto';
        }, 500); // wait for fade out
        sessionStorage.setItem('winstay_welcome_shown', 'true');
    }
}

// State
let guests = { adults: 0, children: 0 };
let currentView = 'Home';
let editingId = null;
let deleteTargetId = null;
let isLoggedIn = false; // Auth State
let currentUserData = null; // Store user details (roles, statuses)
let isSignUpMode = true; // State tracker for modal
let pendingAction = null; // To remember what user wanted to do
// Hosting photo state for Add/Edit home
let hostPhotoData = []; // array of {src: dataURL, fileName}
let deletedPhotoIds = []; // array of image IDs deleted during editing
// Hosting Map state
let hostMapInstance = null;
let hostMarkerInstance = null;
// Detail Map state
let detailMapInstance = null;
let detailMarkerInstance = null;
// Hosting Basics State
let hostBasics = { guests: 4, bedrooms: 2, beds: 2, baths: 1 };
// Hosting Amenities State
let hostAmenities = [];
// Hosting Highlights State
let hostHighlights = [];

// Draft State for Search (prevents live updates to booking context)
let draftDates = { checkIn: null, checkOut: null };

// Booking Search Data - for transferring search info to booking page
let bookingSearchData = {
    checkIn: null,
    checkOut: null,
    guests: 1,
    guestCounts: {
        adults: 0,
        children: 0,
        infants: 0
    },
    location: ''
};
let currentPropertyPrice = 0; // Store current property price for calculations

// Clear all search and reservation dates when switching accounts or logging out
function clearBookingSearchDates() {
    bookingSearchData.checkIn = null;
    bookingSearchData.checkOut = null;
    bookingSearchData.guests = 1;
    bookingSearchData.guestCounts = { adults: 0, children: 0, infants: 0 };
    bookingSearchData.location = '';
    
    draftDates.checkIn = null;
    draftDates.checkOut = null;
    
    if (typeof checkInPicker !== 'undefined' && checkInPicker) {
        try { checkInPicker.clear(); } catch (e) {}
    }
    if (typeof checkOutPicker !== 'undefined' && checkOutPicker) {
        try { checkOutPicker.clear(); } catch (e) {}
    }
    
    const agendaEl = document.getElementById('agendaInput');
    if (agendaEl && agendaEl._flatpickr) {
        try { agendaEl._flatpickr.clear(); } catch (e) {}
    }
    
    const dateInput = document.getElementById('dateInput');
    if (dateInput) dateInput.value = '';
    
    const checkInInput = document.getElementById('checkInInput');
    if (checkInInput) checkInInput.value = '';
    
    const checkOutInput = document.getElementById('checkOutInput');
    if (checkOutInput) checkOutInput.value = '';
    
    const arCheckIn = document.getElementById('arCheckIn');
    if (arCheckIn) {
        arCheckIn.innerText = 'Add date';
        arCheckIn.style.opacity = '0.5';
    }
    const arCheckOut = document.getElementById('arCheckOut');
    if (arCheckOut) {
        arCheckOut.innerText = 'Add date';
        arCheckOut.style.opacity = '0.5';
    }
    
    const priceSection = document.getElementById('priceDetailsSection');
    if (priceSection) priceSection.style.display = 'none';
    
    if (typeof updateBookingCalculation === 'function') {
        try { updateBookingCalculation(); } catch (e) {}
    }
}

let pendingVerificationEmail = ''; // Store email for OTP verification

// OTP Input handling
document.addEventListener('input', (e) => {
    if (e.target.classList.contains('otp-box') || e.target.classList.contains('otp-box-reset')) {
        const val = e.target.value;
        if (val && e.target.nextElementSibling) {
            e.target.nextElementSibling.focus();
        }
    }
});

document.addEventListener('keydown', (e) => {
    if ((e.target.classList.contains('otp-box') || e.target.classList.contains('otp-box-reset')) && e.key === 'Backspace') {
        if (!e.target.value && e.target.previousElementSibling) {
            e.target.previousElementSibling.focus();
        }
    }
});

// Real-time Password Confirmation Validation
document.addEventListener('input', (e) => {
    const id = e.target.id;
    if (id === 'confirmPassword' || id === 'loginPassword') {
        if (isSignUpMode) {
            const pass = document.getElementById('loginPassword').value;
            const confirm = document.getElementById('confirmPassword').value;
            const errorBox = document.getElementById('loginErrorMsg');
            if (confirm && pass !== confirm) {
                if (errorBox) {
                    errorBox.querySelector('span').innerText = 'Passwords do not match';
                    errorBox.style.display = 'block';
                }
            } else if (errorBox) {
                errorBox.style.display = 'none';
            }
        }
    }
    
    if (id === 'regConfirm' || id === 'regPass') {
        if (isSignUpModeAuth) {
            const pass = document.getElementById('regPass').value;
            const confirm = document.getElementById('regConfirm').value;
            const errorBox = document.getElementById('authErrorMsg');
            if (confirm && pass !== confirm) {
                if (errorBox) {
                    errorBox.querySelector('span').innerText = 'Passwords do not match';
                    errorBox.style.display = 'block';
                }
            } else if (errorBox) {
                errorBox.style.display = 'none';
            }
        }
    }

    if (id === 'inputConfirmPass' || id === 'inputNewPass') {
        const pass = document.getElementById('inputNewPass').value;
        const confirm = document.getElementById('inputConfirmPass').value;
        if (confirm && pass !== confirm) {
            // For profile security, we don't have a dedicated error box in HTML yet, 
            // but we can use the toast or add one. 
            // Let's just focus on the signup modals as requested.
        }
    }
});



let otpTimerInterval;
function startOTPTimer(displayId) {
    let duration = 5 * 60; // 5 minutes
    const display = document.getElementById(displayId);
    if (!display) return;

    clearInterval(otpTimerInterval);
    otpTimerInterval = setInterval(() => {
        let minutes = Math.floor(duration / 60);
        let seconds = duration % 60;

        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;

        display.textContent = minutes + ":" + seconds;

        if (--duration < 0) {
            clearInterval(otpTimerInterval);
            display.textContent = "00:00";
        }
    }, 1000);
}

async function resendOTP() {
    if (!pendingVerificationEmail) {
        showError('Email is missing. Please restart the process.');
        return;
    }

    try {
        showLoading('Resending code...');
        // We can reuse forgotPassword for resending in both cases if logic allows
        // or create a generic resend. Let's just call forgotPassword or register.
        // For simplicity, let's assume it's for the current pending email.
        const response = await API.auth.resendOTP(pendingVerificationEmail);
        hideLoading();

        if (response.success) {
            showSuccess('New code sent to your email!');
            startOTPTimer('otpTimer');
        }
    } catch (error) {
        hideLoading();
        showError(error.message || 'Failed to resend code');
    }
}

async function requestPasswordReset() {
    const email = document.getElementById('recoveryEmail')?.value || '';
    if (!email) { showError('Please enter your email'); return; }

    try {
        showLoading('Sending reset code...');
        const response = await API.auth.forgotPassword(email);
        hideLoading();

        if (response.success) {
            pendingVerificationEmail = email;
            pendingActionType = 'reset';
            showAuthView('verification');
            showSuccess('Reset code sent! Check your email.');
            startOTPTimer('otpTimer');
        }
    } catch (error) {
        hideLoading();
        showError(error.message || 'Failed to send reset code');
    }
}

async function verifyOTP() {
    const boxes = document.querySelectorAll('.otp-box');
    let otp = '';
    boxes.forEach(b => otp += b.value);

    if (otp.length < 6) {
        showError('Please enter the full 6-digit code');
        return;
    }

    pendingOTP = otp;

    if (pendingActionType === 'reset') {
        // Just move to the next view, we will verify during the final reset call
        // or we can verify now to ensure the OTP is correct.
        // Let's verify now for better UX.
        try {
            showLoading('Verifying code...');
            // We use the same verify endpoint
            const response = await API.auth.verifyOTP(pendingVerificationEmail, otp);
            hideLoading();
            if (response.success) {
                // Since verifyOTP in backend moves user to main table, 
                // for reset it might be different. 
                // Let's create a specialized verify_reset_otp.php if needed, 
                // or just handle it in resetPassword.
                // For now, let's just show the reset view.
                showAuthView('reset');
            }
        } catch (error) {
            hideLoading();
            showError(error.message || 'Invalid code');
        }
        return;
    }

    try {
        showLoading('Verifying code...');
        const response = await API.auth.verifyOTP(pendingVerificationEmail, otp);
        hideLoading();

        if (response.success) {
            isLoggedIn = true;
            currentUserData = response.data;
            clearBookingSearchDates();
            showSuccess('Account verified successfully!');
            closeAllPopovers();
            updateUserUI();
            updateSidebarProfileUI(response.data);
            await loadProperties();
        }
    } catch (error) {
        hideLoading();
        showError(error.message || 'Verification failed');
    }
}

async function resetPassword() {
    const newPass = document.getElementById('newPass')?.value || '';
    const confirmPass = document.getElementById('newPassConfirm')?.value || '';

    if (!newPass || newPass.length < 6) { showError('Password must be at least 6 characters'); return; }
    if (newPass !== confirmPass) { showError('Passwords do not match'); return; }

    try {
        showLoading('Updating password...');
        const response = await API.auth.resetPassword(pendingVerificationEmail, pendingOTP, newPass);
        hideLoading();

        if (response.success) {
            showSuccess('Password updated successfully! Please log in.');
            showAuthView('main');
        }
    } catch (error) {
        hideLoading();
        showError(error.message || 'Failed to update password');
    }
}



// initialize list on view show
window.onload = async function () {
    renderGrid(mockStays); // Show mockStays initially as fallback
    initWilayaList();

    // Check if user is logged in
    await checkSession();

    // Load properties from backend
    await loadProperties();

    flatpickr("#agendaInput", {
        mode: "range",
        inline: true,
        minDate: "today",
        dateFormat: "Y-m-d",
        onChange: function (selectedDates, dateStr) {
            if (selectedDates.length === 2) {
                // Store in draftDates only
                const formatDate = (date) => {
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    return `${year}/${month}/${day}`;
                };
                draftDates.checkIn = formatDate(selectedDates[0]);
                draftDates.checkOut = formatDate(selectedDates[1]);
            }
            document.getElementById('dateInput').value = dateStr;
        }
    });
};

// Check user session
async function checkSession() {
    try {
        const response = await API.auth.checkSession();
        if (response.success && response.data) {
            isLoggedIn = true;
            currentUserData = response.data;
            const userData = response.data;

            // Update UI
            const userIcon = document.getElementById('userIcon');
            if (userIcon) {
                userIcon.classList.remove('fa-user-circle');
                userIcon.classList.add('fa-check-circle');
                userIcon.style.color = '#00a8b5';
            }

            updateSidebarProfileUI(userData);
        }
    } catch (error) {
        // User not logged in - that's okay
        isLoggedIn = false;
    }
}

// Load properties from API
async function loadProperties(filters = {}) {
    try {
        const response = await API.properties.list(filters);
        if (response.success && response.data && response.data.properties) {
            // Convert API data to our mockStays format for compatibility
            mockStays = response.data.properties.map(prop => ({
                id: prop.id,
                city: prop.wilaya,
                price: parseFloat(prop.prix_par_nuit).toLocaleString('fr-DZ'),
                rating: prop.moyenne_note || 0,
                img: getImageSrc(prop.image_principale),
                fav: prop.is_favorited || false,
                desc: prop.titre || prop.description?.substring(0, 50) || 'Beautiful property',
                isMine: prop.is_mine || false,
                // Map API espaces to amenities array of strings
                amenities: prop.espaces ? prop.espaces.map(e => e.type_espace) : [],
                // Booked status from API
                isBooked: !!parseInt(prop.is_booked),
                bookedUntil: prop.booked_until || null,
                // Whether the current user has an active booking on this property
                isMyBooking: !!parseInt(prop.is_my_booking),
                // Store all data for later use
                fullData: prop
            }));

            renderGrid(mockStays);
        }
    } catch (error) {
        console.error('Failed to load properties:', error);
        // Keep using mockStays as fallback
    }
}

// Navbar Scroll Effect
window.addEventListener('scroll', () => {
    const nav = document.getElementById('nav');
    if (window.scrollY > 50) nav.classList.add('scrolled');
    else nav.classList.remove('scrolled');
});

// Theme Toggle
function toggleTheme() {
    const body = document.body;
    const current = body.getAttribute('data-theme');
    body.setAttribute('data-theme', current === 'light' ? 'dark' : 'light');
}


// Grid Rendering
function renderGrid(data) {
    const grid = document.getElementById('mainGrid');
    grid.innerHTML = '';

    data.forEach(item => {
        const card = document.createElement('div');
        card.className = 'card';
        card.innerHTML = `
                <button class="fav-btn ${item.fav ? 'active' : ''}" onclick="toggleFavorite(${item.id}, event)">
                    <i class="fas fa-heart"></i>
                </button>
                <div class="card-img-wrapper">
                    <img src="${item.img}" alt="${item.city}" loading="lazy">
                </div>
                <div class="card-info">
                    <h3>
                        ${item.city}, Algeria
                        <span class="rating"><i class="fas fa-star" style="font-size:12px"></i> ${item.rating}</span>
                    </h3>
                    <p>${item.desc}</p>
                    <div class="price-tag">${item.price} DZD <span style="font-weight:400;color:var(--text-color); font-size:14px">night</span></div>
                </div>
            `;
        grid.appendChild(card);
    });
}

// Actions
async function toggleFavorite(id, e) {
    e.stopPropagation();
    const btn = e.currentTarget;

    if (!isLoggedIn) {
        openLoginModal('login');
        return;
    }

    const item = mockStays.find(s => s.id === id);
    const wasFavorite = item.fav;

    try {
        if (wasFavorite) {
            await API.favorites.remove(id);
            item.fav = false;
            showToast('Removed from Favorites');
        } else {
            await API.favorites.add(id);
            item.fav = true;
            showToast('Saved to Favorites');
        }

        // Re-render only if we are in Favorites view, else just toggle class visually
        if (currentView === 'Favorites') {
            handleMenuClick('Favorites');
        } else {
            if (btn) btn.classList.toggle('active');
        }
    } catch (error) {
        showError(error.message || 'Failed to update favorites');
        // Revert the change
        item.fav = wasFavorite;
    }
}

// Login Functions (enhanced)
function openLoginModal(mode = 'login') {
    document.getElementById('loginModal').style.display = 'block';
    document.getElementById('overlay').style.display = 'block';
    document.getElementById('profileDropdown').classList.remove('active'); // Close menu

    // Set initial mode and update UI
    isSignUpMode = (mode === 'signup');
    updateModalUI();
}

function toggleAuthMode() {
    isSignUpMode = !isSignUpMode;
    updateModalUI();
}

function updateModalUI() {
    const title = document.getElementById('modalTitle');
    const subtext = document.getElementById('modalSubtext');
    const nameInput = document.getElementById('loginName');
    const confirmInput = document.getElementById('confirmPassword');
    const submitBtn = document.getElementById('mainSubmitBtn');
    const forgotPw = document.getElementById('forgotPassword');
    const googleText = document.getElementById('googleText');
    const fbText = document.getElementById('fbText');
    const footerText = document.getElementById('footerText');
    const switchBtn = document.getElementById('switchBtn');

    if (!title) return;
    if (isSignUpMode) {
        title.innerText = "Sign Up";
        subtext.innerText = "Join us and find your perfect match";
        nameInput.style.display = "block";
        confirmInput.style.display = "block";
        forgotPw.style.display = "none";
        submitBtn.innerText = "Sign Up";
        if (googleText) googleText.innerText = "Sign up with Google";
        if (fbText) fbText.innerText = "Sign up with Facebook";
        footerText.innerText = "Already Have an Account?";
        switchBtn.innerText = "Log in";
    } else {
        title.innerText = "Welcome Back";
        subtext.innerText = "Sign in to continue to your account";
        nameInput.style.display = "none";
        confirmInput.style.display = "none";
        forgotPw.style.display = "block";
        submitBtn.innerText = "Log in";
        if (googleText) googleText.innerText = "Continue with Google";
        if (fbText) fbText.innerText = "Continue with Facebook";
        footerText.innerText = "Don't have an account?";
        switchBtn.innerText = "Sign up";
    }
}

async function performLogin() {
    // Get form values
    const name = document.getElementById('loginName')?.value || '';
    const email = document.getElementById('loginEmail')?.value || '';
    const pass = document.getElementById('loginPassword')?.value || '';
    const errorMsgBox = document.getElementById('loginErrorMsg');

    // Hide error box initially
    if (errorMsgBox) errorMsgBox.style.display = 'none';

    const displayError = (msg) => {
        if (errorMsgBox) {
            errorMsgBox.querySelector('span').innerText = msg;
            errorMsgBox.style.display = 'block';
        } else {
            showError(msg);
        }
    };

    // Validation
    if (isSignUpMode) {
        const confirmPass = document.getElementById('confirmPassword')?.value || '';
        if (!name || !email || !pass || !confirmPass) {
            displayError('Please fill in all fields');
            return;
        }
        if (pass !== confirmPass) {
            displayError('Passwords do not match');
            return;
        }
    }
    if (!email || !pass) {
        displayError('Please enter email and password');
        return;
    }

    try {
        showLoading(isSignUpMode ? 'Creating account...' : 'Logging in...');

        let response;
        if (isSignUpMode) {
            // Register
            response = await API.auth.register(name, email, pass);
        } else {
            // Login
            response = await API.auth.login(email, pass);
        }

        hideLoading();

        if (response.success) {
            if (response.data.verification_required) {
                // Show OTP View
                pendingVerificationEmail = email;
                pendingActionType = 'register';
                closeLoginModal(); // Close main login modal
                document.getElementById('authModal').style.display = 'block';
                document.getElementById('overlay').style.display = 'block';
                showAuthView('verification');
                showSuccess('Please check your email for the verification code');
                startOTPTimer('otpTimer');
                return;
            }

            // Store user data
            isLoggedIn = true;
            currentUserData = response.data;
            const userData = response.data;

            // Visual feedback for login
            const userIcon = document.getElementById('userIcon');
            if (userIcon) {
                userIcon.classList.remove('fa-user-circle');
                userIcon.classList.add('fa-check-circle');
                userIcon.style.color = '#00a8b5';
            }

            // Update profile fields
            updateSidebarProfileUI(userData);

            clearBookingSearchDates();

            closeLoginModal();
            showSuccess(isSignUpMode ? 'Account created successfully!' : 'Welcome back!');

            // Reload properties to show user-specific data
            await loadProperties();
        }
    } catch (error) {
        hideLoading();
        displayError(error.message || 'Authentication failed');
    }
}

// Social Login Initialization & Callbacks
window.addEventListener('message', async (event) => {
    if (event.data && event.data.type === 'SOCIAL_LOGIN_SUCCESS') {
        const payload = event.data.payload;
        authenticateSocial(payload.provider, payload.email, payload.nom, payload.token, payload.photo_profil);
    }
});


function handleGoogleLogin(response) {
    const responsePayload = decodeJwtResponse(response.credential);
    authenticateSocial('google', responsePayload.email, responsePayload.name, response.credential, responsePayload.picture);
}

function decodeJwtResponse(token) {
    var base64Url = token.split('.')[1];
    var base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
    var jsonPayload = decodeURIComponent(atob(base64).split('').map(function (c) {
        return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
    }).join(''));
    return JSON.parse(jsonPayload);
}
async function authenticateSocial(provider, email, nom, token, photo) {
    try {
        showLoading(`Authenticating with ${provider}...`);
        const response = await API.auth.socialLogin(provider, email, nom, token, photo);
        hideLoading();

        if (response.success) {
            isLoggedIn = true;
            currentUserData = response.data;
            const userData = response.data;

            const userIcon = document.getElementById('userIcon');
            if (userIcon) {
                userIcon.classList.remove('fa-user-circle');
                userIcon.classList.add('fa-check-circle');
                userIcon.style.color = '#00a8b5';
            }

            updateSidebarProfileUI(userData);
            closeLoginModal();
            document.getElementById('authModal').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
            clearBookingSearchDates();
            showSuccess(`Welcome back, ${userData.nom}!`);
            await loadProperties();
        }
    } catch (error) {
        hideLoading();
        showError(error.message || 'Social authentication failed');
    }
}

function initSocialLogin(provider) {
    const googleClientId = 'YOUR_GOOGLE_CLIENT_ID';

    if (googleClientId !== 'YOUR_GOOGLE_CLIENT_ID' && provider === 'google') {
        try {
            google.accounts.id.prompt();
        } catch (e) {
            console.error("Google Prompt Error:", e);
            showError("Google authentication is not configured yet.");
        }
    } else {
        // Fallback to professional simulation
        showLoading(`Connecting to Google...`);
        setTimeout(() => {
            hideLoading();
            const width = 500;
            const height = 650;
            const left = (window.innerWidth - width) / 2;
            const top = (window.innerHeight - height) / 2;
            window.open(
                `oauth_simulate.php?provider=google`,
                `Login with Google`,
                `width=${width},height=${height},top=${top},left=${left},resizable=yes,scrollbars=yes`
            );
        }, 500);
    }
}


async function performLogout() {
    try {
        await API.auth.logout();

        isLoggedIn = false;
        const userIcon = document.getElementById('userIcon');
        if (userIcon) {
            userIcon.classList.remove('fa-check-circle');
            userIcon.classList.add('fa-user-circle');
            userIcon.style.color = '';
        }
        document.getElementById('profileDropdown')?.classList.remove('active');

        const inputName = document.getElementById('inputName');
        if (inputName) inputName.value = '';

        const inputEmail = document.getElementById('inputEmail');
        if (inputEmail) inputEmail.value = '';

        clearBookingSearchDates();

        showSuccess('Logged out successfully');
    } catch (error) {
        showError('Logout failed');
    }
}

function updateUserUI() {
    const icon = document.getElementById('userIcon');
    const badge = document.getElementById('userBadge');

    if (isLoggedIn) {
        icon.style.color = 'var(--pink-accent)';
        icon.style.opacity = '1';
        badge.style.display = 'block';
    } else {
        icon.style.color = 'var(--text-color)';
        icon.style.opacity = '0.7';
        badge.style.display = 'none';
    }
}

function updateSidebarProfileUI(userData) {
    const sidebarName = document.getElementById('sidebarName');
    if (sidebarName) sidebarName.innerText = userData.nom;

    const inputName = document.getElementById('inputName');
    if (inputName) inputName.value = userData.nom;

    const inputEmail = document.getElementById('inputEmail');
    if (inputEmail) inputEmail.value = userData.email;

    const sidebarAvatar = document.getElementById('sidebarAvatar');
    if (sidebarAvatar) {
        if (userData.photo_profil) {
            sidebarAvatar.style.backgroundImage = `url(${window.location.origin}/memoire/${userData.photo_profil})`;
            sidebarAvatar.style.backgroundSize = 'cover';
            sidebarAvatar.style.backgroundPosition = 'center';
            sidebarAvatar.innerText = '';
        } else {
            sidebarAvatar.style.backgroundImage = 'none';
            sidebarAvatar.style.backgroundColor = getAvatarColor(userData.nom);
            sidebarAvatar.style.display = 'flex';
            sidebarAvatar.style.alignItems = 'center';
            sidebarAvatar.style.justifyContent = 'center';
            sidebarAvatar.style.color = 'white';
            sidebarAvatar.style.fontWeight = '700';
            sidebarAvatar.style.fontSize = '32px';
            sidebarAvatar.innerText = userData.nom.charAt(0).toUpperCase();
        }
    }

    const sidebarStatus = document.getElementById('sidebarStatus');
    if (sidebarStatus) {
        sidebarStatus.innerText = userData.is_admin ? 'Admin' : (userData.total_logements > 0 ? 'Host' : 'Guest');
    }

    // Update join date
    const sidebarJoined = document.getElementById('sidebarJoined');
    if (sidebarJoined && userData.date_inscription) {
        const joinYear = new Date(userData.date_inscription).getFullYear();
        sidebarJoined.innerHTML = `<i class="fas fa-calendar-alt"></i> Joined in ${joinYear}`;
    }

    // Toggle Remove Photo Link if it exists
    const removeLink = document.getElementById('removePhotoLink');
    if (removeLink) {
        removeLink.style.display = userData.photo_profil ? 'block' : 'none';
    }
}

// Search Logic
function setActive(section, e) {
    e.stopPropagation();
    closeAllPopovers();
    document.querySelectorAll('.search-section').forEach(s => s.classList.remove('active'));

    document.getElementById('section-' + section).classList.add('active');
    document.getElementById('pop-' + section).classList.add('active');
    document.getElementById('overlay').style.display = 'block';

    if (section === 'dest') {
        const inp = document.getElementById('popoverSearchInput');
        if (inp) inp.focus();
    }
}

function closeAllPopovers() {
    document.querySelectorAll('.popover').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.search-section').forEach(s => s.classList.remove('active'));
    document.getElementById('overlay').style.display = 'none';
    document.getElementById('confirmModal').style.display = 'none';
    document.getElementById('loginModal').style.display = 'none'; // Close login too
    // Close the auth modal (password recovery) if present
    if (document.getElementById('authModal')) document.getElementById('authModal').style.display = 'none';
    document.getElementById('profileDropdown').classList.remove('active');
    document.getElementById('hostWilayaListPopover').style.display = 'none';
}


// Wilaya Logic
function initWilayaList() {
    const list = document.getElementById('wilayaList');
    const hostList = document.getElementById('hostWilayaList');
    const template = (w, isHost = false) => `<div class="wilaya-item" onclick="${isHost ? "selectHostWilaya" : "selectWilaya"}('${w}')"><i class="fas fa-map-marker-alt" style="color:var(--pink-accent)"></i> ${w}</div>`;

    list.innerHTML = wilayas.map(w => template(w, false)).join('');
    hostList.innerHTML = wilayas.map(w => template(w, true)).join('');

    // Add Enter key listener for search input
    const searchInput = document.getElementById('popoverSearchInput');
    if (searchInput) {
        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                const val = searchInput.value.trim();
                if (!val) return;

                // Try to find a match in the list
                const match = wilayas.find(w => w.toLowerCase() === val.toLowerCase()) ||
                    wilayas.find(w => w.toLowerCase().startsWith(val.toLowerCase()));

                if (match) {
                    selectWilaya(match);
                } else {
                    // No match, just use the typed value
                    document.getElementById('destInput').value = val;
                    // Treat as "Selection Made" for UI flow, but don't commit to bookingSearchData yet
                    // bookingSearchData.location = val; // DECOUPLED
                    setActive('dates', e);
                }
            }
        });
    }
}

function filterWilayas() {
    const input = document.getElementById('popoverSearchInput');
    if (!input) return;
    const val = input.value.toLowerCase();
    const items = document.querySelectorAll('#wilayaList .wilaya-item');
    items.forEach(item => {
        item.style.display = item.innerText.toLowerCase().includes(val) ? 'flex' : 'none';
    });
}

function selectWilaya(name) {
    document.getElementById('destInput').value = name;
    // bookingSearchData.location = name; // DECOUPLED
    setActive('dates', event); // Auto-advance
}

// Guest Logic
function updateGuest(type, delta, e) {
    e.stopPropagation();
    const el = document.getElementById('cnt-' + type);
    let val = parseInt(el.innerText) + delta;
    if (val < 0) val = 0;

    // Validation
    if (type === 'adults' && val > 16) return;
    if (type === 'children' && val > 15) return;
    if (type === 'infants' && val > 5) return;

    el.innerText = val;
    guests[type] = val;

    // bookingSearchData update REMOVED from here to decouple search input from details page
    // It will be committed in handleSearch()

    const total = guests.adults + guests.children;
    // bookingSearchData.guests = total; // DECOUPLED

    document.getElementById('guestInput').value = total > 0 ? `${total} guest${total > 1 ? 's' : ''}` : '';
}

async function handleSearch() {
    closeAllPopovers();
    let dest = document.getElementById('destInput').value.toLowerCase();

    // Fallback: If dest is empty, check if user typed something in the search box but didn't select
    if (!dest) {
        const input = document.getElementById('popoverSearchInput');
        const typed = input ? input.value.trim().toLowerCase() : '';
        if (typed) {
            dest = typed;
            document.getElementById('destInput').value = input.value; // Sync UI
        }
    }

    // COMMIT SEARCH DATA (This transfers draft state to the booking state used by Property Details)
    bookingSearchData.location = document.getElementById('destInput').value;
    bookingSearchData.checkIn = draftDates.checkIn;
    bookingSearchData.checkOut = draftDates.checkOut;
    bookingSearchData.guestCounts = { ...guests, infants: 0 }; // using current guests state
    bookingSearchData.guests = guests.adults + guests.children;

    //Load properties with filters from backend
    const filters = {};
    if (dest) filters.wilaya = dest;
    if (draftDates.checkIn) filters.date_arrivee = draftDates.checkIn;
    if (draftDates.checkOut) filters.date_depart = draftDates.checkOut;
    if (bookingSearchData.guests > 0) filters.invites = bookingSearchData.guests;

    await loadProperties(filters);

    document.getElementById('emptyState').style.display = 'none';
    document.getElementById('mainGrid').style.display = 'grid';

    // If no results
    if (mockStays.length === 0) {
        document.getElementById('mainGrid').innerHTML = '<div style="grid-column: 1/-1; text-align:center; padding: 40px; opacity: 0.7">No stays found matching your criteria.</div>';
    }
}

// Menu Logic
function toggleProfileMenu(e) {
    e.stopPropagation();
    const drop = document.getElementById('profileDropdown');

    // Build menu based on Auth state
    let html = '';
    if (isLoggedIn) {
        html = `
                <div class="dropdown-item" onclick="handleMenuClick('Home')"><i class="fas fa-home"></i> <b>Home</b></div>
                <div class="dropdown-item" onclick="handleMenuClick('Profile')"><i class="fas fa-user-edit"></i> My Profile</div>
                <div class="dropdown-item" onclick="handleMenuClick('Trips')"><i class="fas fa-suitcase"></i> My Trips</div>
                <div class="dropdown-item" onclick="handleMenuClick('Reservations')"><i class="fas fa-clipboard-list"></i> Reservations</div>
                <div class="dropdown-item" onclick="handleMenuClick('Favorites')"><i class="fas fa-heart"></i> Favorites</div>
                ${currentUserData && currentUserData.is_admin ? `<a href="admin.php" class="dropdown-item" style="text-decoration:none; color:inherit; display:block;"><i class="fas fa-shield-alt"></i> Admin Dashboard</a>` : ''}
                <div class="dropdown-item" onclick="handleMenuClick('Help')"><i class="fas fa-question-circle"></i> Help Center</div>
                <div class="dropdown-item logout" onclick="handleMenuClick('Logout')"><i class="fas fa-power-off"></i> Logout</div>
            `;
    } else {
        html = `
                <div class="dropdown-item" onclick="openLoginModal('login')"><b>Log in</b></div>
                <div style="height:1px; background:var(--border-color); margin:8px 0"></div>
                <div class="dropdown-item" onclick="handleMenuClick('Home')"><i class="fas fa-home"></i> Home</div>
                <div class="dropdown-item" onclick="handleMenuClick('Help')"><i class="fas fa-question-circle"></i> Help Center</div>
            `;
    }
    drop.innerHTML = html;
    drop.classList.toggle('active');
}

async function handleMenuClick(view) {
    // Close menu immediately
    document.getElementById('profileDropdown')?.classList.remove('active');

    if (view === 'Help') {
        window.location.href = 'help_center.php';
        return;
    }

    if ((view === 'Profile' || view === 'Favorites' || view === 'Trips' || view === 'Reservations') && !isLoggedIn) {
        pendingAction = view;
        openLoginModal();
        return;
    }

    if (view === 'Logout') {
        try {
            await API.auth.logout();
            isLoggedIn = false;
            currentUserData = null;

            // FIX: Reset user icon class (revert checkmark to user circle)
            const uIcon = document.getElementById('userIcon');
            if (uIcon) {
                uIcon.classList.remove('fa-check-circle');
                uIcon.classList.add('fa-user-circle');
            }

            updateUserUI();
            showToast('Logged out successfully');
            await handleMenuClick('Home'); // Return home
        } catch (error) {
            showError('Logout failed');
        }
        return;
    }

    if (view === 'Login') {
        openLoginModal();
        return;
    }

    currentView = view;
    const home = document.getElementById('homeView');
    const profile = document.getElementById('profileView');
    const empty = document.getElementById('emptyState');
    const grid = document.getElementById('mainGrid');
    const hero = document.getElementById('heroSection');
    const search = document.getElementById('searchWrapper');
    const trips = document.getElementById('tripsView');
    const reservations = document.getElementById('reservationsView');

    // Reset visibility
    home.style.display = 'block';
    profile.style.display = 'none';
    empty.style.display = 'none';
    grid.style.display = 'grid';
    hero.style.display = 'flex';
    search.style.display = 'block';
    if (trips) trips.style.display = 'none';
    if (reservations) reservations.style.display = 'none';

    // Hide Detail views if open
    const detailsView = document.getElementById('arPropertyModal');
    if (detailsView) detailsView.style.display = 'none';
    const propertyModal = document.getElementById('arPropertyModal');
    if (propertyModal) propertyModal.style.display = 'none';
    document.body.style.overflow = 'auto'; // Restore scrolling

    const favHeader = document.getElementById('favoritesHeader');
    if (favHeader) favHeader.style.display = 'none';

    if (view === 'Profile') {
        home.style.display = 'none';
        profile.style.display = 'block';
    } else if (view === 'Favorites') {
        try {
            const response = await API.favorites.list();
            if (response.success && response.data) {
                const favs = response.data.map(prop => ({
                    id: prop.id,
                    city: prop.wilaya,
                    price: parseFloat(prop.prix_par_nuit).toLocaleString('fr-DZ'),
                    rating: prop.moyenne_note || 0,
                    img: getImageSrc(prop.image_principale),
                    fav: true,
                    desc: prop.titre || prop.description?.substring(0, 50) || 'Beautiful property',
                    fullData: prop
                }));

                if (favs.length === 0) {
                    grid.style.display = 'none';
                    empty.style.display = 'flex';
                    hero.style.display = 'none';
                    search.style.display = 'none';
                    if (favHeader) favHeader.style.display = 'none';
                } else {
                    renderGrid(favs);
                    hero.style.display = 'none';
                    search.style.display = 'none';
                    if (favHeader) favHeader.style.display = 'block';
                }
            }
        } catch (error) {
            console.error('Failed to load favorites:', error);
            // Fallback to local filtering if API fails
            const favs = mockStays.filter(s => s.fav);
            if (favs.length === 0) {
                grid.style.display = 'none';
                empty.style.display = 'block';
                hero.style.display = 'none';
                search.style.display = 'none';
                if (favHeader) favHeader.style.display = 'none';
            } else {
                renderGrid(favs);
                hero.style.display = 'none';
                search.style.display = 'none';
                if (favHeader) favHeader.style.display = 'block';
            }
        }
    } else if (view === 'Trips') {
        home.style.display = 'none';
        hero.style.display = 'none';
        search.style.display = 'none';
        grid.style.display = 'none';
        if (trips) trips.style.display = 'block';
        loadTrips();
    } else if (view === 'Reservations') {
        home.style.display = 'none';
        hero.style.display = 'none';
        search.style.display = 'none';
        grid.style.display = 'none';
        if (reservations) reservations.style.display = 'block';
        loadHostReservations();
    } else if (view === 'Home') {
        // Refresh properties when returning home to ensure images and data are fresh
        await loadProperties();
    }
}

function renderMessages() {
    const container = document.getElementById('messagesList');
    container.innerHTML = mockMessages.map(msg => `
            <div class="msg-card" onclick="handleMessageClick(${msg.id})">
                <img src="${msg.avatar}" class="msg-avatar">
                <div class="msg-info">
                    <div class="msg-top">
                        <span class="msg-name">${msg.sender}</span>
                        <span class="msg-time">${msg.time}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center">
                        <span class="msg-preview" style="${msg.unread ? 'font-weight:600' : ''}">${msg.text}</span>
                        ${msg.unread ? '<div class="unread-dot"></div>' : ''}
                    </div>
                </div>
            </div>
        `).join('');
}

function handleMessageClick(id) {
    const msg = mockMessages.find(m => m.id === id);
    if (!msg) return;

    // If user is not logged in, open login modal (login mode)
    if (!isLoggedIn) {
        openLoginModal('login');
        return;
    }

    // Otherwise open chat (placeholder behavior)
    showToast('Opened chat with ' + msg.sender);
}

// Profile Sub-views
function openProfileSub(sub) {
    // Hide all sub-views first to allow switching between them (e.g., Host -> All Homes)
    document.querySelectorAll('.sub-view').forEach(v => v.style.display = 'none');
    document.getElementById('profileMain').style.display = 'none';

    document.getElementById('view-' + sub).style.display = 'block';

    if (sub === 'all-homes') {
        renderMyHomes();
        // Ensure host progress is hidden if coming back from host
        const progressCont = document.getElementById('hostProgressContainer');
        if (progressCont) progressCont.style.display = 'none';
    }
}

function closeProfileSub() {
    document.querySelectorAll('.sub-view').forEach(v => v.style.display = 'none');
    document.getElementById('profileMain').style.display = 'block';
}

async function renderMyHomes() {
    const container = document.getElementById('my-homes-list');
    container.innerHTML = '<div style="text-align:center; padding: 40px; opacity: 0.7">Loading your listings...</div>';

    try {
        const response = await API.properties.getMyListings();
        if (response.success && response.data) {
            const myStays = response.data.map(prop => ({
                id: prop.id,
                city: prop.wilaya,
                price: parseFloat(prop.prix_par_nuit).toLocaleString('fr-DZ'),
                rating: prop.moyenne_note || 0,
                img: getImageSrc(prop.image_principale),
                desc: prop.titre || prop.description?.substring(0, 50) || 'Beautiful property',
                category: prop.categorie,
                basics: {
                    bedrooms: prop.nombre_chambres,
                    beds: prop.nombre_lits,
                    baths: prop.nombre_salles_bain,
                    guests: prop.nombre_invites
                },
                fullData: prop
            }));

            // Cache in mockStays
            myStays.forEach(stay => {
                const stayIndex = mockStays.findIndex(s => s.id == stay.id);
                if (stayIndex > -1) {
                    mockStays[stayIndex] = stay;
                } else {
                    mockStays.push(stay);
                }
            });

            if (myStays.length === 0) {
                container.innerHTML = `
                    <div style="text-align:center; padding: 60px 40px; opacity: 0.6; border: 1px dashed var(--border-color); border-radius: 24px; background: rgba(255,255,255,0.02)">
                        <i class="fas fa-home" style="font-size: 48px; margin-bottom: 20px; color: var(--pink-accent)"></i>
                        <h3 style="margin-bottom: 10px;">No listings yet</h3>
                        <p style="font-size: 15px;">Ready to share your space? Start by listing your home.</p>
                        <button class="save-btn" style="margin-top: 24px; padding: 12px 24px;" onclick="initAddHome()">List your home</button>
                    </div>
                `;
                return;
            }

            // Header for the list (Desktop only)
            let headerHtml = `
                <div class="listing-row" style="background: transparent; border: none; font-weight: 700; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; padding: 0 16px 10px 16px; opacity: 0.6; display: grid;">
                    <div></div>
                    <div>Listing</div>
                    <div>Status</div>
                    <div>Basics</div>
                    <div>Price</div>
                </div>
            `;

            container.innerHTML = headerHtml + myStays.map(item => {
                const basics = item.basics;
                return `
                <div class="listing-row">
                    <img src="${getImageSrc(item.img)}" class="listing-img-small" alt="${item.city}">
                    <div class="listing-details">
                        <h4>${item.city}</h4>
                        <p>${item.desc.substring(0, 50)}${item.desc.length > 50 ? '...' : ''}</p>
                    </div>
                    <div>
                        <span class="listing-status">Listed</span>
                    </div>
                    <div class="listing-info">
                        ${basics.bedrooms} Br · ${basics.beds} Beds · ${basics.baths} Ba
                    </div>
                    <div class="listing-price">
                        ${item.price} DZD
                    </div>
                    <div class="listing-actions">
                        <button class="action-btn" onclick="editHome(${item.id})" title="Edit Listing"><i class="fas fa-pen"></i></button>
                        <button class="action-btn delete" onclick="deleteHome(${item.id})" title="Remove Listing"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
                `;
            }).join('');
        }
    } catch (error) {
        container.innerHTML = `<div style="text-align:center; padding: 40px; color: #ff5a5f">Failed to load listings: ${error.message}</div>`;
    }
}

// Updated Delete Logic with Custom Modal
function deleteHome(id) {
    deleteTargetId = id;
    document.getElementById('confirmModal').style.display = 'block';
    document.getElementById('overlay').style.display = 'block';
}

async function confirmDelete() {
    if (deleteTargetId !== null) {
        try {
            showLoading('Deleting property...');
            await API.properties.delete(deleteTargetId);
            
            // Remove from local list and UI
            mockStays = mockStays.filter(s => s.id !== deleteTargetId);
            localStorage.setItem('winstay_data', JSON.stringify(mockStays));
            
            await renderMyHomes();
            await loadProperties();
            hideLoading();
            showToast('Property deleted successfully');
        } catch (error) {
            hideLoading();
            showError('Failed to delete property: ' + error.message);
        }
    }
    closeConfirmModal();
}

function closeConfirmModal() {
    document.getElementById('confirmModal').style.display = 'none';
    document.getElementById('overlay').style.display = 'none';
    deleteTargetId = null;
}

// ============================================
// NEW & UPDATED: Hosting Flow (Airbnb Style)
// ============================================

// Hosting State
let currentHostStep = 1;
const totalHostSteps = 11;
let hostCategory = '';

function selectHostCategory(el, cat) {
    hostCategory = cat;
    document.querySelectorAll('#hostStep1 .selection-card').forEach(c => c.classList.remove('active'));
    el.classList.add('active');
}

function navigateHostStep(delta) {
    // Validation before going next
    if (delta > 0) {
        if (currentHostStep === 1 && !hostCategory) {
            showToast('Please select a category');
            return;
        }
        if (currentHostStep === 2 && (!document.getElementById('hostLat').value || !document.getElementById('hostLng').value)) {
            showToast('Please select a location on the map');
            return;
        }
        if (currentHostStep === 3 && (!document.getElementById('hostWilaya').value || !document.getElementById('hostAddress').value)) {
            showToast('Please confirm your Wilaya and Address');
            return;
        }
        if (currentHostStep === 6 && hostPhotoData.length === 0) {
            showToast('Please add at least one photo');
            return;
        }
    }

    // Hide current
    document.getElementById(`hostStep${currentHostStep}`).classList.remove('active');

    // Update step
    currentHostStep += delta;

    // Constrain
    if (currentHostStep < 1) currentHostStep = 1;
    if (currentHostStep > totalHostSteps) currentHostStep = totalHostSteps;

    // Show new
    document.getElementById(`hostStep${currentHostStep}`).classList.add('active');

    // Update UI (Progress & Buttons)
    updateHostWizardUI();

    // Initialize Map if step 2
    if (currentHostStep === 2) {
        setTimeout(initHostMap, 100);
    }
}

function initHostMap() {
    const latInput = document.getElementById('hostLat');
    const lngInput = document.getElementById('hostLng');

    // Default to Algiers if no coords
    let initialLat = 36.7538;
    let initialLng = 3.0588;

    if (latInput.value && lngInput.value) {
        initialLat = parseFloat(latInput.value);
        initialLng = parseFloat(lngInput.value);
    }

    if (!hostMapInstance) {
        hostMapInstance = L.map('hostMap', {
            zoomControl: false,
            attributionControl: false
        }).setView([initialLat, initialLng], 13);

        // Define Google Maps Layers
        const googleStreets = L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            attribution: 'Map data &copy; Google'
        });
        
        const googleSatellite = L.tileLayer('http://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            attribution: 'Map data &copy; Google'
        });

        const googleHybrid = L.tileLayer('http://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            attribution: 'Map data &copy; Google'
        });

        googleStreets.addTo(hostMapInstance);
        
        // Layer Control
        const baseMaps = {
            "Google Streets": googleStreets,
            "Google Satellite": googleSatellite,
            "Google Hybrid": googleHybrid
        };
        L.control.layers(baseMaps, null, { position: 'topright' }).addTo(hostMapInstance);

        // Search Control (Google Maps style search)
        const geocoder = L.Control.geocoder({
            defaultMarkGeocode: false,
            placeholder: "Search for a place...",
            position: 'topleft'
        }).on('markgeocode', function (e) {
            const bbox = e.geocode.bbox;
            const poly = L.polygon([
                bbox.getSouthEast(),
                bbox.getNorthEast(),
                bbox.getNorthWest(),
                bbox.getSouthWest()
            ]);
            hostMapInstance.fitBounds(poly.getBounds());
            hostMarkerInstance.setLatLng(e.geocode.center);
            updateHostLocationFields(e.geocode.center.lat, e.geocode.center.lng);
        }).addTo(hostMapInstance);

        L.control.zoom({ position: 'bottomright' }).addTo(hostMapInstance);

        hostMarkerInstance = L.marker([initialLat, initialLng], {
            draggable: true
        }).addTo(hostMapInstance);

        hostMarkerInstance.on('dragend', function (e) {
            const pos = e.target.getLatLng();
            updateHostLocationFields(pos.lat, pos.lng);
        });

        hostMapInstance.on('click', function (e) {
            hostMarkerInstance.setLatLng(e.latlng);
            updateHostLocationFields(e.latlng.lat, e.latlng.lng);
        });
    } else {
        hostMapInstance.invalidateSize();
        hostMapInstance.setView([initialLat, initialLng], 13);
        hostMarkerInstance.setLatLng([initialLat, initialLng]);
    }
}

async function updateHostLocationFields(lat, lng) {
    document.getElementById('hostLat').value = lat;
    document.getElementById('hostLng').value = lng;

    // Try to reverse geocode
    try {
        const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1&accept-language=fr`);
        const data = await response.json();
        if (data && data.display_name) {
            const addrInput = document.getElementById('hostAddress');
            if (addrInput) {
                addrInput.value = data.display_name;
            }

            // Detect Wilaya automatically and accurately (handles English, French, Arabic, and accents)
            if (data.address) {
                const addressFields = [
                    data.address.state,
                    data.address.city,
                    data.address.county,
                    data.address.suburb,
                    data.address.town,
                    data.address.village,
                    data.address.region
                ].filter(Boolean);

                const fullText = data.display_name || "";

                // Normalization helper (removes accents, spaces, symbols)
                const normalize = (str) => {
                    if (!str) return "";
                    return str
                        .toLowerCase()
                        .normalize("NFD")
                        .replace(/[\u0300-\u036f]/g, "")
                        .replace(/[^a-z0-9\s]/g, " ")
                        .trim();
                };

                // Mappings for all 69 supported Winstay Wilayas / Districts
                const wilayaMappings = {
                    "Adrar": ["أدرار", "adrar"],
                    "Chlef": ["الشلف", "chlef"],
                    "Laghouat": ["الأغواط", "الاغواط", "laghouat"],
                    "Oum El Bouaghi": ["أم البواقي", "ام البواقي", "oum el bouaghi", "bouaghi"],
                    "Batna": ["باتنة", "batna"],
                    "Béjaïa": ["بجاية", "bejaia"],
                    "Biskra": ["بسكرة", "biskra"],
                    "Béchar": ["بشار", "bechar"],
                    "Blida": ["البليدة", "البليده", "blida"],
                    "Bouira": ["البويرة", "البويره", "bouira"],
                    "Tamanrasset": ["تمنراست", "tamanrasset", "tamanghasset"],
                    "Tébessa": ["تبسة", "تبسه", "tebessa"],
                    "Tlemcen": ["تلمسان", "tlemcen"],
                    "Tiaret": ["تيارت", "tiaret"],
                    "Tizi Ouzou": ["تيزي وزو", "tizi ouzou", "tizi-ouzou"],
                    "Algiers": ["الجزائر", "alger", "algiers", "algier"],
                    "Djelfa": ["الجلفة", "الجلفه", "djelfa"],
                    "Jijel": ["جيجل", "jijel"],
                    "Sétif": ["سطيف", "setif"],
                    "Saïda": ["سعيدة", "سعيده", "saida"],
                    "Skikda": ["سكيكدة", "سكيكده", "skikda"],
                    "Sidi Bel Abbès": ["سيدي بلعباس", "sidi bel abbes", "sidi bel-abbes"],
                    "Annaba": ["عنابة", "عنابه", "annaba"],
                    "Guelma": ["قالمة", "قالمه", "guelma"],
                    "Constantine": ["قسنطينة", "قسنطينه", "constantine"],
                    "Médéa": ["المدية", "المديه", "medea"],
                    "Mostaganem": ["مستغانم", "mostaganem"],
                    "M'Sila": ["المسيلة", "المسيله", "msila", "m'sila"],
                    "Mascara": ["معسكر", "mascara"],
                    "Ouargla": ["ورقلة", "ورقله", "ouargla"],
                    "Oran": ["وهران", "oran"],
                    "El Bayadh": ["البيض", "el bayadh", "bayadh"],
                    "Illizi": ["إليزي", "اليزي", "illizi"],
                    "Bordj Bou Arréridj": ["برج بوعريريج", "bordj bou arreridj", "bordj bou-arreridj", "bba"],
                    "Boumerdès": ["بومرداس", "boumerdes", "boumerdas"],
                    "El Tarf": ["الطارف", "el tarf", "tarf"],
                    "Tindouf": ["تندوف", "tindouf"],
                    "Tissemsilt": ["تيسمسيلت", "tissemsilt"],
                    "El Oued": ["الوادي", "الوادي", "el oued", "oued"],
                    "Khenchela": ["خنشلة", "خنشله", "khenchela"],
                    "Souk Ahras": ["سوق أهراس", "سوق اهراس", "souk ahras"],
                    "Tipaza": ["تيبازة", "تيبازه", "tipaza", "tipasa"],
                    "Mila": ["ميلة", "ميله", "mila"],
                    "Aïn Defla": ["عين الدفلى", "عين الدفلي", "ain defla", "ain-defla"],
                    "Naâma": ["النعامة", "النعامه", "naama"],
                    "Aïn Témouchent": ["عين تموشنت", "ain temouchent", "ain-temouchent"],
                    "Ghardaïa": ["غرداية", "غردايه", "ghardaia"],
                    "Relizane": ["غليزان", "relizane"],
                    "El M'Ghair": ["المغير", "el m'ghair", "m'ghair", "mghair"],
                    "El Meniaa": ["المنيعة", "المنيعه", "el meniaa", "el menia"],
                    "Ouled Djellal": ["أولاد جلال", "اولاد جلال", "ouled djellal"],
                    "Bordj Baji Mokhtar": ["برج باجي مختار", "bordj baji mokhtar", "bbm"],
                    "Béni Abbès": ["بني عباس", "beni abbes", "beni-abbes"],
                    "Timimoun": ["تيميمون", "timimoun"],
                    "Touggourt": ["تقرت", "touggourt"],
                    "Djanet": ["جانت", "djanet"],
                    "In Salah": ["عين صالح", "in salah"],
                    "In Guezzam": ["عين قزام", "in guezzam"],
                    "Aflou": ["أفلو", "aflou"],
                    "Barika": ["بريكة", "barika"],
                    "Ksar Chellala": ["قصر الشلالة", "ksar chellala"],
                    "Messaad": ["مسعد", "messaad"],
                    "Aïn Oussera": ["عين وسارة", "ain oussera"],
                    "Boussaâda": ["بوسعادة", "boussaada", "bou saada"],
                    "El Abiodh Sidi Cheikh": ["الأبيض سيدي الشيخ", "el abiodh sidi cheikh"],
                    "El Kantara": ["القنطرة", "el kantara"],
                    "Bir El Ater": ["بئر العاتر", "bir el ater"],
                    "Ksar El Boukhari": ["قصر البخاري", "ksar el boukhari"],
                    "El Aricha": ["العريشة", "el aricha"]
                };

                let matchedWilaya = null;

                // 1. Search in specific fields (precise matching)
                for (const field of addressFields) {
                    const normalizedField = normalize(field);
                    const fieldStr = field.toLowerCase().trim();

                    for (const [wilayaName, keywords] of Object.entries(wilayaMappings)) {
                        for (const keyword of keywords) {
                            if (/[\u0600-\u06FF]/.test(keyword)) {
                                if (fieldStr.includes(keyword)) {
                                    matchedWilaya = wilayaName;
                                    break;
                                }
                            } else {
                                const normalizedKeyword = normalize(keyword);
                                if (normalizedField.includes(normalizedKeyword)) {
                                    matchedWilaya = wilayaName;
                                    break;
                                }
                            }
                        }
                        if (matchedWilaya) break;
                    }
                    if (matchedWilaya) break;
                }

                // 2. Fallback to searching the full display name
                if (!matchedWilaya) {
                    const fullTextStr = fullText.toLowerCase();
                    const normalizedFullText = normalize(fullText);

                    for (const [wilayaName, keywords] of Object.entries(wilayaMappings)) {
                        for (const keyword of keywords) {
                            if (/[\u0600-\u06FF]/.test(keyword)) {
                                if (fullTextStr.includes(keyword)) {
                                    matchedWilaya = wilayaName;
                                    break;
                                }
                            } else {
                                const normalizedKeyword = normalize(keyword);
                                if (normalizedFullText.includes(normalizedKeyword)) {
                                    matchedWilaya = wilayaName;
                                    break;
                                }
                            }
                        }
                        if (matchedWilaya) break;
                    }
                }

                // If found a match, pre-fill it automatically
                if (matchedWilaya) {
                    document.getElementById('hostWilaya').value = matchedWilaya;
                }
            }
        }
    } catch (e) {
        console.error("Geocoding error", e);
    }
}

function toggleHostWilayaList(event) {
    event.stopPropagation();
    const popover = document.getElementById('hostWilayaListPopover');
    const isVisible = popover.style.display === 'block';
    
    // Close all other popovers if any
    document.querySelectorAll('.popover').forEach(p => p.style.display = 'none');
    
    if (!isVisible) {
        popover.style.display = 'block';
        filterHostWilayas(); // Render initial list
    }
}

function filterHostWilayas() {
    const input = document.getElementById('hostWilaya');
    const filter = input.value.toLowerCase();
    const list = document.getElementById('hostWilayaList');
    const popover = document.getElementById('hostWilayaListPopover');
    
    const filtered = wilayas.filter(w => w.toLowerCase().includes(filter));
    
    list.innerHTML = filtered.map(w => `
        <div class="wilaya-item" onclick="selectHostWilaya(event, '${w}')">
            <i class="fas fa-map-marker-alt" style="color: var(--primary-color); opacity: 1;"></i>
            <span style="color: #ffffff; font-weight: 500;">${w}</span>
        </div>
    `).join('');
    
    if (filtered.length > 0) {
        popover.style.display = 'block';
    } else {
        popover.style.display = 'none';
    }
}

function selectHostWilaya(event, name) {
    if (event) event.stopPropagation();
    const input = document.getElementById('hostWilaya');
    input.value = name;
    document.getElementById('hostWilayaListPopover').style.display = 'none';
}


// Close popovers on outside click
document.addEventListener('click', () => {
    const popover = document.getElementById('hostWilayaListPopover');
    if (popover) popover.style.display = 'none';
});


function locateMe() {
    if (!navigator.geolocation) {
        showError('Geolocation is not supported by your browser');
        return;
    }

    const btn = document.querySelector('.locate-me-btn');
    if (btn) btn.classList.add('locating');

    showLoading('Finding your precise location...');
    navigator.geolocation.getCurrentPosition(
        (position) => {
            if (btn) btn.classList.remove('locating');
            hideLoading();
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            const accuracy = position.coords.accuracy;

            if (hostMapInstance) {
                // Smooth Fly to
                hostMapInstance.flyTo([lat, lng], 17, {
                    animate: true,
                    duration: 1.5
                });
                
                hostMarkerInstance.setLatLng([lat, lng]);
                updateHostLocationFields(lat, lng);

                // Add an accuracy circle (Google Maps style)
                if (window.accuracyCircle) {
                    hostMapInstance.removeLayer(window.accuracyCircle);
                }
                window.accuracyCircle = L.circle([lat, lng], {
                    radius: accuracy,
                    color: '#4285F4',
                    fillColor: '#4285F4',
                    fillOpacity: 0.15,
                    weight: 1
                }).addTo(hostMapInstance);

                showToast(`Located within ${Math.round(accuracy)} meters`);
            }
        },
        (error) => {
            if (btn) btn.classList.remove('locating');
            hideLoading();
            let msg = 'Unable to retrieve your location';
            if (error.code === 1) msg = 'Location access denied. Please enable it in your browser settings.';
            showError(msg);
            console.error("Geolocation error", error);
        },
        { 
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        }
    );
}

function updateHostWizardUI() {
    // Progress Bar
    const progress = (currentHostStep / totalHostSteps) * 100;
    const bar = document.getElementById('hostProgressBar');
    if (bar) bar.style.width = `${progress}%`;

    // Buttons
    const backBtn = document.getElementById('hostBackBtn');
    const nextBtn = document.getElementById('hostNextBtn');

    if (backBtn) backBtn.style.visibility = currentHostStep === 1 ? 'hidden' : 'visible';

    if (nextBtn) {
        if (currentHostStep === totalHostSteps) {
            nextBtn.innerText = editingId ? 'Update Listing' : 'Publish Listing';
            nextBtn.onclick = saveHosting;
        } else {
            nextBtn.innerText = 'Next';
            nextBtn.onclick = () => navigateHostStep(1);
        }
    }
}

function initAddHome() {
    if (!currentUserData) {
        openLoginModal();
        return;
    }

    if (currentUserData.statut_hote === 'en_attente') {
        showToast('Your host application is pending review.');
        return;
    }

    if (currentUserData.statut_hote !== 'approuve') {
        handleMenuClick('Profile');
        openProfileSub('host-apply');
        return;
    }

    editingId = null;
    currentHostStep = 1;
    hostCategory = '';
    deletedPhotoIds = [];

    // Reset Fields
    document.getElementById('hostWilaya').value = '';
    document.getElementById('hostAddress').value = '';
    document.getElementById('hostLat').value = '';
    document.getElementById('hostLng').value = '';
    document.getElementById('hostPrice').value = '';
    document.getElementById('hostDesc').value = '';
    document.getElementById('hostBio').value = '';
    document.getElementById('hostProfession').value = '';
    document.getElementById('hostSince').value = '';
    document.getElementById('hostLanguages').value = '';
    document.getElementById('hostCancelPolicy').value = '';
    document.getElementById('hostHouseRules').value = '';
    document.getElementById('hostSafety').value = '';

    // Reset Basics State
    hostBasics = { guests: 4, bedrooms: 2, beds: 2, baths: 1 };
    updateHostBasicsUI();

    // Reset Amenities
    hostAmenities = [];
    updateHostAmenitiesUI();

    // Reset Photos
    hostPhotoData = [];
    const previews = document.getElementById('photoPreviews');
    if (previews) previews.innerHTML = '';

    // Reset Category UI
    document.querySelectorAll('#hostStep1 .selection-card').forEach(c => c.classList.remove('active'));

    // Reset Wizard UI
    document.querySelectorAll('.host-step').forEach(s => s.classList.remove('active'));
    const step1 = document.getElementById('hostStep1');
    if (step1) step1.classList.add('active');

    // Show Progress Container
    const progressCont = document.getElementById('hostProgressContainer');
    if (progressCont) progressCont.style.display = 'block';

    updateHostWizardUI();
    openProfileSub('host');
}

async function editHome(id) {
    try {
        showLoading('Loading property details...');
        const response = await API.properties.get(id);
        hideLoading();

        if (!response.success || !response.data) {
            showError('Failed to load property details');
            return;
        }

        const prop = response.data;
        const item = {
            id: prop.id,
            city: prop.wilaya,
            price: parseFloat(prop.prix_par_nuit).toLocaleString('fr-DZ'),
            rating: prop.moyenne_note || 0,
            img: getImageSrc(prop.image_principale),
            fav: prop.is_favorited || false,
            desc: prop.titre || prop.description?.substring(0, 50) || 'Beautiful property',
            isMine: prop.is_mine || false,
            category: prop.categorie,
            amenities: prop.espaces ? prop.espaces.map(e => e.type_espace) : [],
            images: prop.images ? prop.images.map(img => getImageSrc(img.url_image)) : (prop.image_principale ? [getImageSrc(prop.image_principale)] : []),
            basics: {
                guests: prop.nombre_invites,
                bedrooms: prop.nombre_chambres,
                beds: prop.nombre_lits,
                baths: prop.nombre_salles_bain
            },
            fullData: prop
        };

        // Cache in mockStays
        const stayIndex = mockStays.findIndex(s => s.id == item.id);
        if (stayIndex > -1) {
            mockStays[stayIndex] = item;
        } else {
            mockStays.push(item);
        }

        editingId = id;
        currentHostStep = 1;
        hostCategory = item.category || 'House';

        // Populate standard fields
        document.getElementById('hostWilaya').value = item.city;
        document.getElementById('hostAddress').value = item.fullData ? (item.fullData.adresse || '') : '';
        document.getElementById('hostLat').value = item.fullData ? (item.fullData.latitude || '') : '';
        document.getElementById('hostLng').value = item.fullData ? (item.fullData.longitude || '') : '';
        document.getElementById('hostPrice').value = item.price.replace(/[^0-9]/g, '');
        document.getElementById('hostDesc').value = item.fullData ? (item.fullData.description || '') : item.desc;
        document.getElementById('hostCancelPolicy').value = item.fullData ? (item.fullData.cancel_policy || '') : '';
        document.getElementById('hostHouseRules').value = item.fullData ? (item.fullData.house_rules || '') : '';
        document.getElementById('hostSafety').value = item.fullData ? (item.fullData.safety_rules || '') : '';

        // Populate Basics (from item or default if not exists)
        hostBasics = item.basics || { guests: 4, bedrooms: 2, beds: 2, baths: 1 };
        updateHostBasicsUI();

        // Populate Amenities (from item or default)
        hostAmenities = item.amenities || [];
        updateHostAmenitiesUI();

        // Populate Photos
        hostPhotoData = [];
        deletedPhotoIds = [];
        if (prop.images && prop.images.length) {
            prop.images.forEach((img) => hostPhotoData.push({ 
                id: img.id, 
                src: getImageSrc(img.url_image), 
                fileName: '' 
            }));
        } else if (item.images && item.images.length) {
            item.images.forEach((imgUrl) => hostPhotoData.push({ src: imgUrl, fileName: '' }));
        } else if (item.img) {
            hostPhotoData.push({ src: item.img, fileName: '' });
        }
        renderPhotoPreviews();

        // Populate Category UI
        document.querySelectorAll('#hostStep1 .selection-card').forEach(card => {
            if (card.innerText.includes(hostCategory)) card.classList.add('active');
            else card.classList.remove('active');
        });

        // Reset Wizard UI
        document.querySelectorAll('.host-step').forEach(s => s.classList.remove('active'));
        const step1 = document.getElementById('hostStep1');
        if (step1) step1.classList.add('active');

        // Show Progress Container
        const progressCont = document.getElementById('hostProgressContainer');
        if (progressCont) progressCont.style.display = 'block';

        updateHostWizardUI();
        openProfileSub('host');
    } catch (e) {
        hideLoading();
        showError('Failed to load property data for editing: ' + e.message);
    }
}

// UI Updaters for Host Form
function updateHostBasicsUI() {
    document.getElementById('host-guests-val').innerText = hostBasics.guests;
    document.getElementById('host-bedrooms-val').innerText = hostBasics.bedrooms;
    document.getElementById('host-beds-val').innerText = hostBasics.beds;
    document.getElementById('host-baths-val').innerText = hostBasics.baths;
}

function updateHostCounter(key, delta) {
    let val = hostBasics[key] + delta;
    if (val < 0) val = 0; // prevent negative
    if (key === 'guests' && val < 1) val = 1; // min 1 guest
    hostBasics[key] = val;
    updateHostBasicsUI();
}

function updateHostAmenitiesUI() {
    // Updated for card-based UI
    const cards = document.querySelectorAll('#hostStep5 .selection-card');
    cards.forEach(card => {
        const key = card.getAttribute('onclick').match(/'([^']+)'/)[1];
        if (hostAmenities.includes(key)) card.classList.add('active');
        else card.classList.remove('active');
    });
}

function toggleAmenity(el, key) {
    if (hostAmenities.includes(key)) {
        hostAmenities = hostAmenities.filter(k => k !== key);
        el.classList.remove('active');
    } else {
        hostAmenities.push(key);
        el.classList.add('active');
    }
}


async function saveHosting() {
    const wilaya = document.getElementById('hostWilaya').value;
    const adresse = document.getElementById('hostAddress').value;
    const latitude = document.getElementById('hostLat').value;
    const longitude = document.getElementById('hostLng').value;
    const priceStr = document.getElementById('hostPrice').value;
    const desc = document.getElementById('hostDesc').value;
    const bio = document.getElementById('hostBio').value;
    const profession = document.getElementById('hostProfession').value;
    const since = document.getElementById('hostSince').value;
    const languages = document.getElementById('hostLanguages').value;
    const cancelPolicy = document.getElementById('hostCancelPolicy').value;
    const houseRules = document.getElementById('hostHouseRules').value;
    const safetyRules = document.getElementById('hostSafety').value;
    const title = desc.split('.')[0]; // Use first sentence as title if not provided

    if (!wilaya || !priceStr) {
        showToast('Please fill in required fields');
        return;
    }

    const price = parseInt(priceStr);

    try {
        showLoading(editingId ? 'Updating property...' : 'Publishing property...');

        const propertyData = {
            titre: title || 'Beautiful Home in ' + wilaya,
            description: desc,
            wilaya: wilaya,
            adresse: adresse,
            latitude: latitude,
            longitude: longitude,
            prix_par_nuit: price,
            nombre_invites: hostBasics.guests,
            nombre_chambres: hostBasics.bedrooms,
            nombre_lits: hostBasics.beds,
            nombre_salles_bain: hostBasics.baths,
            categorie: hostCategory || 'House',
            espaces: hostAmenities, // Assuming espaces maps to amenities for now
            cancel_policy: cancelPolicy,
            house_rules: houseRules,
            safety_rules: safetyRules,
            host_bio: bio,
            host_profession: profession,
            host_since: since,
            host_languages: languages
        };

        if (editingId) {
            propertyData.deleted_images = deletedPhotoIds;
            if (hostPhotoData[0] && hostPhotoData[0].id) {
                propertyData.main_image_id = hostPhotoData[0].id;
            }
        }

        let response;
        if (editingId) {
            response = await API.properties.update(editingId, propertyData);
        } else {
            response = await API.properties.create(propertyData);
        }

        if (response.success) {
            const logementId = response.data.id;

            // Upload images if any new ones
            const newFiles = hostPhotoData
                .filter(p => p.file) // Only those with actual File objects
                .map(p => p.file);

            if (newFiles.length > 0) {
                const uploadRes = await API.properties.uploadImages(logementId, newFiles);
                if (hostPhotoData[0] && hostPhotoData[0].file && uploadRes.success && uploadRes.data?.images?.length > 0) {
                    const newCoverId = uploadRes.data.images[0].id;
                    await API.properties.update(logementId, { main_image_id: newCoverId });
                }
            }

            hideLoading();
            showSuccess(editingId ? 'Property updated successfully!' : 'Listing published successfully!');

            // Reload properties to reflect changes
            await loadProperties();

            // Hide Progress
            const progressCont = document.getElementById('hostProgressContainer');
            if (progressCont) progressCont.style.display = 'none';

            // Return to "All the home" view
            openProfileSub('all-homes');
        }
    } catch (error) {
        hideLoading();
        showError(error.message || 'Failed to save property');
    }
}

async function savePersonalInfo() {
    const nom = document.getElementById('inputName').value;
    const email = document.getElementById('inputEmail').value;

    if (!nom || !email) {
        showError('Please fill in all fields');
        return;
    }

    try {
        showLoading('Updating profile...');
        const updates = { nom, email };
        await API.users.updateProfile(updates);
        hideLoading();

        // Sync in-memory user data so the whole app stays consistent
        if (currentUserData) {
            currentUserData.nom   = nom;
            currentUserData.email = email;
        }
        // Re-run the same UI refresh used on login — updates name, avatar,
        // avatar colour, email input field, status badge, join date all at once
        updateSidebarProfileUI(currentUserData);

        showSuccess('Profile updated successfully!');
        closeProfileSub();
    } catch (error) {
        hideLoading();
        showError(error.message || 'Failed to update profile');
    }
}

async function saveSecurity() {
    const newPass = document.getElementById('inputNewPass').value;
    const confirmPass = document.getElementById('inputConfirmPass').value;

    if (!newPass || !confirmPass) {
        showError('Please fill in both password fields');
        return;
    }

    if (newPass !== confirmPass) {
        showError('Passwords do not match');
        return;
    }

    if (newPass.length < 6) {
        showError('Password must be at least 6 characters');
        return;
    }

    try {
        showLoading('Updating password...');
        await API.users.updateProfile({ mot_de_passe: newPass });
        hideLoading();

        // Clear fields
        document.getElementById('inputCurrentPass').value = '';
        document.getElementById('inputNewPass').value = '';

        showSuccess('Password updated successfully!');
        closeProfileSub();
    } catch (error) {
        hideLoading();
        showError(error.message || 'Failed to update password');
    }
}

// Hosting Logic
function toggleHostWilayaList(e) {
    e.stopPropagation();
    document.getElementById('hostWilayaListPopover').style.display = 'block';
    document.getElementById('overlay').style.display = 'block';
}

function filterHostWilayas() {
    const val = document.getElementById('hostWilaya').value.toLowerCase();
    const items = document.querySelectorAll('#hostWilayaList .wilaya-item');
    items.forEach(item => {
        item.style.display = item.innerText.toLowerCase().includes(val) ? 'flex' : 'none';
    });
}

function selectHostWilaya(name) {
    document.getElementById('hostWilaya').value = name;
    document.getElementById('hostWilayaListPopover').style.display = 'none';
    document.getElementById('overlay').style.display = 'none';
}

// NEW: Profile Picture Upload Logic
function triggerProfileUpload() {
    document.getElementById('profilePicUpload').click();
}

async function handleProfilePic(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];

        // Validate file size (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            showError('File size must be less than 5MB');
            return;
        }

        // Validate file type
        if (!file.type.startsWith('image/')) {
            showError('Please select an image file');
            return;
        }

        try {
            showLoading('Uploading photo...');
            const response = await API.users.uploadPhoto(file);
            hideLoading();

            if (response.success) {
                // Fetch full session to get updated user data correctly
                await checkSession();
                showSuccess('Profile picture updated!');
            }
        } catch (error) {
            hideLoading();
            showError(error.message || 'Failed to upload photo');
        }
    }
}

async function removeProfilePic() {
    try {
        showLoading('Removing photo...');
        const response = await API.users.updateProfile({ photo_profil: null });
        hideLoading();

        if (response.success) {
            await checkSession();
            showSuccess('Profile picture removed');
        }
    } catch (error) {
        hideLoading();
        showError(error.message || 'Failed to remove photo');
    }
}

// Hosting photo upload handlers
function handlePhotoUpload(input) {
    if (!input || !input.files) return;
    const files = Array.from(input.files);
    files.forEach(file => {
        const reader = new FileReader();
        reader.onload = function (e) {
            hostPhotoData.push({
                src: e.target.result,
                fileName: file.name,
                file: file // Store the actual File object for upload
            });
            renderPhotoPreviews();
        };
        reader.readAsDataURL(file);
    });
    // reset input so same file can be reselected later
    input.value = '';
}

function renderPhotoPreviews() {
    const container = document.getElementById('photoPreviews');
    if (!container) return;
    container.innerHTML = hostPhotoData.map((p, idx) => {
        const isCover = idx === 0;
        return `
            <div class="photo-preview-card ${isCover ? 'is-cover' : ''}" style="position:relative; width:120px; height:90px; border-radius:12px; overflow:hidden; border:${isCover ? '2px solid var(--pink-accent)' : '1px solid var(--border-color)'}; background:#f3f3f3; transition: all 0.3s ease; box-shadow: ${isCover ? '0 4px 12px rgba(255, 90, 95, 0.2)' : 'none'}">
                <img src="${p.src}" style="width:100%; height:100%; object-fit:cover; display:block;">
                
                <!-- Cover Badge -->
                ${isCover ? `
                    <div style="position:absolute; top:6px; left:6px; background:var(--pink-accent); color:white; font-size:10px; font-weight:700; padding:3px 6px; border-radius:6px; display:flex; align-items:center; gap:2px; box-shadow: 0 2px 4px rgba(0,0,0,0.1)">
                        <i class="fas fa-star" style="font-size:9px"></i> Cover
                    </div>
                ` : `
                    <button type="button" onclick="event.preventDefault(); event.stopPropagation(); setAsCover(${idx})" title="Set as Cover Photo" style="position:absolute; top:6px; left:6px; background:rgba(0,0,0,0.6); color:#fff; border:none; border-radius:6px; padding:4px; cursor:pointer; display:flex; align-items:center; justify-content:center; width:22px; height:22px; transition: all 0.2s" onmouseover="this.style.background='var(--pink-accent)'" onmouseout="this.style.background='rgba(0,0,0,0.6)'">
                        <i class="far fa-star" style="font-size:11px"></i>
                    </button>
                `}
                
                <!-- Remove Button -->
                <button type="button" onclick="event.preventDefault(); event.stopPropagation(); removePhoto(${idx})" title="Remove Photo" style="position:absolute; top:6px; right:6px; background:rgba(0,0,0,0.6); color:white; border:none; border-radius:6px; padding:4px; cursor:pointer; display:flex; align-items:center; justify-content:center; width:22px; height:22px; transition: all 0.2s" onmouseover="this.style.background='#ff385c'" onmouseout="this.style.background='rgba(0,0,0,0.6)'">
                    <i class="fas fa-times" style="font-size:11px"></i>
                </button>
            </div>
        `;
    }).join('');
}

function setAsCover(index) {
    if (index <= 0 || index >= hostPhotoData.length) return;
    // Move selected photo to index 0
    const selected = hostPhotoData.splice(index, 1)[0];
    hostPhotoData.unshift(selected);
    renderPhotoPreviews();
}

function removePhoto(index) {
    if (index < 0 || index >= hostPhotoData.length) return;
    const removed = hostPhotoData[index];
    if (removed.id) {
        deletedPhotoIds.push(removed.id);
    }
    hostPhotoData.splice(index, 1);
    renderPhotoPreviews();
}

// Close login modal with optional fade and redirect
function closeLoginModal(redirect = false) {
    const modal = document.getElementById('loginModal');
    const overlay = document.getElementById('overlay');
    if (!modal) return;

    try {
        if (overlay) overlay.style.display = 'block';
        modal.style.transition = 'opacity 0.45s ease, transform 0.45s ease';
        if (overlay) overlay.style.transition = 'opacity 0.45s ease';

        modal.style.opacity = '1';
        if (overlay) overlay.style.opacity = '1';

        requestAnimationFrame(() => {
            modal.style.opacity = '0';
            modal.style.transform = 'translate(-50%, -45%) scale(0.98)';
            if (overlay) overlay.style.opacity = '0';
        });

        setTimeout(() => {
            modal.style.display = 'none';
            if (overlay) overlay.style.display = 'none';
            modal.style.transition = '';
            modal.style.opacity = '';
            modal.style.transform = 'translate(-50%, -50%)'; // FIX: Restore centering position
            if (overlay) overlay.style.transition = '';

            if (redirect) {
                if (pendingAction === 'Profile') {
                    const act = pendingAction;
                    pendingAction = null;
                    handleMenuClick(act);
                    return;
                }
                window.location.href = 'index.php';
            }
        }, 480);
    } catch (e) {
        modal.style.display = 'none';
        if (overlay) overlay.style.display = 'none';
        if (redirect) window.location.href = 'index.php';
    }
}

// --- Auth modal (recovery flow) helpers ---
let authCurrentView = 'main';
let isSignUpModeAuth = true;

function openAuthModal(mode = 'main') {
    const lm = document.getElementById('loginModal');
    if (lm) lm.style.display = 'none';

    const am = document.getElementById('authModal');
    if (!am) return;
    am.style.display = 'block';
    document.getElementById('overlay').style.display = 'block';

    if (mode === 'recovery') {
        showAuthView('recovery');
    } else {
        // If mode is main, we should probably just show the login modal instead?
        // But if we must use this modal for some reason:
        showAuthView('main');
    }
}

function showAuthView(view) {
    authCurrentView = view;
    const views = ['main', 'recovery', 'verification', 'reset'];
    views.forEach(v => {
        const el = document.getElementById('auth-view-' + v);
        if (el) el.style.display = (v === view) ? 'block' : 'none';
    });

    const header = document.getElementById('modalHeaderLabel');
    if (header) {
        if (view === 'main') header.innerText = 'Create Account';
        else if (view === 'recovery') header.innerText = 'Reset Password';
        else if (view === 'verification') header.innerText = 'Verification';
        else if (view === 'reset') header.innerText = 'Update Password';
    }
}

function handleAuthBack() {
    if (authCurrentView === 'main') {
        // creating account flow inside auth modal -> go back to initial option or closure
        document.getElementById('authModal').style.display = 'none';
        openLoginModal('signup'); // Re-open main signup modal
    } else if (authCurrentView === 'recovery') {
        // Recover password flow -> Go back to Login
        document.getElementById('authModal').style.display = 'none';
        openLoginModal('login'); // Re-open main login modal
    } else if (authCurrentView === 'verification') {
        showAuthView('recovery');
    } else if (authCurrentView === 'reset') {
        showAuthView('verification');
    }
}

function toggleAuthModeAuth() {
    isSignUpModeAuth = !isSignUpModeAuth;
    const title = document.getElementById('authModalTitle');
    const sub = document.getElementById('authModalSub');
    const submit = document.getElementById('authSubmitBtn');
    const switchBtn = document.getElementById('authSwitchBtn');

    if (isSignUpModeAuth) {
        if (title) title.innerText = 'Create Account';
        if (sub) sub.innerText = 'Join us and find your perfect match';
        document.getElementById('regName').style.display = 'block';
        document.getElementById('regConfirm').style.display = 'block';
        if (submit) submit.innerText = 'Create Account';
        if (switchBtn) switchBtn.innerText = 'sign in';
    } else {
        if (title) title.innerText = 'WELCOME BACK';
        if (sub) sub.innerText = 'Sign in to continue to your account';
        document.getElementById('regName').style.display = 'none';
        document.getElementById('regConfirm').style.display = 'none';
        if (submit) submit.innerText = 'Sign In';
        if (switchBtn) switchBtn.innerText = 'sign up';
    }
    showAuthView('main');
}

async function performAuthLogin() {
    const name = document.getElementById('regName')?.value || '';
    const email = document.getElementById('regEmail')?.value || '';
    const pass = document.getElementById('regPass')?.value || '';
    const confirm = document.getElementById('regConfirm')?.value || '';

    console.log("Attempting registration for:", email);

    if (!email || !pass) {
        showError('Please enter email and password');
        return;
    }

    try {
        showLoading(isSignUpModeAuth ? 'Creating account...' : 'Logging in...');

        let response;
        if (isSignUpModeAuth) {
            if (!name) { hideLoading(); showError('Please enter your name'); return; }
            if (pass !== confirm) { hideLoading(); showError('Passwords do not match'); return; }
            response = await API.auth.register(name, email, pass);
        } else {
            response = await API.auth.login(email, pass);
        }

        hideLoading();

        if (response.success) {
            if (response.data.verification_required) {
                pendingVerificationEmail = email;
                pendingActionType = 'register';
                showAuthView('verification');
                showSuccess('Please check your email for the verification code');
                startOTPTimer('otpTimer');
                return;
            }

            isLoggedIn = true;
            currentUserData = response.data;
            updateUserUI();
            updateSidebarProfileUI(response.data);
            document.getElementById('authModal').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
            showSuccess(isSignUpModeAuth ? 'Account created!' : 'Welcome back!');
            // Reload session to ensure all data including is_admin is fresh
            await checkSession();
            if (pendingAction) { const a = pendingAction; pendingAction = null; handleMenuClick(a); }
        }
    } catch (error) {
        hideLoading();
        showError(error.message || 'Authentication failed');
    }
}

function finishPasswordReset() {
    showToast('Password updated successfully');
    showAuthView('main');
}

// wire the small forgot-password link in the login modal to open auth recovery
document.addEventListener('DOMContentLoaded', () => {
    const fp = document.getElementById('forgotPassword');
    if (fp) fp.onclick = () => { openAuthModal('recovery'); };
});

function showToast(msg) {
    const t = document.getElementById('toast');
    document.getElementById('toastMsg').innerText = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3000);
}


// --- Listing Details Logic ---
let currentDetailId = null;

function openListingDetails(id) {
    const item = mockStays.find(s => s.id === id);
    if (!item) return;
    currentDetailId = id;

    // Populate Info
    document.getElementById('detTitle').innerText = item.desc;
    document.getElementById('detCity').innerText = item.city + ", Algeria";
    document.getElementById('detRating').innerHTML = `<i class="fas fa-star" style="font-size:12px"></i> ${item.rating}`;
    document.getElementById('detPrice').innerText = item.price;
    document.getElementById('detDesc').innerText = item.desc + ". This property offers a unique experience. (Placeholder description to show layout)";

    // Heart State
    const heart = document.getElementById('detailsHeart');
    if (item.fav) { heart.classList.remove('far'); heart.classList.add('fas'); heart.style.color = 'var(--primary-color)'; }
    else { heart.classList.remove('fas'); heart.classList.add('far'); heart.style.color = 'inherit'; }

    // Populate Gallery
    const gallery = document.getElementById('detGallery');
    let images = item.images || [];
    if (images.length === 0) images = [item.img, item.img, item.img, item.img, item.img];
    while (images.length < 5) images.push(item.img); // Ensure at least 5

    gallery.innerHTML = `
                <div class="main-img"><img src="${images[0]}" onclick="viewImageFull(this.src)"></div>
                <div><img src="${images[1]}" onclick="viewImageFull(this.src)"></div>
                <div><img src="${images[2]}" onclick="viewImageFull(this.src)"></div>
                <div><img src="${images[3]}" onclick="viewImageFull(this.src)"></div>
                <div><img src="${images[4]}" onclick="viewImageFull(this.src)"></div>
                <button class="show-photos-btn" onclick="alert('Gallery View coming soon')"><i class="fas fa-th"></i> Show all photos</button>
            `;

    // Check if Mine (Show Edit Bar)
    const oldBar = document.getElementById('editFloatingBar');
    if (oldBar) oldBar.remove();

    if (item.isMine) {
        const bar = document.createElement('div');
        bar.id = 'editFloatingBar';
        bar.className = 'edit-floating-bar';
        bar.innerHTML = `
                    <div>
                        <strong>This is your listing.</strong>
                        <span style="opacity:0.7; font-size:14px; margin-left:8px;">Make changes instantly.</span>
                    </div>
                    <button class="save-btn" style="padding: 10px 24px" onclick="editHome(${item.id}); closeListingDetails();">Edit Listing</button>
                `;
        document.getElementById('arPropertyModal').appendChild(bar);
    }

    document.getElementById('arPropertyModal').style.display = 'block';

    // --- AMENITIES LOGIC ---
    // 1. Get active amenities from item.fullData (from API) or items.amenities (if local/mock)
    let activeKeys = [];
    if (item.fullData && item.fullData.espaces) {
        // API format: objects with type_espace
        activeKeys = item.fullData.espaces.map(e => e.type_espace);
    } else if (item.amenities) {
        // Local mock format: array of strings
        activeKeys = item.amenities;
    }

    // Populate Basics for Sidebar
    const guestsEl = document.getElementById('detGuests');
    const roomsEl = document.getElementById('detRooms');
    const bedsEl = document.getElementById('detBeds');
    const bathsEl = document.getElementById('detBaths');

    // Determine basics source
    const basics = item.basics || (item.fullData ? {
        guests: item.fullData.nombre_invites,
        bedrooms: item.fullData.nombre_chambres,
        beds: item.fullData.nombre_lits,
        baths: item.fullData.nombre_salles_bain
    } : null);

    if (basics) {
        if (guestsEl) guestsEl.innerText = `${basics.guests} guest${basics.guests > 1 ? 's' : ''}`;
        if (roomsEl) roomsEl.innerText = `${basics.bedrooms} bedroom${basics.bedrooms > 1 ? 's' : ''}`;
        if (bedsEl) bedsEl.innerText = `${basics.beds} bed${basics.beds > 1 ? 's' : ''}`;
        if (bathsEl) bathsEl.innerText = `${basics.baths} bath${basics.baths > 1 ? 's' : ''}`;
    }

    // 2. Render Preview (first 10 active)
    const amenityGrid = document.querySelector('.amenities-section .amenity-grid');
    if (amenityGrid) {
        const activeList = AMENITIES_MASTER_LIST.filter(a => activeKeys.includes(a.key));
        const previewList = activeList.slice(0, 10);

        amenityGrid.innerHTML = previewList.map(a => `
            <div class="amenity-item"><i class="${a.icon}"></i> ${a.label}</div>
        `).join('');
    }

    const modalGrid = document.querySelector('#arAmenitiesModal .grid') || document.querySelector('#arAmenitiesModal > div > div:nth-child(2)');
    // Note: The modal structure in HTML might be nested.
    // Let's re-target the grid container inside the modal more robustly.
    // The HTML showed: <div style="display:grid; gap:40px;"> ... categories ... </div>
    // We will replace that entire content with a flat list or categorized list.
    // For simplicity and user request, we can use a simpler grid for now, or just inject into the existing container if it has an ID.
    // The existing modal has categories. It's better to clear it and just show a nice list or trying to keep categories is harder without mapping.
    // Let's replace the inner HTML of the scrollable container.

    const modalContainer = document.querySelector('#arAmenitiesModal > div'); // The white box
    if (modalContainer) {
        const headerHTML = `
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; padding-bottom:24px; border-bottom:1px solid var(--border-color);">
                <h2 style="font-size:24px; margin:0;">What this place offers</h2>
                <button onclick="document.getElementById('arAmenitiesModal').style.display='none'" style="background:none; border:none; font-size:24px; cursor:pointer;">✕</button>
            </div>
        `;

        // Group by category
        const categories = {};
        AMENITIES_MASTER_LIST.forEach(item => {
            if (!categories[item.category]) categories[item.category] = [];
            categories[item.category].push(item);
        });

        let gridsHTML = '<div style="display:grid; gap:40px;">';

        for (const [catName, items] of Object.entries(categories)) {
            gridsHTML += `
                <div>
                    <h3 style="font-size:20px; font-weight:600; margin-bottom:20px; color:var(--text-color);">${catName}</h3>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
            `;

            items.forEach(a => {
                const isActive = activeKeys.includes(a.key);
                const style = isActive ? '' : 'text-decoration: line-through; opacity: 0.5;';
                const iconStyle = isActive ? 'color: var(--text-color);' : 'color: #ccc;';

                gridsHTML += `
                    <div style="display:flex; align-items:center; gap:16px; padding:16px 0; border-bottom:1px solid var(--border-color); ${style}">
                        <i class="${a.icon}" style="width:28px; font-size:22px; ${iconStyle}"></i>
                        <span style="color:var(--text-color);">${a.label}</span>
                    </div>
                `;
            });

            gridsHTML += `
                    </div>
                </div>
            `;
        }

        gridsHTML += '</div>';

        modalContainer.innerHTML = headerHTML + gridsHTML;
    }

    document.body.style.overflow = 'hidden';
}

requestAnimationFrame(() => { console.log('Details logic loaded'); });

function closeListingDetails() {
    document.getElementById('arPropertyModal').style.display = 'none';
    document.body.style.overflow = '';
}

async function toggleLikeDetails() {
    if (currentDetailId) {
        if (!isLoggedIn) {
            closeListingDetails();
            closeArModal();
            openLoginModal('login');
            return;
        }

        const item = mockStays.find(s => s.id === currentDetailId);
        if (item) {
            const wasFavorite = item.fav;
            try {
                if (wasFavorite) {
                    await API.favorites.remove(currentDetailId);
                    item.fav = false;
                    showToast('Removed from Favorites');
                } else {
                    await API.favorites.add(currentDetailId);
                    item.fav = true;
                    showToast('Saved to Favorites');
                }

                // Update UI elements
                const heart = document.getElementById('detailsHeart');
                if (item.fav) {
                    heart.classList.remove('far');
                    heart.classList.add('fas');
                    heart.style.color = 'var(--primary-color)';
                } else {
                    heart.classList.remove('fas');
                    heart.classList.add('far');
                    heart.style.color = 'inherit';
                }

                // Update Grid UI if visible
                const gridBtn = document.querySelector(`.card button[onclick*="${currentDetailId}"]`);
                if (gridBtn) {
                    if (item.fav) gridBtn.classList.add('active');
                    else gridBtn.classList.remove('active');
                }

            } catch (error) {
                showError(error.message || 'Failed to update favorites');
                item.fav = wasFavorite;
            }
        }
    }
}

function shareListing() {
    showToast('Link copied to clipboard!');
}

function viewImageFull(src) {
    // Placeholder
}
// Event Delegation for Image Clicks using existing mainGrid
document.addEventListener('DOMContentLoaded', () => {
    const grid = document.getElementById('mainGrid');
    if (grid) {
        grid.addEventListener('click', function (e) {
            // Check if click is on card image or wrapper
            const imgWrapper = e.target.closest('.card-img-wrapper');
            if (imgWrapper) {
                e.stopPropagation(); // Stop bubbling to prevent other clicks
                // Find the card element to get the ID
                const card = imgWrapper.closest('.card');
                // We need the ID. We can extract it from the Favorite button onclick attribute
                const btn = card.querySelector('.fav-btn');
                if (btn) {
                    const onclickStr = btn.getAttribute('onclick');
                    const match = onclickStr.match(/(\d+)/);
                    if (match && match[1]) {
                        openArModal(parseInt(match[1]));
                    }
                }
            }
        });
    }
});

async function openArModal(id) {
    currentDetailId = id;

    // Show Loading or Skeleton
    showLoading('Loading property details...');

    try {
        // Fetch full details from API
        const dates = {};
        if (bookingSearchData.checkIn) dates.date_arrivee = bookingSearchData.checkIn;
        if (bookingSearchData.checkOut) dates.date_depart = bookingSearchData.checkOut;
        const response = await API.properties.get(id, dates);
        if (!response.success || !response.data) {
            throw new Error(response.message || 'Failed to load property details');
        }

        const stay = {
            ...response.data,
            // Adapter for existing logic
            id: response.data.id,
            city: response.data.wilaya,
            price: parseFloat(response.data.prix_par_nuit).toLocaleString('fr-DZ'),
            rating: response.data.moyenne_note || 0,
            img: getImageSrc(response.data.image_principale),
            fav: response.data.is_favorited || false,
            desc: response.data.titre || response.data.description?.substring(0, 50) || 'Beautiful property',
            isMine: response.data.is_mine || false,
            // Amenities from API
            amenities: response.data.espaces ? response.data.espaces.map(e => e.type_espace) : [],
            fullData: response.data,
            // Map basics for UI
            basics: {
                guests: response.data.nombre_invites,
                bedrooms: response.data.nombre_chambres,
                beds: response.data.nombre_lits,
                baths: response.data.nombre_salles_bain
            }
        };

        // Update local cache so it can be used for booking later
        const stayIndex = mockStays.findIndex(s => s.id == stay.id);
        if (stayIndex > -1) {
            mockStays[stayIndex] = stay;
        } else {
            mockStays.push(stay);
        }

        // Fetch reviews dynamically
        let propertyReviews = [];
        let totalReviewsCount = 0;
        let canReview = false;
        let eligibleReservationId = null;
        try {
            const revRes = await API.reviews.getPropertyReviews(id);
            if (revRes && revRes.success && revRes.data) {
                propertyReviews = revRes.data.reviews || [];
                totalReviewsCount = revRes.data.total_reviews || 0;
                canReview = revRes.data.can_review || false;
                eligibleReservationId = revRes.data.eligible_reservation_id || null;
                // Store globally for editing
                window.currentPropertyReviews = propertyReviews;
            }
        } catch (err) {
            console.error("Failed to load reviews", err);
        }

        // Hide Loading
        hideLoading();

        // Populate Main Data

        // Populate Images
        const images = (response.data.images && response.data.images.length > 0)
            ? response.data.images.map(img => img.url_image)
            : [stay.img]; // Fallback to main image

        // Store globally for gallery modal
        window.currentPropertyImages = images;

        // Ensure we have enough images for the grid by repeating if necessary (or leave as is if design supports it)
        // For this specific 5-image grid, we will fill with available, then cycle
        const getImg = (idx) => images[idx % images.length];

        const img1 = getImageSrc(getImg(0));
        const img2 = getImageSrc(getImg(1));
        const img3 = getImageSrc(getImg(2));
        const img4 = getImageSrc(getImg(3));
        const img5 = getImageSrc(getImg(4));

        // Global getImageSrc is used instead

        document.getElementById('arImgMain').src = img1;
        document.getElementById('arImg2').src = img2;
        document.getElementById('arImg3').src = img3;
        document.getElementById('arImg4').src = img4;
        document.getElementById('arImg5').src = img5;

        document.getElementById('arTitle').innerText = stay.isMine ? stay.desc : `Entire ${stay.categorie || 'home'} in ${stay.city}, Algeria`;
        document.getElementById('arCity').innerText = stay.city;
        if (document.getElementById('arCityMap')) document.getElementById('arCityMap').innerText = stay.city;

        // Description
        const descEl = document.getElementById('arDesc');
        if (descEl) {
            descEl.innerText = stay.desc;
            descEl.classList.remove('expanded'); // Reset expansion
            // Only show 'show more' if description is long enough (simple check)
            const showMoreBtn = document.querySelector('.show-more-link');
            if (showMoreBtn) showMoreBtn.style.display = stay.desc.length > 150 ? 'flex' : 'none';
        }

        // Updated Map
        if (document.getElementById('detailMap')) {
            setTimeout(() => {
                const lat = parseFloat(stay.fullData.latitude) || 36.7538;
                const lng = parseFloat(stay.fullData.longitude) || 3.0588;
                const city = stay.city;
                const adresse = stay.fullData.adresse || city;

                if (!detailMapInstance) {
                    detailMapInstance = L.map('detailMap', {
                        zoomControl: false,
                        attributionControl: false
                    }).setView([lat, lng], 14);

                    const streets = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}', {
                        attribution: 'Tiles &copy; Esri'
                    });
                    const satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                        attribution: 'Tiles &copy; Esri'
                    });

                    streets.addTo(detailMapInstance);
                    L.control.layers({ "Streets": streets, "Satellite": satellite }, null, { position: 'topright' }).addTo(detailMapInstance);
                    L.control.zoom({ position: 'bottomright' }).addTo(detailMapInstance);

                    detailMarkerInstance = L.marker([lat, lng]).addTo(detailMapInstance);
                } else {
                    detailMapInstance.invalidateSize();
                    detailMapInstance.setView([lat, lng], 14);
                    detailMarkerInstance.setLatLng([lat, lng]);
                }

                if (adresse) {
                    const directionsUrl = `https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}`;
                    detailMarkerInstance.bindPopup(`
                        <div style="padding: 10px;">
                            <b style="font-size: 16px;">${city}</b><br>
                            <span style="font-size: 13px; opacity: 0.8;">${adresse}</span>
                            <div style="margin-top: 10px;">
                                <a href="${directionsUrl}" target="_blank" 
                                    style="display: inline-block; background: var(--primary-color); color: #000; padding: 8px 12px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 12px;">
                                    <i class="fas fa-directions"></i> Get Directions
                                </a>
                            </div>
                        </div>
                    `).openPopup();
                }
            }, 300);
        }

        if (document.getElementById('arRatingMain')) document.getElementById('arRatingMain').innerText = stay.rating;
        if (document.getElementById('arRatingReviews')) document.getElementById('arRatingReviews').innerText = stay.rating;

        // --- Render Dynamic Reviews ---
        const reviewsSectionHeader = document.getElementById('arReviewsSectionHeader');
        const reviewsPreviewGrid = document.getElementById('arReviewsPreviewGrid');
        const reviewsModalGrid = document.getElementById('arReviewsModalGrid');
        const reviewsShowAllBtn = document.getElementById('arReviewsShowAllBtn');
        const addReviewContainer = document.getElementById('arAddReviewContainer');

        if (addReviewContainer) {
            if (canReview && eligibleReservationId) {
                addReviewContainer.style.display = 'block';
                
                const titleEl = addReviewContainer.querySelector('h3');
                if (titleEl) titleEl.innerText = 'Share your experience';
                
                const submitBtn = document.getElementById('arSubmitReviewBtn');
                if (submitBtn) {
                    submitBtn.innerText = 'Submit Review';
                    submitBtn.onclick = () => submitPropertyReview(id, eligibleReservationId);
                }
                if (document.getElementById('arReviewText')) document.getElementById('arReviewText').value = '';
                if (typeof setReviewRating === 'function') setReviewRating(0);
            } else {
                addReviewContainer.style.display = 'none';
            }
        }

        if (reviewsSectionHeader) {
            reviewsSectionHeader.innerHTML = totalReviewsCount > 0 
                ? `<i class="fas fa-star"></i> ${stay.rating} · ${totalReviewsCount} review${totalReviewsCount > 1 ? 's' : ''}` 
                : `<i class="fas fa-star"></i> No reviews yet`;
        }

        function generateReviewHTML(r) {
            const dateStr = new Date(r.date_avis).toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
            const avatar = r.utilisateur_photo ? getImageSrc(r.utilisateur_photo) : getInitialsAvatar(r.utilisateur_nom || 'G');
            const noteStars = '★'.repeat(r.note) + '☆'.repeat(5 - r.note);
            
            // Check if it's my review
            const isMine = typeof currentUserData !== 'undefined' && currentUserData && parseInt(r.utilisateur_id) === parseInt(currentUserData.id);
            const editBtn = isMine ? `<button onclick="editMyReview(${r.id})" style="background:none; border:none; color:var(--primary-color); cursor:pointer; font-size:14px; text-decoration:underline; margin-left: 10px;">Edit</button>` : '';

            return `
                <div style="padding-bottom:20px; border-bottom:1px solid var(--border-color);">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px;">
                        <div style="display:flex; gap:12px;">
                            <img src="${avatar}" style="width:48px; height:48px; border-radius:50%; object-fit:cover;">
                            <div>
                                <h4 style="margin:0; font-size:16px; font-weight:600; color:var(--text-color);">${r.utilisateur_nom || 'Guest'}</h4>
                                <div style="color:#717171; font-size:14px;">${dateStr} ${editBtn}</div>
                            </div>
                        </div>
                        <div style="color:var(--primary-color); font-size:14px;">${noteStars}</div>
                    </div>
                    <p style="line-height:1.6; color:var(--text-color); opacity:0.9; margin:0;">${r.commentaire}</p>
                </div>
            `;
        }

        if (reviewsPreviewGrid) {
            reviewsPreviewGrid.innerHTML = '';
            if (propertyReviews.length > 0) {
                const previewReviews = propertyReviews.slice(0, 4);
                reviewsPreviewGrid.innerHTML = previewReviews.map(generateReviewHTML).join('');
                if (reviewsShowAllBtn) {
                    reviewsShowAllBtn.style.display = propertyReviews.length > 4 ? 'block' : 'none';
                    reviewsShowAllBtn.innerText = `Show all ${totalReviewsCount} reviews`;
                }
            } else {
                reviewsPreviewGrid.innerHTML = '<div style="grid-column: 1 / -1; text-align: left; opacity: 0.7; font-size: 16px; padding: 20px 0;">This property has no reviews yet.</div>';
                if (reviewsShowAllBtn) reviewsShowAllBtn.style.display = 'none';
            }
        }

        if (reviewsModalGrid) {
            reviewsModalGrid.innerHTML = propertyReviews.map(generateReviewHTML).join('');
        }
        // ------------------------------

        // Price Populating & Store for Booking Calculations
        // Robust parsing: remove all non-digits (handles spaces, commas, dots)
        const priceCleaned = stay.price.toString().replace(/\D/g, '');
        const priceNum = parseInt(priceCleaned) || 0;
        currentPropertyPrice = priceNum; // Store globally for booking calculations

        // Check if we have search data (user came from search)
        const hasSearchData = bookingSearchData.checkIn && bookingSearchData.checkOut;

        // Update date displays
        const checkInEl = document.getElementById('arCheckIn');
        const checkOutEl = document.getElementById('arCheckOut');

        if (hasSearchData) {
            // Use search data
            if (checkInEl) {
                checkInEl.innerText = bookingSearchData.checkIn;
                checkInEl.style.opacity = '1';
            }
            if (checkOutEl) {
                checkOutEl.innerText = bookingSearchData.checkOut;
                checkOutEl.style.opacity = '1';
            }

            // Update guest counts from search
            if (bookingSearchData.guestCounts) {
                guestCounts.adults = bookingSearchData.guestCounts.adults || 1;
                guestCounts.children = bookingSearchData.guestCounts.children || 0;

                // Update UI
                if (document.getElementById('adultsCount')) document.getElementById('adultsCount').innerText = guestCounts.adults;
                if (document.getElementById('childrenCount')) document.getElementById('childrenCount').innerText = guestCounts.children;
                updateGuestPreviewText();
            }

            // Show price details and calculate
            const priceSection = document.getElementById('priceDetailsSection');
            if (priceSection) {
                priceSection.style.display = 'block';
            }
            updateBookingCalculation();
        } else {
            // No search data - show placeholders
            if (checkInEl) {
                checkInEl.innerText = 'Add date';
                checkInEl.style.opacity = '0.5';
            }
            if (checkOutEl) {
                checkOutEl.innerText = 'Add date';
                checkOutEl.style.opacity = '0.5';
            }

            // Reset guest counts to default
            guestCounts.adults = 1;
            guestCounts.children = 0;
            guestCounts.infants = 0;
            document.getElementById('adultsCount').innerText = 1;

            // Refresh Flatpickr if they exist with search dates
            if (checkInPicker && bookingSearchData.checkIn) {
                checkInPicker.setDate(bookingSearchData.checkIn);
            }
            if (checkOutPicker && bookingSearchData.checkOut) {
                checkOutPicker.setDate(bookingSearchData.checkOut);
            }
            if (document.getElementById('childrenCount')) document.getElementById('childrenCount').innerText = 0;
            updateGuestPreviewText();

            // Hide price details
            const priceSection = document.getElementById('priceDetailsSection');
            if (priceSection) {
                priceSection.style.display = 'none';
            }
        }

        // Host Info
        let hostName = "Host";
        let hostImg = ""; // Will be set below

        if (stay.fullData) {
            hostName = stay.fullData.proprietaire_nom || "Host";
            if (stay.fullData.proprietaire_photo) {
                hostImg = getImageSrc(stay.fullData.proprietaire_photo);
            }
        } else if (stay.isMine) {
            const sidebarName = document.getElementById('sidebarName');
            hostName = sidebarName ? sidebarName.innerText : "You";
            // Try to get avatar from sidebar background image
            const sidebarAvatar = document.getElementById('sidebarAvatar');
            if (sidebarAvatar) {
                const style = window.getComputedStyle(sidebarAvatar);
                const bg = style.backgroundImage;
                if (bg && bg !== 'none') {
                    hostImg = bg.slice(5, -2); // url("...") -> ...
                }
            }
            if (!hostImg) hostImg = getInitialsAvatar(hostName);
        } else {
            // Default generic fallback
            hostImg = getInitialsAvatar(hostName);
        }

        if (document.getElementById('arHostImg')) document.getElementById('arHostImg').src = hostImg;
        if (document.getElementById('arHostName')) document.getElementById('arHostName').innerText = hostName;
        if (document.getElementById('arHostNameLarge')) document.getElementById('arHostNameLarge').innerText = hostName;
        if (document.getElementById('arHostImgLarge')) document.getElementById('arHostImgLarge').src = hostImg;

        // Dynamic Host Stats based on owner ID for consistency
        const ownerId = stay.fullData ? stay.fullData.proprietaire_id : id;
        const hostReviews = (ownerId * 7) % 50 + 5;
        const hostRating = (4.5 + (ownerId % 5) * 0.1).toFixed(2);
        
        // Use real hosting date if available
        const hostSinceYear = (stay.fullData && stay.fullData.proprietaire_since) 
            ? stay.fullData.proprietaire_since 
            : ((ownerId % 10) + 1);

        if (document.getElementById('arHostReviewsCount')) document.getElementById('arHostReviewsCount').innerText = hostReviews;
        if (document.getElementById('arHostRatingStats')) document.getElementById('arHostRatingStats').innerText = hostRating + ' ★';
        if (document.getElementById('arHostYearsStats')) document.getElementById('arHostYearsStats').innerText = hostSinceYear;
        if (document.getElementById('arHostReviewsHeader')) document.getElementById('arHostReviewsHeader').innerText = `${hostReviews} Reviews`;
        
        const tenureText = (stay.fullData && stay.fullData.proprietaire_since)
            ? `Hosting since ${stay.fullData.proprietaire_since}`
            : `${hostSinceYear} year${hostSinceYear > 1 ? 's' : ''} of hosting`;
            
        if (document.getElementById('arHostTenure')) document.getElementById('arHostTenure').innerText = tenureText;

        // Real Bio from Database
        const hostBio = (stay.fullData && stay.fullData.proprietaire_bio) 
            ? stay.fullData.proprietaire_bio 
            : "Hello! I am a professional host who loves welcoming guests from all over the world. I always strive to provide a unique and comfortable stay experience.";
        
        if (document.getElementById('arHostBio')) document.getElementById('arHostBio').innerText = hostBio;

        // Real Profession and Languages
        const hostProf = (stay.fullData && stay.fullData.proprietaire_profession) ? stay.fullData.proprietaire_profession : "";
        const hostLangs = (stay.fullData && stay.fullData.proprietaire_languages) ? stay.fullData.proprietaire_languages : "";

        if (document.getElementById('arHostProfession')) {
            document.getElementById('arHostProfession').innerText = hostProf || "Not specified";
            document.getElementById('arHostProfessionRow').style.display = hostProf ? 'flex' : 'none';
        }
        if (document.getElementById('arHostLanguages')) {
            document.getElementById('arHostLanguages').innerText = hostLangs || "Not specified";
            document.getElementById('arHostLanguagesRow').style.display = hostLangs ? 'flex' : 'none';
        }

        // Contact Button Link removed to allow inline onclick to work
        // Amenities Logic - Render Modal Payload using AMENITIES_MASTER_LIST
        // This replaces the old selector based logic which depended on hardcoded HTML in index.php
        // Amenities Logic - Render Modal Payload using AMENITIES_MASTER_LIST with Categories
        let amenities = stay.amenities || [];

        const modalBody = document.querySelector('#arAmenitiesModal > div');
        if (modalBody) {
            const headerHTML = `
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; padding-bottom:24px; border-bottom:1px solid var(--border-color);">
                    <h2 style="font-size:24px; margin:0;">What this place offers</h2>
                    <button onclick="document.getElementById('arAmenitiesModal').style.display='none'" style="background:none; border:none; font-size:24px; cursor:pointer;">✕</button>
                </div>
            `;

            // Group by category
            const categories = {};
            AMENITIES_MASTER_LIST.forEach(item => {
                if (!categories[item.category]) categories[item.category] = [];
                categories[item.category].push(item);
            });

            let gridsHTML = '<div style="display:grid; gap:40px;">';

            for (const [catName, items] of Object.entries(categories)) {
                gridsHTML += `
                    <div>
                        <h3 style="font-size:20px; font-weight:600; margin-bottom:20px; color:var(--text-color);">${catName}</h3>
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                `;

                items.forEach(a => {
                    const isActive = amenities.includes(a.key);
                    const style = isActive ? '' : 'text-decoration: line-through; opacity: 0.5;';
                    const iconStyle = isActive ? 'color: var(--text-color);' : 'color: #ccc;';

                    gridsHTML += `
                        <div style="display:flex; align-items:center; gap:16px; padding:16px 0; border-bottom:1px solid var(--border-color); ${style}">
                            <i class="${a.icon}" style="width:28px; font-size:22px; ${iconStyle}"></i>
                            <span style="color:var(--text-color);">${a.label}</span>
                        </div>
                    `;
                });

                gridsHTML += `
                        </div>
                    </div>
                `;
            }

            gridsHTML += '</div>'; // Close main gap wrapper

            modalBody.innerHTML = headerHTML + gridsHTML;
        }

        // 5. Populate Amenities Preview (Icons on main details page)
        const amenityGrid = document.getElementById('arAmenityGrid');
        if (amenityGrid) {
            const activeList = AMENITIES_MASTER_LIST.filter(a => amenities.includes(a.key));
            const previewList = activeList.slice(0, 6); // Just 6 for preview
            amenityGrid.innerHTML = previewList.map(a => `
                <div class="amenity-item"><i class="${a.icon}"></i> ${a.label}</div>
            `).join('');
        }

        // Populate Basics (Guests, Bedrooms, etc.)
        if (stay.basics) {
            if (document.getElementById('arGuests')) document.getElementById('arGuests').innerText = `${stay.basics.guests} guest${stay.basics.guests > 1 ? 's' : ''}`;
            if (document.getElementById('arBedrooms')) document.getElementById('arBedrooms').innerText = `${stay.basics.bedrooms} bedroom${stay.basics.bedrooms > 1 ? 's' : ''}`;
            if (document.getElementById('arBeds')) document.getElementById('arBeds').innerText = `${stay.basics.beds} bed${stay.basics.beds > 1 ? 's' : ''}`;
            if (document.getElementById('arBaths')) document.getElementById('arBaths').innerText = `${stay.basics.baths} bath${stay.basics.baths > 1 ? 's' : ''}`;
        }

        // Populate Highlights
        const highlightsCont = document.getElementById('arHighlightsContainer');
        if (highlightsCont) {
            highlightsCont.innerHTML = '';
            const highlights = stay.highlights || [];
            if (highlights.length > 0) {
                highlights.forEach(hKey => {
                    const h = highlightsMap[hKey];
                    if (h) {
                        highlightsCont.innerHTML += `
                        <div class="feature-row">
                            <div class="feature-icon"><i class="${h.icon}"></i></div>
                            <div class="feature-content">
                                <h3>${h.label}</h3>
                                <p>${h.desc}</p>
                            </div>
                        </div>
                    `;
                    }
                });
            } else {
                // Fallback for older listings: Show standard checking highlight
                highlightsCont.innerHTML = `
                <div class="feature-row">
                    <div class="feature-icon"><i class="fas fa-key"></i></div>
                    <div class="feature-content">
                        <h3>Great check-in experience</h3>
                        <p>Guests who stayed here recently loved the seamless start to their trip.</p>
                    </div>
                </div>
            `;
            }
        }

        // Update Amenities Preview (Top 10)
        const previewGrid = document.querySelector('.amenity-grid');
        if (previewGrid) {
            previewGrid.innerHTML = '';
            // Filter master list to get objects for active amenities
            const activeAmenities = AMENITIES_MASTER_LIST.filter(a => amenities.includes(a.key));

            activeAmenities.slice(0, 10).forEach(a => {
                previewGrid.innerHTML += `<div class="amenity-item"><i class="${a.icon}"></i> ${a.label}</div>`;
            });
        }
        
        // Update Things to Know (Important Information)
        const d_cancel = (stay.fullData && stay.fullData.cancel_policy) ? stay.fullData.cancel_policy : "Free cancellation before Sept 2. Cancel before check-in on Sept 3 for a partial refund.\n\nReview this host's full policy for more details.";
        const d_rules = (stay.fullData && stay.fullData.house_rules) ? stay.fullData.house_rules : "Check-in after 4:00 PM\nCheckout before 11:00 AM\n4 guests maximum";
        const d_safety = (stay.fullData && stay.fullData.safety_rules) ? stay.fullData.safety_rules : "Carbon monoxide alarm\nSmoke alarm";
        
        if(document.getElementById('arCancelPolicy')) document.getElementById('arCancelPolicy').innerText = d_cancel;
        if(document.getElementById('arHouseRules')) document.getElementById('arHouseRules').innerText = d_rules;
        if(document.getElementById('arSafetyRules')) document.getElementById('arSafetyRules').innerText = d_safety;

        // Update date pickers with blocked dates
        if (response.data.blocked_dates && response.data.blocked_dates.length > 0) {
            const disableRanges = response.data.blocked_dates.map(range => ({
                from: range.date_arrivee,
                to: range.date_depart
            }));

            if (typeof checkInPicker !== 'undefined' && checkInPicker) {
                checkInPicker.set('disable', disableRanges);
            }
            if (typeof checkOutPicker !== 'undefined' && checkOutPicker) {
                checkOutPicker.set('disable', disableRanges);
            }
        } else {
            // Clear any previously disabled dates
            if (typeof checkInPicker !== 'undefined' && checkInPicker) {
                checkInPicker.set('disable', []);
            }
            if (typeof checkOutPicker !== 'undefined' && checkOutPicker) {
                checkOutPicker.set('disable', []);
            }
        }

        // Booked status banner & reserve button state
        updateBookingStatusUI(response.data, stay.isMine);

        // Show Modal
        const modal = document.getElementById('arPropertyModal');
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';

    } catch (error) {
        hideLoading();
        showError('Could not load property details: ' + error.message);
    }
}

function updateBookingStatusUI(data, isMine) {
    // Remove any old booked banner (we no longer use it, relying on calendar disabled dates)
    const oldBookedBanner = document.getElementById('arBookedBanner');
    if (oldBookedBanner) oldBookedBanner.remove();

    const reserveBtn = document.getElementById('btnReserve');
    if (reserveBtn) {
        const isBooked = data.is_booked && !isMine;
        reserveBtn.disabled = isMine || isBooked;

        if (isMine) {
            reserveBtn.innerText = 'Your listing';
        } else if (isBooked) {
            reserveBtn.innerText = 'Not available for these dates';
            reserveBtn.style.background = '#ccc';
        } else {
            reserveBtn.innerText = 'Reserve';
            reserveBtn.style.background = '';
        }

        reserveBtn.style.cursor = (isMine || isBooked) ? 'not-allowed' : 'pointer';
    }
}

async function refreshAvailability() {
    if (!currentDetailId) return;

    // Only refresh if both dates are selected
    if (!bookingSearchData.checkIn || !bookingSearchData.checkOut) return;

    try {
        const dates = {
            date_arrivee: bookingSearchData.checkIn,
            date_depart: bookingSearchData.checkOut
        };
        const response = await API.properties.get(currentDetailId, dates);
        if (response.success) {
            const stay = mockStays.find(s => s.id == currentDetailId);
            updateBookingStatusUI(response.data, stay ? stay.isMine : false);
        }
    } catch (error) {
        console.error('Failed to refresh availability:', error);
    }
}

function closeArModal() {
    const modal = document.getElementById('arPropertyModal');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
}

/* --- Reviews Interactions --- */
let currentReviewRating = 0;
function setReviewRating(rating) {
    currentReviewRating = rating;
    const stars = document.querySelectorAll('#arReviewStars i');
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.remove('far');
            star.classList.add('fas');
            star.style.color = '#ffb400';
        } else {
            star.classList.remove('fas');
            star.classList.add('far');
            star.style.color = '#ccc';
        }
    });
}

async function submitPropertyReview(propertyId, reservationId) {
    if (currentReviewRating === 0) {
        showError('Please select a star rating');
        return;
    }
    const textEl = document.getElementById('arReviewText');
    const text = textEl ? textEl.value.trim() : '';
    if (!text) {
        showError('Please write a review');
        return;
    }

    const btn = document.getElementById('arSubmitReviewBtn');
    if (btn) {
        btn.disabled = true;
        btn.innerText = 'Submitting...';
    }

    try {
        const res = await API.reviews.addPropertyReview(reservationId, currentReviewRating, text);
        if (res.success) {
            showToast('Review submitted successfully!');
            // Refresh the modal to show the new review and hide the form
            openArModal(propertyId); 
        } else {
            throw new Error(res.message || 'Failed to submit review');
        }
    } catch (err) {
        showError(err.message);
        if (btn) {
            btn.disabled = false;
            btn.innerText = 'Submit Review';
        }
    }
}

function editMyReview(reviewId) {
    if (!window.currentPropertyReviews) return;
    const review = window.currentPropertyReviews.find(r => r.id == reviewId);
    if (!review) return;

    // Show the add review container if it was hidden
    const addReviewContainer = document.getElementById('arAddReviewContainer');
    if (addReviewContainer) {
        addReviewContainer.style.display = 'block';
        addReviewContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        // Populate text
        const textEl = document.getElementById('arReviewText');
        if (textEl) textEl.value = review.commentaire;
        
        // Populate rating
        setReviewRating(review.note);
        
        // Update title
        const titleEl = addReviewContainer.querySelector('h3');
        if (titleEl) titleEl.innerText = 'Edit your experience';
        
        // Update button
        const submitBtn = document.getElementById('arSubmitReviewBtn');
        if (submitBtn) {
            submitBtn.innerText = 'Update Review';
            submitBtn.onclick = () => updatePropertyReviewAction(currentDetailId, review.id);
        }
    }
}

async function updatePropertyReviewAction(propertyId, reviewId) {
    if (currentReviewRating === 0) {
        showError('Please select a star rating');
        return;
    }
    const textEl = document.getElementById('arReviewText');
    const text = textEl ? textEl.value.trim() : '';
    if (!text) {
        showError('Please write a review');
        return;
    }

    const btn = document.getElementById('arSubmitReviewBtn');
    if (btn) {
        btn.disabled = true;
        btn.innerText = 'Updating...';
    }

    try {
        const res = await API.reviews.updatePropertyReview(reviewId, currentReviewRating, text);
        if (res.success) {
            showToast('Review updated successfully!');
            openArModal(propertyId); 
        } else {
            throw new Error(res.message || 'Failed to update review');
        }
    } catch (err) {
        showError(err.message);
        if (btn) {
            btn.disabled = false;
            btn.innerText = 'Update Review';
        }
    }
}

/* --- New Interactions --- */

// function saveListing() { ... } // REMOVED redundant function

function shareListing() {
    navigator.clipboard.writeText(window.location.href);
    showToast('Link copied to clipboard');
}

// Translation Effect
const translateLink = document.querySelector('.translate-link');
if (translateLink) {
    translateLink.addEventListener('click', function () {
        const text = this.innerText;
        if (text.includes('Original')) {
            this.innerText = 'Translating...';
            setTimeout(() => {
                this.innerText = 'Show machine translation';
                document.getElementById('arDesc').innerText = "Welcome to your perfect getaway! This cozy and stylish place blends modern comfort with family-friendly warmth. Featuring stunning views and a location close to all attractions. Enjoy peace and relaxation in our carefully designed space.";
            }, 1000);
        } else {
            this.innerText = 'Show original text';
            document.getElementById('arDesc').innerText = "Welcome to your perfect getaway! This cozy and stylish place blends modern comfort with family-friendly warmth. Featuring stunning views and a location close to all attractions. Enjoy peace and relaxation in our carefully designed space.";
        }
    });
}

// Show Full Description
const showMoreLink = document.querySelector('.show-more-link');
if (showMoreLink) {
    showMoreLink.addEventListener('click', function () {
        const desc = document.getElementById('arDesc');
        if (desc && !desc.classList.contains('expanded')) {
            desc.innerText += " \n\nThis property includes a fully equipped kitchen and spacious bedrooms with luxury linens. High-speed Wi-Fi and a smart TV with a Netflix subscription are available. The neighborhood is very quiet and safe.";
            desc.classList.add('expanded');
            this.style.display = 'none'; // Hide button after expand
        }
    });
}

// Show All Photos
const showPhotosBtn = document.querySelector('.show-photos-btn');
if (showPhotosBtn) {
    showPhotosBtn.addEventListener('click', function () {
        showToast('Photo gallery will open (Preview)');
    });
}

// Amenities Button - OPEN MODAL
document.querySelectorAll('.btn-outline').forEach(btn => {
    if (btn.innerText.includes('amenities')) {
        btn.onclick = function () {
            document.getElementById('arAmenitiesModal').style.display = 'flex';
        }
    }
    if (btn.innerText.includes('reviews')) {
        btn.onclick = function () {
            document.getElementById('arReviewsModal').style.display = 'flex';
        }
    }
});

// ========================================
// NEW BOOKING SYSTEM FUNCTIONS
// ========================================

// Guest Counter State
let guestCounts = {
    adults: 1,
    children: 0
};

// Toggle Guest Counter Dropdown
document.addEventListener('DOMContentLoaded', function () {
    const trigger = document.getElementById('guestsDropdownTrigger');
    const dropdown = document.getElementById('guestCounterDropdown');

    if (trigger && dropdown) {
        trigger.addEventListener('click', function (e) {
            e.stopPropagation();
            dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function (e) {
            if (!trigger.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });
    }
});

// Update Guest Count
function updateGuestCount(type, delta) {
    const currentCount = guestCounts[type];
    const newCount = currentCount + delta;

    // Validation
    if (type === 'adults' && newCount < 1) return; // Minimum 1 adult
    if (newCount < 0) return; // No negative numbers
    if (type === 'adults' && newCount > 16) return; // Maximum 16 adults
    if (type === 'children' && newCount > 15) return; // Maximum 15 children

    guestCounts[type] = newCount;

    // Update UI
    const countEl = document.getElementById(type + 'Count');
    if (countEl) countEl.innerText = newCount;
    updateGuestPreviewText();
    updateBookingCalculation();
}

// Update Guest Preview Text (English)
function updateGuestPreviewText() {
    const total = guestCounts.adults + guestCounts.children;
    let text = '';

    if (total === 1) {
        text = '1 guest';
    } else if (total > 1) {
        text = total + ' guests';
    } else {
        text = '0 guests';
    }

    document.getElementById('arGuestCountPreview').innerText = text;

    // Update search data
    bookingSearchData.guests = total;
}

// Initialize Date Pickers using Flatpickr
let checkInPicker, checkOutPicker;

document.addEventListener('DOMContentLoaded', function () {
    const checkInInput = document.getElementById('checkInInput');
    const checkOutInput = document.getElementById('checkOutInput');

    if (checkInInput && typeof flatpickr !== 'undefined') {
        checkInPicker = flatpickr(checkInInput, {
            dateFormat: "Y/m/d",
            minDate: "today",
            defaultDate: bookingSearchData.checkIn || null,
            onChange: function (selectedDates, dateStr) {
                if (selectedDates.length > 0) {
                    bookingSearchData.checkIn = dateStr;
                    const checkInEl = document.getElementById('arCheckIn');
                    checkInEl.innerText = dateStr;
                    checkInEl.style.opacity = '1';

                    // Update checkout minimum date
                    if (checkOutPicker) {
                        const nextDay = new Date(selectedDates[0]);
                        nextDay.setDate(nextDay.getDate() + 1);
                        checkOutPicker.set('minDate', nextDay);
                    }

                    updateBookingCalculation();
                }
            }
        });
    }

    if (checkOutInput && typeof flatpickr !== 'undefined') {
        checkOutPicker = flatpickr(checkOutInput, {
            dateFormat: "Y/m/d",
            minDate: "today",
            defaultDate: bookingSearchData.checkOut || null,
            onChange: function (selectedDates, dateStr) {
                if (selectedDates.length > 0) {
                    bookingSearchData.checkOut = dateStr;
                    const checkOutEl = document.getElementById('arCheckOut');
                    checkOutEl.innerText = dateStr;
                    checkOutEl.style.opacity = '1';
                    updateBookingCalculation();
                }
            }
        });
    }
});

// Calculate Number of Nights
function calculateNights(checkIn, checkOut) {
    if (!checkIn || !checkOut) return 0;

    const start = new Date(checkIn);
    const end = new Date(checkOut);
    const diffTime = Math.abs(end - start);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays;
}

// Update Booking Calculation
function updateBookingCalculation() {
    if (!bookingSearchData.checkIn || !bookingSearchData.checkOut) {
        // Just update UI with 1 night preview or 0
        const nights = 1;
        const pricePerNight = currentPropertyPrice || 0;
        updateBookingUI(nights, pricePerNight);
        return;
    }

    const nights = calculateNights(bookingSearchData.checkIn, bookingSearchData.checkOut);
    if (nights <= 0) return;

    const pricePerNight = currentPropertyPrice || 0;
    updateBookingUI(nights, pricePerNight);

    // Refresh availability status based on new dates
    refreshAvailability();
}

// Update Booking UI with Calculations
function updateBookingUI(nights, pricePerNight) {
    // Robust parsing: remove anything that isn't a digit
    if (typeof pricePerNight === 'string') {
        pricePerNight = parseInt(pricePerNight.replace(/\D/g, '')) || 0;
    } else if (typeof pricePerNight === 'number') {
        // Ensure it's an integer
        pricePerNight = Math.floor(pricePerNight);
    }

    const subtotal = pricePerNight * nights;
    const total = subtotal;

    // Show price details section if dates are selected
    const priceSection = document.getElementById('priceDetailsSection');
    if (priceSection && nights > 0 && bookingSearchData.checkIn && bookingSearchData.checkOut) {
        priceSection.style.display = 'block';
    }

    // Update breakdown elements
    const calcLabelEl = document.getElementById('arCalcLabel');
    if (calcLabelEl) calcLabelEl.innerText = `${pricePerNight.toLocaleString()} DZD × ${nights} ${nights === 1 ? 'night' : 'nights'}`;

    const subtotalEl = document.getElementById('arCalcSubtotal');
    if (subtotalEl) subtotalEl.innerText = `${subtotal.toLocaleString()} DZD`;

    const totalEl = document.getElementById('arCalcTotal');
    if (totalEl) totalEl.innerText = `${total.toLocaleString()} DZD`;
}

async function initiateBooking() {
    if (!isLoggedIn) {
        closeArModal();
        openLoginModal('login');
        showToast('Login required to reserve');
        return;
    }

    // Get current booking data
    if (!bookingSearchData.checkIn || !bookingSearchData.checkOut) {
        showError('Please select check-in and check-out dates');
        return;
    }

    if (!currentDetailId) {
        showError('Missing property information');
        return;
    }

    // Enforce 3-day advance booking rule
    const checkInDate = new Date(bookingSearchData.checkIn.replace(/\//g, '-'));
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    const diffTime = checkInDate.getTime() - today.getTime();
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays < 3) {
        showError('Reservations must be made at least 3 days in advance.');
        return;
    }

    // Find property to get its ID
    const property = mockStays.find(s => s.id == currentDetailId);
    if (!property || !property.fullData || !property.fullData.annonce_id) {
        showError('Property is not available for booking');
        return;
    }

    const guestsCount = (bookingSearchData.guestCounts.adults || 1) + (bookingSearchData.guestCounts.children || 0);

    const btn = document.getElementById('btnReserve');
    if (btn) {
        btn.innerText = 'Processing...';
        btn.disabled = true;
    }

    let isSuccess = false;

    try {
        const response = await API.reservations.create({
            annonce_id: property.fullData.annonce_id,
            date_arrivee: bookingSearchData.checkIn.replace(/\//g, '-'),
            date_depart: bookingSearchData.checkOut.replace(/\//g, '-'),
            nombre_invites: guestsCount
        });

        if (response.success) {
            isSuccess = true;
            if (response.data && response.data.is_duplicate) {
                showError(response.message);
            } else {
                showSuccess('Reservation request sent successfully!');
                setTimeout(() => {
                    closeArModal();
                    handleMenuClick('Trips');
                }, 1500);
            }
        }
    } catch (error) {
        showError(error.message || 'Failed to create reservation');
    } finally {
        // Only re-enable the button if it FAILED. If it succeeded, keep it locked until the window closes.
        if (btn && !isSuccess) {
            btn.innerText = 'Check availability';
            btn.disabled = false;
        }
    }
}

async function loadTrips() {
    const list = document.getElementById('tripsList');
    if (!list) return;
    list.innerHTML = '<div style="text-align:center; padding: 40px;">Loading your trips...</div>';

    try {
        const response = await API.reservations.list('guest');
        if (response.success && response.data && response.data.length > 0) {
            list.innerHTML = response.data.map(trip => `
                <div style="border: 1px solid var(--border-color); border-radius: 12px; padding: 20px; display: flex; gap: 20px; background: var(--bg-color); margin-bottom: 15px;">
                    <img src="${trip.logement_image || DEFAULT_HOUSE_IMAGE}" style="width: 150px; height: 100px; object-fit: cover; border-radius: 8px;">
                    <div style="flex-grow: 1;">
                        <h3 style="margin: 0 0 10px 0;">${trip.logement_titre || 'Property'}</h3>
                        <p style="margin: 0 0 5px 0; color: #717171;">
                            <i class="far fa-calendar-alt"></i> ${trip.date_arrivee} to ${trip.date_depart}
                        </p>
                        <p style="margin: 0 0 10px 0; color: #717171;">
                            <i class="fas fa-users"></i> ${trip.nombre_invites} guest(s)
                        </p>
                        <span style="padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; 
                            background: ${trip.statut === 'en_attente' ? '#fff3cd' : (trip.statut === 'confirmee' ? '#d4edda' : '#f8d7da')};
                            color: ${trip.statut === 'en_attente' ? '#856404' : (trip.statut === 'confirmee' ? '#155724' : '#721c24')};">
                            ${trip.statut.replace('_', ' ').toUpperCase()}
                        </span>
                    </div>
                    ${trip.statut === 'en_attente' ? `
                    <div style="display: flex; flex-direction: column; justify-content: flex-end;">
                        <button onclick="handleReservationAction(${trip.id}, 'annuler')" style="padding: 8px 16px; border-radius: 8px; border: 1px solid #ff5a5f; color: #ff5a5f; background: transparent; cursor: pointer;">Cancel Request</button>
                    </div>` : ''}
                </div>
            `).join('');
        } else {
            list.innerHTML = `
                <div style="text-align:center; padding: 40px;">
                    <i class="fas fa-suitcase" style="font-size: 48px; color: var(--border-color); margin-bottom: 20px;"></i>
                    <h3>No trips booked... yet!</h3>
                    <p style="color: #717171;">Time to dust off your bags and start planning your next adventure.</p>
                    <button onclick="handleMenuClick('Home')" class="save-btn" style="margin-top: 20px;">Start searching</button>
                </div>
            `;
        }
    } catch (error) {
        list.innerHTML = `<div style="text-align:center; color: #ff5a5f; padding: 40px;">Error loading trips: ${error.message}</div>`;
    }
}

async function loadHostReservations() {
    const list = document.getElementById('reservationsList');
    if (!list) return;
    list.innerHTML = '<div style="text-align:center; padding: 40px;">Loading requests...</div>';

    try {
        const response = await API.reservations.list('host');
        if (response.success && response.data && response.data.length > 0) {
            list.innerHTML = response.data.map(res => `
                <div style="border: 1px solid var(--border-color); border-radius: 12px; padding: 20px; display: flex; gap: 20px; background: var(--bg-color); margin-bottom: 15px;">
                    <img src="${res.client_photo ? getImageSrc(res.client_photo) : getInitialsAvatar(res.client_nom)}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 50%;">
                    <div style="flex-grow: 1;">
                        <h3 style="margin: 0 0 5px 0;">Request from ${res.client_nom || 'Guest'}</h3>
                        <p style="margin: 0 0 5px 0; font-weight: 600;">Property: ${res.logement_titre || 'Your listing'}</p>
                        <p style="margin: 0 0 5px 0; color: #717171;">
                            <i class="far fa-calendar-alt"></i> ${res.date_arrivee} to ${res.date_depart}
                        </p>
                        <p style="margin: 0 0 10px 0; color: #717171;">
                            <i class="fas fa-users"></i> ${res.nombre_invites} guest(s)
                        </p>
                        <span style="padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; 
                            background: ${res.statut === 'en_attente' ? '#fff3cd' : (res.statut === 'confirmee' ? '#d4edda' : '#f8d7da')};
                            color: ${res.statut === 'en_attente' ? '#856404' : (res.statut === 'confirmee' ? '#155724' : '#721c24')};">
                            ${res.statut.replace('_', ' ').toUpperCase()}
                        </span>
                    </div>
                    ${res.statut === 'en_attente' ? `
                    <div style="display: flex; flex-direction: column; gap: 10px; justify-content: center;">
                        <button onclick="handleReservationAction(${res.id}, 'confirmer')" style="padding: 8px 16px; border-radius: 8px; border: none; color: white; background: #008a05; cursor: pointer; font-weight: 600;">Accept</button>
                        <button onclick="handleReservationAction(${res.id}, 'refuser')" style="padding: 8px 16px; border-radius: 8px; border: 1px solid #ff5a5f; color: #ff5a5f; background: transparent; cursor: pointer;">Reject</button>
                    </div>` : ''}
                </div>
            `).join('');
        } else {
            list.innerHTML = `
                <div style="text-align:center; padding: 40px;">
                    <i class="fas fa-clipboard-list" style="font-size: 48px; color: var(--border-color); margin-bottom: 20px;"></i>
                    <h3>No reservation requests</h3>
                    <p style="color: #717171;">When guests request to book your properties, they will appear here.</p>
                </div>
            `;
        }
    } catch (error) {
        list.innerHTML = `<div style="text-align:center; color: #ff5a5f; padding: 40px;">Error loading reservations: ${error.message}</div>`;
    }
}

async function handleReservationAction(id, action) {
    try {
        let response;
        if (action === 'annuler') {
            response = await API.reservations.cancel(id);
            if (response.success) {
                showSuccess('Reservation cancelled successfully');
                loadTrips(); // Refresh trips list
            }
        } else {
            // confirmer or refuser
            const statusMap = { 'confirmer': 'confirmee', 'refuser': 'annulee' };
            const status = statusMap[action];
            response = await API.reservations.updateStatus(id, status);
            if (response.success) {
                showSuccess(`Reservation ${status} successfully`);
                loadHostReservations(); // Refresh host list
            }
        }
    } catch (error) {
        showError(error.message || 'Action failed');
    }
}

// Dates/Guests interactions
document.querySelectorAll('.b-date-input, .b-guests').forEach(el => {
    el.addEventListener('click', () => {
        el.style.background = '#f7f7f7';
        setTimeout(() => el.style.background = 'transparent', 300);
    });
});

// Guest Favorite Link
document.querySelector('.guest-favorite-card').addEventListener('click', () => {
    showToast('This home is in the top 10%');
});

// Close when clicking outside content
document.getElementById('arPropertyModal').addEventListener('click', function (e) {
    if (e.target === this) closeArModal();
});
// ============================================
// Host Application & Admin Dashboard Logic
// ============================================
async function submitHostApplication() {
    const name = document.getElementById('applyHostName').value;
    const phone = document.getElementById('applyHostPhone').value;
    const carteInput = document.getElementById('applyHostCarteNational');
    const carteFile = carteInput ? carteInput.files[0] : null;

    if (!name || !phone || !carteFile) {
        showError('Please fill in all fields and upload your ID');
        return;
    }

    try {
        showLoading('Submitting application...');
        const formData = new FormData();
        formData.append('nom_complet', name);
        formData.append('telephone', phone);
        formData.append('carte_national', carteFile);

        const res = await API.users.applyHost(formData);
        hideLoading();
        showSuccess(res.message);
        checkSession(); // Update current user status
        closeProfileSub();
    } catch (error) {
        hideLoading();
        showError(error.message);
    }
}

function openGalleryModal() {
    const modal = document.getElementById("arGalleryModal");
    if (!modal) return;
    const grid = document.getElementById("galleryFullGrid");
    if (!grid) return;
    
    const images = window.currentPropertyImages || [];
    grid.innerHTML = images.map(img => {
        return `<img src="${getImageSrc(img)}" style="width:100%; border-radius:16px; margin-bottom:20px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); transition: 0.3s;">`;
    }).join("");
    
    modal.style.display = "block";
    document.body.style.overflow = "hidden";
}

function closeGalleryModal() {
    const modal = document.getElementById("arGalleryModal");
    if (modal) modal.style.display = "none";
    document.body.style.overflow = "auto";
}

// --- Contact Host Functions ---
function toggleContactHostForm(e) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    const form = document.getElementById('arContactHostForm');
    if (form) {
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }
}

async function sendHostMessage(e) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    const msgInput = document.getElementById('arContactHostMessage');
    const msg = msgInput ? msgInput.value.trim() : '';
    
    if (!msg) {
        showError('Please write a message first.');
        return;
    }

    if (!isLoggedIn) {
        openLoginModal('login');
        return;
    }

    try {
        showLoading('Sending message...');
        const response = await fetch(`${window.location.origin}/memoire/backend/api/messages/contact_host.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                property_id: currentDetailId,
                message: msg
            })
        });

        const data = await response.json();
        hideLoading();

        if (data.success) {
            showSuccess('Message sent successfully!');
            msgInput.value = '';
            toggleContactHostForm();
        } else {
            throw new Error(data.message || 'Failed to send message');
        }
    } catch (error) {
        hideLoading();
        showError(error.message || 'Failed to send message');
    }
}
