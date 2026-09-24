<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>@yield('title', 'Gestion des absences')</title>
        <style>
            :root {
                --ink: #1f2937;
                --muted: #64748b;
                --line: #dbe3ec;
                --paper: #ffffff;
                --page: #f4f7fb;
                --brand: #1e3a5f;
                --brand-dark: #142943;
                --accent: #0f766e;
                --danger: #b42318;
                --danger-soft: #fff1f0;
                --success: #166534;
                --success-soft: #ecfdf3;
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                color: var(--ink);
                background: var(--page);
                font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
                line-height: 1.5;
            }

            main {
                width: min(1120px, calc(100% - 32px));
                margin: 0 auto;
                padding: 48px 0 64px;
            }

            h1 {
                margin: 0;
                color: var(--brand-dark);
                font-size: clamp(1.8rem, 4vw, 2.5rem);
                line-height: 1.15;
            }

            .page-header {
                display: flex;
                align-items: flex-end;
                justify-content: space-between;
                gap: 24px;
                margin-bottom: 28px;
            }

            .page-intro {
                margin: 8px 0 0;
                color: var(--muted);
            }

            .panel {
                background: var(--paper);
                border: 1px solid var(--line);
                border-radius: 8px;
                box-shadow: 0 12px 30px rgba(30, 58, 95, 0.07);
            }

            .table-wrapper {
                overflow-x: auto;
            }

            table {
                width: 100%;
                min-width: 720px;
                border-collapse: collapse;
                background: var(--paper);
                table-layout: fixed;
            }

            .absences-table th:nth-child(1),
            .absences-table td:nth-child(1) {
                width: 18%;
            }

            .absences-table th:nth-child(2),
            .absences-table td:nth-child(2) {
                width: 34%;
            }

            .absences-table th:nth-child(3),
            .absences-table td:nth-child(3),
            .absences-table th:nth-child(4),
            .absences-table td:nth-child(4) {
                width: 13%;
                white-space: nowrap;
            }

            .absences-table th:nth-child(5),
            .absences-table td:nth-child(5) {
                width: 22%;
            }

            th,
            td {
                padding: 16px 20px;
                border-bottom: 1px solid var(--line);
                text-align: left;
                vertical-align: middle;
            }

            th {
                color: #ffffff;
                background: var(--brand);
                font-size: 0.8rem;
                letter-spacing: 0.04em;
                text-transform: uppercase;
            }

            tbody tr:last-child td {
                border-bottom: 0;
            }

            tbody tr:hover {
                background: #f8fafc;
            }

            .actions {
                display: flex;
                align-items: center;
                flex-wrap: wrap;
                gap: 10px;
            }

            .actions form {
                margin: 0;
            }

            a,
            button {
                font: inherit;
            }

            a {
                color: var(--brand);
                font-weight: 600;
            }

            .button,
            button {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-height: 42px;
                padding: 9px 16px;
                border: 0;
                border-radius: 6px;
                color: #ffffff;
                background: var(--brand);
                cursor: pointer;
                font-weight: 700;
                text-decoration: none;
            }

            .button:hover,
            button:hover {
                background: var(--brand-dark);
            }

            .button-secondary {
                color: var(--brand);
                background: #e8eef5;
            }

            .button-danger {
                color: var(--danger);
                background: var(--danger-soft);
            }

            .button-danger:hover {
                color: #ffffff;
                background: var(--danger);
            }

            .form-panel {
                max-width: 680px;
                padding: 28px;
            }

            .form-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 20px;
            }

            .field {
                display: flex;
                flex-direction: column;
                gap: 7px;
            }

            .field label {
                color: var(--brand-dark);
                font-weight: 700;
            }

            .field input,
            .field select {
                width: 100%;
                min-height: 44px;
                padding: 10px 12px;
                border: 1px solid #b8c5d3;
                border-radius: 6px;
                color: var(--ink);
                background: #ffffff;
                font: inherit;
            }

            .field input:focus,
            .field select:focus {
                outline: 3px solid rgba(15, 118, 110, 0.18);
                border-color: var(--accent);
            }

            .error {
                margin: 0;
                color: var(--danger);
                font-size: 0.9rem;
            }

            .form-actions {
                display: flex;
                align-items: center;
                gap: 12px;
                margin-top: 28px;
            }

            .alert-success {
                margin-bottom: 20px;
                padding: 13px 16px;
                border: 1px solid #bbf7d0;
                border-radius: 6px;
                color: var(--success);
                background: var(--success-soft);
            }

            @media (max-width: 680px) {
                main {
                    width: min(100% - 24px, 560px);
                    padding-top: 28px;
                }

                .page-header,
                .form-actions {
                    align-items: stretch;
                    flex-direction: column;
                }

                .form-grid {
                    grid-template-columns: 1fr;
                }

                .form-panel {
                    padding: 20px;
                }
            }
        </style>
    </head>
    <body>
        <main>
            @yield('content')
        </main>
    </body>
</html>
