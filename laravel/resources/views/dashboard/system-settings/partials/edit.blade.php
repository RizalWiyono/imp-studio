{{-- dashboard.system-settings.partials.edit.php --}}
<div class="offcanvas offcanvas-end" 
     tabindex="-1" 
     id="offcanvasEditSystemSetting" 
     aria-labelledby="offcanvasEditSystemSettingLabel"
     data-bs-scroll="true"
     style="max-height: 100vh;">
  <div class="offcanvas-header">
    <h5 id="offcanvasEditSystemSettingLabel" class="offcanvas-title">Edit System Setting</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body" style="overflow-y: auto;">
    <form id="editSystemSettingForm" onsubmit="return false">
      @csrf
      @method('PUT')
      <input type="hidden" name="system_setting_id" id="system_setting_id" />

      <div class="mb-3">
        <label for="edit_key" class="form-label">Key</label>
        <input type="text" name="key" id="edit_key" class="form-control" required />
      </div>
      
      <div class="mb-3">
        <label for="edit_type" class="form-label">Type</label>
        <select name="type" id="edit_type" class="form-select select2" required>
          <option value="">-- Select Type --</option>
          <option value="STRING">String</option>
          <option value="INTEGER">Integer</option>
          <option value="BOOLEAN">Boolean</option>
          <option value="JSON">JSON</option>
          <option value="URL">URL</option>
        </select>
      </div>

      <div class="mb-3">
        <label for="edit_value" class="form-label">Value</label>
        
        <!-- For STRING type -->
        <div id="edit-value-string-container" style="display: none;">
          <textarea name="value" id="edit-value-string" class="form-control" rows="3"></textarea>
        </div>
        
        <!-- For INTEGER type -->
        <div id="edit-value-integer-container" style="display: none;">
          <input type="number" name="value" id="edit-value-integer" class="form-control" />
        </div>
        
        <!-- For BOOLEAN type -->
        <div id="edit-value-boolean-container" style="display: none;">
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="edit_value" id="edit-value-boolean-true" value="1">
              <label class="form-check-label" for="edit-value-boolean-true">True</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="edit_value" id="edit-value-boolean-false" value="0">
              <label class="form-check-label" for="edit-value-boolean-false">False</label>
            </div>
          </div>
        
        <!-- For JSON type -->
        <div id="edit-value-json-container" style="display: none;">
          <textarea name="value" id="edit-value-json" class="form-control" rows="5"></textarea>
          <small class="text-muted">Enter valid JSON format</small>
          <button id="format-json-btn" type="button" class="btn btn-sm btn-outline-secondary mt-2">Format JSON</button>
        </div>
        
        <!-- For URL type -->
        <div id="edit-value-url-container" style="display: none;">
          <input type="url" name="value" id="edit-value-url" class="form-control" placeholder="https://example.com" />
        </div>
      </div>

      <div class="mb-3">
        <label for="edit_description" class="form-label">Description</label>
        <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
      </div>

      <div class="mb-3">
        <label for="edit_status" class="form-label">Status</label>
        <select name="status" id="edit_status" class="form-select select2" required>
          <option value="">-- Select Status --</option>
          <option value="ACTIVE">Active</option>
          <option value="INACTIVE">Inactive</option>
          <option value="ARCHIVED">Archived</option>
          <option value="DELETED">Deleted</option>
        </select>
      </div>

      <button type="submit" class="btn btn-primary">Save</button>
    </form>
  </div>
</div>

