/* app.js
   Simple frontend SPA logic + demo data
*/

document.addEventListener('DOMContentLoaded', () => {
  // =============== Charts (Enrollment & Composition) ===============
  function renderEnrollmentChart() {
    const canvas = document.getElementById('enrollmentChart');
    if (!canvas || typeof Chart === 'undefined') return;
    const ctx = canvas.getContext('2d');
    const labels = ['2019', '2020', '2021', '2022', '2023', '2024', '2025'];
    const data = [850, 780, 920, 1020, 1100, 1205, 1280];

    new Chart(ctx, {
      type: 'line',
      data: {
        labels,
        datasets: [{
          label: 'Students',
          data,
          fill: true,
          tension: 0.35,
          borderColor: '#3b82f6',
          backgroundColor: 'rgba(59,130,246,0.12)',
          pointBackgroundColor: '#3b82f6',
          pointBorderColor: '#ffffff',
          pointRadius: 3,
          borderWidth: 2
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: { enabled: true }
        },
        scales: {
          x: {
            grid: { display: false },
            ticks: { color: '#6b7280' }
          },
          y: {
            beginAtZero: true,
            grid: { color: 'rgba(0,0,0,0.06)' },
            ticks: { color: '#6b7280' }
          }
        }
      }
    });
  }

  function renderCompositionChart() {
    const canvas = document.getElementById('compositionChart');
    if (!canvas || typeof Chart === 'undefined') return;
    const ctx = canvas.getContext('2d');

    const labels = ['Graduate', 'Undergraduate', 'Faculty'];
    const data = [35, 55, 10];
    const colors = ['#8b5cf6', '#3b82f6', '#10b981'];

    new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels,
        datasets: [{
          data,
          backgroundColor: colors,
          borderWidth: 0
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '60%',
        plugins: {
          legend: { display: false }, // using custom legend in HTML
          tooltip: { enabled: true }
        }
      }
    });
  }

  renderEnrollmentChart();
  renderCompositionChart();
  // ---------- Topic Tracker ----------
  const topicList = document.getElementById('topicList');
  const topicTrackerRow = document.querySelector('#page-upcoming .card:nth-of-type(2) .row');
  // Create form elements
  if (topicTrackerRow) {
    topicTrackerRow.innerHTML = `
      <select id="topicClass" class="filters">
        <option value="CSE-1">CSE-1</option>
        <option value="CSE-2">CSE-2</option>
      </select>
      <select id="topicSection" class="filters">
        <option value="A">A</option>
        <option value="B">B</option>
      </select>
      <select id="topicSubject" class="filters">
        <option value="Maths">Maths</option>
        <option value="English">English</option>
        <option value="CS">Computer Science</option>
        <option value="Data Structures">Data Structures</option>
      </select>
      <input type="text" id="topicInput" placeholder="Topic taught..." style="flex:1;min-width:180px;" />
      <button id="postTopicBtn" class="btn primary">Post</button>
    `;
  }

  // Store posted topics
  let postedTopics = [];

  function renderPostedTopics() {
    if (!topicList) return;
    topicList.innerHTML = '';
    // Get current selector values
    const cls = document.getElementById('topicClass')?.value;
    const section = document.getElementById('topicSection')?.value;
    const subject = document.getElementById('topicSubject')?.value;
    postedTopics.slice().reverse().forEach(t => {
      if (t.class === cls && t.section === section && t.subject === subject) {
        const div = document.createElement('div');
        div.className = 'topic-item';
        div.innerHTML = `<b>${t.topic}</b> <span style="color:var(--muted);font-size:13px;">(${t.class} ${t.section}, ${t.subject})</span><br><span style="font-size:12px;color:var(--muted);">${t.date}</span>`;
        topicList.appendChild(div);
      }
    });
  }

  // Handle post button
  const postBtn = document.getElementById('postTopicBtn');
  if (postBtn) {
    postBtn.addEventListener('click', () => {
      const cls = document.getElementById('topicClass').value;
      const section = document.getElementById('topicSection').value;
      const subject = document.getElementById('topicSubject').value;
      const topic = document.getElementById('topicInput').value.trim();
      if (!topic) return;
      postedTopics.push({
        class: cls,
        section: section,
        subject: subject,
        topic: topic,
        date: new Date().toLocaleString()
      });
      document.getElementById('topicInput').value = '';
      renderPostedTopics();
    });
    // Add change listeners to selectors to filter stack
    document.getElementById('topicClass').addEventListener('change', renderPostedTopics);
    document.getElementById('topicSection').addEventListener('change', renderPostedTopics);
    document.getElementById('topicSubject').addEventListener('change', renderPostedTopics);
  }
  renderPostedTopics();
  // --------- navigation ----------
  const navBtns = document.querySelectorAll('.nav-btn');
  const pages = document.querySelectorAll('.page');

  navBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
      const pageId = e.currentTarget.dataset.page;
      if (!pageId) return;
      navBtns.forEach(b => b.classList.remove('active'));
      e.currentTarget.classList.add('active');
      pages.forEach(p => p.classList.remove('active'));
      const target = document.getElementById('page-' + pageId);
      if (target) target.classList.add('active');
    });
  });

  // ---------- sample data ----------
  const students = [
    {id: 1, name: "Amit Kumar", avatar: "/Images/Photo1.jpeg"},
    {id: 2, name: "Neha Verma", avatar: "/Images/Photo2.jpeg"},
    {id: 3, name: "Zoya Khan", avatar: "/Images/Photo3.jpeg"},
    {id: 4, name: "Priya Singh", avatar: "/Images/Photo4.jpeg"},
    {id: 5, name: "Rohan Patel", avatar: "/Images/Photo6.jpeg"},
  ];

  // generate last 5 dates
  function lastNDates(n) {
    const arr = [];
    for(let i = n-1; i >= 0; i--) {
      const d = new Date();
      d.setDate(d.getDate()-i);
      arr.push(d.toISOString().slice(0,10));
    }
    return arr;
  }
  const last5 = lastNDates(5);

  // attendance map: studentId -> array of status for last5
  const attendanceStatuses = ['P','A','M','L']; // P present, A absent, M medical, L leave
  const attendance = {};
  students.forEach(s => {
    attendance[s.id] = last5.map(() => attendanceStatuses[Math.floor(Math.random() * attendanceStatuses.length)]);
  });

  // ---------- Home: students grid & attendance log ----------
  const studentsGrid = document.getElementById('studentsGrid');
  
  function renderStudents() {
    studentsGrid.innerHTML = '';
    students.forEach(s => {
      const last = attendance[s.id][attendance[s.id].length-1];
      let cls = 'status-present', label = 'Present';
      if(last === 'A') { cls = 'status-absent'; label = 'Absent'; }
      if(last === 'M') { cls = 'status-med'; label = 'Medical'; }
      if(last === 'L') { cls = 'status-leave'; label = 'Leave'; }
      
      const div = document.createElement('div');
      div.className = 'student';
      div.innerHTML = `
        <img src="${s.avatar}" alt="${s.name}" />
        <div class="name">${s.name}</div>
        <div style="display:flex;gap:8px;align-items:center;">
          <div class="status-dot ${cls}" title="${label}"></div>
          <div style="font-size:12px;color:var(--muted)">${label}</div>
        </div>
      `;
      studentsGrid.appendChild(div);
    });
  }

  // ---------- Attendance Table ----------
  const attendanceLogTable = document.getElementById('attendanceLogTable');
  
  function updateAttendanceStatus(studentId, date, newStatus) {
    if (attendance[studentId]) {
      const dateIndex = last5.indexOf(date);
      if (dateIndex !== -1) {
        attendance[studentId][dateIndex] = newStatus;
        renderAttendanceLog();
        renderStudents();
      }
    }
  }

  function showAttendanceOptions(event, studentId, date) {
    const cell = event.target;
    const currentStatus = cell.textContent.trim();
    
    // Create dropdown
    const dropdown = document.createElement('select');
    dropdown.className = 'attendance-dropdown';
    const options = ['P', 'A', 'M', 'L'];
    
    options.forEach(option => {
      const opt = document.createElement('option');
      opt.value = option;
      opt.textContent = option;
      if (option === currentStatus) opt.selected = true;
      dropdown.appendChild(opt);
    });

    // Handle selection
    dropdown.addEventListener('change', (e) => {
      updateAttendanceStatus(studentId, date, e.target.value);
    });

    // Handle blur
    dropdown.addEventListener('blur', () => {
      if (!attendance[studentId]) return;
      const dateIndex = last5.indexOf(date);
      cell.textContent = attendance[studentId][dateIndex];
      dropdown.remove();
    });

    // Replace text with dropdown
    cell.textContent = '';
    cell.appendChild(dropdown);
    dropdown.focus();
  }

  function renderAttendanceLog() {
    const tbl = document.createElement('table');
    tbl.className = 'table';
    
    // Create header
    const thead = document.createElement('thead');
    thead.innerHTML = `<tr><th>Student</th>${last5.map(d => `<th>${d}</th>`).join('')}</tr>`;
    tbl.appendChild(thead);
    
    // Create body
    const tbody = document.createElement('tbody');
    students.forEach(student => {
      const tr = document.createElement('tr');
      tr.innerHTML = `<td>${student.name}</td>`;
      
      const statuses = attendance[student.id] || Array(5).fill('-');
      statuses.forEach((status, i) => {
        const td = document.createElement('td');
        td.className = 'status-cell';
        td.textContent = status;
        
        // Set color based on status
        const color = status === 'P' ? 'var(--success)' :
                     status === 'A' ? 'var(--danger)' :
                     status === 'M' ? 'var(--warning)' :
                     status === 'L' ? 'var(--orange)' : 'inherit';
        td.style.color = color;
        td.style.fontWeight = '700';
        
        // Add data and click handler
        td.dataset.student = student.id;
        td.dataset.date = last5[i];
        td.addEventListener('click', (e) => showAttendanceOptions(e, student.id, last5[i]));
        
        tr.appendChild(td);
      });
      
      tbody.appendChild(tr);
    });
    
    tbl.appendChild(tbody);
    attendanceLogTable.innerHTML = '';
    attendanceLogTable.appendChild(tbl);
  }

  // ---------- Upcoming Lectures ----------
  const schedule = [
    {time:'09:00', cls:'CSE-1', subject:'Data Structures'},
    {time:'10:05', cls:'CSE-1', subject:'English'},
    {time:'11:00', cls:'CSE-2', subject:'Maths'},
  ];

  const scheduleTbody = document.getElementById('scheduleTbody');
  const todayDate = document.getElementById('todayDate');
  if (todayDate) {
    todayDate.textContent = new Date().toLocaleDateString();
  }

  function renderSchedule(){
    if (!scheduleTbody) return;
    scheduleTbody.innerHTML = '';
    schedule.forEach((s, i) => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${s.time}</td>
        <td>${s.cls}</td>
        <td>${s.subject}</td>
        <td>
          <input type="text" class="room-input" placeholder="Room No." style="width:80px;" />
          <button class="btn primary present-btn">Present</button>
        </td>
      `;
      scheduleTbody.appendChild(tr);
    });

    // Enable only the first row initially
    const rows = scheduleTbody.querySelectorAll('tr');
    rows.forEach((row, idx) => {
      const input = row.querySelector('.room-input');
      const btn = row.querySelector('.present-btn');
      if (idx === 0) {
        input.disabled = false;
        btn.disabled = false;
        input.addEventListener('input', () => {
          btn.disabled = input.value.trim() === '';
        });
      } else {
        input.disabled = true;
        btn.disabled = true;
        input.addEventListener('input', () => {
          btn.disabled = input.value.trim() === '';
        });
      }
      btn.addEventListener('click', () => {
        if (input.value.trim() === '') return;
        input.disabled = true;
        btn.disabled = true;
  btn.classList.add('btn-disabled');
  btn.style.background = '#e5e7eb';
  btn.style.color = '#9ca3af';
  btn.style.border = '1px solid #e5e7eb';
  btn.style.pointerEvents = 'none';
  btn.onmouseover = btn.onmouseout = null;
        // Enable next row
        if (rows[idx+1]) {
          const nextInput = rows[idx+1].querySelector('.room-input');
          const nextBtn = rows[idx+1].querySelector('.present-btn');
          nextInput.disabled = false;
          nextBtn.disabled = nextInput.value.trim() === '';
          nextInput.addEventListener('input', () => {
            nextBtn.disabled = nextInput.value.trim() === '';
          });
          nextInput.focus();
        }
      });
    });
  }
  renderSchedule();

  // Initial render
  renderStudents();
  renderAttendanceLog();
});