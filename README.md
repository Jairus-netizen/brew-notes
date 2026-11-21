# BrewNotes — Aesthetic Coffee Notepad

A distraction-free, responsive notepad built with **PHP, Vanilla JS, CSS, and MySQL**, structured using the **Superpowers** engineering workflow and **UI/UX Pro Max** design tokens.

## Features
- **Warm Roast UI:** High-contrast Oat Milk (`#FAF6F0`) & Dark Espresso (`#2C1E16`) palette.
- **Auto-Save:** 600ms debounced saving with live status feedback.
- **Pin & Search:** Pin priority notes and filter instantly.
- **Responsive:** Split-pane desktop layout and slide-over mobile drawer.

## Setup
1. Import `schema.sql` into your MySQL server.
2. Copy `config/database.example.php` to `config/database.php` and add your MySQL credentials.
3. Start the local PHP server:
   ```bash
   php -S localhost:8000