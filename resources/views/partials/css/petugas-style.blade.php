@push('styles')
    <style>
        :root {
            --sidebar-width: 260px;
            --primary-color: #2563eb;
            --bg-body: #f8fafc;
            --transition-speed: 0.3s;
        }

        /* Lock Body Scroll */
        body {
            background: var(--bg-body);
            color: #1e293b;
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* --- SIDEBAR STYLE (Fixed Position) --- */
        #sidebar {
            width: var(--sidebar-width);
            min-width: var(--sidebar-width);
            height: 100vh;
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
            transition: margin-left var(--transition-speed) ease-in-out;
            display: flex;
            flex-direction: column;
            z-index: 1050;
        }

        /* Sidebar Toggle Logic */
        #sidebar.collapsed {
            margin-left: calc(var(--sidebar-width) * -1);
        }

        .sidebar-header {
            padding: 25px 20px;
            border-bottom: 1px solid #f1f5f9;
            background: #fff;
        }

        .nav-link {
            padding: 12px 20px;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 600;
            transition: 0.2s;
            text-decoration: none;
        }

        .nav-link:hover,
        .nav-link.active {
            background: #f1f5f9;
            color: var(--primary-color);
        }

        /* Sticky Top Navbar */
        .top-navbar {
            background: #ffffff;
            padding: 15px 25px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        #content {
            flex-grow: 1;
            height: 100vh;
            overflow-y: auto;
            /* Area ini saja yang bisa scroll */
            display: flex;
            flex-direction: column;
            background: var(--bg-body);
            position: relative;
        }



        /* Dashboard UI Elements */
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 20px 0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            #sidebar {
                position: fixed;
                margin-left: calc(var(--sidebar-width) * -1);
            }

            #sidebar.mobile-show {
                margin-left: 0;
            }

            #content {
                width: 100%;
            }

            .header-container {
                flex-direction: column;
                gap: 15px;
            }

        }
    </style>
@endpush