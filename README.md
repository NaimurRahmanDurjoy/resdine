# ResDine - Enterprise Restaurant Management, POS & ERP System

ResDine is an enterprise-grade, modern, and comprehensive Restaurant Management System (RMS) designed to handle all aspects of restaurant operations. It integrates a customer-facing digital menu, a real-time Point of Sale (POS) terminal, a Kitchen Display System (KDS), double-entry accounting, inventory control, HR/Payroll management, and driver dispatching into a unified dashboard.

Built with **Laravel 12**, **Inertia.js**, and **Vue 3**, ResDine features real-time synchronization out of the box using WebSockets, providing instant updates across all panels (POS, KDS, customer ordering, and drivers).

---

## 🏗️ Architecture & System Design

ResDine is built with a **Service-Oriented MVC architecture** tailored for a seamless Single Page Application (SPA) experience using **Inertia.js**. 

### System Design Diagram

```mermaid
graph TD
    %% Client Tier
    subgraph Client Tier (Vue 3 / Tailwind CSS)
        Customer[Customer Web Menu & Order Tracking]
        POS[Cashier POS Terminal & Register]
        KDS[Kitchen Display System - Expo/Prep]
        Driver[Driver Dispatch & Dashboard]
        AdminPanel[Admin Dashboard & ERP Panel]
    end

    %% Routing & Real-Time Tier
    subgraph Communication Tier
        Inertia[Inertia.js Routing / State Hydration]
        Echo[Laravel Echo / WebSocket Channels]
    end

    %% Application Tier
    subgraph Application Tier (Laravel 12 / PHP 8.2+)
        Controllers[HTTP Controllers / Middlewares]
        Reverb[Laravel Reverb WebSocket Server]
        
        subgraph Services Layer
            OrderSvc[OrderService]
            RecipeSvc[RecipeService]
            AcctSvc[AccountingService]
            PayMngr[PaymentManager & Gateways]
            NotifSvc[NotificationService]
        end
    end

    %% Storage Tier
    subgraph Storage Tier
        DB[(MySQL / PostgreSQL)]
        Cache[(Redis Cache / Queue)]
    end

    %% External Systems
    subgraph External Gateways
        Gateways[Stripe / bKash / SSLCommerz]
    end

    %% Connections
    Customer & POS & Driver & AdminPanel --> Inertia
    KDS & Customer & POS & Driver -.-> Echo
    Echo <--> Reverb
    Inertia <--> Controllers
    Controllers --> ServicesLayer
    
    %% Service connections
    OrderSvc --> RecipeSvc
    OrderSvc --> AcctSvc
    OrderSvc --> NotifSvc
    OrderSvc --> PayMngr
    
    ServicesLayer <--> DB
    ServicesLayer <--> Cache
    PayMngr <--> Gateways
```

---

## 🛠️ Tech Stack

ResDine uses a modern, high-performance stack:

