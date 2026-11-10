
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
    .summary-card span {
        color: #849087;
        font-size: 13px;
    }
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
    .category-status {
        display: inline-block;
        padding: 5px 11px;
        background: #e8f5eb;
        color: #278047;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
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

    <div class="category-summary">
        <div class="summary-card">
            <span>Total Categories</span>
            <h2 id="totalCategories">6</h2>
        </div>
        <div class="summary-card">
            <span>Active Categories</span>
            <h2 id="activeCategories">6</h2>
        </div>
        <div class="summary-card">
            <span>Inactive Categories</span>
            <h2 id="inactiveCategories">0</h2>
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

            <button class="add-category-btn" onclick="openAddModal()">
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
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="categoryTableBody">
                    <tr>
                        <td><div class="category-name"><span class="category-icon">📱</span> Electronics</div></td>
                        <td>Phones, gadgets, and electronic devices</td>
                        <td>24</td>
                        <td><span class="category-status">Active</span></td>
                        <td><button class="action-btn" onclick="editCategory(this)">Edit</button><button class="action-btn delete-btn" onclick="deleteCategory(this)">Delete</button></td>
                    </tr>
                    <tr>
                        <td><div class="category-name"><span class="category-icon">🎒</span> Bags</div></td>
                        <td>Backpacks, handbags, and wallets</td>
                        <td>18</td>
                        <td><span class="category-status">Active</span></td>
                        <td><button class="action-btn" onclick="editCategory(this)">Edit</button><button class="action-btn delete-btn" onclick="deleteCategory(this)">Delete</button></td>
                    </tr>
                    <tr>
                        <td><div class="category-name"><span class="category-icon">📚</span> Books</div></td>
                        <td>Textbooks, notebooks, and reading materials</td>
                        <td>12</td>
                        <td><span class="category-status">Active</span></td>
                        <td><button class="action-btn" onclick="editCategory(this)">Edit</button><button class="action-btn delete-btn" onclick="deleteCategory(this)">Delete</button></td>
                    </tr>
                    <tr>
                        <td><div class="category-name"><span class="category-icon">👕</span> Clothing</div></td>
                        <td>Jackets, uniforms, and other clothing</td>
                        <td>9</td>
                        <td><span class="category-status">Active</span></td>
                        <td><button class="action-btn" onclick="editCategory(this)">Edit</button><button class="action-btn delete-btn" onclick="deleteCategory(this)">Delete</button></td>
                    </tr>
                    <tr>
                        <td><div class="category-name"><span class="category-icon">🔑</span> Accessories</div></td>
                        <td>Keys, watches, jewelry, and accessories</td>
                        <td>15</td>
                        <td><span class="category-status">Active</span></td>
                        <td><button class="action-btn" onclick="editCategory(this)">Edit</button><button class="action-btn delete-btn" onclick="deleteCategory(this)">Delete</button></td>
                    </tr>
                    <tr>
                        <td><div class="category-name"><span class="category-icon">📄</span> Documents</div></td>
                        <td>IDs, school documents, and certificates</td>
                        <td>7</td>
                        <td><span class="category-status">Active</span></td>
                        <td><button class="action-btn" onclick="editCategory(this)">Edit</button><button class="action-btn delete-btn" onclick="deleteCategory(this)">Delete</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add / Edit Modal -->
<div class="category-modal" id="categoryModal">
    <div class="modal-content">
        <h3 id="modalTitle">Add Category</h3>

        <form id="categoryForm">
            <div class="form-group">
                <label for="categoryName">Category Name</label>
                <input type="text" id="categoryName" required placeholder="Enter category name">
            </div>

            <div class="form-group">
                <label for="categoryDescription">Description</label>
                <textarea id="categoryDescription" rows="3" placeholder="Enter category description"></textarea>
            </div>

            <div class="modal-actions">
                <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
                <button type="submit" class="save-btn">Save Category</button>
            </div>
        </form>
    </div>
</div>

<script>
    let editingRow = null;

    function updateCounts() {
        const rows = document.querySelectorAll('#categoryTableBody tr');
        document.getElementById('totalCategories').textContent = rows.length;
        document.getElementById('activeCategories').textContent = rows.length;
        document.getElementById('inactiveCategories').textContent = 0;
    }

    function openAddModal() {
        editingRow = null;
        document.getElementById('modalTitle').textContent = 'Add Category';
        document.getElementById('categoryForm').reset();
        document.getElementById('categoryModal').classList.add('show');
    }

    function closeModal() {
        document.getElementById('categoryModal').classList.remove('show');
    }

    function editCategory(button) {
        editingRow = button.closest('tr');
        const name = editingRow.querySelector('.category-name').textContent.trim();
        const description = editingRow.cells[1].textContent.trim();

        document.getElementById('modalTitle').textContent = 'Edit Category';
        document.getElementById('categoryName').value = name;
        document.getElementById('categoryDescription').value = description;
        document.getElementById('categoryModal').classList.add('show');
    }

    function deleteCategory(button) {
        if (confirm('Are you sure you want to delete this sample category?')) {
            button.closest('tr').remove();
            updateCounts();
        }
    }

    document.getElementById('categoryForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const name = document.getElementById('categoryName').value.trim();
        const description = document.getElementById('categoryDescription').value.trim();

        if (!name) return;

        if (editingRow) {
            editingRow.querySelector('.category-name').innerHTML =
                '<span class="category-icon">📦</span> ' + escapeHTML(name);
            editingRow.cells[1].textContent = description || 'No description provided';
        } else {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><div class="category-name"><span class="category-icon">📦</span> ${escapeHTML(name)}</div></td>
                <td>${escapeHTML(description || 'No description provided')}</td>
                <td>0</td>
                <td><span class="category-status">Active</span></td>
                <td><button class="action-btn" onclick="editCategory(this)">Edit</button><button class="action-btn delete-btn" onclick="deleteCategory(this)">Delete</button></td>
            `;
            document.getElementById('categoryTableBody').appendChild(row);
        }

        closeModal();
        updateCounts();
    });

    function escapeHTML(value) {
        return value.replace(/[&<>"']/g, function(character) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[character];
        });
    }

    updateCounts();
</script>

@endsection
