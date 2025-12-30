let packageData = {}; 
let userProfile = {};
let currentBasePrice = 0;
let currentPackageId = null;
let currentPackageName = "";
let currentEventType = "";
let customUserBudget = 0;
let finalBookingData = null;
let currentInclusions = []; // New: Stores the list of included services

const MIN_CUSTOM_BUDGET = 80000;
const DEFAULT_VENUE_PRICE = 0; 

document.addEventListener('DOMContentLoaded', () => {
    initBookingPage();
    createToastContainer();
});

async function initBookingPage() {
    try {
        const sessionRes = await fetch('php/api_session.php');
        const sessionData = await sessionRes.json();
        const welcomeMsg = document.getElementById('welcomeMsg');
        const sumClient = document.getElementById('sum-client');

        if(!sessionData.logged_in) {
            if(welcomeMsg) welcomeMsg.textContent = "Welcome, Guest!";
            if(sumClient) sumClient.textContent = "Guest";
        } else {
            userProfile = sessionData.user;
            if(sumClient) {
                sumClient.textContent = userProfile.full_name;
                sumClient.style.fontWeight = "bold";
                sumClient.style.color = "var(--vino)";
            }
            if(welcomeMsg) welcomeMsg.textContent = `Welcome back, ${userProfile.full_name}!`;
        }
    } catch (e) { console.error("Session Error:", e); }
    
    setupInputListeners();

    try {
        const dataRes = await fetch('php/api_booking_data.php');
        packageData = await dataRes.json();
        const typeSelect = document.getElementById('eventType');
        if(typeSelect && typeSelect.value) onEventTypeChange();
    } catch (e) { console.error("Data Error:", e); }
}

function setupInputListeners() {
    const dateInput = document.getElementById('eventDate');
    const sumDate = document.getElementById('sum-date');
    if(dateInput && sumDate) {
        dateInput.addEventListener('change', function() { sumDate.textContent = this.value ? this.value : '--'; });
    }
    const typeSelect = document.getElementById('eventType');
    const sumType = document.getElementById('sum-type');
    if(typeSelect && sumType) {
         typeSelect.addEventListener('change', function() {
             sumType.textContent = this.value ? this.value : '--';
             onEventTypeChange();
         });
    }
}

function onEventTypeChange() {
    const typeSelect = document.getElementById('eventType');
    currentEventType = typeSelect.value;
    const sumType = document.getElementById('sum-type');
    if(sumType) sumType.textContent = currentEventType;
    if(currentEventType) {
        document.getElementById('packageSelectionArea').classList.remove('hidden-section');
        renderPackages();
        renderAddons(); 
    }
}

function renderPackages() {
    const grid = document.getElementById('packagesGrid');
    grid.innerHTML = '';
    if (!packageData.packages) return;

    packageData.packages.forEach(pkg => {
        const isCustom = (pkg.package_id == 4 || pkg.package_name === "Custom Builder");
        const priceDisplay = isCustom ? 'Flexible' : '₱' + parseFloat(pkg.base_price).toLocaleString();
        let featuresHTML = '';
        
        if (isCustom) {
             featuresHTML = `<li><i class="fas fa-check"></i> Min ₱${(MIN_CUSTOM_BUDGET/1000)}k Budget</li><li><i class="fas fa-check"></i> Fully Customizable</li><li><i class="fas fa-check"></i> Choose Any Service</li>`;
        } else {
             const defs = packageData.package_definitions;
             if (defs && defs[pkg.package_id] && defs[pkg.package_id][currentEventType]) {
                const services = defs[pkg.package_id][currentEventType];
                featuresHTML = services.slice(0, 3).map(s => `<li><i class="fas fa-check"></i> ${s}</li>`).join('');
                if(services.length > 3) featuresHTML += `<li><i class="fas fa-plus-circle" style="color:var(--vino); opacity:0.7;"></i> ${services.length - 3} more...</li>`;
            } else {
                featuresHTML = `<li><i class="fas fa-check"></i> Standard Inclusions</li>`;
            }
        }

        const card = document.createElement('div');
        card.className = 'pkg-select-card'; 
        card.innerHTML = `
            <div class="pkg-card-header"><div class="pkg-title">${pkg.package_name}</div><div class="pkg-price">${priceDisplay}</div></div>
            <div class="pkg-card-body"><ul class="pkg-features">${featuresHTML}</ul></div>
            <div class="pkg-card-footer"><button class="btn btn-solid-vino select-btn" onclick="openBookingModal(${pkg.package_id}, '${pkg.package_name}', ${pkg.base_price})">${isCustom ? 'Build Now' : 'Select'}</button></div>
        `;
        grid.appendChild(card);
    });
}

