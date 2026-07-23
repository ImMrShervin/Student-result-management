<h1 align="center">🎓 SRMS — Student Result Management System</h1>

<p align="center">
  <strong>Enterprise-grade university academic management platform.</strong><br>
  Laravel 12 · PHP 8.4 · MySQL 8 · Redis · React 18 · TypeScript · TailwindCSS · Docker
</p>


<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-red?logo=laravel" />
  <img src="https://img.shields.io/badge/PHP-8.4-777bb4?logo=php" />
  <img src="https://img.shields.io/badge/React-18-61dafb?logo=react" />
  <img src="https://img.shields.io/badge/TypeScript-5-3178c6?logo=typescript" />
  <img src="https://img.shields.io/badge/Tailwind-3-38bdf8?logo=tailwindcss" />
  <img src="https://img.shields.io/badge/MySQL-8-4479a1?logo=mysql" />
  <img src="https://img.shields.io/badge/Redis-7-dc382d?logo=redis" />
  <img src="https://img.shields.io/badge/Docker-Ready-2496ed?logo=docker" />
  <img src="https://img.shields.io/badge/i18n-EN%20%2B%20FA-blue" />
  <img src="https://img.shields.io/badge/tests-Pest-8b5cf6" />
  <img src="https://img.shields.io/badge/License-MIT-green" />
</p>

---

## 📖 Overview

**SRMS** is a production-ready, full-stack university academic management platform designed as a showcase of **senior-level Laravel + React engineering**. It goes far beyond CRUD — implementing real domain logic (GPA calculation, prerequisite validation, credit caps, transcript issuance with QR verification), clean architecture (Service + Repository + DTO + Policy layers), and bilingual (English + Persian, RTL) support.

> 🎯 **Portfolio-ready.** Every layer is deliberately structured so a reviewer can jump to any file and understand *why* it exists.

---

## ✨ Features

### Core academic domain
- 🎓 **Multi-tenant academic hierarchy** — Faculties → Departments → Courses → Sections → Enrollments → Grades
- 📊 **Automatic GPA engine** — semester, cumulative, weighted; per-semester snapshots
- 🔒 **Prerequisite graph** — DAG with runtime validation before enrollment
- 🎯 **Business-rule enforcement** — capacity locking, duplicate prevention, credit-cap per semester
- 📈 **7-component weighted grading** — attendance / assignment / quiz / project / midterm / practical / final
- 🏆 **Automatic academic status** — Excellent / Passed / Conditional / Probation / Failed / Graduated / Dismissed
- 📜 **Signed PDF transcripts** with **QR verification** + public verification page


### Platform
- 🔐 **Laravel Sanctum** — SPA session + bearer PAT tokens with expiry
- 👥 **RBAC (Spatie)** — Super Admin, Admin, Dean, Department Head, Teacher, Student
- 🛡️ **Policies for every resource** — authorization on every controller action
- 📨 **Queued Notifications** — Email + database (Redis-backed queue via Horizon)
- 🌐 **Bilingual EN + FA** with RTL auto-switch
- 🌓 **Dark mode**
- 📤 **Import/Export** — students, teachers, courses, grades (Excel / CSV / PDF)
- 🔍 **Advanced search & filtering** on every list endpoint (Spatie Query Builder ready)
- 📋 **Activity log** for every mutation (Spatie ActivityLog)
- 🗑️ **Soft deletes** across all core entities
- 📚 **Swagger / OpenAPI** auto-generated documentation at `/api/documentation`
- ⚙️ **Docker + docker-compose** — one command to run everything
- 🚀 **GitHub Actions CI** — PHP tests, JS build, Docker build
- ✅ **Pest test suite** — unit + feature + auth + policy tests

### Dashboards & analytics
| Role | Sees |
|---|---|
| Super Admin / Admin | Everything: system-wide stats, grade distribution, top students, avg-GPA per department, academic status distribution |
| Dean | Cross-department analytics for their faculty |
| Department Head | Department teachers, courses, students, pending grades |
| Teacher | Own sections, students, pending grade entries |
| Student | Personal GPA, credits, recent enrollments, published grades |

---

## 🖼️ Screenshots

Place your screenshots under `docs/screenshots/`:

```
docs/screenshots/
  01-login.png              # Login screen with role demo buttons
  02-admin-dashboard.png    # Admin dashboard with charts
  03-students-list.png      # Students table with filters
  04-grade-distribution.png # Analytics charts
  05-transcript.png         # Generated PDF transcript
  06-verify-page.png        # Public transcript verification
  07-dark-mode.png          # Dark mode
  08-persian-rtl.png        # Persian RTL layout
```

---

## 🏗️ Architecture

