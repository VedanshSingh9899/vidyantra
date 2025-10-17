/**
 * Attendance Manager - Handles all attendance-related operations
 * Supports marking, viewing, and exporting attendance data
 */

class AttendanceManager {
  constructor() {
    this.currentClass = 'CSE-1';
    this.currentSection = 'A';
    this.currentDate = new Date().toISOString().split('T')[0];
    this.students = this.initializeStudents();
    this.attendanceRecords = this.loadAttendanceRecords();
    this.init();
  }

  // Initialize sample student data
  initializeStudents() {
    const students = [];
    const names = [
      'Aarav Kumar', 'Priya Singh', 'Rohan Sharma', 'Ananya Patel',
      'Arjun Verma', 'Sneha Reddy', 'Vikram Joshi', 'Diya Gupta',
      'Karan Mehta', 'Ishita Rao', 'Aditya Desai', 'Kavya Nair',
      'Sahil Khan', 'Riya Malhotra', 'Manish Iyer', 'Pooja Chopra',
      'Varun Bose', 'Nisha Agarwal', 'Rahul Sinha', 'Meera Pillai'
    ];

    for (let i = 0; i < 20; i++) {
      students.push({
        id: `STU-${String(i + 1).padStart(3, '0')}`,
        name: names[i],
        class: 'CSE-1',
        section: 'A',
        rollNo: i + 1
      });
    }
    return students;
  }

  // Load attendance records from localStorage
  loadAttendanceRecords() {
    const stored = localStorage.getItem('attendanceRecords');
    return stored ? JSON.parse(stored) : {};
  }

  // Save attendance records to localStorage
  saveAttendanceRecords() {
    localStorage.setItem('attendanceRecords', JSON.stringify(this.attendanceRecords));
  }

  // Get attendance key for storage
  getAttendanceKey(date, classId, section) {
    return `${date}_${classId}_${section}`;
  }

  // Get or create attendance record for a specific date/class/section
  getAttendanceRecord(date, classId, section) {
    const key = this.getAttendanceKey(date, classId, section);
    if (!this.attendanceRecords[key]) {
      this.attendanceRecords[key] = {
        date,
        class: classId,
        section,
        records: this.students.map(s => ({
          studentId: s.id,
          name: s.name,
          status: 'unmarked', // unmarked, present, absent, late, excused
          markedAt: null,
          notes: ''
        }))
      };
    }
    return this.attendanceRecords[key];
  }

  // Mark attendance for a student
  markAttendance(studentId, status, notes = '') {
    const record = this.getAttendanceRecord(
      this.currentDate,
      this.currentClass,
      this.currentSection
    );

    const studentRecord = record.records.find(r => r.studentId === studentId);
    if (studentRecord) {
      studentRecord.status = status;
      studentRecord.markedAt = new Date().toISOString();
      studentRecord.notes = notes;
      this.saveAttendanceRecords();
      return true;
    }
    return false;
  }

  // Bulk mark attendance
  bulkMarkAttendance(studentIds, status) {
    studentIds.forEach(id => this.markAttendance(id, status));
  }

  // Get attendance statistics
  getAttendanceStats(date, classId, section) {
    const record = this.getAttendanceRecord(date, classId, section);
    const stats = {
      total: record.records.length,
      present: 0,
      absent: 0,
      late: 0,
      excused: 0,
      unmarked: 0
    };

    record.records.forEach(r => {
      stats[r.status]++;
    });

    stats.percentage = stats.total > 0 
      ? ((stats.present / stats.total) * 100).toFixed(1)
      : 0;

    return stats;
  }

  // Get student attendance history
  getStudentHistory(studentId) {
    const history = [];
    Object.values(this.attendanceRecords).forEach(record => {
      const studentRecord = record.records.find(r => r.studentId === studentId);
      if (studentRecord) {
        history.push({
          date: record.date,
          class: record.class,
          section: record.section,
          status: studentRecord.status,
          notes: studentRecord.notes
        });
      }
    });
    return history.sort((a, b) => new Date(b.date) - new Date(a.date));
  }

