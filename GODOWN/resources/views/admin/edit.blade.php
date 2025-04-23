@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Edit Role Pengguna</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.update', $user->id) }}" id="editForm">
                        @csrf
                        @method('PUT')

                        <div class="form-group row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-right">Email</label>
                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" value="{{ $user->email }}" disabled>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="role" class="col-md-4 col-form-label text-md-right">Role</label>
                            <div class="col-md-6">
                                <select id="role" name="role" class="form-control @error('role') is-invalid @enderror" required>
                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="staff" {{ $user->role === 'staff' ? 'selected' : '' }}>Staff</option>
                                </select>
                                @error('role')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="new_password" class="col-md-4 col-form-label text-md-right">Password Baru</label>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input id="new_password" type="password" class="form-control @error('new_password') is-invalid @enderror" name="new_password">
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('new_password')">
                                        <i class="fa fa-eye" id="eye-new_password"></i>
                                    </button>
                                </div>
                                <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password</small>
                                @error('new_password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="new_password_confirmation" class="col-md-4 col-form-label text-md-right">Konfirmasi Password Baru</label>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input id="new_password_confirmation" type="password" class="form-control" name="new_password_confirmation">
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('new_password_confirmation')">
                                        <i class="fa fa-eye" id="eye-new_password_confirmation"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="button" class="btn btn-primary" onclick="showVerificationModal()">
                                    Update Role
                                </button>
                                <a href="{{ route('admin.manage') }}" class="btn btn-secondary">
                                    Kembali
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Verifikasi Password -->
<div class="modal fade" id="verificationModal" tabindex="-1" aria-labelledby="verificationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verificationModalLabel">Verifikasi Perubahan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="verification_password" class="form-label">Masukkan Password Anda untuk Verifikasi</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="verification_password" name="verification_password" required>
                        <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('verification_password')">
                            <i class="fa fa-eye" id="eye-verification_password"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="submitForm()">Konfirmasi</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function togglePassword(inputId) {
    const passwordInput = document.getElementById(inputId);
    const eyeIcon = document.getElementById('eye-' + inputId);
    
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

function showVerificationModal() {
    const modal = document.getElementById('verificationModal');
    new bootstrap.Modal(modal).show();
}

function submitForm() {
    const form = document.getElementById('editForm');
    const verificationPassword = document.getElementById('verification_password');
    
    // Tambahkan input verification_password ke form
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'verification_password';
    input.value = verificationPassword.value;
    form.appendChild(input);
    
    form.submit();
}
</script>
@endpush
@endsection 