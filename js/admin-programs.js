
document.getElementById('addProgramBtn').addEventListener('click', function() {
    alert('Add New Program form would appear here. This would include fields for program name, type, duration, partner institutions, and other relevant details.');
});

document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', function() {
        const programName = this.closest('.program-item').querySelector('strong').textContent;
        alert(`Edit form for ${programName} would appear here. This would allow updating program details.`);
    });
});

document.querySelectorAll('.delete-btn').forEach(button => {
    button.addEventListener('click', function() {
        const programName = this.closest('.program-item').querySelector('strong').textContent;
        if (confirm(`Are you sure you want to delete the program "${programName}"?`)) {
            alert(`Program "${programName}" has been deleted.`);
            this.closest('.program-item').remove();
        }
    });
});