  // Export to JSON
  exportToJSON() {
    const dataStr = JSON.stringify(this.attendanceRecords, null, 2);
    const dataBlob = new Blob([dataStr], { type: 'application/json' });
    const url = URL.createObjectURL(dataBlob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `attendance_${new Date().toISOString().split('T')[0]}.json`;
    link.click();
    URL.revokeObjectURL(url);
  }

  // Export to CSV
  exportToCSV(date, classId, section) {
    const record = this.getAttendanceRecord(date, classId, section);
    let csv = 'Student ID,Name,Roll No,Status,Marked At,Notes\n';
    
    record.records.forEach(r => {
      const student = this.students.find(s => s.id === r.studentId);
      csv += `${r.studentId},"${r.name}",${student.rollNo},${r.status},${r.markedAt || 'N/A'},"${r.notes}"\n`;
    });

    const blob = new Blob([csv], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `attendance_${classId}_${section}_${date}.csv`;
    link.click();
    URL.revokeObjectURL(url);
  }

  // Export attendance log (all dates)
  exportAttendanceLog() {
    let csv = 'Date,Class,Section,Total,Present,Absent,Late,Excused,Unmarked,Percentage\n';
    
    Object.values(this.attendanceRecords).forEach(record => {
      const stats = this.getAttendanceStats(record.date, record.class, record.section);
      csv += `${record.date},${record.class},${record.section},${stats.total},${stats.present},${stats.absent},${stats.late},${stats.excused},${stats.unmarked},${stats.percentage}%\n`;
    });

    const blob = new Blob([csv], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `attendance_log_${new Date().toISOString().split('T')[0]}.csv`;
    link.click();
    URL.revokeObjectURL(url);
  }

  // Render attendance portal
  renderAttendancePortal() {
    const modal = document.createElement('div');
    modal.id = 'attendancePortalModal';
    modal.className = 'attendance-modal';
    modal.innerHTML = `
      <div class="attendance-modal-content">
        <div class="attendance-modal-header">
          <h2>Mark Attendance</h2>
          <button id="closeAttendancePortal" class="close-btn">✖</button>
        </div>
        
        <div class="attendance-controls">
          <div class="control-group">
            <label>Date:</label>
            <input type="date" id="attendanceDate" value="${this.currentDate}">
          </div>
          <div class="control-group">
            <label>Class:</label>
            <select id="attendanceClass">
              <option value="CSE-1">CSE-1</option>
              <option value="CSE-2">CSE-2</option>
              <option value="CSE-3">CSE-3</option>
            </select>
          </div>
          <div class="control-group">
            <label>Section:</label>
            <select id="attendanceSection">
              <option value="A">A</option>
              <option value="B">B</option>
            </select>
          </div>
          <button id="loadAttendance" class="btn primary">Load</button>
        </div>

        <div class="attendance-stats" id="attendanceStats"></div>

        <div class="bulk-actions">
          <button class="btn" id="markAllPresent">Mark All Present</button>
          <button class="btn" id="markAllAbsent">Mark All Absent</button>
          <button class="btn primary" id="saveAttendance">Save Attendance</button>
          <button class="btn" id="exportAttendanceCSV">Export CSV</button>
        </div>

        <div class="attendance-list" id="attendanceList"></div>
      </div>
    `;

    document.body.appendChild(modal);
    this.attachPortalEvents();
    this.renderAttendanceList();
  }

  // Render attendance list
  renderAttendanceList() {
    const listContainer = document.getElementById('attendanceList');
    if (!listContainer) return;

    const record = this.getAttendanceRecord(
      this.currentDate,
      this.currentClass,
      this.currentSection
    );

    listContainer.innerHTML = `
      <table class="attendance-table">
        <thead>
          <tr>
            <th>Roll No</th>
            <th>Student ID</th>
            <th>Name</th>
            <th>Status</th>
            <th>Notes</th>
          </tr>
        </thead>
        <tbody>
          ${record.records.map((r, idx) => {
            const student = this.students.find(s => s.id === r.studentId);
            return `
              <tr data-student-id="${r.studentId}">
                <td>${student.rollNo}</td>
                <td>${r.studentId}</td>
                <td>${r.name}</td>
                <td>
                  <select class="status-select" data-student-id="${r.studentId}">
                    <option value="unmarked" ${r.status === 'unmarked' ? 'selected' : ''}>Unmarked</option>
                    <option value="present" ${r.status === 'present' ? 'selected' : ''}>Present</option>
                    <option value="absent" ${r.status === 'absent' ? 'selected' : ''}>Absent</option>
                    <option value="late" ${r.status === 'late' ? 'selected' : ''}>Late</option>
                    <option value="excused" ${r.status === 'excused' ? 'selected' : ''}>Excused</option>
                  </select>
                </td>
                <td>
                  <input type="text" class="notes-input" data-student-id="${r.studentId}" 
                         value="${r.notes}" placeholder="Add notes...">
                </td>
              </tr>
            `;
          }).join('')}
        </tbody>
      </table>
    `;

    this.updateStats();
    this.attachListEvents();
  }

  // Update attendance statistics
  updateStats() {
    const statsContainer = document.getElementById('attendanceStats');
    if (!statsContainer) return;

    const stats = this.getAttendanceStats(
      this.currentDate,
      this.currentClass,
      this.currentSection
    );

    statsContainer.innerHTML = `
      <div class="stat-item">
        <span class="stat-label">Total:</span>
        <span class="stat-value">${stats.total}</span>
      </div>
      <div class="stat-item present">
        <span class="stat-label">Present:</span>
        <span class="stat-value">${stats.present}</span>
      </div>
      <div class="stat-item absent">
        <span class="stat-label">Absent:</span>
        <span class="stat-value">${stats.absent}</span>
      </div>
      <div class="stat-item late">
        <span class="stat-label">Late:</span>
        <span class="stat-value">${stats.late}</span>
      </div>
      <div class="stat-item excused">
        <span class="stat-label">Excused:</span>
        <span class="stat-value">${stats.excused}</span>
      </div>
      <div class="stat-item percentage">
        <span class="stat-label">Attendance:</span>
        <span class="stat-value">${stats.percentage}%</span>
      </div>
    `;
  }

  // Attach event listeners to portal
  attachPortalEvents() {
    document.getElementById('closeAttendancePortal')?.addEventListener('click', () => {
      document.getElementById('attendancePortalModal')?.remove();
    });

    document.getElementById('loadAttendance')?.addEventListener('click', () => {
      this.currentDate = document.getElementById('attendanceDate').value;
      this.currentClass = document.getElementById('attendanceClass').value;
      this.currentSection = document.getElementById('attendanceSection').value;
      this.renderAttendanceList();
    });

    document.getElementById('markAllPresent')?.addEventListener('click', () => {
      const allIds = this.students.map(s => s.id);
      this.bulkMarkAttendance(allIds, 'present');
      this.renderAttendanceList();
    });

    document.getElementById('markAllAbsent')?.addEventListener('click', () => {
      const allIds = this.students.map(s => s.id);
      this.bulkMarkAttendance(allIds, 'absent');
      this.renderAttendanceList();
    });

    document.getElementById('saveAttendance')?.addEventListener('click', () => {
      alert('Attendance saved successfully!');
    });

    document.getElementById('exportAttendanceCSV')?.addEventListener('click', () => {
      this.exportToCSV(this.currentDate, this.currentClass, this.currentSection);
    });
  }

  // Attach event listeners to list items
  attachListEvents() {
    document.querySelectorAll('.status-select').forEach(select => {
      select.addEventListener('change', (e) => {
        const studentId = e.target.dataset.studentId;
        const status = e.target.value;
        const notesInput = document.querySelector(`.notes-input[data-student-id="${studentId}"]`);
        const notes = notesInput ? notesInput.value : '';
        this.markAttendance(studentId, status, notes);
        this.updateStats();
      });
    });

    document.querySelectorAll('.notes-input').forEach(input => {
      input.addEventListener('blur', (e) => {
        const studentId = e.target.dataset.studentId;
        const notes = e.target.value;
        const select = document.querySelector(`.status-select[data-student-id="${studentId}"]`);
        const status = select ? select.value : 'unmarked';
        this.markAttendance(studentId, status, notes);
      });
    });
  }

  // Initialize the manager
  init() {
    // Add event listener for opening attendance portal
    document.addEventListener('DOMContentLoaded', () => {
      const openBtn = document.getElementById('openAttendancePortalBtn');
      if (openBtn) {
        openBtn.addEventListener('click', () => {
          this.renderAttendancePortal();
        });
      }

      // Export button in attendance page
      const exportBtn = document.getElementById('exportCsv');
      if (exportBtn) {
        exportBtn.addEventListener('click', () => {
          this.exportAttendanceLog();
        });
      }
    });
  }
}

// Initialize the attendance manager
const attendanceManager = new AttendanceManager();