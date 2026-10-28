/**
 * WINSTAY API Integration
 * Helper functions for backend communication
 */

const API_BASE_URL = window.location.origin + '/memoire/backend/api';

// API Helper Functions
const API = {
    // Generic fetch wrapper with error handling
    async request(endpoint, options = {}) {
        const defaultOptions = {
            credentials: 'include', // Important for sessions!
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            }
        };

        try {
            const response = await fetch(`${API_BASE_URL}${endpoint}`, {
                ...defaultOptions,
                ...options
            });

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || 'Request failed');
            }

            return data;
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    },

    // Authentication
    auth: {
        async register(nom, email, mot_de_passe) {
            return await API.request('/auth/register.php', {
                method: 'POST',
                body: JSON.stringify({ nom, email, mot_de_passe })
            });
        },

        async login(email, mot_de_passe) {
            return await API.request('/auth/login.php', {
                method: 'POST',
                body: JSON.stringify({ email, mot_de_passe })
            });
        },

        async socialLogin(provider, email, nom, token, photo_profil = null) {
            return await API.request('/auth/social_login.php', {
                method: 'POST',
                body: JSON.stringify({ provider, email, nom, token, photo_profil })
            });
        },

        async verifyOTP(email, otp) {
            return await API.request('/auth/verify_otp.php', {
                method: 'POST',
                body: JSON.stringify({ email, otp })
            });
        },

        async forgotPassword(email) {
            return await API.request('/auth/forgot_password.php', {
                method: 'POST',
                body: JSON.stringify({ email })
            });
        },

        async resendOTP(email) {
            return await API.request('/auth/resend_otp.php', {
                method: 'POST',
                body: JSON.stringify({ email })
            });
        },

        async resetPassword(email, otp, new_password) {
            return await API.request('/auth/reset_password.php', {
                method: 'POST',
                body: JSON.stringify({ email, otp, new_password })
            });
        },

        async logout() {
            return await API.request('/auth/logout.php', {
                method: 'POST'
            });
        },

        async checkSession() {
            return await API.request('/auth/check.php');
        }
    },

    // Users
    users: {
        async getProfile(userId = null) {
            const query = userId ? `?id=${userId}` : '';
            return await API.request(`/users/profile.php${query}`);
        },

        async updateProfile(updates) {
            return await API.request('/users/update.php', {
                method: 'PUT',
                body: JSON.stringify(updates)
            });
        },

        async uploadPhoto(file) {
            const formData = new FormData();
            formData.append('photo', file);

            return await API.request('/users/upload-photo.php', {
                method: 'POST',
                headers: {}, // Let browser set Content-Type for FormData
                body: formData
            });
        },

        async applyHost(formData) {
            return await API.request('/users/apply_host.php', {
                method: 'POST',
                headers: {}, // Important: let browser set Content-Type for FormData
                body: formData
            });
        }
    },

    // Admin
    admin: {
        async getHostRequests() {
            return await API.request('/admin/host_requests.php');
        },
        async approveHost(demande_id, action) {
            return await API.request('/admin/approve_host.php', {
                method: 'POST',
                body: JSON.stringify({ demande_id, action })
            });
        }
    },

    // Properties
    properties: {
        async list(filters = {}) {
            const params = new URLSearchParams(filters);
            return await API.request(`/properties/list.php?${params}`);
        },

        async get(id, dates = {}) {
            const params = new URLSearchParams({ id, ...dates });
            return await API.request(`/properties/get.php?${params}`);
        },

        async create(propertyData) {
            return await API.request('/properties/create.php', {
                method: 'POST',
                body: JSON.stringify(propertyData)
            });
        },

        async update(id, updates) {
            return await API.request('/properties/update.php', {
                method: 'PUT',
                body: JSON.stringify({ id, ...updates })
            });
        },

        async delete(id) {
            return await API.request('/properties/delete.php', {
                method: 'DELETE',
                body: JSON.stringify({ id })
            });
        },

        async uploadImages(logementId, files) {
            const formData = new FormData();
            formData.append('logement_id', logementId);

            for (let i = 0; i < files.length; i++) {
                formData.append('images[]', files[i]);
            }

            return await API.request('/properties/upload-images.php', {
                method: 'POST',
                headers: {},
                body: formData
            });
        },

        async getMyListings() {
            return await API.request('/properties/my-listings.php');
        }
    },

    // Reservations
    reservations: {
        async create(reservationData) {
            return await API.request('/reservations/create.php', {
                method: 'POST',
                body: JSON.stringify(reservationData)
            });
        },

        async list(type = 'guest') {
            return await API.request(`/reservations/list.php?type=${type}`);
        },

        async updateStatus(id, statut) {
            return await API.request('/reservations/update-status.php', {
                method: 'PUT',
                body: JSON.stringify({ id, statut })
            });
        },

        async cancel(id) {
            return await API.request('/reservations/cancel.php', {
                method: 'POST',
                body: JSON.stringify({ id })
            });
        }
    },

    // Reviews
    reviews: {
        async addPropertyReview(demande_reservation_id, note, commentaire) {
            return await API.request('/reviews/add-property-review.php', {
                method: 'POST',
                body: JSON.stringify({ demande_reservation_id, note, commentaire })
            });
        },

        async updatePropertyReview(review_id, note, commentaire) {
            return await API.request('/reviews/update-property-review.php', {
                method: 'PUT',
                body: JSON.stringify({ review_id, note, commentaire })
            });
        },

        async getPropertyReviews(logementId) {
            return await API.request(`/reviews/property-reviews.php?logement_id=${logementId}`);
        }
    },

    // Favorites
    favorites: {
        async add(logementId) {
            return await API.request('/favorites/add.php', {
                method: 'POST',
                body: JSON.stringify({ logement_id: logementId })
            });
        },

        async remove(logementId) {
            return await API.request('/favorites/remove.php', {
                method: 'DELETE',
                body: JSON.stringify({ logement_id: logementId })
            });
        },

        async list() {
            return await API.request('/favorites/list.php');
        }
    },

    // Messages
    messages: {
        async send(destinataireId, message) {
            return await API.request('/messages/send.php', {
                method: 'POST',
                body: JSON.stringify({ destinataire_id: destinataireId, message })
            });
        },

        async getConversations() {
            return await API.request('/messages/conversations.php');
        },

        async getMessages(userId) {
            return await API.request(`/messages/get.php?user_id=${userId}`);
        }
    },

    // Notifications
    notifications: {
        async list() {
            return await API.request('/notifications/list.php');
        },

        async markRead(id = null) {
            const body = id ? { id } : {};
            return await API.request('/notifications/mark-read.php', {
                method: 'PUT',
                body: JSON.stringify(body)
            });
        }
    }
};

// Loading State Helper
function showLoading(message = 'Loading...') {
    // You can customize this with a better UI
    const loader = document.createElement('div');
    loader.id = 'api-loader';
    loader.style.cssText = `
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(0,0,0,0.8);
        color: white;
        padding: 20px 40px;
        border-radius: 10px;
        z-index: 10000;
    `;
    loader.textContent = message;
    document.body.appendChild(loader);
}

function hideLoading() {
    const loader = document.getElementById('api-loader');
    if (loader) loader.remove();
}

// Error Display Helper
function showError(message) {
    let t = document.getElementById('errorToast');
    if (!t) {
        t = document.createElement('div');
        t.id = 'errorToast';
        t.className = 'toast';
        t.style.background = '#ff5a5f';
        t.style.color = 'white';
        t.innerHTML = '<i class="fas fa-exclamation-circle"></i> <span id="errorToastMsg"></span>';
        document.body.appendChild(t);
    }
    document.getElementById('errorToastMsg').innerText = message;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3000);
}

function showSuccess(message) {
    const t = document.getElementById('toast');
    if (t) {
        const msgEl = document.getElementById('toastMsg');
        if (msgEl) msgEl.innerText = message;
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 3000);
    } else {
        alert(message);
    }
}
