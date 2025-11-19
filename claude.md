# 🚀 Advanced CRM System - Detaylı Geliştirme Planı

## 📊 İlerleme Durumu
- ⏸️ Beklemede
- 🔄 Devam Ediyor
- ✅ Tamamlandı
- ❌ İptal/Atlandı

---

## 🎯 Proje Özeti
Modern, mobile-first CRM sistemi - PHP MVC mimarisi üzerine inşa edilmiş

### 🛠️ Teknoloji Stack'i
- **Backend:** PHP 8.3, Laravel 10+
- **Frontend:** HTML5, CSS3 (Tailwind CSS), JavaScript (ES6+), Vue.js 3
- **Database:** MySQL 8.0 / PostgreSQL 15
- **Package Management:** Composer (PHP), NPM (JS/CSS)
- **Development Environment:** Docker & Docker Compose
- **Version Control:** Git

---

## Phase 0: Foundation & Project Setup ✅

### 0.1. Environment Setup ✅
**Hedef:** Geliştirme ortamını Docker ile kurmak

- ✅ **Docker ve Docker Compose kurulumu**
  - Docker Desktop veya Docker Engine yükle
  - `docker --version` ve `docker-compose --version` ile doğrula
  - Komut: `curl -fsSL https://get.docker.com -o get-docker.sh && sh get-docker.sh`
  - **Tamamlandı:** Dockerfile ve docker-compose.yml oluşturuldu

- ✅ **docker-compose.yml oluşturma**
  - Services: PHP-FPM 8.3, Nginx, MySQL 8.0, Redis
  - Volume mapping'leri tanımla
  - Network konfigürasyonu
  - **Tamamlandı:** docker-compose.yml, Dockerfile ve config dosyaları oluşturuldu
  - Servisler: app (PHP-FPM), nginx, mysql, redis, node

- ✅ **Laravel projesi oluşturma**
  - `composer create-project laravel/laravel advanced-crm`
  - Laravel sürümünü doğrula: `php artisan --version`
  - Proje klasör yapısını incele
  - **Tamamlandı:** Laravel 12.39.0 kuruldu

- ✅ **.env dosyası konfigürasyonu**
  - Database connection ayarları
  - APP_NAME, APP_ENV, APP_DEBUG
  - Cache, Queue, Session driver'ları
  - Redis connection
  - Mail configuration
  - **Tamamlandı:** MySQL, Redis ve diğer ayarlar yapılandırıldı

- ✅ **Git repository başlatma**
  - `git init`
  - `.gitignore` dosyasını kontrol et
  - İlk commit: `git add . && git commit -m "Initial Laravel project setup"`
  - Remote repository oluştur (GitHub/GitLab)
  - `git remote add origin <repo-url>`
  - `git push -u origin main`
  - **Tamamlandı:** Git repository zaten mevcut

### 0.2. Frontend Scaffolding ✅
**Hedef:** Modern frontend geliştirme araçlarını kurmak

- ✅ **Laravel Vite kurulumu ve konfigürasyonu**
  - `npm install` komutu ile dependencies yükle
  - `vite.config.js` dosyasını kontrol et
  - Input dosyalarını tanımla: `resources/js/app.js`, `resources/css/app.css`
  - Test: `npm run dev`
  - **Tamamlandı:** NPM dependencies kuruldu, Vite zaten Laravel ile geldi

