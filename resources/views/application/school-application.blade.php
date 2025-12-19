<x-layout>
    <x-slot:title>
        School Application Page
    </x-slot:title>

    <div class="container mt-5">
        <div class="row">
            <div class="col-12 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-8 offset-lg-2 col-xl-8 offset-xl-2">
                <div class="card card-primary">
                    <div class="card-header">
                        <h4>Register a School</h4> <span class="text-muted" style="font-size: .85em;"> (If you have already registered your school, DO NOT register it again. Call us on +255(0) 760 400 200.)</span>
                    </div>
                    <div class="card-body">

                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>There were some errors with your submission:</strong>
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form method="POST" action="{{ route('register.school') }}" id="schoolRegistrationForm">
                            @csrf
                            <div class="form-group">
                                <label>Registration Type</label><br>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" id="singleSchool" name="registration_type" value="single" checked>
                                    <label class="form-check-label" for="singleSchool">Single School</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" id="groupOfSchools" name="registration_type" value="group">
                                    <label class="form-check-label" for="groupOfSchools" style="margin-right:-12px">Group of Schools</label><span class="text-muted">(Under one Ownership)</span>
                                </div>
                            </div>

                            <div class="form-group" id="genericNameField" style="display: none;">
                                <label for="generic_name">Generic Name for Schools</label>
                                <input type="text" class="form-control" id="generic_name" name="generic_name" placeholder="e.g. Clementina Schools" value="{{ old('generic_name') }}">
                            </div>

                            <div id="schoolFieldsContainer">
                                <div class="school-fields">
                                    <h6 class="mb-4 school-details-heading" style="border-bottom: 2px solid #007bff; padding-bottom: 10px;">
                                        School Details
                                    </h6>
                                    <div class="form-group">
                                        <label for="school_name_0">School Name</label>
                                        <input type="text" class="form-control" id="school_name_0" name="schools[0][school_name]" value="{{ old('schools.0.school_name') }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="address_0">Postal Address (Optional)</label>
                                        <textarea id="address_0" name="schools[0][address]" class="form-control">{{ old('schools.0.address') }}</textarea>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-4">
                                            <label for="region_0">Region</label>
                                            <select class="form-control" id="region_0" name="schools[0][region]">
                                                <option value="">Select Region</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-4">
                                            <label for="district_0">District</label>
                                            <select class="form-control" id="district_0" name="schools[0][district]">
                                                <option value="">Select District</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-4">
                                            <label for="ward_0">Ward</label>
                                            <select class="form-control" id="ward_0" name="schools[0][ward]">
                                                <option value="">Select Ward</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-lg-6 col-12">
                                            <div class="school-level-container">
                                                <label>School Level</label><br>
                                                <div class="form-check form-check-inline">
                                                    <input type="checkbox" class="form-check-input" id="primary_0" name="schools[0][school_type][]" value="Primary" {{ is_array(old('schools.0.school_type')) && in_array('Primary', old('schools.0.school_type')) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="primary_0">Primary</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input type="checkbox" class="form-check-input" id="O-level_0" name="schools[0][school_type][]" value="O-Level" {{ is_array(old('schools.0.school_type')) && in_array('O-Level', old('schools.0.school_type')) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="O-level_0">O-level</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input type="checkbox" class="form-check-input" id="A-level_0" name="schools[0][school_type][]" value="A-Level" {{ is_array(old('schools.0.school_type')) && in_array('A-Level', old('schools.0.school_type')) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="A-level_0">A-level</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-6 col-12 sponsorship-type-container" id="sponsorshipContainer_0">
                                            <label>Sponsorship Type</label><br>
                                            <div class="form-check form-check-inline">
                                                <input type="radio" class="form-check-input" id="government_0" name="schools[0][sponsorship_type]" value="Government" {{ old('schools.0.sponsorship_type')=='Government' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="government_0">Government</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input type="radio" class="form-check-input" id="private_0" name="schools[0][sponsorship_type]" value="Private" {{ old('schools.0.sponsorship_type')=='Private' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="private_0">Private</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="headteacher-fields">
                                        <h6 class="mb-4" style="border-bottom: 2px solid #007bff; padding-bottom: 10px;">
                                            Headteacher/Head of School Details
                                        </h6>
                                        <div class="row">
                                            <div class="form-group col-lg-4 col-12">
                                                <label for="first_name_0">First Name</label>
                                                <input type="text" class="form-control" id="first_name_0" name="schools[0][first_name]" value="{{ old('schools.0.first_name') }}">
                                            </div>
                                            <div class="form-group col-lg-4 col-12">
                                                <label for="middle_name_0">Middle Name (Optional)</label>
                                                <input type="text" class="form-control" id="middle_name_0" name="schools[0][middle_name]" value="{{ old('schools.0.middle_name') }}">
                                            </div>
                                            <div class="form-group col-lg-4 col-12">
                                                <label for="surname_0">Surname</label>
                                                <input type="text" class="form-control" id="surname_0" name="schools[0][surname]" value="{{ old('schools.0.surname') }}">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-12">
                                                <small class="pt-2 form-text text-info">
                                                    <i class="fas fa-exclamation-circle text-info"></i>&nbsp;The phone number will be used to send your username and password.</small>
                                            </div>
                                            <div class="form-group col-lg-6 col-12" style="margin-top:-20px">
                                                <label for="phone_0">Primary Phone Number</label>
                                                <input type="text" class="form-control" id="phone_0" placeholder="07XXXXXXXXXX" name="schools[0][phone]" value="{{ old('schools.0.phone') }}">
                                            </div>
                                            <div class="form-group col-lg-6 col-12" style="margin-top:-20px">
                                                <label for="email_0">Email (Optional)</label>
                                                <input type="text" class="form-control" id="email_0" name="schools[0][email]" value="{{ old('schools.0.email') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group d-flex mt-4">
                                <button type="button" class="btn btn-primary" id="addSchoolBtn" style="display: none;">
                                    <i class="fas fa-plus"></i> Add School
                                </button> &nbsp; &nbsp;
                                <button type="submit" class="btn btn-primary" id="submitButton"> &nbsp; &nbsp; Submit &nbsp; &nbsp;</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const registrationTypeRadios = document.querySelectorAll('input[name="registration_type"]');
    const genericNameField = document.getElementById('genericNameField');
    const addSchoolBtn = document.getElementById('addSchoolBtn');
    const submitButton = document.getElementById('submitButton');
    const schoolFieldsContainer = document.getElementById('schoolFieldsContainer');
    const firstSchoolBlock = schoolFieldsContainer.querySelector('.school-fields'); 

    const originalSchoolBlockHtml = firstSchoolBlock.outerHTML; 

    let schoolIndex = 0;

    function manageSubmitButtonVisibility() {
        const isGroup = document.getElementById('groupOfSchools').checked;
        const schoolBlocksCount = schoolFieldsContainer.querySelectorAll('.school-fields').length;

        if (isGroup && schoolBlocksCount === 1) {
            submitButton.style.display = 'none';
        } else {
            submitButton.style.display = 'inline-block';
        }
    }

    function createRemoveButton() {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'btn btn-danger btn-sm float-right remove-school-btn';
        button.innerHTML = '<i class="fas fa-trash"></i> Remove School';
        button.onclick = function() {
            this.closest('.school-fields').remove();
            reIndexSchoolBlocks();
            manageSubmitButtonVisibility();
        };
        return button;
    }

    function reIndexSchoolBlocks() {
        const allBlocks = schoolFieldsContainer.querySelectorAll('.school-fields');
        schoolIndex = 0;
        allBlocks.forEach((block, index) => {
            if (index > 0) {
                const heading = block.querySelector('.school-details-heading');
                if (heading) {
                    heading.textContent = `School Details #${index + 1}`;
                }
                let removeBtn = block.querySelector('.remove-school-btn');
                if (!removeBtn) {
                     removeBtn = createRemoveButton();
                     const btnContainer = document.createElement('div');
                     btnContainer.className = 'd-flex justify-content-end mb-3';
                     btnContainer.appendChild(removeBtn);
                     block.querySelector('.school-details-heading').insertAdjacentElement('afterend', btnContainer);
                }
            } else {
                const heading = block.querySelector('.school-details-heading');
                if (heading) {
                    heading.textContent = 'School Details';
                }
                const removeBtnContainer = block.querySelector('.remove-school-btn') ? block.querySelector('.remove-school-btn').closest('div') : null;
                if (removeBtnContainer) removeBtnContainer.remove();
            }
            
            updateFieldAttributes(block, index, false); 
            schoolIndex = index;
        });
        schoolIndex = allBlocks.length > 0 ? allBlocks.length - 1 : 0; 
    }

    function manageSchoolLevel(inputElement) {
        const schoolName = inputElement.value.toLowerCase();
        const parentBlock = inputElement.closest('.school-fields');
        if (!parentBlock) return;

        const schoolLevelContainer = parentBlock.querySelector('.school-level-container');
        const sponsorshipContainer = parentBlock.querySelector('.sponsorship-type-container');
        const primaryCheckbox = parentBlock.querySelector('input[value="Primary"]');
        const otherCheckboxes = parentBlock.querySelectorAll('input[value="O-Level"], input[value="A-Level"]');
        const sponsorshipFormGroup = sponsorshipContainer ? sponsorshipContainer.closest('.form-group') : null;

        const isGroup = document.getElementById('groupOfSchools').checked;
        const isSecondary = schoolName.includes('secondary') || schoolName.includes('high') || schoolName.includes('sekondari');

        if (isSecondary) {
            schoolLevelContainer.style.display = 'block';
            if (primaryCheckbox) {
                primaryCheckbox.closest('.form-check').style.display = 'none';
                primaryCheckbox.checked = false;
            }
            
            otherCheckboxes.forEach(checkbox => {
                checkbox.closest('.form-check').style.display = 'inline-block';
            });

            if (sponsorshipFormGroup) {
                sponsorshipFormGroup.classList.remove('col-lg-12');
                sponsorshipFormGroup.classList.add('col-lg-6');
            }
        } else {

            schoolLevelContainer.style.display = 'none'; 
        
        // Now, you must also ensure Primary is checked and O/A-Levels are unchecked 
        // to handle the case where the user deletes 'Secondary' from the name.
        if (primaryCheckbox) {
            primaryCheckbox.closest('.form-check').style.display = 'inline-block';
            primaryCheckbox.checked = true; // Force Primary selection
        }
        
        otherCheckboxes.forEach(checkbox => {
            checkbox.closest('.form-check').style.display = 'none';
            checkbox.checked = false; // Force O/A-Level uncheck
        });
            
            if (primaryCheckbox) {
                primaryCheckbox.closest('.form-check').style.display = 'inline-block';

                if (!isGroup || schoolName.length === 0) {
                    primaryCheckbox.checked = true;
                }
            }
            
            otherCheckboxes.forEach(checkbox => {
                checkbox.closest('.form-check').style.display = isGroup ? 'inline-block' : 'none';

                if (!isGroup || schoolName.length === 0) {
                    checkbox.checked = false; 
                }
            });
            
            if (sponsorshipFormGroup) {
                sponsorshipFormGroup.classList.remove('col-lg-6');
                sponsorshipFormGroup.classList.add('col-lg-12');
            }
        }
    }

    function updateFieldAttributes(clonedElement, index, resetValue = true) {
        clonedElement.querySelectorAll('input, select, textarea, label').forEach(element => {
            const originalId = element.id;
            const originalName = element.name;
            const originalFor = element.htmlFor;

            if (originalId) {
                element.id = originalId.replace(/_\d+$/, `_${index}`);
            }
            if (originalFor) {
                element.htmlFor = originalFor.replace(/_\d+$/, `_${index}`);
            }

            if (originalName) {
                element.name = originalName.replace(/schools\[\d+\]/, `schools[${index}]`);
            }

            if (resetValue) { 
                if (element.tagName === 'INPUT' && (element.type === 'text' || element.type === 'email' || element.type === 'tel')) {
                    element.value = '';
                } else if (element.tagName === 'TEXTAREA') {
                    element.value = '';
                } else if (element.tagName === 'SELECT') {
                    element.selectedIndex = 0;
                } else if (element.type === 'radio' || element.type === 'checkbox') {
                    element.checked = false; 
                }
            }
        });

        const primaryCheckbox = clonedElement.querySelector(`input[id="primary_${index}"]`);
        const otherCheckboxes = clonedElement.querySelectorAll(`input[name="schools[${index}][school_type][]"]:not([value="Primary"])`);

        if (primaryCheckbox) {
            primaryCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    otherCheckboxes.forEach(checkbox => checkbox.checked = false);
                }
            });
        }

        otherCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (this.checked && primaryCheckbox) {
                    primaryCheckbox.checked = false;
                }
            });
        });

        const schoolNameInput = clonedElement.querySelector('input[name*="[school_name]"]');
        if (schoolNameInput) {
             schoolNameInput.removeEventListener('input', manageSchoolLevel);
             schoolNameInput.addEventListener('input', (event) => manageSchoolLevel(event.target));
        }

        const regionSelect = clonedElement.querySelector('select[name*="[region]"]');
        if (regionSelect) {
            regionSelect.onchange = () => fetchDistricts(regionSelect.id, `district_${index}`, `ward_${index}`);
        }
        const districtSelect = clonedElement.querySelector('select[name*="[district]"]');
        if (districtSelect) {
            districtSelect.onchange = () => fetchWards(districtSelect.id, `ward_${index}`);
        }
    }

    function toggleFormFields() {
        const isGroup = document.getElementById('groupOfSchools').checked;

        genericNameField.style.display = isGroup ? 'block' : 'none';
        addSchoolBtn.style.display = isGroup ? 'block' : 'none';

        const initialSchoolNameInput = document.getElementById('school_name_0');
        if (initialSchoolNameInput) {
            initialSchoolNameInput.placeholder = isGroup ? "e.g. Clementina Girls Primary School - Kibiti" : "";
        }
        if (!isGroup) {
            const clonedBlocks = schoolFieldsContainer.querySelectorAll('.school-fields:not(:first-child)');
            clonedBlocks.forEach(block => block.remove());
            schoolIndex = 0;
            reIndexSchoolBlocks();

        }

        const sponsorshipContainers = document.querySelectorAll('.sponsorship-type-container');
        sponsorshipContainers.forEach(container => {
            const privateRadio = container.querySelector('input[value="Private"]');
            if (isGroup) {
                container.style.display = 'none';
                if (privateRadio) privateRadio.checked = true;
            } else {
                container.style.display = 'block';
            }
        });

        const allSchoolBlocks = document.querySelectorAll('.school-fields');
        allSchoolBlocks.forEach(block => {
            const schoolNameInput = block.querySelector('input[name*="[school_name]"]');
            if (schoolNameInput) {
                manageSchoolLevel(schoolNameInput);
            }
        });

        manageSubmitButtonVisibility();
    }

    registrationTypeRadios.forEach(radio => {
        radio.addEventListener('change', toggleFormFields);
    });

    addSchoolBtn.addEventListener('click', function () {
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = originalSchoolBlockHtml;
        const clonedSchoolBlock = tempDiv.querySelector('.school-fields');
        schoolIndex++;

        const heading = clonedSchoolBlock.querySelector('.school-details-heading');
        if (heading) {
            heading.textContent = `School Details #${schoolIndex + 1}`;
        }

        const removeButtonContainer = document.createElement('div');
        removeButtonContainer.className = 'd-flex justify-content-end mb-3';
        const removeBtn = createRemoveButton();
        removeButtonContainer.appendChild(removeBtn);
        heading.insertAdjacentElement('afterend', removeButtonContainer);

        updateFieldAttributes(clonedSchoolBlock, schoolIndex, true); 

        const isGroup = document.getElementById('groupOfSchools').checked;
        const newSponsorshipRadio = clonedSchoolBlock.querySelector('input[value="Private"]');
        const newSchoolNameInput = clonedSchoolBlock.querySelector('input[name*="[school_name]"]');

        if (isGroup && newSponsorshipRadio) {
            newSponsorshipRadio.checked = true;
            clonedSchoolBlock.querySelector('.sponsorship-type-container').style.display = 'none';
        } 

        schoolFieldsContainer.appendChild(clonedSchoolBlock);
        
        if (newSchoolNameInput) {
            manageSchoolLevel(newSchoolNameInput);
        }
        
        fetchRegions(schoolIndex);
        manageSubmitButtonVisibility();
    });

    const initialSchoolNameInput = document.getElementById('school_name_0');
    if (initialSchoolNameInput) {
        initialSchoolNameInput.addEventListener('input', () => manageSchoolLevel(initialSchoolNameInput));
    }
    const initialRegionSelect = document.getElementById('region_0');
    const initialDistrictSelect = document.getElementById('district_0');
    if (initialRegionSelect && initialDistrictSelect) {
        initialRegionSelect.onchange = () => fetchDistricts('region_0', 'district_0', 'ward_0');
        initialDistrictSelect.onchange = () => fetchWards('district_0', 'ward_0');
    }

    toggleFormFields();
    manageSubmitButtonVisibility();
    
    document.querySelectorAll('.school-fields').forEach((block, index) => {
        fetchRegions(index);
    });

    function fetchRegions(index) {
        const regionSelectId = `region_${index}`;
        const regionSelect = document.getElementById(regionSelectId);
        if (!regionSelect) return;

        const regionsUrl = "{{ route('regions') }}"; 
        
        fetch(regionsUrl, { headers: { 'X-CSRF-TOKEN': csrfToken } })
            .then(response => {
                if (!response.ok) {
                     console.error(`Fetch failed for regions. Status: ${response.status}`);
                     return { regions: [] }; 
                }
                return response.json();
            })
            .then(data => {
                regionSelect.innerHTML = '<option value="">Select a Region</option>';
                if (data.regions && Array.isArray(data.regions)) {
                    data.regions.forEach(region => {
                        const option = document.createElement('option');
                        option.value = region;
                        option.textContent = region;
                        regionSelect.appendChild(option);
                    });
                }
            })
            .catch(error => console.error('Error fetching regions:', error));
    }

    function fetchDistricts(regionSelectId, districtSelectId, wardSelectId) {
        const region = document.getElementById(regionSelectId).value;
        const districtSelect = document.getElementById(districtSelectId);
        const wardSelect = document.getElementById(wardSelectId);
        if (!districtSelect || !wardSelect) return;

        if (!region) {
            districtSelect.innerHTML = '<option value="">Select a District</option>';
            wardSelect.innerHTML = '<option value="">Select a Ward</option>';
            return;
        }

        const url = `/get-districts?region=${encodeURIComponent(region)}`;
        fetch(url, { headers: { 'X-CSRF-TOKEN': csrfToken } })
            .then(response => response.json())
            .then(data => {
                districtSelect.innerHTML = '<option value="">Select a District</option>';
                if (data.districts && Array.isArray(data.districts)) {
                    data.districts.forEach(district => {
                        const option = document.createElement('option');
                        option.value = district;
                        option.textContent = district;
                        districtSelect.appendChild(option);
                    });
                }
                wardSelect.innerHTML = '<option value="">Select a Ward</option>';
            })
            .catch(error => console.error('Error fetching districts:', error));
    }

    function fetchWards(districtSelectId, wardSelectId) {
        const index = districtSelectId.split('_')[1];
        const region = document.getElementById(`region_${index}`).value; 
        const district = document.getElementById(districtSelectId).value;
        const wardSelect = document.getElementById(wardSelectId);
        if (!wardSelect) return;

        if (!district) {
            wardSelect.innerHTML = '<option value="">Select a Ward</option>';
            return;
        }

        const url = `/get-wards?region=${encodeURIComponent(region)}&district=${encodeURIComponent(district)}`;
        fetch(url, { headers: { 'X-CSRF-TOKEN': csrfToken } })
            .then(response => response.json())
            .then(data => {
                wardSelect.innerHTML = '<option value="">Select a Ward</option>';
                if (data.wards && Array.isArray(data.wards)) {
                    data.wards.forEach(ward => {
                        const option = document.createElement('option');
                        option.value = ward;
                        option.textContent = ward;
                        wardSelect.appendChild(option);
                    });
                }
            })
            .catch(error => console.error('Error fetching wards:', error));
    }
});
</script>

</x-layout>