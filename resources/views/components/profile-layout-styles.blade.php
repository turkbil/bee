@push('styles')
<style>
    .profile-card {
        border: 1px solid rgb(229 231 235);
    }
    .dark .profile-card {
        border-color: rgb(55 65 81);
    }
    .profile-sidebar-item {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        border-bottom: 1px solid rgb(229 231 235);
        transition: background-color 0.2s ease, color 0.2s ease;
        color: rgb(75 85 99);
        text-decoration: none;
    }
    .profile-sidebar-item:hover {
        background-color: rgb(249 250 251);
        color: rgb(59 130 246);
    }
    .dark .profile-sidebar-item {
        border-bottom-color: rgb(55 65 81);
        color: rgb(156 163 175);
    }
    .dark .profile-sidebar-item:hover {
        background-color: rgb(31 41 55);
        color: rgb(96 165 250);
    }
    .profile-sidebar-item.danger:hover {
        background-color: rgb(254 242 242);
        color: rgb(239 68 68);
    }
    .dark .profile-sidebar-item.danger:hover {
        background-color: rgb(69 10 10);
        color: rgb(248 113 113);
    }
</style>
@endpush