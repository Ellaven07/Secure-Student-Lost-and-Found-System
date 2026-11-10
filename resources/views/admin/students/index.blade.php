
@extends('layouts.app')

@section('title', 'Manage Student Accounts')

@section('content')

<style>
    .student-page { color: #26382e; }
    .student-header { margin-bottom: 24px; }
    .student-header h1 { color: #20392c; font-size: 23px; font-weight: 800; }
    .student-header p { margin-top: 5px; color: #849188; font-size: 12px; }

    .stats-section {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        margin-bottom: 25px;
        padding: 17px 0;
        border-top: 1px solid #e4eae5;
        border-bottom: 1px solid #e4eae5;
    }

    .stat-item { display: flex; align-items: center; gap: 13px; padding: 5px 24px; }
    .stat-item:first-child { padding-left: 5px; }
    .stat-item + .stat-item { border-left: 1px solid #dfe6e0; }
    .stat-icon {
        display: grid; place-items: center; width: 38px; height: 38px;
        flex-shrink: 0; border-radius: 50%; background: #edf3ee;
        color: #24563d; font-size: 17px; font-weight: 700;
    }
    .stat-label { color: #829087; font-size: 11px; font-weight: 600; }
    .stat-number { margin-top: 3px; color: #20392c; font-size: 23px; font-weight: 800; }

    .student-panel { overflow: hidden; background: #fff; border: 1px solid #e5ebe6; border-radius: 9px; }
    .panel-heading {
        display: flex; justify-content: space-between; align-items: center;
        gap: 15px; flex-wrap: wrap; padding: 19px 22px;
        border-bottom: 1px solid #edf0ed;
    }
    .panel-title { color: #263b2e; font-size: 14px; font-weight: 800; }
    .panel-subtitle { margin-top: 4px; color: #8a968e; font-size: 11px; }

    .add-button {
        display: inline-flex; align-items: center; justify-content: center;
        gap: 7px; height: 37px; padding: 0 15px; border-radius: 6px;
        background: #174b38; color: #fff; font-size: 11px;
        font-weight: 700; text-decoration: none; transition: .2s;
    }
    .add-button:hover { background: #28634c; }

    .student-controls { display: flex; gap: 9px; flex-wrap: wrap; }
    .search-input, .status-filter {
        height: 37px; padding: 0 11px; border: 1px solid #dfe7e0;
        border-radius: 6px; outline: none; background: #fff;
        color: #34463a; font-family: inherit; font-size: 11px;
    }
    .search-input { width: 220px; }
    .search-input:focus, .status-filter:focus { border-color: #397b5b; }

    .search-button, .reset-button {
        display: inline-flex; align-items: center; justify-content: center;
        height: 37px; padding: 0 15px; border-radius: 6px;
        font-size: 11px; font-weight: 700; text-decoration: none; cursor: pointer;
    }
    .search-button { border: 0; background: #174b38; color: #fff; }
    .search-button:hover { background: #28634c; }
    .reset-button { border: 1px solid #dfe7e0; color: #526157; background: #fff; }

    .table-wrapper { overflow-x: auto; }
    .student-table { width: 100%; border-collapse: collapse; text-align: left; white-space: nowrap; }
    .student-table th {
        padding: 13px 15px; background: #f8faf8; color: #849188;
        font-size: 10px; font-weight: 800; letter-spacing: .4px; text-transform: uppercase;
    }
    .student-table td { padding: 14px 15px; border-bottom: 1px solid #eff2ef; color: #526157; font-size: 11px; }
    .student-table tbody tr:hover { background: #fbfdfb; }

    .student-person { display: flex; align-items: center; gap: 10px; }
    .student-avatar {
        display: grid; place-items: center; width: 35px; height: 35px;
        flex-shrink: 0; border-radius: 50%; background: #e7efe7;
        color: #20533a; font-size: 12px; font-weight: 800;
    }
    .student-name { color: #293b30; font-weight: 700; }
    .student-email { margin-top: 3px; color: #98a29a; font-size: 10px; }

    .status-badge {
        display: inline-block; padding: 5px 9px; border-radius: 20px;
        font-size: 10px; font-weight: 700; text-transform: capitalize;
    }
    .active-status { background: #e5f3e9; color: #267246; }
    .inactive-status { background: #f0f1f0; color: #737d75; }

    .action-links { display: flex; align-items: center; gap: 10px; }
    .action-links a, .action-links button {
        border: 0; background: none; padding: 0; cursor: pointer;
        font-family: inherit; font-size: 10px; font-weight: 700; text-decoration: none;
    }
    .view-link { color: #28634c; }
    .edit-link { color: #3976a5; }
    .toggle-link { color: #b45353; }
    .activate-link { color: #267246; }
    .delete-link { color: #b42318; }

    .empty-state { padding: 38px 20px; color: #89958d; text-align: center; font-size: 12px; }
    .table-footer {
        display: flex; justify-content: space-between; align-items: center;
        gap: 12px; flex-wrap: wrap; padding: 15px 20px;
        color: #89958d; font-size: 11px;
    }
    .pagination-wrapper nav { display: flex; justify-content: flex-end; }
    .pagination-wrapper nav div:first-child { display: none; }
    .pagination-wrapper nav div:last-child { display: flex; align-items: center; gap: 4px; }
    .pagination-wrapper nav a,
    .pagination-wrapper nav span[aria-current="page"] span,
    .pagination-wrapper nav span[aria-disabled="true"] span {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 29px; height: 29px; padding: 0 8px;
        border: 1px solid #e3e9e4; border-radius: 5px;
        color: #526157; font-size: 11px; text-decoration: none;
    }
    .pagination-wrapper nav span[aria-current="page"] span {
        border-color: #174b38; background: #174b38; color: #fff;
    }

    @media (max-width: 700px) {
        .stats-section { grid-template-columns: 1fr; gap: 12px; }
        .stat-item, .stat-item:first-child { padding: 5px 3px; }
        .stat-item + .stat-item { padding-top: 14px; border-top: 1px solid #e4eae5; border-left: none; }
        .panel-heading { align-items: flex-start; }
        .student-controls { width: 100%; }
        .search-input { flex: 1; min-width: 120px; }
    }
</style>

<div class="student-page">

    <div class="student-header">
        <h1>Manage Student Accounts</h1>
        <p>View, register, and monitor student accounts.</p>
    </div>

    @if (session('success'))
        <div class="mb-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <section class="stats-section">
        <div class="stat-item">
            <div class="stat-icon">#</div>
            <div>
                <div class="stat-label">Total Students</div>
                <div class="stat-number">{{ number_format($totalStudents) }}</div>
            </div>
        </div>

        <div class="stat-item">
            <div class="stat-icon">&#10003;</div>
            <div>
                <div class="stat-label">Active Accounts</div>
                <div class="stat-number">{{ number_format($activeStudents) }}</div>
            </div>
        </div>

        <div class="stat-item">
            <div class="stat-icon">&#9679;</div>
            <div>
                <div class="stat-label">Inactive Accounts</div>
                <div class="stat-number">{{ number_format($inactiveStudents) }}</div>
            </div>
        </div>
    </section>

    <section class="student-panel">

        <div class="panel-heading">
            <div>
                <div class="panel-title">Student Directory</div>
                <div class="panel-subtitle">Registered student account information</div>
            </div>

            <a href="{{ route('admin.students.create') }}" class="add-button">
                <span>+</span> Add Student
            </a>
        </div>

        <div class="panel-heading">
            <form method="GET" action="{{ route('admin.students.index') }}" class="student-controls">

                <input type="text" name="search" class="search-input"
                       placeholder="Search name, email or student ID..."
                       value="{{ request('search') }}">

                <select name="department" class="status-filter">
                    <option value="">All Departments</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department }}" @selected(request('department') === $department)>
                            {{ $department }}
                        </option>
                    @endforeach
                </select>

                <select name="course" class="status-filter">
                    <option value="">All Courses</option>
                    @foreach ($courses as $course)
                        <option value="{{ $course }}" @selected(request('course') === $course)>
                            {{ $course }}
                        </option>
                    @endforeach
                </select>

                <select name="year_level" class="status-filter">
                    <option value="">All Year Levels</option>
                    @foreach ($yearLevels as $year)
                        <option value="{{ $year }}" @selected(request('year_level') === $year)>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>

                <select name="status" class="status-filter">
                    <option value="">All Status</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                </select>

                <button type="submit" class="search-button">Apply Filters</button>

                @if (request()->query())
                    <a href="{{ route('admin.students.index') }}" class="reset-button">Reset</a>
                @endif
            </form>
        </div>

        <div class="table-wrapper">
            <table class="student-table">
                <thead>
                    <tr>
                        <th>Student Information</th>
                        <th>Student Number</th>
                        <th>Department</th>
                        <th>Course / Program</th>
                        <th>Year Level</th>
                        <th>Account Status</th>
                        <th>Registration Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($students as $student)
                        <tr>
                            <td>
                                <div class="student-person">
                                    <div class="student-avatar">
                                        {{ strtoupper(substr($student->name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="student-name">{{ $student->name }}</div>
                                        <div class="student-email">{{ $student->email }}</div>
                                    </div>
                                </div>
                            </td>

                            <td>{{ $student->student_no ?: '—' }}</td>
                            <td>{{ $student->department ?: '—' }}</td>
                            <td>{{ $student->course ?: '—' }}</td>
                            <td>{{ $student->year_level ?: '—' }}</td>

                            <td>
                                @if ($student->status === 'active')
                                    <span class="status-badge active-status">Active</span>
                                @else
                                    <span class="status-badge inactive-status">
                                        {{ ucfirst($student->status ?: 'Inactive') }}
                                    </span>
                                @endif
                            </td>

                            <td>{{ $student->created_at?->format('M d, Y') ?? '—' }}</td>

                            <td>
                                <div class="action-links">
                                    <a href="{{ route('admin.students.show', $student) }}" class="view-link">View</a>
                                    <a href="{{ route('admin.students.edit', $student) }}" class="edit-link">Edit</a>

                                    <form action="{{ route('admin.students.status', $student) }}"
                                          method="POST"
                                          onsubmit="return confirm('Are you sure you want to change this account status?');">
                                        @csrf
                                        @method('PATCH')

                                        <button type="submit"
                                            class="{{ $student->status === 'active' ? 'toggle-link' : 'activate-link' }}">
                                            {{ $student->status === 'active' ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.students.destroy', $student) }}"
                                          method="POST"
                                          onsubmit="return confirm('Delete this student account? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="delete-link">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">No student records found.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="table-footer">
            <span>
                Showing {{ $students->firstItem() ?? 0 }}
                to {{ $students->lastItem() ?? 0 }}
                of {{ $students->total() }} student records
            </span>

            <div class="pagination-wrapper">
                {{ $students->links() }}
            </div>
        </div>

    </section>
</div>

@endsection
