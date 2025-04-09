window.addEventListener('load', function() {
   this.reset();    
});
document.getElementById('registrationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Reset error states
    document.querySelectorAll('.error-message').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.form-control').forEach(el => el.classList.remove('input-error'));
    
    let isValid = true;
    
    // Validate first name
    const firstName = document.getElementById('firstName').value.trim();
    if (!firstName) {
        document.getElementById('firstNameError').style.display = 'block';
        document.getElementById('firstName').classList.add('input-error');
        isValid = false;
    }
    
    // Validate last name
    const lastName = document.getElementById('lastName').value.trim();
    if (!lastName) {
        document.getElementById('lastNameError').style.display = 'block';
        document.getElementById('lastName').classList.add('input-error');
        isValid = false;
    }
    
    // Validate email
    const email = document.getElementById('email').value.trim();
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        document.getElementById('emailError').style.display = 'block';
        document.getElementById('email').classList.add('input-error');
        isValid = false;
    }
    
    // Validate username
    const username = document.getElementById('username').value.trim();
    if (!username) {
        document.getElementById('usernameError').style.display = 'block';
        document.getElementById('username').classList.add('input-error');
        isValid = false;
    }
    
    // Validate password
    const password = document.getElementById('password').value;
    if (password.length < 8) {
        document.getElementById('passwordError').style.display = 'block';
        document.getElementById('password').classList.add('input-error');
        isValid = false;
    }
    
    // Validate confirm password
    const confirmPassword = document.getElementById('confirmPassword').value;
    if (password !== confirmPassword) {
        document.getElementById('confirmPasswordError').style.display = 'block';
        document.getElementById('confirmPassword').classList.add('input-error');
        isValid = false;
    }
    
    if (isValid) {
        // Form is valid
        this.submit();
        
        // this.reset();
    }
});