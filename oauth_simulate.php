<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in - WINSTAY Social Auth</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --google-blue: #1a73e8;
            --text-main: #202124;
            --text-sub: #5f6368;
            --border: #dadce0;
        }

        body {
            font-family: 'Roboto', arial, sans-serif;
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: var(--text-main);
        }

        .auth-container {
            width: 100%;
            max-width: 450px;
            background: white;
            padding: 48px 40px 36px;
            border: 1px solid var(--border);
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
            position: relative;
        }

        .logo-container {
            margin-bottom: 12px;
        }

        .logo-container img {
            width: 75px;
            height: auto;
        }

        h1 {
            font-size: 24px;
            font-weight: 400;
            margin: 0 0 8px 0;
        }

        p.subtitle {
            font-size: 16px;
            color: var(--text-main);
            margin: 0 0 40px 0;
        }

        .input-group {
            position: relative;
            margin-bottom: 24px;
            text-align: left;
        }

        input {
            width: 100%;
            padding: 13px 15px;
            border: 1px solid var(--border);
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
            outline: none;
            transition: border 0.2s;
        }

        input:focus {
            border: 2px solid var(--google-blue);
            padding: 12px 14px;
        }

        .profile-preview {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            margin-bottom: 24px;
            text-align: left;
            background: #fff;
        }

        .profile-preview img {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
            background: #eee;
        }

        .profile-info b {
            display: block;
            font-size: 14px;
        }

        .profile-info span {
            font-size: 12px;
            color: var(--text-sub);
        }

        .footer-btns {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 32px;
        }

        .link-btn {
            color: var(--google-blue);
            font-weight: 500;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
        }

        .next-btn {
            background-color: var(--google-blue);
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            font-size: 14px;
            transition: box-shadow 0.2s;
        }

        .next-btn:hover {
            box-shadow: 0 1px 2px 0 rgba(60,64,67,0.3), 0 1px 3px 1px rgba(60,64,67,0.15);
        }

        .loading-overlay {
            display: none;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.9);
            z-index: 10;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border-radius: 8px;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid var(--google-blue);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 16px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="loading-overlay" id="loadingOverlay">
            <div class="spinner"></div>
            <p>Signing you in...</p>
        </div>

        <div class="logo-container">
            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/53/Google_%22G%22_Logo.svg/512px-Google_%22G%22_Logo.svg.png">
        </div>
        <h1>Sign in</h1>
        <p class="subtitle">Use your Google Account</p>

        <div class="input-group">
            <input type="email" id="email" placeholder="Email or phone" required value="demo.user@gmail.com">
        </div>
        <div class="input-group">
            <input type="text" id="name" placeholder="First and Last Name" required value="Demo Professional">
        </div>

        <div class="profile-preview" id="profilePreview">
            <img id="userAvatar" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23666'%3E%3Cpath d='M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z'/%3E%3C/svg%3E">
            <div class="profile-info">
                <b id="previewName">Demo Professional</b>
                <span id="previewEmail">demo.user@gmail.com</span>
            </div>
        </div>

        <div class="footer-btns">
            <a class="link-btn">Create account</a>
            <button class="next-btn" onclick="submitAuth()">Next</button>
        </div>
    </div>

    <script>
        // Live preview updates
        document.getElementById('email').addEventListener('input', updatePreview);
        document.getElementById('name').addEventListener('input', updatePreview);

        function getInitialsAvatar(name) {
            if (!name) name = "User";
            const initial = name.charAt(0).toUpperCase();
            const colors = ['#00a8b5', '#e91e63', '#9c27b0', '#673ab7', '#3f51b5', '#2196f3', '#009688', '#4caf50', '#ff9800', '#f44336'];
            let hash = 0;
            for (let i = 0; i < name.length; i++) hash = name.charCodeAt(i) + ((hash << 5) - hash);
            const color = colors[Math.abs(hash) % colors.length];
            return `data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect width='100' height='100' fill='${encodeURIComponent(color)}'/%3E%3Ctext x='50%25' y='50%25' dominant-baseline='central' text-anchor='middle' fill='white' font-family='Montserrat, sans-serif' font-size='50' font-weight='800'%3E${initial}%3C/text%3E%3C/svg%3E`;
        }

        function updatePreview() {
            const name = document.getElementById('name').value || "Your Name";
            const email = document.getElementById('email').value || "email@example.com";
            document.getElementById('previewName').innerText = name;
            document.getElementById('previewEmail').innerText = email;
            document.getElementById('userAvatar').src = getInitialsAvatar(name);
        }

        function submitAuth() {
            const email = document.getElementById('email').value;
            const name = document.getElementById('name').value;
            const avatar = document.getElementById('userAvatar').src;

            if (!email || !name) {
                alert("Please complete all fields for the demo");
                return;
            }

            document.getElementById('loadingOverlay').style.display = "flex";
            
            // Realistic delay for demo effect
            setTimeout(() => {
                window.opener.postMessage({
                    type: 'SOCIAL_LOGIN_SUCCESS',
                    payload: {
                        provider: 'google',
                        email: email,
                        nom: name,
                        token: 'MOCK_TOKEN_' + Math.random().toString(36).substr(2),
                        photo_profil: avatar
                    }
                }, "*");
                window.close();
            }, 2000);
        }

        // Initial preview
        updatePreview();
    </script>
</body>
</html>