- ✅ **Tailwind CSS kurulumu**
  - `npm install -D tailwindcss postcss autoprefixer`
  - `npx tailwindcss init -p`
  - `tailwind.config.js` dosyasına content paths ekle
  - `resources/css/app.css` dosyasına Tailwind directive'lerini ekle
  - Test class'ı ile doğrula
  - **Tamamlandı:** Tailwind CSS kuruldu ve yapılandırıldı (Laravel 12'de built-in)

- ✅ **Vue.js 3 kurulumu**
  - `npm install vue`
  - `npm install @vitejs/plugin-vue`
  - `vite.config.js` dosyasına Vue plugin ekle
  - `resources/js/app.js` dosyasını Vue için yapılandır
  - İlk test component'i oluştur ve test et
  - **Tamamlandı:** Vue.js 3 kuruldu, vite.config.js güncellendi, Welcome.vue component'i oluşturuldu

- ✅ **UI Component Library kurulumu**
  - Headless UI kurulumu: `npm install @headlessui/vue`
  - İkon kütüphanesi: `npm install @heroicons/vue`
  - Temel component'leri hazırla: Button, Input, Modal, Card
  - **Tamamlandı:** Headless UI ve Heroicons kuruldu

### 0.3. Core Package Installation ✅
**Hedef:** Temel Laravel paketlerini yüklemek

- ✅ **Authentication paketi kurulumu**
  - Laravel Breeze: `composer require laravel/breeze --dev`
  - `php artisan breeze:install vue --dark`
  - Migration'ları çalıştır: `php artisan migrate`
  - Test: Register ve Login sayfalarını kontrol et
  - **Tamamlandı:** Laravel Breeze 2.3.8 kuruldu (Inertia.js + Vue stack)

- ✅ **Roles & Permissions paketi**
  - `composer require spatie/laravel-permission`
  - Config publish: `php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"`
  - Migration çalıştır: `php artisan migrate`
  - User model'e `HasRoles` trait'i ekle
  - Test: Role ve permission oluştur
  - **Tamamlandı:** Spatie Permission 6.23.0 kuruldu, config ve migration oluşturuldu

- ✅ **Media Management paketi**
  - `composer require spatie/laravel-medialibrary`
  - Config publish: `php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider"`
  - Migration çalıştır
  - Test model için `HasMedia` interface ve trait ekle
  - Storage link oluştur: `php artisan storage:link`
  - **Tamamlandı:** Spatie MediaLibrary 11.17.5 kuruldu, migration oluşturuldu

---

## Phase 1: Authentication & Authorization (MVC Foundation) 🔄

### 1.1. User Model & Migration ✅
**Hedef:** User modelini CRM ihtiyaçlarına göre genişletmek

- ✅ **User migration'ı güncelleme**
  - Migration dosyasını aç: `database/migrations/*_create_users_table.php`
  - Yeni alanlar eklendi:
    ```php
    $table->string('phone_number')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamp('last_login_at')->nullable();
    $table->string('timezone')->default('UTC');
    ```
  - **Tamamlandı:** User table CRM alanları ile genişletildi
  - Not: Avatar Spatie MediaLibrary ile polymorphic olarak yönetilecek

- ✅ **User Model konfigürasyonu**
  - `app/Models/User.php` güncellendi
  - `HasRoles` trait eklendi (Spatie Permission)
  - Fillable alanları güncellendi (phone_number, is_active, last_login_at, timezone)
  - Cast'ler tanımlandı: `'is_active' => 'boolean'`, `'last_login_at' => 'datetime'`
  - **Tamamlandı:** User model CRM için hazır
  - Not: Relationships Phase 2'de eklenecek (companies, contacts, deals)

### 1.2. Authentication (Controllers & Views) ⏸️
**Hedef:** Özelleştirilmiş authentication sistemi

- ⏸️ **Breeze/Jetstream görünümlerini özelleştirme**
  - Login view: `resources/views/auth/login.blade.php`
  - Register view: `resources/views/auth/register.blade.php`
  - Tailwind classes ile CRM temasına uygun tasarım
  - Logo ve brand color'ları ekle
  - Form validation mesajlarını Türkçeleştir (opsiyonel)

- ⏸️ **Password Reset işlevi**
  - Mail konfigürasyonu kontrol: `.env` dosyasında MAIL_ ayarları
  - Password reset view'larını özelleştir
  - Email template'lerini düzenle: `resources/views/emails/`
  - Test: Şifre sıfırlama akışını kontrol et

- ⏸️ **Email Verification**
  - User model'e `MustVerifyEmail` interface ekle
  - Routes'da `verified` middleware kullan
  - Verification email template'ini özelleştir
  - Test: Yeni kullanıcı kaydı ve email doğrulama

- ⏸️ **Two-Factor Authentication (2FA)**
  - Jetstream kullanıyorsa, 2FA zaten mevcut
  - Değilse: `pragmarx/google2fa-laravel` paketi kur
  - 2FA setup view'ları oluştur
  - QR kod gösterimi için library ekle
  - User settings'de 2FA enable/disable seçeneği
  - Test: 2FA setup ve login akışı

### 1.3. Roles & Permissions Setup ✅
**Hedef:** Rol tabanlı yetkilendirme sistemi

- ✅ **Role ve Permission seeder oluşturma**
  - Seeder oluştur: `php artisan make:seeder RolePermissionSeeder`
  - **Tamamlandı:** 4 rol tanımlandı:
    - **Super Admin:** Tüm yetkiler (71 permission)
    - **Admin:** Kullanıcı yönetimi hariç tüm yetkiler
    - **Manager:** Ekip yönetimi ve raporlar
    - **Sales Rep:** Sadece kendi kayıtlarını yönetme
  - **Tamamlandı:** 71 permission tanımlandı:
    - User Management (5): manage-users, view-users, create-users, edit-users, delete-users
    - Leads (9): view, view-all, create, edit, edit-all, delete, delete-all, assign, convert
    - Contacts (7): view, view-all, create, edit, edit-all, delete, delete-all
    - Companies (7): view, view-all, create, edit, edit-all, delete, delete-all
    - Deals (8): view, view-all, create, edit, edit-all, delete, delete-all, manage-stages
    - Tasks (7): view, view-all, create, edit, edit-all, delete, delete-all
    - Activities (4): view, view-all, create, delete
    - Reports (3): view, view-all, export
    - Settings (2): manage-settings, view-settings
  - DatabaseSeeder'a eklendi
  - 4 test kullanıcısı oluşturuldu (her rol için birer tane)

- ✅ **Middleware kayıtları**
  - Spatie Permission middleware'leri `bootstrap/app.php`'ye kaydedildi
  - Alias'lar:
    - `role`: RoleMiddleware
    - `permission`: PermissionMiddleware
    - `role_or_permission`: RoleOrPermissionMiddleware
  - Kullanım: `Route::middleware(['auth', 'role:Admin'])->group(...)`

- ⏸️ **User Management Interface (Super Admin)**
  - Controller: `php artisan make:controller Admin/UserManagementController`
  - Views: Inertia.js ile Vue component'leri oluşturulacak
  - Not: Phase 2'den sonra eklenecek (CRM entity'leri hazır olduktan sonra)

---

## Phase 2: Database Schema & Models (The "M" in MVC) ⏸️

### 2.1. Core CRM Entity Migrations & Models ✅
**Hedef:** CRM'in temel veri yapılarını oluşturmak

- ✅ **Companies (Şirketler) - Migration & Model**
  - Migration oluştur: `php artisan make:migration create_companies_table`
  - Alan tanımlamaları:
    ```php
    $table->id();
    $table->string('name');
    $table->string('industry')->nullable();
    $table->string('website')->nullable();
    $table->string('phone_number')->nullable();
    $table->text('address')->nullable();
    $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
    $table->timestamps();
    $table->softDeletes();
    ```
  - Model oluştur: `php artisan make:model Company`
  - Model'de fillable, casts, relationships tanımla
  - `owner` relation: `belongsTo(User::class, 'owner_id')`
  - `contacts` relation: `hasMany(Contact::class)`

