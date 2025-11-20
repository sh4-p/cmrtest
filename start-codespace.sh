#!/bin/bash

# Start Laravel development server for GitHub Codespace
echo "🚀 Starting CRM application in Codespace..."

# Start PHP built-in server in background
echo "📱 Starting Laravel server..."
php artisan serve --host=0.0.0.0 --port=8000 &
PHP_PID=$!

# Wait a moment for PHP server to start
sleep 2

# Start Vite dev server
echo "⚡ Starting Vite dev server..."
npm run dev

# When Vite is stopped, also stop PHP server
kill $PHP_PID 2>/dev/null
echo "✅ Servers stopped"
