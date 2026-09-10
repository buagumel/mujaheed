# VTU Express Nigeria - Flutter Mobile Application Documentation

## 1. Overview & Architecture

The VTU Express mobile application is a high-performance, native Flutter mobile client built for Android and iOS. It communicates directly with the existing Laravel backend over secure REST API endpoints.

```
       ┌─────────────────────────────────────────────────────────────┐
       │                   FLUTTER MOBILE CLIENT                     │
       │                                                             │
       │   • Clean Modular Architecture                              │
       │   • Riverpod Reactive State Management                      │
       │   • Dio HTTP Client with Sanctum Bearer Interceptors        │
       │   • Encrypted Secure Token & Preference Storage             │
       │   • Material Kit Inspired Fintech Design System             │
       └──────────────────────────────┬──────────────────────────────┘
                                      │
                                HTTPS REST API
                                      │
                                      ▼
       ┌─────────────────────────────────────────────────────────────┐
       │                  EXISTING LARAVEL BACKEND                   │
       │                                                             │
       │   • Single Source of Truth for Auth, Wallet & Transactions  │
       │   • Dynamic Config & Service Availability Management        │
       │   • VTU Providers (Airtime, Data, Electricity, Cable, Exams)│
       │   • Automated Bank Transfer & Online Funding Gateways       │
       │   • MySQL Database & Eloquent Services                      │
       └─────────────────────────────────────────────────────────────┘
```

---

## 2. Directory Structure

```
Mobile-App/
├── pubspec.yaml                 # Flutter dependencies & metadata
├── analysis_options.yaml        # Linting and style rules
├── android/                     # Native Android build & configurations
├── ios/                         # Native iOS build & configurations
└── lib/
    ├── core/
    │   ├── config/              # AppConfig & Environment variables
    │   ├── constants/           # AppColors, AppRadius, AppSpacing, Typography
    │   ├── network/             # ApiClient, ApiEndpoints, ApiException handler
    │   ├── storage/             # SecureStorageService (Encrypted storage)
    │   ├── theme/               # Centralized AppTheme (Material Kit styled)
    │   └── utils/               # CurrencyFormatter, DateFormatter, Validators
    ├── data/
    │   ├── models/              # Typed models (User, Wallet, Transactions, Plans)
    │   └── repositories/        # Repositories communicating with Laravel API
    ├── features/
    │   ├── splash/              # Startup initialization & maintenance screen
    │   ├── onboarding/          # 3-slide first-launch onboarding
    │   ├── auth/                # Register, Login, Forgot & Reset Password
    │   ├── dashboard/           # Bottom navigation shell
    │   ├── home/                # Balance card, Quick services grid, Recent txs
    │   ├── wallet/              # Balance details, Auto DVA accounts, Online funding
    │   ├── services/
    │   │   ├── airtime/         # Airtime purchase with live network discounts
    │   │   ├── data/            # Data bundles with dynamic plan fetching
    │   │   ├── electricity/     # Meter verification & token delivery
    │   │   ├── cable/           # Smartcard verification & bouquet selection
    │   │   └── exam_pins/       # WAEC/JAMB scratch card PIN purchases
    │   ├── transactions/        # Infinite history list, filters, detail & receipt
    │   ├── notifications/       # User & system announcements list
    │   └── profile/             # Profile details, password, PIN & Support tickets
    ├── shared/
    │   └── widgets/             # CustomButton, CustomTextField, PinDialog, etc.
    ├── routing/                 # AppRoutes registry
    └── main.dart                # Application entry point
```

---

## 3. Environment & API Configuration

