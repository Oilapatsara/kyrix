@extends('layouts.customer')

@section('title', 'รายละเอียดการเช่า ' . ($rental->rental_code ?? 'KR-' . $rental->rental_id) . ' | KYRIX')

@push('styles')
    <style>
        .order-detail-wrap {
            max-width: 1100px;
            margin: 40px auto 80px;
            padding: 0 24px;
        }


        .top-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .invoice-box {
            background: #fff;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            padding: 36px;
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 24px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 28px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .timeline-wrap {
            margin: 24px 0 32px;
            padding: 20px;
            background: #faf8f5;
            border-radius: var(--radius-md);
            border: 1px solid var(--border);
            overflow-x: auto;
        }

        .timeline-steps {
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-width: 680px;
            position: relative;
        }

        .timeline-line {
            position: absolute;
            top: 20px;
            left: 30px;
            right: 30px;
            height: 4px;
            background: #e2dcd5;
            z-index: 1;
        }

        .timeline-progress {
            position: absolute;
            top: 20px;
            left: 30px;
            height: 4px;
            background: var(--primary);
            z-index: 2;
        }

        .step-node {
            position: relative;
            z-index: 3;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            width: 90px;
        }

        .step-circle {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #fff;
            border: 3px solid #e2dcd5;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: #999;
            margin-bottom: 8px;
        }

        .step-node.completed .step-circle {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
        }

        .step-node.active .step-circle {
            background: #fff;
            border-color: var(--primary);
            color: var(--primary);
            box-shadow: 0 0 0 5px rgba(122, 31, 43, 0.15);
        }

        .step-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
        }

        .step-node.active .step-label,
        .step-node.completed .step-label {
            color: var(--text-main);
            font-weight: 700;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 24px 0;
        }

        .items-table th {
            background: #faf8f5;
            padding: 12px 16px;
            text-align: left;
            font-size: 13px;
            font-weight: 700;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border);
        }

        .items-table td {
            padding: 16px;
            border-bottom: 1px solid var(--border);
            font-size: 14px;
        }

        .breakdown-grid {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 30px;
            margin-top: 30px;
        }

        .info-card {
            background: #faf8f5;
            border-radius: var(--radius-md);
            padding: 20px;
            font-size: 13px;
            line-height: 1.8;
        }

        .info-card h4 {
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .totals-box {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 20px;
        }

        .totals-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            margin-bottom: 8px;
            color: var(--text-muted);
        }

        .totals-row.final {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 2px solid var(--border);
            font-size: 20px;
            font-weight: 800;
            color: var(--primary);
        }

        .slip-img-card {
            max-width: 180px;
            max-height: 180px;
            object-fit: contain;
            border-radius: 8px;
            border: 1px solid var(--border);
            cursor: pointer;
        }

        /* =========================================================
                                   SHIPPING / RETURN TRACKING
                                ========================================================= */
        .shipping-panel {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .shipping-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            padding: 10px 0;
            border-bottom: 1px dashed var(--border);
        }

        .shipping-row:last-child {
            border-bottom: none;
        }

        .shipping-label {
            color: var(--text-muted);
            font-size: 12px;
        }

        .shipping-value {
            text-align: right;
            font-size: 13px;
            font-weight: 700;
            color: var(--text-main);
        }

        .shipping-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 11px;
            border-radius: 999px;
            background: #fff7ed;
            color: #9a3412;
            border: 1px solid #fed7aa;
            font-size: 12px;
            font-weight: 700;
        }

        .tracking-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 40px;
            padding: 0 16px;
            border-radius: 9px;
            background: var(--primary);
            color: #fff !important;
            text-decoration: none !important;
            font-size: 12.5px;
            font-weight: 700;
            transition: .18s ease;
        }

        .tracking-btn:hover {
            filter: brightness(.95);
            transform: translateY(-1px);
        }

        .return-due-box {
            margin-top: 2px;
            padding: 15px 16px;
            border-radius: 10px;
            background: #faf8f5;
            border: 1px solid var(--border);
        }

        .return-due-box.overdue {
            background: #fff1f2;
            border-color: #fecdd3;
        }

        .return-due-title {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 6px;
            color: var(--primary);
            font-size: 13px;
            font-weight: 800;
        }

        .return-due-box.overdue .return-due-title {
            color: #b42318;
        }

        .return-due-time {
            font-size: 19px;
            font-weight: 800;
            color: var(--primary);
        }

        .return-due-box.overdue .return-due-time {
            color: #b42318;
        }

        .return-due-note {
            margin-top: 5px;
            font-size: 11.5px;
            color: var(--text-muted);
            line-height: 1.6;
        }

        /* =========================================================
                                   RETURN SHIPPING
                                ========================================================= */
        .return-shipping-box {
            margin-top: 14px;
            padding-top: 16px;
            border-top: 1px dashed var(--border);
        }

        .return-shipping-title {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #166534;
            font-size: 14px;
            font-weight: 800;
            margin-bottom: 12px;
        }

        .return-shipping-status {
            background: #f0fdf4;
            color: #166534;
            border-color: #bbf7d0;
        }

        #returnSection {
            transition: .18s ease;
        }

        #returnCarrierWrap,
        #returnTrackingWrap {
            transition: .18s ease;
        }

        /* =========================================================
                                   RETURN STATUS
                                ========================================================= */
        .return-status-card {
            margin-top: 36px;
            padding: 24px;
            border-radius: var(--radius-md);
            border: 1px solid var(--border);
            background: #faf8f5;
        }

        .return-status-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .return-status-title {
            margin: 0;
            font-size: 18px;
            font-weight: 800;
            color: var(--text-main);
        }

        .return-status-subtitle {
            margin: 5px 0 0;
            font-size: 12.5px;
            color: var(--text-muted);
            line-height: 1.7;
        }

        .return-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 13px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
            white-space: nowrap;
        }

        .return-status-badge.waiting {
            background: #fff7ed;
            color: #9a3412;
            border: 1px solid #fed7aa;
        }

        .return-status-badge.success {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .return-status-badge.neutral {
            background: #f5f5f5;
            color: #666;
            border: 1px solid #e5e5e5;
        }

        .return-request-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .return-request-field {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 13px 14px;
        }

        .return-request-label {
            font-size: 11px;
            color: var(--text-muted);
            margin-bottom: 4px;
        }

        .return-request-value {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-main);
            word-break: break-word;
        }

        .return-request-value.green {
            color: #166534;
        }

        .return-request-note {
            margin-top: 12px;
            padding: 11px 13px;
            border-radius: 9px;
            background: #fff;
            border: 1px dashed var(--border);
            color: var(--text-muted);
            font-size: 11.5px;
            line-height: 1.7;
        }

        .return-request-form {
            margin-top: 4px;
        }

        .return-method-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 16px;
        }

        .return-method-card {
            display: block;
            border: 1px solid var(--border);
            border-radius: 11px;
            background: #fff;
            padding: 14px;
            cursor: pointer;
            transition: .18s ease;
        }

        .return-method-card:hover {
            border-color: var(--primary);
            background: #fffdfd;
        }

        .return-method-card input {
            margin-right: 7px;
            accent-color: var(--primary);
        }

        .return-method-name {
            font-size: 13px;
            font-weight: 800;
            color: var(--text-main);
        }

        .return-method-desc {
            margin-top: 5px;
            font-size: 11px;
            color: var(--text-muted);
            line-height: 1.5;
        }

        .return-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 14px;
        }

        .return-form-group label {
            display: block;
            margin-bottom: 6px;
            font-size: 12.5px;
            font-weight: 650;
            color: var(--text-main);
        }

        .return-form-group label span {
            color: #b42318;
        }

        .return-form-group .input-field {
            margin-top: 0;
        }

        .return-submit-row {
            display: flex;
            justify-content: flex-end;
            margin-top: 6px;
        }

        .return-submit-btn {
            height: 44px;
            padding: 0 20px;
            border: none;
            border-radius: 9px;
            background: linear-gradient(135deg, var(--primary), #5c1522);
            color: #fff;
            font-family: inherit;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: .18s ease;
        }

        .return-submit-btn:hover {
            filter: brightness(.96);
            transform: translateY(-1px);
        }

        .return-helper {
            margin-top: 10px;
            font-size: 11px;
            color: var(--text-muted);
            line-height: 1.6;
        }

        @media (max-width: 900px) {

            .return-form-grid,
            .return-request-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 600px) {
            .return-method-grid {
                grid-template-columns: 1fr;
            }

            .return-submit-row {
                display: block;
            }

            .return-submit-btn {
                width: 100%;
            }
        }

        /* =========================================================
                                   PRINT RECEIPT
                                ========================================================= */
        .print-receipt {
            display: none;
        }

        /* =========================================================
                                   CANCEL RENTAL
                                ========================================================= */
        .cancel-rental-box {
            margin-top: 24px;
            padding: 18px 20px;
            background: #fff8f7;
            border: 1px solid #f0d0cc;
            border-radius: var(--radius-md);
        }

        .cancel-rental-content {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .cancel-rental-icon {
            width: 42px;
            height: 42px;
            min-width: 42px;
            border-radius: 50%;
            background: #fdeceb;
            color: #b42318;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cancel-rental-text {
            flex: 1;
            min-width: 0;
        }

        .cancel-rental-text h3 {
            margin: 0 0 3px;
            color: #5f161e;
            font-size: 15px;
            font-weight: 800;
        }

        .cancel-rental-text p {
            margin: 0;
            color: #7c6f6a;
            font-size: 12.5px;
        }

        .cancel-payment-note {
            margin-top: 5px !important;
            color: #9a5b08 !important;
        }

        .cancel-payment-note i {
            margin-right: 3px;
        }

        .cancel-rental-btn {
            height: 40px;
            padding: 0 18px;
            border: 1px solid #dfb0ab;
            border-radius: 9px;
            background: #fff;
            color: #b42318;
            font-family: inherit;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            white-space: nowrap;
            transition: all .18s ease;
        }

        .cancel-rental-btn:hover {
            background: #b42318;
            border-color: #b42318;
            color: #fff;
            box-shadow: 0 5px 15px rgba(180, 35, 24, .18);
        }

        .cancel-modal {
            position: fixed;
            inset: 0;
            z-index: 10000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .cancel-modal-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(32, 18, 20, .58);
            backdrop-filter: blur(4px);
        }

        .cancel-modal-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 520px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 25px 70px rgba(0, 0, 0, .22);
            overflow: hidden;
            animation: cancelModalIn .2s ease;
        }

        @keyframes cancelModalIn {
            from {
                opacity: 0;
                transform: translateY(10px) scale(.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .cancel-modal-header {
            padding: 18px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .cancel-modal-title-wrap {
            display: flex;
            align-items: center;
            gap: 11px;
        }

        .cancel-modal-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #fdeceb;
            color: #b42318;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cancel-modal-header h3 {
            margin: 0;
            color: #2a2421;
            font-size: 16px;
            font-weight: 800;
        }

        .cancel-modal-header p {
            margin: 2px 0 0;
            color: #8a817b;
            font-size: 11px;
        }

        .cancel-modal-close {
            width: 32px;
            height: 32px;
            border: none;
            background: transparent;
            border-radius: 8px;
            color: #8a817b;
            font-size: 22px;
            cursor: pointer;
        }

        .cancel-modal-close:hover {
            background: #f8f3f1;
            color: #5f171f;
        }

        .cancel-modal-body {
            padding: 20px;
        }

        .cancel-warning {
            display: flex;
            gap: 10px;
            padding: 13px 14px;
            margin-bottom: 18px;
            background: #fff8e8;
            border: 1px solid #f0dfad;
            border-radius: 10px;
            color: #805b13;
        }

        .cancel-warning>i {
            margin-top: 2px;
        }

        .cancel-warning strong {
            display: block;
            font-size: 13px;
        }

        .cancel-warning p {
            margin: 3px 0 0;
            font-size: 12px;
            line-height: 1.5;
        }

        .cancel-form-group label {
            display: block;
            margin-bottom: 7px;
            color: #2a2421;
            font-size: 13px;
            font-weight: 700;
        }

        .cancel-form-group label span {
            color: #b42318;
        }

        .cancel-reason-textarea {
            margin-top: 10px;
            resize: vertical;
            min-height: 90px;
        }

        .cancel-modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 14px 20px;
            background: #faf8f5;
            border-top: 1px solid var(--border);
        }

        .cancel-back-btn,
        .cancel-confirm-btn {
            height: 40px;
            padding: 0 17px;
            border-radius: 8px;
            font-family: inherit;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
        }

        .cancel-back-btn {
            background: #fff;
            color: #736b66;
            border: 1px solid var(--border-strong, #d5cdc6);
        }

        .cancel-back-btn:hover {
            background: #f5f1ee;
        }

        .cancel-confirm-btn {
            border: 1px solid #b42318;
            background: #b42318;
            color: #fff;
            box-shadow: 0 4px 12px rgba(180, 35, 24, .18);
        }

        .cancel-confirm-btn:hover {
            background: #941b12;
            border-color: #941b12;
        }

        @media (max-width: 600px) {
            .cancel-rental-content {
                align-items: flex-start;
                flex-wrap: wrap;
            }

            .cancel-rental-btn {
                width: 100%;
                margin-left: 56px;
            }

            .cancel-modal {
                padding: 12px;
            }

            .cancel-modal-footer {
                flex-direction: column-reverse;
            }

            .cancel-back-btn,
            .cancel-confirm-btn {
                width: 100%;
            }
        }

        /* =========================================================
                                   PRINT
                                ========================================================= */
        @media print {
            @page {
                size: A4 portrait;
                margin: 8mm;
            }

            html,
            body {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                min-height: 0 !important;
                background: #fff !important;
            }

            body.printing>* {
                display: none !important;
            }

            body.printing>.print-receipt {
                display: block !important;
            }

            .print-receipt {
                position: static !important;
                width: 100% !important;
                max-width: none !important;
                height: auto !important;
                min-height: 0 !important;
                max-height: none !important;
                overflow: visible !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
                color: #111 !important;
                font-family: Arial, Tahoma, sans-serif !important;
                font-size: 10px !important;
                line-height: 1.35 !important;
                box-sizing: border-box !important;
                page-break-after: avoid !important;
                break-after: avoid !important;
            }

            .print-receipt * {
                box-sizing: border-box !important;
            }

            .print-header {
                text-align: center;
                padding-bottom: 8px;
                margin-bottom: 9px;
                border-bottom: 2px solid #7a1f2b;
            }

            .print-logo {
                font-size: 26px;
                font-weight: 900;
                color: #7a1f2b;
                letter-spacing: 2px;
            }

            .print-subtitle {
                font-size: 8px;
                letter-spacing: 2px;
                color: #777;
                margin-top: 1px;
            }

            .print-title {
                font-size: 16px;
                font-weight: 800;
                margin-top: 6px;
            }

            .print-code {
                color: #7a1f2b;
                font-size: 13px;
                font-weight: 800;
                margin-top: 2px;
            }

            .print-info-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 7px;
                margin-bottom: 8px;
            }

            .print-info-box {
                border: 1px solid #d7d7d7;
                border-radius: 5px;
                padding: 7px 8px;
            }

            .print-section-title {
                color: #7a1f2b;
                font-size: 10px;
                font-weight: 800;
                margin-bottom: 4px;
            }

            .print-table {
                width: 100%;
                border-collapse: collapse;
                margin: 5px 0 8px;
            }

            .print-table th {
                background: #f3f1ef;
                border: 1px solid #d5d5d5;
                padding: 4px 5px;
                font-size: 9px;
                text-align: left;
            }

            .print-table td {
                border: 1px solid #d5d5d5;
                padding: 5px;
                font-size: 9px;
            }

            .print-center {
                text-align: center !important;
            }

            .print-right {
                text-align: right !important;
            }

            .print-summary {
                width: 300px;
                margin-left: auto;
                border: 1px solid #d5d5d5;
                border-radius: 5px;
                padding: 7px 9px;
            }

            .print-summary-row {
                display: flex;
                justify-content: space-between;
                gap: 15px;
                margin-bottom: 3px;
            }

            .print-summary-final {
                display: flex;
                justify-content: space-between;
                gap: 15px;
                margin-top: 5px;
                padding-top: 6px;
                border-top: 2px solid #7a1f2b;
                color: #7a1f2b;
                font-size: 13px;
                font-weight: 900;
            }

            .print-payment {
                margin-top: 8px;
                border: 1px solid #d5d5d5;
                border-radius: 5px;
                padding: 7px 9px;
            }

            .print-status {
                display: inline-block;
                padding: 2px 7px;
                border-radius: 10px;
                background: #fff7ed;
                border: 1px solid #fed7aa;
                color: #9a3412;
                font-weight: 700;
                font-size: 9px;
            }

            .print-note {
                margin-top: 7px;
                background: #faf8f5;
                border: 1px solid #ddd;
                border-radius: 4px;
                padding: 6px 8px;
                font-size: 8.5px;
            }

            .print-footer {
                margin-top: 7px;
                padding-top: 6px;
                border-top: 1px solid #ddd;
                text-align: center;
                color: #777;
                font-size: 8px;
            }
        }

        @media (max-width: 800px) {
            .breakdown-grid {
                grid-template-columns: 1fr;
            }

            .invoice-box {
                padding: 24px;
            }

            .items-table {
                display: block;
                overflow-x: auto;
            }
        }

        @media (max-width: 600px) {
            .order-detail-wrap {
                padding: 0 14px;
            }

            .top-actions {
                flex-direction: column;
                align-items: stretch;
                gap: 10px;
            }

            .top-actions>div {
                text-align: right;
            }

            .invoice-box {
                padding: 18px;
            }

            .invoice-header {
                flex-direction: column;
            }
        }
    </style>
@endpush

@section('content')

    <div class="order-detail-wrap">

        @php
            /*
        |--------------------------------------------------------------------------
        | RETURN STATUS
        |--------------------------------------------------------------------------
        |
        | ระบบใหม่:
        |
        | not_returned       = ยังไม่คืน
        | returned_requested = แจ้งคืนแล้ว
        | returned           = คืนชุดแล้ว
        |
        | รองรับรายการเก่าที่ไม่มี return_status
        |--------------------------------------------------------------------------
        */

            $returnStatus = trim((string) ($rental->return_status ?? ''));

            if ($returnStatus === '') {
                if ($rental->status === 'pending_return') {
                    $returnStatus = 'returned_requested';
                } elseif (in_array($rental->status, ['returned', 'completed'], true)) {
                    $returnStatus = 'returned';
                } else {
                    $returnStatus = 'not_returned';
                }
            }

            $returnMethod = trim((string) ($rental->return_method ?? ''));

            $returnTrackingNumber = trim(
                (string) ($rental->return_tracking_number ?: $rental->return_tracking_no ?? ''),
            );

            $returnCarrier = trim((string) ($rental->return_shipping_carrier ?? ''));

            $returnShippingStatus = trim((string) ($rental->return_shipping_status ?? ''));

            /*
        |--------------------------------------------------------------------------
        | รองรับข้อมูลเก่า
        |--------------------------------------------------------------------------
        */
            if ($returnMethod === '') {
                $returnMethod =
                    $returnTrackingNumber !== '' ? 'parcel' : ($returnStatus === 'returned_requested' ? 'store' : '');
            }

            $returnMethodText = match ($returnMethod) {
                'parcel' => 'ส่งพัสดุ',
                'store' => 'คืนที่ร้าน',
                default => '-',
            };

            $returnStatusText = match ($returnStatus) {
                'not_returned' => 'ยังไม่คืน',
                'returned_requested' => 'แจ้งคืนแล้ว',
                'returned' => 'คืนชุดแล้ว',
                default => 'ยังไม่คืน',
            };

            $canRequestReturn =
                in_array($rental->status, ['confirmed', 'ready_pickup', 'renting'], true) &&
                $returnStatus === 'not_returned';

            $isReturnRequested = $returnStatus === 'returned_requested';

            $isReturnCompleted = $returnStatus === 'returned';
        @endphp

        {{-- TOP ACTIONS --}}
        <div class="top-actions">

            <a href="{{ route('rentals.index') }}" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-arrow-left"></i>
                กลับไปหน้ารายการการเช่า
            </a>

            <div>
                <button type="button" id="printReceiptButton" class="btn btn-secondary btn-sm">
                    <i class="fa-solid fa-print"></i>
                    พิมพ์ใบเสร็จ
                </button>
            </div>

        </div>

        <div class="invoice-box">

            {{-- HEADER --}}
            <div class="invoice-header">

                <div>

                    <span class="logo" style="font-size: 24px;">
                        KYRIX
                    </span>

                    <div
                        style="
                        font-size: 12px;
                        color: var(--gold);
                        font-weight: 600;
                        letter-spacing: 2px;
                        margin-bottom: 12px;
                    ">
                        DRESS RENTAL BOUTIQUE
                    </div>

                    <div
                        style="
                        font-size: 13px;
                        color: var(--text-muted);
                    ">
                        77 ตำบลในเมือง อำเภอเมือง จังหวัดนครราชสีมา 30000
                        | โทร: 0652599072
                    </div>

                </div>

                <div style="text-align: right;">

                    <span
                        style="
                        font-size: 12px;
                        color: var(--text-muted);
                    ">
                        เลขที่การเช่า (Rental Code)
                    </span>

                    <h2
                        style="
                        font-family: 'Plus Jakarta Sans', monospace;
                        font-size: 24px;
                        color: var(--primary);
                        font-weight: 800;
                        margin: 2px 0;
                    ">
                        {{ $rental->rental_code ?? 'KR-2026-' . $rental->rental_id }}
                    </h2>

                    <div style="margin-top: 6px;">

                        <span class="badge {{ $rental->status_badge_class }}"
                            style="
                            font-size: 13px;
                            padding: 6px 14px;
                        ">
                            <i class="fa-solid fa-circle-dot"></i>
                            {{ $rental->status_label }}
                        </span>

                    </div>

                </div>

            </div>

            {{-- TIMELINE --}}
            @php

                $step = $rental->step_index;

                $progressPercent = match ($step) {
                    1 => 0,
                    2 => 17,
                    3 => 34,
                    4 => 50,
                    5 => 67,
                    6 => 84,
                    7 => 100,
                    default => 0,
                };

                $stages = [
                    1 => [
                        'label' => 'รอชำระ',
                        'icon' => 'fa-credit-card',
                    ],
                    2 => [
                        'label' => 'รอตรวจสอบ',
                        'icon' => 'fa-receipt',
                    ],
                    3 => [
                        'label' => 'ยืนยันการเช่า',
                        'icon' => 'fa-check',
                    ],
                    4 => [
                        'label' => 'รอรับชุด',
                        'icon' => 'fa-box',
                    ],
                    5 => [
                        'label' => 'กำลังเช่า',
                        'icon' => 'fa-person-dress',
                    ],
                    6 => [
                        'label' => 'คืนแล้ว',
                        'icon' => 'fa-shield-heart',
                    ],
                    7 => [
                        'label' => 'เสร็จสิ้น',
                        'icon' => 'fa-circle-check',
                    ],
                ];

            @endphp

            <div class="timeline-wrap">

                <div class="timeline-steps">

                    <div class="timeline-line"></div>

                    <div class="timeline-progress" style="width: {{ $progressPercent }}%;"></div>

                    @foreach ($stages as $stageNum => $stage)
                        @php
                            $isCompleted = $step > $stageNum;
                            $isActive = $step === $stageNum;
                        @endphp

                        <div class="step-node {{ $isCompleted ? 'completed' : ($isActive ? 'active' : '') }}">

                            <div class="step-circle">

                                @if ($isCompleted)
                                    <i class="fa-solid fa-check"></i>
                                @else
                                    <i class="fa-solid {{ $stage['icon'] }}"></i>
                                @endif

                            </div>

                            <div class="step-label">
                                {{ $stage['label'] }}
                            </div>

                        </div>
                    @endforeach

                </div>

            </div>

            {{-- DATES --}}
            <div
                style="
                background: #faf8f5;
                padding: 16px 20px;
                border-radius: 10px;
                display: flex;
                justify-content: space-around;
                text-align: center;
                border: 1px solid var(--border);
                margin-bottom: 24px;
                flex-wrap: wrap;
                gap: 14px;
            ">

                <div>

                    <span
                        style="
                        font-size: 12px;
                        color: var(--text-muted);
                        display: block;
                    ">
                        วันที่ทำรายการ
                    </span>

                    <strong>
                        {{ $rental->rental_date ? date('d/m/Y', strtotime($rental->rental_date)) : $rental->created_at->format('d/m/Y') }}
                    </strong>

                </div>

                <div>

                    <span
                        style="
                        font-size: 12px;
                        color: var(--text-muted);
                        display: block;
                    ">
                        วันเริ่มรับชุด
                    </span>

                    <strong style="color: var(--primary);">
                        {{ date('d/m/Y', strtotime($rental->start_date)) }}
                    </strong>

                </div>

                <div>

                    <span
                        style="
                        font-size: 12px;
                        color: var(--text-muted);
                        display: block;
                    ">
                        วันกำหนดส่งคืนชุด
                    </span>

                    <strong style="color: #b91c1c;">
                        {{ date('d/m/Y', strtotime($rental->end_date)) }}
                    </strong>

                </div>

                <div>

                    <span
                        style="
                        font-size: 12px;
                        color: var(--text-muted);
                        display: block;
                    ">
                        วิธีรับชุด
                    </span>

                    <strong>
                        {{ $rental->delivery_method === 'delivery' ? 'จัดส่งถึงที่อยู่' : 'รับที่หน้าร้าน KYRIX' }}
                    </strong>

                </div>

            </div>

            {{-- RENTAL ITEMS --}}
            <table class="items-table">

                <thead>

                    <tr>

                        <th>
                            ชุดที่เช่า
                        </th>

                        <th>
                            ขนาด / สี
                        </th>

                        <th>
                            ระยะเวลา
                        </th>

                        <th>
                            ค่าเช่า / ครั้ง
                        </th>

                        <th style="text-align: right;">
                            ยอดรวม
                        </th>

                        @if ($rental->can_review)
                            <th style="text-align: center;">
                                รีวิว
                            </th>
                        @endif

                    </tr>

                </thead>

                <tbody>

                    @foreach ($rental->details as $detail)
                        <tr>

                            <td>

                                <div
                                    style="
                                    display: flex;
                                    align-items: center;
                                    gap: 12px;
                                ">

                                    <img src="{{ $detail->product->main_image_url ?? '' }}"
                                        style="
                                        width: 50px;
                                        height: 60px;
                                        border-radius: 6px;
                                        object-fit: cover;
                                    "
                                        alt="dress">

                                    <div>

                                        <a href="{{ route('products.show', $detail->product_id) }}"
                                            style="
                                            font-weight: 700;
                                            color: var(--text-main);
                                        ">
                                            {{ $detail->product->product_name ?? 'ชุดเช่า' }}
                                        </a>

                                        <div
                                            style="
                                            font-size: 11px;
                                            color: var(--text-muted);
                                        ">
                                            รหัสชุด:
                                            {{ $detail->product->product_code ?? '-' }}
                                        </div>

                                    </div>

                                </div>

                            </td>

                            <td>

                                <div>
                                    ไซซ์:
                                    <strong>
                                        {{ $detail->selected_size ?? 'M' }}
                                    </strong>
                                </div>

                                <div
                                    style="
                                    font-size: 12px;
                                    color: var(--text-muted);
                                ">
                                    สี:
                                    {{ $detail->selected_color ?? 'ตามแบบ' }}
                                </div>

                            </td>

                            <td>
                                {{ $detail->rental_days ?? 1 }} วัน
                            </td>

                            <td>
                                ฿{{ number_format($detail->price, 2) }}
                            </td>

                            <td
                                style="
                                text-align: right;
                                font-weight: 700;
                                color: var(--primary);
                            ">
                                ฿{{ number_format($detail->subtotal, 2) }}
                            </td>

                            @if ($rental->can_review)
                                <td style="text-align: center;">

                                    <a href="{{ route('reviews.create', [
                                        'rental' => $rental->rental_id,
                                        'product' => $detail->product_id,
                                    ]) }}"
                                        class="btn btn-gold btn-sm">
                                        <i class="fa-solid fa-star"></i>
                                        เขียนรีวิว
                                    </a>

                                </td>
                            @endif

                        </tr>
                    @endforeach

                </tbody>

            </table>

            {{-- DETAILS --}}
            <div class="breakdown-grid">

                {{-- DELIVERY --}}
                <div
                    style="
                    display: flex;
                    flex-direction: column;
                    gap: 16px;
                ">

                    <div class="info-card">

                        <h4>
                            <i class="fa-solid fa-truck"></i>
                            ข้อมูลการจัดส่ง / ติดตามพัสดุ
                        </h4>

                        <div class="shipping-panel">

                            <div class="shipping-row">

                                <div class="shipping-label">
                                    ผู้รับ
                                </div>

                                <div class="shipping-value">
                                    {{ trim(($rental->customer->first_name ?? '') . ' ' . ($rental->customer->last_name ?? '')) ?: 'ลูกค้า' }}
                                </div>

                            </div>

                            <div class="shipping-row">

                                <div class="shipping-label">
                                    เบอร์โทรติดต่อ
                                </div>

                                <div class="shipping-value">
                                    {{ $rental->recipient_phone ?? ($rental->customer->phone ?? '-') }}
                                </div>

                            </div>

                            <div class="shipping-row">

                                <div class="shipping-label">
                                    สถานที่รับ/จัดส่ง
                                </div>

                                <div class="shipping-value">
                                    {{ $rental->delivery_address ?: 'รับที่หน้าร้าน KYRIX' }}
                                </div>

                            </div>

                            {{-- FORWARD SHIPPING --}}
                            @if ($rental->shipping_carrier)
                                <div class="shipping-row">

                                    <div class="shipping-label">
                                        บริษัทขนส่ง
                                    </div>

                                    <div class="shipping-value">
                                        {{ $rental->shipping_carrier }}
                                    </div>

                                </div>
                            @endif

                            @if ($rental->tracking_number)
                                <div class="shipping-row">

                                    <div class="shipping-label">
                                        เลขพัสดุจัดส่ง
                                    </div>

                                    <div class="shipping-value" style="color:var(--primary);">
                                        <i class="fa-solid fa-barcode"></i>
                                        {{ $rental->tracking_number }}
                                    </div>

                                </div>
                            @endif

                            @if ($rental->shipping_status)

                                <div class="shipping-row">

                                    <div class="shipping-label">
                                        สถานะการจัดส่ง
                                    </div>

                                    <div class="shipping-value">

                                        <span class="shipping-status-badge">

                                            @if ($rental->shipping_status === 'กำลังเตรียมสินค้า')
                                                <i class="fa-solid fa-box"></i>
                                            @elseif ($rental->shipping_status === 'ส่งพัสดุแล้ว')
                                                <i class="fa-solid fa-box-open"></i>
                                            @elseif ($rental->shipping_status === 'กำลังขนส่ง')
                                                <i class="fa-solid fa-truck-fast"></i>
                                            @elseif ($rental->shipping_status === 'กำลังนำจ่าย')
                                                <i class="fa-solid fa-location-dot"></i>
                                            @elseif ($rental->shipping_status === 'จัดส่งสำเร็จ')
                                                <i class="fa-solid fa-circle-check"></i>
                                            @else
                                                <i class="fa-solid fa-circle-info"></i>
                                            @endif

                                            {{ $rental->shipping_status_label }}

                                        </span>

                                    </div>

                                </div>

                            @endif

                            @if ($rental->shipped_at)
                                <div class="shipping-row">

                                    <div class="shipping-label">
                                        วันที่ส่งพัสดุ
                                    </div>

                                    <div class="shipping-value">
                                        {{ $rental->shipped_at->format('d/m/Y H:i') }} น.
                                    </div>

                                </div>
                            @endif

                            @if ($rental->estimated_delivery_at)
                                <div class="shipping-row">

                                    <div class="shipping-label">
                                        คาดว่าจะได้รับ
                                    </div>

                                    <div class="shipping-value" style="color:var(--primary);">
                                        {{ $rental->estimated_delivery_at->format('d/m/Y H:i') }} น.
                                    </div>

                                </div>
                            @endif

                            @if ($rental->tracking_url && $rental->tracking_number)
                                <div style="margin-top:4px;">

                                    <a href="{{ $rental->tracking_url }}" target="_blank" rel="noopener noreferrer"
                                        class="tracking-btn">
                                        <i class="fa-solid fa-location-arrow"></i>
                                        ติดตามพัสดุ
                                    </a>

                                </div>
                            @elseif ($rental->tracking_number)
                                <div
                                    style="
                                    margin-top:4px;
                                    font-size:11.5px;
                                    color:var(--text-muted);
                                    line-height:1.6;
                                ">
                                    <i class="fa-solid fa-circle-info"></i>
                                    ร้านยังไม่ได้ระบุลิงก์สำหรับติดตามพัสดุ
                                </div>
                            @endif

                            {{-- =====================================================
                             RETURN SHIPPING
                        ====================================================== --}}

                            @if (
                                $returnStatus !== 'not_returned' ||
                                    $returnTrackingNumber ||
                                    $returnCarrier ||
                                    $returnShippingStatus ||
                                    $rental->return_requested_at ||
                                    $rental->return_received_at)

                                <div class="return-shipping-box">

                                    <div class="return-shipping-title">

                                        <i class="fa-solid fa-box-archive"></i>

                                        ข้อมูลการคืนชุด

                                    </div>

                                    {{-- RETURN STATUS --}}
                                    <div class="shipping-row">

                                        <div class="shipping-label">
                                            สถานะการคืน
                                        </div>

                                        <div class="shipping-value">

                                            @if ($returnStatus === 'returned')
                                                <span class="return-status-badge success">
                                                    <i class="fa-solid fa-circle-check"></i>
                                                    คืนชุดแล้ว
                                                </span>
                                            @elseif ($returnStatus === 'returned_requested')
                                                <span class="return-status-badge waiting">
                                                    <i class="fa-solid fa-clock"></i>
                                                    แจ้งคืนแล้ว
                                                </span>
                                            @else
                                                <span class="return-status-badge neutral">
                                                    <i class="fa-solid fa-shirt"></i>
                                                    ยังไม่คืน
                                                </span>
                                            @endif

                                        </div>

                                    </div>

                                    {{-- RETURN METHOD --}}
                                    @if ($returnMethod !== '')
                                        <div class="shipping-row">

                                            <div class="shipping-label">
                                                วิธีคืนชุด
                                            </div>

                                            <div class="shipping-value">
                                                {{ $returnMethodText }}
                                            </div>

                                        </div>
                                    @endif

                                    {{-- REQUESTED AT --}}
                                    @if ($rental->return_requested_at)
                                        <div class="shipping-row">

                                            <div class="shipping-label">
                                                วันที่แจ้งคืน
                                            </div>

                                            <div class="shipping-value">
                                                {{ $rental->return_requested_at->format('d/m/Y H:i') }} น.
                                            </div>

                                        </div>
                                    @endif

                                    {{-- PARCEL --}}
                                    @if ($returnMethod === 'parcel')

                                        @if ($returnCarrier)
                                            <div class="shipping-row">

                                                <div class="shipping-label">
                                                    บริษัทขนส่ง
                                                </div>

                                                <div class="shipping-value">
                                                    {{ $returnCarrier }}
                                                </div>

                                            </div>
                                        @endif

                                        @if ($returnTrackingNumber)
                                            <div class="shipping-row">

                                                <div class="shipping-label">
                                                    เลขพัสดุส่งคืน
                                                </div>

                                                <div class="shipping-value" style="color:#166534;">
                                                    <i class="fa-solid fa-barcode"></i>
                                                    {{ $returnTrackingNumber }}
                                                </div>

                                            </div>
                                        @endif

                                        @if ($returnShippingStatus)

                                            <div class="shipping-row">

                                                <div class="shipping-label">
                                                    สถานะพัสดุส่งคืน
                                                </div>

                                                <div class="shipping-value">

                                                    <span class="shipping-status-badge return-shipping-status">

                                                        @if ($returnShippingStatus === 'ลูกค้ายังไม่ได้ส่งคืน')
                                                            <i class="fa-solid fa-clock"></i>
                                                        @elseif ($returnShippingStatus === 'ส่งพัสดุแล้ว' || $returnShippingStatus === 'ลูกค้าส่งคืนแล้ว')
                                                            <i class="fa-solid fa-box-open"></i>
                                                        @elseif ($returnShippingStatus === 'กำลังขนส่ง')
                                                            <i class="fa-solid fa-truck-fast"></i>
                                                        @elseif ($returnShippingStatus === 'กำลังนำจ่าย')
                                                            <i class="fa-solid fa-location-dot"></i>
                                                        @elseif ($returnShippingStatus === 'ถึงร้านแล้ว')
                                                            <i class="fa-solid fa-circle-check"></i>
                                                        @else
                                                            <i class="fa-solid fa-circle-info"></i>
                                                        @endif

                                                        {{ $returnShippingStatus }}

                                                    </span>

                                                </div>

                                            </div>

                                        @endif

                                        @if ($rental->return_shipped_at)
                                            <div class="shipping-row">

                                                <div class="shipping-label">
                                                    วันที่ส่งคืน
                                                </div>

                                                <div class="shipping-value">
                                                    {{ $rental->return_shipped_at->format('d/m/Y H:i') }} น.
                                                </div>

                                            </div>
                                        @endif

                                        @if ($rental->return_estimated_delivery_at)
                                            <div class="shipping-row">

                                                <div class="shipping-label">
                                                    คาดว่าจะถึงร้าน
                                                </div>

                                                <div class="shipping-value" style="color:var(--primary);">
                                                    {{ $rental->return_estimated_delivery_at->format('d/m/Y H:i') }} น.
                                                </div>

                                            </div>
                                        @endif

                                        @if (!empty($returnTrackingUrl))
                                            <div style="margin-top:12px;">

                                                <a href="{{ $returnTrackingUrl }}" target="_blank"
                                                    rel="noopener noreferrer" class="tracking-btn">

                                                    <i class="fa-solid fa-location-arrow"></i>

                                                    ติดตามพัสดุส่งคืน

                                                </a>

                                            </div>
                                        @elseif ($returnTrackingNumber && $returnCarrier)
                                            <div
                                                style="
                                                margin-top:12px;
                                                padding:10px 12px;
                                                border-radius:9px;
                                                background:#fff;
                                                border:1px solid var(--border);
                                                color:var(--text-muted);
                                                font-size:11.5px;
                                                line-height:1.6;
                                            ">

                                                <i class="fa-solid fa-circle-info"></i>

                                                ระบบยังไม่พบลิงก์ติดตามอัตโนมัติของบริษัทขนส่งนี้

                                            </div>
                                        @endif

                                    @endif

                                    {{-- STORE RETURN --}}
                                    @if ($returnMethod === 'store')
                                        <div
                                            style="
                                            margin-top:8px;
                                            padding:11px 12px;
                                            border-radius:9px;
                                            background:#fff;
                                            border:1px solid var(--border);
                                            color:var(--text-muted);
                                            font-size:11.5px;
                                            line-height:1.6;
                                        ">

                                            <i class="fa-solid fa-store"></i>

                                            ลูกค้าเลือกนำชุดมาคืนที่ร้าน KYRIX

                                            <div
                                                style="
                                                    margin-top:7px;
                                                    padding-left:22px;
                                                    color:var(--text-muted);
                                                ">
                                                <strong style="color:var(--text-main);">
                                                    ที่อยู่ร้าน:
                                                </strong>
                                                77 ตำบลในเมือง อำเภอเมือง
                                                จังหวัดนครราชสีมา 30000

                                                <div
                                                    style="
                                                        margin-top:6px;
                                                        color:var(--text-muted);
                                                    ">
                                                    กรุณานำชุดมาคืนที่ร้านตามวันและเวลาที่กำหนด
                                                </div>
                                            </div>

                                            <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode('77 ตำบลในเมือง อำเภอเมือง จังหวัดนครราชสีมา 30000') }}"
                                                target="_blank" rel="noopener noreferrer"
                                                style="
                                                    display:inline-flex;
                                                    align-items:center;
                                                    gap:6px;
                                                    margin-top:8px;
                                                    margin-left:22px;
                                                    padding:7px 11px;
                                                    border-radius:8px;
                                                    background:#fff;
                                                    border:1px solid var(--border);
                                                    color:var(--primary);
                                                    text-decoration:none;
                                                    font-size:11.5px;
                                                    font-weight:700;
                                                ">
                                                <i class="fa-solid fa-map-location-dot"></i>
                                                เปิดแผนที่ร้าน
                                            </a>

                                        </div>
                                    @endif

                                    {{-- RECEIVED AT --}}
                                    @if ($rental->return_received_at)
                                        <div
                                            style="
                                            margin-top:10px;
                                            padding:11px 12px;
                                            border-radius:9px;
                                            background:#f0fdf4;
                                            border:1px solid #bbf7d0;
                                            color:#166534;
                                            font-size:11.5px;
                                            line-height:1.6;
                                        ">

                                            <i class="fa-solid fa-circle-check"></i>

                                            ร้านรับคืนชุดแล้ว เมื่อ

                                            <strong>
                                                {{ $rental->return_received_at->format('d/m/Y H:i') }} น.
                                            </strong>

                                        </div>
                                    @endif

                                </div>

                            @endif

                        </div>

                    </div>

                    {{-- RETURN DUE --}}
                    <div class="info-card">

                        <h4>
                            <i class="fa-solid fa-calendar-check"></i>
                            กำหนดคืนชุด
                        </h4>

                        @if ($rental->return_due_at)

                            <div class="return-due-box {{ $rental->is_return_overdue ? 'overdue' : '' }}">

                                <div class="return-due-title">

                                    @if ($rental->is_return_overdue)
                                        <i class="fa-solid fa-triangle-exclamation"></i>
                                        เกินกำหนดคืนชุด
                                    @else
                                        <i class="fa-solid fa-clock"></i>
                                        ต้องคืนชุดภายใน
                                    @endif

                                </div>

                                <div class="return-due-time">

                                    {{ $rental->return_due_at->format('d/m/Y') }}

                                    เวลา

                                    {{ $rental->return_due_at->format('H:i') }} น.

                                </div>

                                <div class="return-due-note">
                                    กรุณาส่งคืนชุดภายในวันและเวลาที่ร้านกำหนด
                                </div>

                            </div>
                        @else
                            <div
                                style="
                                padding:14px 0;
                                color:var(--text-muted);
                                font-size:12.5px;
                            ">

                                <i class="fa-solid fa-calendar-xmark"></i>

                                ร้านยังไม่ได้กำหนดวันและเวลาคืนชุด

                            </div>

                        @endif

                    </div>

                    {{-- PAYMENT --}}
                    <div class="info-card">

                        <h4>
                            <i class="fa-solid fa-receipt"></i>
                            ข้อมูลการชำระเงิน & สลิป
                        </h4>

                        @php

                            // ดึงรายการชำระเงินล่าสุด
                            $latestPayment = collect($rental->payments ?? [])
                                ->sortByDesc('payment_id')
                                ->first();

                            // ยอดที่ควรแสดง = ยอดที่ชำระจริง หากมีค่า
                            // ถ้ายังไม่มี/เป็น 0 ให้ใช้ยอดรวมรายการเช่าแทน
                            $displayPaymentAmount =
                                $latestPayment && (float) $latestPayment->amount > 0
                                    ? (float) $latestPayment->amount
                                    : (float) ($rental->grand_total ?? 0);

                        @endphp

                        @if ($latestPayment)

                            <div
                                style="
                                display:flex;
                                justify-content:space-between;
                                align-items:center;
                                gap:20px;
                                padding:8px 0;
                            ">

                                <div>

                                    <div>

                                        ยอด:

                                        <strong style="color:var(--primary); font-size:16px;">
                                            ฿{{ number_format($displayPaymentAmount, 2) }}
                                        </strong>

                                        <span
                                            style="
                                            font-size:11px;
                                            color:#888;
                                        ">

                                            (
                                            {{ $latestPayment->paid_at ? $latestPayment->paid_at->format('d/m/Y H:i') : '-' }}
                                            )

                                        </span>

                                    </div>

                                    <div
                                        style="
                                        font-size:11px;
                                        margin-top:4px;
                                    ">

                                        สถานะ:

                                        @if ($latestPayment->status === 'approved')
                                            <span
                                                style="
                                                color:#166534;
                                                font-weight:700;
                                            ">
                                                ตรวจสอบอนุมัติแล้ว
                                            </span>
                                        @elseif ($latestPayment->status === 'rejected')
                                            <span
                                                style="
                                                color:#b91c1c;
                                                font-weight:700;
                                            ">
                                                ปฏิเสธการชำระเงิน
                                            </span>
                                        @else
                                            <span
                                                style="
                                                color:#b45309;
                                                font-weight:700;
                                            ">
                                                รอเจ้าหน้าที่ตรวจสลิป
                                            </span>
                                        @endif

                                    </div>

                                </div>

                                @if ($latestPayment->slip_url)
                                    <a href="{{ $latestPayment->slip_url }}" target="_blank">

                                        <img src="{{ $latestPayment->slip_url }}" class="slip-img-card" alt="สลิป">

                                    </a>
                                @endif

                            </div>
                        @else
                            <p
                                style="
                                color:var(--text-muted);
                                margin:0;
                            ">

                                ยังไม่มีข้อมูลการชำระเงินหรือแนบสลิป

                            </p>

                        @endif

                    </div>

                </div>

                {{-- TOTALS --}}
                <div class="totals-box">

                    <h4
                        style="
                        font-size:16px;
                        font-weight:800;
                        margin-bottom:16px;
                        padding-bottom:8px;
                        border-bottom:1px solid var(--border);
                    ">

                        แจกแจงค่าใช้จ่าย

                    </h4>

                    <div class="totals-row">

                        <span>
                            ค่าเช่าชุดรวม:
                        </span>

                        <strong>
                            ฿{{ number_format($rental->total_amount, 2) }}
                        </strong>

                    </div>

                    @if (!empty($rental->discount_amount) && $rental->discount_amount > 0)
                        <div class="totals-row" style="color:#16a34a;">

                            <span>

                                <i class="fa-solid fa-tag"></i>

                                {{ $rental->discount_reason ?? 'ส่วนลดโปรโมชั่น' }}:

                            </span>

                            <strong style="color:#16a34a;">
                                -฿{{ number_format($rental->discount_amount, 2) }}
                            </strong>

                        </div>

                        <div class="totals-row" style="font-size:13px;">

                            <span>
                                ค่าเช่าสุทธิหลังหักส่วนลด:
                            </span>

                            <strong>
                                ฿{{ number_format($rental->net_rental_amount, 2) }}
                            </strong>

                        </div>
                    @endif

                    <div class="totals-row">

                        <span>
                            เงินมัดจำประกันชุด:
                        </span>

                        <strong style="color:#b45309;">
                            ฿{{ number_format($rental->deposit_amount ?: 0, 2) }}
                        </strong>

                    </div>

                    <div class="totals-row final">

                        <span>
                            ยอดรวมสุทธิ:
                        </span>

                        <span>
                            ฿{{ number_format($rental->grand_total, 2) }}
                        </span>

                    </div>

                    <div
                        style="
                        background:var(--gold-light);
                        padding:10px;
                        border-radius:8px;
                        font-size:12px;
                        color:#855d14;
                        margin-top:14px;
                    ">

                        <i class="fa-solid fa-shield"></i>

                        เงินมัดจำ

                        <strong>
                            ฿{{ number_format($rental->deposit_amount ?: 0, 2) }}
                        </strong>

                        จะถูกโอนคืนเต็มจำนวนหลังตรวจสภาพชุดเรียบร้อย

                    </div>

                </div>

            </div>

            {{-- =====================================================
             RETURN REQUEST
        ====================================================== --}}

            <div id="returnSection" class="return-status-card">

                <div class="return-status-header">

                    <div>

                        <h3 class="return-status-title">
                            <i class="fa-solid fa-arrow-rotate-left" style="color:var(--primary);"></i>
                            แจ้งคืนชุด
                        </h3>

                        <p class="return-status-subtitle">
                            กรุณาเลือกวิธีคืนชุดที่ต้องการ
                            หากเลือกส่งพัสดุให้กรอกบริษัทขนส่งและเลขพัสดุ
                        </p>

                    </div>

                    @if ($isReturnCompleted)
                        <span class="return-status-badge success">
                            <i class="fa-solid fa-circle-check"></i>
                            คืนชุดแล้ว
                        </span>
                    @elseif ($isReturnRequested)
                        <span class="return-status-badge waiting">
                            <i class="fa-solid fa-clock"></i>
                            แจ้งคืนแล้ว
                        </span>
                    @else
                        <span class="return-status-badge neutral">
                            <i class="fa-solid fa-shirt"></i>
                            ยังไม่คืน
                        </span>
                    @endif

                </div>

                {{-- =====================================================
                 ยังไม่คืน + สามารถแจ้งคืนได้
            ====================================================== --}}

                @if ($canRequestReturn)

                    <form action="{{ route('rentals.request-return', $rental->rental_id) }}" method="POST"
                        id="returnRequestForm" class="return-request-form">
                        @csrf

                        <div class="return-method-grid">

                            {{-- คืนที่ร้าน --}}
                            <label class="return-method-card">

                                <div>
                                    <input type="radio" name="return_method" value="store" checked required>

                                    <span class="return-method-name">
                                        <i class="fa-solid fa-store"></i>
                                        คืนที่ร้าน
                                    </span>
                                </div>

                                <div class="return-method-desc">
                                    นำชุดมาคืนที่ร้าน KYRIX
                                    ไม่ต้องกรอกเลขพัสดุ
                                </div>

                                {{-- STORE ADDRESS --}}
                                <div class="store-address-box"
                                    style="
                                        margin-top:12px;
                                        padding:12px;
                                        border-radius:9px;
                                        background:#faf8f5;
                                        border:1px solid var(--border);
                                    ">
                                    <div
                                        style="
                                            display:flex;
                                            align-items:flex-start;
                                            gap:8px;
                                            font-size:12px;
                                            font-weight:800;
                                            color:var(--text-main);
                                            line-height:1.6;
                                        ">
                                        <i class="fa-solid fa-location-dot"
                                            style="
                                                color:var(--primary);
                                                margin-top:3px;
                                            "></i>
                                        <span>ที่อยู่ร้าน KYRIX</span>
                                    </div>

                                    <div
                                        style="
                                            margin-top:5px;
                                            padding-left:22px;
                                            font-size:11.5px;
                                            color:var(--text-muted);
                                            line-height:1.7;
                                        ">
                                        77 ตำบลในเมือง อำเภอเมือง
                                        จังหวัดนครราชสีมา 30000
                                    </div>

                                    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode('77 ตำบลในเมือง อำเภอเมือง จังหวัดนครราชสีมา 30000') }}"
                                        target="_blank" rel="noopener noreferrer"
                                        style="
                                            display:inline-flex;
                                            align-items:center;
                                            gap:6px;
                                            margin-top:8px;
                                            padding:7px 11px;
                                            border-radius:8px;
                                            background:#fff;
                                            border:1px solid var(--border);
                                            color:var(--primary);
                                            text-decoration:none;
                                            font-size:11.5px;
                                            font-weight:700;
                                        ">
                                        <i class="fa-solid fa-map-location-dot"></i>
                                        เปิดแผนที่ร้าน
                                    </a>
                                </div>

                            </label>

                            {{-- ส่งพัสดุ --}}
                            <label class="return-method-card">

                                <div>
                                    <input type="radio" name="return_method" value="parcel" id="returnMethodParcel"
                                        required>

                                    <span class="return-method-name">
                                        <i class="fa-solid fa-truck"></i>
                                        ส่งพัสดุ
                                    </span>
                                </div>

                                <div class="return-method-desc">
                                    ส่งชุดกลับมาทางบริษัทขนส่ง
                                    และกรอกเลขพัสดุ
                                </div>

                            </label>

                        </div>

                        <div id="parcelReturnFields" style="display:none;">

                            <div class="return-form-grid">

                                <div class="return-form-group">

                                    <label for="return_shipping_carrier">
                                        บริษัทขนส่ง
                                        <span>*</span>
                                    </label>

                                    <select name="return_shipping_carrier" id="return_shipping_carrier"
                                        class="input-field">
                                        <option value="">
                                            -- เลือกบริษัทขนส่ง --
                                        </option>
                                        <option value="ไปรษณีย์ไทย">ไปรษณีย์ไทย</option>
                                        <option value="Flash Express">Flash Express</option>
                                        <option value="J&T Express">J&amp;T Express</option>
                                        <option value="KEX">KEX</option>
                                        <option value="Ninja Van">Ninja Van</option>
                                        <option value="DHL">DHL</option>
                                    </select>

                                </div>

                                <div class="return-form-group">

                                    <label for="return_tracking_number">
                                        เลขพัสดุส่งคืน
                                        <span>*</span>
                                    </label>

                                    <input type="text" name="return_tracking_number" id="return_tracking_number"
                                        class="input-field" maxlength="100" placeholder="เช่น TH123456789">

                                </div>

                            </div>

                            <div class="return-helper">
                                <i class="fa-solid fa-circle-info"></i>
                                กรุณากรอกเลขพัสดุให้ตรงกับใบเสร็จของบริษัทขนส่ง
                                เพื่อให้ร้านสามารถติดตามการส่งคืนได้
                            </div>

                        </div>

                        <div class="return-submit-row">
                            <button type="submit" class="return-submit-btn">
                                <i class="fa-solid fa-paper-plane"></i>
                                ยืนยันแจ้งคืนชุด
                            </button>
                        </div>

                    </form>

                    {{-- =====================================================
                 แจ้งคืนแล้ว
            ====================================================== --}}
                @elseif ($isReturnRequested)
                    <div class="return-request-grid">

                        <div class="return-request-field">
                            <div class="return-request-label">วิธีคืนชุด</div>
                            <div class="return-request-value">
                                {{ $returnMethodText }}
                            </div>
                        </div>

                        <div class="return-request-field">
                            <div class="return-request-label">วันที่แจ้งคืน</div>
                            <div class="return-request-value">
                                {{ $rental->return_requested_at ? $rental->return_requested_at->format('d/m/Y H:i') . ' น.' : '-' }}
                            </div>
                        </div>

                        @if ($returnMethod === 'parcel')

                            <div class="return-request-field">
                                <div class="return-request-label">บริษัทขนส่ง</div>
                                <div class="return-request-value">
                                    {{ $returnCarrier ?: '-' }}
                                </div>
                            </div>

                            <div class="return-request-field">
                                <div class="return-request-label">เลขพัสดุ</div>
                                <div class="return-request-value green">
                                    {{ $returnTrackingNumber ?: '-' }}
                                </div>
                            </div>

                            @if ($returnShippingStatus)
                                <div class="return-request-field">
                                    <div class="return-request-label">สถานะพัสดุ</div>
                                    <div class="return-request-value">
                                        {{ $returnShippingStatus }}
                                    </div>
                                </div>
                            @endif

                        @endif

                    </div>

                    @if ($returnMethod === 'store')
                        <div class="return-request-note">
                            <i class="fa-solid fa-store"></i>
                            คุณเลือก <strong>คืนที่ร้าน</strong>
                            กรุณานำชุดมาคืนที่ร้าน KYRIX
                            แล้วรอเจ้าของร้านตรวจรับ
                        </div>
                    @else
                        <div class="return-request-note">
                            <i class="fa-solid fa-truck"></i>
                            หากเป็นการส่งพัสดุ
                            สามารถติดตามสถานะพัสดุได้จากข้อมูลด้านบน
                            และรอให้พัสดุถึงร้านก่อนเจ้าของร้านตรวจรับ
                        </div>
                    @endif

                    {{-- =====================================================
                 คืนชุดแล้ว
            ====================================================== --}}
                @elseif ($isReturnCompleted)
                    <div class="return-request-grid">

                        <div class="return-request-field">
                            <div class="return-request-label">วิธีคืนชุด</div>
                            <div class="return-request-value">
                                {{ $returnMethodText }}
                            </div>
                        </div>

                        <div class="return-request-field">
                            <div class="return-request-label">วันที่ร้านรับคืน</div>
                            <div class="return-request-value green">
                                {{ $rental->return_received_at ? $rental->return_received_at->format('d/m/Y H:i') . ' น.' : '-' }}
                            </div>
                        </div>

                        @if ($returnMethod === 'parcel' && $returnTrackingNumber)
                            <div class="return-request-field">
                                <div class="return-request-label">เลขพัสดุส่งคืน</div>
                                <div class="return-request-value">
                                    {{ $returnTrackingNumber }}
                                </div>
                            </div>
                        @endif

                        @if ($rental->condition_status)
                            <div class="return-request-field">
                                <div class="return-request-label">ผลตรวจสภาพชุด</div>
                                <div class="return-request-value">

                                    @if ($rental->condition_status === 'good')
                                        <span style="color:#166534;">
                                            <i class="fa-solid fa-circle-check"></i>
                                            ชุดสมบูรณ์
                                        </span>
                                    @elseif ($rental->condition_status === 'damaged')
                                        <span style="color:#b91c1c;">
                                            <i class="fa-solid fa-triangle-exclamation"></i>
                                            ชุดชำรุด / มีความเสียหาย
                                        </span>
                                    @else
                                        {{ $rental->condition_status }}
                                    @endif

                                </div>
                            </div>
                        @endif

                    </div>

                    @if ($rental->condition_status === 'good')

                        <div class="return-request-note"
                            style="
                                background:#f0fdf4;
                                border-color:#bbf7d0;
                                color:#166534;
                            ">
                            <i class="fa-solid fa-hand-holding-dollar"></i>
                            ร้านตรวจรับชุดสภาพสมบูรณ์
                            และดำเนินการคืนเงินมัดจำตามระบบแล้ว

                            @if ($rental->deposit_refund_amount !== null)
                                จำนวน
                                <strong>
                                    ฿{{ number_format((float) $rental->deposit_refund_amount, 2) }}
                                </strong>
                            @endif
                        </div>
                    @elseif ($rental->condition_status === 'damaged')
                        <div class="return-request-note"
                            style="
                                background:#fff1f2;
                                border-color:#fecdd3;
                                color:#991b1b;
                            ">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            ร้านตรวจพบความเสียหายของชุด
                            และดำเนินการตามเงื่อนไขเงินมัดจำของร้าน

                            @if ($rental->damage_note)
                                <br>
                                สาเหตุ:
                                <strong>
                                    {{ $rental->damage_note }}
                                </strong>
                            @endif
                        </div>

                    @endif

                    {{-- =====================================================
                 ยังไม่พร้อมแจ้งคืน
            ====================================================== --}}
                @else
                    <div
                        style="
                            padding:16px 18px;
                            border-radius:10px;
                            background:#fff8ed;
                            border:1px solid #fed7aa;
                            color:#9a3412;
                            font-size:12.5px;
                            line-height:1.7;
                        ">
                        <i class="fa-solid fa-clock"></i>

                        <strong>
                            ยังไม่สามารถแจ้งคืนชุดได้
                        </strong>

                        <div style="margin-top:4px;">
                            เมื่อรายการเช่าเข้าสู่สถานะ
                            <strong>กำลังเช่า</strong>
                            คุณจะสามารถเลือกวิธีคืนชุดได้ที่ส่วนนี้
                        </div>

                    </div>

                @endif

            </div>

            {{-- =====================================================
             CANCEL RENTAL
        ====================================================== --}}

            @if ($rental->status === 'pending_payment')
                <div class="cancel-rental-box" id="cancelRentalSection">

                    <div class="cancel-rental-content">

                        <div class="cancel-rental-icon">
                            <i class="fa-solid fa-ban"></i>
                        </div>

                        <div class="cancel-rental-text">

                            <h3>
                                ต้องการยกเลิกรายการเช่า?
                            </h3>

                            <p>
                                รายการนี้ยังไม่ได้ชำระเงิน
                                คุณสามารถยกเลิกการเช่าได้ในขั้นตอนนี้
                            </p>

                        </div>

                        <button type="button" class="cancel-rental-btn" onclick="openCancelRentalModal()">

                            <i class="fa-solid fa-ban"></i>

                            ยกเลิกการเช่า

                        </button>

                    </div>

                </div>

                {{-- CANCEL MODAL --}}
                <div id="cancelRentalModal" class="cancel-modal" style="display:none;" aria-hidden="true">

                    <div class="cancel-modal-backdrop"></div>

                    <div class="cancel-modal-card" role="dialog" aria-modal="true" aria-labelledby="cancelModalTitle">

                        {{-- HEADER --}}
                        <div class="cancel-modal-header">

                            <div class="cancel-modal-title-wrap">

                                <div class="cancel-modal-icon">
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                </div>

                                <div>

                                    <h3 id="cancelModalTitle">
                                        ยืนยันการยกเลิก
                                    </h3>

                                    <p>
                                        {{ $rental->rental_code ?? 'รายการเช่า' }}
                                    </p>

                                </div>

                            </div>

                            <button type="button" class="cancel-modal-close" onclick="closeCancelRentalModal()"
                                aria-label="ปิด">
                                &times;
                            </button>

                        </div>

                        {{-- FORM --}}
                        <form action="{{ route('rentals.cancel', $rental->rental_id) }}" method="POST">

                            @csrf

                            <div class="cancel-modal-body">

                                <div class="cancel-warning">

                                    <i class="fa-solid fa-circle-exclamation"></i>

                                    <div>

                                        <strong>
                                            คุณกำลังจะยกเลิกรายการเช่านี้
                                        </strong>

                                        <p>
                                            รายการนี้ยังไม่ได้ชำระเงิน
                                            เมื่อยืนยันแล้ว
                                            สถานะจะเปลี่ยนเป็น
                                            <strong>ยกเลิกแล้ว</strong>
                                            และระบบจะคืนจำนวนชุดกลับเข้าสต็อก
                                        </p>

                                    </div>

                                </div>

                                {{-- REASON --}}
                                <div class="cancel-form-group">

                                    <label for="cancel_reason_select">

                                        เหตุผลในการยกเลิก

                                        <span>*</span>

                                    </label>

                                    <select id="cancel_reason_select" class="input-field"
                                        onchange="handleCancelReasonChange()">

                                        <option value="">
                                            -- เลือกเหตุผล --
                                        </option>

                                        <option value="เปลี่ยนใจ">
                                            เปลี่ยนใจ
                                        </option>

                                        <option value="ไม่สะดวกใช้ชุดแล้ว">
                                            ไม่สะดวกใช้ชุดแล้ว
                                        </option>

                                        <option value="เปลี่ยนวันเช่า">
                                            ต้องการเปลี่ยนวันเช่า
                                        </option>

                                        <option value="พบชุดที่ต้องการใหม่">
                                            พบชุดที่ต้องการใหม่
                                        </option>

                                        <option value="อื่น ๆ">
                                            อื่น ๆ
                                        </option>

                                    </select>

                                    <textarea name="cancel_reason" id="cancel_reason" class="input-field cancel-reason-textarea" rows="3"
                                        placeholder="ระบุเหตุผลในการยกเลิก" required></textarea>

                                </div>

                            </div>

                            {{-- FOOTER --}}
                            <div class="cancel-modal-footer">

                                <button type="button" class="cancel-back-btn" onclick="closeCancelRentalModal()">
                                    ย้อนกลับ
                                </button>

                                <button type="submit" class="cancel-confirm-btn" onclick="return confirmCancelRental()">

                                    <i class="fa-solid fa-ban"></i>

                                    ยืนยันการยกเลิก

                                </button>

                            </div>

                        </form>

                    </div>

                </div>
            @endif

            {{-- UPLOAD SLIP --}}
            @if ($rental->status === 'pending_payment')
                <div id="slipSection"
                    style="
                    margin-top:36px;
                    padding:26px;
                    border:2px dashed var(--primary);
                    border-radius:var(--radius-md);
                    background:var(--primary-soft);
                ">

                    <h3
                        style="
                        font-size:18px;
                        font-weight:800;
                        color:var(--primary);
                        margin-bottom:8px;
                    ">

                        <i class="fa-solid fa-cloud-arrow-up"></i>

                        แนบสลิปโอนเงินสำหรับรายการนี้

                    </h3>

                    <p
                        style="
                        font-size:13px;
                        color:var(--text-muted);
                        margin-bottom:18px;
                    ">

                        โอนเงินเข้าบัญชี ออมสิน
                        020310925126
                        (นางสาว อภัสรา แคะมะดัน)
                        ยอดรวม
                        ฿{{ number_format($rental->grand_total, 2) }}
                        แล้วแนบสลิปด้านล่าง

                    </p>

                    <form action="{{ route('rentals.upload-slip', $rental->rental_id) }}" method="POST"
                        enctype="multipart/form-data">

                        @csrf

                        <input type="hidden" name="payment_method" value="qr">

                        <div
                            style="
                            display:flex;
                            gap:14px;
                            align-items:center;
                            flex-wrap:wrap;
                        ">

                            <input type="file" name="slip_image" accept="image/*" required style="font-size:14px;">

                            <button type="submit" class="btn btn-primary">

                                <i class="fa-solid fa-upload"></i>

                                อัปโหลดสลิปยืนยันการชำระ

                            </button>

                        </div>

                    </form>

                </div>
            @endif

        </div>

    </div>

    {{-- =========================================================
     PRINT RECEIPT
========================================================= --}}

    @php

        $printRentalAmount = (float) ($rental->total_amount ?? 0);

        $printDiscount = (float) ($rental->discount_amount ?? 0);

        $printNetRental = max(0, $printRentalAmount - $printDiscount);

        $printDeposit = (float) ($rental->deposit_amount ?? 0);

        $printServiceFee = (float) ($rental->service_fee ?? 0);

        $printGrandTotal = $printNetRental + $printDeposit + $printServiceFee;

        $latestPrintPayment = collect($rental->payments ?? [])
            ->sortByDesc('payment_id')
            ->first();

        // ยอดชำระสำหรับใบพิมพ์
        $printPaymentAmount =
            $latestPrintPayment && (float) $latestPrintPayment->amount > 0
                ? (float) $latestPrintPayment->amount
                : (float) ($rental->grand_total ?? $printGrandTotal);

    @endphp

    <div class="print-receipt" id="printReceipt">

        {{-- HEADER --}}
        <div class="print-header">

            <div class="print-logo">
                KYRIX
            </div>

            <div class="print-subtitle">
                DRESS RENTAL BOUTIQUE
            </div>

            <div class="print-title">
                ใบเสร็จรับเงิน / สรุปการเช่าชุด
            </div>

            <div class="print-code">
                {{ $rental->rental_code ?? 'KR-' . $rental->rental_id }}
            </div>

        </div>

        {{-- BASIC INFO --}}
        <div class="print-info-grid">

            <div class="print-info-box">

                <div class="print-section-title">
                    ข้อมูลการเช่า
                </div>

                <div>
                    <strong>เลขที่การเช่า:</strong>
                    {{ $rental->rental_code ?? '-' }}
                </div>

                <div>
                    <strong>วันที่ทำรายการ:</strong>
                    {{ $rental->rental_date ? date('d/m/Y', strtotime($rental->rental_date)) : '-' }}
                </div>

                <div>
                    <strong>วันเริ่มเช่า:</strong>
                    {{ date('d/m/Y', strtotime($rental->start_date)) }}
                </div>

                <div>
                    <strong>วันคืนชุด:</strong>
                    {{ date('d/m/Y', strtotime($rental->end_date)) }}
                </div>

            </div>

            <div class="print-info-box">

                <div class="print-section-title">
                    ข้อมูลลูกค้า
                </div>

                <div>
                    <strong>ชื่อ:</strong>
                    {{ $rental->customer->user->name ?? 'ลูกค้า' }}
                </div>

                <div>
                    <strong>โทร:</strong>
                    {{ $rental->recipient_phone ?? ($rental->customer->phone ?? '-') }}
                </div>

                <div>
                    <strong>รับชุด:</strong>
                    {{ $rental->delivery_method === 'delivery' ? 'จัดส่งถึงที่อยู่' : 'รับที่หน้าร้าน KYRIX' }}
                </div>

            </div>

        </div>

        {{-- ITEMS --}}
        <div class="print-section-title">
            รายการเช่า
        </div>

        <table class="print-table">

            <thead>

                <tr>

                    <th>
                        รายการ
                    </th>

                    <th class="print-center">
                        จำนวน
                    </th>

                    <th class="print-center">
                        วัน
                    </th>

                    <th class="print-right">
                        ยอดรวม
                    </th>

                </tr>

            </thead>

            <tbody>

                @foreach ($rental->details as $detail)
                    <tr>

                        <td>

                            <strong>
                                {{ $detail->product->product_name ?? 'ชุดเช่า' }}
                            </strong>

                            <br>

                            <span
                                style="
                                font-size:8px;
                                color:#777;
                            ">
                                {{ $detail->product->product_code ?? '-' }}
                                /
                                {{ $detail->selected_size ?? 'M' }}
                                /
                                {{ $detail->selected_color ?? 'ตามแบบ' }}
                            </span>

                        </td>

                        <td class="print-center">
                            {{ $detail->quantity }}
                        </td>

                        <td class="print-center">
                            {{ $detail->rental_days ?? 1 }}
                        </td>

                        <td class="print-right">
                            ฿{{ number_format((float) $detail->subtotal, 2) }}
                        </td>

                    </tr>
                @endforeach

            </tbody>

        </table>

        {{-- SUMMARY --}}
        <div class="print-summary">

            <div class="print-section-title">
                สรุปยอดเงิน
            </div>

            <div class="print-summary-row">

                <span>
                    ค่าเช่าชุดรวม
                </span>

                <strong>
                    ฿{{ number_format($printRentalAmount, 2) }}
                </strong>

            </div>

            @if ($printDiscount > 0)
                <div class="print-summary-row">

                    <span>
                        ส่วนลด
                    </span>

                    <strong style="color:#16803b;">
                        -฿{{ number_format($printDiscount, 2) }}
                    </strong>

                </div>

                <div class="print-summary-row">

                    <span>
                        ค่าเช่าสุทธิ
                    </span>

                    <strong>
                        ฿{{ number_format($printNetRental, 2) }}
                    </strong>

                </div>
            @endif

            <div class="print-summary-row">

                <span>
                    เงินมัดจำ
                </span>

                <strong>
                    ฿{{ number_format($printDeposit, 2) }}
                </strong>

            </div>

            <div class="print-summary-final">

                <span>
                    ยอดรวมสุทธิ
                </span>

                <span>
                    ฿{{ number_format($printGrandTotal, 2) }}
                </span>

            </div>

        </div>

        {{-- PAYMENT --}}
        <div class="print-payment">

            <div class="print-section-title">
                ข้อมูลการชำระเงิน
            </div>

            @if ($latestPrintPayment)

                <div>

                    <strong>
                        วิธีชำระ:
                    </strong>

                    {{ $latestPrintPayment->payment_method === 'qr'
                        ? 'QR Code PromptPay'
                        : ($latestPrintPayment->payment_method === 'transfer'
                            ? 'โอนเงินผ่านบัญชีธนาคาร'
                            : '-') }}

                </div>

                <div>

                    <strong>
                        ยอดชำระ:
                    </strong>

                    ฿{{ number_format($printPaymentAmount, 2) }}

                </div>

                <div>

                    <strong>
                        วันที่ชำระ:
                    </strong>

                    {{ $latestPrintPayment->paid_at ? $latestPrintPayment->paid_at->format('d/m/Y H:i') : '-' }}

                </div>

                <div>

                    <strong>
                        สถานะ:
                    </strong>

                    <span class="print-status">

                        @if ($latestPrintPayment->status === 'approved')
                            ตรวจสอบอนุมัติแล้ว
                        @elseif ($latestPrintPayment->status === 'rejected')
                            ปฏิเสธการชำระเงิน
                        @else
                            รอเจ้าหน้าที่ตรวจสลิป
                        @endif

                    </span>

                </div>
            @else
                <div>
                    ยังไม่มีข้อมูลการชำระเงิน
                </div>

            @endif

        </div>

        {{-- NOTE --}}
        <div class="print-note">

            <strong>
                หมายเหตุ:
            </strong>

            เงินมัดจำจะได้รับคืนตามเงื่อนไขของร้าน
            หลังจากส่งคืนชุดและตรวจสอบสภาพเรียบร้อยแล้ว

        </div>

        {{-- FOOTER --}}
        <div class="print-footer">

            <strong>
                KYRIX DRESS RENTAL BOUTIQUE
            </strong>

            <br>

            77 ตำบลในเมือง อำเภอเมือง จังหวัดนครราชสีมา 30000
            | โทร: 065-259-9072

            <br>

            ขอบคุณที่ใช้บริการ KYRIX

        </div>

    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                /* =====================================================
                   PRINT RECEIPT
                ====================================================== */

                const printButton =
                    document.getElementById('printReceiptButton');

                const printReceipt =
                    document.getElementById('printReceipt');

                if (printButton && printReceipt) {

                    const originalParent =
                        printReceipt.parentNode;

                    const originalNextSibling =
                        printReceipt.nextSibling;

                    printButton.addEventListener('click', function() {

                        document.body.appendChild(
                            printReceipt
                        );

                        document.body.classList.add(
                            'printing'
                        );

                        window.print();

                    });

                    window.addEventListener(
                        'afterprint',
                        function() {

                            document.body.classList.remove(
                                'printing'
                            );

                            if (
                                originalNextSibling &&
                                originalNextSibling.parentNode === originalParent
                            ) {

                                originalParent.insertBefore(
                                    printReceipt,
                                    originalNextSibling
                                );

                            } else {

                                originalParent.appendChild(
                                    printReceipt
                                );

                            }

                        }
                    );

                }

                /* =====================================================
                   CANCEL RENTAL MODAL
                ====================================================== */

                window.openCancelRentalModal = function() {

                    const modal =
                        document.getElementById('cancelRentalModal');

                    if (!modal) {
                        return;
                    }

                    modal.style.display = 'flex';

                    modal.setAttribute(
                        'aria-hidden',
                        'false'
                    );

                    document.body.style.overflow = 'hidden';

                };

                window.closeCancelRentalModal = function() {

                    const modal =
                        document.getElementById('cancelRentalModal');

                    if (!modal) {
                        return;
                    }

                    modal.style.display = 'none';

                    modal.setAttribute(
                        'aria-hidden',
                        'true'
                    );

                    document.body.style.overflow = '';

                };

                window.handleCancelReasonChange = function() {

                    const select =
                        document.getElementById('cancel_reason_select');

                    const textarea =
                        document.getElementById('cancel_reason');

                    if (!select || !textarea) {
                        return;
                    }

                    if (select.value === 'อื่น ๆ') {

                        textarea.value = '';

                        textarea.placeholder =
                            'กรุณาระบุเหตุผลในการยกเลิก';

                        textarea.focus();

                        return;

                    }

                    textarea.value =
                        select.value;

                    textarea.placeholder =
                        select.value !== '' ?
                        'ระบุเหตุผลเพิ่มเติมได้' :
                        'ระบุเหตุผลในการยกเลิก';

                };

                window.confirmCancelRental = function() {

                    const reason =
                        document.getElementById('cancel_reason');

                    if (!reason || !reason.value.trim()) {

                        alert(
                            'กรุณาระบุเหตุผลในการยกเลิก'
                        );

                        if (reason) {
                            reason.focus();
                        }

                        return false;

                    }

                    return confirm(
                        'ยืนยันการยกเลิกรายการเช่านี้ใช่หรือไม่?\n\n' +
                        'รายการนี้ยังไม่ได้ชำระเงิน\n' +
                        'เมื่อยืนยันแล้ว สถานะจะเปลี่ยนเป็น "ยกเลิกแล้ว" และระบบจะคืนชุดเข้าสต็อก'
                    );

                };

                /* =====================================================
                   RETURN REQUEST
                ====================================================== */

                const returnForm =
                    document.getElementById('returnRequestForm');

                const returnMethodRadios =
                    document.querySelectorAll(
                        'input[name="return_method"]'
                    );

                const parcelFields =
                    document.getElementById(
                        'parcelReturnFields'
                    );

                const carrier =
                    document.getElementById(
                        'return_shipping_carrier'
                    );

                const tracking =
                    document.getElementById(
                        'return_tracking_number'
                    );

                function updateReturnFields() {

                    if (
                        !parcelFields ||
                        !carrier ||
                        !tracking
                    ) {
                        return;
                    }

                    const selected =
                        document.querySelector(
                            'input[name="return_method"]:checked'
                        );

                    if (!selected) {
                        return;
                    }

                    if (
                        selected.value === 'parcel'
                    ) {

                        parcelFields.style.display =
                            'block';

                        carrier.required =
                            true;

                        tracking.required =
                            true;

                    } else {

                        parcelFields.style.display =
                            'none';

                        carrier.required =
                            false;

                        tracking.required =
                            false;

                        carrier.value =
                            '';

                        tracking.value =
                            '';

                    }

                }

                returnMethodRadios.forEach(
                    function(radio) {

                        radio.addEventListener(
                            'change',
                            updateReturnFields
                        );

                    }
                );

                updateReturnFields();

                if (returnForm) {

                    returnForm.addEventListener(
                        'submit',
                        function(event) {

                            const selected =
                                document.querySelector(
                                    'input[name="return_method"]:checked'
                                );

                            if (!selected) {

                                event.preventDefault();

                                alert(
                                    'กรุณาเลือกวิธีคืนชุด'
                                );

                                return;

                            }

                            if (
                                selected.value === 'parcel'
                            ) {

                                if (
                                    !carrier ||
                                    !carrier.value.trim()
                                ) {

                                    event.preventDefault();

                                    alert(
                                        'กรุณาเลือกบริษัทขนส่ง'
                                    );

                                    if (carrier) {
                                        carrier.focus();
                                    }

                                    return;

                                }

                                if (
                                    !tracking ||
                                    !tracking.value.trim()
                                ) {

                                    event.preventDefault();

                                    alert(
                                        'กรุณากรอกเลขพัสดุ'
                                    );

                                    if (tracking) {
                                        tracking.focus();
                                    }

                                    return;

                                }

                            }

                        }
                    );

                }

                /* =====================================================
                   ESC
                ====================================================== */

                document.addEventListener(
                    'keydown',
                    function(e) {

                        if (e.key === 'Escape') {

                            closeCancelRentalModal();

                        }

                    }
                );

                /* =====================================================
                   CLICK BACKDROP
                ====================================================== */

                document.addEventListener(
                    'click',
                    function(e) {

                        const modal =
                            document.getElementById(
                                'cancelRentalModal'
                            );

                        if (!modal) {
                            return;
                        }

                        const backdrop =
                            modal.querySelector(
                                '.cancel-modal-backdrop'
                            );

                        if (
                            backdrop &&
                            e.target === backdrop
                        ) {

                            closeCancelRentalModal();

                        }

                    }
                );

            });
        </script>
    @endpush


@endsection
