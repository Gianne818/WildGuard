function updateClock() {
    const now = new Date();
    const dateStr = now.toLocaleDateString('en-US');
    const timeStr = now.toLocaleTimeString('en-GB'); 
    
    document.querySelectorAll('.live-date').forEach(el => el.innerText = dateStr);
    document.querySelectorAll('.live-time').forEach(el => el.innerText = timeStr);
}
setInterval(updateClock, 1000);
updateClock();

function switchTab(element, tabId) {
    // 1. Hide all forms
    document.getElementById('form-student').style.display = 'none';
    document.getElementById('form-personnel').style.display = 'none';
    document.getElementById('form-visitor').style.display = 'none';
    
    // 2. Remove the 'active' class from all tabs
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    
    // 3. Show the selected form
    document.getElementById('form-' + tabId).style.display = 'block';
    
    // 4. Add the 'active' class to the tab you just clicked
    element.classList.add('active');
}