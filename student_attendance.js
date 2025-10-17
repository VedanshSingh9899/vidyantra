

document.addEventListener('DOMContentLoaded', () => {
  const markAttendanceBtn = document.getElementById('markAttendanceBtn');
  function updateButtonState() {
    if (!markAttendanceBtn) return;
    const portalOpen = localStorage.getItem('attendancePortalOpen') === 'true';
    const studentAttendance = JSON.parse(localStorage.getItem('studentAttendanceMarked') || '{}');
    if (!portalOpen) {
      markAttendanceBtn.disabled = true;
      markAttendanceBtn.textContent = 'Portal not open';
      markAttendanceBtn.style.background = '#e5e7eb';
      markAttendanceBtn.style.color = '#9ca3af';
      markAttendanceBtn.style.cursor = 'not-allowed';
    } else if (studentAttendance.marked) {
      markAttendanceBtn.disabled = true;
      markAttendanceBtn.textContent = 'Portal not open';
      markAttendanceBtn.style.background = '#e5e7eb';
      markAttendanceBtn.style.color = '#9ca3af';
      markAttendanceBtn.style.cursor = 'not-allowed';
    } else {
      markAttendanceBtn.disabled = false;
      markAttendanceBtn.textContent = 'Mark Attendance';
      markAttendanceBtn.style.background = '';
      markAttendanceBtn.style.color = '';
      markAttendanceBtn.style.cursor = '';
    }
  }

  updateButtonState();

  if (markAttendanceBtn) {
    markAttendanceBtn.addEventListener('click', () => {
      if (markAttendanceBtn.disabled) return;
      localStorage.setItem('studentAttendanceMarked', JSON.stringify({ marked: true, date: new Date().toISOString() }));
      updateButtonState();
      alert('Attendance marked! Teacher will see this reflected.');
    });
  }

  
  window.addEventListener('storage', (e) => {
    if (e.key === 'attendancePortalOpen' || e.key === 'studentAttendanceMarked') {
      updateButtonState();
    }
  });
});