```
Frontend  ──▶  Nginx  ──▶  PHP-FPM (Laravel 12)  ──▶  MySQL 8
   React                       │                        Redis 7
   Vite                        ├── Queue (Horizon)      Mailpit
   TS + Tailwind               ├── Scheduler
                               └── Swagger UI
```

Backend follows a **strict layered architecture**:

```
Controller (thin)  ─▶  FormRequest (validation)
       │
       ▼
  Policy (authorization)
       │
       ▼
  Service / Action (business logic, transactions)
       │
       ▼
  Repository (data access)
       │
       ▼
  Eloquent Model
```

Full details: **[docs/ARCHITECTURE.md](docs/ARCHITECTURE.md)** · ERD: **[docs/ERD.md](docs/ERD.md)**

---

## 📂 Project structure

```
srms/
├── backend/                          # Laravel 12 API
│   ├── app/
│   │   ├── Actions/                  # Single-purpose invokables
│   │   ├── Contracts/                # Repository / Service interfaces
│   │   ├── DTOs/                     # GradeInput and other value objects
│   │   ├── Enums/                    # LetterGrade, UserRole, AcademicStatus...
│   │   ├── Events/  Listeners/       # GradePublished → NotifyStudentOfGrade
│   │   ├── Http/
│   │   │   ├── Controllers/Api/V1/   # 10+ resource controllers
│   │   │   ├── Requests/             # FormRequests
│   │   │   └── Resources/            # API JSON transformers
│   │   ├── Imports/ Exports/         # Maatwebsite Excel
│   │   ├── Models/                   # 14 Eloquent models
│   │   ├── Notifications/            # Queued Mail + DB notifications
│   │   ├── Policies/                 # 8 policies (one per resource)
│   │   ├── Providers/
│   │   ├── Repositories/             # Data-access layer
│   │   └── Services/                 # GradeCalculator, GpaCalculator, EnrollmentService, TranscriptService, GradeService
│   ├── config/
│   ├── database/
│   │   ├── factories/                # UserFactory, StudentFactory, ...
│   │   ├── migrations/               # 7 migrations (users, permissions, academic, students/teachers, courses, enrollments/grades, transcripts/notifications)
│   │   └── seeders/                  # DatabaseSeeder — full dataset (300 students, 40 teachers, 80 courses, 500 enrollments, 3000 grades)
│   ├── resources/
│   │   ├── lang/{en,fa}/             # i18n
│   │   └── views/pdf/                # Transcript blade template
│   ├── routes/api.php                # /api/v1/* — 40+ endpoints
│   └── tests/                        # Pest: Feature + Unit
├── frontend/                         # React 18 + TS 5 + Vite 5
│   └── src/
│       ├── components/{layout,ui,charts}/
│       ├── i18n/                     # react-i18next + RTL
│       ├── lib/                      # Axios client
│       ├── pages/                    # Dashboard, Students, Teachers, Courses, Enrollments, Grades, Transcripts, Reports
│       └── stores/                   # Zustand auth store
├── docker/                           # PHP-FPM + Nginx configs
├── docker-compose.yml                # app + nginx + mysql + redis + mailpit + horizon + frontend
├── docs/
│   ├── ARCHITECTURE.md
│   ├── ERD.md
│   └── postman_collection.json
├── .github/workflows/ci.yml          # GitHub Actions CI
├── Makefile
├── LICENSE
├── CONTRIBUTING.md
├── CHANGELOG.md
└── README.md
```

---

## 🚀 Quick Start (Docker — recommended)

Requirements: **Docker**, **Docker Compose**, **~2 GB free RAM**.

```bash
git clone https://github.com/ImMrShervin/Student-result-management.git srms && cd srms

# 1. Backend env
cp backend/.env.example backend/.env

# 2. Start the whole stack
docker compose up -d --build

# 3. Install dependencies and prepare DB (first time only)
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan storage:link

# 4. Open the app
#    Frontend →  http://localhost:5173
#    Backend  →  http://localhost:8000
#    API docs →  http://localhost:8000/api/documentation
#    Mailpit  →  http://localhost:8025
```

**Demo accounts** (all with password `password`):

| Role | Email |
|---|---|
| Super Admin | `admin@srms.local` |
| Admin | `staff@srms.local` |
| Teacher | `teacher@srms.local` |
| Student | `student@srms.local` |

---

## 💻 Local development (without Docker)

```bash
# --- Backend ---
cd backend
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve                 # http://localhost:8000
php artisan queue:work &          # or `php artisan horizon`

# --- Frontend ---
cd ../frontend
npm install
npm run dev                       # http://localhost:5173
```

---

## 🧪 Testing

```bash
docker compose exec app ./vendor/bin/pest
# or locally:
cd backend && ./vendor/bin/pest --parallel
```

