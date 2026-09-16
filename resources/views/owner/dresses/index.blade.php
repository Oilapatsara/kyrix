@extends('layouts.owner')

@section('title', 'คลังชุด | KYRIX Rental')

@push('styles')
<style>
    /* -------------------------------------------------------
       PAGE VARIABLES — aligned with owner layout theme
    ------------------------------------------------------- */
    .kyrix-page {
        --p:        #7a1f2b;   /* primary maroon */
        --p-dark:   #58141d;
        --p-soft:   #fbf0f2;
        --p-hover:  #f6e4e8;
        --gold:     #c69c4c;
        --gold-bg:  #fdf6e8;

        --bg:       #faf8f5;
        --surface:  #ffffff;
        --surface-alt: #f9f7f5;

        --ink:      #2a2421;
        --heading:  #2a2421;
        --muted:    #736b66;
        --faint:    #b0a89e;

        --border:       #ede8e3;
        --border-strong:#d5cdc6;

        --accent:       var(--p);
        --accent-ink:   #ffffff;
        --accent-soft:  var(--p-soft);

        --green:    #2a6b47;
        --green-bg: #e8f5ee;
        --amber:    #8c5e0a;
        --amber-bg: #fef3df;
        --red:      #9e3830;
        --red-bg:   #fcecea;

        --radius:   10px;
        --shadow-sm: 0 2px 8px rgba(0,0,0,.05);
        --shadow-md: 0 8px 24px rgba(122,31,43,.08);
    }

    .kyrix-page {
        color: var(--ink);
        font-family: 'Prompt', 'Plus Jakarta Sans', -apple-system, sans-serif;
        font-size: 14px;
        line-height: 1.6;
    }

    .kyrix-page * {
        box-sizing: border-box;
    }

    /* -------------------------------------------------------
       HEADER
    ------------------------------------------------------- */

    .page-head {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .page-eyebrow {
        display: inline-block;
        color: #a97f45;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 2px;
        text-transform: uppercase;
        margin-bottom: 6px;
    }

    .page-title {
        margin: 0;
        font-family: 'Prompt', sans-serif;
        font-size: 28px;
        line-height: 1.25;
        font-weight: 700;
        color: #430d17;
        letter-spacing: -.3px;
    }

    .page-description {
        margin: 6px 0 0;
        color: var(--muted);
        font-size: 13px;
        font-weight: 400;
    }

    .header-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-shrink: 0;
    }

    .add-category-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        height: 40px;
        padding: 0 18px;
        border-radius: var(--radius);
        background: var(--surface);
        border: 1px solid var(--border-strong);
        color: var(--muted);
        font-size: 13px;
        font-weight: 500;
        font-family: inherit;
        cursor: pointer;
        transition: all .18s ease;
        flex-shrink: 0;
        box-shadow: var(--shadow-sm);
    }

    .add-category-btn:hover {
        background: var(--p-soft);
        border-color: var(--p);
        color: var(--p);
        box-shadow: 0 4px 14px rgba(122,31,43,.12);
    }

    .add-category-btn i {
        font-size: 12px;
    }

    .add-dress-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        height: 40px;
        padding: 0 20px;
        border-radius: var(--radius);
        background: linear-gradient(135deg, var(--p) 0%, var(--p-dark) 100%);
        color: #fff;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        font-family: inherit;
        transition: all .18s ease;
        flex-shrink: 0;
        box-shadow: 0 4px 14px rgba(122,31,43,.28);
    }

    .add-dress-btn:hover {
        background: linear-gradient(135deg, var(--p-dark) 0%, #3e0e17 100%);
        color: #fff;
        box-shadow: 0 6px 20px rgba(122,31,43,.38);
        transform: translateY(-1px);
    }

    .add-dress-btn i {
        font-size: 11px;
    }

    /* -------------------------------------------------------
       MODAL STYLES
    ------------------------------------------------------- */
    .modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(30, 15, 18, 0.55);
        backdrop-filter: blur(5px);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .modal-card {
        background: var(--surface);
        border-radius: 16px;
        width: 100%;
        max-width: 560px;
        box-shadow: 0 24px 60px rgba(0,0,0,.18);
        overflow: hidden;
        animation: modalFadeIn 0.22s cubic-bezier(.34,1.26,.64,1);
        font-family: 'Prompt', sans-serif;
    }

    .modal-tabs {
        display: flex;
        background: var(--surface-alt);
        border-bottom: 1px solid var(--border);
        padding: 0 20px;
        gap: 4px;
    }

    .modal-tab-btn {
        padding: 13px 16px;
        background: none;
        border: none;
        border-bottom: 2px solid transparent;
        font-size: 13px;
        font-weight: 600;
        font-family: inherit;
        color: var(--muted);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all .15s ease;
    }

    .modal-tab-btn.active {
        color: var(--p);
        border-bottom-color: var(--p);
    }

    .category-manage-list {
        max-height: 300px;
        overflow-y: auto;
        padding-right: 4px;
    }

    .category-list-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 11px 14px;
        background: var(--surface-alt);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        margin-bottom: 8px;
        gap: 10px;
        transition: border-color .15s ease;
    }

    .category-list-item:hover {
        border-color: var(--border-strong);
    }

    .cat-item-info {
        flex: 1;
        min-width: 0;
    }

    .cat-item-name {
        font-size: 14px;
        font-weight: 600;
        color: var(--ink);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .cat-item-desc {
        font-size: 12px;
        color: var(--muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-top: 2px;
    }

    .cat-item-actions {
        display: flex;
        gap: 6px;
        flex-shrink: 0;
    }

    .btn-icon-edit {
        padding: 5px 12px;
        background: var(--p-soft);
        color: var(--p);
        border: 1px solid rgba(122,31,43,.25);
        border-radius: 7px;
        font-size: 12px;
        font-weight: 600;
        font-family: inherit;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all .15s ease;
    }

    .btn-icon-edit:hover {
        background: var(--p);
        color: #fff;
        border-color: var(--p);
    }

    .btn-icon-delete {
        padding: 5px 12px;
        background: var(--red-bg);
        color: var(--red);
        border: 1px solid rgba(158,56,48,.25);
        border-radius: 7px;
        font-size: 12px;
        font-weight: 600;
        font-family: inherit;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all .15s ease;
    }

    .btn-icon-delete:hover {
        background: var(--red);
        color: #fff;
        border-color: var(--red);
    }

    @keyframes modalFadeIn {
        from { opacity: 0; transform: scale(.96) translateY(-8px); }
        to   { opacity: 1; transform: scale(1)  translateY(0); }
    }

    .modal-header {
        padding: 18px 22px;
        background: linear-gradient(135deg, var(--p-soft) 0%, #fff 100%);
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .modal-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--p);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 9px;
    }

    .modal-close {
        background: none;
        border: none;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        font-size: 20px;
        color: var(--muted);
        cursor: pointer;
        line-height: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background .15s ease;
    }

    .modal-close:hover {
        background: var(--p-hover);
        color: var(--p);
    }

    .modal-body {
        padding: 22px;
    }

    .form-group-custom {
        margin-bottom: 16px;
    }

    .form-label-custom {
        display: block;
        font-size: 12.5px;
        font-weight: 600;
        margin-bottom: 7px;
        color: var(--ink);
        letter-spacing: .1px;
    }

    .form-control-custom {
        width: 100%;
        padding: 10px 13px;
        border: 1.5px solid var(--border);
        border-radius: var(--radius);
        font-size: 14px;
        font-family: 'Prompt', inherit;
        color: var(--ink);
        background: var(--surface);
        outline: none;
        transition: border-color .18s ease, box-shadow .18s ease;
    }

    .form-control-custom:focus {
        border-color: var(--p);
        box-shadow: 0 0 0 3px rgba(122,31,43,.1);
    }

    .modal-footer {
        padding: 14px 22px;
        background: var(--surface-alt);
        border-top: 1px solid var(--border);
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .btn-cancel {
        padding: 9px 18px;
        background: transparent;
        border: 1.5px solid var(--border-strong);
        border-radius: var(--radius);
        font-size: 13px;
        font-weight: 600;
        font-family: inherit;
        color: var(--muted);
        cursor: pointer;
        transition: all .15s ease;
    }

    .btn-cancel:hover {
        background: #f0ece8;
        color: var(--ink);
    }

    .btn-submit {
        padding: 9px 20px;
        background: linear-gradient(135deg, var(--p) 0%, var(--p-dark) 100%);
        color: #fff;
        border: none;
        border-radius: var(--radius);
        font-size: 13px;
        font-weight: 600;
        font-family: inherit;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        transition: all .18s ease;
        box-shadow: 0 4px 12px rgba(122,31,43,.25);
    }

    .btn-submit:hover {
        background: linear-gradient(135deg, var(--p-dark) 0%, #3e0e17 100%);
        box-shadow: 0 6px 18px rgba(122,31,43,.35);
        transform: translateY(-1px);
    }

    /* -------------------------------------------------------
       FLASH
    ------------------------------------------------------- */

    .flash {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 18px;
        padding: 12px 16px;
        border-radius: var(--radius);
        font-size: 13px;
        font-weight: 500;
        border: 1px solid transparent;
        box-shadow: var(--shadow-sm);
    }

    .flash-success {
        background: var(--green-bg);
        color: var(--green);
        border-color: #c3dfd0;
    }

    .flash-error {
        background: var(--red-bg);
        color: var(--red);
        border-color: #eecfcc;
    }

    /* -------------------------------------------------------
       STAT BAR
    ------------------------------------------------------- */

    .stat-bar {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-bottom: 20px;
    }

    .stat-cell {
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 5px;
        padding: 18px 22px;
        text-decoration: none;
        color: inherit;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        transition: background .15s ease, border-color .15s ease, box-shadow .15s ease;
        overflow: hidden;
    }

    .stat-cell:last-child {
        border-right: 1px solid var(--border);
    }

    .stat-cell:hover {
        background: var(--surface-alt);
        border-color: var(--border-strong);
    }

    .stat-cell.active {
        background: var(--surface);
        border-color: var(--p);
        box-shadow: 0 0 0 3px rgba(122,31,43,.07), var(--shadow-sm);
    }

    .stat-cell.active::after {
        content: "";
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 3px;
        background: linear-gradient(180deg, var(--p) 0%, var(--p-dark) 100%);
        border-radius: 0 2px 2px 0;
    }

    .stat-cell-label {
        color: var(--muted);
        font-size: 11.5px;
        font-weight: 500;
        letter-spacing: .2px;
        text-transform: uppercase;
    }

    .stat-cell-number {
        color: var(--heading);
        font-size: 26px;
        font-weight: 700;
        line-height: 1.1;
        letter-spacing: -.8px;
    }

    /* -------------------------------------------------------
       WORKSPACE
    ------------------------------------------------------- */

    .workspace {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }

    /* -------------------------------------------------------
       TOOLBAR
    ------------------------------------------------------- */

    .toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
        padding: 14px 18px;
        background: var(--surface-alt);
        border-bottom: 1px solid var(--border);
    }

    .result-summary {
        color: var(--muted);
        font-size: 13px;
        white-space: nowrap;
    }

    .result-summary strong {
        color: var(--heading);
        font-weight: 700;
    }

    .filter-group {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .select-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }

    .select-wrap i {
        position: absolute;
        left: 12px;
        font-size: 11px;
        color: var(--faint);
        pointer-events: none;
    }

    .toolbar-select {
        height: 36px;
        min-width: 155px;
        padding: 0 32px 0 33px;
        border: 1.5px solid var(--border);
        border-radius: 999px;
        background-color: var(--surface);
        color: var(--ink);
        font-family: 'Prompt', inherit;
        font-size: 12.5px;
        font-weight: 500;
        outline: none;
        cursor: pointer;

        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;

        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%23736b66' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 13px 13px;

        transition: border-color .15s ease, box-shadow .15s ease;
    }

    .toolbar-select:hover {
        border-color: var(--border-strong);
    }

    .toolbar-select:focus {
        border-color: var(--p);
        box-shadow: 0 0 0 3px rgba(122,31,43,.1);
    }

    #category_id.toolbar-select {
        min-width: 215px;
    }

    .category-reset {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        height: 36px;
        padding: 0 8px;
        color: var(--muted);
        font-size: 12px;
        text-decoration: none;
        white-space: nowrap;
        transition: color .12s ease;
        font-weight: 500;
    }

    .category-reset i {
        font-size: 9px;
    }

    .category-reset:hover {
        color: var(--p);
    }

    /* -------------------------------------------------------
       TABLE
    ------------------------------------------------------- */

    .table-wrap {
        overflow-x: auto;
    }

    .dress-table {
        width: 100%;
        min-width: 960px;
        border-collapse: collapse;
    }

    .dress-table thead th {
        position: sticky;
        top: 0;
        z-index: 1;
        padding: 11px 16px;
        background: var(--surface-alt);
        color: var(--muted);
        border-bottom: 1px solid var(--border);
        font-size: 11px;
        font-weight: 600;
        letter-spacing: .5px;
        text-transform: uppercase;
        text-align: left;
        white-space: nowrap;
    }

    .dress-table td {
        padding: 12px 16px;
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
        font-size: 13.5px;
    }

    .dress-table tbody tr:last-child td {
        border-bottom: none;
    }

    .dress-table tbody tr {
        transition: background .12s ease;
    }

    .dress-table tbody tr:hover {
        background: var(--surface-alt);
    }

    .dress-code {
        color: var(--muted);
        font-size: 11.5px;
        font-weight: 600;
        font-variant-numeric: tabular-nums;
        letter-spacing: .3px;
    }

    .dress-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .dress-image,
    .dress-image-empty {
        width: 46px;
        height: 46px;
        min-width: 46px;
        border-radius: 8px;
        border: 1px solid var(--border);
        object-fit: cover;
        background: var(--surface-alt);
    }

    .dress-image-empty {
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--faint);
        font-size: 16px;
    }

    .dress-name {
        color: var(--ink);
        font-size: 13.5px;
        font-weight: 600;
        line-height: 1.4;
    }

    .dress-meta {
        margin-top: 2px;
        color: var(--faint);
        font-size: 11px;
    }

    .category-tag {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 10px;
        background: var(--gold-bg);
        color: #7a5e20;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        border: 1px solid rgba(198,156,76,.25);
    }

    .category-empty {
        color: var(--faint);
        font-size: 12px;
        font-style: italic;
    }

    .price {
        color: var(--heading);
        font-size: 13.5px;
        font-weight: 700;
        font-variant-numeric: tabular-nums;
    }

    .status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        height: 25px;
        padding: 0 10px;
        border-radius: 999px;
        font-size: 11.5px;
        font-weight: 600;
        white-space: nowrap;
    }

    .status::before {
        content: "";
        width: 6px;
        height: 6px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .status.available {
        background: var(--green-bg);
        color: var(--green);
    }

    .status.available::before {
        background: var(--green);
    }

    .status.rented {
        background: var(--amber-bg);
        color: var(--amber);
    }

    .status.rented::before {
        background: var(--amber);
    }

    .status.inactive {
        background: var(--red-bg);
        color: var(--red);
    }

    .status.inactive::before {
        background: var(--red);
    }

    .action-group {
        display: flex;
        justify-content: flex-end;
        gap: 6px;
    }

    .edit-btn,
    .delete-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        height: 30px;
        padding: 0 12px;
        border-radius: 7px;
        font-size: 12px;
        font-weight: 600;
        font-family: inherit;
        transition: all .14s ease;
        cursor: pointer;
        border: 1.5px solid var(--border);
        background: var(--surface);
    }

    .edit-btn {
        color: var(--ink);
        text-decoration: none;
    }

    .edit-btn:hover {
        border-color: var(--p);
        background: var(--p-soft);
        color: var(--p);
    }

    .delete-btn {
        color: var(--red);
    }

    .delete-btn:hover {
        background: var(--red-bg);
        border-color: rgba(158,56,48,.4);
    }

    /* -------------------------------------------------------
       EMPTY
    ------------------------------------------------------- */

    .empty-box {
        padding: 64px 20px;
        text-align: center;
    }

    .empty-icon {
        width: 52px;
        height: 52px;
        margin: 0 auto 14px;
        border-radius: 14px;
        background: var(--surface-alt);
        border: 1.5px solid var(--border);
        color: var(--faint);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .empty-title {
        color: var(--heading);
        font-size: 14px;
        font-weight: 700;
    }

    .empty-text {
        margin-top: 5px;
        color: var(--muted);
        font-size: 13px;
    }

    /* -------------------------------------------------------
       PAGINATION
    ------------------------------------------------------- */

    .pagination-wrap {
        padding: 14px 20px;
        background: var(--surface-alt);
        border-top: 1px solid var(--border);
    }

    .pagination-wrap svg,
    .pagination-wrap nav svg {
        width: 1.25rem !important;
        height: 1.25rem !important;
        max-width: 1.25rem !important;
        max-height: 1.25rem !important;
        display: inline-block !important;
        vertical-align: middle !important;
        flex-shrink: 0;
    }

    .pagination-wrap nav {
        width: 100%;
    }

    .pagination-wrap nav > div:first-child {
        margin-bottom: 8px;
    }

    @media (min-width: 640px) {
        .pagination-wrap nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .pagination-wrap nav > div:first-child {
            margin-bottom: 0;
        }
    }

    .pagination-wrap span[aria-current="page"] > span {
        background-color: var(--p) !important;
        color: #ffffff !important;
        border-color: var(--p) !important;
    }

    /* -------------------------------------------------------
       RESPONSIVE
    ------------------------------------------------------- */

    @media (max-width: 900px) {
        .stat-bar {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 640px) {
        .stat-bar {
            grid-template-columns: 1fr;
            gap: 8px;
        }

        .page-head {
            flex-direction: column;
            align-items: flex-start;
        }

        .add-dress-btn {
            width: 100%;
            justify-content: center;
        }

        .toolbar {
            flex-direction: column;
            align-items: stretch;
        }

        .filter-group {
            width: 100%;
            flex-direction: column;
            align-items: stretch;
        }

        .select-wrap {
            width: 100%;
        }

        .toolbar-select,
        #category_id.toolbar-select {
            flex: 1;
            min-width: 0;
            width: 100%;
        }

        .category-reset {
            justify-content: flex-start;
            width: fit-content;
        }
    }
</style>
@endpush


@section('content')

<div class="kyrix-page">

    {{-- =====================================================
         HEADER
    ====================================================== --}}
    <div class="page-head">

        <div>
            <span class="page-eyebrow">KYRIX RENTAL · DRESSES</span>
            <h1 class="page-title">
                คลังชุด
            </h1>

            <p class="page-description">
                จัดการชุดเช่าทั้งหมด ตรวจสอบหมวดหมู่ ราคา และสถานะการใช้งาน
            </p>
        </div>

        <div class="header-actions">
            <button
                type="button"
                class="add-category-btn"
                onclick="openCategoryModal('manage')"
            >
                <i class="fa-solid fa-folder-gear"></i>
                จัดการ / เพิ่มหมวดหมู่
            </button>

            <a
                href="{{ route('owner.dresses.create') }}"
                class="add-dress-btn"
            >
                <i class="fa-solid fa-plus"></i>
                เพิ่มชุด
            </a>
        </div>

    </div>


    {{-- =====================================================
         FLASH MESSAGE
    ====================================================== --}}
    @if(session('success'))
        <div class="flash flash-success">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="flash flash-error">
            <i class="fa-solid fa-circle-exclamation"></i>
            {{ session('error') }}
        </div>
    @endif


    {{-- =====================================================
         STAT BAR
    ====================================================== --}}
    <div class="stat-bar">

        <a
            href="{{ route('owner.dresses.index', request()->except(['status','category_id','page'])) }}"
            class="stat-cell {{ !request('status') && !request('category_id') ? 'active' : '' }}"
        >
            <span class="stat-cell-label">ชุดทั้งหมด</span>
            <span class="stat-cell-number">{{ number_format($summary['total'] ?? $products->total()) }}</span>
        </a>

        <a
            href="{{ route('owner.dresses.index', array_merge(
                request()->except(['status','category_id','page']),
                ['status' => 'active']
            )) }}"
            class="stat-cell {{ request('status') === 'active' ? 'active' : '' }}"
        >
            <span class="stat-cell-label">พร้อมให้เช่า</span>
            <span class="stat-cell-number">{{ number_format($summary['available'] ?? 0) }}</span>
        </a>

        <a
            href="{{ route('owner.dresses.index', array_merge(
                request()->except(['status','category_id','page']),
                ['status' => 'rented']
            )) }}"
            class="stat-cell {{ request('status') === 'rented' ? 'active' : '' }}"
        >
            <span class="stat-cell-label">กำลังเช่า</span>
            <span class="stat-cell-number">{{ number_format($summary['rented'] ?? 0) }}</span>
        </a>

        <a
            href="{{ route('owner.dresses.index', array_merge(
                request()->except(['status','category_id','page']),
                ['status' => 'inactive']
            )) }}"
            class="stat-cell {{ request('status') === 'inactive' ? 'active' : '' }}"
        >
            <span class="stat-cell-label">ปิดใช้งาน</span>
            <span class="stat-cell-number">{{ number_format($summary['inactive'] ?? 0) }}</span>
        </a>

    </div>


    {{-- =====================================================
         WORKSPACE
    ====================================================== --}}
    <div class="workspace">

        {{-- =================================================
             TOOLBAR
        ================================================== --}}
        <div class="toolbar">

            <div class="result-summary">
                พบ <strong>{{ number_format($products->total()) }}</strong> รายการ
            </div>

            <form
                action="{{ route('owner.dresses.index') }}"
                method="GET"
                class="filter-group"
            >

                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif

                {{-- =================================================
                     CATEGORY DROPDOWN
                ================================================== --}}
                <div class="select-wrap">

                    <i class="fa-solid fa-layer-group"></i>

                    <select
                        name="category_id"
                        id="category_id"
                        class="toolbar-select"
                        aria-label="กรองตามหมวดหมู่"
                        onchange="this.form.submit()"
                    >

                        <option value="">
                            ทุกหมวดหมู่ ({{ $summary['total'] ?? $products->total() }})
                        </option>

                        @foreach($categories as $category)

                            @php
                                // ตาราง `categories` ใช้คอลัมน์ category_id / category_name
                                $categoryId = $category->category_id ?? $category->id;
                                $categoryName = $category->category_name ?? $category->name;
                            @endphp

                            <option
                                value="{{ $categoryId }}"
                                {{ (string) request('category_id') === (string) $categoryId ? 'selected' : '' }}
                            >
                                {{ $categoryName }}{{ isset($category->products_count) ? ' ('.$category->products_count.')' : '' }}
                            </option>

                        @endforeach

                    </select>

                </div>

                @if(request('category_id'))
                    <a
                        href="{{ route('owner.dresses.index', request()->except(['category_id','page'])) }}"
                        class="category-reset"
                    >
                        <i class="fa-solid fa-xmark"></i>
                        ล้างตัวกรอง
                    </a>
                @endif

                {{-- =================================================
                     SORT DROPDOWN
                ================================================== --}}
                <div class="select-wrap">

                    <i class="fa-solid fa-arrow-down-wide-short"></i>

                    <select
                        name="sort"
                        id="sort"
                        class="toolbar-select"
                        aria-label="เรียงลำดับ"
                        onchange="this.form.submit()"
                    >
                        <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>ล่าสุด</option>
                        <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>ชื่อ A-Z</option>
                        <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>ราคา ต่ำ → สูง</option>
                        <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>ราคา สูง → ต่ำ</option>
                    </select>

                </div>

            </form>

        </div>


        {{-- =================================================
             TABLE
        ================================================== --}}
        <div class="table-wrap">

            <table class="dress-table">

                <thead>
                    <tr>
                        <th style="width:10%;">รหัส</th>
                        <th style="width:33%;">ชุด</th>
                        <th style="width:16%;">หมวดหมู่</th>
                        <th style="width:12%; text-align:right;">ราคาเช่า</th>
                        <th style="width:13%;">สถานะ</th>
                        <th style="width:16%; text-align:right;">จัดการ</th>
                    </tr>
                </thead>

                <tbody>

                @forelse($products as $product)

                    @php

                        $image = null;

                        if (isset($product->images) && $product->images->count()) {
                            $image = $product->images->first()->image_path
                                ?? $product->images->first()->url;
                        }

                        if (!$image && !empty($product->image)) {
                            $image = $product->image;
                        }

                        if (!$image && !empty($product->image_path)) {
                            $image = $product->image_path;
                        }

                        $productId = $product->product_id ?? $product->id;

                        $status = strtolower($product->status ?? 'available');

                        $statusText = match ($status) {
                            'active', 'available' => 'พร้อมให้เช่า',
                            'rented', 'busy' => 'กำลังเช่า',
                            'maintenance' => 'ซ่อมบำรุง',
                            'inactive' => 'ปิดใช้งาน',
                            default => $product->status ?? '-',
                        };

                        $statusClass = match ($status) {
                            'active', 'available' => 'available',
                            'rented', 'busy' => 'rented',
                            default => 'inactive',
                        };

                        // ตาราง `categories` ใช้คอลัมน์ category_name (ไม่ใช่ name)
                        $productCategoryName = $product->category->category_name
                            ?? $product->category->name
                            ?? null;

                    @endphp

                    <tr>

                        <td>
                            <span class="dress-code">{{ $product->product_code }}</span>
                        </td>

                        <td>
                            <div class="dress-info">

                                @if($image)
                                    <img
                                        src="{{
                                            Str::startsWith($image, ['http://', 'https://'])
                                                ? $image
                                                : asset('storage/' . ltrim($image, '/'))
                                        }}"
                                        alt="{{ $product->product_name }}"
                                        class="dress-image"
                                    >
                                @else
                                    <div class="dress-image-empty">
                                        <i class="fa-solid fa-shirt"></i>
                                    </div>
                                @endif

                                <div>
                                    <div class="dress-name">{{ $product->product_name }}</div>
                                    <div class="dress-meta">รายการ #{{ $productId }}</div>
                                </div>

                            </div>
                        </td>

                        <td>
                            @if($productCategoryName)
                                <span class="category-tag">{{ $productCategoryName }}</span>
                            @else
                                <span class="category-empty">ไม่ระบุหมวดหมู่</span>
                            @endif
                        </td>

                        <td style="text-align:right;">
                            <span class="price">฿{{ number_format($product->rental_price, 2) }}</span>
                        </td>

                        <td>
                            <span class="status {{ $statusClass }}">{{ $statusText }}</span>
                        </td>

                        <td>
                            <div class="action-group">

                                <a href="{{ route('owner.dresses.edit', $productId) }}" class="edit-btn">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                    แก้ไข
                                </a>

                                <form
                                    action="{{ route('owner.dresses.destroy', $productId) }}"
                                    method="POST"
                                    style="margin:0;"
                                    onsubmit="return confirm('ยืนยันการลบชุด {{ $product->product_code }} ใช่หรือไม่?');"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="delete-btn">
                                        <i class="fa-regular fa-trash-can"></i>
                                        ลบ
                                    </button>
                                </form>

                            </div>
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="6">
                            <div class="empty-box">
                                <div class="empty-icon">
                                    <i class="fa-solid fa-shirt"></i>
                                </div>
                                <div class="empty-title">ไม่พบชุดตามเงื่อนไขที่เลือก</div>
                                <div class="empty-text">ลองเลือกหมวดหมู่หรือสถานะอื่น</div>
                            </div>
                        </td>
                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>


        {{-- =================================================
             PAGINATION
        ================================================== --}}
        @if($products->hasPages())
            <div class="pagination-wrap">
                {{ $products->withQueryString()->links() }}
            </div>
        @endif

    </div>

