// 1. Dynamic Menu Loading
async function loadMenu() {
    const response = await fetch('/api/customer/get_menu.php');
    const items = await response.json();
    
    items.forEach(item => {
      // Render interactive menu items
    });
  }
  
  // 2. Real-time Order Calculation
  function updateOrderSummary() {
    let total = 0;
    selectedItems.forEach(item => {
      total += item.price * item.quantity;
    });
    document.getElementById('total').textContent = total.toFixed(2);
  }
  
  // 3. Order Submission
  document.getElementById('place-order').addEventListener('click', async () => {
    const orderData = {
      customer: {
        name: document.getElementById('customer-name').value,
        phone: document.getElementById('customer-phone').value
      },
      items: Array.from(selectedItems.values())
    };
  
    const response = await fetch('/api/customer/create_order.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(orderData)
    });
  
    // Show confirmation modal
  });