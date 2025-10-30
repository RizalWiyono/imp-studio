<div class="col-xl-4 col-lg-6 col-md-6">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-2">
                <h6 class="fw-normal">Total <b class="setting-active-count">{{ $totalSettingActive }}</b> Settings</h6>
            </div>
            <div class="d-flex justify-content-between align-items-end">
                <div class="role-heading">
                    <h4 class="mb-1">Active</h4>
                    <small>
                        This system has <b class="setting-active-count">{{ $totalSettingActive }}</b> active settings
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-xl-4 col-lg-6 col-md-6">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-2">
                <h6 class="fw-normal">Total <b class="setting-inactive-count">{{ $totalSettingInactive }}</b> Settings</h6>
            </div>
            <div class="d-flex justify-content-between align-items-end">
                <div class="role-heading">
                    <h4 class="mb-1">Inactive</h4>
                    <small>
                        This system has <b class="setting-inactive-count">{{ $totalSettingInactive }}</b> inactive settings
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-xl-4 col-lg-6 col-md-6">
    <div class="card h-100">
        <div class="row h-100">
            <div class="col-sm-5">
                <div class="d-flex align-items-end h-100 justify-content-center mt-sm-0 mt-3">
                    <img src="{{ asset('img/illustrations/sitting-girl-with-laptop-light.png') }}" class="img-fluid"
                        alt="Image" width="120" />
                </div>
            </div>
            <div class="col-sm-7">
                <div class="card-body text-sm-end text-center ps-sm-0">
                    <button data-bs-target="#offcanvasAddSystemSetting" data-bs-toggle="offcanvas"
                        class="btn btn-primary mb-3 text-nowrap add-new">
                        Add New Setting
                    </button>
                    <p class="mb-0">Add setting, if it does not exist</p>
                </div>
            </div>
        </div>
    </div>
</div>