</div>

{{-- =====================================================
     CATEGORY MODAL (ADD / EDIT / DELETE)
====================================================== --}}
<div id="categoryModal" class="modal-backdrop" style="display: none;">
    <div class="modal-card">
        <div class="modal-header">
            <h3 class="modal-title" id="modalTitle">
                <i class="fa-solid fa-folder-gear"></i> จัดการหมวดหมู่ชุด
            </h3>
            <button type="button" class="modal-close" onclick="closeCategoryModal()">&times;</button>
        </div>

        <div class="modal-tabs">
            <button type="button" class="modal-tab-btn active" id="tabAddBtn" onclick="switchCategoryTab('add')">
                <i class="fa-solid fa-plus"></i> เพิ่มหมวดหมู่ใหม่
            </button>
            <button type="button" class="modal-tab-btn" id="tabManageBtn" onclick="switchCategoryTab('manage')">
                <i class="fa-solid fa-list-check"></i> แก้ไข / รายการหมวดหมู่ ({{ $categories->count() }})
            </button>
        </div>

        {{-- Tab 1: เพิ่มหมวดหมู่ใหม่ --}}
        <div id="tabAddContent">
            <form action="{{ route('owner.dresses.categories.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group-custom">
                        <label for="category_name_input" class="form-label-custom">
                            ชื่อหมวดหมู่ <span style="color:var(--red);">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="category_name" 
                            id="category_name_input" 
                            class="form-control-custom" 
                            placeholder="เช่น Western Muse — เดรสสายฝอ" 
                            required 
                            maxlength="100"
                        >
                    </div>
                    <div class="form-group-custom">
                        <label for="description_input" class="form-label-custom">
                            รายละเอียดหมวดหมู่ (ถ้ามี)
                        </label>
                        <textarea 
                            name="description" 
                            id="description_input" 
                            class="form-control-custom" 
                            rows="3" 
                            placeholder="ระบุคำอธิบายเกี่ยวกับหมวดหมู่นี้..."
                        ></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeCategoryModal()">ยกเลิก</button>
                    <button type="submit" class="btn-submit">
                        <i class="fa-solid fa-check"></i> บันทึกหมวดหมู่ใหม่
                    </button>
                </div>
            </form>
        </div>

        {{-- Tab 2: รายการหมวดหมู่ทั้งหมดสำหรับแก้ไข/ลบ --}}
        <div id="tabManageContent" style="display: none;">
            <div class="modal-body">
                <div class="category-manage-list">
                    @foreach($categories as $cat)
                        @php
                            $cId = $cat->category_id ?? $cat->id;
                            $cName = $cat->category_name ?? $cat->name;
                            $cDesc = $cat->description ?? '';
                        @endphp
                        <div class="category-list-item">
                            <div class="cat-item-info">
                                <div class="cat-item-name">{{ $cName }}</div>
                                @if($cDesc)
                                    <div class="cat-item-desc">{{ $cDesc }}</div>
                                @endif
                            </div>
                            <div class="cat-item-actions">
                                <button 
                                    type="button" 
                                    class="btn-icon-edit" 
                                    onclick="startEditCategory({{ $cId }}, '{{ addslashes($cName) }}', '{{ addslashes($cDesc) }}')"
                                >
                                    <i class="fa-regular fa-pen-to-square"></i> แก้ไข
                                </button>
                                <form 
                                    action="{{ route('owner.dresses.categories.destroy', $cId) }}" 
                                    method="POST" 
                                    style="margin:0;"
                                    onsubmit="return confirm('ยืนยันการลบหมวดหมู่ {{ addslashes($cName) }} ใช่หรือไม่?');"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon-delete">
                                        <i class="fa-regular fa-trash-can"></i> ลบ
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeCategoryModal()">ปิด</button>
            </div>
        </div>

        {{-- Tab 3: ฟอร์มแก้ไขชื่อหมวดหมู่ --}}
        <div id="tabEditContent" style="display: none;">
            <form id="editCategoryForm" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <div style="margin-bottom: 14px; color: var(--accent); font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-pen-to-square"></i> แก้ไขชื่อหมวดหมู่ในฐานข้อมูล
                    </div>
                    <div class="form-group-custom">
                        <label for="edit_category_name_input" class="form-label-custom">
                            ชื่อหมวดหมู่ <span style="color:var(--red);">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="category_name" 
                            id="edit_category_name_input" 
                            class="form-control-custom" 
                            required 
                            maxlength="100"
                        >
                    </div>
                    <div class="form-group-custom">
                        <label for="edit_description_input" class="form-label-custom">
                            รายละเอียดหมวดหมู่ (ถ้ามี)
                        </label>
                        <textarea 
                            name="description" 
                            id="edit_description_input" 
                            class="form-control-custom" 
                            rows="3" 
                        ></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="switchCategoryTab('manage')">ย้อนกลับ</button>
                    <button type="submit" class="btn-submit">
                        <i class="fa-solid fa-check"></i> บันทึกการแก้ไขลง DB
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

