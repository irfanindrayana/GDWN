@extends('layouts.app')

@section('title', 'Manajemen Akun')

@section('content')
<div class="container-fluid">
    <h1 class="mt-4 mb-4">Manajemen Akun</h1>

    <div class="card">
        <div class="card-header">
            @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.create') }}" class="btn btn-primary mb-3">Tambah Admin</a>
            @endif
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table id="datatablesSimple" class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th width="25%">Nama</th>
                            <th width="25%">Email</th>
                            <th class="text-center" width="15%">Role</th>
                            <th class="ps-3" width="30%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $index => $user)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td class="text-center">
                                @if($user->role === 'admin')
                                    <span class="badge bg-primary">Admin</span>
                                @elseif($user->role === 'staff')
                                    <span class="badge bg-success">Staff</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($user->role) }}</span>
                                @endif
                            </td>
                            <td class="ps-3">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.edit', $user->id) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm ms-1" onclick="showDeleteModal({{ $user->id }}, '{{ $user->name }}')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $users->links() }}
        </div>
    </div>
</div>

<!-- Modal Verifikasi Password untuk Delete -->
<div class="modal fade" id="deleteVerificationModal" tabindex="-1" aria-labelledby="deleteVerificationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteVerificationModalLabel">Verifikasi Penghapusan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Anda akan menghapus user <strong id="deleteUserName"></strong></p>
                    <div class="mb-3">
                        <label for="verificationPassword" class="form-label">Masukkan Password Anda untuk Verifikasi</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="verificationPassword" name="verification_password" required>
                            <button type="button" class="btn btn-outline-secondary" onclick="toggleDeletePassword()">
                                <i class="fa fa-eye" id="eye-delete-verification"></i>
                            </button>
                        </div>
                        <div id="deletePasswordError" class="invalid-feedback" style="display: none;">
                            Password verifikasi salah
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endpush

@push('scripts')
<script>
function showDeleteModal(userId, userName) {
    const modal = document.getElementById('deleteVerificationModal');
    const form = document.getElementById('deleteForm');
    const userNameElement = document.getElementById('deleteUserName');
    const passwordError = document.getElementById('deletePasswordError');
    
    // Reset form dan error
    form.reset();
    passwordError.style.display = 'none';
    document.getElementById('verificationPassword').classList.remove('is-invalid');
    
    form.action = `/admin/${userId}`;
    userNameElement.textContent = userName;
    
    new bootstrap.Modal(modal).show();
}

function toggleDeletePassword() {
    const passwordInput = document.getElementById('verificationPassword');
    const eyeIcon = document.getElementById('eye-delete-verification');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
    }
}

// Handle form submission
document.getElementById('deleteForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const passwordInput = document.getElementById('verificationPassword');
    const passwordError = document.getElementById('deletePasswordError');
    
    fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            passwordInput.classList.add('is-invalid');
            passwordError.style.display = 'block';
            passwordError.textContent = data.error;
        } else {
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});
</script>
@endpush
@endsection 