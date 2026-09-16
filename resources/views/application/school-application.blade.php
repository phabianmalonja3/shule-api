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

<script>
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

function toggleFormFields() {
    const isGroup = document.getElementById('groupOfSchools').checked;
    const addSchoolWrapper = document.getElementById('addSchoolWrapper');
    const schoolBlocks = document.querySelectorAll('.school-fields');

    addSchoolWrapper.style.display = isGroup ? 'block' : 'none';

    // Show/Hide remove buttons based on group mode
    schoolBlocks.forEach((block, idx) => {
        const removeBtn = block.querySelector('.remove-school-btn');
        if (removeBtn) {
            removeBtn.style.display = (isGroup && schoolBlocks.length > 1) ? 'block' : 'none';
        }
    });

    // If switched back to single, remove extra school blocks beyond the first
    if (!isGroup && schoolBlocks.length > 1) {
        for (let i = 1; i < schoolBlocks.length; i++) {
            schoolBlocks[i].remove();
        }
        reIndexSchools();
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
</script>