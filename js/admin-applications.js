

document.querySelectorAll('.approve-btn').forEach(button => {
    button.addEventListener('click', function() {
        const applicantName = this.closest('.application-item').querySelector('strong').textContent;
        alert(`Application from ${applicantName} has been approved.`);
        this.closest('.application-item').remove();
    });
});

document.querySelectorAll('.reject-btn').forEach(button => {
    button.addEventListener('click', function() {
        const applicantName = this.closest('.application-item').querySelector('strong').textContent;
        if (confirm(`Are you sure you want to reject the application from ${applicantName}?`)) {
            alert(`Application from ${applicantName} has been rejected.`);
            this.closest('.application-item').remove();
        }
    });
});
