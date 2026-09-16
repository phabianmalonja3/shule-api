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

<form id="registrationForm" action="{{ route('register.school') }}" method="POST">
    @csrf

    <!-- Ownership Type Selection -->
    <div class="form-group mb-4">
        <label class="form-label font-weight-bold">Registration Type</label>
        <div class="d-flex gap-3">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="registration_type" id="singleSchool" value="single" checked onchange="toggleFormFields()">
                <label class="form-check-label" for="singleSchool">Single School</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="registration_type" id="groupOfSchools" value="group" onchange="toggleFormFields()">
                <label class="form-check-label" for="groupOfSchools">Group of Schools / Educational Hub</label>
            </div>
        </div>
    </div>

    <!-- Container for School Blocks -->
    <div id="schoolsContainer">
        <div class="school-fields border rounded p-3 mb-3 position-relative" data-index="0">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-2 remove-school-btn" onclick="removeSchoolBlock(this)" style="display: none;"></button>
            <h5 class="school-title mb-3">School Details</h5>

            <div class="row">
                <!-- School Name -->
                <div class="col-lg-6 form-group mb-3">
                    <label class="form-label">School Name <span class="text-danger">*</span></label>
                    <input type="text" name="schools[0][name]" class="form-control school-name-input" required oninput="manageSchoolLevel(this)">
                </div>

                <!-- Sponsorship Type -->
                <div class="col-lg-6 form-group sponsorship-type-container mb-3">
                    <label class="form-label">Sponsorship Type <span class="text-danger">*</span></label>
                    <select name="schools[0][sponsorship_type]" class="form-select" required>
                        <option value="" disabled selected>Select Sponsorship</option>
                        <option value="Private">Private / Self-Sponsored</option>
                        <option value="Government">Government / Public</option>
                        <option value="Faith-Based">Faith-Based</option>
                    </select>
                </div>

                <!-- School Level (Dynamic Checks) -->
                <div class="col-lg-12 form-group school-level-container mb-3" style="display: none;">
                    <label class="form-label">School Level / Offerings <span class="text-danger">*</span></label>
                    <div class="d-flex gap-3">
                        <div class="form-check level-primary">
                            <input class="form-check-input" type="checkbox" name="schools[0][levels][]" value="Primary" id="primary_0" checked>
                            <label class="form-check-label" for="primary_0">Primary</label>
                        </div>
                        <div class="form-check level-olevel">
                            <input class="form-check-input" type="checkbox" name="schools[0][levels][]" value="O-Level" id="olevel_0">
                            <label class="form-check-label" for="olevel_0">O-Level (Form I - IV)</label>
                        </div>
                        <div class="form-check level-alevel">
                            <input class="form-check-input" type="checkbox" name="schools[0][levels][]" value="A-Level" id="alevel_0">
                            <label class="form-check-label" for="alevel_0">A-Level (Form V - VI)</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add School Button (Shown for Group Registrations) -->
    <div id="addSchoolWrapper" class="mb-4" style="display: none;">
        <button type="button" class="btn btn-outline-primary" onclick="addSchoolBlock()">
            + Add Another School Under Same Owner
        </button>
    </div>

    <button type="submit" class="btn btn-success px-4">Submit Registration</button>
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

function reIndexSchools() {
    const schoolBlocks = document.querySelectorAll('.school-fields');
    const isGroup = document.getElementById('groupOfSchools').checked;

    schoolBlocks.forEach((block, index) => {
        block.setAttribute('data-index', index);
        
        // Update Title Numbering
        const title = block.querySelector('.school-title');
        title.textContent = isGroup ? `School #${index + 1} Details` : 'School Details';

        // Update Remove Button Visibility
        const removeBtn = block.querySelector('.remove-school-btn');
        if (removeBtn) {
            removeBtn.style.display = (isGroup && schoolBlocks.length > 1) ? 'block' : 'none';
        }

        // Update Input Name Attributes for Laravel Array Processing
        block.querySelector('.school-name-input').name = `schools[${index}][name]`;
        block.querySelector('.sponsorship-type-container select').name = `schools[${index}][sponsorship_type]`;

        const primaryCb = block.querySelector('.level-primary input');
        const oLevelCb = block.querySelector('.level-olevel input');
        const aLevelCb = block.querySelector('.level-alevel input');

        primaryCb.name = `schools[${index}][levels][]`;
        primaryCb.id = `primary_${index}`;
        primaryCb.nextElementSibling.setAttribute('for', `primary_${index}`);

        oLevelCb.name = `schools[${index}][levels][]`;
        oLevelCb.id = `olevel_${index}`;
        oLevelCb.nextElementSibling.setAttribute('for', `olevel_${index}`);

        aLevelCb.name = `schools[${index}][levels][]`;
        aLevelCb.id = `alevel_${index}`;
        aLevelCb.nextElementSibling.setAttribute('for', `alevel_${index}`);
    });
}

