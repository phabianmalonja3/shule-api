<x-layout>
    <x-slot:title>
        ShuleMIS | Students' List
    </x-slot:title>

    <x-navbar />
    <x-admin.sidebar />
    <link rel="stylesheet" href="{{ asset('css/custom-styles.css') }}">
    <div class="main-content" style="min-height: 635px;">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">

                            @livewire('student-list')

                    </div>
                </div>
            </div>
        </section>
    </div>



</x-layout>

<script>

    function initializeCombinationModal() {

        var modal = document.getElementById('combinationAssignmentModal');
        var triggerBtn = document.getElementById('assignCombinationBtn');
        var cancelBtn = document.getElementById('cancelBtn');
        var selectAllCheckbox = document.getElementById('selectAllStudents');
        var studentCheckboxes = document.querySelectorAll('.student-checkbox');
        var combinationDropdown = document.getElementById('combination_id');
        var assignForm = document.getElementById('assignCombinationForm');
        
        var unassignedCountInput = document.getElementById('unassignedStudentCount');
        var unassignedCount = unassignedCountInput ? parseInt(unassignedCountInput.value) : 0;
        
        function closeModal() {
            if (modal) {
                modal.style.display = "none";
            }
        }
        
        if (triggerBtn) {

             triggerBtn.onclick = null; 
             triggerBtn.onclick = function(e) {
                 e.preventDefault();
                 modal.style.display = "block";
             }
        }
        
        if (cancelBtn) {
            cancelBtn.onclick = closeModal;
        }
        
        window.onclick = function(event) {
            if (event.target == modal) {
                closeModal();
            }
        }

        if (studentCheckboxes.length > 0) {
            
            if (selectAllCheckbox) {

                selectAllCheckbox.removeEventListener('change', arguments.callee);
                
                selectAllCheckbox.addEventListener('change', function() {
                    const isChecked = this.checked;
                    studentCheckboxes.forEach(function(checkbox) {
                        checkbox.checked = isChecked;
                    });
                });
            }

            studentCheckboxes.forEach(function(checkbox) {

                checkbox.removeEventListener('change', arguments.callee);
                checkbox.addEventListener('change', function() {

                    if (!selectAllCheckbox) return; 
                    
                    const allChecked = Array.from(studentCheckboxes).every(cb => cb.checked);
                    selectAllCheckbox.checked = allChecked;
                });
            });
        }
        
        if (assignForm) {
            assignForm.onsubmit = null;
            assignForm.onsubmit = function(e) {

                if (combinationDropdown.value === "") {
                    alert("Please select a Combination from the dropdown.");
                    e.preventDefault();
                    return;
                }
                
                if (unassignedCount > 1) {
                    var selectedCount = Array.from(studentCheckboxes).filter(cb => cb.checked).length;
                    
                    if (selectedCount === 0) {
                        alert("Please select at least one student to assign the Combination.");
                        e.preventDefault();
                        return;
                    }
                }
            }
        }
    }

    document.addEventListener('DOMContentLoaded', initializeCombinationModal);
    document.addEventListener('livewire:initialized', () => {
        Livewire.hook('morph.updated', initializeCombinationModal);
    });

</script>