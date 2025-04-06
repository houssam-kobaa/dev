// 1. Load Orders with Filters
async function loadOrders(filters = {}) {
    const query = new URLSearchParams(filters).toString();
    const response = await fetch(`/api/admin/get_orders.php?${query}`);
    const orders = await response.json();
  
    // Render table with sorting capabilities
  }
  
  // 2. Real-time Updates
  function setupWebSocket() {
    const ws = new WebSocket(`wss://${window.location.host}/ws-orders`);
    
    ws.onmessage = (event) => {
      const data = JSON.parse(event.data);
      if (data.type === 'new_order') {
        addNewOrderToTable(data.order);
      }
    };
  }
  
  // 3. Status Update Handler
  document.querySelectorAll('.status-select').forEach(select => {
    select.addEventListener('change', async (e) => {
      const orderId = e.target.dataset.orderId;
      const newStatus = e.target.value;
      
      await fetch('/api/admin/update_status.php', {
        method: 'POST',
        body: JSON.stringify({ orderId, newStatus })
      });
  
      // Visual feedback
    });
  });
  async function adminLogout() {
    try {
        const response = await fetch('/api/admin/logout.php', {
            method: 'POST',
            credentials: 'same-origin' // Include cookies
        });
        
        if (response.ok) {
            // Clear client-side data
            localStorage.clear();
            sessionStorage.clear();
            
            // Redirect to login
            window.location.href = '/admin/login.html';
        }
    } catch (error) {
        console.error('Logout failed:', error);
    }
}

// Attach to logout button
document.getElementById('logout-btn').addEventListener('click', adminLogout);