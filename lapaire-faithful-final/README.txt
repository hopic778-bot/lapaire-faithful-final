FAITHFUL VISUAL REBUILD
This package supersedes earlier ZIPs. It uses generated visual assets to closely reproduce the supplied 9-page design board while keeping the PHP/MySQL features.

LAPAIRE COMPLETE PHP/MYSQL SITE

Quick start with XAMPP:
1. Copy folder to C:/xampp/htdocs/lapaire-complete
2. Start Apache and MySQL.
3. Import database/lapaire.sql in phpMyAdmin.
4. Visit http://localhost/lapaire-complete/database/setup_admin.php ONCE, then delete setup_admin.php.
5. Public site: http://localhost/lapaire-complete/
6. Admin: http://localhost/lapaire-complete/admin/login.php
   Temporary login: admin / ChangeMe123!
7. Change credentials before any public deployment.

Features:
- Multi-page public site based on supplied design reference.
- Cinematic cross-fade + slow zoom hero.
- Database-driven products, branches, blog posts.
- Search/filter glasses.
- Appointment booking saved to MySQL.
- Contact/enquiry form saved to MySQL.
- Protected admin login using password_hash/password_verify.
- CRUD: products, branches, blog posts; appointment/enquiry status management and deletion.
- CSRF tokens, prepared statements, output escaping, session regeneration.
- Responsive Bootstrap 5 + custom CSS.

Before selling/deployment:
- Replace placeholder branch records with client-approved addresses/phone/email.
- Replace/approve all brand assets and legal/privacy/returns text.
- Change admin password and delete setup_admin.php.
- Configure HTTPS, production database credentials, backups, email/WhatsApp integrations and server security.
- Bootstrap currently loads from CDN; vendor locally if offline operation is required.
