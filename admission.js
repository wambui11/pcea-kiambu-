document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('admission-form');

  if (form) {
    form.addEventListener('submit', function(e) {


      // Validate email (both name or type field)
      const emailInput = document.querySelector('input[name="email"], input[name="email address"], input[type="email"]');
      if (emailInput) {
        const emailValue = emailInput.value.trim();
        if (!/^\S+@\S+\.\S+$/.test(emailValue)) {
          alert('Please enter a valid email address.');
          e.preventDefault();
          return;
        }
      }

      // All validations passed, allow form to submit normally to PHP
    });
  }
});
