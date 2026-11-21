(() => {
  const API_URL = 'api/notes.php';

  let notes = [];
  let activeNoteId = null;
  let saveTimeout = null;
  let searchTimeout = null;

  // DOM Elements
  const sidebar = document.getElementById('sidebar');
  const backdrop = document.getElementById('sidebarBackdrop');
  const mobileMenuBtn = document.getElementById('mobileMenuBtn');
  const newNoteBtn = document.getElementById('newNoteBtn');
  const searchInput = document.getElementById('searchInput');
  const notesList = document.getElementById('notesList');

  const saveStatus = document.getElementById('saveStatus');
  const noteActions = document.getElementById('noteActions');
  const pinNoteBtn = document.getElementById('pinNoteBtn');
  const deleteNoteBtn = document.getElementById('deleteNoteBtn');

  const editorContainer = document.getElementById('editorContainer');
  const emptyState = document.getElementById('emptyState');
  const noteTitle = document.getElementById('noteTitle');
  const noteContent = document.getElementById('noteContent');

  // Escape HTML to prevent XSS
  const escapeHtml = (str) => {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
  };

  const formatDate = (dateStr) => {
    const date = new Date(dateStr.replace(' ', 'T'));
    return date.toLocaleDateString(undefined, { month: 'short', day: 'numeric' });
  };

  // Mobile Drawer Controls
  const toggleSidebar = (open) => {
    sidebar.classList.toggle('open', open);
    backdrop.classList.toggle('hidden', !open);
  };

  mobileMenuBtn.addEventListener('click', () => toggleSidebar(true));
  backdrop.addEventListener('click', () => toggleSidebar(false));

  // Fetch Notes
  async function loadNotes(query = '') {
    try {
      const url = query ? `${API_URL}?q=${encodeURIComponent(query)}` : API_URL;
      const res = await fetch(url);
      notes = await res.json();
      renderNotesList();

      if (notes.length > 0 && !notes.some(n => n.id === activeNoteId)) {
        selectNote(notes[0].id);
      } else if (notes.length === 0) {
        activeNoteId = null;
        showEmptyState();
      }
    } catch (err) {
      saveStatus.textContent = 'Offline';
    }
  }

  // Render Sidebar List
  function renderNotesList() {
    if (notes.length === 0) {
      notesList.innerHTML = `<p style="padding: 16px; font-size: 0.85rem; color: var(--text-muted); text-align: center;">No notes found.</p>`;
      return;
    }

    notesList.innerHTML = notes.map(note => {
      const isActive = note.id === activeNoteId ? 'active' : '';
      const preview = note.content ? escapeHtml(note.content.slice(0, 60)) : 'No additional text';
      const pinIcon = Number(note.is_pinned) === 1 ? '<span class="pin-badge">Pinned</span>' : '';

      return `
        <div class="note-item ${isActive}" data-id="${note.id}" role="listitem">
          <div class="note-item-header">
            <span class="note-item-title">${escapeHtml(note.title)}</span>
            ${pinIcon}
          </div>
          <div class="note-item-preview">${preview}</div>
          <div class="note-item-date">${formatDate(note.updated_at)}</div>
        </div>
      `;
    }).join('');
  }

  // Select Active Note
  function selectNote(id) {
    const note = notes.find(n => Number(n.id) === Number(id));
    if (!note) return;

    activeNoteId = Number(note.id);
    noteTitle.value = note.title;
    noteContent.value = note.content || '';

    const isPinned = Number(note.is_pinned) === 1;
    pinNoteBtn.textContent = isPinned ? 'Pinned' : 'Pin';
    pinNoteBtn.classList.toggle('pinned', isPinned);

    emptyState.classList.add('hidden');
    editorContainer.classList.remove('hidden');
    noteActions.classList.remove('hidden');
    saveStatus.textContent = 'Saved';

    renderNotesList();
    toggleSidebar(false);
  }

  function showEmptyState() {
    editorContainer.classList.add('hidden');
    noteActions.classList.add('hidden');
    emptyState.classList.remove('hidden');
    saveStatus.textContent = 'Ready';
  }

  // Create Note
  async function createNote() {
    saveStatus.textContent = 'Brewing...';
    const res = await fetch(API_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ title: 'Untitled Note', content: '' })
    });
    const newNote = await res.json();
    notes.unshift(newNote);
    selectNote(newNote.id);
    noteTitle.focus();
    noteTitle.select();
  }

  // Debounced Auto-Save
  function queueSave() {
    if (!activeNoteId) return;
    saveStatus.textContent = 'Brewing...';

    clearTimeout(saveTimeout);
    saveTimeout = setTimeout(async () => {
      const payload = {
        id: activeNoteId,
        title: noteTitle.value,
        content: noteContent.value
      };

      const res = await fetch(API_URL, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const updated = await res.json();

      const idx = notes.findIndex(n => Number(n.id) === activeNoteId);
      if (idx !== -1) {
        notes[idx] = updated;
        renderNotesList();
      }
      saveStatus.textContent = 'Saved ✓';
    }, 600);
  }

  // Toggle Pin
  async function togglePin() {
    if (!activeNoteId) return;
    const note = notes.find(n => Number(n.id) === activeNoteId);
    const nextPinState = Number(note.is_pinned) === 1 ? 0 : 1;

    await fetch(API_URL, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: activeNoteId, is_pinned: nextPinState })
    });

    await loadNotes(searchInput.value.trim());
    selectNote(activeNoteId);
  }

  // Delete Note
  async function deleteNote() {
    if (!activeNoteId || !confirm('Delete this note permanently?')) return;

    await fetch(`${API_URL}?id=${activeNoteId}`, { method: 'DELETE' });
    notes = notes.filter(n => Number(n.id) !== activeNoteId);
    activeNoteId = null;

    if (notes.length > 0) {
      selectNote(notes[0].id);
    } else {
      renderNotesList();
      showEmptyState();
    }
  }

  // Event Listeners
  newNoteBtn.addEventListener('click', createNote);
  noteTitle.addEventListener('input', queueSave);
  noteContent.addEventListener('input', queueSave);
  pinNoteBtn.addEventListener('click', togglePin);
  deleteNoteBtn.addEventListener('click', deleteNote);

  notesList.addEventListener('click', (e) => {
    const item = e.target.closest('.note-item');
    if (item) selectNote(item.dataset.id);
  });

  searchInput.addEventListener('input', (e) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
      loadNotes(e.target.value.trim());
    }, 250);
  });

  // Boot
  loadNotes();
})();