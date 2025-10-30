<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasDetailLog" aria-labelledby="offcanvasDetailLogLabel"
    data-bs-scroll="true" style="max-height: 100vh;">
    <div class="offcanvas-header">
        <h5 id="offcanvasDetailLogLabel" class="offcanvas-title">Activity Log Detail</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body" style="overflow-y: auto;">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Log Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">ID</label>
                            <h5><b id="detail_log_id"></b></h5>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">User</label>
                            <h5><b id="detail_log_user"></b></h5>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Action</label>
                            <h5><b id="detail_log_action"></b></h5>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <p id="detail_log_description"></p>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Target Table</label>
                                    <p id="detail_log_target_table"></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Target ID</label>
                                    <p id="detail_log_target_id"></p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">IP Address</label>
                                    <p id="detail_log_ip_address"></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Date & Time</label>
                                    <p id="detail_log_created_at"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Target Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <div id="detail_target_status"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Actions</label>
                                <div id="detail_target_link"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Metadata</h5>
                    </div>
                    <div class="card-body">
                        <div id="detail_log_metadata" class="p-2"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>