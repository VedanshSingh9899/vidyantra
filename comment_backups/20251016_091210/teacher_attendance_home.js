// teacher_attendance_home.js
// Fetch attendance records for the home page grid from JSON and render.
// Schema: [{ id, name, email, phone, status }]

(async function initTeacherHomeAttendance() {
  const grid = document.getElementById('studentsGrid');
  if (!grid) return;

  function renderGrid(list) {
    grid.innerHTML = list.map((s, i) => `
      <div class="student-avatar ${s.status}">
        <img src="https://i.pravatar.cc/100?u=${encodeURIComponent(s.id)}" alt="${s.name}">
        <div title="${s.name} | ${s.email} | ${s.phone}">${(s.name || '').split(' ')[0] || s.id}</div>
      </div>
    `).join('');
  }

  function renderLog(list) {
    const logTbody = document.getElementById('attendanceLogTable');
    if (!logTbody) return;
    const present = list.filter(s => s.status === 'present').length;
    const absent = list.filter(s => s.status === 'absent').length;
    const total = list.length;
    const pct = total ? Math.round((present / total) * 100) : 0;
    const today = new Date().toISOString().slice(0,10);
    logTbody.innerHTML = `
      <tr>
        <td>${today}</td>
        <td>CSE-1</td>
        <td>A</td>
        <td style="color: #10b981;">${present}</td>
        <td style="color: #ef4444;">${absent}</td>
        <td><span style="color: #6366f1; font-weight: 600;">${pct}%</span></td>
      </tr>
    `;
  }

  async function loadFromJson() {
    try {
      const res = await fetch('data/attendance.json', { cache: 'no-cache' });
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const list = await res.json();
      if (!Array.isArray(list)) throw new Error('Invalid JSON format');
      renderGrid(list);
      renderLog(list);
    } catch (err) {
      console.error('Failed to load attendance JSON:', err);
      grid.innerHTML = `<div style="color: var(--text-secondary);">Could not load attendance data.</div>`;
    }
  }

  loadFromJson();
})();
