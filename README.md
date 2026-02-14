# Digital Signage CMS - Backend

Laravel-based REST API for managing digital signage content, displays, layouts, and schedules.

## Tech Stack

- **Framework**: Laravel 12
- **PHP Version**: 8.4
- **Database**: SQLite
- **Authentication**: Laravel Sanctum
- **Media Processing**: FFmpeg (via pbmedia/laravel-ffmpeg)

## Features

- User authentication with token-based API
- Content management (images, videos)
- Display registration and management
- Layout designer with multi-region support
- Playlist creation and scheduling
- Automatic video thumbnail generation
- File upload with configurable size limits

## Requirements

- PHP 8.4 or higher
- Composer
- FFmpeg (for video thumbnail generation)
- SQLite

## Installation

1. **Install Dependencies**
   ```bash
   composer install
   ```

2. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Configure Database**
   
   The `.env` file is already configured for SQLite:
   ```
   DB_CONNECTION=sqlite
   DB_DATABASE=/absolute/path/to/database.sqlite
   ```

4. **Run Migrations**
   ```bash
   php artisan migrate
   ```

5. **Create Storage Link**
   ```bash
   php artisan storage:link
   ```

6. **Configure FFmpeg** (for video thumbnails)
   
   Update `.env` with FFmpeg paths:
   ```
   FFMPEG_BINARIES=/opt/homebrew/bin/ffmpeg
   FFPROBE_BINARIES=/opt/homebrew/bin/ffprobe
   ```

7. **Create Admin User**
   ```bash
   php artisan tinker
   ```
   Then run:
   ```php
   $user = App\Models\User::create([
       'name' => 'Admin',
       'email' => 'admin@example.com',
       'password' => Hash::make('password')
   ]);
   ```

8. **Start Development Server**
   ```bash
   php artisan serve
   ```
   
   API will be available at `http://127.0.0.1:8000`

## API Endpoints

### Authentication
- `POST /api/login` - Login with email/password
- `POST /api/register` - Register new user
- `POST /api/logout` - Logout (requires auth)
- `GET /api/me` - Get current user (requires auth)

### Protected Endpoints (require authentication)
- `GET|POST /api/displays` - Manage displays
- `GET|POST /api/contents` - Manage content (images/videos)
- `GET|POST /api/playlists` - Manage playlists
- `GET|POST /api/schedules` - Manage schedules
- `GET|POST /api/layouts` - Manage layouts
- `GET|POST /api/regions` - Manage layout regions

### Player Endpoints (public)
- `POST /api/player/register` - Register a display player
- `GET /api/player/{code}/content` - Get content for player

## Configuration

### Upload Limits

Edit `php.ini` to increase upload limits:
```ini
upload_max_filesize = 100M
post_max_size = 100M
```

Restart PHP/server after changes.

### CORS

CORS is configured in `config/cors.php` to allow all origins for development.

## Database Schema

### Main Tables
- `users` - Admin users
- `displays` - Registered display devices
- `contents` - Media files (images/videos)
- `playlists` - Collections of content
- `schedules` - Time-based content scheduling
- `layouts` - Display layouts with dimensions
- `regions` - Layout regions with position/size
- `personal_access_tokens` - Sanctum auth tokens

## File Storage

Uploaded files are stored in:
- `storage/app/public/content/` - Media files
- `storage/app/public/thumbnails/` - Video thumbnails

Access via: `http://localhost:8000/storage/content/{filename}`

## Development

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### Run Tests
```bash
php artisan test
```

### Database Reset
```bash
php artisan migrate:fresh
```

## Troubleshooting

### FFmpeg Not Found
Install FFmpeg:
```bash
# macOS
brew install ffmpeg

# Ubuntu/Debian
sudo apt-get install ffmpeg
```

### Permission Issues
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Sanctum Token Issues
Ensure `personal_access_tokens` table exists:
```bash
php artisan migrate
```

## Production Deployment

1. Set `APP_ENV=production` in `.env`
2. Set `APP_DEBUG=false`
3. Configure proper database (MySQL/PostgreSQL)
4. Set up queue workers for background jobs
5. Configure proper file permissions
6. Use HTTPS for API endpoints
7. Set up proper CORS origins

## License

Proprietary
