// Sidebar toggle
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('toggle-sidebar');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (window.innerWidth <= 991) {
                document.body.classList.toggle('sidebar-open');
            } else {
                document.body.classList.toggle('sidebar-collapsed');
            }
        });
    }

    // Close sidebar on overlay click (mobile)
    document.addEventListener('click', function(e) {
        if (e.target.id === 'sidebar-overlay') {
            document.body.classList.remove('sidebar-open');
        }
    });

    // Auto-close sidebar on mobile navigation item click
    document.querySelectorAll('.sidebar-link').forEach(function(link) {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 991) {
                document.body.classList.remove('sidebar-open');
            }
        });
    });

    // Auto-dismiss alert notifications after 5 seconds
    document.querySelectorAll('.alert-dismissible').forEach(function(alert) {
        setTimeout(function() {
            alert.classList.remove('show');
            setTimeout(() => alert.remove(), 150);
        }, 5000);
    });
});

// Member search (AJAX)
function searchMembers(query, callback) {
    if (query.length < 2) {
        callback([]);
        return;
    }
    fetch('/members/api/search?q=' + encodeURIComponent(query))
        .then(r => r.json())
        .then(data => callback(data))
        .catch(() => callback([]));
}

// Get member dues (for payment form)
function getMemberDues(memberId) {
    return fetch('/payments/create?member_id=' + memberId, {
        headers: {'X-Requested-With': 'XMLHttpRequest'}
    }).then(r => r.json());
}

// Confirm delete/action dialogs
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

// Format currency
function formatCurrency(amount, symbol) {
    symbol = symbol || 'GH₵';
    return symbol + parseFloat(amount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

// Print receipt
function printReceipt() {
    window.print();
}
