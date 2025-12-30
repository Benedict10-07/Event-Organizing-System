<?php
session_start();
// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services | EOS Admin</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        /* Admin Layout Overrides */
        body { background: #f4f6f9; padding-top: 0; display: block; }
        .admin-header { background: #2c3e50; padding: 20px 4%; display: flex; justify-content: space-between; align-items: center; color: white; }
        .admin-brand { font-family: 'Playfair Display', serif; font-size: 1.5rem; font-weight: 700; }
        
        .container-admin { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        
        /* Tabs */
        .admin-tabs { display: flex; gap: 20px; margin-bottom: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
        .tab-btn { background: none; border: none; font-size: 1.1rem; font-weight: 600; color: #888; cursor: pointer; padding: 10px 20px; transition: 0.3s; }
        .tab-btn:hover { color: var(--vino); }
        .tab-btn.active { color: var(--vino); border-bottom: 3px solid var(--vino); margin-bottom: -12px; }

        /* Card Panels */
        .card-panel { background: white; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); padding: 25px; animation: fadeIn 0.5s; }
        .panel-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        
        /* Table */
        .admin-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .admin-table th { text-align: left; background: #f8f9fa; padding: 15px; font-size: 0.9rem; color: #555; font-weight: 700; border-bottom: 2px solid #eee; }
        .admin-table td { padding: 15px; border-bottom: 1px solid #eee; font-size: 0.95rem; vertical-align: middle; }
        .admin-table tr:hover { background: #fafafa; }
        
        /* Buttons & Badges */
        .btn-icon { background: #f0f0f0; border: none; cursor: pointer; width: 35px; height: 35px; border-radius: 50%; margin-right: 5px; transition: 0.2s; display: inline-flex; align-items: center; justify-content: center; }
        .btn-icon:hover { transform: translateY(-2px); }
        .edit-icon { color: #007bff; } .edit-icon:hover { background: #e3f2fd; }
        .del-icon { color: #dc3545; } .del-icon:hover { background: #f8d7da; }
        
        .badge { padding: 5px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
        .bg-avail { background: #d4edda; color: #155724; }
        .bg-unavail { background: #f8d7da; color: #721c24; }

        /* --- MODAL SYSTEM CSS (This was missing!) --- */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5); z-index: 2000;
            display: none; /* Hidden by default */
            align-items: center; justify-content: center;
            opacity: 0; transition: opacity 0.3s ease;
        }
        .modal-overlay.active { display: flex; opacity: 1; }

        .modal-container {
            background: white; width: 90%; max-width: 500px;
            border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            transform: translateY(20px); transition: transform 0.3s ease;
            overflow: hidden; display: flex; flex-direction: column;
        }
        .modal-overlay.active .modal-container { transform: translateY(0); }

        .modal-header {
            padding: 15px 25px; border-bottom: 1px solid #eee;
            display: flex; justify-content: space-between; align-items: center;
            background: #fff;
        }
        .modal-header h2 { margin: 0; font-size: 1.25rem; color: #333; }
        .close-modal { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #999; }
        .close-modal:hover { color: #333; }

        .modal-body { padding: 25px; overflow-y: auto; max-height: 70vh; }

        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; color: #444; font-size: 0.9rem; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem; }
        .form-control:focus { border-color: var(--vino); outline: none; }
        
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

    <div class="admin-header">
        <div class="admin-brand">EOS Admin</div>
        <div style="display: flex; gap: 20px; align-items: center;">
            <a href="admin_dashboard.php" style="color:white; opacity:0.8; text-decoration:none;">Dashboard</a>
            <a href="admin_services.php" style="color:white; font-weight:bold; text-decoration:underline;">Services & Vendors</a>
            <span style="border-left:1px solid rgba(255,255,255,0.3); padding-left:20px; color:rgba(255,255,255,0.8);">Welcome, Admin</span>
            <a href="php/logout.php" class="btn btn-outline-light" style="padding: 5px 15px; font-size: 0.85rem; border-color: rgba(255,255,255,0.3);">Logout</a>
        </div>
    </div>

    <div class="container-admin">
        <div class="admin-tabs">
            <button class="tab-btn active" onclick="switchTab('services')"><i class="fas fa-concierge-bell"></i> Manage Services</button>
            <button class="tab-btn" onclick="switchTab('vendors')"><i class="fas fa-store"></i> Manage Vendors</button>
        </div>

        <div id="servicesPanel" class="card-panel">
            <div class="panel-header">
                <h3><i class="fas fa-list"></i> Service Catalog</h3>
                <button class="btn btn-solid-vino" onclick="openAddServiceModal()"><i class="fas fa-plus"></i> Add New Service</button>
            </div>
            <div style="overflow-x:auto;">
                <table class="admin-table">
                    <thead><tr><th>Service Name</th><th>Category</th><th>Price</th><th>Vendor</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody id="servicesBody"><tr><td colspan="6" style="text-align:center;">Loading data...</td></tr></tbody>
                </table>
            </div>
        </div>

        <div id="vendorsPanel" class="card-panel" style="display:none;">
            <div class="panel-header">
                <h3><i class="fas fa-users"></i> Registered Vendors</h3>
                <button class="btn btn-solid-vino" onclick="openAddVendorModal()"><i class="fas fa-plus"></i> Add New Vendor</button>
            </div>
            <table class="admin-table">
                <thead><tr><th>Vendor Name</th><th>Category</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody id="vendorsBody"></tbody>
            </table>
        </div>
    </div>

    <div id="serviceModal" class="modal-overlay">
        <div class="modal-container" style="max-width: 500px;">
            <div class="modal-header">
                <h2 id="svcModalTitle">Add Service</h2>
                <button class="close-modal" onclick="closeModal('serviceModal')">&times;</button>
            </div>
            <div class="modal-body">
                <form id="serviceForm">
                    <input type="hidden" id="svcId">
                    <input type="hidden" id="svcAction" value="add_service">
                    
                    <div class="form-group">
                        <label>Service Name</label>
                        <input type="text" id="svcName" class="form-control" required placeholder="e.g. 3-Layer Cake">
                    </div>
                    
                    <div class="form-group" id="svcCatGroup">
                        <label>Category</label>
                        <select id="svcCategory" class="form-control">
                            <option value="Food">Food & Catering</option>
                            <option value="Styling">Styling & Decor</option>
                            <option value="Technical">Lights & Sound</option>
                            <option value="Media">Photo & Video</option>
                            <option value="Entertainment">Entertainment</option>
                            <option value="Rentals">Furniture Rentals</option>
                            <option value="Logistics">Transpo & Logistics</option>
                            <option value="Planning">Planning & Coordination</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Price (₱)</label>
                        <input type="number" id="svcPrice" class="form-control" required placeholder="0.00">
                    </div>

                    <div class="form-group" id="svcVendorGroup">
                        <label>Vendor Provider</label>
                        <select id="svcVendor" class="form-control" required><option>Loading vendors...</option></select>
                    </div>

                    <div class="form-group" id="svcScopeGroup">
                        <label>Event Scope</label>
                        <select id="svcScope" class="form-control">
                            <option value="All">Available for All Events</option>
                            <option value="Wedding">Wedding Only</option>
                            <option value="Birthday">Birthday Only</option>
                            <option value="Corporate">Corporate Only</option>
                            <option value="Debut">Debut Only</option>
                        </select>
                    </div>

                    <div class="form-group" id="svcStatusGroup" style="display:none; background:#f9f9f9; padding:10px; border-radius:5px;">
                        <label style="color:var(--vino);">Availability Status</label>
                        <select id="svcStatus" class="form-control">
                            <option value="Available">Available</option>
                            <option value="Unavailable">Unavailable / Fully Booked</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-solid-vino full-width" style="margin-top:10px;">Save Service</button>
                </form>
            </div>
        </div>
    </div>

    <div id="vendorModal" class="modal-overlay">
        <div class="modal-container" style="max-width: 450px;">
            <div class="modal-header">
                <h2>Add Vendor</h2>
                <button class="close-modal" onclick="closeModal('vendorModal')">&times;</button>
            </div>
            <div class="modal-body">
                <form id="vendorForm">
                    <input type="hidden" name="action" value="add_vendor">
                    <div class="form-group">
                        <label>Vendor Name</label>
                        <input type="text" name="name" class="form-control" required placeholder="Company Name">
                    </div>
                    <div class="form-group">
                        <label>Specialty Category</label>
                        <select name="category" class="form-control">
                            <option value="Food">Food</option>
                            <option value="Styling">Styling</option>
                            <option value="Technical">Technical</option>
                            <option value="Media">Media</option>
                            <option value="Entertainment">Entertainment</option>
                            <option value="Rentals">Rentals</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-solid-vino full-width">Add Vendor</button>
                </form>
            </div>
        </div>
    </div>

    <div id="deleteModal" class="modal-overlay">
        <div class="modal-container" style="max-width: 400px; text-align:center;">
            <div class="modal-body" style="padding-top:40px;">
                <i class="fas fa-exclamation-circle" style="font-size:3rem; color:#dc3545; margin-bottom:20px;"></i>
                <h3>Are you sure?</h3>
                <p style="color:#666; margin-bottom:30px;">You are about to permanently delete this item. This action cannot be undone.</p>
                <input type="hidden" id="deleteTargetId">
                <input type="hidden" id="deleteTargetType"> <div style="display:flex; gap:10px; justify-content:center;">
                    <button class="btn btn-outline-light" style="color:#555; border-color:#ccc;" onclick="closeModal('deleteModal')">Cancel</button>
                    <button class="btn btn-solid-vino" style="background:#dc3545;" onclick="confirmDelete()">Yes, Delete it</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let globalData = { services: [], vendors: [] };

        document.addEventListener('DOMContentLoaded', loadData);

        // --- 1. DATA LOADING ---
        async function loadData() {
            try {
                const res = await fetch('php/api_admin_services.php?action=get_all');
                const result = await res.json();
                if(result.success) {
                    globalData = result.data;
                    renderServices();
                    renderVendors();
                    populateVendorSelect();
                } else {
                    alert("Failed to load data.");
                }
            } catch(e) { console.error(e); }
        }

        // --- 2. RENDERING ---
        function renderServices() {
            const tbody = document.getElementById('servicesBody');
            if(globalData.services.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding:20px;">No services found. Add one!</td></tr>';
                return;
            }
            tbody.innerHTML = globalData.services.map(s => `
                <tr>
                    <td><strong>${s.service_name}</strong></td>
                    <td>${s.category}</td>
                    <td>₱${parseFloat(s.price).toLocaleString()}</td>
                    <td>${s.vendor_name || '<span style="color:#999;">Internal</span>'}</td>
                    <td><span class="badge ${s.status === 'Available' ? 'bg-avail' : 'bg-unavail'}">${s.status || 'Available'}</span></td>
                    <td>
                        <button class="btn-icon edit-icon" title="Edit" onclick="openEditService(${s.service_id})"><i class="fas fa-edit"></i></button>
                        <button class="btn-icon del-icon" title="Delete" onclick="askDelete('service', ${s.service_id})"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `).join('');
        }

        function renderVendors() {
            const tbody = document.getElementById('vendorsBody');
            tbody.innerHTML = globalData.vendors.map(v => `
                <tr>
                    <td><strong>${v.vendor_name}</strong></td>
                    <td>${v.category}</td>
                    <td><span class="badge bg-avail">Active</span></td>
                    <td><button class="btn-icon del-icon" onclick="askDelete('vendor', ${v.vendor_id})"><i class="fas fa-trash"></i></button></td>
                </tr>
            `).join('');
        }

        function populateVendorSelect() {
            const sel = document.getElementById('svcVendor');
            sel.innerHTML = globalData.vendors.map(v => `<option value="${v.vendor_id}">${v.vendor_name}</option>`).join('');
        }

        // --- 3. ADD/EDIT SERVICE LOGIC ---
        function openAddServiceModal() {
            document.getElementById('svcModalTitle').textContent = "Add New Service";
            document.getElementById('svcAction').value = "add_service";
            document.getElementById('serviceForm').reset();
            
            // Show fields needed for creation
            document.getElementById('svcCatGroup').style.display = 'block';
            document.getElementById('svcVendorGroup').style.display = 'block';
            document.getElementById('svcScopeGroup').style.display = 'block';
            document.getElementById('svcStatusGroup').style.display = 'none'; // Hide status on add
            
            document.getElementById('serviceModal').classList.add('active');
        }

        function openEditService(id) {
            const svc = globalData.services.find(s => s.service_id == id);
            if(!svc) return;

            document.getElementById('svcModalTitle').textContent = "Edit Service";
            document.getElementById('svcAction').value = "update_service";
            document.getElementById('svcId').value = svc.service_id;
            
            // Fill Data
            document.getElementById('svcName').value = svc.service_name;
            document.getElementById('svcPrice').value = svc.price;
            
            // Hide immutable fields
            document.getElementById('svcCatGroup').style.display = 'none';
            document.getElementById('svcVendorGroup').style.display = 'none';
            document.getElementById('svcScopeGroup').style.display = 'none';
            
            // Show Status
            document.getElementById('svcStatusGroup').style.display = 'block';
            document.getElementById('svcStatus').value = svc.status || 'Available';

            document.getElementById('serviceModal').classList.add('active');
        }

        document.getElementById('serviceForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData();
            const action = document.getElementById('svcAction').value;
            formData.append('action', action);
            
            if(action === 'add_service') {
                formData.append('name', document.getElementById('svcName').value);
                formData.append('category', document.getElementById('svcCategory').value);
                formData.append('price', document.getElementById('svcPrice').value);
                formData.append('vendor_id', document.getElementById('svcVendor').value);
                formData.append('scope', document.getElementById('svcScope').value);
            } else {
                // Update
                formData.append('service_id', document.getElementById('svcId').value);
                formData.append('name', document.getElementById('svcName').value);
                formData.append('price', document.getElementById('svcPrice').value);
                formData.append('status', document.getElementById('svcStatus').value);
            }

            await submitAPI(formData);
            closeModal('serviceModal');
        });

        // --- 4. VENDOR LOGIC ---
        function openAddVendorModal() {
            document.getElementById('vendorModal').classList.add('active');
        }

        document.getElementById('vendorForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            await submitAPI(formData);
            closeModal('vendorModal');
        });

        // --- 5. DELETE MODAL LOGIC ---
        function askDelete(type, id) {
            document.getElementById('deleteTargetType').value = type;
            document.getElementById('deleteTargetId').value = id;
            document.getElementById('deleteModal').classList.add('active');
        }

        async function confirmDelete() {
            const type = document.getElementById('deleteTargetType').value;
            const id = document.getElementById('deleteTargetId').value;
            const formData = new FormData();
            
            formData.append('action', type === 'service' ? 'delete_service' : 'delete_vendor');
            if(type === 'service') formData.append('service_id', id);
            else formData.append('vendor_id', id);

            await submitAPI(formData);
            closeModal('deleteModal');
        }

        // --- HELPER FUNCTIONS ---
        async function submitAPI(formData) {
            try {
                const res = await fetch('php/api_admin_services.php', { method: 'POST', body: formData });
                const result = await res.json();
                if(result.success) {
                    loadData(); // Refresh table
                } else {
                    alert("Error: " + (result.message || "Operation failed"));
                }
            } catch(e) { console.error(e); alert("System Error"); }
        }

        function switchTab(tab) {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            event.target.closest('.tab-btn').classList.add('active');
            
            document.getElementById('servicesPanel').style.display = tab === 'services' ? 'block' : 'none';
            document.getElementById('vendorsPanel').style.display = tab === 'vendors' ? 'block' : 'none';
        }

        function closeModal(id) { document.getElementById(id).classList.remove('active'); }
    </script>
</body>
</html>