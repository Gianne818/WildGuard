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

// Simple message box helper (uses Bootstrap alert styles)
function showMessage(message, type = 'info', timeout = 4000) {
    let container = document.getElementById('site-message-container');
    if(!container) {
        container = document.createElement('div');
        container.id = 'site-message-container';
        container.style.position = 'fixed';
        container.style.top = '20px';
        container.style.right = '20px';
        container.style.zIndex = 1060;
        document.body.appendChild(container);
    }

    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert';
    const typeClass = ({info:'alert-info', success:'alert-success', danger:'alert-danger', warning:'alert-warning'}[type] || 'alert-info');
    alertDiv.classList.add(typeClass);
    alertDiv.style.minWidth = '220px';
    alertDiv.style.boxShadow = '0 4px 12px rgba(0,0,0,0.08)';
    alertDiv.style.borderRadius = '6px';
    alertDiv.style.marginTop = '8px';
    alertDiv.innerText = message;

    container.appendChild(alertDiv);

    setTimeout(() => {
        alertDiv.style.transition = 'opacity 0.25s ease';
        alertDiv.style.opacity = '0';
        setTimeout(() => alertDiv.remove(), 300);
    }, timeout);
}

// Drain pre-loaded message queue (if any)
if(window._msgQueue && Array.isArray(window._msgQueue)){
    window._msgQueue.forEach(item => {
        try{ showMessage(item.m, item.t || 'info', item.timeout || 4000); }catch(e){}
    });
    window._msgQueue = [];
}