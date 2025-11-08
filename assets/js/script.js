// 🌐 Common JavaScript for Hostel Management System
document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.querySelector('.sidebar');
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn, .md\\:hidden');
    const loginForms = document.querySelectorAll('form');
    const dashboardCards = document.querySelectorAll('.dashboard-card');
    const sidebarLinks = document.querySelectorAll('.sidebar a');

    /* 📱 Toggle Mobile Sidebar */
    if (mobileMenuBtn && sidebar) {
        mobileMenuBtn.addEventListener('click', () => {
            sidebar.classList.toggle('show');
        });
    }

    /* 🧾 Login Form Validation */
    loginForms.forEach(form => {
        form.addEventListener('submit', e => {
            const username = form.querySelector('#username, #student-id');
            const password = form.querySelector('#password');

            if (!username || !password) return;

            if (!username.value.trim()) {
                e.preventDefault();
                showToast('Please enter your username/student ID', 'error');
                return;
            }

            if (!password.value.trim()) {
                e.preventDefault();
                showToast('Please enter your password', 'error');
                return;
            }
        });
    });

    /* 🧭 Highlight Active Sidebar Link */
    const currentPage = window.location.pathname.split('/').pop();
    sidebarLinks.forEach(link => {
        if (link.getAttribute('href') === currentPage) {
            link.classList.add('bg-gray-700');
        }
    });

    /* 🧱 Clickable Dashboard Cards */
    dashboardCards.forEach(card => {
        card.addEventListener('click', e => {
            if (e.target.tagName !== 'A') {
                const link = card.querySelector('a');
                if (link) window.location.href = link.href;
            }
        });
    });
});

/* 🔔 Toast Notification */
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}
