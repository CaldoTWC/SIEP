# Company Management Hub - Issue #4

## Overview
This implementation adds a comprehensive company management hub for UPIS administrators, featuring three integrated sections accessible through a modern tab-based interface.

## Implementation Details

### Database Changes
A new table `company_rejections_history` was created to store rejection records:
- **Independent Design**: Not linked to `users` table via foreign keys
- **Purpose**: Allows companies to re-register after rejection
- **Fields**: company_name, contact_email, contact_name, rejection_reason, rfc, commercial_name, rejection_date
- **Indexes**: Optimized for email and date queries

### New Components

#### 1. CompanyRejection Model
Location: `src/Models/CompanyRejection.php`

Methods:
- `create($data)` - Save new rejection record
- `getAll()` - Get all rejections ordered by date
- `getPaginated($limit, $offset)` - Paginated results
- `getByEmail($email)` - Search by email
- `getById($id)` - Get single record
- `getTotal()` - Count total rejections

#### 2. Company Management Hub View
Location: `src/Views/upis/company_management_hub.php`

Features three tabs:
1. **Pendientes**: Link to existing `review_companies.php` module
2. **Empresas Activas**: Table with ID, razón social, nombre comercial, contacto, email, teléfono, fecha de registro
3. **Historial de Rechazos**: Table with nombre empresa, email contacto, fecha intento, motivo rechazo

Design:
- Responsive tab interface with smooth transitions
- Consistent with existing project styling
- Empty state messages for each section
- Hover effects on rejection reasons for full text

### Modified Components

#### 1. User Model
Location: `src/Models/User.php`

Added methods:
- `getActiveCompanies()` - Returns list of active companies with basic info
- `findById($user_id)` - Find user by ID with full details

#### 2. UpisController
Location: `src/Controllers/UpisController.php`

**New Method:**
- `companyManagementHub()` - Loads data for all three tabs and renders view

**Modified Method:**
- `rejectCompany()` - Enhanced to:
  1. Retrieve company data BEFORE status change
  2. Save to `company_rejections_history` table
  3. Change user status to 'inactive'
  4. Send email notification with mandatory reason
  5. Redirect with success/error messages

#### 3. UPIS Dashboard
Location: `src/Views/upis/dashboard_hub.php`

Changed:
- Replaced "Empresas" card with "🏢 Gestión de Empresas" hub card
- Links to new `companyManagementHub` action
- Shows pending companies count

#### 4. Routing
Location: `public/index.php`

Added route:
```php
case 'companyManagementHub':
    $upisController->companyManagementHub();
    break;
```

## Key Features

### Rejection Workflow
1. UPIS admin reviews company in review_companies.php
2. Admin enters mandatory rejection reason
3. System saves data to history BEFORE changing status
4. Email sent to company with rejection reason
5. Company status changed to 'inactive'
6. Rejection appears in "Historial de Rechazos" tab
7. Company can re-register with same email (no blocking)

### Security
- All routes protected with `Session::guard(['upis', 'admin'])`
- Input sanitization with `htmlspecialchars()` and `trim()`
- Prepared statements for SQL queries (PDO)
- No SQL injection vulnerabilities
- No XSS vulnerabilities

### Email Integration
- Uses existing `EmailService::notifyCompanyStatus()` method
- Mandatory rejection reason included in email
- Sent to company's registered email address
- No internal notification system (email only)

## Installation

### 1. Database Migration
Run the SQL script to create the new table:

```bash
mysql -u username -p database_name < updates/add_company_rejections_table.sql
```

Or manually execute the SQL in your database management tool.

### 2. Verify Setup
1. Log in as UPIS or admin user
2. Navigate to UPIS Dashboard
3. Click on "🏢 Gestión de Empresas" card
4. Verify three tabs are visible
5. Test each tab's functionality

## Usage Guide

### For UPIS Administrators

#### Accessing the Hub
1. Log in with UPIS or admin credentials
2. From main dashboard, click "🏢 Gestión de Empresas"
3. Hub opens with three tabs

#### Tab 1: Pendientes
- Shows count of pending companies
- Click "Ir a Revisión Completa" button
- Redirects to full review interface
- Approve or reject companies with reasons

#### Tab 2: Empresas Activas
- View all approved companies
- See basic information at a glance
- Click email/phone links to contact
- No actions available (companies already approved)

#### Tab 3: Historial de Rechazos
- View all rejection records
- See reason for each rejection
- Hover over reason for full text
- Companies can re-register after rejection

## Technical Notes

### Design Decisions

1. **Separate History Table**: The rejection history table is intentionally NOT linked to users table, allowing rejected companies to re-register without conflicts.

2. **No "Who Rejected" Field**: As per requirements, we don't store which UPIS admin rejected the company - only the date, reason, and company information.

3. **History Before Status Change**: The rejection is saved to history BEFORE changing the user status, ensuring data is captured even if the status update fails.

4. **Link vs Include**: Tab 1 (Pendientes) links to the existing review page rather than including it, avoiding HTML nesting issues and maintaining code cleanliness.

5. **Minimal Changes**: Modified only necessary files and followed existing code patterns to maintain consistency.

### Code Quality
- ✅ All PHP syntax validated
- ✅ Consistent with project coding standards
- ✅ Uses existing CSS variables and styling
- ✅ Follows MVC pattern
- ✅ No code duplication
- ✅ Proper error handling
- ✅ Secure against common vulnerabilities

## Future Enhancements

Potential improvements (not currently implemented):
- Pagination for rejection history table
- Search/filter functionality for active companies
- Export functionality (CSV/PDF) for reports
- Bulk actions for active companies
- Email template customization interface
- Statistics dashboard for company metrics

## Files Changed

### New Files
- `src/Models/CompanyRejection.php`
- `src/Views/upis/company_management_hub.php`
- `updates/add_company_rejections_table.sql`

### Modified Files
- `src/Controllers/UpisController.php`
- `src/Models/User.php`
- `src/Views/upis/dashboard_hub.php`
- `public/index.php`

## Version History
- v1.0.0 (2025-11-08): Initial implementation
  - Company management hub with 3 tabs
  - Rejection history tracking
  - Active companies listing
  - Email notifications on rejection
  - Allow re-registration after rejection
