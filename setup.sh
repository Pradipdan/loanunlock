#!/bin/bash
# LoanUnlock – One-command setup script
# Usage: bash setup.sh

set -e

echo ""
echo "🏦 LoanUnlock – Setup Script"
echo "=============================="
echo ""

# 1. Copy env
if [ ! -f .env ]; then
    cp .env.example .env
    echo "✅ .env created from .env.example"
else
    echo "ℹ️  .env already exists, skipping..."
fi

# 2. Install dependencies
echo ""
echo "📦 Installing PHP dependencies..."
composer install --no-interaction --prefer-dist

# 3. Generate app key
echo ""
echo "🔑 Generating app key..."
php artisan key:generate

# 4. Run migrations
echo ""
echo "🗄️  Running database migrations..."
php artisan migrate --force

# 5. Seed database
echo ""
echo "🌱 Seeding admin accounts..."
php artisan db:seed --force

# 6. Storage link
echo ""
echo "📁 Creating storage symlink..."
php artisan storage:link

echo ""
echo "✅ Setup complete!"
echo ""
echo "🚀 Start the server:"
echo "   php artisan serve"
echo ""
echo "🔑 Admin Login: http://localhost:8000/admin/login"
echo "   Email:    admin@loanunlock.com"
echo "   Password: Admin@123"
echo ""
echo "📱 User App: http://localhost:8000/welcome"
echo "   OTP (demo): 123456"
echo ""
