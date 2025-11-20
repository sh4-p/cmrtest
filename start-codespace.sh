#!/usr/bin/env bash
set -e

# Start Laravel development server for GitHub Codespace
echo "🚀 Starting CRM application in Codespace..."

# Check if ports are available
if lsof -Pi :8000 -sTCP:LISTEN -t >/dev/null 2>&1 ; then
    echo "⚠️  Port 8000 is already in use. Stopping existing process..."
    lsof -ti:8000 | xargs kill -9 2>/dev/null || true
    sleep 1
fi

if lsof -Pi :5173 -sTCP:LISTEN -t >/dev/null 2>&1 ; then
    echo "⚠️  Port 5173 is already in use. Stopping existing process..."
    lsof -ti:5173 | xargs kill -9 2>/dev/null || true
    sleep 1
fi

# Start PHP built-in server in background
echo "📱 Starting Laravel server on port 8000..."
/usr/bin/php artisan serve --host=0.0.0.0 --port=8000 &
PHP_PID=$!

# Wait a moment for PHP server to start
sleep 2

# Check if PHP started successfully
if ! ps -p $PHP_PID > /dev/null; then
    echo "❌ Failed to start Laravel server"
    exit 1
fi

echo "✅ Laravel server started (PID: $PHP_PID)"

# Start Vite dev server
echo "⚡ Starting Vite dev server on port 5173..."
npm run dev

# When Vite is stopped (Ctrl+C), also stop PHP server
kill $PHP_PID 2>/dev/null || true
echo "✅ Servers stopped"
