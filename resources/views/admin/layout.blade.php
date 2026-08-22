<!doctype html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>@yield('title') · C-Net Admin</title><link rel="stylesheet" href="{{ asset('css/admin.css') }}"></head>
<body><div class="admin-shell"><aside class="sidebar"><div class="logo"><img src="{{ asset('images/cnet-logo.webp') }}" alt="C-Net"><div><b>C-Net Admin</b><small>Computer Education</small></div></div><nav>
<a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">⌂ Dashboard</a>
<a class="{{ request()->routeIs('admin.courses.*') ? 'active' : '' }}" href="{{ route('admin.courses.index') }}">▣ Courses</a>
<a class="{{ request()->routeIs('admin.admissions.*') ? 'active' : '' }}" href="{{ route('admin.admissions.index') }}">✓ Admissions</a>
<a class="{{ request()->routeIs('admin.students.*') ? 'active' : '' }}" href="{{ route('admin.students.index') }}">♟ Students</a>
<a class="{{ request()->routeIs('admin.fees.dues') ? 'active' : '' }}" href="{{ route('admin.fees.dues') }}">₹ Fee Dues</a>
<a class="{{ request()->routeIs('admin.fees.collections') ? 'active' : '' }}" href="{{ route('admin.fees.collections') }}">▤ Collection Report</a>
<a class="{{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}" href="{{ route('admin.attendance.index') }}">◫ Attendance</a>
<a class="{{ request()->routeIs('admin.learning.*') ? 'active' : '' }}" href="{{ route('admin.learning.index') }}">▰ Study Materials</a>
<a class="{{ request()->routeIs('admin.assignments.*') ? 'active' : '' }}" href="{{ route('admin.assignments.index') }}">✓ Assignment Review</a>
<a class="{{ request()->routeIs('admin.practice.*') ? 'active' : '' }}" href="{{ route('admin.practice.index') }}">◉ Practice Tests</a>
<a class="{{ request()->routeIs('admin.progress.*') ? 'active' : '' }}" href="{{ route('admin.progress.index') }}">↗ Progress Reports</a>
<a class="{{ request()->routeIs('admin.results.*') ? 'active' : '' }}" href="{{ route('admin.results.index') }}">★ Exams & Results</a>
<a class="{{ request()->routeIs('admin.certificates.*') ? 'active' : '' }}" href="{{ route('admin.certificates.index') }}">◇ Certificates</a>
<a class="{{ request()->routeIs('admin.enquiries.*') ? 'active' : '' }}" href="{{ route('admin.enquiries.index') }}">✉ Enquiries</a>
<a class="{{ request()->routeIs('admin.jobs.*') ? 'active' : '' }}" href="{{ route('admin.jobs.index') }}">⌕ Job Opportunities</a>
<a class="{{ request()->routeIs('admin.notices.*') ? 'active' : '' }}" href="{{ route('admin.notices.index') }}">◆ Notices</a>
<a class="{{ request()->routeIs('admin.gallery.*') ? 'active' : '' }}" href="{{ route('admin.gallery.index') }}">▧ Gallery</a>
<a class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.edit') }}">◉ Website Settings</a>
<a class="{{ request()->routeIs('admin.profile.*') ? 'active' : '' }}" href="{{ route('admin.profile.edit') }}">⚙ Profile & Security</a>
<a href="{{ route('home') }}" target="_blank">↗ View Website</a>
</nav></aside><main class="main"><div class="topbar"><div><small>ADMIN PANEL</small><h1>@yield('title')</h1><p class="welcome">Welcome, {{ auth()->user()->name }}</p></div><form method="post" action="{{ route('admin.logout') }}">@csrf<button class="logout">Logout</button></form></div>@if(session('success'))<div class="flash">{{ session('success') }}</div>@endif @if($errors->any())<div class="alert"><b>Please check the form:</b><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif @yield('content')</main></div></body></html>