function renderAddons() {
    const container = document.getElementById('addonsArea');
    if (!container || !packageData.addons) return;
    container.innerHTML = '';
    
    for (const [category, services] of Object.entries(packageData.addons)) {
        const validServices = services.filter(addon => addon.event_scope === 'All' || addon.event_scope === currentEventType);
        
        if (validServices.length > 0) {
            const groupDiv = document.createElement('div');
            groupDiv.className = 'addon-group';
            groupDiv.innerHTML = `<div class="addon-cat-title"><span>${category}</span><div class="line"></div></div>`;
            
            const gridContainer = document.createElement('div');
            gridContainer.className = 'addon-grid'; 
            gridContainer.style.display = 'grid'; 
            gridContainer.style.gridTemplateColumns = 'repeat(2, 1fr)'; 
            gridContainer.style.gap = '15px'; // Slightly tighter gap
            
            validServices.forEach(addon => {
                const isUnavailable = addon.status === 'Unavailable';
                
                // Styles for unavailable state
                const cardClass = isUnavailable ? 'addon-card unavailable' : 'addon-card';
                const disabledAttr = isUnavailable ? 'disabled' : '';
                const badgeHtml = isUnavailable ? '<span class="badge-unavailable">UNAVAILABLE</span>' : '';

                const item = document.createElement('label');
                item.className = cardClass;
                // Fix alignment: align-items: start allows multiline text to look better
                item.style.cssText = 'display:flex; align-items: flex-start; gap:12px; padding: 15px; border: 1px solid #e0e0e0; border-radius: 8px; transition: 0.2s;';
                
                item.innerHTML = `
                    <input type="checkbox" class="addon-checkbox" value="${addon.service_id}" 
                        data-price="${addon.price}" data-name="${addon.service_name}" 
                        onchange="updateModalTotal()" 
                        style="margin-top: 4px; accent-color:var(--vino);" ${disabledAttr}>
                    
                    <div class="addon-info" style="flex:1;">
                        <div style="font-weight:600; font-size:0.95rem; line-height: 1.3; margin-bottom: 4px;">
                            ${addon.service_name}
                            ${badgeHtml} 
                        </div>
                        <div style="font-weight:700; font-size:0.9rem;">+₱${parseFloat(addon.price).toLocaleString()}</div>
                    </div>
                `;
                gridContainer.appendChild(item);
            });
            groupDiv.appendChild(gridContainer);
            container.appendChild(groupDiv);
        }
    }
}

