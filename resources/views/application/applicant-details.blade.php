<x-layout>
    <x-slot:title>
        Application Form
    </x-slot:title>

    <div class="container-fluid">
        <div class="row">
            <div class="col-5" style="height:100%; padding-top:10px">
                <img src="{{ asset('/logo/bright-future-logo.png') }}" alt="Logo" class="img-fluid" style="width:60%">
            </div>

            <div class="col-7" style="height:100%; background-color:rgb(255, 255, 255); padding-top:10px">
                <form method="POST" action="{{ url('bright-future-schools/application') }}">
                    @csrf
                    <input type="hidden" class="form-control" id="level" name="level"
                    value="2">
                    <!-- School Information Section -->
                    <h6 class="mb-4" style="border-bottom: 1px solid #007bff; padding-bottom: 5px;">
                        Application Type
                    </h6>

                    <!-- Application Type -->
                    <div class="form-group col-lg-6 col-12">
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="new"
                                name="application_type" value="New" {{
                                old('application_type')=='New' ? 'checked' : '' }}>
                            <label class="form-check-label" for="new">New Application</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="saved"
                                name="application_type" value="Saved" {{
                                old('application_type')=='Saved' ? 'checked' : '' }}>
                            <label class="form-check-label" for="private">Saved Application</label>
                        </div>

                    </div>


                    <!-- Applicant Details -->
                    <h6 class="mb-4" style="border-bottom: 1px solid #007bff; padding-bottom: 2px;">
                        Applicant Details
                    </h6>

                    <div class="row">
                        <div class="form-group col-lg-4 col-12">
                            <label for="first_name">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name"
                                value="{{ old('first_name') }}">

                        </div>
                        <div class="form-group col-lg-4 col-12">
                            <label for="middle_name">Middle Name </label>
                            <input type="text" class="form-control" id="middle_name" name="middle_name"
                                value="{{ old('middle_name') }}">

                        </div>
                        <div class="form-group col-lg-4 col-12">
                            <label for="middle_name">Surname</label>
                            <input type="text" class="form-control" id="middle_name" name="surname"
                                value="{{ old('surname') }}">

                        </div>
                        <div class="mb-4 col-md-2">
                            <label for="gender">Gender</label>
                            <select class="form-control" id="gender">
                                <option value=""  >Select Gender</option>
                                <option value="female"  >Female</option>
                                <option value="male"  >Male</option>
                            </select>
                        </div>
                        &nbsp; &nbsp; &nbsp;
                        <div class="mb-4 form-group">
                            <label for="dob">Date of Birth</label>
                            <input type="date" class="form-control" id="dob">
                        </div>
                        &nbsp; &nbsp;
                        <div class="form-group col-lg-4 col-12">
                            <label for="hospital">Hospital</label>
                            <input type="text" class="form-control" id="hospital" name="hospital"
                                value="{{ old('hospital') }}">

                        </div>
                        <div class="form-group col-4">
                            <label for="region">Region</label>
                            <select class="form-control" id="region" name="region" onchange="fetchDistricts()">
                                <option value="">Select Region</option>
                            </select>

                        </div>
                        <div class="form-group col-4">
                            <label for="district">District</label>
                            <select class="form-control" id="district" name="district" onchange="fetchWards()">
                                <option value="">Select District</option>
                            </select>

                        </div>
                        <div class="mb-4 col-md-4">
                            <label for="religion">Religion</label>
                            <select class="form-control" id="religion">
                                <option value=""  >Select Religion</option>
                                <option value="christianity"  >Christianity</option>
                                <option value="muslim"  >Muslim</option>
                                <option value="buddhism"  >Buddhism</option>
                                <option value="others"  >Others</option>
                               
                            </select>
                        </div>
                        <div class="mb-4 col-md-4">
                            <label for="denomination">Denomination</label>
                            <select class="form-control" id="denomination">
                                <option value=""  >Select Denomination</option>
                                <option value="catholic"  >Roman Catholic</option>
                                <option value="lutheran"  >Lutheran</option>
                                <option value="assemblies"  >Assemblies of God</option>
                                <option value="sabbath"  >7th Day Adventist</option>
                               
                            </select>
                        </div>
                        <div class="form-group col-lg-4 col-12">
                            <label for="physical-address">Physical Address</label>
                            <textarea id="physical-address" name="physical-address"
                                class="form-control">{{ old('physical-address') }}</textarea>
                        </div>
                        <div class="form-group col-lg-4 col-12">
                            <label for="address">Postal Address</label>
                            <textarea id="address" name="address"
                                class="form-control">{{ old('address') }}</textarea>
                        </div>

                    </div>

                    <!-- Schools Details -->
                    <h6 class="mb-4" style="border-bottom: 1px solid #007bff; padding-bottom: 2px;">
                        Schools Attended
                    </h6>

                    <div class="row">
                        <div class="form-group col-lg-4 col-12">
                            <label for="nursery">Nursery School (Optional)</label>
                            <input type="text" class="form-control" id="nursery" name="nursery"
                                value="{{ old('nursery') }}">

                        </div>
                        <div class="form-group col-lg-4 col-12">
                            <label for="nursery-address">Address (Optional)</label>
                            <input type="text" class="form-control" id="nursery-address" name="nursery-address"
                                value="{{ old('nursery-address') }}">

                        </div>
                        <div class="form-group col-lg-4 col-12">
                            <label for="nursery-place">Place (Optional)</label>
                            <input type="text" class="form-control" id="nursery-place" name="nursery-place"
                                value="{{ old('nursery-place') }}">

                        </div>

                        <div class="form-group col-lg-4 col-12">
                            <label for="primary">Primary School</label>
                            <input type="text" class="form-control" id="primary" name="primary"
                                value="{{ old('primary') }}">

                        </div>
                        <div class="form-group col-lg-4 col-12">
                            <label for="primary-address">Address</label>
                            <input type="text" class="form-control" id="primary-address" name="primary-address"
                                value="{{ old('primary-address') }}">

                        </div>
                        <div class="form-group col-lg-4 col-12">
                            <label for="primary-place">Place</label>
                            <input type="text" class="form-control" id="primary-place" name="primary-place"
                                value="{{ old('primary-place') }}">

                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="form-group">
                        <a href="{{ url('bright-future-schools') }}" class="btn btn-primary"> Save & Exit </a>
                        <button type="continue" class="btn btn-primary">Save & Continue</button>
                        <a href="{{ url('bright-future-schools') }}" class="btn btn-primary"> Cancel </a>
                    </div>
                </form>
            </div>
        </div>
    </div>


</x-layout>