# WINSTAY Backend Setup Guide

## Installation Steps

### 1. Database Setup

1. Open phpMyAdmin in your WAMP interface (usually `http://localhost/phpmyadmin`)
2. Create a new database called `winstay`:
   - Click "New" in the left sidebar
   - Enter database name: `winstay`
   - Select collation: `utf8mb4_unicode_ci`
   - Click "Create"

3. Import the database schema:
   - Select the `winstay` database
   - Click the "Import" tab
   - Choose file: `database/install.sql`
   - Click "Go"

### 2. Backend Configuration

The backend is already configured for WAMP default settings:
- **Host**: localhost
- **Database**: winstay
- **User**: root
- **Password**: (empty)

If your WAMP has different settings, edit `backend/config/database.php`

### 3. Upload Directories

Create necessary upload directories (or they will be created automatically):
```
mkdir uploads
mkdir uploads/profiles
mkdir uploads/properties
```

### 4. Test API

Test if the backend is working:
```
http://localhost/memoire/backend/api/auth/check.php
```

You should see a JSON response like:
```json
{
  "success": false,
  "message": "Not authenticated"
}
```

## API Endpoints

### Authentication
- **POST** `/backend/api/auth/register.php` - Register new user
- **POST** `/backend/api/auth/login.php` - Login user  
- **POST** `/backend/api/auth/logout.php` - Logout user
- **GET** `/backend/api/auth/check.php` - Check session

### Users
- **GET** `/backend/api/users/profile.php?id={id}` - Get user profile
- **PUT** `/backend/api/users/update.php` - Update profile
- **POST** `/backend/api/users/upload-photo.php` - Upload profile photo

### Properties
- **POST** `/backend/api/properties/create.php` - Create property
- **GET** `/backend/api/properties/list.php` - List properties (with filters)
- **GET** `/backend/api/properties/get.php?id={id}` - Get property details
- **PUT** `/backend/api/properties/update.php` - Update property
- **DELETE** `/backend/api/properties/delete.php` - Delete property
- **POST** `/backend/api/properties/upload-images.php` - Upload property images
- **GET** `/backend/api/properties/my-listings.php` - Get user's properties

### Reservations
- **POST** `/backend/api/reservations/create.php` - Create reservation
- **GET** `/backend/api/reservations/list.php?type=guest|host` - List reservations
- **PUT** `/backend/api/reservations/update-status.php` - Update status (host only)
- **POST** `/backend/api/reservations/cancel.php` - Cancel reservation (guest)

### Reviews
- **POST** `/backend/api/reviews/add-property-review.php` - Add review
- **GET** `/backend/api/reviews/property-reviews.php?logement_id={id}` - Get reviews

### Favorites
- **POST** `/backend/api/favorites/add.php` - Add to favorites
- **DELETE** `/backend/api/favorites/remove.php` - Remove from favorites
- **GET** `/backend/api/favorites/list.php` - Get favorites

### Messages
- **POST** `/backend/api/messages/send.php` - Send message
- **GET** `/backend/api/messages/conversations.php` - Get conversations
- **GET** `/backend/api/messages/get.php?user_id={id}` - Get messages

### Notifications
- **GET** `/backend/api/notifications/list.php` - Get notifications
- **PUT** `/backend/api/notifications/mark-read.php` - Mark as read

### Admin
- **GET** `/backend/api/admin/statistics.php` - Get platform statistics

## Default Accounts

After running `database/install.sql`, the following test accounts are available:

**Regular Users:**
- Email: `ahmed@example.com` | Password: `password`
- Email: `fatima@example.com` | Password: `password`
- Email: `karim@example.com` | Password: `password`

**Admin Account:**
- Email: `admin@winstay.com` | Password: `password`

## Sample API Requests

### Register
```javascript
fetch('http://localhost/memoire/backend/api/auth/register.php', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    nom: 'Test User',
    email: 'test@example.com',
    mot_de_passe: 'password123'
  })
})
```

### Login
```javascript
fetch('http://localhost/memoire/backend/api/auth/login.php', {
  method: 'POST',
  credentials: 'include', // Important for sessions
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    email: 'ahmed@example.com',
    mot_de_passe: 'password'
  })
})
```

### Get Properties
```javascript
fetch('http://localhost/memoire/backend/api/properties/list.php?wilaya=Alger&page=1')
```

### Create Property
```javascript
fetch('http://localhost/memoire/backend/api/properties/create.php', {
  method: 'POST',
  credentials: 'include',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    titre: 'Beautiful Apartment',
    description: 'Great location',
    wilaya: 'Alger',
    prix_par_nuit: 5000,
    nombre_invites: 4,
    nombre_chambres: 2,
    nombre_lits: 2,
    nombre_salles_bain: 1,
    categorie: 'Apartment'
  })
})
```

## Security Notes

- All passwords are hashed with bcrypt
- Session-based authentication (must include `credentials: 'include'`)
- SQL injection protection via PDO prepared statements
- XSS protection via input sanitization
- File upload validation (type, size)
- CORS headers configured in `.htaccess`

## Troubleshooting

### Error: "Database connection failed"
- Check if MySQL is running in WAMP
- Verify database name is `winstay`
- Check credentials in `backend/config/database.php`

### Error: "Session not working"
- Make sure to include `credentials: 'include'` in fetch requests
- Check if cookies are enabled in browser
- Verify session settings in WAMP PHP configuration

### Error: "File upload failed"
- Check write permissions on `uploads` directory
- Verify `upload_max_filesize` in `php.ini`
- Check file size limit (max 5MB)

## Next Steps

1. ✅ Database is installed
2. ✅ Test API endpoints
3. ⏭️ Integrate with your frontend JavaScript
4. ⏭️ Replace localStorage calls with API calls
5. ⏭️ Test complete user flows

For frontend integration examples, see the sample requests above!
