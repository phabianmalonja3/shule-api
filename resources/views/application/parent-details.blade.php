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
                    value="3">
                    <!-- Applicant Details -->
                    <h6 class="mb-4" style="border-bottom: 1px solid #007bff; padding-bottom: 2px;">
                        Mother's Details
                    </h6>

                    <div class="row">
                        <div class="form-group col-lg-4 col-12">
                            <label for="first_name">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name"
                                value="{{ old('first_name') }}">

                        </div>
                        <div class="form-group col-lg-4 col-12">
                            <label for="middle_name">Middle Name (Optional)</label>
                            <input type="text" class="form-control" id="middle_name" name="middle_name"
                                value="{{ old('middle_name') }}">

                        </div>
                        <div class="form-group col-lg-4 col-12">
                            <label for="surname">Surname</label>
                            <input type="text" class="form-control" id="surname" name="surname"
                                value="{{ old('surname') }}">

                        </div>
                        <div class="mb-4 col-md-4">
                            <label for="m-marital-status">Marital Status</label>
                            <select class="form-control" id="m-marital-status">
                                <option value=""  >Select Marital Status</option>
                                <option value="single"  >Single</option>
                                <option value="married"  >Married</option>
                                <option value="divorced"  >Divorced</option>
                                <option value="widowed"  >Widowed</option>
                                <option value="separated"  >Separated</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-4 col-12">
                            <label for="m-occupation">Occupation</label>
                            <input type="text" class="form-control" id="m-occupation" name="m-occupation"
                                value="{{ old('m-occupation') }}">

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
                            <label for="m-physical-address">Physical Address</label>
                            <textarea id="m-physical-address" name="m-physical-address"
                                class="form-control">{{ old('m-physical-address') }}</textarea>
                        </div>
                        <div class="mb-4 col-md-4 col-12">
                            <label for="m-primary-phone">Primary Phone Number</label>
                            <input type="tel" class="form-control" id="phone"
                            placeholder="eg, 07XXXXXXXXX"
                                value="{{ old('phone', $teacher->phone ?? '') }}">
                        </div>
                        <div class="mb-4 col-md-4 col-12">
                            <label for="m-secondary-phone">Secondary Phone Number (Optional)</label>
                            <input type="tel" class="form-control" id="m-secondary-phone"
                            placeholder="eg, 07XXXXXXXXX"
                                value="{{ old('m-secondary-phone', $teacher->phone ?? '') }}">
                        </div>
                        <div class="form-group col-lg-4 col-12">
                            <label for="email">Email (Optional)</label>
                            <input type="text" class="form-control" id="email" name="email"
                                value="{{ old('email') }}">

                        </div>
                    </div>

                    <h6 class="mb-4" style="border-bottom: 1px solid #007bff; padding-bottom: 2px;">
                        Father's Details
                    </h6>

                    <div class="row">
                        <div class="form-group col-lg-4 col-12">
                            <label for="f_first_name">First Name</label>
                            <input type="text" class="form-control" id="f_first_name" name="f_first_name"
                                value="{{ old('f_first_name') }}">

                        </div>
                        <div class="form-group col-lg-4 col-12">
                            <label for="f_middle_name">Middle Name (Optional)</label>
                            <input type="text" class="form-control" id="f_middle_name" name="f_middle_name"
                                value="{{ old('f_middle_name') }}">

                        </div>
                        <div class="form-group col-lg-4 col-12">
                            <label for="f_surname">Surname</label>
                            <input type="text" class="form-control" id="f_surname" name="f_surname"
                                value="{{ old('f_surname') }}">

                        </div>
                        <div class="mb-4 col-md-4">
                            <label for="f-marital-status">Marital Status</label>
                            <select class="form-control" id="f-marital-status">
                                <option value=""  >Select Marital Status</option>
                                <option value="single"  >Single</option>
                                <option value="married"  >Married</option>
                                <option value="divorced"  >Divorced</option>
                                <option value="widowed"  >Widowed</option>
                                <option value="separated"  >Separated</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-4 col-12">
                            <label for="f-occupation">Occupation</label>
                            <input type="text" class="form-control" id="f-occupation" name="f-occupation"
                                value="{{ old('f-occupation') }}">

                        </div>
                        <div class="mb-4 col-md-4">
                            <label for="f-religion">Religion</label>
                            <select class="form-control" id="f-religion">
                                <option value=""  >Select Religion</option>
                                <option value="christianity"  >Christianity</option>
                                <option value="muslim"  >Muslim</option>
                                <option value="buddhism"  >Buddhism</option>
                                <option value="others"  >Others</option>
                               
                            </select>
                        </div>
                        <div class="mb-4 col-md-4">
                            <label for="f-denomination">Denomination</label>
                            <select class="form-control" id="f-denomination">
                                <option value=""  >Select Denomination</option>
                                <option value="catholic"  >Roman Catholic</option>
                                <option value="lutheran"  >Lutheran</option>
                                <option value="assemblies"  >Assemblies of God</option>
                                <option value="sabbath"  >7th Day Adventist</option>
                               
                            </select>
                        </div>
                        <div class="form-group col-lg-4 col-12">
                            <label for="f-physical-address">Physical Address</label>
                            <textarea id="f-physical-address" name="f-physical-address"
                                class="form-control">{{ old('f-physical-address') }}</textarea>
                        </div>
                        <div class="mb-4 col-md-4 col-12">
                            <label for="f-primary-phone">Primary Phone Number</label>
                            <input type="tel" class="form-control" id="f-phone"
                            placeholder="eg, 07XXXXXXXXX"
                                value="{{ old('f-phone', $teacher->phone ?? '') }}">
                        </div>
                        <div class="mb-4 col-md-4 col-12">
                            <label for="f-secondary-phone">Secondary Phone Number (Optional)</label>
                            <input type="tel" class="form-control" id="f-secondary-phone"
                            placeholder="eg, 07XXXXXXXXX"
                                value="{{ old('f-secondary-phone', $teacher->phone ?? '') }}">
                        </div>
                        <div class="form-group col-lg-4 col-12">
                            <label for="f-email">Email (Optional)</label>
                            <input type="text" class="form-control" id="f-email" name="f-email"
                                value="{{ old('f-email') }}">

                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="form-group">
                        <a href="{{ url('bright-future-schools/application') }}" class="btn btn-primary"> Go Back </a>
                        <a href="{{ url('bright-future-schools') }}" class="btn btn-primary"> Save & Exit </a>
                        <button type="continue" class="btn btn-primary">Save & Continue</button>
                        <a href="{{ url('bright-future-schools') }}" class="btn btn-primary"> Cancel </a>
                    </div>
                </form>
            </div>
        </div>
    </div>


</x-layout>