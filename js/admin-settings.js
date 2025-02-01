
document.getElementById('generalSettingsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    alert('General settings saved successfully!');
});

document.getElementById('adminAccountForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    if (newPassword !== confirmPassword) {
        alert('Passwords do not match. Please try again.');
    } else {
        alert('Admin account updated successfully!');
    }
});
