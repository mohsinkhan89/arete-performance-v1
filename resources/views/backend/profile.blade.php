@extends('backend.layouts.master')

@section('title', $pageTitle)

@section('body')
    <div class="page-heading">
        <h1>{{ $pageTitle }}</h1>
        <p>Manage your account details and password from one place.</p>
    </div>

    <article class="panel profile-page-panel">
        <div class="profile-hero">
            <span class="avatar profile-avatar"><i class="fa-solid fa-user"></i><b></b></span>
            <div>
                <h2>{{ $admin->name }}</h2>
                <p>{{ $admin->email }}</p>
                <div class="profile-tags">
                    <span><i class="fa-solid fa-shield-halved"></i>{{ ucfirst($admin->role ?? 'admin') }}</span>
                    <span class="{{ ($admin->status ?? 'active') === 'active' ? 'active' : '' }}"><i class="fa-solid fa-circle"></i>{{ ucfirst($admin->status ?? 'active') }}</span>
                </div>
            </div>
            @unless ($editMode)
                <a href="{{ route('backend.profile.edit') }}"><i class="fa-solid fa-pen"></i>Edit Profile</a>
            @endunless
        </div>

        @if ($editMode)
            <form action="{{ route('backend.profile.update') }}" method="POST" class="admin-form profile-form">
                @csrf
                @method('PUT')

                <div class="form-grid">
                    <label>Name
                        <input name="name" value="{{ old('name', $admin->name) }}" required>
                        @error('name')<span>{{ $message }}</span>@enderror
                    </label>
                    <label>Email
                        <input type="email" name="email" value="{{ old('email', $admin->email) }}" required>
                        @error('email')<span>{{ $message }}</span>@enderror
                    </label>
                    <label>Phone
                        <input name="phone" value="{{ old('phone', $admin->phone) }}" placeholder="Add phone number">
                    </label>
                    <label>Current Password
                        <input type="password" name="current_password" placeholder="Required only to change password">
                        @error('current_password')<span>{{ $message }}</span>@enderror
                    </label>
                    <label>Password
                        <input type="password" name="password" placeholder="New password">
                        @error('password')<span>{{ $message }}</span>@enderror
                    </label>
                    <label>Confirm Password
                        <input type="password" name="password_confirmation" placeholder="Confirm new password">
                    </label>
                </div>

                <div class="form-actions">
                    <a href="{{ route('backend.profile') }}">Cancel</a>
                    <button type="submit"><i class="fa-solid fa-floppy-disk"></i>Update Profile</button>
                </div>
            </form>
        @else
            <div class="profile-info-grid">
                <div><span>Full Name</span><strong>{{ $admin->name }}</strong></div>
                <div><span>Email</span><strong>{{ $admin->email }}</strong></div>
                <div><span>Phone</span><strong>{{ $admin->phone ?: 'Not added' }}</strong></div>
                <div><span>Role</span><strong>{{ ucfirst($admin->role ?? 'admin') }}</strong></div>
                <div><span>Status</span><strong>{{ ucfirst($admin->status ?? 'active') }}</strong></div>
                <div><span>Joined</span><strong>{{ $admin->created_at?->format('M d, Y') }}</strong></div>
            </div>
        @endif
    </article>
@endsection
