<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BrewNotes — Coffee Notepad</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Lora:wght@600&display=swap" rel="stylesheet">
  <!-- <link rel="stylesheet" href="assets/css/variables.css"> -->
  <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
  <div class="app-shell">
    <div id="sidebarBackdrop" class="sidebar-backdrop hidden"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar">
      <div class="sidebar-header">
        <div class="brand">
          <span class="brand-title">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/><line x1="6" y1="2" x2="6" y2="4"/><line x1="10" y1="2" x2="10" y2="4"/><line x1="14" y1="2" x2="14" y2="4"/></svg>
            BrewNotes
          </span>
        </div>
        <button id="newNoteBtn" class="btn-primary">+ New Note</button>
        <div class="search-box">
          <input type="search" id="searchInput" class="search-input" placeholder="Search notes..." aria-label="Search notes">
        </div>
      </div>

      <div id="notesList" class="notes-list" role="list"></div>
    </aside>

    <!-- Main Editor -->
    <main class="editor-pane">
      <header class="editor-toolbar">
        <div class="toolbar-left">
          <button id="mobileMenuBtn" class="btn-icon mobile-menu-btn" aria-label="Open notes menu">☰ Notes</button>
          <span id="saveStatus" class="status-text">Ready</span>
        </div>
        <div id="noteActions" class="toolbar-right hidden">
          <button id="pinNoteBtn" class="btn-icon" title="Pin note">Pin</button>
          <button id="deleteNoteBtn" class="btn-icon btn-danger" title="Delete note">Delete</button>
        </div>
      </header>

      <!-- Active Note Canvas -->
      <div id="editorContainer" class="editor-container hidden">
        <input type="text" id="noteTitle" class="editor-title" placeholder="Note title..." autocomplete="off">
        <textarea id="noteContent" class="editor-body" placeholder="Pour your thoughts here..."></textarea>
      </div>

      <!-- Empty State -->
      <div id="emptyState" class="empty-state">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/><line x1="6" y1="2" x2="6" y2="4"/><line x1="10" y1="2" x2="10" y2="4"/><line x1="14" y1="2" x2="14" y2="4"/></svg>
        <p>Select a note or brew a fresh one to begin.</p>
      </div>
    </main>
  </div>

  <script src="assets/js/app.js"></script>
</body>
</html>