| Layer | Technology | Key Details |
| :--- | :--- | :--- |
| **Backend Framework** | [Laravel 12](https://laravel.com) | MVC framework, Eloquent ORM, Job Queues, Event Broadcasting |
| **Frontend Framework** | [Vue 3](https://vuejs.org/) | Composition API, high reactivity, reusable custom components |
| **SPA Middleware** | [Inertia.js](https://inertiajs.com/) | Eliminates the need for traditional REST/GraphQL APIs by routing directly from backend controller responses to Vue page templates |
| **Styling** | [Tailwind CSS](https://tailwindcss.com/) | Premium, responsive, modern, utility-first UI styling |
| **Real-time Engine** | [Laravel Reverb](https://laravel.com/docs/reverb) | High-performance WebSocket server native to Laravel |
| **Asset Bundler** | [Vite](https://vite.dev/) | Ultra-fast frontend development and compilation |
| **Payments** | Custom Integrations | Multi-gateway support: Stripe, bKash, and SSLCommerz |
| **Analytics/Charts**| [Chart.js](https://www.chartjs.org/) | Used for business metrics, hourly heatmaps, and sales performance graphs |

---

## 🌟 Core Features

### 1. Customer Ordering & Live Tracking
*   **Digital Menu:** Interactive menu categorized with item modifiers and product variants (e.g., size, extras).
*   **Order Tracking:** Live step-by-step progress tracking (Pending ➡️ Preparing ➡️ Ready ➡️ Dispatched ➡️ Delivered).
*   **Payment Simulator:** Integrated simulator to test Stripe, bKash, and SSLCommerz gateways during development.

### 2. Point of Sale (POS) & Register Shifts
*   **Dynamic POS Grid:** Responsive menu selection with categories, instant variant modifiers, discount applications, and loyalty points validation.
*   **Register Shift Management:** Cashier registers with open/close flows tracking cash-in-hand, expected drawer balance, and opening/closing records.
*   **Table Management:** Floor-based restaurant table layout with real-time occupancy status tracking.

### 3. Kitchen Display System (KDS)
*   **Real-time Kitchen Screens:** Immediate ticket updates on order placement with VueDraggable interfaces.
*   **Expo & Line Prep Views:** Dedicated screens for food preparation tracking item-by-item status vs entire order dispatch.
*   **Instant Audio/Visual Alerts:** Notifies KDS screens of incoming items immediately using WebSockets.

### 4. Advanced Inventory & Recipe Control
*   **Ingredients & Suppliers:** Track ingredients, raw materials, and suppliers ledger.
*   **Stock Adjustment & Transfers:** Transfer inventory items between branches, adjust stock counts, and log waste entries.
*   **Recipe Ingredient Deduction:** Automatic deduction of raw ingredients from stock based on recipe ratios when finished products are sold in POS.
*   **Stock Ledger & Summaries:** Detailed logs tracking every stock movement (Purchase, POS Sale, Adjustment, Transfer, Waste).

### 5. Double-Entry Accounting
*   **Chart of Accounts (COA):** Structured asset, liability, equity, revenue, and expense accounts.
*   **Journal Vouchers:** Balanced debit-credit transactions with status workflows (Draft ➡️ Approved).
*   **POS Autoposting:** Cash receipts and food revenue are automatically journaled in real-time when POS transactions execute.
*   **Financial Reporting:** Automated Trial Balance, General Ledger, and Profit & Loss reports.


### 6. HR & Payroll
*   **Employee Directory:** Centralized files for employees, departments, and roles.
*   **Attendance & Leaves:** Register clock-ins, daily attendances, and request/approve leaves.
*   **Payroll:** Process monthly/weekly salaries, generate pay slips, and log expenses.


### 7. Branch & User Controls
*   **Multi-Branch Setup:** Switch environments, manage isolated inventories, and handle branch-level VAT and service charges.
*   **Granular Permissions:** Custom role structures (Admin, Manager, Cashier, Kitchen Staff, Driver, etc.) with specific access control lists.


## 📂 Project Directory Structure

```bash
resdine/
├── app/
│   ├── Http/
│   │   ├── Controllers/       # POS, KDS, HR, Inventory, Accounts, and Driver Controllers
│   │   └── Middleware/        # Auth, Role, and Custom Permission Middlewares
│   ├── Models/                # Eloquent Models (OrderMaster, Recipe, Supplier, Account, etc.)
│   └── Services/              # Core Business Logic (Order, Recipe, Accounting, Payments, Loyalty)
├── database/
│   ├── migrations/            # DB schemas for ERP, HR, POS, and Accounting Ledger
│   └── seeders/               # Initial system seeders (Roles, Accounts, Menu items)
├── resources/
│   ├── js/
│   │   ├── Components/        # Reusable Vue 3 UI components
│   │   ├── Layouts/           # Admin Dashboard layouts & Customer views
│   │   └── Pages/             # Inertia Pages (Admin, POS, KDS, Accounts, HR, Web)
│   └── views/                 # Blade templates (Inertia root, offline fallbacks)
├── routes/
│   ├── admin.php              # Software ERP Management, POS, KDS, and Accounting routes
│   └── web.php                # Customer-facing menu, tracking, and simulator routes
└── vite.config.js             # Vite configurations for compiling Vue 3 templates
```

---

## ⚡ Getting Started

### Prerequisites
*   PHP `>= 8.2`
*   Composer
*   Node.js & NPM
*   Database (MySQL or PostgreSQL)
<!--
### Installation Steps

1. **Clone the repository:**
   ```bash
   git clone https://github.com/your-username/resdine.git
   cd resdine
   ```

2. **Install Composer dependencies:**
   ```bash
   composer install
   ```

3. **Install NPM dependencies:**
   ```bash
   npm install
   ```

4. **Environment Configuration:**
   Copy the environment template file:
   ```bash
   cp .env.example .env
   ```
   Open `.env` and configure your database, Pusher/Reverb credentials, and payment settings.

5. **Generate Application Key:**
   ```bash
   php artisan key:generate
   ```

6. **Run Migrations & Seeders:**
   ```bash
   php artisan migrate --seed
   ```

7. **Start the Development Servers:**
   Run servers concurrently (Vite, Laravel App, Reverb/Queue listener):
   ```bash
   composer dev
   ```
-->
## Access the application

*   Customer Menu: `https://resdine.up.railway.app/menu`
*   Admin Portal: `https://resdine.up.railway.app/admin/login`

---

## 📜 License

This project is licensed under the MIT License.
