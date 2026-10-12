# The Next Highlandah (TNH Season 3) - William Lawson's Microsite & Points Platform

A full-featured promotional campaign web platform built for **William Lawson's "The Next Highlandah" (TNH Season 3)** campaign in Nigeria (`TNHNG.COM`).

---

## 🌟 Key Features & Capabilities

### 1. Consumer Public Microsite (`index.php`)
- **18+ Age Gate & DOB Verification**: Mandatory verification ensuring participants are of legal drinking age before entering.
- **"Join The Clan" (1PD Data Collection)**: Fast, frictionless registration capturing Full Name, Phone/WhatsApp, Email, DOB, Preferred Outlet, and Instagram.
- **Search by Clan ID or Email**: Look up any participant by their Clan ID (e.g., `TNH-7701`) or registered Email Address.
- **Custom Branded Alert Engine (`js/highlandah-alert.js`)**: 100% custom-built modal alert dialog system carrying William Lawson's brand identity. Zero external dependencies, no browser alerts, no SweetAlert. Supports celebratory finale popups, warnings, and errors.
- **Official Challenges Scorecard**: Pre-configured point values across categories: *Good Humored*, *Unconventional*, and *Bold*.
- **Live Leaderboard**: Real-time national standings highlighting Top 3 podium ranks, total points, outlets visited, and the glowing **Grand Finale Qualified** badge (👑) for scores $\ge 100$. Searchable by Clan ID, Name, and Email.
- **"Check My Clan Points & History"**: Participants enter their Clan ID or Email address at any time to inspect their score, road to 100 points, and an audit trail of every challenge completed at every outlet.
- **"Come Claim Your Throne" Event Calendar**: List of activation venues and schedules across Lagos, Abuja, and Port Harcourt.
- **VIP Creator Table Spotlight (Slide 5)**: Hype mechanism for the 5 lucky consumers who qualify to share a table with the celebrity creator at the Grand Finale.

### 2. On-Site Brand Ambassador Portal (`promoter.php`)
- **Multi-Outlet Universal Lookup**: Promoters look up participants by **Clan ID**, **Email**, or Phone.
- **On-Site Fast Registration**: For walk-ins who haven't registered online, ambassadors can enroll them on the spot. Automatically records `registered_by_promoter_id` and the outlet.
- **Accurate & Tamper-Proof Points Logger**: Promoters select the completed challenge from the dropdown. The point value is **strictly locked and non-editable**, preventing manual tampering.
- **Physical On-Site Gameplay**: Games are exclusively played in person at activation outlets (not online in the browser).
- **Audit Tracking**: Automatically records:
  - Exact **Outlet** where the game was played and points were allocated.
  - Exact **Promoter (Staff ID & Name)** who awarded the points.
  - Timestamp and optional referee notes.
  - Automatically triggers qualification when participant reaches $\ge 100$ points!
- **Shift Venue Switching**: Ambassadors can switch their active outlet location seamlessly.

### 3. Company Management & Supervisor Console (`admin.php`)
- **Create Brand Ambassadors**: Company admins can create staff accounts with unique Staff Codes (e.g. `PRM-101`), email, phone, and assign them to outlets.
- **Staff Performance Tracking**: Real-time statistics showing:
  - How many participants each promoter registered.
  - How many points each promoter has allocated across shifts.
  - Active/Inactive account toggle.
- **Complete Points Allocation Audit Log**: Real-time stream of all points transactions showing Date, Participant, Points, Challenge, **Outlet where allocated**, and **Promoter who allocated**.
- **1-Click Google Sheets / CSV Export (Slide 2 CEP Platform)**:
  - Download complete Points Log CSV.
  - Download complete 1PD Participants directory for SMS, WhatsApp, and email retargeting.
- **Outlet & Event Calendar Manager**: Add new activation venues and schedule dates directly from the console.

---

## 🗄️ Database Architecture (`microsite`)

The application automatically connects to MySQL database `microsite` and initializes all tables and seed data upon first access:

1. `outlets`: Stores venues, cities, addresses, and activation dates.
2. `promoters`: Stores staff credentials, roles (`promoter`, `supervisor`, `admin`), staff codes, and assigned outlets.
3. `participants`: First-Party Data collection (`clan_id`, `full_name`, `phone`, `email`, `dob`, `total_points`, `is_qualified`, `registered_by_promoter_id`, `outlet_id`).
4. `challenges`: Master catalog of Good Humored, Unconventional, and Bold challenges with official scorecard points.
5. `points_log`: Immutable audit log storing every point allocation, including `participant_id`, `promoter_id`, `outlet_id`, and `outlet_name`.

---

## 🔑 Default Credentials

### Brand Ambassadors (`/promoter.php`)
| Staff Code | Name | Assigned Outlet | Password |
|---|---|---|---|
| `PRM-101` | Emeka Nnamdi | Highland Arena - Victoria Island | `promoter123` |
| `PRM-102` | Folake Adebayo | The Highlander Lounge - Ikeja | `promoter123` |
| `PRM-103` | Chinedu Okeke | The Clan Vault - Lekki Phase 1 | `promoter123` |
| `PRM-104` | Fatima Bello | Braveheart Sports Bar - Abuja | `promoter123` |

### Company Supervisor / Admin (`/admin.php`)
- **Email / Staff Code**: `admin@tnhng.com` or `ADM-001`
- **Password**: `admin123`

---

## 🚀 How to Run in XAMPP

1. Ensure **Apache** and **MySQL** are running in your XAMPP Control Panel.
2. The project is located in `c:\xampp\htdocs\microsite`.
3. Open your browser and navigate to:
   - **Public Microsite**: `http://localhost/microsite/`
   - **Ambassador Console**: `http://localhost/microsite/promoter.php`
   - **Admin Command Center**: `http://localhost/microsite/admin.php`
4. All database tables and initial seed data are created automatically on first page load!
