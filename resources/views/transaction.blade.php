<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f3f6fc">
    <title>Transaksi Kasir - MINIMARTGIRL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --blue: #2864ed;
            --blue-dark: #194fc9;
            --ink: #17243b;
            --muted: #748199;
            --line: #e5ebf4;
        }

        * { box-sizing: border-box; }

        body {
            min-height: 100vh;
            margin: 0;
            color: var(--ink);
            background: #f2f5fb;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .topbar {
            height: 74px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 max(24px, calc((100vw - 1320px) / 2));
            background: #fff;
            border-bottom: 1px solid var(--line);
            animation: slide-down .45s ease both;
        }

        .brand, .topbar-actions, .brand-icon {
            display: flex;
            align-items: center;
        }

        .brand { gap: 12px; }

        .brand-icon {
            width: 42px;
            height: 42px;
            justify-content: center;
            border-radius: 13px;
            color: white;
            background: linear-gradient(145deg, #4383ff, #2058dc);
            box-shadow: 0 8px 18px rgba(40, 100, 237, .22);
            font-size: 1.2rem;
        }

        .brand-name {
            margin: 0;
            font-size: .95rem;
            font-weight: 850;
            letter-spacing: -.03em;
        }

        .brand-subtitle {
            margin: 2px 0 0;
            color: var(--muted);
            font-size: .7rem;
        }

        .topbar-actions { gap: 16px; }

        .cashier-name-form {
            display: flex;
            align-items: center;
            gap: 8px;
            max-width: min(340px, 38vw);
            padding: 5px 7px 5px 12px;
            border: 1px solid #f4d9e6;
            border-radius: 999px;
            background: #fff4f9;
        }

        .cashier-name-label {
            flex: 0 0 auto;
            color: #a43d70;
            font-size: .75rem;
            font-weight: 750;
        }

        .cashier-name-input {
            width: 130px;
            min-width: 0;
            padding: 4px 0;
            border: 0;
            outline: 0;
            color: var(--ink);
            background: transparent;
            font: inherit;
            font-size: .75rem;
        }

        .cashier-name-input:focus-visible {
            outline: 2px solid var(--blue);
            outline-offset: 2px;
            border-radius: 3px;
        }

        .cashier-name-submit {
            flex: 0 0 auto;
            padding: 6px 10px;
            border: 0;
            border-radius: 999px;
            color: white;
            background: var(--blue-dark);
            font-size: .7rem;
            font-weight: 750;
            cursor: pointer;
        }

        .cashier-name-submit:hover {
            background: var(--blue);
        }

        .cashier-name-submit:focus-visible {
            outline: 2px solid var(--blue-dark);
            outline-offset: 2px;
        }

        .cashier-update-status {
            width: min(1320px, calc(100% - 48px));
            margin: 12px auto 0;
            color: #228651;
            font-size: .85rem;
            font-weight: 650;
        }

        .topbar-link {
            color: #65738c;
            text-decoration: none;
            font-size: .78rem;
            font-weight: 700;
        }

        .topbar-link:hover { color: var(--blue); }

        .page-shell {
            width: min(1320px, calc(100% - 48px));
            margin: 0 auto;
            padding: 30px 0 48px;
        }

        .page-heading {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 22px;
            animation: rise-in .55s .06s ease both;
        }

        .eyebrow {
            margin: 0 0 7px;
            color: var(--blue);
            font-size: .7rem;
            font-weight: 850;
            letter-spacing: .15em;
            text-transform: uppercase;
        }

        .page-heading h1 {
            margin: 0;
            font-size: clamp(1.7rem, 3vw, 2.25rem);
            font-weight: 850;
            letter-spacing: -.055em;
        }

        .page-heading p {
            margin: 7px 0 0;
            color: var(--muted);
            font-size: .86rem;
        }

        .transaction-code {
            text-align: right;
        }

        .transaction-code small {
            display: block;
            margin-bottom: 4px;
            color: #8793a8;
            font-size: .69rem;
        }

        .transaction-code strong {
            color: #263653;
            font-size: .84rem;
            letter-spacing: .025em;
        }

        .page-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 340px;
            align-items: start;
            gap: 18px;
        }

        .main-column, .summary-column {
            display: grid;
            gap: 16px;
        }

        .panel {
            overflow: hidden;
            border: 1px solid #e7ecf4;
            border-radius: 17px;
            background: white;
            box-shadow: 0 8px 25px rgba(29, 48, 82, .045);
            animation: rise-in .58s both;
        }

        .main-column .panel:nth-child(1) { animation-delay: .12s; }
        .main-column .panel:nth-child(2) { animation-delay: .2s; }
        .summary-column .panel { animation-delay: .17s; }

        .panel-heading {
            min-height: 48px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 0 17px;
            border-bottom: 1px solid var(--line);
        }

        .panel-title {
            display: flex;
            align-items: center;
            gap: 9px;
            margin: 0;
            font-size: .84rem;
            font-weight: 800;
        }

        .panel-icon {
            color: var(--blue);
            font-size: 1rem;
        }

        .panel-meta {
            color: #8793a8;
            font-size: .7rem;
            font-weight: 650;
        }

        .panel-body { padding: 16px; }

        .search-line {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 8px;
            margin-bottom: 15px;
        }

        .search-wrap { position: relative; }

        .search-icon {
            position: absolute;
            top: 50%;
            left: 14px;
            color: #9aa6b8;
            pointer-events: none;
            transform: translateY(-50%);
        }

        .search-input, .form-control, .form-select {
            min-height: 42px;
            border: 1px solid #dfe6f0;
            border-radius: 9px;
            color: #27354d;
            background-color: #fff;
            font-size: .8rem;
            box-shadow: none !important;
        }

        .search-input { padding-left: 40px; }

        .search-input:focus, .form-control:focus, .form-select:focus {
            border-color: #80a7ff;
        }

        .btn-blue {
            min-width: 84px;
            border: 0;
            border-radius: 9px;
            color: #fff;
            background: var(--blue);
            font-size: .78rem;
            font-weight: 750;
            transition: background .18s ease, transform .18s ease, box-shadow .18s ease;
        }

        .btn-blue:hover {
            color: white;
            background: var(--blue-dark);
            box-shadow: 0 7px 15px rgba(40, 100, 237, .2);
            transform: translateY(-1px);
        }

        .catalog-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .catalog-product {
            min-width: 0;
            display: grid;
            grid-template-columns: 58px minmax(0, 1fr);
            align-items: center;
            gap: 10px;
            padding: 9px;
            border: 1px solid #edf0f6;
            border-radius: 11px;
            background: #fff;
            transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease;
            animation: pop-in .35s ease both;
        }

        .catalog-product:hover {
            border-color: #bdd0ff;
            box-shadow: 0 7px 18px rgba(40, 100, 237, .08);
            transform: translateY(-2px);
        }

        .catalog-image {
            width: 58px;
            height: 58px;
            border-radius: 9px;
            object-fit: contain;
            background: #f6f8fc;
        }

        .catalog-info { min-width: 0; }

        .catalog-name {
            overflow: hidden;
            margin: 0 0 4px;
            color: #27354d;
            font-size: .73rem;
            font-weight: 780;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .catalog-price {
            margin: 0 0 6px;
            color: #4c5e7a;
            font-size: .69rem;
            font-weight: 700;
        }

        .catalog-action {
            padding: 4px 7px;
            border: 1px solid #dce6ff;
            border-radius: 6px;
            color: var(--blue);
            background: #f5f8ff;
            font-size: .64rem;
            font-weight: 750;
        }

        .catalog-action:hover:not(:disabled) {
            color: white;
            background: var(--blue);
        }

        .catalog-action:disabled {
            border-color: #edf0f4;
            color: #a0a9b7;
            background: #f5f6f8;
        }

        .catalog-empty {
            grid-column: 1 / -1;
            padding: 22px 10px;
            color: var(--muted);
            font-size: .78rem;
            text-align: center;
        }

        .cart-list { min-height: 130px; }

        .cart-empty {
            min-height: 130px;
            display: grid;
            place-content: center;
            justify-items: center;
            gap: 7px;
            color: #8a98ae;
            font-size: .75rem;
            text-align: center;
        }

        .cart-empty-icon {
            color: #a9bad4;
            font-size: 1.4rem;
        }

        .cart-row {
            display: grid;
            grid-template-columns: 46px minmax(100px, 1fr) auto auto auto;
            align-items: center;
            gap: 11px;
            padding: 12px 0;
            border-bottom: 1px solid #eef1f6;
            animation: pop-in .25s ease both;
        }

        .cart-row:last-child { border-bottom: 0; }

        .cart-image {
            width: 46px;
            height: 46px;
            border-radius: 8px;
            object-fit: contain;
            background: #f6f8fc;
        }

        .cart-product-name {
            margin: 0 0 4px;
            font-size: .76rem;
            font-weight: 780;
        }

        .cart-product-price {
            color: var(--muted);
            font-size: .67rem;
        }

        .quantity-control {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 4px;
            border: 1px solid #e3e9f2;
            border-radius: 8px;
        }

        .quantity-control button {
            width: 22px;
            height: 22px;
            display: grid;
            place-items: center;
            padding: 0;
            border: 0;
            border-radius: 5px;
            color: #52627a;
            background: #f1f4f9;
            font-size: .85rem;
        }

        .quantity-value {
            min-width: 14px;
            color: #27354d;
            font-size: .72rem;
            font-weight: 750;
            text-align: center;
        }

        .cart-line-total {
            min-width: 85px;
            color: #34435c;
            font-size: .72rem;
            font-weight: 750;
            text-align: right;
        }

        .remove-item {
            width: 27px;
            height: 27px;
            border: 0;
            border-radius: 7px;
            color: #9da7b6;
            background: transparent;
            font-size: 1rem;
        }

        .remove-item:hover {
            color: #d14358;
            background: #fff1f2;
        }

        .summary-panel { position: sticky; top: 18px; }

        .summary-content {
            display: grid;
            gap: 14px;
            padding: 17px;
        }

        .summary-line {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            color: #738097;
            font-size: .75rem;
        }

        .summary-line strong {
            color: #334159;
            font-size: .76rem;
        }

        .summary-edit {
            width: 105px;
            min-height: 35px;
            padding: 5px 9px;
            text-align: right;
        }

        .summary-total {
            display: grid;
            gap: 6px;
            margin-top: 2px;
            padding: 14px;
            border: 1px solid #e4ebf6;
            border-radius: 11px;
            background: #f6f8fc;
        }

        .summary-total span {
            color: #7d8aa0;
            font-size: .66rem;
            font-weight: 800;
            letter-spacing: .06em;
        }

        .summary-total strong {
            color: #17243b;
            font-size: 1.45rem;
            font-weight: 900;
            letter-spacing: -.05em;
        }

        .payment-field {
            display: grid;
            gap: 7px;
        }

        .payment-field label {
            color: #66748b;
            font-size: .72rem;
            font-weight: 700;
        }

        .payment-input {
            text-align: right;
        }

        .change-row strong { color: #23824f; }

        .checkout-button {
            min-height: 45px;
            border: 0;
            border-radius: 10px;
            color: white;
            background: var(--blue);
            font-size: .82rem;
            font-weight: 800;
            box-shadow: 0 8px 18px rgba(40, 100, 237, .19);
            transition: background .18s ease, transform .18s ease;
        }

        .checkout-button:hover:not(:disabled) {
            color: white;
            background: var(--blue-dark);
            transform: translateY(-1px);
        }

        .checkout-button:disabled {
            color: #8994a5;
            background: #e6eaf0;
            box-shadow: none;
        }

        .summary-note {
            margin: 0;
            color: #929db0;
            font-size: .67rem;
            line-height: 1.5;
        }

        .feedback-alert {
            padding: 12px 15px;
            border: 1px solid #cfe9d8;
            border-radius: 10px;
            color: #277344;
            background: #f0faf3;
            font-size: .78rem;
        }

        .feedback-alert.error {
            border-color: #f1d3d5;
            color: #a13d47;
            background: #fff5f5;
        }

        .page-footer {
            margin-top: 24px;
            color: #98a3b5;
            font-size: .7rem;
        }

        .add-product-panel {
            margin-bottom: 18px;
            border: 1px solid rgba(244, 226, 236, .94);
            border-radius: 17px;
            background: rgba(255, 255, 255, .92);
            box-shadow: 0 12px 35px rgba(99, 54, 79, .06);
            animation: rise-in .58s .1s both;
        }

        .product-import-content {
            display: grid;
            gap: 12px;
            padding: 16px;
            border-top: 1px solid #f2e8ee;
        }

        .product-import-hint {
            margin: 0;
            color: #7c7180;
            font-size: .75rem;
            line-height: 1.6;
        }

        .product-import-errors {
            margin: 0;
            padding-left: 20px;
            color: #a13d47;
            font-size: .74rem;
            line-height: 1.6;
        }

        .product-import-actions {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .product-import-actions .form-control {
            flex: 1 1 260px;
            max-width: 520px;
        }

        .product-import-template {
            color: #a5366e;
            font-size: .75rem;
            font-weight: 750;
        }

        .product-import-submit {
            min-height: 40px;
            padding: 0 15px;
            border: 0;
            border-radius: 9px;
            color: white;
            background: #c92f76;
            font-size: .75rem;
            font-weight: 800;
        }

        .held-panel {
            margin-bottom: 18px;
            overflow: hidden;
            border: 1px solid #f0dce7;
            border-radius: 16px;
            background: rgba(255, 255, 255, .94);
            box-shadow: 0 10px 28px rgba(99, 54, 79, .05);
        }

        .held-heading, .held-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
        }

        .held-heading {
            padding: 15px 18px;
            border-bottom: 1px solid #f2e8ee;
            color: #392d40;
            font-size: .84rem;
            font-weight: 850;
        }

        .held-list { display: grid; }
        .held-row { padding: 12px 18px; border-bottom: 1px solid #f5edf1; }
        .held-row:last-child { border-bottom: 0; }
        .held-details { display: grid; gap: 3px; }
        .held-details strong { color: #443849; font-size: .76rem; }
        .held-details span { color: #8b8090; font-size: .68rem; }
        .held-actions { display: flex; align-items: center; gap: 8px; }
        .held-actions a, .held-actions button {
            padding: 7px 10px;
            border: 1px solid #f0d8e4;
            border-radius: 8px;
            color: #b43270;
            background: #fff8fb;
            font-size: .68rem;
            font-weight: 800;
            text-decoration: none;
        }
        .held-actions button { color: #7d6d78; background: #fff; }
        .held-empty { padding: 14px 18px; color: #8b8090; font-size: .72rem; }
        .payment-method-field { display: grid; gap: 7px; }
        .payment-method-field label { color: #66748b; font-size: .72rem; font-weight: 700; }
        .payment-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 9px; }
        .hold-button, .clear-cart-button {
            min-height: 42px;
            border: 1px solid #f0d8e4;
            border-radius: 10px;
            color: #a5366e;
            background: #fff7fb;
            font-size: .74rem;
            font-weight: 800;
        }
        .clear-cart-button { color: #7e6875; background: #fff; }
        .hold-button:disabled { color: #aeb3bd; background: #f2f3f5; }

        .add-product-summary {
            min-height: 54px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 0 17px;
            color: #392d40;
            cursor: pointer;
            font-size: .88rem;
            font-weight: 800;
            list-style: none;
        }

        .add-product-summary::-webkit-details-marker { display: none; }

        .add-product-summary::after {
            color: #c92f76;
            content: "+";
            font-size: 1.3rem;
            transition: transform .2s ease;
        }

        .add-product-panel[open] .add-product-summary::after {
            transform: rotate(45deg);
        }

        .add-product-summary span {
            display: inline-flex;
            align-items: center;
            gap: 9px;
        }

        .add-product-summary span::before {
            color: #e94b91;
            content: "✦";
        }

        .add-product-form {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 13px;
            padding: 16px;
            border-top: 1px solid #f2e8ee;
        }

        .add-product-field {
            display: grid;
            align-content: start;
            gap: 6px;
        }

        .add-product-field label {
            color: #62566a;
            font-size: .74rem;
            font-weight: 750;
        }

        .add-product-field .form-control {
            min-height: 42px;
        }

        .add-product-field textarea.form-control {
            min-height: 74px;
            resize: vertical;
        }

        .add-product-field.full-width {
            grid-column: 1 / -1;
        }

        .add-product-hint {
            margin: 0;
            color: #9b93a1;
            font-size: .68rem;
        }

        .add-product-submit {
            min-height: 43px;
            justify-self: end;
            padding: 0 18px;
            border: 0;
            border-radius: 10px;
            color: white;
            background: linear-gradient(100deg, #e94b91, #ce367d);
            box-shadow: 0 8px 17px rgba(211, 54, 125, .18);
            font-size: .78rem;
            font-weight: 800;
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .add-product-submit:hover {
            box-shadow: 0 11px 22px rgba(211, 54, 125, .26);
            transform: translateY(-1px);
        }

        @keyframes slide-down {
            from { opacity: 0; transform: translateY(-9px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes rise-in {
            from { opacity: 0; transform: translateY(13px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes pop-in {
            from { opacity: 0; transform: scale(.98); }
            to { opacity: 1; transform: scale(1); }
        }

        @media (max-width: 1050px) {
            .page-grid { grid-template-columns: minmax(0, 1fr) 310px; }
            .catalog-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 780px) {
            .page-shell { width: min(100% - 30px, 620px); padding-top: 22px; }
            .page-grid { grid-template-columns: 1fr; }
            .summary-panel { position: static; }
            .summary-column { order: -1; }
            .catalog-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .topbar { height: auto; min-height: 66px; flex-wrap: wrap; gap: 8px 16px; padding: 10px 16px; }
            .topbar-actions { width: 100%; flex-wrap: wrap; justify-content: space-between; gap: 8px; }
            .cashier-name-form { flex: 1 1 240px; max-width: none; }
            .cashier-name-input { flex: 1 1 auto; width: auto; }
        }

        @media (max-width: 480px) {
            .page-shell { width: calc(100% - 22px); padding-top: 17px; }
            .page-heading { align-items: start; }
            .page-heading p { max-width: 230px; font-size: .78rem; }
            .transaction-code { padding-top: 23px; }
            .transaction-code strong { font-size: .7rem; }
            .cashier-name-form { flex-basis: 100%; }
            .cashier-name-label, .cashier-name-input { font-size: .7rem; }
            .cashier-update-status { width: calc(100% - 22px); }
            .topbar-link { font-size: .7rem; }
            .brand-icon { width: 37px; height: 37px; }
            .panel-body { padding: 12px; }
            .catalog-grid { gap: 7px; }
            .catalog-product { grid-template-columns: 45px minmax(0, 1fr); gap: 7px; padding: 7px; }
            .catalog-image { width: 45px; height: 45px; }
            .catalog-name { font-size: .68rem; }
            .cart-row { grid-template-columns: 38px minmax(70px, 1fr) auto auto; gap: 7px; }
            .cart-image { width: 38px; height: 38px; }
            .cart-line-total { display: none; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                scroll-behavior: auto !important;
                transition-duration: .01ms !important;
            }
        }

        :root {
            --blue: #e94b91;
            --blue-dark: #c92f76;
            --ink: #29233a;
            --muted: #858094;
            --line: #f1e4ec;
        }

        body {
            background:
                radial-gradient(ellipse at 92% 8%, rgba(255, 211, 230, .48), transparent 27%),
                radial-gradient(ellipse at 5% 92%, rgba(255, 229, 204, .44), transparent 28%),
                #fff9fc;
        }

        .topbar {
            background: rgba(255, 255, 255, .9);
            border-color: #f3e6ed;
            box-shadow: 0 8px 28px rgba(99, 54, 79, .055);
            backdrop-filter: blur(14px);
        }

        .brand-icon {
            background: linear-gradient(145deg, #ef6ca5, #c92f76);
            box-shadow: 0 8px 18px rgba(211, 54, 125, .22);
        }

        .page-heading {
            position: relative;
            min-height: 222px;
            align-items: center;
            overflow: hidden;
            padding: 34px 42px;
            border: 1px solid rgba(255, 255, 255, .75);
            border-radius: 26px;
            color: white;
            background: linear-gradient(116deg, #ed65a0, #d63b82 57%, #b92d75);
            box-shadow: 0 24px 60px rgba(177, 57, 112, .18);
            isolation: isolate;
        }

        .page-heading::before,
        .page-heading::after {
            position: absolute;
            z-index: -1;
            border: 1px solid rgba(255, 255, 255, .2);
            border-radius: 50%;
            content: "";
        }

        .page-heading::before {
            width: 390px;
            height: 390px;
            top: -270px;
            right: 110px;
        }

        .page-heading::after {
            width: 510px;
            height: 510px;
            top: -335px;
            right: 50px;
        }

        .page-heading > div:first-child {
            position: relative;
            z-index: 2;
        }

        .page-heading .eyebrow {
            color: rgba(255, 255, 255, .78);
        }

        .page-heading h1 {
            font-size: clamp(2rem, 4vw, 3rem);
        }

        .page-heading p:last-child {
            max-width: 460px;
            color: rgba(255, 255, 255, .82);
            line-height: 1.7;
        }

        .transaction-code {
            position: relative;
            z-index: 2;
            margin-left: auto;
            padding: 11px 15px;
            border: 1px solid rgba(255, 255, 255, .28);
            border-radius: 13px;
            background: rgba(255, 255, 255, .13);
            backdrop-filter: blur(10px);
        }

        .transaction-code small,
        .transaction-code strong {
            color: white;
        }

        .transaction-art {
            position: absolute;
            top: 50%;
            right: 22%;
            width: 128px;
            height: 128px;
            display: grid;
            place-items: center;
            border: 1px solid rgba(255, 255, 255, .4);
            border-radius: 36px;
            background: linear-gradient(145deg, rgba(255,255,255,.34), rgba(255,255,255,.1));
            box-shadow: inset -10px -12px 25px rgba(124, 17, 74, .12), 0 25px 45px rgba(105, 14, 63, .2);
            font-size: 3.8rem;
            transform: translateY(-50%) rotateY(-15deg) rotateX(8deg) rotateZ(-5deg);
            transform-style: preserve-3d;
            animation: cart-hover 5.5s ease-in-out infinite;
        }

        .panel {
            border-color: rgba(244, 226, 236, .94);
            border-radius: 19px;
            box-shadow: 0 12px 35px rgba(99, 54, 79, .065);
            backdrop-filter: blur(10px);
        }

        .panel-heading {
            min-height: 54px;
            background: linear-gradient(180deg, #fff, #fffafd);
        }

        .panel-title {
            color: #392d40;
            font-size: .88rem;
        }

        .panel-icon {
            color: var(--blue);
        }

        .search-input:focus,
        .form-control:focus,
        .form-select:focus {
            border-color: #ec83b1;
            box-shadow: 0 0 0 4px rgba(233, 75, 145, .1) !important;
        }

        .catalog-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .catalog-product {
            position: relative;
            grid-template-columns: 72px minmax(0, 1fr);
            gap: 12px;
            padding: 11px;
            border-color: #f1e8ee;
            border-radius: 15px;
            background: linear-gradient(145deg, #fff, #fffafd);
            box-shadow: 0 5px 16px rgba(99, 54, 79, .035);
        }

        .catalog-product:hover {
            border-color: #efb6d0;
            box-shadow: 0 13px 25px rgba(177, 57, 112, .12);
            transform: translateY(-4px);
        }

        .catalog-image {
            width: 72px;
            height: 72px;
            border: 1px solid rgba(244, 226, 236, .8);
            border-radius: 13px;
            background:
                radial-gradient(circle at 50% 45%, rgba(255,255,255,.95), rgba(255,240,247,.7)),
                #fff4f9;
            filter: drop-shadow(0 5px 8px rgba(74, 37, 58, .1));
            transition: transform .3s cubic-bezier(.2,.8,.2,1);
        }

        .catalog-product:hover .catalog-image {
            transform: translateY(-3px) scale(1.06) rotate(-2deg);
        }

        .catalog-name {
            color: #352b3d;
            font-size: .79rem;
        }

        .catalog-price {
            color: #c92f76;
            font-size: .75rem;
            font-weight: 800;
        }

        .catalog-action {
            padding: 5px 10px;
            border-color: #f1c8da;
            border-radius: 8px;
            color: #c92f76;
            background: #fff3f8;
            transition: transform .18s ease, background .18s ease;
        }

        .catalog-action:hover:not(:disabled) {
            color: #fff;
            background: #d83b82;
            transform: translateY(-1px);
        }

        .latest-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            padding: 4px 7px;
            border-radius: 999px;
            color: white;
            background: linear-gradient(100deg, #ed65a0, #c92f76);
            box-shadow: 0 4px 10px rgba(201, 47, 118, .18);
            font-size: .57rem;
            font-weight: 850;
            letter-spacing: .04em;
        }

        .quantity-control {
            border-color: #f0dfe8;
            background: #fff9fc;
        }

        .quantity-control button {
            color: #a43d70;
            background: #fdebf3;
        }

        .summary-total {
            border-color: #f4dce8;
            background: linear-gradient(145deg, #fff7fb, #fff);
        }

        .summary-total strong,
        .change-row strong {
            color: #c92f76;
        }

        .checkout-button {
            background: linear-gradient(100deg, #e94b91, #ce367d);
            box-shadow: 0 9px 20px rgba(211, 54, 125, .2);
        }

        .checkout-button:hover:not(:disabled) {
            background: linear-gradient(100deg, #dc3e85, #b92a70);
        }

        .cart-empty-icon {
            color: #df80aa;
        }

        @keyframes cart-hover {
            0%, 100% { transform: translateY(-50%) rotateY(-15deg) rotateX(8deg) rotateZ(-5deg); }
            50% { transform: translateY(calc(-50% - 11px)) rotateY(10deg) rotateX(-4deg) rotateZ(3deg); }
        }

        @media (max-width: 780px) {
            .page-heading {
                min-height: 200px;
                padding: 28px;
            }

            .transaction-art {
                right: 18%;
                width: 100px;
                height: 100px;
                border-radius: 29px;
                font-size: 3rem;
            }

            .catalog-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 520px) {
            .page-heading {
                min-height: 182px;
                align-items: center;
                padding: 23px 20px;
                border-radius: 23px;
            }

            .page-heading h1 {
                font-size: 1.9rem;
            }

            .page-heading p:last-child {
                max-width: 230px;
                font-size: .76rem;
            }

            .transaction-code {
                position: absolute;
                right: 16px;
                bottom: 15px;
                margin: 0;
                padding: 7px 9px;
            }

            .transaction-code small {
                font-size: .6rem;
            }

            .transaction-code strong {
                font-size: .66rem;
            }

            .transaction-art {
                top: 26%;
                right: 9%;
                width: 75px;
                height: 75px;
                border-radius: 22px;
                font-size: 2.3rem;
                opacity: .85;
            }

            .catalog-product {
                grid-template-columns: 52px minmax(0, 1fr);
                gap: 8px;
                padding: 8px;
            }

            .catalog-image {
                width: 52px;
                height: 52px;
            }

            .catalog-name {
                padding-right: 26px;
                font-size: .7rem;
            }

            .add-product-form {
                grid-template-columns: 1fr;
                gap: 11px;
                padding: 13px;
            }

            .add-product-field.full-width {
                grid-column: auto;
            }

            .add-product-submit {
                width: 100%;
            }
        }

        @media (max-width: 390px) {
            .catalog-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<header class="topbar">
    <a href="{{ route('dashboard') }}" class="brand text-decoration-none text-reset">
        <span class="brand-icon" aria-hidden="true">▦</span>
        <span>
            <span class="brand-name d-block">MINIMARTGIRL</span>
            <span class="brand-subtitle d-block">Sistem kasir</span>
        </span>
    </a>
    <div class="topbar-actions">
        <form action="{{ route('profile.name.update') }}" method="POST" class="cashier-name-form">
            @csrf
            @method('PUT')
            <label for="cashier-name" class="cashier-name-label">Nama kasir:</label>
            <input
                id="cashier-name"
                class="cashier-name-input"
                type="text"
                name="name"
                value="{{ old('name', $cashierName) }}"
                autocomplete="name"
                maxlength="255"
                required
            >
            <button type="submit" class="cashier-name-submit">Simpan</button>
        </form>
        <a href="#new-product" class="topbar-link">+ Barang baru</a>
        <a href="{{ route('transaction.history') }}" class="topbar-link">Riwayat</a>
        <a href="{{ route('dashboard') }}" class="topbar-link">Dasbor</a>
    </div>
</header>

@if (session('status'))
    <p class="cashier-update-status" role="status">{{ session('status') }}</p>
@endif

<main class="page-shell" id="pos-app"
      data-catalog="{{ json_encode($products, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) }}"
      data-initial-cart="{{ json_encode($initialCart, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) }}">
    <div class="page-heading">
        <div>
            <p class="eyebrow">Kasir</p>
            <h1>Transaksi Kasir</h1>
            <p>Cari barang, tambahkan ke keranjang, lalu selesaikan pembayaran.</p>
        </div>
        <div class="transaction-code">
            <small>No. Transaksi</small>
            <strong>TRX-{{ now()->format('Ymd') }}-BARU</strong>
        </div>
        <div class="transaction-art" aria-hidden="true">🛒</div>
    </div>

    @if (session('success'))
        <div class="feedback-alert mb-3" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="feedback-alert error mb-3" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    <details class="add-product-panel" id="new-product" @if ($errors->hasAny(['name', 'barcode', 'category', 'description', 'price', 'stock', 'image'])) open @endif>
        <summary class="add-product-summary"><span>Tambah barang baru</span></summary>
        <form action="{{ route('transaction.products.store') }}" method="POST" enctype="multipart/form-data" class="add-product-form">
            @csrf
            <div class="add-product-field">
                <label for="new-product-name">Nama barang *</label>
                <input id="new-product-name" name="name" type="text" class="form-control"
                       value="{{ old('name') }}" maxlength="255" required placeholder="Contoh: Susu UHT">
            </div>
            <div class="add-product-field">
                <label for="new-product-barcode">Kode batang / kode barang *</label>
                <input id="new-product-barcode" name="barcode" type="text" class="form-control"
                       value="{{ old('barcode') }}" maxlength="255" required placeholder="Masukkan kode unik">
            </div>
            <div class="add-product-field">
                <label for="new-product-category">Kategori</label>
                <input id="new-product-category" name="category" type="text" class="form-control"
                       value="{{ old('category') }}" maxlength="255" placeholder="Makanan, minuman, dan lainnya">
            </div>
            <div class="add-product-field">
                <label for="new-product-price">Harga jual (Rp) *</label>
                <input id="new-product-price" name="price" type="number" class="form-control"
                       value="{{ old('price') }}" min="0.01" max="9999999999.99" step="0.01" required>
            </div>
            <div class="add-product-field">
                <label for="new-product-stock">Stok awal *</label>
                <input id="new-product-stock" name="stock" type="number" class="form-control"
                       value="{{ old('stock', 0) }}" min="0" max="2147483647" step="1" required>
            </div>
            <div class="add-product-field">
                <label for="new-product-image">Gambar produk</label>
                <input id="new-product-image" name="image" type="file" class="form-control"
                       accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                <p class="add-product-hint">JPG, PNG, atau WebP. Maksimal 2 MB.</p>
            </div>
            <div class="add-product-field full-width">
                <label for="new-product-description">Deskripsi</label>
                <textarea id="new-product-description" name="description" class="form-control"
                          maxlength="2000" placeholder="Deskripsi singkat (opsional)">{{ old('description') }}</textarea>
            </div>
            <div class="add-product-field full-width">
                <button type="submit" class="add-product-submit">Simpan barang ke katalog</button>
            </div>
        </form>
    </details>

    <details class="add-product-panel" id="import-products" @if (session('open_product_import') || $errors->has('import_file')) open @endif>
        <summary class="add-product-summary"><span>Impor banyak produk dari CSV</span></summary>
        <div class="product-import-content">
            <p class="product-import-hint">
                Gunakan berkas CSV UTF-8 (dapat dibuat atau disimpan melalui Excel). Judul kolom wajib:
                <strong>kode batang, nama, harga, stok</strong>. Judul kolom tambahan: kategori dan deskripsi.
                Harga gunakan titik untuk desimal tanpa pemisah ribuan; stok harus berupa bilangan bulat.
                Maksimal 5.000 produk dan ukuran berkas 5 MB per impor. Kode batang yang sudah ada atau
                berulang akan membatalkan seluruh impor.
            </p>
            <a class="product-import-template" href="{{ route('transaction.products.import-template') }}">Unduh templat CSV</a>
            @if ($errors->has('import_file'))
                <ul class="product-import-errors" role="alert">
                    @foreach ($errors->get('import_file') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            @endif
            <form action="{{ route('transaction.products.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <label class="visually-hidden" for="product-import-file">Berkas CSV produk</label>
                <div class="product-import-actions">
                    <input
                        id="product-import-file"
                        class="form-control"
                        type="file"
                        name="import_file"
                        accept=".csv,text/csv"
                        required
                    >
                    <button class="product-import-submit" type="submit">Masukkan produk</button>
                </div>
            </form>
        </div>
    </details>

    <section class="held-panel" aria-labelledby="held-transactions-title">
        <div class="held-heading" id="held-transactions-title">Transaksi tertahan</div>
        @forelse ($heldTransactions as $held)
            <div class="held-row">
                <div class="held-details">
                    <strong>{{ $held->transaction_code }} · Rp {{ number_format($held->total, 0, ',', '.') }}</strong>
                    <span>{{ $held->transaction_details_count }} barang · {{ $held->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="held-actions">
                    <a href="{{ route('transaction.create', ['resume' => $held->id]) }}">Lanjutkan</a>
                    <form action="{{ route('transaction.cancel', $held) }}" method="POST" onsubmit="return confirm('Batalkan transaksi tertahan ini?')">
                        @csrf
                        <button type="submit">Batalkan</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="held-empty">Tidak ada transaksi yang ditahan.</div>
        @endforelse
    </section>

    <form action="{{ route('transaction.store') }}" method="POST" id="checkout-form">
        @csrf
        <input type="hidden" id="held-transaction-id" name="held_transaction_id" value="{{ $resumeTransaction?->id }}">
        <div class="page-grid">
            <div class="main-column">
                <section class="panel">
                    <div class="panel-heading">
                        <h2 class="panel-title"><span class="panel-icon" aria-hidden="true">⊞</span> Tambah Barang</h2>
                        <span class="panel-meta">Pilih dari katalog</span>
                    </div>
                    <div class="panel-body">
                        <label class="visually-hidden" for="product-search">Cari barang</label>
                        <div class="search-line">
                            <div class="search-wrap">
                                <span class="search-icon" aria-hidden="true">⌕</span>
                                <input id="product-search" type="search" class="form-control search-input"
                                       placeholder="Cari nama barang atau kode batang..." autocomplete="off">
                            </div>
                            <button type="button" class="btn btn-blue" id="search-button"><span aria-hidden="true">⌕</span> Cari</button>
                        </div>
                        <div class="catalog-grid" id="catalog-grid" aria-live="polite"></div>
                    </div>
                </section>

                <section class="panel">
                    <div class="panel-heading">
                        <h2 class="panel-title"><span class="panel-icon" aria-hidden="true">🛒</span> Keranjang Belanja</h2>
                        <span class="panel-meta" id="cart-count">0 barang</span>
                    </div>
                    <div class="panel-body cart-list" id="cart-list" aria-live="polite"></div>
                </section>
            </div>

            <aside class="summary-column">
                <section class="panel summary-panel">
                    <div class="panel-heading">
                        <h2 class="panel-title"><span class="panel-icon" aria-hidden="true">▤</span> Ringkasan Pembayaran</h2>
                    </div>
                    <div class="summary-content">
                        <div class="summary-line">
                            <span>Jumlah Barang</span>
                            <strong id="summary-items">0</strong>
                        </div>
                        <div class="summary-line">
                            <span>Jumlah sementara</span>
                            <strong id="summary-subtotal">Rp 0</strong>
                        </div>
                        <div class="summary-line">
                            <label for="discount-percentage">Diskon (%)</label>
                            <input id="discount-percentage" name="discount_percentage" type="number"
                                   class="form-control summary-edit" min="0" max="100" step="0.01"
                                   value="{{ old('discount_percentage', $resumeTransaction?->discount_percentage ?? 0) }}">
                        </div>
                        <div class="summary-line">
                            <label for="discount-amount">Diskon (Rp)</label>
                            <input id="discount-amount" name="discount_amount" type="number"
                                   class="form-control summary-edit" min="0" step="1"
                                   value="{{ old('discount_amount', $resumeTransaction?->discount_amount ?? 0) }}">
                        </div>
                        <div class="summary-line">
                            <label for="tax-amount">Pajak / PPN (Rp)</label>
                            <input id="tax-amount" name="tax" type="number"
                                   class="form-control summary-edit" min="0" step="1"
                                   value="{{ old('tax', $resumeTransaction?->tax ?? 0) }}">
                        </div>

                        <div class="summary-total">
                            <span>JUMLAH AKHIR</span>
                            <strong id="summary-total">Rp 0</strong>
                        </div>

                        <div class="payment-method-field">
                            <label for="payment-method">Cara pembayaran</label>
                            <select id="payment-method" name="payment_method" class="form-select">
                                @foreach (['cash' => 'Tunai', 'qris' => 'QRIS', 'debit' => 'Kartu debit', 'credit' => 'Kartu kredit', 'ewallet' => 'Dompet digital', 'transfer' => 'Transfer bank'] as $method => $label)
                                    <option value="{{ $method }}" @selected(old('payment_method', 'cash') === $method)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="payment-field">
                            <label for="amount-paid" id="amount-paid-label">Uang diterima (Rp)</label>
                            <input id="amount-paid" name="amount_paid" type="number"
                                   class="form-control payment-input" min="0" step="1"
                                   value="{{ old('amount_paid', 0) }}">
                        </div>

                        <div class="summary-line change-row">
                            <span>Kembalian</span>
                            <strong id="summary-change">Rp 0</strong>
                        </div>

                        <div id="cart-inputs"></div>
                        <div class="payment-actions">
                            <button type="submit" class="btn checkout-button" id="checkout-button" name="action" value="pay" disabled>
                                Bayar &amp; Cetak
                            </button>
                            <button type="submit" class="hold-button" id="hold-button" name="action" value="hold" disabled>
                                Tahan Transaksi
                            </button>
                        </div>
                        <button type="button" class="clear-cart-button w-100 mt-2" id="clear-cart-button">Batalkan keranjang</button>
                        <p class="summary-note">Transaksi tertahan tidak mengurangi stok. Pembayaran elektronik dicatat sebagai cara pembayaran tanpa konfirmasi dari penyedia layanan.</p>
                    </div>
                </section>
            </aside>
        </div>
    </form>

    <footer class="page-footer">MINIMARTGIRL · Sistem kasir</footer>
</main>

<script>
    const posApp = document.getElementById('pos-app');
    const catalog = JSON.parse(posApp.dataset.catalog);
    const initialCart = JSON.parse(posApp.dataset.initialCart);
    const cart = new Map();
    const currency = new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 });
    const formatRupiah = (amount) => `Rp ${currency.format(Math.round(amount || 0))}`;
    const catalogGrid = document.getElementById('catalog-grid');
    const cartList = document.getElementById('cart-list');
    const cartInputs = document.getElementById('cart-inputs');
    const searchInput = document.getElementById('product-search');
    const discountPercentage = document.getElementById('discount-percentage');
    const discountAmount = document.getElementById('discount-amount');
    const taxAmount = document.getElementById('tax-amount');
    const amountPaid = document.getElementById('amount-paid');
    const paymentMethod = document.getElementById('payment-method');
    const checkoutButton = document.getElementById('checkout-button');
    const holdButton = document.getElementById('hold-button');

    function makeElement(tag, className, text) {
        const element = document.createElement(tag);
        if (className) element.className = className;
        if (text !== undefined) element.textContent = text;
        return element;
    }

    function renderCatalog() {
        const query = searchInput.value.trim().toLocaleLowerCase('id');
        const matches = catalog.filter((product) =>
            `${product.name} ${product.barcode} ${product.category || ''}`.toLocaleLowerCase('id').includes(query)
        );
        catalogGrid.replaceChildren();

        if (matches.length === 0) {
            catalogGrid.append(makeElement('p', 'catalog-empty', 'Produk tidak ditemukan. Coba kata kunci lain.'));
            return;
        }

        matches.forEach((product, index) => {
            const card = makeElement('article', 'catalog-product');
            card.style.animationDelay = `${Math.min(index * 35, 210)}ms`;
            const image = makeElement('img', 'catalog-image');
            image.src = product.image;
            image.alt = product.name;
            image.loading = 'lazy';

            const info = makeElement('div', 'catalog-info');
            info.append(makeElement('h3', 'catalog-name', product.name));
            info.append(makeElement('p', 'catalog-price', formatRupiah(product.price)));
            if (product.is_new) {
                card.append(makeElement('span', 'latest-badge', 'BARU'));
            }
            const button = makeElement('button', 'catalog-action', product.stock > 0 ? '+ Tambahkan' : 'Stok habis');
            button.type = 'button';
            button.disabled = product.stock < 1;
            button.addEventListener('click', () => addToCart(product.id));
            info.append(button);

            card.append(image, info);
            catalogGrid.append(card);
        });
    }

    function addToCart(productId) {
        const product = catalog.find((item) => item.id === Number(productId));
        if (!product || product.stock < 1) return;

        const current = cart.get(product.id)?.quantity || 0;
        cart.set(product.id, { product, quantity: Math.min(current + 1, product.stock) });
        renderCart();
    }

    function changeQuantity(productId, amount) {
        const item = cart.get(productId);
        if (!item) return;
        const quantity = item.quantity + amount;

        if (quantity < 1) {
            cart.delete(productId);
        } else {
            item.quantity = Math.min(quantity, item.product.stock);
        }

        renderCart();
    }

    function renderCart() {
        cartList.replaceChildren();
        cartInputs.replaceChildren();
        const items = [...cart.values()];
        let subtotal = 0;
        let totalItems = 0;

        if (items.length === 0) {
            const empty = makeElement('div', 'cart-empty');
            empty.append(makeElement('span', 'cart-empty-icon', '🛒'));
            empty.append(makeElement('strong', '', 'Keranjang masih kosong'));
            empty.append(makeElement('span', '', 'Tambahkan produk dari katalog di atas.'));
            cartList.append(empty);
        }

        items.forEach(({ product, quantity }, index) => {
            subtotal += product.price * quantity;
            totalItems += quantity;

            const row = makeElement('div', 'cart-row');
            const image = makeElement('img', 'cart-image');
            image.src = product.image;
            image.alt = '';

            const info = makeElement('div', '');
            info.append(makeElement('p', 'cart-product-name', product.name));
            info.append(makeElement('span', 'cart-product-price', `${formatRupiah(product.price)} / barang`));

            const controls = makeElement('div', 'quantity-control');
            const decrease = makeElement('button', '', '−');
            decrease.type = 'button';
            decrease.setAttribute('aria-label', `Kurangi ${product.name}`);
            decrease.addEventListener('click', () => changeQuantity(product.id, -1));
            const quantityValue = makeElement('span', 'quantity-value', String(quantity));
            const increase = makeElement('button', '', '+');
            increase.type = 'button';
            increase.disabled = quantity >= product.stock;
            increase.setAttribute('aria-label', `Tambah ${product.name}`);
            increase.addEventListener('click', () => changeQuantity(product.id, 1));
            controls.append(decrease, quantityValue, increase);

            const lineTotal = makeElement('strong', 'cart-line-total', formatRupiah(product.price * quantity));
            const remove = makeElement('button', 'remove-item', '×');
            remove.type = 'button';
            remove.setAttribute('aria-label', `Hapus ${product.name}`);
            remove.addEventListener('click', () => {
                cart.delete(product.id);
                renderCart();
            });
            row.append(image, info, controls, lineTotal, remove);
            cartList.append(row);

            const idInput = makeElement('input');
            idInput.type = 'hidden';
            idInput.name = `products[${index}][id]`;
            idInput.value = String(product.id);
            const quantityInput = makeElement('input');
            quantityInput.type = 'hidden';
            quantityInput.name = `products[${index}][quantity]`;
            quantityInput.value = String(quantity);
            cartInputs.append(idInput, quantityInput);
        });

        document.getElementById('cart-count').textContent = `${totalItems} barang`;
        document.getElementById('summary-items').textContent = String(totalItems);
        document.getElementById('summary-subtotal').textContent = formatRupiah(subtotal);

        const percentage = Math.min(100, Math.max(0, Number(discountPercentage.value) || 0));
        const nominalDiscount = Math.max(0, Number(discountAmount.value) || 0);
        const discount = Math.min(subtotal, Math.round(subtotal * percentage / 100) + nominalDiscount);
        const tax = Math.max(0, Number(taxAmount.value) || 0);
        const total = Math.max(0, subtotal - discount + tax);
        const paid = Math.max(0, Number(amountPaid.value) || 0);
        const isCash = paymentMethod.value === 'cash';

        document.getElementById('summary-total').textContent = formatRupiah(total);
        document.getElementById('summary-change').textContent = formatRupiah(isCash ? Math.max(0, paid - total) : 0);
        document.getElementById('amount-paid-label').textContent = isCash ? 'Uang diterima (Rp)' : 'Nilai pembayaran (otomatis)';
        amountPaid.disabled = !isCash;
        checkoutButton.disabled = items.length === 0 || (isCash && paid < total);
        holdButton.disabled = items.length === 0;
    }

    searchInput.addEventListener('input', renderCatalog);
    document.getElementById('search-button').addEventListener('click', renderCatalog);
    [discountPercentage, discountAmount, taxAmount, amountPaid].forEach((input) => {
        input.addEventListener('input', renderCart);
    });
    paymentMethod.addEventListener('change', renderCart);
    document.getElementById('clear-cart-button').addEventListener('click', () => {
        if (!cart.size || confirm('Kosongkan keranjang transaksi ini?')) {
            cart.clear();
            document.getElementById('held-transaction-id')?.remove();
            renderCart();
        }
    });

    (Array.isArray(initialCart) ? initialCart : []).forEach((item) => {
        const product = catalog.find((candidate) => candidate.id === Number(item.id));
        const quantity = Number(item.quantity) || 0;
        if (product && quantity > 0) cart.set(product.id, { product, quantity });
    });

    renderCatalog();
    renderCart();
</script>
</body>
</html>
