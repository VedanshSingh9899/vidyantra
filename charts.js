


Chart.defaults.color = '#9ca3af';
Chart.defaults.borderColor = '#374151';
Chart.defaults.font.family = "'Inter', sans-serif";


const skillsRadarCtx = document.getElementById('skillsRadar').getContext('2d');
const skillsRadar = new Chart(skillsRadarCtx, {
    type: 'radar',
    data: {
        labels: ['DSA', 'Algorithms', 'DBMS', 'OS', 'Networks'],
        datasets: [{
            label: 'Skill Level',
            data: [80, 65, 75, 50, 60],
            backgroundColor: 'rgba(167, 139, 250, 0.2)',
            borderColor: '#a78bfa',
            pointBackgroundColor: '#a78bfa',
            pointBorderColor: '#000000ff',
            pointHoverBackgroundColor: '#000000ff',
            pointHoverBorderColor: '#a78bfa'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            r: {
                beginAtZero: true,
                max: 100,
                grid: { color: '#374151' },
                angleLines: { color: '#374151' },
                pointLabels: {
                    font: { size: 14, weight: 'bold' },
                    color: '#000000ff'
                },
                ticks: {
                    backdropColor: 'transparent',
                    color: '#000000ff'
                }
            }
        },
        plugins: {
            legend: {
                position: 'top',
                 labels: {
                    color: '#000000ff',
                    font: { size: 14 }
                }
            }
        }
    }
});


const assignmentChartCtx = document.getElementById('assignmentChart').getContext('2d');
const assignmentChart = new Chart(assignmentChartCtx, {
    type: 'bar',
    data: {
        labels: ['DSA', 'Algo', 'DBMS', 'OS', 'Networks'],
        datasets: [{
            label: 'Assignments Completed',
            data: [5, 4, 3, 2, 4],
            backgroundColor: ['#8b5cf6', '#ec4899', '#3b82f6', '#10b981', '#f59e0b'],
            borderRadius: 8,
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: '#374151'
                }
            },
             x: {
                grid: {
                    display: false
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});


const consistencyChartCtx = document.getElementById('consistencyChart').getContext('2d');
const gradient = consistencyChartCtx.createLinearGradient(0, 0, 0, 200);
gradient.addColorStop(0, 'rgba(236, 72, 153, 0.5)');
gradient.addColorStop(1, 'rgba(236, 72, 153, 0)');

const consistencyChart = new Chart(consistencyChartCtx, {
    type: 'line',
    data: {
        labels: ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7'],
        datasets: [{
            label: 'Tasks Done',
            data: [1, 2, 1, 3, 2, 2, 1],
            borderColor: '#ec4899',
            backgroundColor: gradient,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#ec4899',
            pointBorderColor: '#fff',
            pointRadius: 5
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: '#374151' }
            },
            x: {
                grid: { display: false }
            }
        }
    }
});


const badges = [
    { icon: '🏅', title: 'On-Time Finisher' },
    { icon: '🧩', title: 'Problem Solver' },
    { icon: '⚡', title: 'Fast Learner' },
    { icon: '📚', title: 'Consistency Star' },
    { icon: '💻', title: 'DSA Guru' },
    { icon: '🐛', title: 'Bug Buster'}
];
const grid = document.getElementById('badgesGrid');

badges.forEach(b => {
    const div = document.createElement('div');
    div.className = 'flex flex-col items-center justify-center p-3 bg-gray-800 rounded-lg transition-transform transform hover:scale-110 cursor-pointer';
    div.innerHTML = `<div class="text-4xl">${b.icon}</div><div class="mt-2 text-xs font-semibold text-center text-gray-300">${b.title}</div>`;
    grid.appendChild(div);
});