function openBookingModal(pkgId, pkgName, basePrice) {
    const eventDate = document.getElementById('eventDate').value;
    if (!currentEventType || !eventDate) { showToast("Please ensure Event Type and Date are selected!", 'error'); return; }
    
    currentPackageId = pkgId; 
    currentPackageName = pkgName; 
    currentBasePrice = parseFloat(basePrice); 
    customUserBudget = 0; 
    currentInclusions = []; // Reset inclusions

    document.getElementById('modalPkgName').textContent = pkgName;
    const basePriceEl = document.getElementById('sidebarBasePrice');
    const modalContainer = document.getElementById('modalBox'); 
    const list = document.getElementById('modalInclusions');
    const summaryList = document.getElementById('sidebarAddonsList');

    if (pkgId == 4) {
        // --- CUSTOM PACKAGE LOGIC ---
        basePriceEl.innerHTML = `<div class="budget-input-wrapper custom-entry-anim"><span class="currency-prefix">₱</span><input type="number" id="budgetInput" class="budget-input-styled" placeholder="0.00" oninput="handleBudgetInput(this.value)"></div><div id="budgetLeftDisplay" class="budget-hint">Enter Min. ₱${MIN_CUSTOM_BUDGET.toLocaleString()} to Start</div>`;
        list.innerHTML = '<li><i class="fas fa-magic"></i> Set budget to unlock add-ons!</li>';
        modalContainer.classList.add('expanded');
        document.getElementById('addonsArea').classList.add('addons-locked');
        document.querySelector('.btn-expand-addons').style.display = 'none';
        if(document.getElementById('addonArrow')) document.getElementById('addonArrow').style.transform = 'rotate(180deg)';
        
        // Clear Summary
        summaryList.innerHTML = `<div class="empty-addons-msg"><i class="fas fa-plus-circle" style="font-size: 20px; margin-bottom: 10px; display:block; opacity:0.5;"></i>No add-ons selected yet.</div>`;

    } else {
        // --- STANDARD PACKAGE LOGIC ---
        basePriceEl.textContent = '₱' + currentBasePrice.toLocaleString();
        modalContainer.classList.remove('expanded');
        document.getElementById('addonsArea').classList.remove('addons-locked');
        document.querySelector('.btn-expand-addons').style.display = 'flex';
        if(document.getElementById('addonArrow')) document.getElementById('addonArrow').style.transform = 'rotate(0deg)';

        // Fetch Inclusions
        if (packageData.package_definitions && packageData.package_definitions[pkgId] && packageData.package_definitions[pkgId][currentEventType]) {
            currentInclusions = packageData.package_definitions[pkgId][currentEventType];
            
            // Render Left List
            list.innerHTML = currentInclusions.map(item => `<li><i class="fas fa-check"></i> ${item}</li>`).join('');
            
            // Render Right Sidebar Summary (Inclusions)
            let inclusionsHtml = '<div style="margin-bottom:15px; border-bottom:1px dashed #ddd; padding-bottom:10px;">';
            inclusionsHtml += '<div style="font-size:0.75rem; color:#999; text-transform:uppercase; margin-bottom:8px;">Included in Package:</div>';
            currentInclusions.forEach(item => {
                inclusionsHtml += `
                    <div style="display:flex; justify-content:space-between; font-size:0.85rem; color:#555; margin-bottom:4px;">
                        <span>• ${item}</span>
                        <span style="color:#28a745; font-size:0.75rem; background:#e6f4ea; padding:1px 6px; border-radius:4px;">INCLUDED</span>
                    </div>`;
            });
            inclusionsHtml += '</div>';
            
            // Set up summary area with a specific container for add-ons
            summaryList.innerHTML = inclusionsHtml + `<div id="summaryAddonContainer"></div>`;
        } else {
            list.innerHTML = '<li>Standard inclusions applied.</li>';
            summaryList.innerHTML = `<div id="summaryAddonContainer"></div>`;
        }
    }

    document.getElementById('modalVenueText').value = "";
    
    renderAddons();
    document.querySelectorAll('.addon-checkbox').forEach(cb => cb.checked = false);
    document.getElementById('bookingModal').classList.add('active');
    updateModalTotal();
}

function expandModal() {
    const modalContainer = document.getElementById('modalBox');
    modalContainer.classList.toggle('expanded');
    const arrow = document.getElementById('addonArrow');
    if(arrow) arrow.style.transform = modalContainer.classList.contains('expanded') ? "rotate(180deg)" : "rotate(0deg)";
}

function handleBudgetInput(val) {
    customUserBudget = parseFloat(val) || 0;
    const addonsArea = document.getElementById('addonsArea');
    if (customUserBudget >= MIN_CUSTOM_BUDGET) addonsArea.classList.remove('addons-locked'); else addonsArea.classList.add('addons-locked');
    updateModalTotal();
}

