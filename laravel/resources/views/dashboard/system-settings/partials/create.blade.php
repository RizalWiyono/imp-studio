<div class="offcanvas offcanvas-end"
     tabindex="-1"
     id="offcanvasAddSystemSetting"
     aria-labelledby="offcanvasAddSystemSettingLabel"
     data-bs-scroll="true"
     style="max-height: 100vh;">
  <div class="offcanvas-header">
    <h5 id="offcanvasAddSystemSettingLabel" class="offcanvas-title">Add System Setting</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body" style="overflow-y: auto;">
    <form id="addNewSystemSettingForm" onsubmit="return false">
      @csrf
      <div class="mb-3">
        <label for="key" class="form-label">Key</label>
        <input type="text" name="key" id="key" class="form-control" required />
      </div>

      <div class="mb-3">
        <label for="type" class="form-label">Type</label>
        <select name="type" id="type" class="form-select select2" required>
          <option value="">-- Select Type --</option>
          <option value="STRING">String</option>
          <option value="INTEGER">Integer</option>
          <option value="BOOLEAN">Boolean</option>
          <option value="JSON">JSON</option>
          <option value="URL">URL</option>
        </select>
      </div>

      <div class="mb-3">
        <label for="value" class="form-label">Value</label>
        
        <!-- For STRING type -->
        <div id="value-string-container" style="display: none;">
          <textarea name="value" id="value-string" class="form-control" rows="3"></textarea>
        </div>
        
        <!-- For INTEGER type -->
        <div id="value-integer-container" style="display: none;">
          <input type="number" name="value" id="value-integer" class="form-control" />
        </div>
        
        <!-- For BOOLEAN type -->
        <div id="value-boolean-container" style="display: none;">
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="value" id="value-boolean-true" value="1">
            <label class="form-check-label" for="value-boolean-true">True</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="value" id="value-boolean-false" value="0">
            <label class="form-check-label" for="value-boolean-false">False</label>
          </div>
        </div>
        
        <!-- For JSON type -->
        <div id="value-json-container" style="display: none;">
          <textarea name="value" id="value-json" class="form-control" rows="5"></textarea>
          <small class="text-muted">Enter valid JSON format</small>
        </div>
        
        <!-- For URL type -->
        <div id="value-url-container" style="display: none;">
          <input type="url" name="value" id="value-url" class="form-control" placeholder="https://example.com" />
        </div>
      </div>

      <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea name="description" id="description" class="form-control" rows="3"></textarea>
      </div>

      <div class="mb-3">
        <label for="status" class="form-label">Status</label>
        <select name="status" id="status" class="form-select select2" required>
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
  $(document).ready(function() {
    // Handling the dynamic value input based on type selection
    $('#type').on('change', function() {
      // Hide all value containers first
      $('#value-string-container, #value-integer-container, #value-boolean-container, #value-json-container, #value-url-container').hide();
      
      // Clear all value inputs
      $('#value-string, #value-integer, #value-json, #value-url').val('');
      $('#value-boolean-true, #value-boolean-false').prop('checked', false);
      
      // Show the relevant container based on selected type
      var selectedType = $(this).val();
      
      switch(selectedType) {
        case 'STRING':
          $('#value-string-container').show();
          break;
        case 'INTEGER':
          $('#value-integer-container').show();
          break;
        case 'BOOLEAN':
          $('#value-boolean-container').show();
          break;
        case 'JSON':
          $('#value-json-container').show();
          break;
        case 'URL':
          $('#value-url-container').show();
          break;
      }
    });
    
    // Form submission logic
    $('#addNewSystemSettingForm').submit(function() {
      var selectedType = $('#type').val();
      var value;
      
      // Get the value from the appropriate input based on type
      switch(selectedType) {
        case 'STRING':
          value = $('#value-string').val();
          break;
        case 'INTEGER':
          value = $('#value-integer').val();
          break;
        case 'BOOLEAN':
          value = $('input[name="value"]:checked').val();
          break;
        case 'JSON':
          value = $('#value-json').val();
          break;
        case 'URL':
          value = $('#value-url').val();
          break;
      }
      
      // Set a hidden input for the form submission
      if ($('#hidden-value').length === 0) {
        $('<input>').attr({
          type: 'hidden',
          id: 'hidden-value',
          name: 'value'
        }).appendTo('#addNewSystemSettingForm');
      }
      
      $('#hidden-value').val(value);
    });
    
    // JSON validation for the JSON input
    $('#value-json').on('blur', function() {
      try {
        if ($(this).val()) {
          JSON.parse($(this).val());
          $(this).removeClass('is-invalid').addClass('is-valid');
        }
      } catch (e) {
        $(this).removeClass('is-valid').addClass('is-invalid');
        // You can add an error message here
      }
    });
  });
</script>
@endpush