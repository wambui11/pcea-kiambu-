// transport.js
document.getElementById('transportForm').addEventListener('submit', function(e){
  let name = document.getElementById('studentName').value.trim();
  let phone = document.getElementById('parent_Phone').value.trim(); // Fix: match the form field name
  let zone = document.getElementById('Zone_id').value;

  if(name.length < 2){
    alert("Student name is too short!");
    e.preventDefault();
    return;
  }

  if(phone.length < 10){
    alert("Parent phone number seems invalid!");
    e.preventDefault();
    return;
  }

  if(!zone){
    alert("Please select a zone!");
    e.preventDefault();
    return;
  }
});

