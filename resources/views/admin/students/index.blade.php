
@extends('layouts.admin')

@section('title', 'Manage Student Accounts | Manoa')
@section('page-label', 'Student Management')

@section('content')

<style>
    .student-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        margin-bottom: 27px;
        flex-wrap: wrap;
    }

    .add-student-btn {
        background: #174b38;
        color: white;
        border: none;
        padding: 12px 19px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
    }

    .add-student-btn:hover { background: #286349; }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 17px;
        margin-bottom: 25px;
    }

    .stat-card {
        background: white;
        border: 1px solid #e9eee9;
        border-radius: 12px;
        padding: 21px;
    }

    .stat-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .stat-label { font-size: 12px; color: #849188; font-weight: 600; }
    .stat-number { font-size: 28px; font-weight: 800; color: #24352d; }
    .stat-icon {
        width: 39px;
        height: 39px;
        display: grid;
        place-items: center;
        border-radius: 10px;
        font-size: 17px;
    }

    .green-icon { background: #e7f2e9; color: #246344; }
    .blue-icon { background: #e8f1f8; color: #397ba5; }
    .gold-icon { background: #faf2df; color: #b18a32; }

    .student-panel {
        background: white;
        border: 1px solid #e9eee9;
        border-radius: 13px;
        overflow: hidden;
    }

    .panel-heading {
        padding: 23px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
        border-bottom: 1px solid #edf0ed;
    }

    .panel-title { font-size: 15px; font-weight: 800; }
    .panel-subtitle { font-size: 11px; color: #89958d; margin-top: 5px; }

    .student-controls {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .search-input, .status-filter {
        height: 39px;
        border: 1px solid #e1e8e1;
        border-radius: 7px;
        padding: 0 12px;
        font-family: inherit;
        font-size: 11px;
        outline: none;
        background: white;
        color: #34463a;
    }

    .search-input { width: 220px; }
    .search-input:focus, .status-filter:focus { border-color: #397b5b; }

    .table-wrapper { overflow-x: auto; }

    .student-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        white-space: nowrap;
    }

    .student-table th {
        padding: 14px 20px;
        background: #f8faf8;
        color: #89958d;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .6px;
        text-transform: uppercase;
    }

    .student-table td {
        padding: 16px 20px;
        border-bottom: 1px solid #eff2ef;
        font-size: 11px;
        color: #526157;
    }

    .student-table tbody tr:hover { background: #fbfdfb; }
    .student-name { font-weight: 700; color: #293b30; }
    .student-email { font-size: 10px; color: #98a29a; margin-top: 4px; }

    .student-avatar {
        width: 34px;
        height: 34px;
        display: grid;
        place-items: center;
        border-radius: 50%;
        background: #e7efe7;
        color: #20533a;
        font-weight: 800;
        font-size: 11px;
    }

    .student-person {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .status-badge {
        display: inline-block;
        padding: 6px 10px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 700;
    }

    .active-status { background: #e5f3e9; color: #267246; }
    .inactive-status { background: #f0f1f0; color: #737d75; }

    .action-btn {
        border: none;
        background: transparent;
        color: #397ba5;
        font-weight: 700;
        font-size: 11px;
        cursor: pointer;
        margin-right: 9px;
    }

    .action-btn.edit { color: #b18a32; }

    .table-footer {
        padding: 17px 22px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #89958d;
        font-size: 11px;
    }

    .empty-state {
        text-align: center;
        padding: 35px;
        color: #89958d;
        display: none;
        font-size: 12px;
    }

    @media(max-width: 850px) {
        .stats-grid { grid-template-columns: 1fr; }
        .search-input { width: 170px; }
    }
</style>

<div class="student-header">
    <div>
        <h1 class="page-heading">Manage Student Accounts</h1>
        <p class="page-description">
            View, manage, and monitor registered student accounts.
        </p>
    </div>

    <a class="add-student-btn" href="{{ route('admin.students.create') }}">
        + Add Student
    </a>
</div>

@if (session('status'))
    <div class="mb-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800" role="status">
        {{ session('status') }}
    </div>
@endif

<div class="stats-grid">

    <div class="stat-card">
        <div class="stat-top">
            <span class="stat-label">Total Students</span>
            <span class="stat-icon green-icon">♙</span>
        </div>
        <div class="stat-number" id="totalCount">6</div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <span class="stat-label">Active Accounts</span>
            <span class="stat-icon blue-icon">✓</span>
        </div>
        <div class="stat-number" id="activeCount">4</div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <span class="stat-label">Inactive Accounts</span>
            <span class="stat-icon gold-icon">◷</span>
        </div>
        <div class="stat-number" id="inactiveCount">2</div>
    </div>

</div>

<div class="student-panel">

    <div class="panel-heading">
        <div>
            <div class="panel-title">Student Directory</div>
            <div class="panel-subtitle">Registered student account information</div>
        </div>

        <div class="student-controls">
            <input
                type="text"
                id="studentSearch"
                class="search-input"
                placeholder="Search student..."
            >

            <select id="statusFilter" class="status-filter">
                <option value="all">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
    </div>

    <div class="table-wrapper">
        <table class="student-table">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Student ID</th>
                    <th>Course</th>
                    <th>Year Level</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody id="studentTable">

                <tr data-status="active">
                    <td>
                        <div class="student-person">
                            <div class="student-avatar">JD</div>
                            <div>
                                <div class="student-name">Juan Dela Cruz</div>
                                <div class="student-email">juan@example.com</div>
                            </div>
                        </div>
                    </td>
                    <td>2024-00125</td>
                    <td>BS Information Technology</td>
                    <td>3rd Year</td>
                    <td><span class="status-badge active-status">Active</span></td>
                    <td>
                        <button class="action-btn" type="button">View</button>
                        <button class="action-btn edit" type="button">Edit</button>
                    </td>
                </tr>

                <tr data-status="active">
                    <td>
                        <div class="student-person">
                            <div class="student-avatar">MS</div>
                            <div>
                                <div class="student-name">Maria Santos</div>
                                <div class="student-email">maria@example.com</div>
                            </div>
                        </div>
                    </td>
                    <td>2024-00126</td>
                    <td>BS Computer Science</td>
                    <td>2nd Year</td>
                    <td><span class="status-badge active-status">Active</span></td>
                    <td>
                        <button class="action-btn" type="button">View</button>
                        <button class="action-btn edit" type="button">Edit</button>
                    </td>
                </tr>

                <tr data-status="inactive">
                    <td>
                        <div class="student-person">
                            <div class="student-avatar">AR</div>
                            <div>
                                <div class="student-name">Angela Reyes</div>
                                <div class="student-email">angela@example.com</div>
                            </div>
                        </div>
                    </td>
                    <td>2023-00451</td>
                    <td>BS Information Technology</td>
                    <td>4th Year</td>
                    <td><span class="status-badge inactive-status">Inactive</span></td>
                    <td>
                        <button class="action-btn" type="button">View</button>
                        <button class="action-btn edit" type="button">Edit</button>
                    </td>
                </tr>

                <tr data-status="active">
                    <td>
                        <div class="student-person">
                            <div class="student-avatar">CM</div>
                            <div>
                                <div class="student-name">Carlos Mendoza</div>
                                <div class="student-email">carlos@example.com</div>
                            </div>
                        </div>
                    </td>
                    <td>2024-00210</td>
                    <td>BS Information Systems</td>
                    <td>2nd Year</td>
                    <td><span class="status-badge active-status">Active</span></td>
                    <td>
                        <button class="action-btn" type="button">View</button>
                        <button class="action-btn edit" type="button">Edit</button>
                    </td>
                </tr>

                <tr data-status="inactive">
                    <td>
                        <div class="student-person">
                            <div class="student-avatar">SL</div>
                            <div>
                                <div class="student-name">Sofia Lopez</div>
                                <div class="student-email">sofia@example.com</div>
                            </div>
                        </div>
                    </td>
                    <td>2023-00315</td>
                    <td>BS Computer Science</td>
                    <td>3rd Year</td>
                    <td><span class="status-badge inactive-status">Inactive</span></td>
                    <td>
                        <button class="action-btn" type="button">View</button>
                        <button class="action-btn edit" type="button">Edit</button>
                    </td>
                </tr>

                <tr data-status="active">
                    <td>
                        <div class="student-person">
                            <div class="student-avatar">MR</div>
                            <div>
                                <div class="student-name">Miguel Ramos</div>
                                <div class="student-email">miguel@example.com</div>
                            </div>
                        </div>
                    </td>
                    <td>2024-00198</td>
                    <td>BS Information Technology</td>
                    <td>1st Year</td>
                    <td><span class="status-badge active-status">Active</span></td>
                    <td>
                        <button class="action-btn" type="button">View</button>
                        <button class="action-btn edit" type="button">Edit</button>
                    </td>
                </tr>

            </tbody>
        </table>
    </div>

    <div class="empty-state" id="emptyState">
        No students found matching your search.
    </div>

    <div class="table-footer">
        <span>Showing <strong id="visibleCount">6</strong> sample student records</span>
        <span>Student Directory</span>
    </div>

</div>

<script>
    const searchInput = document.getElementById('studentSearch');
    const statusFilter = document.getElementById('statusFilter');
    const rows = document.querySelectorAll('#studentTable tr');

    function filterStudents() {
        const search = searchInput.value.toLowerCase();
        const status = statusFilter.value;
        let visible = 0;

        rows.forEach(row => {
            const matchesSearch = row.textContent.toLowerCase().includes(search);
            const matchesStatus = status === 'all' || row.dataset.status === status;

            const show = matchesSearch && matchesStatus;
            row.style.display = show ? '' : 'none';

            if (show) visible++;
        });

        document.getElementById('visibleCount').textContent = visible;
        document.getElementById('emptyState').style.display =
            visible === 0 ? 'block' : 'none';
    }

    searchInput.addEventListener('input', filterStudents);
    statusFilter.addEventListener('change', filterStudents);
</script>

@endsection
