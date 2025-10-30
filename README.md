# Traction Ideas - Feedback Wall

A community feedback wall where team members submit suggestions, upvote, and discuss priorities. Admins moderate and resolve suggestions.

## Tech Stack
- Frontend: HTML, Bootstrap 5, Vanilla JS
- Backend: PHP (no frameworks), MySQL
- Runtime: XAMPP/WAMP/MAMP

## Database
- Import `traction_ideas.sql` into MySQL.
- Database name: `traction_ideas`.

### Seeded Accounts
- Admin
  - Email: `admin@demo.local`
  - Password: `password`
- Demo User
  - Email: `user@demo.local`
  - Password: `password`

> Passwords are stored with bcrypt (`password_hash` / `password_verify`).

## Local Setup (XAMPP)
1. Copy repository into your web root (e.g., `htdocs/traction-ideas-feedback-wall`).
2. Start Apache and MySQL.
3. Create DB and import `traction_ideas.sql` using phpMyAdmin or CLI.
4. Adjust DB credentials in `config/config.php` if needed.
5. Visit `http://localhost/traction-ideas-feedback-wall/`.

## Features
- View, filter (category), sort (votes/date), search
- Upvote with live update via AJAX
- Users can create, edit, and delete their own suggestions
- Admin can resolve/unresolve and delete any suggestion
- Admin edits only admin-owned suggestions (no editing user content)
- Basic CSRF protection on write forms
- Responsive UI with Bootstrap, toasts for feedback

## Structure
- `config/config.php`: App config
- `includes/`: DB connection, auth, helpers, layout
- `api/`: AJAX endpoints (upvote, admin moderation)
- `admin/`: Admin dashboard and moderation pages

## Ownership & Access Rules
- Guests: view, filter/sort/search, upvote per session (fingerprint)
- Users: CRUD for own suggestions, upvote per suggestion per user
- Admin: create suggestions, edit only admin-owned content; resolve/unresolve/delete any suggestion
- Server-side checks enforce ownership and roles on every action

## Known Limitations
- No email verification
- Minimal pagination (can be added)
- Basic styles; can be further refined

## Security Notes
- Prepared statements for DB access
- Bcrypt hashing
- Output escaping to prevent XSS
- CSRF token on write forms
