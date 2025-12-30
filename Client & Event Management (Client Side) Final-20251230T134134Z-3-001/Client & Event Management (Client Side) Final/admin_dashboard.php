<?php
session_start();
// Security: Redirect if not admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html?error=unauthorized_access");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | EOS</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        /* Admin Specific Styles */
        .admin-header { background: #2c3e50; padding: 20px 4%; display: flex; justify-content: space-between; align-items: center; color: white; }
        .admin-brand { font-family: 'Playfair Display', serif; font-size: 1.5rem; font-weight: 700; }
        
        .dashboard-container { padding: 40px 4%; max-width: 1400px; margin: 0 auto; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border-left: 4px solid var(--vino); }
        .stat-number { font-size: 2rem; font-weight: 700; color: #333; }
        .stat-label { color: #666; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; }

        /* Table Styles */
        .data-table-wrapper { background: white; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); overflow: hidden; }
        .admin-table { width: 100%; border-collapse: collapse; min-width: 800px; }
        .admin-table th { background: #f8f9fa; padding: 15px; text-align: left; font-weight: 600; color: #444; border-bottom: 2px solid #eee; font-size: 0.9rem; }
        .admin-table td { padding: 15px; border-bottom: 1px solid #eee; font-size: 0.95rem; color: #333; vertical-align: middle; }
        .admin-table tr:hover { background-color: #fcfcfc; }
        
        .status-badge { padding: 5px 10px; border-radius: 50px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; }
        .badge-pending { background: #fff3cd; color: #856404; }
        .badge-confirmed { background: #d4edda; color: #155724; }
        .badge-ongoing { background: #cce5ff; color: #004085; } /* New Blue for Ongoing */
        .badge-cancelled { background: #f8d7da; color: #721c24; }
        .badge-completed { background: #d1ecf1; color: #0c5460; }

        .action-btn { padding: 8px 10px; border: none; border-radius: 4px; cursor: pointer; font-size: 0.85rem; margin-right: 5px; transition: 0.2s; color:white; }
        .btn-approve { background: #28a745; }
        .btn-approve:hover { background: #218838; }
        .btn-reject { background: #dc3545; }
        .btn-reject:hover { background: #c82333; }
        .btn-complete { background: #007bff; }
        .btn-complete:hover { background: #0056b3; }
        .btn-delete { background: #343a40; } 
        .btn-delete:hover { background: #23272b; }

        /* Modal Styles */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 2000; }
        .modal-overlay.active { display: flex; }
        .modal-container { background: white; border-radius: 8px; width: 90%; max-width: 600px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .close-modal { background: none; border: none; font-size: 1.5rem; cursor: pointer; }

        @media (max-width: 768px) {
            .data-table-wrapper { overflow-x: auto; }
        }
    </style>
</head>
<body style="padding-top: 0; display: block; background: #f4f6f9;">

    <div class="admin-header">
        <div class="admin-brand">EOS Admin</div>
            <div style="display: flex; gap: 20px; align-items: center;">
                <a href="admin_dashboard.php" style="color:white; font-weight:bold; text-decoration:underline;">Dashboard</a>
                <a href="admin_services.php" style="color:white; opacity:0.8; text-decoration:none;">Services & Vendors</a>
                <span style="border-left:1px solid rgba(255,255,255,0.3); padding-left:20px;">Welcome, Admin</span>
                <a href="php/logout.php" class="btn btn-outline-light" style="padding: 5px 15px; font-size: 0.85rem; border-color: rgba(255,255,255,0.3);">Logout</a>
            </div>
    </div>

    <div class="dashboard-container">
        <h2 style="margin-bottom: 20px; color: #2c3e50;">Booking Management</h2>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number" id="statPending">0</div>
                <div class="stat-label">Pending Requests</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="statConfirmed">0</div>
                <div class="stat-label">Confirmed Events</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="statTotal">₱0</div>
                <div class="stat-label">Total Revenue (Est.)</div>
            </div>
        </div>

        <div class="data-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Ref ID</th>
                        <th>Client</th>
                        <th>Event Details</th>
                        <th>Date</th>
                        <th>Budget</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="bookingsTableBody">
                    <tr><td colspan="7" style="text-align:center; padding: 30px;">Loading data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div id="toast-container" style="position: fixed; bottom: 20px; right: 20px; z-index: 10000;"></div>

    <div id="adminDetailsModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h2>Booking Details</h2>
                <button class="close-modal" onclick="closeAdminModal()">&times;</button>
            </div>
            <div class="modal-body" id="adminModalContent">
                <div style="text-align:center; padding:20px;">Loading...</div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            loadAdminData();
        });

        async function loadAdminData() {
            try {
                const res = await fetch('php/api_admin_bookings.php');
                const result = await res.json();
                
                if(result.success) {
                    renderTable(result.data);
                    updateStats(result.data);
                }
            } catch (e) { console.error(e); }
        }

        function renderTable(bookings) {
            const tbody = document.getElementById('bookingsTableBody');
            if(bookings.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" style="text-align:center; padding: 30px;">No bookings found.</td></tr>';
                return;
            }

            tbody.innerHTML = bookings.map(bk => {
                const status = bk.booking_status;
                let actionBtns = '';

                if (status === 'Pending') {
                    // Approve / Reject
                    actionBtns = `
                        <button class="action-btn btn-approve" title="Approve" onclick="updateStatus('${bk.booking_id}', 'Confirmed')"><i class="fas fa-check"></i></button>
                        <button class="action-btn btn-reject" title="Reject" onclick="updateStatus('${bk.booking_id}', 'Cancelled')"><i class="fas fa-times"></i></button>
                    `;
                } else if (status === 'Ongoing') {
                    // Mark as Completed (Admin finishes the event)
                    actionBtns = `
                        <button class="action-btn btn-complete" title="Mark as Finished" onclick="markCompleted('${bk.booking_id}')"><i class="fas fa-flag-checkered"></i> Done</button>
                    `;
                }
                
                // Always allow Delete (Admin override)
                actionBtns += `<button class="action-btn btn-delete" title="Delete Permanently" onclick="deleteBooking('${bk.booking_id}')"><i class="fas fa-trash"></i></button>`;

                return `
                <tr>
                    <td><span style="font-family:monospace; color:#666;">${bk.booking_id}</span></td>
                    <td>
                        <strong>${bk.full_name}</strong><br>
                        <span style="font-size:0.8rem; color:#888;">${bk.contact_number}</span>
                    </td>
                    <td>
                        <div style="font-weight:600; color:var(--vino);">${bk.event_type}</div>
                        <button onclick="viewBookingDetails('${bk.booking_id}')" style="font-size:0.75rem; border:none; background:transparent; color:#007bff; cursor:pointer; text-decoration:underline; padding:0;">View Full Details</button>
                    </td>
                    <td>${bk.event_date}</td>
                    <td style="font-weight:700;">₱${parseFloat(bk.agreed_budget).toLocaleString()}</td>
                    <td><span class="status-badge badge-${status.toLowerCase()}">${status}</span></td>
                    <td>
                        <div style="display:flex; gap:5px;">
                            ${actionBtns}
                        </div>
                    </td>
                </tr>
                `;
            }).join('');
        }

        function updateStats(bookings) {
            const pending = bookings.filter(b => b.booking_status === 'Pending').length;
            const confirmed = bookings.filter(b => b.booking_status === 'Confirmed' || b.booking_status === 'Ongoing').length;
            const total = bookings
                .filter(b => b.booking_status !== 'Cancelled' && b.booking_status !== 'Pending') 
                .reduce((acc, curr) => acc + parseFloat(curr.agreed_budget), 0);

            document.getElementById('statPending').textContent = pending;
            document.getElementById('statConfirmed').textContent = confirmed;
            document.getElementById('statTotal').textContent = '₱' + total.toLocaleString();
        }

        // --- ACTION 1: Update Status (Approve/Reject) ---
        async function updateStatus(id, newStatus) {
            if(!confirm(`Mark this booking as ${newStatus}?`)) return;

            try {
                const res = await fetch('php/api_admin_action.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({ booking_id: id, status: newStatus, action: 'update' })
                });
                const result = await res.json();
                
                if(result.success) {
                    showToast(`Booking ${newStatus}!`, 'success');
                    loadAdminData(); 
                } else {
                    showToast('Error updating status.', 'error');
                }
            } catch (e) { console.error(e); }
        }

        // --- ACTION 2: Mark Completed (Ongoing -> Completed) ---
        async function markCompleted(id) {
            if(!confirm("Is this event successfully finished? This will allow the client to rate the service.")) return;

            try {
                const res = await fetch('php/api_admin_complete_booking.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({ booking_id: id })
                });
                const result = await res.json();
                
                if(result.success) {
                    showToast("Event marked as Completed!", 'success');
                    loadAdminData(); 
                } else {
                    showToast('Error completing event.', 'error');
                }
            } catch (e) { console.error(e); }
        }

        // --- ACTION 3: Delete Booking ---
        async function deleteBooking(id) {
            if(!confirm("⚠️ PERMANENTLY DELETE this booking?\n\nThis cannot be undone.")) return;

            try {
                const res = await fetch('php/api_admin_action.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({ booking_id: id, action: 'delete' })
                });
                const result = await res.json();
                
                if(result.success) {
                    showToast('Booking deleted successfully.', 'success');
                    loadAdminData(); 
                } else {
                    showToast('Error deleting booking.', 'error');
                }
            } catch (e) { console.error(e); }
        }

        // --- ACTION 4: View Details (Modal) ---
        async function viewBookingDetails(id) {
            const modal = document.getElementById('adminDetailsModal');
            const content = document.getElementById('adminModalContent');
            modal.classList.add('active');
            
            try {
                const res = await fetch(`php/api_get_booking_details.php?id=${id}`);
                const data = await res.json();
                
                let servicesHtml = '';
                if(data.booked_details && data.booked_details.length > 0) {
                    servicesHtml = `<ul style="list-style:none; padding:0; margin-top:10px; background:#f9f9f9; padding:15px; border-radius:8px;">`;
                    data.booked_details.forEach(s => {
                        servicesHtml += `<li style="display:flex; justify-content:space-between; margin-bottom:5px; font-size:0.9rem;">
                            <span>${s.service_name}</span>
                            <strong>₱${parseFloat(s.price).toLocaleString()}</strong>
                        </li>`;
                    });
                    servicesHtml += `</ul>`;
                } else {
                    servicesHtml = '<p style="color:#888; font-style:italic;">No add-ons selected.</p>';
                }

                content.innerHTML = `
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:20px;">
                        <div><small style="color:#888;">VENUE</small><br><strong>${data.venue_name}</strong></div>
                        <div><small style="color:#888;">DATE</small><br><strong>${data.event_date}</strong></div>
                        <div><small style="color:#888;">PACKAGE</small><br><strong>${data.package_name || 'Custom'}</strong></div>
                        <div><small style="color:#888;">CLIENT</small><br><strong>${data.client_name}</strong></div>
                    </div>
                    <h4 style="font-size:1rem; border-bottom:1px solid #eee; padding-bottom:5px;">Included Add-ons</h4>
                    ${servicesHtml}
                    <div style="text-align:right; margin-top:15px; font-size:1.2rem; font-weight:bold; color:var(--vino);">
                        Total: ₱${parseFloat(data.agreed_budget).toLocaleString()}
                    </div>
                `;
            } catch (e) {
                content.innerHTML = '<p style="color:red;">Error loading details.</p>';
            }
        }

        function closeAdminModal() {
            document.getElementById('adminDetailsModal').classList.remove('active');
        }

        function showToast(msg, type) {
            const box = document.createElement('div');
            box.style.cssText = `background:white; padding:15px; border-radius:5px; margin-top:10px; box-shadow:0 4px 10px rgba(0,0,0,0.1); border-left: 4px solid ${type === 'success' ? '#28a745' : '#dc3545'}`;
            box.textContent = msg;
            document.getElementById('toast-container').appendChild(box);
            setTimeout(() => box.remove(), 3000);
        }
    </script>
</body>
</html>