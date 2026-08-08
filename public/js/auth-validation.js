// Auth Form Validation

function togglePassword(inputId, iconId) {
    var input = document.getElementById(inputId);
    var icon = document.getElementById(iconId);
    
    if (!input || !icon) return;
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />';
    } else {
        input.type = 'password';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
    }
}

function validatePassword(input) {
    var errorEl = document.getElementById('password_error');
    if (!input || !errorEl) return;
    
    var pwd = input.value;
    
    // Reset styles
    input.classList.remove('border-red-500', 'dark:border-red-400', 'ring-1', 'ring-red-500', 'border-green-500', 'dark:border-green-400');
    
    if (!pwd) {
        errorEl.classList.add('hidden');
        errorEl.textContent = '';
        return;
    }
    
    // Check each requirement
    if (pwd.length < 8) {
        errorEl.textContent = 'At least 8 characters required.';
        errorEl.classList.remove('hidden');
        input.classList.add('border-red-500', 'dark:border-red-400', 'ring-1', 'ring-red-500');
        return;
    }
    if (!/[A-Z]/.test(pwd)) {
        errorEl.textContent = 'Add at least one uppercase letter.';
        errorEl.classList.remove('hidden');
        input.classList.add('border-red-500', 'dark:border-red-400', 'ring-1', 'ring-red-500');
        return;
    }
    if (!/[a-z]/.test(pwd)) {
        errorEl.textContent = 'Add at least one lowercase letter.';
        errorEl.classList.remove('hidden');
        input.classList.add('border-red-500', 'dark:border-red-400', 'ring-1', 'ring-red-500');
        return;
    }
    if (!/[0-9]/.test(pwd)) {
        errorEl.textContent = 'Add at least one number.';
        errorEl.classList.remove('hidden');
        input.classList.add('border-red-500', 'dark:border-red-400', 'ring-1', 'ring-red-500');
        return;
    }
    if (!/[!@#$%^&*(),.?":{}|<>]/.test(pwd)) {
        errorEl.textContent = 'Add at least one special character.';
        errorEl.classList.remove('hidden');
        input.classList.add('border-red-500', 'dark:border-red-400', 'ring-1', 'ring-red-500');
        return;
    }
    
    // All valid
    errorEl.classList.add('hidden');
    errorEl.textContent = '';
    input.classList.add('border-green-500', 'dark:border-green-400');
}

function validateConfirmPassword(input) {
    var errorEl = document.getElementById('confirm_error');
    var pwdInput = document.getElementById('password');
    if (!input || !errorEl || !pwdInput) return;
    
    var pwd = pwdInput.value;
    var confirmPwd = input.value;
    
    // Reset styles
    input.classList.remove('border-red-500', 'dark:border-red-400', 'ring-1', 'ring-red-500', 'border-green-500', 'dark:border-green-400');
    
    if (!confirmPwd) {
        errorEl.classList.add('hidden');
        errorEl.textContent = '';
        return;
    }
    
    if (pwd !== confirmPwd) {
        errorEl.textContent = 'Passwords do not match.';
        errorEl.classList.remove('hidden');
        input.classList.add('border-red-500', 'dark:border-red-400', 'ring-1', 'ring-red-500');
        return;
    }
    
    // Match
    errorEl.classList.add('hidden');
    errorEl.textContent = '';
    input.classList.add('border-green-500', 'dark:border-green-400');
}

// Initialize when DOM is ready - use direct attachment
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initValidation);
} else {
    initValidation();
}

function initValidation() {
    var passwordInput = document.getElementById('password');
    var confirmInput = document.getElementById('password_confirmation');
    
    if (passwordInput) {
        passwordInput.removeAttribute('onblur');
        passwordInput.addEventListener('blur', function() {
            validatePassword(this);
        });
    }
    
    if (confirmInput) {
        confirmInput.removeAttribute('onblur');
        confirmInput.addEventListener('blur', function() {
            validateConfirmPassword(this);
        });
    }
}