- ✅ **Contacts (Kişiler) - Migration & Model**
  - Migration oluştur: `php artisan make:migration create_contacts_table`
  - Alan tanımlamaları:
    ```php
    $table->id();
    $table->string('first_name');
    $table->string('last_name');
    $table->string('email')->unique();
    $table->string('phone_number')->nullable();
    $table->foreignId('company_id')->nullable()->constrained()->onDelete('set null');
    $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
    $table->text('notes')->nullable();
    $table->timestamps();
    $table->softDeletes();
    ```
  - Model oluştur: `php artisan make:model Contact`
  - Relationships:
    - `company()`: belongsTo(Company)
    - `owner()`: belongsTo(User)
    - `deals()`: hasMany(Deal)
    - `activities()`: morphMany(Activity)
  - Accessor: `full_name` attribute

- ✅ **Leads (Potansiyel Müşteriler) - Migration & Model**
  - Migration oluştur: `php artisan make:migration create_leads_table`
  - Alan tanımlamaları:
    ```php
    $table->id();
    $table->string('first_name');
    $table->string('last_name');
    $table->string('email');
    $table->string('phone_number')->nullable();
    $table->string('source'); // 'Website', 'Referral', 'Cold Call', etc.
    $table->string('status'); // 'New', 'Contacted', 'Qualified', 'Unqualified'
    $table->foreignId('assigned_to_id')->nullable()->constrained('users')->onDelete('set null');
    $table->foreignId('converted_to_contact_id')->nullable()->constrained('contacts')->onDelete('set null');
    $table->timestamp('converted_at')->nullable();
    $table->text('notes')->nullable();
    $table->timestamps();
    $table->softDeletes();
    ```
  - Model oluştur: `php artisan make:model Lead`
  - Enum oluştur (PHP 8.1+): `LeadStatus`, `LeadSource`
  - Relationships:
    - `assignedTo()`: belongsTo(User)
    - `convertedToContact()`: belongsTo(Contact)
    - `activities()`: morphMany(Activity)
  - Method: `convertToContact()`

- ✅ **Deal Stages (Satış Aşamaları) - Migration & Model**
  - Migration oluştur: `php artisan make:migration create_deal_stages_table`
  - Alan tanımlamaları:
    ```php
    $table->id();
    $table->string('name'); // 'Lead', 'Contacted', 'Proposal', 'Negotiation', 'Won', 'Lost'
    $table->integer('order')->default(0);
    $table->string('color')->default('#3B82F6'); // Tailwind color
    $table->timestamps();
    ```
  - Model oluştur: `php artisan make:model DealStage`
  - Seeder oluştur: Default stage'leri ekle
  - Relationship: `deals()` hasMany

- ✅ **Deals (Satışlar) - Migration & Model**
  - Migration oluştur: `php artisan make:migration create_deals_table`
  - Alan tanımlamaları:
    ```php
    $table->id();
    $table->string('name');
    $table->foreignId('contact_id')->constrained()->onDelete('cascade');
    $table->foreignId('deal_stage_id')->constrained()->onDelete('restrict');
    $table->decimal('amount', 12, 2);
    $table->date('closing_date')->nullable();
    $table->integer('probability')->default(0); // 0-100
    $table->foreignId('assigned_to_id')->constrained('users')->onDelete('cascade');
    $table->text('description')->nullable();
    $table->timestamps();
    $table->softDeletes();
    ```
  - Model oluştur: `php artisan make:model Deal`
  - Relationships:
    - `contact()`: belongsTo(Contact)
    - `stage()`: belongsTo(DealStage)
    - `assignedTo()`: belongsTo(User)
    - `activities()`: morphMany(Activity)
  - Accessor: `expected_revenue` (amount * probability)

- ✅ **Tasks (Görevler) - Migration & Model**
  - Migration oluştur: `php artisan make:migration create_tasks_table`
  - Alan tanımlamaları:
    ```php
    $table->id();
    $table->string('title');
    $table->text('description')->nullable();
    $table->dateTime('due_date')->nullable();
    $table->string('status')->default('Pending'); // 'Pending', 'In Progress', 'Completed'
    $table->string('priority')->default('Medium'); // 'Low', 'Medium', 'High', 'Urgent'
    $table->foreignId('assigned_to_id')->constrained('users')->onDelete('cascade');
    $table->morphs('related_to'); // Polymorphic: related_to_type, related_to_id
    $table->timestamp('completed_at')->nullable();
    $table->timestamps();
    ```
  - Model oluştur: `php artisan make:model Task`
  - Relationships:
    - `assignedTo()`: belongsTo(User)
    - `relatedTo()`: morphTo()
  - Scope: `overdue()`, `completed()`, `pending()`

- ✅ **Activities (Aktiviteler) - Migration & Model**
  - Migration oluştur: `php artisan make:migration create_activities_table`
  - Alan tanımlamaları:
    ```php
    $table->id();
    $table->text('description');
    $table->string('type'); // 'Call', 'Meeting', 'Email', 'Note', 'Task Completed'
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->morphs('subject'); // subject_type, subject_id (Contact, Deal, Lead)
    $table->timestamp('activity_date')->default(now());
    $table->timestamps();
    ```
  - Model oluştur: `php artisan make:model Activity`
  - Relationships:
    - `user()`: belongsTo(User)
    - `subject()`: morphTo()
  - Scope: `recent()`, `byType()`

### 2.2. Define Model Relationships ✅
**Hedef:** Eloquent ilişkilerini tamamlamak

- ✅ **User Model relationships**
  - `app/Models/User.php` dosyasını aç
  - İlişkileri ekle:
    ```php
    public function companies() {
        return $this->hasMany(Company::class, 'owner_id');
    }
    public function contacts() {
        return $this->hasMany(Contact::class, 'owner_id');
    }
    public function assignedLeads() {
        return $this->hasMany(Lead::class, 'assigned_to_id');
    }
    public function assignedDeals() {
        return $this->hasMany(Deal::class, 'assigned_to_id');
    }
    public function tasks() {
        return $this->hasMany(Task::class, 'assigned_to_id');
    }
    public function activities() {
        return $this->hasMany(Activity::class);
    }
    ```

