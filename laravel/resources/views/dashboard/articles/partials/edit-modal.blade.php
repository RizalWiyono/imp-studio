<!-- Edit Article Modal -->
<div class="modal fade" id="editArticleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-simple">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                <div class="text-center mb-4">
                    <h3>Edit Artikel</h3>
                    <p class="text-muted">Perbarui informasi artikel di bawah ini.</p>
                </div>

                <form id="editArticleForm" enctype="multipart/form-data" class="row g-3">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editArticleId" name="id" />
                    <input type="hidden" id="editHiddenContent" name="content" />

                    <!-- Judul -->
                    <div class="col-12">
                        <label class="form-label">Judul <span class="text-danger">*</span></label>
                        <input type="text" id="editArticleTitle" name="title" class="form-control" required />
                    </div>

                    <!-- Excerpt -->
                    <div class="col-12">
                        <label class="form-label">Excerpt</label>
                        <textarea id="editArticleExcerpt" name="excerpt" class="form-control" rows="2"></textarea>
                    </div>

                    <!-- Konten -->
                    <div class="col-12">
                        <label class="form-label">Konten</label>
                        <div id="editEditorContainer"></div>
                    </div>

                    <!-- Thumbnail -->
                    <div class="col-12" style="margin-top: 80px;">
                        <label class="form-label">Thumbnail</label>
                        <input type="file" name="thumbnail" class="form-control" accept="image/*" />
                        <small class="text-muted">Kosongkan jika tidak ingin mengganti thumbnail.</small>
                    </div>

                    <!-- META TITLE -->
                    <div class="col-md-6">
                        <label class="form-label">Meta Title</label>
                        <input type="text" id="editMetaTitle" name="meta_title" class="form-control" maxlength="255"
                            placeholder="Judul SEO (opsional)" />
                    </div>

                    <!-- META DESCRIPTION -->
                    <div class="col-md-6">
                        <label class="form-label">Meta Description</label>
                        <textarea id="editMetaDescription" name="meta_description" class="form-control" rows="2" maxlength="500"
                            placeholder="Deskripsi singkat untuk SEO (opsional)"></textarea>
                    </div>

                    <!-- META KEYWORDS -->
                    <div class="col-12">
                        <label class="form-label">Meta Keywords</label>
                        <input type="text" id="editMetaKeywords" name="meta_keywords" class="form-control"
                            placeholder="Pisahkan dengan koma, contoh: kesehatan, dokter, AI" maxlength="500" />
                    </div>

                    <!-- Status -->
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select id="editArticleStatus" name="status" class="form-select" required>
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>

                    <!-- Tombol -->
                    <div class="col-12 text-center mt-4">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
