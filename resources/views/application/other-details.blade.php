<x-layout>
    <x-slot:title>
        Application Form | Other Details
    </x-slot:title>

    <div class="container-fluid">
        <div class="row">
            <div class="col-5" style="padding-top:10px">
                <img src="{{ asset('/logo/bright-future-logo.png') }}" alt="Logo" class="img-fluid" style="width:60%">
            </div>

            <div class="col-7" style="height:80%; background-color:rgb(255, 255, 255); padding-top:10px">
                <form method="POST" action="{{ url('bright-future-schools/application') }}">
                    @csrf
                    <input type="hidden" class="form-control" id="level" name="level"
                    value="4">
                    <!-- Applicant Details -->
                    <h6 class="mb-4" style="height:80%; border-bottom: 1px solid #007bff; padding-bottom: 2px;">
                        Guardian's Details
                    </h6>

                    <div class="row">
                        <div class="form-group col-lg-4 col-12">
                            <label for="guardian-first-name">First Name</label>
                            <input type="text" class="form-control" id="g-fname" name="g-fname"
                                value="{{ old('g-fname') }}">

                        </div>
                        <div class="form-group col-lg-4 col-12">
                            <label for="guardian-middle-name">Middle Name (Optional)</label>
                            <input type="text" class="form-control" id="g-mname" name="g-mname"
                                value="{{ old('g-mname') }}">

                        </div>
                        <div class="form-group col-lg-4 col-12">
                            <label for="guardian-surname">Surname</label>
                            <input type="text" class="form-control" id="g-surname" name="g-surname"
                                value="{{ old('g-surname') }}">

                        </div>
                        <div class="mb-4 col-md-4">
                            <label for="guardian-marital-status">Marital Status</label>
                            <select class="form-control" id="g-marital-status">
                                <option value=""  >Select Marital Status</option>
                                <option value="single"  >Single</option>
                                <option value="married"  >Married</option>
                                <option value="divorced"  >Divorced</option>
                                <option value="widowed"  >Widowed</option>
                                <option value="separated"  >Separated</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-4 col-12">
                            <label for="guardian-occupation">Occupation</label>
                            <input type="text" class="form-control" id="g-occupation" name="g-occupation"
                                value="{{ old('g-occupation') }}">

                        </div>
                        <div class="mb-4 col-md-4">
                            <label for="guardian-religion">Religion</label>
                            <select class="form-control" id="g-religion">
                                <option value=""  >Select Religion</option>
                                <option value="christianity"  >Christianity</option>
                                <option value="muslim"  >Muslim</option>
                                <option value="buddhism"  >Buddhism</option>
                                <option value="others"  >Others</option>
                               
                            </select>
                        </div>
                        <div class="mb-4 col-md-4">
                            <label for="guardian-denomination">Denomination</label>
                            <select class="form-control" id="g-denomination">
                                <option value=""  >Select Denomination</option>
                                <option value="catholic"  >Roman Catholic</option>
                                <option value="lutheran"  >Lutheran</option>
                                <option value="assemblies"  >Assemblies of God</option>
                                <option value="sabbath"  >7th Day Adventist</option>
                               
                            </select>
                        </div>
                        <div class="form-group col-lg-4 col-12">
                            <label for="guardian-physical-address">Physical Address</label>
                            <textarea id="guardian-physical-address" name="guardian-physical-address"
                                class="form-control">{{ old('guardian-physical-address') }}</textarea>
                        </div>
                        <div class="mb-4 col-md-4 col-12">
                            <label for="guardian-primary-phone">Primary Phone Number</label>
                            <input type="tel" class="form-control" id="g-phone"
                            placeholder="eg, 07XXXXXXXXX"
                                value="{{ old('g-phone', $teacher->phone ?? '') }}">
                        </div>
                        <div class="mb-4 col-md-4 col-12">
                            <label for="guardian-secondary-phone">Secondary Phone Number (Optional)</label>
                            <input type="tel" class="form-control" id="g-secondary-phone"
                            placeholder="eg, 07XXXXXXXXX"
                                value="{{ old('g-secondary-phone', $teacher->phone ?? '') }}">
                        </div>
                        <div class="form-group col-lg-4 col-12">
                            <label for="guardian-email">Email (Optional)</label>
                            <input type="text" class="form-control" id="g-email" name="g-email"
                                value="{{ old('g-email') }}">

                        </div>
                    </div>

                    <h6 class="mb-4" style="border-bottom: 1px solid #007bff; padding-bottom: 2px;">
                       Exam Details
                    </h6>

                    <div class="row">
                        <div class="mb-4 col-md-4">
                            <label for="exam-language">Exam Language</label>
                            <select class="form-control" id="exam-language">
                                <option value=""  >Select Language</option>
                                <option value="english"  >English</option>
                                <option value="swahili"  >Kiswahili</option>
                            </select>
                        </div>
                        <div class="mb-4 col-md-4">
                            <label for="exam-date">Exam Date</label>
                            <select class="form-control" id=""exam-date">
                                <option value=""  >Select Exam Date</option>
                                <option value="1"  >15th September 2025</option>
                                <option value="2"  >20th September 2025</option>
                            </select>
                        </div>
                        <div class="mb-4 col-md-4">
                            <label for="exam-venue">Exam Venue</label>
                            <select class="form-control" id=""exam-date">
                                <option value=""  >Select Exam Venue</option>
                                <option value="1"  >15th September 2025</option>
                                <option value="2"  >20th September 2025</option>
                            </select>
                        </div>
                    </div>

                    <h6 class="mb-4" style="border-bottom: 1px solid #007bff; padding-bottom: 2px;">
                        Other Details
                    </h6>

                    <div class="form-group col-lg-12 col-12">
                        Parent(s) Deceased? &nbsp;
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="both-deceased"
                                name="parent-status" value="both-deceased" {{
                                old('parent-status')=='both-deceased' ? 'checked' : '' }}>
                            <label class="form-check-label" for="new">Both Deceased</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="mother-deceased"
                                name="parent-status" value="mother-deceased" {{
                                old('parent-status')=='mother-deceased' ? 'checked' : '' }}>
                            <label class="form-check-label" for="mother-deceased">Mother</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="father-deceased"
                                name="parent-status" value="father-deceased" {{
                                old('parent-status')=='father-deceased' ? 'checked' : '' }}>
                            <label class="form-check-label" for="father-deceased">Father</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="none"
                                name="parent-status" value="none" {{
                                old('parent-status')=='none' ? 'checked' : '' }}>
                            <label class="form-check-label" for="none">None</label>
                        </div>
                    </div>
                    
                    <div class="form-group col-lg-12 col-12">
                        Living with? &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="both"
                                name="living-status" value="both" {{
                                old('living-status')=='both' ? 'checked' : '' }}>
                            <label class="form-check-label" for="new">Both Parents</label> &nbsp; &nbsp; &nbsp;
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="mother"
                                name="living-status" value="mother" {{
                                old('living-status')=='mother' ? 'checked' : '' }}>
                            <label class="form-check-label" for="mother">Mother</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="father"
                                name="living-status" value="father" {{
                                old('living-status')=='father' ? 'checked' : '' }}>
                            <label class="form-check-label" for="father">Father</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="guardian"
                                name="living-status" value="guardian" {{
                                old('living-status')=='father' ? 'checked' : '' }}>
                            <label class="form-check-label" for="guardian">Guardian</label>
                        </div>
                    </div>
                    <!-- Submit Button -->
                    <div class="form-group">
                        <a href="{{ url('bright-future-schools/application') }}" class="btn btn-primary"> Go Back </a>
                        <a href="{{ url('bright-future-schools') }}" class="btn btn-primary"> Save & Exit </a>
                        <button type="continue" class="btn btn-primary">Submit</button>
                        <a href="{{ url('bright-future-schools') }}" class="btn btn-primary"> Cancel </a>
                    </div>
                </form>
            </div>
        </div>
    </div>


</x-layout>