function manageSchoolLevel(inputElement) {
    const schoolName = inputElement.value.trim().toLowerCase();
    const parentBlock = inputElement.closest('.school-fields');
    if (!parentBlock) return;

    const levelContainer = parentBlock.querySelector('.school-level-container');
    const primaryCheck = parentBlock.querySelector('.level-primary');
    const oLevelCheck = parentBlock.querySelector('.level-olevel');
    const aLevelCheck = parentBlock.querySelector('.level-alevel');
    
    const isSecondary = /secondary|high|sekondari/i.test(schoolName);

    if (schoolName.length > 0) {
        levelContainer.style.display = 'block';
    } else {
        levelContainer.style.display = 'none';
        return;
    }

    if (isSecondary) {
        primaryCheck.style.display = 'none';
        primaryCheck.querySelector('input').checked = false;
        
        oLevelCheck.style.display = 'inline-block';
        aLevelCheck.style.display = 'inline-block';
    } else {
        primaryCheck.style.display = 'inline-block';
        primaryCheck.querySelector('input').checked = true;

        oLevelCheck.style.display = 'none';
        aLevelCheck.style.display = 'none';
        oLevelCheck.querySelector('input').checked = false;
        aLevelCheck.querySelector('input').checked = false;
    }
}

function addSchoolBlock() {
    const container = document.getElementById('schoolsContainer');
    const firstBlock = container.querySelector('.school-fields');
    const newBlock = firstBlock.cloneNode(true);

    // Reset input values in cloned block
    newBlock.querySelectorAll('input[type="text"]').forEach(input => input.value = '');
    newBlock.querySelectorAll('select').forEach(select => select.selectedIndex = 0);
    newBlock.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
    newBlock.querySelector('.school-level-container').style.display = 'none';

    container.appendChild(newBlock);
    reIndexSchools();
}

function removeSchoolBlock(button) {
    const block = button.closest('.school-fields');
    const allBlocks = document.querySelectorAll('.school-fields');
    
    if (allBlocks.length > 1) {
        block.remove();
        reIndexSchools();
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
                        option.value = region.name;                 
                        option.setAttribute('data-id', region.id);   
                        option.textContent = region.name;
                        regionSelect.appendChild(option);
                    });
                }
            })
            .catch(error => console.error('Error fetching regions:', error));
    }

    function fetchDistricts(regionSelectId, districtSelectId, wardSelectId) {
        const regionSelect = document.getElementById(regionSelectId);
        const districtSelect = document.getElementById(districtSelectId);
        const wardSelect = document.getElementById(wardSelectId);
        if (!regionSelect || !districtSelect || !wardSelect) return;

        const selectedOption = regionSelect.options[regionSelect.selectedIndex];
        
        const regionId = selectedOption.getAttribute('data-id');

        if (!regionId) {
            districtSelect.innerHTML = '<option value="">Select a District</option>';
            wardSelect.innerHTML = '<option value="">Select a Ward</option>';
            return;
        }

        const url = `/get-districts?region_id=${regionId}`;
        fetch(url, { headers: { 'X-CSRF-TOKEN': csrfToken } })
            .then(response => response.json())
            .then(data => {
                districtSelect.innerHTML = '<option value="">Select a District</option>';
                if (data.districts && Array.isArray(data.districts)) {
                    data.districts.forEach(district => {
                        const option = document.createElement('option');
                        option.value = district.name;                
                        option.setAttribute('data-id', district.id); 
                        option.textContent = district.name;
                        districtSelect.appendChild(option);
                    });
                }
                wardSelect.innerHTML = '<option value="">Select a Ward</option>';
            })
            .catch(error => console.error('Error fetching districts:', error));
    }

    function fetchWards(districtSelectId, wardSelectId) {
        const districtSelect = document.getElementById(districtSelectId);
        const wardSelect = document.getElementById(wardSelectId);
        if (!districtSelect || !wardSelect) return;

        const selectedOption = districtSelect.options[districtSelect.selectedIndex];
        
        const districtId = selectedOption.getAttribute('data-id');

        if (!districtId) {
            wardSelect.innerHTML = '<option value="">Select a Ward</option>';
            return;
        }

        const url = `/get-wards?district_id=${districtId}`;
        fetch(url, { headers: { 'X-CSRF-TOKEN': csrfToken } })
            .then(response => response.json())
            .then(data => {
                wardSelect.innerHTML = '<option value="">Select a Ward</option>';
                if (data.wards && Array.isArray(data.wards)) {
                    data.wards.forEach(ward => {
                        const option = document.createElement('option');
                        option.value = ward.name;                 
                        option.setAttribute('data-id', ward.id);  
                        option.textContent = ward.name;
                        wardSelect.appendChild(option);
                    });
                }
            })
            .catch(error => console.error('Error fetching wards:', error));
    }
});
</script>

</x-layout>