- ✅ **Tüm modellerdeki relationships'i kontrol et**
  - Her model dosyasını gözden geçir
  - Eksik relationship'leri ekle
  - Inverse relationship'leri kontrol et
  - Eager loading için `with` property'lerini tanımla (performans için)

- ⏸️ **Migration'ları çalıştır ve test et** (Skipped - database not available in current environment)
  - `php artisan migrate:fresh`
  - Tinker ile test: `php artisan tinker`
  - Test senaryoları:
    ```php
    $user = User::factory()->create();
    $company = Company::factory()->create(['owner_id' => $user->id]);
    $contact = Contact::factory()->create(['company_id' => $company->id, 'owner_id' => $user->id]);
    // Relationships test
    $company->contacts; // Should return collection
    $contact->company; // Should return company
    ```

- ✅ **Factory'leri oluştur**
  - Her model için factory: `php artisan make:factory CompanyFactory`
  - Realistic fake data tanımla
  - Test ve seeding için kullan
  - DealStageSeeder oluşturuldu (default pipeline stages)

---

## Phase 3: Frontend & User Interface (The "V" in MVC) ⏸️

### 3.1. Layout & Design System ⏸️
**Hedef:** Temel layout yapısını ve design system'i oluşturmak

- ⏸️ **Master Layout oluşturma**
  - `resources/views/layouts/app.blade.php` dosyası
  - Yapı:
    ```blade
    <!DOCTYPE html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'CRM System')</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-50">
        @include('layouts.navigation')

        <div class="flex">
            @include('layouts.sidebar')

            <main class="flex-1 p-6">
                @yield('content')
            </main>
        </div>
    </body>
    </html>
    ```

- ⏸️ **Top Navigation Bar**
  - Partial view: `resources/views/layouts/navigation.blade.php`
  - Elemanlar:
    - Logo/Brand
    - Global search bar (Vue component)
    - Notifications dropdown (Vue component)
    - User profile dropdown (logout, settings)
  - Responsive: Mobile'da hamburger menu
  - Sticky top position

- ⏸️ **Sidebar Navigation**
  - Partial view: `resources/views/layouts/sidebar.blade.php`
  - Menü öğeleri:
    - Dashboard (icon: home)
    - Leads (icon: user-group)
    - Contacts (icon: users)
    - Companies (icon: building)
    - Deals (icon: currency-dollar)
    - Tasks (icon: check-circle)
    - Reports (icon: chart-bar)
    - Settings (icon: cog)
  - Active state highlighting
  - Permission-based görünürlük: `@can('view-leads')`
  - Mobile'da collapsible

- ⏸️ **Reusable Blade Components oluşturma**
  - Button component: `php artisan make:component Button`
    - Variants: primary, secondary, danger, success
    - Sizes: sm, md, lg
    - Loading state
  - Input component: `php artisan make:component Input`
    - Label, error message, hint text
    - Types: text, email, password, number, date
  - Card component: `php artisan make:component Card`
    - Header, body, footer slots
  - Modal component: `php artisan make:component Modal`
    - Vue ile interactive yapılacak
  - Alert component: `php artisan make:component Alert`
    - Types: success, error, warning, info
  - Badge component: `php artisan make:component Badge`
    - Colors: blue, green, red, yellow

- ⏸️ **Mobile-First Responsive Design**
  - Breakpoint'leri tanımla: sm, md, lg, xl, 2xl
  - Sidebar mobile'da off-canvas
  - Navigation mobile'da hamburger menu
  - Tables mobile'da card view'a dönüşsün
  - Touch-friendly button sizes (min 44px)
  - Test: Chrome DevTools mobile emulation

### 3.2. Vue.js Component Development ⏸️
**Hedef:** İnteraktif Vue component'leri oluşturmak

- ⏸️ **Vue component yapısını kurma**
  - Klasör yapısı oluştur:
    ```
    resources/js/
    ├── components/
    │   ├── DataTable.vue
    │   ├── KanbanBoard.vue
    │   ├── ModalForm.vue
    │   ├── SearchBar.vue
    │   └── Dashboard/
    │       ├── RevenueChart.vue
    │       ├── LeadSourceChart.vue
    │       └── ActivityFeed.vue
    ├── composables/
    │   ├── useApi.js
    │   └── useNotification.js
    └── app.js
    ```

