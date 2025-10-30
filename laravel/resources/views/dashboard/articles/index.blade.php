@extends('layouts.app')

@section('title', 'Manajemen Artikel')

@push('styles')
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <style>
        .ql-editor {
            min-height: 300px;
        }
    </style>
@endpush


@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 pb-0 mb-2">Daftar Artikel</h4>
        <p class="mb-4">
            Halaman ini digunakan untuk mengelola daftar artikel. Kamu dapat menambah, mengedit, atau menghapus artikel.
        </p>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row g-4">
            <!-- Kartu Tambah Artikel -->
            <div class="col-xl-4 col-lg-6 col-md-6">
                <div class="card h-100">
                    <div class="row h-100">
                        <div class="col-sm-5 d-flex align-items-center justify-content-center">
                            <img src="{{ asset('img/illustrations/sitting-girl-with-laptop-light.png') }}"
                                alt="Articles Image" width="120" />
                        </div>
                        <div class="col-sm-7 d-flex align-items-center justify-content-center">
                            <button data-bs-target="#addArticleModal" data-bs-toggle="modal"
                                class="btn btn-primary mb-3 text-nowrap">
                                Tambah Artikel
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Artikel -->
            <div class="col-12">
                <div class="card">
                    <div class="card-datatable table-responsive">
                        <table class="table border-top" id="articles-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Judul</th>
                                    <th>Penulis</th>
                                    <th>Status</th>
                                    <th>Thumbnail</th>
                                    <th>Tanggal Publikasi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('dashboard.articles.partials.add-modal')
    @include('dashboard.articles.partials.edit-modal')
@endsection

@push('scripts')
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>

    <script>
        $(function() {
            // Inisialisasi DataTable
            const table = $('#articles-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('articles.index') }}',
                columns: [{
                        data: null,
                        render: (data, type, row, meta) => meta.row + meta.settings._iDisplayStart + 1,
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'author',
                        name: 'author',
                        defaultContent: '-'
                    },
                    {
                        data: 'status_badge',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'thumbnail',
                        name: 'thumbnail',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'published_at',
                        name: 'published_at',
                        defaultContent: '-'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            // =========================
            // CREATE ARTICLE
            // =========================
            $('#addArticleForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                $.ajax({
                    url: '{{ route('articles.store') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: (response) => {
                        $('#addArticleModal').modal('hide');
                        this.reset();
                        table.ajax.reload();
                        Swal.fire('Berhasil', response.message, 'success');
                    },
                    error: (xhr) => {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Terjadi kesalahan',
                            'error');
                    }
                });
            });

            // =========================
            // EDIT ARTICLE
            // =========================
            $(document).on('click', '.btn-edit', function() {
                const id = $(this).data('id');

                $.get(`{{ route('articles.edit', ':id') }}`.replace(':id', id), (data) => {
                    $('#editArticleId').val(data.id);
                    $('#editArticleTitle').val(data.title);
                    $('#editArticleExcerpt').val(data.excerpt);
                    $('#editArticleContent').val(data.content);
                    $('#editArticleStatus').val(data.status);
                    $('#editArticleModal').modal('show');
                });
            });

            // =========================
            // UPDATE ARTICLE
            // =========================
            $('#editArticleForm').on('submit', function(e) {
                e.preventDefault();
                const id = $('#editArticleId').val();
                const formData = new FormData(this);
                formData.append('_method', 'PUT');

                $.ajax({
                    url: `{{ route('articles.update', ':id') }}`.replace(':id', id),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: (response) => {
                        $('#editArticleModal').modal('hide');
                        this.reset();
                        table.ajax.reload();
                        Swal.fire('Berhasil', response.message, 'success');
                    },
                    error: (xhr) => {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Terjadi kesalahan',
                            'error');
                    }
                });
            });

            // =========================
            // DELETE ARTICLE
            // =========================
            $(document).on('click', '.btn-delete', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Yakin hapus artikel ini?',
                    text: 'Tindakan ini tidak bisa dibatalkan!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ route('articles.destroy', ':id') }}`.replace(':id',
                                id),
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: (response) => {
                                Swal.fire('Terhapus!', response.message, 'success');
                                table.ajax.reload();
                            },
                            error: (xhr) => {
                                Swal.fire('Error', xhr.responseJSON?.message ||
                                    'Terjadi kesalahan', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const addEditor = new Quill('#addEditorContainer', {
                theme: 'snow',
                placeholder: 'Tulis konten artikel di sini...',
                modules: {
                    toolbar: [
                        [{
                            header: [1, 2, 3, false]
                        }],
                        ['bold', 'italic', 'underline'],
                        [{
                            list: 'ordered'
                        }, {
                            list: 'bullet'
                        }],
                        ['link', 'image'],
                        ['clean']
                    ]
                }
            });

            const addForm = document.getElementById('addArticleForm');
            addForm.addEventListener('submit', function(e) {
                const content = document.querySelector('input[name="content"]');
                content.value = addEditor.root.innerHTML;
            });

            const editEditor = new Quill('#editEditorContainer', {
                theme: 'snow',
                placeholder: 'Edit konten artikel...',
                modules: {
                    toolbar: [
                        [{
                            header: [1, 2, 3, false]
                        }],
                        ['bold', 'italic', 'underline'],
                        [{
                            list: 'ordered'
                        }, {
                            list: 'bullet'
                        }],
                        ['link', 'image'],
                        ['clean']
                    ]
                }
            });

            $(document).on('click', '.btn-edit', function() {
                const id = $(this).data('id');
                $.get(`{{ route('articles.edit', ':id') }}`.replace(':id', id), (data) => {
                    $('#editArticleId').val(data.id);
                    $('#editArticleTitle').val(data.title);
                    $('#editArticleExcerpt').val(data.excerpt);
                    $('#editArticleStatus').val(data.status);
                    editEditor.root.innerHTML = data.content || '';
                    $('#editArticleModal').modal('show');
                });
            });

            const editForm = document.getElementById('editArticleForm');
            editForm.addEventListener('submit', function(e) {
                const content = document.querySelector('#editHiddenContent');
                content.value = editEditor.root.innerHTML;
            });
        });
    </script>
@endpush
