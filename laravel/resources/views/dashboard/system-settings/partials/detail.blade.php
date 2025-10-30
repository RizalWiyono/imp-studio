<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasDetailSystemSetting" aria-labelledby="offcanvasDetailSystemSettingLabel"
    data-bs-scroll="true" style="max-height: 100vh;">
    <div class="offcanvas-header">
        <h5 id="offcanvasDetailSystemSettingLabel" class="offcanvas-title">System Setting Detail</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body" style="overflow-y: auto;">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Setting Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">ID</label>
                            <h5><b id="detail_setting_id"></b></h5>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Key</label>
                            <h5><b id="detail_setting_key"></b></h5>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Value</label>
                            <div class="p-3 bg-light rounded">
                                <pre style="white-space: pre-wrap; word-wrap: break-word;" id="detail_setting_value"></pre>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Type</label>
                            <h5><span id="detail_setting_type" class="badge"></span></h5>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <h5><span id="detail_setting_status" class="badge"></span></h5>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Description</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="p-3 bg-light rounded">
                                <p id="detail_setting_description" style="white-space: pre-wrap; word-wrap: break-word;"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Audit Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Created By</label>
                                    <h5><b id="detail_setting_created_by"></b></h5>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Created At</label>
                                    <h5><b id="detail_setting_created_at"></b></h5>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Updated By</label>
                                    <h5><b id="detail_setting_updated_by"></b></h5>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Updated At</label>
                                    <h5><b id="detail_setting_updated_at"></b></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>