@push('scripts')
<script>
function openCategoryModal(tab = 'add') {
    var modal = document.getElementById('categoryModal');
    if (modal) {
        modal.style.display = 'flex';
        switchCategoryTab(tab);
    }
}

function closeCategoryModal() {
    var modal = document.getElementById('categoryModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

function switchCategoryTab(tab) {
    var addContent = document.getElementById('tabAddContent');
    var manageContent = document.getElementById('tabManageContent');
    var editContent = document.getElementById('tabEditContent');
    var addBtn = document.getElementById('tabAddBtn');
    var manageBtn = document.getElementById('tabManageBtn');

    if (tab === 'add') {
        if (addContent) addContent.style.display = 'block';
        if (manageContent) manageContent.style.display = 'none';
        if (editContent) editContent.style.display = 'none';
        if (addBtn) addBtn.classList.add('active');
        if (manageBtn) manageBtn.classList.remove('active');
        var input = document.getElementById('category_name_input');
        if (input) input.focus();
    } else if (tab === 'manage') {
        if (addContent) addContent.style.display = 'none';
        if (manageContent) manageContent.style.display = 'block';
        if (editContent) editContent.style.display = 'none';
        if (addBtn) addBtn.classList.remove('active');
        if (manageBtn) manageBtn.classList.add('active');
    } else if (tab === 'edit') {
        if (addContent) addContent.style.display = 'none';
        if (manageContent) manageContent.style.display = 'none';
        if (editContent) editContent.style.display = 'block';
        if (addBtn) addBtn.classList.remove('active');
        if (manageBtn) manageBtn.classList.remove('active');
    }
}

function startEditCategory(id, name, description) {
    var form = document.getElementById('editCategoryForm');
    var nameInput = document.getElementById('edit_category_name_input');
    var descInput = document.getElementById('edit_description_input');

    if (form) {
        form.action = '/owner/dresses/categories/' + id + '/update';
    }
    if (nameInput) {
        nameInput.value = name;
    }
    if (descInput) {
        descInput.value = description || '';
    }

    switchCategoryTab('edit');
    if (nameInput) nameInput.focus();
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeCategoryModal();
    }
});
</script>
@endpush

@endsection