
// Partnership Growth Chart
const partnershipCtx = document.getElementById('partnershipChart').getContext('2d');
new Chart(partnershipCtx, {
    type: 'line',
    data: {
        labels: ['2019', '2020', '2021', '2022', '2023'],
        datasets: [{
            label: 'Number of Partnerships',
            data: [20, 25, 30, 35, 42],
            borderColor: 'rgb(75, 192, 192)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
    }
});

// Program Participation Chart
const programCtx = document.getElementById('programChart').getContext('2d');
new Chart(programCtx, {
    type: 'bar',
    data: {
        labels: ['Study Abroad', 'Faculty Exchange', 'Research Collaboration', 'Summer Programs', 'Internships'],
        datasets: [{
            label: 'Number of Participants',
            data: [150, 30, 45, 80, 60],
            backgroundColor: 'rgba(54, 162, 235, 0.5)',
            borderColor: 'rgb(54, 162, 235)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Application Statistics Chart
const applicationCtx = document.getElementById('applicationChart').getContext('2d');
new Chart(applicationCtx, {
    type: 'pie',
    data: {
        labels: ['Approved', 'Rejected', 'Pending'],
        datasets: [{
            data: [65, 15, 20],
            backgroundColor: [
                'rgba(75, 192, 192, 0.5)',
                'rgba(255, 99, 132, 0.5)',
                'rgba(255, 205, 86, 0.5)'
            ],
            borderColor: [
                'rgb(75, 192, 192)',
                'rgb(255, 99, 132)',
                'rgb(255, 205, 86)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
    }
});

document.getElementById('generateReport').addEventListener('click', function() {
    alert('Generating full report... This would typically create a comprehensive PDF report with detailed statistics and analysis.');
});
