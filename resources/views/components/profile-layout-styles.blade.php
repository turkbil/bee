@push('styles')
<style>
    /* Glassmorphism Profile Card */
    .profile-card {
        background: rgba(255, 255, 255, 0.7) !important;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: none;
        transition: all 0.3s ease;
    }
    .profile-card:hover {
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        transform: translateY(-2px);
    }
    .dark .profile-card {
        background: rgba(255, 255, 255, 0.05) !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }
    .dark .profile-card:hover {
        background: rgba(255, 255, 255, 0.08) !important;
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
    }

    /* Glassmorphism Profile Sidebar */
    .profile-sidebar {
        background: rgba(255, 255, 255, 0.7) !important;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }
    .dark .profile-sidebar {
        background: rgba(255, 255, 255, 0.05) !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }

    /* Glass Input Fields */
    .profile-card input,
    .profile-card select,
    .profile-card textarea {
        background: rgba(255, 255, 255, 0.5) !important;
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        border: 1px solid rgba(0, 0, 0, 0.1) !important;
        transition: all 0.3s ease;
    }
    .profile-card input:focus,
    .profile-card select:focus,
    .profile-card textarea:focus {
        background: rgba(255, 255, 255, 0.8) !important;
        border-color: rgba(59, 130, 246, 0.5) !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .dark .profile-card input,
    .dark .profile-card select,
    .dark .profile-card textarea {
        background: rgba(255, 255, 255, 0.05) !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
    }
    .dark .profile-card input:focus,
    .dark .profile-card select:focus,
    .dark .profile-card textarea:focus {
        background: rgba(255, 255, 255, 0.1) !important;
        border-color: rgba(96, 165, 250, 0.5) !important;
        box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.1);
    }

    /* Glass Buttons */
    .profile-card button[type="submit"] {
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        transition: all 0.3s ease;
    }
    .profile-card button[type="submit"]:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    /* Sidebar Navigation Items */
    .profile-sidebar-item {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        color: rgb(75 85 99);
        text-decoration: none;
        border-radius: 8px;
        margin-bottom: 4px;
    }
    .profile-sidebar-item:hover {
        background: rgba(59, 130, 246, 0.1);
        color: rgb(59 130 246);
        transform: translateX(4px);
    }
    .dark .profile-sidebar-item {
        border-bottom-color: rgba(255, 255, 255, 0.05);
        color: rgb(156 163 175);
    }
    .dark .profile-sidebar-item:hover {
        background: rgba(96, 165, 250, 0.1);
        color: rgb(96 165 250);
    }
    .profile-sidebar-item.danger:hover {
        background: rgba(239, 68, 68, 0.1);
        color: rgb(239 68 68);
    }
    .dark .profile-sidebar-item.danger:hover {
        background: rgba(248, 113, 113, 0.1);
        color: rgb(248 113 113);
    }

    /* Header Glass Effect */
    .profile-header-glass {
        background: rgba(255, 255, 255, 0.7) !important;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
    }
    .dark .profile-header-glass {
        background: rgba(255, 255, 255, 0.05) !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
    }
</style>
@endpush