API base URLs are centralized in [`lib/core/config/environment.dart`](file:///Users/user/Documents/Projectstation/VTU/Mobile-App/lib/core/config/environment.dart) and [`lib/core/config/app_config.dart`](file:///Users/user/Documents/Projectstation/VTU/Mobile-App/lib/core/config/app_config.dart).

### Base URLs by Target:
- **Local Development (macOS / iOS Simulator / Web)**: `http://127.0.0.1:8000/api`
- **Android Emulator**: `http://10.0.2.2:8000/api` (Android emulator maps `10.0.2.2` to host computer's `127.0.0.1`)
- **Physical Device**: `http://<YOUR_LOCAL_IP>:8000/api`
- **Production**: `https://vtuexpress.ng/api`

---

## 4. API Endpoints Mapping Table

| Feature Area | HTTP Method | Laravel API Endpoint | Authentication | Description |
|---|---|---|---|---|
| **Public Config** | `GET` | `/api/app/config` | Public | Dynamic app name, logo, colors, maintenance mode, service flags |
| **Registration** | `POST` | `/api/register` | Public | Register customer, creates wallet & returns Sanctum token |
| **Login** | `POST` | `/api/login` | Public | Authenticates credentials, returns user, wallet & token |
| **Forgot Password**| `POST`| `/api/forgot-password` | Public | Sends 6-digit email OTP for password reset |
| **Reset Password** | `POST` | `/api/reset-password` | Public | Verifies OTP and resets account password |
| **Logout** | `POST` | `/api/logout` | Sanctum Bearer | Revokes current Sanctum access token |
| **Current User** | `GET` | `/api/user` | Sanctum Bearer | Retrieves user profile, wallet balance, and PIN status |
| **Dashboard** | `GET` | `/api/dashboard` | Sanctum Bearer | Retrieves balance, stats, active networks, and recent transactions |
| **Wallet** | `GET` | `/api/wallet` | Sanctum Bearer | Returns authoritative wallet balance and dedicated virtual accounts |
| **Wallet History**| `GET` | `/api/wallet/transactions` | Sanctum Bearer | Wallet credit / debit ledger |
| **Fund Wallet** | `POST` | `/api/wallet/fund` | Sanctum Bearer | Initializes online gateway payment |
| **Airtime Networks**| `GET`| `/api/airtime/networks` | Sanctum Bearer | Active networks with discount percentages |
| **Airtime Purchase**| `POST`| `/api/airtime/purchase` | Sanctum Bearer | Purchase airtime with 4-digit PIN |
| **Data Networks** | `GET` | `/api/data/networks` | Sanctum Bearer | Active data networks |
| **Data Plans** | `GET` | `/api/data/plans?network={net}` | Sanctum Bearer | Live data packages (SME, Gifting, Corporate) |
| **Data Purchase** | `POST` | `/api/data/purchase` | Sanctum Bearer | Purchase data bundle with 4-digit PIN |
| **Electricity Providers**| `GET`| `/api/electricity/providers` | Sanctum Bearer | Active Discos (IKEDC, EKEDC, AEDC, IBEDC, etc.) |
| **Meter Verify** | `POST` | `/api/electricity/verify` | Sanctum Bearer | Verifies customer meter number before payment |
| **Electricity Pay**| `POST`| `/api/electricity/pay` | Sanctum Bearer | Pays electricity bill and returns token |
| **Cable Plans** | `GET` | `/api/cable/plans?provider={prov}` | Sanctum Bearer | Bouquets for DStv, GOtv, Startimes |
| **Smartcard Verify**| `POST`| `/api/cable/verify` | Sanctum Bearer | Verifies smartcard/IUC owner before renewal |
| **Cable Pay** | `POST` | `/api/cable/pay` | Sanctum Bearer | Subscribes/renews cable bouquet with PIN |
| **Exam PINs** | `GET` | `/api/exam-pins/packages` | Sanctum Bearer | Scratch card packages (WAEC, NECO, JAMB) |
| **Exam Purchase** | `POST` | `/api/exam-pins/purchase` | Sanctum Bearer | Purchase exam scratch card PINs with PIN |
| **Transactions** | `GET` | `/api/transactions` | Sanctum Bearer | Filterable, paginated transaction history |
| **Transaction Detail**| `GET`| `/api/transactions/{ref}` | Sanctum Bearer | Single transaction breakdown & receipt data |
| **Notifications** | `GET` | `/api/notifications` | Sanctum Bearer | User transaction alerts & platform notices |
| **Mark Read** | `POST` | `/api/notifications/mark-read` | Sanctum Bearer | Marks all notifications as read |
| **Update Profile** | `POST` | `/api/profile` | Sanctum Bearer | Updates customer name and phone |
| **Update Password**| `POST` | `/api/profile/password` | Sanctum Bearer | Changes login password |
| **Set PIN** | `POST` | `/api/profile/pin` | Sanctum Bearer | Sets 4-digit transaction PIN |
| **Change PIN** | `POST` | `/api/profile/pin/change` | Sanctum Bearer | Updates current 4-digit PIN |
| **Reset PIN OTP** | `POST` | `/api/profile/pin/send-otp` | Sanctum Bearer | Sends 6-digit OTP for PIN reset |
| **Reset PIN** | `POST` | `/api/profile/pin/reset-otp` | Sanctum Bearer | Resets 4-digit PIN with OTP code |
| **Support Tickets**| `GET`/`POST`| `/api/support/tickets` | Sanctum Bearer | List or create helpdesk support tickets |

---

## 5. Security & Financial Rules

1. **Zero Client Secrets**: No database passwords, VTU provider API credentials, or payment secret keys exist in the Flutter codebase.
2. **Encrypted Token Storage**: Tokens are stored via `FlutterSecureStorage` (EncryptedSharedPreferences on Android and Keychain on iOS).
3. **Server-Side Authoritative Balances**: The Flutter client never calculates or decides wallet balances, commission discounts, or transaction success status independently.
4. **Idempotent / Anti-Double Tap**: Payment action buttons disable during processing to prevent duplicate debit submissions.
5. **Session Expiry Handling**: On HTTP 401 response, `ApiClient` automatically clears secure credentials and navigates the user safely back to the login screen.

---

## 6. How to Run & Build

### Running the Laravel API Backend:
```bash
cd /path/to/VTU
php artisan serve --port=8000
```

### Running the Flutter Mobile App:
```bash
cd /path/to/VTU/Mobile-App
flutter pub get
flutter run
```

### Building Android APK / App Bundle:
```bash
# Debug APK
flutter build apk --debug

# Release APK
flutter build apk --release

# Production Play Store App Bundle (AAB)
flutter build appbundle --release
```