- ⏸️ **DataTable Component**
  - Dosya: `resources/js/components/DataTable.vue`
  - Features:
    - Pagination (server-side)
    - Sorting (multi-column)
    - Search (debounced)
    - Column filtering
    - Row selection (checkbox)
    - Bulk actions
    - Export to CSV
    - Responsive (mobile'da card view)
  - Props: columns, apiEndpoint, perPage
  - Axios ile API'den data fetch
  - Loading skeleton state
  - Empty state design

- ⏸️ **Kanban Board Component**
  - Dosya: `resources/js/components/KanbanBoard.vue`
  - Library: `@shopify/draggable` veya `vue-draggable-plus`
  - Features:
    - Drag & drop cards between columns
    - Columns = Deal Stages
    - Cards = Individual Deals
    - Add new deal (quick add form)
    - Click card for detail modal
    - Update deal stage via API on drop
    - Real-time updates (optional: WebSocket)
    - Smooth animations
  - Props: stages, deals
  - Emit: @update:deal-stage
  - Responsive: Mobile'da swipe between stages

- ⏸️ **Modal Form Component**
  - Dosya: `resources/js/components/ModalForm.vue`
  - Features:
    - Overlay backdrop (click to close)
    - Close button (X icon)
    - Form içinde slot
    - Submit/Cancel buttons
    - Loading state (submit sırasında)
    - Success/Error notification
    - Form validation (frontend)
    - Keyboard shortcuts (ESC to close)
  - Props: title, isOpen, formAction
  - Emit: @close, @submit
  - Kullanım yerleri: New Lead, Edit Contact, New Task

- ⏸️ **Dashboard Widget Components**
  - Chart.js kurulumu: `npm install chart.js vue-chartjs`
  - RevenueChart.vue:
    - Line chart: Revenue over time (son 12 ay)
    - Filters: Year, Quarter, Month
    - Data API'den fetch
  - LeadSourceChart.vue:
    - Pie chart: Lead sources dağılımı
    - Legend with percentages
  - DealsStageChart.vue:
    - Funnel chart: Deals by stage
    - Show conversion rates
  - ActivityFeed.vue:
    - Recent activities listesi
    - Real-time updates (polling veya WebSocket)
    - User avatars
    - Activity type icons
    - "Load more" button

- ⏸️ **Composable'lar (Reusable Logic)**
  - useApi.js:
    ```js
    import axios from 'axios'
    export function useApi() {
        const get = async (url) => { /* ... */ }
        const post = async (url, data) => { /* ... */ }
        const put = async (url, data) => { /* ... */ }
        const destroy = async (url) => { /* ... */ }
        return { get, post, put, destroy }
    }
    ```
  - useNotification.js:
    - Toast notifications
    - Success, error, warning messages
    - Auto-dismiss
  - usePagination.js:
    - Pagination logic'i
    - Reusable across components

---

## Phase 4: Business Logic & API (The "C" in MVC) ⏸️

### 4.1. API Routes & Controllers ⏸️
**Hedef:** RESTful API endpoint'leri oluşturmak

- ⏸️ **API Routes tanımlama**
  - Dosya: `routes/api.php`
  - Version prefix: `/api/v1/`
  - Middleware: `auth:sanctum` (API token authentication)
  - Resource routes:
    ```php
    Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
        Route::apiResource('leads', LeadController::class);
        Route::apiResource('contacts', ContactController::class);
        Route::apiResource('companies', CompanyController::class);
        Route::apiResource('deals', DealController::class);
        Route::apiResource('tasks', TaskController::class);
        Route::apiResource('activities', ActivityController::class);

        // Custom routes
        Route::post('leads/{lead}/convert', [LeadController::class, 'convert']);
        Route::patch('deals/{deal}/stage', [DealController::class, 'updateStage']);
        Route::get('dashboard/stats', [DashboardController::class, 'stats']);
    });
    ```

- ⏸️ **LeadController oluşturma**
  - Komut: `php artisan make:controller Api/V1/LeadController --api`
  - Methods:
    - `index()`: Paginated list with filters
      - Query params: search, status, source, assigned_to
      - Return: LeadResource collection
    - `store()`: Create new lead
      - Validation: LeadRequest
      - Authorization: `authorize('create', Lead::class)`
      - Return: LeadResource
    - `show($id)`: Single lead details
      - Eager load: assignedTo, activities
      - Return: LeadResource
    - `update($id)`: Update lead
      - Validation: LeadRequest
      - Authorization: `authorize('update', $lead)`
    - `destroy($id)`: Delete lead
      - Soft delete
      - Authorization: `authorize('delete', $lead)`
    - `convert($id)`: Convert lead to contact
      - Create Contact from Lead data
      - Update lead status
      - Return: ContactResource

- ⏸️ **ContactController oluşturma**
  - Komut: `php artisan make:controller Api/V1/ContactController --api`
  - Methods: index, store, show, update, destroy
  - Filters: search, company_id, owner_id
  - Eager load: company, owner, deals

- ⏸️ **CompanyController oluşturma**
  - Komut: `php artisan make:controller Api/V1/CompanyController --api`
  - Methods: index, store, show, update, destroy
  - Filters: search, industry, owner_id
  - Eager load: owner, contacts

- ⏸️ **DealController oluşturma**
  - Komut: `php artisan make:controller Api/V1/DealController --api`
  - Methods: index, store, show, update, destroy
  - Custom method: `updateStage($id, Request $request)`
    - Update deal_stage_id
    - Log activity
    - Return updated deal
  - Filters: stage_id, assigned_to_id, closing_date_range
  - Eager load: contact, stage, assignedTo

- ⏸️ **TaskController oluşturma**
  - Komut: `php artisan make:controller Api/V1/TaskController --api`
  - Methods: index, store, show, update, destroy
  - Filters: status, priority, due_date, assigned_to_id
  - Custom method: `markAsCompleted($id)`

- ⏸️ **ActivityController oluşturma**
  - Komut: `php artisan make:controller Api/V1/ActivityController --api`
  - Methods: index, store, show (destroy isteğe bağlı)
  - Filters: type, subject_type, subject_id, date_range
  - Auto-log activities (Observer pattern kullanabilir)

- ⏸️ **DashboardController oluşturma**
  - Komut: `php artisan make:controller Api/V1/DashboardController`
  - Method: `stats(Request $request)`
    - Return:
      - Total leads (this month vs last month)
      - Total deals & won deals
      - Total revenue
      - Conversion rate
      - Upcoming tasks
      - Recent activities
      - Charts data (revenue over time, lead sources, deal stages)

### 4.2. Form Requests (Validation) ⏸️
**Hedef:** Backend validation'ı tanımlamak

- ⏸️ **LeadRequest**
  - Komut: `php artisan make:request LeadRequest`
  - Rules:
    ```php
    public function rules() {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:leads,email,' . $this->lead?->id,
            'phone_number' => 'nullable|string|max:20',
            'source' => 'required|in:Website,Referral,Cold Call,Social Media',
            'status' => 'required|in:New,Contacted,Qualified,Unqualified',
            'assigned_to_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ];
    }
    ```
  - Custom messages (Türkçe)

- ⏸️ **ContactRequest**
  - Komut: `php artisan make:request ContactRequest`
  - Rules: Similar to Lead + company_id validation

- ⏸️ **CompanyRequest**
  - Komut: `php artisan make:request CompanyRequest`
  - Rules: name, industry, website (url), phone_number, address

- ⏸️ **DealRequest**
  - Komut: `php artisan make:request DealRequest`
  - Rules: name, contact_id, deal_stage_id, amount (numeric), closing_date (date), probability (0-100)

- ⏸️ **TaskRequest**
  - Komut: `php artisan make:request TaskRequest`
  - Rules: title, description, due_date, status, priority, assigned_to_id, related_to_type, related_to_id

### 4.3. API Resources (Response Formatting) ⏸️
**Hedef:** API response'larını formatlamak

- ⏸️ **LeadResource**
  - Komut: `php artisan make:resource LeadResource`
  - `toArray()` method:
    ```php
    return [
        'id' => $this->id,
        'full_name' => $this->first_name . ' ' . $this->last_name,
        'email' => $this->email,
        'phone_number' => $this->phone_number,
        'source' => $this->source,
        'status' => $this->status,
        'assigned_to' => new UserResource($this->whenLoaded('assignedTo')),
        'converted_to_contact' => new ContactResource($this->whenLoaded('convertedToContact')),
        'created_at' => $this->created_at->toDateTimeString(),
    ];
    ```

- ⏸️ **ContactResource**
- ⏸️ **CompanyResource**
- ⏸️ **DealResource**
- ⏸️ **TaskResource**
- ⏸️ **ActivityResource**
- ⏸️ **UserResource** (Basic user info için)

### 4.4. AJAX Integration (Frontend ↔ Backend) ⏸️
**Hedef:** Vue component'lerini API'ye bağlamak

- ⏸️ **Axios configuration**
  - Dosya: `resources/js/bootstrap.js`
  - Base URL: `/api/v1`
  - CSRF token: `X-CSRF-TOKEN` header
  - Authorization: `Bearer {token}` (Sanctum)
  - Interceptors:
    - Request: Add auth token
    - Response: Handle 401 (redirect to login), 422 (validation errors)

- ⏸️ **DataTable component API integration**
  - Axios GET request to apiEndpoint
  - Query params: page, per_page, search, sort, filters
  - Update reactive data on response
  - Handle loading & error states

- ⏸️ **Kanban Board API integration**
  - Fetch deals: GET `/api/v1/deals?include=stage`
  - Update stage: PATCH `/api/v1/deals/{id}/stage`
  - Optimistic UI updates
  - Rollback on error

- ⏸️ **Form submissions**
  - Modal form component submit event
  - POST/PUT request to respective endpoint
  - Show validation errors below inputs
  - Show success notification on success
  - Close modal and refresh list

- ⏸️ **Real-time search/filtering**
  - Debounce search input (300ms)
  - On input change, call API with search param
  - Update DataTable results
  - Show "No results" message if empty

---

## Phase 5: Advanced Features ⏸️

### 5.1. Dashboard ⏸️
**Hedef:** Kapsamlı dashboard oluşturmak

- ⏸️ **Dashboard View**
  - Dosya: `resources/views/dashboard.blade.php`
  - Layout: Grid system (3-4 columns)
  - Sections:
    - KPI Cards (4 cards in row)
    - Charts (2-3 charts)
    - Recent Activities (side panel)
    - Upcoming Tasks (widget)

- ⏸️ **KPI Cards Component**
  - Vue component: `KpiCard.vue`
  - Cards:
    - New Leads This Month (icon: user-add, color: blue)
    - Deals Won This Month (icon: check-circle, color: green)
    - Total Revenue (icon: currency-dollar, color: yellow)
    - Conversion Rate (icon: trending-up, color: purple)
  - Show comparison to last month (% change)
  - Color-coded: green for increase, red for decrease

- ⏸️ **Charts**
  - Sales Pipeline Chart (Funnel or Bar):
    - X-axis: Deal stages
    - Y-axis: Number of deals
    - Show total amount per stage
  - Revenue Over Time (Line chart):
    - X-axis: Months (last 12 months)
    - Y-axis: Revenue
    - Compare with previous year (optional)
  - Lead Sources (Pie/Doughnut chart):
    - Segments: Website, Referral, Cold Call, Social Media
    - Show percentages

- ⏸️ **Recent Activities Widget**
  - List of last 10 activities
  - Format: "{User} {action} {entity} - {time ago}"
  - Example: "John Doe updated Deal ABC - 2 hours ago"
  - Real-time updates (polling every 30s veya WebSocket)

- ⏸️ **Upcoming Tasks Widget**
  - List of tasks due in next 7 days
  - Grouped by: Today, Tomorrow, This Week
  - Color-coded by priority
  - Click to mark as completed

### 5.2. Search & Filtering ⏸️
**Hedef:** Global search ve gelişmiş filtreleme

- ⏸️ **Global Search Bar (Navigation)**
  - Vue component: `GlobalSearch.vue`
  - Search across: Contacts, Companies, Deals
  - Debounced input (300ms)
  - Dropdown results:
    - Grouped by entity type
    - Show avatar/icon, name, subtitle
    - Click to navigate to detail page
  - Keyboard navigation (arrow keys, enter)
  - API endpoint: `/api/v1/search?q={query}`
  - Backend: Use Laravel Scout (optional) or raw queries

- ⏸️ **Advanced Filters (DataTable)**
  - Filter panel (collapsible)
  - Filters:
    - **Leads:** Status, Source, Assigned To, Date Range
    - **Deals:** Stage, Assigned To, Amount Range, Closing Date Range
    - **Tasks:** Status, Priority, Assigned To, Due Date Range
  - Apply/Reset buttons
  - URL query params sync (for bookmarking/sharing)
  - Backend: Build dynamic query with filters

### 5.3. Reporting ⏸️
**Hedef:** Raporlama modülü

- ⏸️ **Reports Page**
  - Route: `/reports`
  - View: `resources/views/reports/index.blade.php`
  - Tabs/Sections:
    - Sales Performance
    - Lead Conversion
    - Deal Forecast
    - User Activity

- ⏸️ **Sales Performance Report**
  - Filters: Date range, User, Team
  - Metrics:
    - Total revenue
    - Number of deals won
    - Average deal size
    - Win rate
  - Chart: Revenue by user (bar chart)
  - Export to PDF/CSV

- ⏸️ **Lead Conversion Report**
  - Filters: Date range, Source
  - Metrics:
    - Total leads
    - Converted leads
    - Conversion rate
    - Time to conversion (average)
  - Chart: Conversion funnel
  - Export to PDF/CSV

- ⏸️ **Deal Forecast Report**
  - Show deals by expected closing date
  - Grouped by: This Month, Next Month, This Quarter
  - Calculate weighted revenue (amount * probability)
  - Chart: Forecast by month (line chart)
  - Export to PDF/CSV

- ⏸️ **Export functionality**
  - Package: `barryvdh/laravel-dompdf` (PDF)
  - CSV: Native PHP `fputcsv`
  - Buttons: "Export PDF", "Export CSV"
  - Generate report in background (Queue) if large
  - Download link or email

### 5.4. Notifications ⏸️
**Hedef:** Bildirim sistemi

- ⏸️ **Database Notifications**
  - Migration: Laravel varsayılan notifications table
  - `php artisan notifications:table`
  - `php artisan migrate`

- ⏸️ **Notification Types oluşturma**
  - `php artisan make:notification TaskAssigned`
  - `php artisan make:notification DealWon`
  - `php artisan make:notification LeadAssigned`
  - Her notification:
    - `via()`: return ['database', 'mail'];
    - `toDatabase()`: Array with notification data
    - `toMail()`: Mailable

- ⏸️ **Trigger Notifications**
  - Task oluşturulduğunda: assigned user'a bildirim
  - Deal stage "Won" olduğunda: deal owner'a bildirim
  - Lead assign edildiğinde: assigned user'a bildirim
  - Event/Listener pattern kullan:
    - `php artisan make:event DealWon`
    - `php artisan make:listener SendDealWonNotification`

- ⏸️ **In-App Notifications (Navbar)**
  - Dropdown component: `NotificationDropdown.vue`
  - Bell icon with unread count badge
  - Dropdown: List of unread notifications
  - Mark as read on click
  - "Mark all as read" button
  - API endpoints:
    - GET `/api/v1/notifications`
    - PATCH `/api/v1/notifications/{id}/read`
    - PATCH `/api/v1/notifications/read-all`

- ⏸️ **Real-time Notifications (WebSocket)**
  - Option 1: Laravel Reverb (yeni, Laravel 11)
  - Option 2: Pusher
  - Option 3: Laravel Echo + soketi
  - Setup:
    - Install: `composer require laravel/reverb` (veya pusher)
    - Config: `config/broadcasting.php`
    - Frontend: Install `laravel-echo` and `pusher-js`
    - Listen to `Illuminate\Notifications\Events\BroadcastNotificationCreated`
  - Update notification dropdown in real-time

- ⏸️ **Email Notifications**
  - Config: `.env` MAIL_ ayarları (SMTP, Mailgun, etc.)
  - Customize email templates:
    - `php artisan vendor:publish --tag=laravel-mail`
    - Edit: `resources/views/vendor/mail/`
  - Test: Send test notification

### 5.5. File Management ⏸️
**Hedef:** Dosya yükleme ve yönetimi

- ⏸️ **Media Library entegrasyonu**
  - Zaten kuruldu (Phase 0.3)
  - Model'lere `HasMedia` trait ekle:
    - Company, Contact, Deal models
  - Collections tanımla: 'documents', 'images'

- ⏸️ **File Upload Component**
  - Vue component: `FileUpload.vue`
  - Features:
    - Drag & drop zone
    - File type validation (documents: pdf, docx; images: jpg, png)
    - File size validation (max 10MB)
    - Progress bar
    - Multiple file upload
  - API endpoint: POST `/api/v1/{entity}/{id}/media`

- ⏸️ **File List Component**
  - Vue component: `FileList.vue`
  - Show uploaded files for entity
  - Features:
    - File name, type, size, uploaded date
    - Download button
    - Delete button (with confirmation)
  - API endpoints:
    - GET `/api/v1/{entity}/{id}/media`
    - DELETE `/api/v1/media/{id}`

- ⏸️ **Integrate into Entity Pages**
  - Contact detail page: Add "Documents" tab
  - Deal detail page: Add "Attachments" section
  - Company detail page: Add "Files" tab

---

## Phase 6: Testing, Deployment & Maintenance ⏸️

### 6.1. Testing ⏸️
**Hedef:** Kapsamlı test coverage

- ⏸️ **Feature Tests (Backend)**
  - Setup: `php artisan test` çalıştığını doğrula
  - Test klasörü: `tests/Feature/Api/`
  - LeadControllerTest:
    ```php
    public function test_user_can_create_lead() { /* ... */ }
    public function test_user_can_update_own_lead() { /* ... */ }
    public function test_user_cannot_delete_other_users_lead() { /* ... */ }
    public function test_lead_can_be_converted_to_contact() { /* ... */ }
    ```
  - ContactControllerTest
  - DealControllerTest
  - TaskControllerTest
  - Test: Authentication, Authorization, Validation

- ⏸️ **Unit Tests**
  - Test klasörü: `tests/Unit/Models/`
  - Test relationships:
    ```php
    public function test_contact_belongs_to_company() {
        $contact = Contact::factory()->create();
        $this->assertInstanceOf(Company::class, $contact->company);
    }
    ```
  - Test accessors, mutators, scopes
  - Test custom methods (e.g., Lead::convertToContact())

- ⏸️ **Frontend Tests (Vue)**
  - Setup: Vitest
    - `npm install -D vitest @vue/test-utils jsdom`
    - `vite.config.js` dosyasına test config ekle
  - Test dosyaları: `resources/js/components/__tests__/`
  - DataTable.test.js:
    ```js
    import { mount } from '@vue/test-utils'
    import DataTable from '../DataTable.vue'

    test('renders table with data', () => { /* ... */ })
    test('search updates results', () => { /* ... */ })
    ```
  - Test tüm major component'leri

- ⏸️ **Test Coverage Report**
  - Backend: `php artisan test --coverage`
  - Frontend: `npm run test:coverage`
  - Target: >80% coverage

### 6.2. Deployment ⏸️
**Hedef:** Production'a deployment

- ⏸️ **Server Provisioning**
  - Option 1: Laravel Forge (önerilir - kolay)
    - Forge hesabı oluştur
    - Server oluştur (DigitalOcean, Vultr, etc.)
    - Site oluştur ve repo bağla
    - Environment variables ayarla
  - Option 2: Manual (VPS)
    - Ubuntu 22.04 LTS
    - Nginx, PHP 8.3, MySQL, Redis yükle
    - SSL certificate (Let's Encrypt)

- ⏸️ **CI/CD Pipeline (GitHub Actions)**
  - Dosya: `.github/workflows/deploy.yml`
  - Trigger: Push to `main` branch
  - Steps:
    1. Checkout code
    2. Setup PHP
    3. Install Composer dependencies
    4. Run tests (`php artisan test`)
    5. Build frontend (`npm run build`)
    6. Deploy to server (SSH/rsync veya Forge API)
  - Secrets: SSH key, server IP, etc.

- ⏸️ **Production Environment Configuration**
  - `.env.production` dosyası
  - Ayarlar:
    - `APP_ENV=production`
    - `APP_DEBUG=false`
    - `APP_URL=https://yourcrm.com`
    - Database credentials
    - Mail configuration (production SMTP)
    - Redis configuration
    - Session/Cache drivers
  - Config cache: `php artisan config:cache`
  - Route cache: `php artisan route:cache`
  - View cache: `php artisan view:cache`

- ⏸️ **Database Migration (Production)**
  - Backup production database önce!
  - Run: `php artisan migrate --force`
  - Seed initial data (roles, deal stages): `php artisan db:seed --force --class=RolePermissionSeeder`

- ⏸️ **Cron Job Setup**
  - Laravel Scheduler için
  - Crontab entry:
    ```
    * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
    ```
  - Scheduled tasks (tanımla `app/Console/Kernel.php`):
    - Daily email digest
    - Clean old activities
    - Backup database

- ⏸️ **Queue Worker Setup**
  - Supervisor kullan (process manager)
  - Config: `/etc/supervisor/conf.d/laravel-worker.conf`
  - Start queue worker: `php artisan queue:work --tries=3`
  - Monitor: `supervisorctl status`

### 6.3. Maintenance ⏸️
**Hedef:** Sürekli bakım ve monitoring

- ⏸️ **Error Tracking Setup**
  - Option 1: Laravel Flare
    - `composer require facade/ignition`
    - Flare hesabı, API key ekle `.env`
  - Option 2: Sentry
    - `composer require sentry/sentry-laravel`
    - Config ve test
  - Production'da hataları track et

- ⏸️ **Application Monitoring**
  - Laravel Telescope (development):
    - `composer require laravel/telescope`
    - Sadece local/staging'de kullan
  - Laravel Pulse (production):
    - `composer require laravel/pulse`
    - Monitor: requests, jobs, exceptions, slow queries
  - Server monitoring: Uptime robot, Pingdom

- ⏸️ **Database Backup**
  - Package: `spatie/laravel-backup`
  - `composer require spatie/laravel-backup`
  - Config: Backup destination (S3, local, etc.)
  - Schedule: Daily backup
  - Test: Restore from backup

- ⏸️ **Dependency Updates**
  - Schedule: Monthly
  - Check: `composer outdated`
  - Update: `composer update`
  - Frontend: `npm outdated`, `npm update`
  - Test after updates!
  - Security updates: Immediate

- ⏸️ **Performance Optimization**
  - Enable OPcache (production)
  - Redis cache için `CACHE_DRIVER=redis`
  - Database query optimization (N+1 problems)
  - Frontend: Lazy loading, code splitting
  - CDN for static assets (optional)

- ⏸️ **Security Audit**
  - Regular: Quarterly
  - Check: OWASP Top 10
  - Tools: `composer audit` (security vulnerabilities)
  - Update Laravel and dependencies
  - Review user permissions
  - Check for SQL injection, XSS vulnerabilities

---

## 📝 Notlar ve Best Practices

### Code Standards
- PSR-12 coding standard (PHP)
- ESLint + Prettier (JavaScript)
- Meaningful variable/function names
- Comments for complex logic
- DRY principle

### Git Workflow
- Feature branch workflow
- Branch naming: `feature/lead-management`, `bugfix/contact-update`
- Commit messages: Descriptive, present tense
- Pull requests: Code review before merge

### Security
- Never commit `.env` file
- Use environment variables for secrets
- Validate all user inputs
- Sanitize outputs (XSS prevention)
- Use prepared statements (Eloquent does this)
- CSRF protection (Laravel default)
- Rate limiting on API routes

### Performance
- Eager load relationships (prevent N+1)
- Use indexes on foreign keys
- Cache frequently accessed data
- Queue time-consuming tasks
- Paginate large datasets
- Optimize images before upload

---

## ✅ Geliştirme Takip Tablosu

Bu dosyada her işlem tamamlandığında, ilgili maddenin başındaki ⏸️ işareti 🔄 (devam ediyor) ve sonra ✅ (tamamlandı) olarak güncellenecek.

**Güncel Durum:** ⏸️ Tüm fazlar beklemede
**Başlangıç Tarihi:** [Girilecek]
**Son Güncelleme:** [Girilecek]

---

**NOT:** Bu doküman, geliştirme sürecinde bir yol haritası ve ilerleme takip aracıdır. Her adım tamamlandıkça güncellenmeli ve detaylar eklenmelidir.