@push('scripts')
<script>
  $(document).ready(function(){
    // Format JSON button event handler
    $(document).on('click', '#format-json-btn', function(e) {
      e.preventDefault();
      const jsonValue = $('#edit-value-json').val();
      $('#edit-value-json').val(formatJSONValue(jsonValue));
    });

    // Format JSON on blur
    $('#edit-value-json').on('blur', function() {
      const jsonValue = $(this).val();
      if (jsonValue && jsonValue.trim() !== '' && isValidJSON(jsonValue)) {
        $(this).val(formatJSONValue(jsonValue));
      }
    });

    // Handling the dynamic value input based on type selection for edit form
    $('#edit_type').on('change', function() {
      // Hide all value containers first
      $('#edit-value-string-container, #edit-value-integer-container, #edit-value-boolean-container, #edit-value-json-container, #edit-value-url-container').hide();
      
      // Get current value
      let currentValue = '';
      switch($(this).val()) {
        case 'STRING':
          currentValue = $('#edit-value-string').val();
          break;
        case 'INTEGER':
          currentValue = $('#edit-value-integer').val();
          break;
        case 'BOOLEAN':
          currentValue = $('input[name="edit_value"]:checked').val();
          break;
        case 'JSON':
          currentValue = $('#edit-value-json').val();
          break;
        case 'URL':
          currentValue = $('#edit-value-url').val();
          break;
      }
      
      // Clear all value inputs
      $('#edit-value-string, #edit-value-integer, #edit-value-json, #edit-value-url').val('');
      $('#edit-value-boolean-true, #edit-value-boolean-false').prop('checked', false);
      
      // Show the relevant container based on selected type
      var selectedType = $(this).val();
      
      switch(selectedType) {
        case 'STRING':
          $('#edit-value-string-container').show();
          $('#edit-value-string').val(currentValue);
          break;
        case 'INTEGER':
          $('#edit-value-integer-container').show();
          $('#edit-value-integer').val(currentValue);
          break;
        case 'BOOLEAN':
          $('#edit-value-boolean-container').show();
          if (currentValue === '1') {
            $('#edit-value-boolean-true').prop('checked', true);
          } else if (currentValue === '0') {
            $('#edit-value-boolean-false').prop('checked', true);
          }
          break;
        case 'JSON':
          $('#edit-value-json-container').show();
          $('#edit-value-json').val(formatJSONValue(currentValue));
          break;
        case 'URL':
          $('#edit-value-url-container').show();
          $('#edit-value-url').val(currentValue);
          break;
      }
    });

    // Form edit submission logic
    $('#editSystemSettingForm').submit(function() {
      var selectedType = $('#edit_type').val();
      var value;
      
      // Get the value from the appropriate input based on type
      switch(selectedType) {
        case 'STRING':
          value = $('#edit-value-string').val();
          break;
        case 'INTEGER':
          value = $('#edit-value-integer').val();
          break;
        case 'BOOLEAN':
          value = $('input[name="edit_value"]:checked').val(); 
          break;
        case 'JSON':
          // Try to parse and re-stringify to ensure valid JSON
          try {
            const jsonObj = JSON.parse($('#edit-value-json').val());
            value = JSON.stringify(jsonObj); // This ensures it's valid JSON in compact form for storage
          } catch (e) {
            // If it's not valid JSON, use the raw value and show an error
            value = $('#edit-value-json').val();
            alert('Warning: The JSON format is invalid. Please check your input.');
            return false; // Prevent submission if invalid
          }
          break;
        case 'URL':
          value = $('#edit-value-url').val();
          break;
      }
      
      // Set a hidden input for the form submission
      if ($('#edit-hidden-value').length === 0) {
        $('<input>').attr({
          type: 'hidden',
          id: 'edit-hidden-value',
          name: 'value'
        }).appendTo('#editSystemSettingForm');
      }
      
      $('#edit-hidden-value').val(value);
    });

    // Modify the edit button handler to properly format JSON values when loading
    $(document).on('click', '.btn-edit', function() {
      let settingId = $(this).data('id');
      let key = $(this).data('key');
      let value = $(this).data('value');
      let type = $(this).data('type');
      let description = $(this).data('description') || '';
      let status = $(this).data('status');
      
      $('#system_setting_id').val(settingId);
      $('#edit_key').val(key);
      $('#edit_type').val(type).trigger('change');
      $('#edit_description').val(description);
      $('#edit_status').val(status).trigger('change');
      
      // Set nilai berdasarkan type setelah container sesuai ditampilkan
      setTimeout(function() {
        switch(type) {
          case 'STRING':
            $('#edit-value-string').val(value);
            break;
          case 'INTEGER':
            $('#edit-value-integer').val(value);
            break;
          case 'BOOLEAN':
          const numValue = Number(value);
          
          if (numValue === 1) {
            $('#edit-value-boolean-true').prop('checked', true);
          } else {
            $('#edit-value-boolean-false').prop('checked', true);
          }
            break;
          case 'JSON':
            // Format JSON before displaying in textarea
            $('#edit-value-json').val(formatJSONValue(value));
            break;
          case 'URL':
            $('#edit-value-url').val(value);
            break;
        }
      }, 100);
      
      let offcanvasEl = document.getElementById('offcanvasEditSystemSetting');
      let offcanvasObj = bootstrap.Offcanvas.getInstance(offcanvasEl);
      if (!offcanvasObj) {
        offcanvasObj = new bootstrap.Offcanvas(offcanvasEl);
      }
      offcanvasObj.show();
    });
  })
</script>
@endpush