function updateModalTotal() {
    let currentTotal = (currentPackageId == 4) ? 0 : currentBasePrice;
    
    // Target the specific container for add-ons to avoid overwriting inclusions (if Standard package)
    const containerTarget = (currentPackageId == 4) ? document.getElementById('sidebarAddonsList') : document.getElementById('summaryAddonContainer');
    
    if(!containerTarget) return;

    const addons = document.querySelectorAll('.addon-checkbox:checked');
    containerTarget.innerHTML = ''; 

    if(addons.length > 0) {
        addons.forEach(addon => {
            const price = parseFloat(addon.dataset.price);
            currentTotal += price;
            
            const div = document.createElement('div');
            div.className = 'addon-summary-item';
            div.innerHTML = `
                <div class="addon-summary-info"><span class="addon-name-text">${addon.dataset.name}</span></div>
                <div class="addon-price-text">+₱${price.toLocaleString()}</div>
            `;
            containerTarget.appendChild(div);
        });
    } else {
        if(currentPackageId == 4) {
             containerTarget.innerHTML = `<div class="empty-addons-msg"><i class="fas fa-plus-circle" style="font-size: 20px; margin-bottom: 10px; display:block; opacity:0.5;"></i>No add-ons selected yet.</div>`;
        }
    }

    const formattedTotal = '₱' + currentTotal.toLocaleString();
    document.getElementById('sidebarTotal').textContent = formattedTotal;
    if(document.getElementById('footerTotal')) document.getElementById('footerTotal').textContent = formattedTotal;

    if(currentPackageId == 4) {
        const remaining = customUserBudget - currentTotal;
        const budgetHint = document.getElementById('budgetLeftDisplay');
        if(customUserBudget >= MIN_CUSTOM_BUDGET) {
            budgetHint.innerHTML = remaining >= 0 ? `Remaining: <span style="color:#28a745">₱${remaining.toLocaleString()}</span>` : `Over Budget by: <span style="color:#d9534f">₱${Math.abs(remaining).toLocaleString()}</span>`;
            document.querySelectorAll('.addon-checkbox').forEach(cb => {
                if(!cb.checked && parseFloat(cb.dataset.price) > remaining) { cb.disabled = true; cb.closest('.addon-card').classList.add('disabled-item'); } 
                else { cb.disabled = false; cb.closest('.addon-card').classList.remove('disabled-item'); }
            });
        } else { budgetHint.textContent = `Min. ₱${MIN_CUSTOM_BUDGET.toLocaleString()} to unlock`; }
    }
}

function closeModal(id) { document.getElementById(id || 'bookingModal').classList.remove('active'); }

function applyModalSelection() {
    if (currentPackageId == 4 && customUserBudget < MIN_CUSTOM_BUDGET) { showToast(`Minimum budget of ₱${MIN_CUSTOM_BUDGET.toLocaleString()} required.`, 'error'); return; }
    
    const venueName = document.getElementById('modalVenueText').value.trim();
    if (!venueName) { showToast("Please enter your venue address.", 'error'); return; }

    const addons = Array.from(document.querySelectorAll('.addon-checkbox:checked'));
    const totalStr = document.getElementById('sidebarTotal').textContent;
    const totalVal = parseFloat(totalStr.replace('₱', '').replace(/,/g, ''));
    if(currentPackageId == 4 && totalVal > customUserBudget) { showToast("Selection exceeds your set budget.", 'warning'); return; }

    finalBookingData = {
        package_id: currentPackageId, 
        package_name: currentPackageName,
        event_type: currentEventType, 
        event_date: document.getElementById('eventDate').value,
        venue_id: null,
        venue_name: venueName, 
        venue_price: DEFAULT_VENUE_PRICE,
        total_budget: totalVal,
        services: addons.map(cb => ({ id: cb.value, name: cb.dataset.name, price: parseFloat(cb.dataset.price) })),
        service_ids: addons.map(cb => cb.value),
        inclusions: currentInclusions // Store inclusions for the receipt
    };

    let displayPackage = currentPackageName;
    if (addons.length > 0 && currentPackageId != 4) displayPackage += " + Addons";
    document.getElementById('sum-package').textContent = displayPackage;
    document.getElementById('sum-total').textContent = totalStr;
    closeModal('bookingModal');
    document.getElementById('mainConfirmBtn').scrollIntoView({ behavior: 'smooth' });
    showToast("Selection Applied! Review and Confirm.", 'success');
}

