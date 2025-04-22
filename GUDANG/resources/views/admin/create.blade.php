@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Tambah Admin</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.store') }}" id="createForm">
                        @csrf

                        <div class="form-group row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-right">Nama</label>
                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" required>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-right">Email</label>
                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" required>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-right">Password</label>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password')">
                                        <i class="fa fa-eye" id="eye-password"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">Konfirmasi Password</label>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password-confirm')">
                                        <i class="fa fa-eye" id="eye-password-confirm"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="button" class="btn btn-primary" onclick="showVerificationModal()">
                                    Simpan
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
                <h5 class="modal-title" id="verificationModalLabel">Verifikasi Penambahan Admin</h5>
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
    const form = document.getElementById('createForm');
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