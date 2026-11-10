
@extends('layouts.app')

@section('title', 'Manage Categories')
@section('page-label', 'Manage Categories')

@section('content')

<style>
    .categories-page { color: #263a30; }
    .page-heading { margin-bottom: 25px; }
    .page-heading h1 {
        color: #174b38;
        font-size: 25px;
        font-weight: 700;
        margin-bottom: 6px;
    }
    .page-heading p { color: #78857d; font-size: 14px; }

    .category-summary {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 18px;
        margin-bottom: 24px;
    }
    .summary-card {
        background: white;
        border: 1px solid #e8eee9;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 3px 12px rgba(24,60,43,.04);
    }
    .summary-card span { color: #849087; font-size: 13px; }
    .summary-card h2 {
        color: #174b38;
        font-size: 27px;
        font-weight: 700;
        margin-top: 8px;
    }

    .category-panel {
        background: white;
        border: 1px solid #e8eee9;
        border-radius: 13px;
        padding: 23px;
        box-shadow: 0 3px 12px rgba(24,60,43,.04);
    }
    .panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
        margin-bottom: 22px;
    }
    .panel-header h2 {
        color: #174b38;
        font-size: 17px;
        font-weight: 700;
    }
    .add-category-btn {
        background: #174b38;
        color: white;
        border: 0;
        border-radius: 8px;
        padding: 10px 16px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
    }
    .add-category-btn:hover { background: #103b2b; }

    .category-table-wrap { overflow-x: auto; }
    .category-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 650px;
    }
    .category-table th {
        background: #f6f8f6;
        color: #78857d;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .5px;
        text-align: left;
        padding: 14px 13px;
    }
    .category-table td {
        padding: 16px 13px;
        border-bottom: 1px solid #edf1ed;
        font-size: 13px;
        color: #405247;
    }
    .category-table tr:last-child td { border-bottom: 0; }

    .category-name {
        display: flex;
        align-items: center;
        gap: 11px;
        font-weight: 600;
        color: #294536;
    }
    .category-icon {
        width: 36px;
        height: 36px;
        display: flex;
        justify-content: center;
        align-items: center;
        background: #edf4ef;
        border-radius: 9px;
        font-size: 17px;
    }
    .action-btn {
        background: #f0f4f1;
        color: #315541;
        border: 0;
        border-radius: 6px;
        padding: 7px 11px;
        font-size: 12px;
        cursor: pointer;
        margin-right: 4px;
    }
    .action-btn:hover { background: #e1ebe4; }
    .delete-btn { color: #b74747; }

    .category-modal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(18,35,26,.45);
        z-index: 9999;
        justify-content: center;
        align-items: center;
        padding: 18px;
    }
    .category-modal.show { display: flex; }
    .modal-content {
        background: white;
        width: 100%;
        max-width: 440px;
        border-radius: 14px;
        padding: 25px;
        box-shadow: 0 15px 45px rgba(0,0,0,.15);
    }
    .modal-content h3 {
        color: #174b38;
        font-size: 19px;
        font-weight: 700;
        margin-bottom: 20px;
    }
    .form-group { margin-bottom: 16px; }
    .form-group label {
        display: block;
        font-size: 12px;
        color: #526258;
        font-weight: 600;
        margin-bottom: 7px;
    }
    .form-group input, .form-group textarea {
        width: 100%;
        border: 1px solid #dfe7e1;
        border-radius: 8px;
        padding: 11px 12px;
        outline: none;
        font-size: 13px;
        color: #34483c;
    }
    .form-group input:focus, .form-group textarea:focus {
        border-color: #56846b;
    }
    .modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 9px;
        margin-top: 22px;
    }
    .modal-actions button {
        border: 0;
        border-radius: 7px;
        padding: 10px 16px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
    }
    .cancel-btn { background: #eef2ef; color: #43564a; }
    .save-btn { background: #174b38; color: white; }

    .alert-success {
        background: #e8f5eb;
        color: #24633b;
        border: 1px solid #cce8d2;
        padding: 12px 15px;
        border-radius: 8px;
        margin-bottom: 18px;
        font-size: 13px;
    }
    .alert-error {
        background: #fff0f0;
        color: #a83232;
        border: 1px solid #f1cccc;
        padding: 12px 15px;
        border-radius: 8px;
        margin-bottom: 18px;
        font-size: 13px;
    }
    .empty-row {
        text-align: center;
        color: #87928b;
        padding: 35px !important;
    }

    @media(max-width: 650px) {
        .category-summary { grid-template-columns: 1fr; }
        .category-panel { padding: 16px; }
    }
</style>

<div class="categories-page">

    <div class="page-heading">
        <h1>Manage Categories</h1>
        <p>Organize and manage the categories used for lost and found items.</p>
    </div>

    @if (session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert-error">
            <strong>Please check the following:</strong>
            <ul style="margin: 7px 0 0 18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="category-summary">
        <div class="summary-card">
            <span>Total Categories</span>
            <h2>{{ number_format($totalCategories) }}</h2>
        </div>
        <div class="summary-card">
            <span>Categories with Items</span>
            <h2>{{ number_format($categoriesWithItems) }}</h2>
        </div>
        <div class="summary-card">
            <span>Categories without Items</span>
            <h2>{{ number_format($categoriesWithoutItems) }}</h2>
        </div>
    </div>

    <div class="category-panel">
        <div class="panel-header">
            <div>
                <h2>Item Categories</h2>
                <p style="color:#87928b;font-size:12px;margin-top:5px;">
                    List of categories available in the system
                </p>
            </div>

            <button type="button" class="add-category-btn" onclick="openAddModal()">
                + Add Category
            </button>
        </div>

        <div class="category-table-wrap">
            <table class="category-table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Items</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td>
                                <div class="category-name">
                                    <span class="category-icon">📦</span>
                                    {{ $category->name }}
                                </div>
                            </td>
                            <td>{{ $category->description ?: 'No description provided' }}</td>
                            <td>{{ number_format($category->items_count) }}</td>
                            <td>
                                <button
                                    type="button"
                                    class="action-btn"
                                    onclick="openEditModal(
                                        {{ $category->id }},
                                        @js($category->name),
                                        @js($category->description)
                                    )"
                                >Edit</button>

                                <form
                                    method="POST"
                                    action="{{ route('admin.categories.destroy', $category->id) }}"
                                    style="display:inline;"
                                    onsubmit="return confirm('Delete {{ addslashes($category->name) }}? Existing item reports will be preserved, but their category will be cleared.')"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn delete-btn">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="empty-row">
                                No categories have been added yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="category-modal" id="categoryModal">
    <div class="modal-content">
        <h3 id="modalTitle">Add Category</h3>

        <form id="categoryForm" method="POST" action="{{ route('admin.categories.store') }}">
            @csrf

            <input type="hidden" id="formMethod" name="_method" value="POST">

            <div class="form-group">
                <label for="categoryName">Category Name</label>
                <input
                    type="text"
                    id="categoryName"
                    name="name"
                    maxlength="100"
                    required
                    placeholder="Enter category name"
                    value="{{ old('name') }}"
                >
            </div>

            <div class="form-group">
                <label for="categoryDescription">Description</label>
                <textarea
                    id="categoryDescription"
                    name="description"
                    rows="3"
                    maxlength="2000"
                    placeholder="Enter category description"
                >{{ old('description') }}</textarea>
            </div>

            <div class="modal-actions">
                <button type="button" class="cancel-btn" onclick="closeModal()">
                    Cancel
                </button>
                <button type="submit" class="save-btn">
                    Save Category
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const categoryModal = document.getElementById('categoryModal');
    const categoryForm = document.getElementById('categoryForm');
    const formMethod = document.getElementById('formMethod');

    function openAddModal() {
        document.getElementById('modalTitle').textContent = 'Add Category';
        categoryForm.action = @json(route('admin.categories.store'));
        formMethod.value = 'POST';
        document.getElementById('categoryName').value = '';
        document.getElementById('categoryDescription').value = '';
        categoryModal.classList.add('show');
    }

    function openEditModal(id, name, description) {
        document.getElementById('modalTitle').textContent = 'Edit Category';
        categoryForm.action = @json(url('/admin/categories')) + '/' + id;
        formMethod.value = 'PUT';
        document.getElementById('categoryName').value = name || '';
        document.getElementById('categoryDescription').value = description || '';
        categoryModal.classList.add('show');
    }

    function closeModal() {
        categoryModal.classList.remove('show');
    }

    categoryModal.addEventListener('click', function(event) {
        if (event.target === categoryModal) {
            closeModal();
        }
    });

    @if ($errors->any())
        categoryModal.classList.add('show');
    @endif
</script>

@endsection