// --- REVISED REVIEW MODAL WITH INCLUSIONS ---
function openReviewModal() {
    if (!finalBookingData) { showToast("Please select a package first.", 'error'); return; }
    const content = document.getElementById('receiptContent');
    const baseP = currentPackageId == 4 ? 0 : currentBasePrice;

    // Build Inclusions HTML
    let inclusionsHTML = '';
    if (finalBookingData.inclusions && finalBookingData.inclusions.length > 0) {
        finalBookingData.inclusions.forEach(item => {
            inclusionsHTML += `
            <div class="review-row" style="padding-left: 20px; font-size: 0.85rem; color: #666; border-bottom: 1px solid #f9f9f9;">
                <span>• ${item}</span>
                <span style="font-style:italic; font-size:0.75rem;">Included</span>
            </div>`;
        });
    }

    let breakdownHTML = `
        <div class="review-row main-item">
            <span>${finalBookingData.package_name}</span>
            <span>₱${baseP.toLocaleString()}</span>
        </div>
        ${inclusionsHTML} 
        <div class="review-row">
            <span style="display:flex; flex-direction:column;">
                <span>Venue Location</span>
                <span style="font-size:0.8rem; font-weight:400; color:#888;">${finalBookingData.venue_name}</span>
            </span>
            <span>-</span>
        </div>`;

    finalBookingData.services.forEach(svc => {
        breakdownHTML += `
        <div class="review-row">
            <span>+ ${svc.name}</span>
            <span>₱${svc.price.toLocaleString()}</span>
        </div>`;
    });

    const html = `
        <div class="review-layout">
            <div class="review-header">
                <h3>Final Review</h3>
                <div class="divider-line"></div>
                <p>Booking Summary</p>
            </div>

            <div class="review-details-grid">
                <div class="review-detail-item"><span>Event Date</span><strong>${finalBookingData.event_date}</strong></div>
                <div class="review-detail-item"><span>Event Type</span><strong>${finalBookingData.event_type}</strong></div>
                <div class="review-detail-item"><span>Client</span><strong>${userProfile.full_name || 'Guest'}</strong></div>
                <div class="review-detail-item"><span>Status</span><strong style="color:#f0ad4e;">Pending Approval</strong></div>
            </div>

            <div class="review-breakdown-list">
                ${breakdownHTML}
            </div>

            <div class="review-total-box">
                <span>Estimated Total</span>
                <span>₱${finalBookingData.total_budget.toLocaleString()}</span>
            </div>
        </div>
    `;

    content.innerHTML = html;
    document.getElementById('reviewTitle').textContent = "Review Request"; 
    document.getElementById('reviewFooter').innerHTML = `
        <button class="btn btn-outline-light no-print" onclick="closeModal('reviewModal')" style="color:#555; border-color:#ccc; padding: 10px 25px;">Back</button>
        <button class="btn btn-solid-vino no-print" onclick="processBookingAndPrint()" style="padding: 10px 30px;">Submit Request</button>
    `;
    document.getElementById('reviewModal').classList.add('active');
}