Covered:
- ✅ `GradeCalculator` — weighted score, letter mapping, GPA points
- ✅ `EnrollmentService` — happy path, duplicates, capacity full
- ✅ Auth — register, login (success + failure)

Add more tests using the factories in `database/factories/`.

---

## 📡 API

- **Base URL**: `http://localhost:8000/api/v1`
- **Authentication**: `Authorization: Bearer <sanctum_token>`
- **Interactive Swagger**: `/api/documentation` (auto-generated from `@OA\*` annotations)
- **Postman collection**: `docs/postman_collection.json`

### Key endpoints

| Method | Endpoint | Purpose |
|---|---|---|
| POST | `/auth/login` | Login, get token |
| GET | `/auth/me` | Current user + roles + permissions |
| GET | `/dashboard` | Role-aware dashboard payload |
| GET/POST/PUT/DELETE | `/students` | Student CRUD |
| GET/POST/PUT/DELETE | `/teachers` | Teacher CRUD |
| GET/POST/PUT/DELETE | `/courses` | Course CRUD (with prerequisites) |
| GET/POST | `/enrollments` | List & create enrollment (validates capacity + prereqs + credit cap) |
| POST | `/enrollments/{id}/approve` | Approve enrollment |
| PUT | `/enrollments/{id}/grade` | Upsert grade (calculator computes total + letter + GPA) |
| POST | `/grades/{id}/publish` | Publish + snapshot GPA + notify student |
| POST | `/students/{id}/transcript` | Generate PDF transcript with QR |
| GET | `/transcripts/verify/{code}` | Public verification (no auth) |
| GET | `/reports/*` | Top students, grade dist, dept stats, enrollment trend, pass-vs-fail |

---

## 🗄️ Database

MySQL 8 with 20+ tables. See **[docs/ERD.md](docs/ERD.md)** for the full diagram.

Highlights:
- **`enrollments`** has a unique `(student_id, course_section_id)` index
- **`grades`** has 1:1 relationship with enrollments
- **`semester_gpas`** are recomputed snapshots (idempotent via `updateOrCreate`)
- **`transcripts`** store the immutable JSON payload used at issue time — verifiable even if data changes later
- Soft deletes on students, teachers, faculties, departments, courses, sections, enrollments, grades

### Seed data
```bash
php artisan db:seed
# → 4 faculties, 15 departments, 40 teachers, 80 courses, 300 students,
#   500 enrollments, ~3000 published grades, 2 semesters, full GPA snapshots
```

---

## 🌍 Internationalization

- English (default)
- Persian / فارسی with automatic `dir="rtl"` and `Vazirmatn` font
- Toggle via language switcher in the sidebar or login screen

Add a new locale:
1. Create `frontend/src/i18n/locales/<lang>.json`
2. Add to `i18n/index.ts`
3. Create `backend/resources/lang/<lang>/messages.php`

---

## 🐳 Docker services

| Service | Port | Purpose |
|---|---|---|
| `nginx` | 8000 | Public web entrypoint |
| `app` | 9000 (internal) | PHP-FPM |
| `mysql` | 3307 | Database |
| `redis` | 6380 | Cache + queue + session |
| `mailpit` | 1025 SMTP / 8025 UI | Email capture |
| `horizon` | — | Queue worker + dashboard |
| `frontend` | 5173 | Vite dev server |

---

## 📅 Scheduler & Queues

Registered in `routes/console.php`:
- `srms:recompute-gpa` — daily 02:00 UTC
- `activitylog:clean` — weekly
- Custom backup, log rotation

Queue driver: **Redis** via **Laravel Horizon**. Grade publication and notifications are queued.

---

## 🔐 Security

- CSRF via Sanctum stateful middleware
- Rate limiting (60/min default via `throttleApi`)
- Bcrypt password hashing
- Policy checks on every controller action
- FormRequest validation everywhere
- Prepared statements via Eloquent
- Secure file upload (image validation, `storage/app/public`)
- Signed URLs for transcript downloads (optional)
- HTTPS-ready (add TLS at Nginx layer)

---

## 🗺️ Roadmap

- [ ] Real-time notifications via Laravel Reverb
- [ ] Attendance QR scanning
- [ ] Scholarship & graduation eligibility calculator
- [ ] Calendar view (exam dates, deadlines)
- [ ] Multi-tenancy for multi-university deployment
- [ ] Prometheus metrics + Grafana dashboards
- [ ] Native mobile app (React Native, sharing types)

---

## 🤝 Contributing

See **[CONTRIBUTING.md](CONTRIBUTING.md)**.

## 📜 License

MIT © 2026 — See **[LICENSE](LICENSE)**.

---

<p align="center">
  Made with ❤️
</p>
