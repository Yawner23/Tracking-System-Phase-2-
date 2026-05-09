{{-- resources/views/layouts/backend.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Tracking System') }}</title>

    {{-- Tailwind via Vite, not CDN --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>

@php
    $user = auth()->user();

    /**
     * Sidebar permission checker.
     *
     * This uses your pages.description values:
     * dashboard, users, roles, pages, privileges, role-privileges, branches, services, etc.
     */
    $can = function (string $page, string $permission = 'can_view') use ($user) {
        if (!$user) {
            return false;
        }

        if (!method_exists($user, 'hasPagePermission')) {
            return false;
        }

        return $user->hasPagePermission($page, $permission);
    };

    $rolesPrivilegeVisible =
        $can('roles') ||
        $can('pages') ||
        $can('privileges') ||
        $can('role-privileges');

    $backJobsVisible =
        $can('backjob-waybills') ||
        $can('returned-backjob-waybills');
@endphp

<body class="text-gray-800 bg-gray-100">
    {{-- Mobile Header --}}
    <header class="lg:hidden fixed top-0 left-0 right-0 bg-[#f8f4f3] border-b border-gray-300 z-50">
        <div class="flex items-center justify-between px-4 py-3">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                <img src="{{ asset('img/ICS2-01.png') }}" alt="Logo" class="h-8">
            </a>

            <button id="menuToggle" type="button" class="text-gray-900 text-2xl">
                <i class="ri-menu-line"></i>
            </button>
        </div>
    </header>

    {{-- Mobile Overlay --}}
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/40 z-40 hidden lg:hidden"></div>

    {{-- Sidebar --}}
    <aside
        id="sidebar"
        class="fixed left-0 top-0 w-64 h-full bg-[#f8f4f3] p-4 z-50 transform -translate-x-full transition-transform duration-300 lg:translate-x-0 lg:block overflow-y-auto"
    >
        {{-- Logo --}}
        <a href="{{ route('dashboard') }}" class="flex items-center pb-4 border-b border-b-gray-300">
            <img src="{{ asset('img/ICS2-01.png') }}" alt="Logo" class="h-10">
        </a>

        <ul class="mt-4 space-y-1">
            <span class="font-bold text-gray-400 text-xs">MENU</span>

            {{-- Dashboard --}}
            @if($can('dashboard'))
                <li>
                    <a href="{{ route('dashboard') }}"
                       class="flex font-semibold items-center py-2 px-4 rounded-md text-gray-900 hover:bg-gray-900 hover:text-white {{ request()->routeIs('dashboard') ? 'bg-gray-900 text-white' : '' }}">
                        <i class="mr-3 text-lg ri-home-2-line"></i>
                        <span class="text-sm">Dashboard</span>
                    </a>
                </li>
            @endif

            {{-- Roles / Privileges Dropdown --}}
            @if($rolesPrivilegeVisible)
                <li>
                    <button
                        type="button"
                        class="w-full flex font-semibold items-center justify-between py-2 px-4 rounded-md text-gray-900 hover:bg-gray-900 hover:text-white"
                        data-dropdown-toggle="rolesPrivilegesDropdown"
                    >
                        <span class="flex items-center">
                            <i class="mr-3 text-lg ri-shield-keyhole-line"></i>
                            <span class="text-sm">Roles & Privileges</span>
                        </span>

                        <i class="ri-arrow-down-s-line text-lg dropdown-icon"></i>
                    </button>

                    <ul id="rolesPrivilegesDropdown" class="mt-1 ml-6 space-y-1 hidden">
                        @if($can('roles'))
                            <li>
                                <a href="{{ route('roles.index') }}"
                                   class="flex font-semibold items-center py-2 px-4 rounded-md text-gray-900 hover:bg-gray-900 hover:text-white {{ request()->routeIs('roles.*') ? 'bg-gray-900 text-white' : '' }}">
                                    <i class="mr-3 text-lg ri-user-settings-line"></i>
                                    <span class="text-sm">Roles</span>
                                </a>
                            </li>
                        @endif

                        @if($can('pages'))
                            <li>
                                <a href="{{ route('pages.index') }}"
                                   class="flex font-semibold items-center py-2 px-4 rounded-md text-gray-900 hover:bg-gray-900 hover:text-white {{ request()->routeIs('pages.*') ? 'bg-gray-900 text-white' : '' }}">
                                    <i class="mr-3 text-lg ri-file-list-line"></i>
                                    <span class="text-sm">Pages</span>
                                </a>
                            </li>
                        @endif

                        @if($can('privileges'))
                            <li>
                                <a href="{{ route('privileges.index') }}"
                                   class="flex font-semibold items-center py-2 px-4 rounded-md text-gray-900 hover:bg-gray-900 hover:text-white {{ request()->routeIs('privileges.*') ? 'bg-gray-900 text-white' : '' }}">
                                    <i class="mr-3 text-lg ri-lock-password-line"></i>
                                    <span class="text-sm">Privileges</span>
                                </a>
                            </li>
                        @endif

                        @if($can('role-privileges'))
                            <li>
                                <a href="{{ route('role-privileges.index') }}"
                                   class="flex font-semibold items-center py-2 px-4 rounded-md text-gray-900 hover:bg-gray-900 hover:text-white {{ request()->routeIs('role-privileges.*') ? 'bg-gray-900 text-white' : '' }}">
                                    <i class="mr-3 text-lg ri-shield-check-line"></i>
                                    <span class="text-sm">Role Privileges</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            {{-- Branches --}}
            @if($can('branches'))
                <li>
                    <a href="{{ route('branches.index') }}"
                       class="flex font-semibold items-center py-2 px-4 rounded-md text-gray-900 hover:bg-gray-900 hover:text-white {{ request()->routeIs('branches.*') ? 'bg-gray-900 text-white' : '' }}">
                        <i class="mr-3 text-lg ri-store-2-line"></i>
                        <span class="text-sm">Branches</span>
                    </a>
                </li>
            @endif

            {{-- Services --}}
            @if($can('services'))
                <li>
                    <a href="{{ route('services.index') }}"
                       class="flex font-semibold items-center py-2 px-4 rounded-md text-gray-900 hover:bg-gray-900 hover:text-white {{ request()->routeIs('services.*') ? 'bg-gray-900 text-white' : '' }}">
                        <i class="mr-3 text-lg ri-box-3-line"></i>
                        <span class="text-sm">Services</span>
                    </a>
                </li>
            @endif

            {{-- Users --}}
            @if($can('users'))
                <li>
                    <a href="{{ route('users.index') }}"
                       class="flex font-semibold items-center py-2 px-4 rounded-md text-gray-900 hover:bg-gray-900 hover:text-white {{ request()->routeIs('users.*') ? 'bg-gray-900 text-white' : '' }}">
                        <i class="mr-3 text-lg ri-shield-user-line"></i>
                        <span class="text-sm">Users</span>
                    </a>
                </li>
            @endif

            {{-- Logistics Tracking --}}
            @if($can('logistics-tracking'))
                <li>
                    <a href="{{ route('waybills.index') }}"
                       class="flex font-semibold items-center py-2 px-4 rounded-md text-gray-900 hover:bg-gray-900 hover:text-white">
                        <i class="mr-3 text-lg ri-truck-line"></i>
                        <span class="text-sm">Logistics Tracking</span>
                    </a>
                </li>
            @endif

            {{-- Return Waybills --}}
            @if($can('return-waybills'))
                <li>
                    <a href="#"
                       class="flex font-semibold items-center py-2 px-4 rounded-md text-gray-900 hover:bg-gray-900 hover:text-white">
                        <i class="mr-3 text-lg ri-arrow-go-back-line"></i>
                        <span class="text-sm">Return Waybills</span>
                    </a>
                </li>
            @endif

            {{-- Back Jobs Dropdown --}}
            @if($backJobsVisible)
                <li>
                    <button
                        type="button"
                        class="w-full flex font-semibold items-center justify-between py-2 px-4 rounded-md text-gray-900 hover:bg-gray-900 hover:text-white"
                        data-dropdown-toggle="backJobsDropdown"
                    >
                        <span class="flex items-center">
                            <i class="mr-3 text-lg ri-tools-line"></i>
                            <span class="text-sm">Back Jobs</span>
                        </span>

                        <i class="ri-arrow-down-s-line text-lg dropdown-icon"></i>
                    </button>

                    <ul id="backJobsDropdown" class="mt-1 ml-6 space-y-1 hidden">
                        @if($can('backjob-waybills'))
                            <li>
                                <a href="#"
                                   class="flex font-semibold items-center py-2 px-4 rounded-md text-gray-900 hover:bg-gray-900 hover:text-white">
                                    <i class="mr-3 text-lg ri-hammer-line"></i>
                                    <span class="text-sm">Back Job List</span>
                                </a>
                            </li>
                        @endif

                        @if($can('returned-backjob-waybills'))
                            <li>
                                <a href="#"
                                   class="flex font-semibold items-center py-2 px-4 rounded-md text-gray-900 hover:bg-gray-900 hover:text-white">
                                    <i class="mr-3 text-lg ri-arrow-left-right-line"></i>
                                    <span class="text-sm">Returned Back Job List</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            {{-- Albums --}}
            @if($can('albums'))
                <li>
                    <a href="#"
                       class="flex font-semibold items-center py-2 px-4 rounded-md text-gray-900 hover:bg-gray-900 hover:text-white">
                        <i class="mr-3 text-lg ri-image-2-line"></i>
                        <span class="text-sm">Albums</span>
                    </a>
                </li>
            @endif

            {{-- Proof of Payment --}}
            @if($can('proof-of-payment'))
                <li>
                    <a href="#"
                       class="flex font-semibold items-center py-2 px-4 rounded-md text-gray-900 hover:bg-gray-900 hover:text-white">
                        <i class="mr-3 text-lg ri-secure-payment-line"></i>
                        <span class="text-sm">Proof of Payment</span>
                    </a>
                </li>
            @endif

            {{-- Reports --}}
            @if($can('reports'))
                <li>
                    <a href="#"
                       class="flex font-semibold items-center py-2 px-4 rounded-md text-gray-900 hover:bg-gray-900 hover:text-white">
                        <i class="mr-3 text-lg ri-bar-chart-box-line"></i>
                        <span class="text-sm">Reports</span>
                    </a>
                </li>
            @endif

            {{-- Logout --}}
            <li class="mt-2">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button
                        type="submit"
                        class="w-full flex font-semibold items-center py-2 px-4 text-gray-900 hover:bg-red-600 hover:text-white rounded-md"
                    >
                        <i class="mr-3 text-lg ri-logout-box-r-line"></i>
                        <span class="text-sm">Logout</span>
                    </button>
                </form>
            </li>
        </ul>
    </aside>

    {{-- Main Content --}}
    <main class="min-h-screen bg-gray-100 p-4 pt-20 lg:pt-6 lg:p-6 lg:ml-64">
        @yield('content')
    </main>

    <script>
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            sidebarOverlay.classList.remove('hidden');
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            sidebarOverlay.classList.add('hidden');
        }

        if (menuToggle) {
            menuToggle.addEventListener('click', openSidebar);
        }

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', closeSidebar);
        }

        document.querySelectorAll('#sidebar a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 1024) {
                    closeSidebar();
                }
            });
        });

        document.querySelectorAll('[data-dropdown-toggle]').forEach(button => {
            button.addEventListener('click', function () {
                const targetId = this.getAttribute('data-dropdown-toggle');
                const target = document.getElementById(targetId);
                const icon = this.querySelector('.dropdown-icon');

                if (target) {
                    target.classList.toggle('hidden');
                }

                if (icon) {
                    icon.classList.toggle('rotate-180');
                }
            });
        });
    </script>
</body>
</html>