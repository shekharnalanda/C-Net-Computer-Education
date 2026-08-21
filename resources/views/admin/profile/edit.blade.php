@extends('admin.layout')
@section('title','Profile & Security')
@section('content')
<div class="profile-header">
    <div class="profile-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
    <div><h2>{{ $user->name }}</h2><p>{{ $user->email }}</p><span>Administrator Account</span></div>
</div>

<div class="profile-grid">
    <section class="panel profile-panel">
        <div class="panel-title"><div><small>ACCOUNT DETAILS</small><h2>Administrator Profile</h2></div></div>
        <p class="section-note">This name and email are used for Admin Panel access. After changing the email, use the new email for your next login.</p>
        <form class="profile-form" method="post" action="{{ route('admin.profile.update') }}">@csrf @method('PUT')
            <label>Administrator Name<input name="name" value="{{ old('name', $user->name) }}" required maxlength="120"></label>
            <label>Login Email<input type="email" name="email" value="{{ old('email', $user->email) }}" required maxlength="190"></label>
            <button class="btn">Save Profile</button>
        </form>
    </section>

    <section class="panel profile-panel security-panel">
        <div class="panel-title"><div><small>ACCOUNT SECURITY</small><h2>Change Password</h2></div></div>
        <p class="section-note">Use at least 10 characters with uppercase, lowercase, number and symbol. Your current password is required.</p>
        <form class="profile-form" method="post" action="{{ route('admin.profile.password') }}">@csrf @method('PUT')
            <label>Current Password<div class="password-field"><input type="password" name="current_password" id="currentPassword" required autocomplete="current-password"><button type="button" onclick="togglePassword('currentPassword',this)">Show</button></div></label>
            <label>New Password<div class="password-field"><input type="password" name="password" id="newPassword" required autocomplete="new-password"><button type="button" onclick="togglePassword('newPassword',this)">Show</button></div></label>
            <label>Confirm New Password<div class="password-field"><input type="password" name="password_confirmation" id="confirmPassword" required autocomplete="new-password"><button type="button" onclick="togglePassword('confirmPassword',this)">Show</button></div></label>
            <div class="password-rules"><b>Strong password checklist</b><span>✓ 10+ characters</span><span>✓ Upper & lowercase letters</span><span>✓ Number and symbol</span></div>
            <button class="btn">Change Password</button>
        </form>
    </section>
</div>

<div class="panel security-info">
    <div><span>🔒</span><div><b>Secure administrator access</b><p>Your password is stored as a one-way secure hash and is never displayed in the Admin Panel.</p></div></div>
    <div><span>↗</span><div><b>Remember your new credentials</b><p>If you change the email or password, record it in a secure password manager.</p></div></div>
</div>

<script>
function togglePassword(id, button) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
    button.textContent = input.type === 'password' ? 'Show' : 'Hide';
}
</script>
@endsection
