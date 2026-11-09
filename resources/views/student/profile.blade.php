@extends('layouts.student')

@section('title', 'Student Profile')
@section('page-label', 'Student Profile')

@section('content')

<style>
    .profile-page {
        color: #24352d;
    }

    .profile-heading {
        margin-bottom: 24px;
    }

    .profile-heading h1 {
        font-size: 25px;
        font-weight: 700;
        margin-bottom: 6px;
        color: #174b38;
    }

    .profile-heading p {
        color: #77847d;
        font-size: 14px;
    }

    .profile-card {
        background: #fff;
        border: 1px solid #e8eee9;
        border-radius: 14px;
        padding: 25px;
        margin-bottom: 22px;
        box-shadow: 0 3px 12px rgba(24, 60, 43, .04);
    }

    .profile-banner {
        display: flex;
        align-items: center;
        gap: 22px;
        flex-wrap: wrap;
    }

    .profile-avatar {
        width: 95px;
        height: 95px;
        border-radius: 50%;
        background: #e5eee8;
        color: #174b38;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 32px;
        font-weight: 700;
        border: 4px solid #f5f8f5;
    }

    .profile-main-info {
        flex: 1;
    }

    .profile-main-info h2 {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 5px;
        color: #174b38;
    }

    .profile-main-info p {
        color: #77847d;
        font-size: 14px;
        margin-bottom: 10px;
    }

    .status-badge {
        display: inline-block;
        padding: 5px 12px;
        background: #e7f5eb;
        color: #258047;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .section-title {
        font-size: 17px;
        font-weight: 700;
        color: #174b38;
        margin-bottom: 22px;
        padding-bottom: 14px;
        border-bottom: 1px solid #edf0ed;
    }

    .profile-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 22px;
    }

    .info-item label {
        display: block;
        font-size: 12px;
        color: #87928b;
        margin-bottom: 7px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .4px;
    }

    .info-item p {
        color: #34483c;
        font-size: 14px;
        font-weight: 500;
        margin: 0;
        overflow-wrap: anywhere;
    }

    .profile-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 22px;
    }

    .profile-btn {
        padding: 10px 18px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        border: none;
    }

    .btn-edit {
        background: #174b38;
        color: white;
    }

    .btn-edit:hover {
        background: #103b2b;
    }

    .btn-secondary {
        background: #f1f4f1;
        color: #43564a;
    }

    .profile-note {
        background: #f8faf8;
        border: 1px solid #e8eee9;
        border-radius: 10px;
        padding: 16px;
        color: #65756b;
        font-size: 13px;
        line-height: 1.6;
    }

    @media(max-width: 650px) {
        .profile-grid {
            grid-template-columns: 1fr;
        }

        .profile-card {
            padding: 18px;
        }

        .profile-avatar {
            width: 75px;
            height: 75px;
            font-size: 26px;
        }
    }
</style>

<div class="profile-page">

    <div class="profile-heading">
        <h1>Student Profile</h1>
        <p>View and manage your personal student information.</p>
    </div>

    <!-- Profile Overview -->
    <div class="profile-card">
        <div class="profile-banner">

            <div class="profile-avatar">
                JD
            </div>

            <div class="profile-main-info">
                <h2>Juan Dela Cruz</h2>
                <p>Student ID: 2024-00001</p>
                <span class="status-badge">Active Student</span>
            </div>

        </div>
    </div>

    <!-- Personal Information -->
    <div class="profile-card">

        <h3 class="section-title">Personal Information</h3>

        <div class="profile-grid">

            <div class="info-item">
                <label>First Name</label>
                <p>Juan</p>
            </div>

            <div class="info-item">
                <label>Last Name</label>
                <p>Dela Cruz</p>
            </div>

            <div class="info-item">
                <label>Middle Name</label>
                <p>Santos</p>
            </div>

            <div class="info-item">
                <label>Date of Birth</label>
                <p>January 15, 2005</p>
            </div>

            <div class="info-item">
                <label>Gender</label>
                <p>Male</p>
            </div>

            <div class="info-item">
                <label>Contact Number</label>
                <p>0912 345 6789</p>
            </div>

            <div class="info-item">
                <label>Email Address</label>
                <p>juan.delacruz@example.com</p>
            </div>

            <div class="info-item">
                <label>Address</label>
                <p>Indang, Cavite, Philippines</p>
            </div>

        </div>

    </div>

    <!-- Academic Information -->
    <div class="profile-card">

        <h3 class="section-title">Academic Information</h3>

        <div class="profile-grid">

            <div class="info-item">
                <label>Student ID</label>
                <p>2024-00001</p>
            </div>

            <div class="info-item">
                <label>College / Department</label>
                <p>College of Information Technology</p>
            </div>

            <div class="info-item">
                <label>Course</label>
                <p>BS Information Technology</p>
            </div>

            <div class="info-item">
                <label>Year Level</label>
                <p>2nd Year</p>
            </div>

            <div class="info-item">
                <label>Academic Status</label>
                <p>Regular</p>
            </div>

            <div class="info-item">
                <label>Account Type</label>
                <p>Student</p>
            </div>

        </div>

        <div class="profile-actions">
            <button type="button" class="profile-btn btn-secondary">
                Cancel
            </button>

            <button type="button" class="profile-btn btn-edit"
                onclick="alert('Edit Profile will be available in a future update.')">
                Edit Profile
            </button>
        </div>

    </div>

    <div class="profile-note">
        <strong>Note:</strong> This is a sample student profile for the frontend
        prototype. Actual student information and profile editing will be
        connected after database integration.
    </div>

</div>

@endsection
