/* app.js
   Simple frontend SPA logic + demo data
*/



document.addEventListener('DOMContentLoaded', () => {
  // Ensure portal is closed by default on page load
  if (localStorage.getItem('attendancePortalOpen') === null) {
    localStorage.setItem('attendancePortalOpen', 'false');
  }
  // Open Attendance Portal logic
  const openAttendancePortalBtn = document.getElementById('openAttendancePortalBtn');
  if (openAttendancePortalBtn) {
    openAttendancePortalBtn.addEventListener('click', () => {
      localStorage.setItem('attendancePortalOpen', 'true');
      localStorage.removeItem('studentAttendanceMarked'); // allow marking again
      alert('Attendance portal opened! Students can now mark attendance.');
    });
  }
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
  // ---------- Attendance page (table with filter) ----------
  const attendanceTableWrap = document.getElementById('attendanceTable');
  function renderAttendanceTable(className='CSE-1', section='A'){
    // Build a table with many dates across
    const dates = lastNDates(12);
    const tbl = document.createElement('table');
    tbl.className='table';
    const thead = document.createElement('thead');
    thead.innerHTML = `<tr><th>Student</th>${dates.map(d=>`<th>${d.slice(5)}</th>`).join('')}</tr>`;
    tbl.appendChild(thead);

    const tbody = document.createElement('tbody');
    students.forEach(s=>{
      const row = document.createElement('tr');
      row.innerHTML = `<td><strong>${s.name}</strong></td>` + dates.map((d,idx)=>{
        // random status for demo
        const st = Math.random() > 0.15 ? 'P' : (Math.random()>0.5 ? 'A' : 'L');
        const color = st==='P' ? 'var(--success)' : st==='A' ? 'var(--danger)' : 'var(--orange)';
        return `<td style="color:${color};font-weight:700">${st}</td>`;
      }).join('');
      tbody.appendChild(row);
    });
    tbl.appendChild(tbody);
    attendanceTableWrap.innerHTML='';
    attendanceTableWrap.appendChild(tbl);
  }
  renderAttendanceTable();

  document.getElementById('applyFilter').addEventListener('click',()=>{
    const cls = document.getElementById('filterClass').value;
    const sec = document.getElementById('filterSection').value;
    renderAttendanceTable(cls,sec);
  });

  // Export CSV (very simple)
  document.getElementById('exportCsv').addEventListener('click', ()=>{
    let csv = 'Student,' + lastNDates(12).join(',') + '\n';
    students.forEach(s=>{
      const row = [s.name];
      for(let i=0;i<12;i++){
        row.push(Math.random()>0.15 ? 'P' : (Math.random()>0.5 ? 'A' : 'L'));
      }
      csv += row.join(',') + '\n';
    });
    const blob = new Blob([csv], {type: 'text/csv'});
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url; a.download = 'attendance.csv';
    document.body.appendChild(a); a.click(); a.remove();
    URL.revokeObjectURL(url);
  });
  // Initial render
  renderStudents();
  renderAttendanceLog();

  // ================ Reference Notes ================
  const refPdfInput = document.getElementById('refPdfInput');
  const refNotesList = document.getElementById('refNotesList');
  const refUploadArea = document.getElementById('refUploadArea');

  // Persist list in-memory (and optionally localStorage)
  let refNotes = []; // { name, url(blob), createdAt }

  function renderRefNotes() {
    if (!refNotesList) return;
    refNotesList.innerHTML = '';
    if (refNotes.length === 0) {
      const li = document.createElement('li');
      li.textContent = 'No PDFs uploaded yet.';
      refNotesList.appendChild(li);
      return;
    }
    refNotes.forEach((n, idx) => {
      const li = document.createElement('li');
      li.style.display = 'flex';
      li.style.alignItems = 'center';
      li.style.justifyContent = 'space-between';
      li.style.gap = '8px';
      li.innerHTML = `
        <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1">${n.name}</span>
        <div style="display:flex;gap:6px">
          <button class="btn" data-open="${idx}">Open</button>
          <a class="btn" href="${n.url}" download>Download</a>
          <button class="btn" data-remove="${idx}">Remove</button>
        </div>
      `;
      refNotesList.appendChild(li);
    });
  }

  function handleRefFiles(files) {
    Array.from(files).forEach(file => {
      if (file.type !== 'application/pdf') return;
      const url = URL.createObjectURL(file);
      refNotes.push({ name: file.name, url, createdAt: Date.now() });
    });
    renderRefNotes();
  }

  if (refPdfInput) {
    refPdfInput.addEventListener('change', (e) => handleRefFiles(e.target.files));
  }
  if (refUploadArea) {
    refUploadArea.addEventListener('dragover', (e) => { e.preventDefault(); refUploadArea.classList.add('dragover'); });
    refUploadArea.addEventListener('dragleave', () => refUploadArea.classList.remove('dragover'));
    refUploadArea.addEventListener('drop', (e) => {
      e.preventDefault();
      refUploadArea.classList.remove('dragover');
      handleRefFiles(e.dataTransfer.files);
    });
  }

  if (refNotesList) {
    refNotesList.addEventListener('click', (e) => {
      const openIdx = e.target.getAttribute && e.target.getAttribute('data-open');
      const remIdx = e.target.getAttribute && e.target.getAttribute('data-remove');
      if (openIdx !== null && openIdx !== undefined) {
        const note = refNotes[Number(openIdx)];
        if (note) openPdfOverlay(note.url, note.name);
      }
      if (remIdx !== null && remIdx !== undefined) {
        const i = Number(remIdx);
        const item = refNotes[i];
        if (item) URL.revokeObjectURL(item.url);
        refNotes.splice(i,1);
        renderRefNotes();
      }
    });
  }

  renderRefNotes();

  // -------- PDF Overlay + Annotation --------
  const overlay = document.getElementById('pdfOverlay');
  const pdfCanvas = document.getElementById('pdfCanvas');
  const annoCanvas = document.getElementById('annoCanvas');
  const ctx = pdfCanvas ? pdfCanvas.getContext('2d') : null;
  const actx = annoCanvas ? annoCanvas.getContext('2d') : null;
  const pdfError = document.getElementById('pdfError');
  const pageInfo = document.getElementById('pdfPageInfo');
  const btnClose = document.getElementById('pdfCloseBtn');
  const btnPrev = document.getElementById('pdfPrevBtn');
  const btnNext = document.getElementById('pdfNextBtn');
  const btnZoomIn = document.getElementById('pdfZoomIn');
  const btnZoomOut = document.getElementById('pdfZoomOut');
  const btnClear = document.getElementById('annoClear');
  const btnDownloadPng = document.getElementById('annoDownloadPng');
  const toolSelect = document.getElementById('toolSelect');
  const colorPicker = document.getElementById('colorPicker');
  const widthSelect = document.getElementById('widthSelect');

  let pdfDoc = null, currentPage = 1, zoom = 1.25;
  let drawing = false, lastX = 0, lastY = 0;

  function fitAnnoCanvas() {
    if (!annoCanvas || !pdfCanvas) return;
    annoCanvas.width = pdfCanvas.width;
    annoCanvas.height = pdfCanvas.height;
  }

  function renderPage(pageNum) {
    if (!pdfDoc || !pdfCanvas || !ctx) return;
    pdfDoc.getPage(pageNum).then(page => {
      const viewport = page.getViewport({ scale: zoom });
      pdfCanvas.width = viewport.width;
      pdfCanvas.height = viewport.height;
      fitAnnoCanvas();
      const renderContext = { canvasContext: ctx, viewport };
      page.render(renderContext).promise.then(() => {
        if (pageInfo) pageInfo.textContent = `Page ${currentPage} / ${pdfDoc.numPages}`;
        fitAnnoCanvas();
      });
    });
  }

  function openPdfOverlay(url, name) {
    if (!overlay) return;
    overlay.classList.remove('hidden');
    overlay.setAttribute('aria-hidden', 'false');
    currentPage = 1; zoom = 1.25;
    if (window['pdfjsLib']) {
      pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
      pdfjsLib.getDocument(url).promise.then(doc => {
        if (pdfError) { pdfError.style.display = 'none'; pdfError.textContent = ''; }
        pdfDoc = doc;
        renderPage(currentPage);
        if (pageInfo) pageInfo.textContent = `Page ${currentPage} / ${pdfDoc.numPages}`;
        if (actx) actx.clearRect(0,0,annoCanvas.width, annoCanvas.height);
      }).catch(err => {
        if (pdfError) {
          pdfError.textContent = 'Failed to load PDF: ' + (err && err.message ? err.message : 'Unknown error');
          pdfError.style.display = 'block';
        }
      });
    } else {
      if (pdfError) {
        pdfError.textContent = 'PDF.js failed to load. Please check your network or CDN.';
        pdfError.style.display = 'block';
      }
    }
  }

  function closePdfOverlay() {
    if (!overlay) return;
    overlay.classList.add('hidden');
    overlay.setAttribute('aria-hidden', 'true');
    if (actx) actx.clearRect(0,0,annoCanvas.width, annoCanvas.height);
  }

  if (btnClose) btnClose.addEventListener('click', closePdfOverlay);
  if (btnPrev) btnPrev.addEventListener('click', () => { if (pdfDoc && currentPage > 1) { currentPage--; renderPage(currentPage); if (actx) actx.clearRect(0,0,annoCanvas.width, annoCanvas.height); fitAnnoCanvas(); } });
  if (btnNext) btnNext.addEventListener('click', () => { if (pdfDoc && currentPage < pdfDoc.numPages) { currentPage++; renderPage(currentPage); if (actx) actx.clearRect(0,0,annoCanvas.width, annoCanvas.height); fitAnnoCanvas(); } });
  if (btnZoomIn) btnZoomIn.addEventListener('click', () => { if (pdfDoc) { zoom = Math.min(zoom + 0.25, 4); renderPage(currentPage); if (actx) actx.clearRect(0,0,annoCanvas.width, annoCanvas.height); fitAnnoCanvas(); } });
  if (btnZoomOut) btnZoomOut.addEventListener('click', () => { if (pdfDoc) { zoom = Math.max(zoom - 0.25, 0.5); renderPage(currentPage); if (actx) actx.clearRect(0,0,annoCanvas.width, annoCanvas.height); fitAnnoCanvas(); } });
  if (btnClear) btnClear.addEventListener('click', () => { if (actx) actx.clearRect(0,0,annoCanvas.width, annoCanvas.height); });
  if (btnDownloadPng) btnDownloadPng.addEventListener('click', () => {
    // Merge layers: draw pdfCanvas and annoCanvas into a temp canvas
    const tmp = document.createElement('canvas');
    tmp.width = pdfCanvas.width; tmp.height = pdfCanvas.height;
    const tctx = tmp.getContext('2d');
    tctx.drawImage(pdfCanvas, 0, 0);
    tctx.drawImage(annoCanvas, 0, 0);
    const a = document.createElement('a');
    a.href = tmp.toDataURL('image/png');
    a.download = `annotation_page_${currentPage}.png`;
    a.click();
  });

  // Annotation drawing
  function getStrokeStyle() {
    let color = colorPicker ? colorPicker.value : '#ff0000';
    let width = parseInt(widthSelect ? widthSelect.value : '2', 10);
    let globalAlpha = 1;
    if (toolSelect && toolSelect.value === 'highlighter') {
      globalAlpha = 0.3; width = Math.max(width, 8);
    }
    if (toolSelect && toolSelect.value === 'eraser') {
      color = '#000000'; globalAlpha = 1; // We will clear using globalCompositeOperation
    }
    return { color, width, globalAlpha };
  }

  function startDraw(x, y) {
    if (!actx) return;
    drawing = true; lastX = x; lastY = y;
    actx.lineCap = 'round'; actx.lineJoin = 'round';
    const style = getStrokeStyle();
    actx.strokeStyle = style.color;
    actx.lineWidth = style.width;
    actx.globalAlpha = style.globalAlpha;
    if (toolSelect && toolSelect.value === 'eraser') {
      actx.globalCompositeOperation = 'destination-out';
    } else {
      actx.globalCompositeOperation = 'source-over';
    }
  }
  function drawTo(x, y) {
    if (!actx || !drawing) return;
    actx.beginPath();
    actx.moveTo(lastX, lastY);
    actx.lineTo(x, y);
    actx.stroke();
    lastX = x; lastY = y;
  }
  function endDraw() { drawing = false; }

  function getCanvasCoords(evt) {
    const rect = annoCanvas.getBoundingClientRect();
    const x = (evt.clientX - rect.left) * (annoCanvas.width / rect.width);
    const y = (evt.clientY - rect.top) * (annoCanvas.height / rect.height);
    return { x, y };
  }

  if (annoCanvas) {
    annoCanvas.addEventListener('mousedown', (e) => { const {x,y} = getCanvasCoords(e); startDraw(x, y); });
    annoCanvas.addEventListener('mousemove', (e) => { const {x,y} = getCanvasCoords(e); drawTo(x, y); });
    window.addEventListener('mouseup', endDraw);
    // Touch
    annoCanvas.addEventListener('touchstart', (e) => { e.preventDefault(); const t=e.touches[0]; const {x,y}=getCanvasCoords(t); startDraw(x,y); });
    annoCanvas.addEventListener('touchmove', (e) => { e.preventDefault(); const t=e.touches[0]; const {x,y}=getCanvasCoords(t); drawTo(x,y); });
    annoCanvas.addEventListener('touchend', endDraw);
  }
});