// --- UPDATED REQUEST/SLIP LOGIC ---
async function processBookingAndPrint() {
    try {
        const payload = { ...finalBookingData, services: finalBookingData.service_ids };
        const res = await fetch('php/api_confirm_booking.php', {
            method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload)
        });
        const result = await res.json();

        if(result.success) {
            const bookingID = result.booking_id;
            const content = document.getElementById('receiptContent');
            const today = new Date().toLocaleString();
            const baseP = currentPackageId == 4 ? 0 : currentBasePrice;

            // Generate Inclusions Rows for Slip
            let inclusionsRows = '';
            if (finalBookingData.inclusions && finalBookingData.inclusions.length > 0) {
                finalBookingData.inclusions.forEach(item => {
                    inclusionsRows += `<tr><td style="padding-left:20px; font-size:0.85rem; color:#666;">• ${item}</td><td style="font-size:0.85rem; color:#888;">Included</td></tr>`;
                });
            }

            let rows = `<tr><td>${finalBookingData.package_name}</td><td>₱${baseP.toLocaleString()}</td></tr>`;
            rows += inclusionsRows;
            rows += `<tr><td>Venue: ${finalBookingData.venue_name}</td><td>-</td></tr>`;
            
            finalBookingData.services.forEach(svc => {
                rows += `<tr><td>${svc.name}</td><td>₱${svc.price.toLocaleString()}</td></tr>`;
            });

            // "PENDING" STAMP and "RESERVATION REQUEST" TITLE
            const invoiceHTML = `
                <div class="web-receipt">
                    <div class="success-stamp" style="border-color: #f0ad4e; color: #f0ad4e;">PENDING</div>
                    <div class="wr-header">
                        <img src="assets/img/EOS_logo.png" class="wr-logo">
                        <div class="wr-title">Reservation Request</div>
                        <div class="wr-subtitle">EOS Event Management Systems</div>
                    </div>
                    
                    <div class="wr-body">
                        <div class="wr-meta-grid">
                            <div class="wr-meta-item"><h4>Reference No.</h4><p>${bookingID}</p></div>
                            <div class="wr-meta-item"><h4>Date Requested</h4><p>${today.split(',')[0]}</p></div>
                            <div class="wr-meta-item"><h4>Client</h4><p>${userProfile.full_name}</p></div>
                        </div>

                        <div style="background: #fff3cd; color: #856404; padding: 10px; margin-bottom: 20px; font-size: 0.9rem; text-align: center; border-radius: 4px;">
                            <strong>Note:</strong> This is a request only. Please wait for Admin approval before making any payments.
                        </div>

                        <table class="wr-table">
                            <thead><tr><th>Description</th><th style="text-align:right;">Estimated Cost</th></tr></thead>
                            <tbody>
                                ${rows}
                                <tr class="total-row"><td>ESTIMATED TOTAL</td><td>₱${finalBookingData.total_budget.toLocaleString()}</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="wr-footer">
                        <p>Your request has been sent to our team.</p>
                        <p>You can track the status in "My Bookings".</p>
                    </div>
                </div>
            `;

            content.innerHTML = invoiceHTML;
            document.getElementById('reviewTitle').textContent = "Request Sent!";
            
            document.getElementById('reviewFooter').innerHTML = `
                <button class="btn btn-outline-light no-print" onclick="window.location.href='my_bookings.html'">Go to My Bookings</button>
                <button class="btn btn-solid-vino no-print" onclick="window.print()"><i class="fas fa-print"></i> Print Slip</button>
            `;
            showToast("Request Submitted Successfully!", 'success');
        } else {
            showToast("Error: " + (result.message || "Unknown error"), 'error');
        }
    } catch (e) { console.error(e); showToast("System error occurred.", 'error'); }
}

function createToastContainer() {
    if (!document.getElementById('toast-container')) {
        const container = document.createElement('div');
        container.id = 'toast-container';
        document.body.appendChild(container);
    }
}

function showToast(message, type = 'info') {
    const container = document.getElementById('toast-container');
    if(!container) return;

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    
    let iconClass = 'fa-info-circle';
    if(type === 'success') iconClass = 'fa-check-circle';
    if(type === 'error') iconClass = 'fa-times-circle';
    if(type === 'warning') iconClass = 'fa-exclamation-triangle';

    toast.innerHTML = `<div class="toast-content"><i class="fas ${iconClass}"></i><span>${message}</span></div><div class="toast-progress"></div>`;
    container.appendChild(toast);
    requestAnimationFrame(() => toast.classList.add('show'));
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => { if(container.contains(toast)) container.removeChild(toast); }, 300);
    }, 4000);
}