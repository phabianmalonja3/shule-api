<div class="min-h-screen bg-slate-50 text-slate-800">
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-5 py-5">
            <div class="flex items-center gap-3">
                <div class="grid size-11 place-items-center rounded-xl bg-teal-700 text-white shadow-lg shadow-teal-700/20">
                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m3 10 9-5 9 5-9 5-9-5Z M7 12.5V17c3 2 7 2 10 0v-4.5M21 10v6" />
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-slate-900">Student Application</p>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Education Opportunity Programme</p>
                </div>
            </div>
            <div class="hidden items-center gap-2 text-sm font-medium text-slate-500 sm:flex">
                <span class="size-2 rounded-full bg-emerald-500 ring-4 ring-emerald-100"></span>
                Secure application portal
            </div>
        </div>
    </header>

    @if ($submitted)
        <main class="mx-auto grid min-h-[75vh] max-w-6xl place-items-center px-5 py-16">
            <div class="w-full max-w-xl rounded-3xl border border-slate-200 bg-white p-8 text-center shadow-xl shadow-slate-200/60 sm:p-14">
                <div class="mx-auto mb-7 grid size-16 place-items-center rounded-full bg-teal-700 text-white ring-8 ring-teal-50">
                    <svg class="size-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" />
                    </svg>
                </div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-teal-700">Application received</p>
                <h1 class="mt-3 text-3xl font-bold tracking-tight text-slate-900">Thank you for applying.</h1>
                <p class="mt-4 leading-7 text-slate-500">Your details were saved successfully. Please monitor the phone number provided for examination updates.</p>
                <button wire:click="startAnotherApplication" type="button" class="mt-8 rounded-xl bg-teal-700 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-teal-700/20 transition hover:bg-teal-800">
                    Submit another application
                </button>
            </div>
        </main>
    @else
        <main class="mx-auto max-w-6xl px-5 py-12 sm:py-16">
            <div class="mb-10 grid gap-8 lg:grid-cols-[1fr_20rem] lg:items-end">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-teal-700">2025 student intake</p>
                    <h1 class="mt-3 text-4xl font-bold tracking-tight text-slate-900 sm:text-5xl">Start your application</h1>
                    <p class="mt-4 max-w-2xl text-lg leading-8 text-slate-500">Tell us about yourself and your education journey. Fields marked with an asterisk are required.</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-5">
                    <div class="flex justify-between text-xs font-semibold text-slate-600"><span>Application progress</span><span>4 sections</span></div>
                    <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-slate-100"><div class="h-full w-full bg-gradient-to-r from-teal-700 to-teal-400"></div></div>
                    <p class="mt-2 text-xs text-slate-400">Complete all sections before submitting</p>
                </div>
            </div>

            <form wire:submit="submit" class="space-y-6">
                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-10">
                    <x-form-section-heading number="01" title="Examination preferences" description="Choose a convenient examination center, date and preferred language." />
                    <div class="grid gap-6 md:grid-cols-2">
                        <x-form-select label="Exam center" wire:model="exam_center" :error="$errors->first('exam_center')" required>
                            <option value="">Choose an exam center</option>
                            @foreach ($this->examCenters() as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </x-form-select>
                        <x-form-input label="Preferred exam date" type="date" wire:model="exam_date" :error="$errors->first('exam_date')" required />
                        <fieldset class="md:col-span-2">
                            <legend class="mb-3 text-sm font-bold text-slate-700">Language preference <span class="text-red-500">*</span></legend>
                            <div class="grid gap-3 md:grid-cols-3">
                                @foreach (['English' => 'Exam instructions in English', 'Kiswahili' => 'Maelekezo kwa Kiswahili', 'No preference' => 'Comfortable with either language'] as $language => $description)
                                    <label class="flex cursor-pointer items-center gap-3 rounded-xl border p-4 transition has-[:checked]:border-teal-600 has-[:checked]:bg-teal-50">
                                        <input wire:model="language_preference" type="radio" value="{{ $language }}" class="size-4 accent-teal-700">
                                        <span><strong class="block text-sm">{{ $language }}</strong><small class="text-xs text-slate-500">{{ $description }}</small></span>
                                    </label>
                                @endforeach
                            </div>
                            @error('language_preference') <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                        </fieldset>
                    </div>
                </section>

                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-10">
                    <x-form-section-heading number="02" title="Applicant details" description="Basic personal information about the applicant." />
                    <div class="grid gap-6 md:grid-cols-2">
                        <x-form-input label="First name" wire:model="first_name" :error="$errors->first('first_name')" required />
                        <x-form-input label="Middle name" wire:model="middle_name" :error="$errors->first('middle_name')" />
                        <x-form-input label="Surname" wire:model="surname" :error="$errors->first('surname')" required />
                        <x-form-select label="Gender" wire:model="gender" :error="$errors->first('gender')" required>
                            <option value="">Select gender</option>
                            @foreach (['Female', 'Male', 'Prefer not to say'] as $option)<option>{{ $option }}</option>@endforeach
                        </x-form-select>
                        <x-form-select label="Religion" wire:model="religion" :error="$errors->first('religion')" required>
                            <option value="">Select religion</option>
                            @foreach (['Christianity', 'Islam', 'Hinduism', 'Traditional faith', 'Other', 'Prefer not to say'] as $option)<option>{{ $option }}</option>@endforeach
                        </x-form-select>
                        <x-form-input label="Denomination" wire:model="denomination" :error="$errors->first('denomination')" placeholder="e.g. Catholic, Lutheran" />
                        <x-form-select label="Current residence" wire:model="residence" :error="$errors->first('residence')" required>
                            <option value="">Select current residence</option>
                            <option value="Father">Stays with father</option>
                            <option value="Mother">Stays with mother</option>
                            <option value="Guardian">Stays with guardian</option>
                        </x-form-select>
                    </div>
                </section>

                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-10">
                    <x-form-section-heading number="03" title="Parent & guardian details" description="Provide details for the applicant's father, mother or guardian." />
                    <div class="space-y-5">
                        @foreach ($family_contacts as $index => $contact)
                            <div wire:key="contact-{{ $contact['_key'] }}" class="rounded-2xl border border-slate-200 bg-slate-50/60 p-5 sm:p-7">
                                <div class="mb-6 flex items-center justify-between">
                                    <div><p class="text-xs font-bold uppercase tracking-wider text-teal-700">Contact {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</p><h3 class="mt-1 font-bold text-slate-800">Parent or guardian</h3></div>
                                    @if (count($family_contacts) > 1)
                                        <button wire:click="removeFamilyContact({{ $index }})" type="button" class="rounded-lg border border-red-200 bg-white p-2 text-red-600 hover:bg-red-50" aria-label="Remove contact">Remove</button>
                                    @endif
                                </div>
                                <div class="grid gap-6 md:grid-cols-2">
                                    <x-form-select label="Relationship" wire:model="family_contacts.{{ $index }}.relationship" :error="$errors->first('family_contacts.'.$index.'.relationship')" required>
                                        <option value="">Select relationship</option>
                                        @foreach (['Father', 'Mother', 'Guardian', 'Other'] as $option)<option>{{ $option }}</option>@endforeach
                                    </x-form-select>
                                    <x-form-input label="Phone number" type="tel" wire:model="family_contacts.{{ $index }}.phone" :error="$errors->first('family_contacts.'.$index.'.phone')" placeholder="+255 7XX XXX XXX" required />
                                    <x-form-input label="First name" wire:model="family_contacts.{{ $index }}.first_name" :error="$errors->first('family_contacts.'.$index.'.first_name')" required />
                                    <x-form-input label="Middle name" wire:model="family_contacts.{{ $index }}.middle_name" :error="$errors->first('family_contacts.'.$index.'.middle_name')" />
                                    <x-form-input label="Surname" wire:model="family_contacts.{{ $index }}.surname" :error="$errors->first('family_contacts.'.$index.'.surname')" required />
                                    <x-form-input label="Occupation" wire:model="family_contacts.{{ $index }}.occupation" :error="$errors->first('family_contacts.'.$index.'.occupation')" required />
                                    <x-form-input label="Region" wire:model="family_contacts.{{ $index }}.region" :error="$errors->first('family_contacts.'.$index.'.region')" required />
                                    <x-form-input label="District" wire:model="family_contacts.{{ $index }}.district" :error="$errors->first('family_contacts.'.$index.'.district')" required />
                                    <x-form-input label="Ward" wire:model="family_contacts.{{ $index }}.ward" :error="$errors->first('family_contacts.'.$index.'.ward')" required />
                                    <x-form-input label="Street / Village" wire:model="family_contacts.{{ $index }}.street" :error="$errors->first('family_contacts.'.$index.'.street')" required />
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button wire:click="addFamilyContact" type="button" class="mt-5 w-full rounded-xl border border-dashed border-teal-400 bg-teal-50 px-5 py-3 text-sm font-bold text-teal-700 hover:bg-teal-100">+ Add mother, father or guardian</button>
                </section>

                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-10">
                    <x-form-section-heading number="04" title="Education history" description="Add details for each secondary school the applicant has attended." />
                    <div class="space-y-5">
                        @foreach ($schools as $index => $school)
                            <div wire:key="school-{{ $school['_key'] }}" class="rounded-2xl border border-slate-200 bg-slate-50/60 p-5 sm:p-7">
                                <div class="mb-6 flex items-center justify-between">
                                    <div><p class="text-xs font-bold uppercase tracking-wider text-teal-700">School {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</p><h3 class="mt-1 font-bold text-slate-800">School information</h3></div>
                                    @if (count($schools) > 1)
                                        <button wire:click="removeSchool({{ $index }})" type="button" class="rounded-lg border border-red-200 bg-white p-2 text-red-600 hover:bg-red-50">Remove</button>
                                    @endif
                                </div>
                                <div class="grid gap-6 md:grid-cols-2">
                                    <div class="md:col-span-2"><x-form-input label="School name" wire:model="schools.{{ $index }}.name" :error="$errors->first('schools.'.$index.'.name')" required /></div>
                                    @foreach (['region' => 'Region', 'district' => 'District', 'ward' => 'Ward', 'street' => 'Street / Village'] as $field => $label)
                                        <x-form-input :label="$label" wire:model="schools.{{ $index }}.{{ $field }}" :error="$errors->first('schools.'.$index.'.'.$field)" required />
                                    @endforeach
                                    <x-form-select label="Maximum standard" wire:model="schools.{{ $index }}.max_standard" :error="$errors->first('schools.'.$index.'.max_standard')" required>
                                        <option value="">Select level</option><option>Form IV</option><option>Form VI</option>
                                    </x-form-select>
                                    <x-form-select label="Finalist status" wire:model.live="schools.{{ $index }}.is_finalist" :error="$errors->first('schools.'.$index.'.is_finalist')" required>
                                        <option value="0">No</option><option value="1">Yes</option>
                                    </x-form-select>
                                    @foreach (['maths_avg_score' => 'Mathematics average', 'english_avg_score' => 'English average', 'science_avg_score' => 'Science average'] as $field => $label)
                                        <x-form-input :label="$label" type="number" min="0" max="100" wire:model="schools.{{ $index }}.{{ $field }}" :error="$errors->first('schools.'.$index.'.'.$field)" placeholder="0–100" required />
                                    @endforeach
                                    <x-form-input label="Headteacher name" wire:model="schools.{{ $index }}.headteacher_name" :error="$errors->first('schools.'.$index.'.headteacher_name')" :required="(bool) $school['is_finalist']" />
                                    <x-form-input label="Headteacher phone" type="tel" wire:model="schools.{{ $index }}.headteacher_phone" :error="$errors->first('schools.'.$index.'.headteacher_phone')" :required="(bool) $school['is_finalist']" />
                                </div>
                                <p class="mt-4 text-xs text-amber-700">{{ $school['is_finalist'] ? 'Headteacher details are required for finalists.' : 'Headteacher details are optional for non-finalists.' }}</p>
                            </div>
                        @endforeach
                    </div>
                    <button wire:click="addSchool" type="button" class="mt-5 w-full rounded-xl border border-dashed border-teal-400 bg-teal-50 px-5 py-3 text-sm font-bold text-teal-700 hover:bg-teal-100">+ Add another school</button>
                </section>

                <div class="flex flex-col items-stretch justify-between gap-5 py-4 sm:flex-row sm:items-center">
                    <p class="max-w-xl text-sm leading-6 text-slate-500">By submitting, you confirm that the information provided is accurate and complete.</p>
                    <button type="submit" wire:loading.attr="disabled" class="rounded-xl bg-teal-700 px-7 py-4 text-sm font-bold text-white shadow-lg shadow-teal-700/20 transition hover:bg-teal-800 disabled:cursor-wait disabled:opacity-60">
                        <span wire:loading.remove wire:target="submit">Submit application</span>
                        <span wire:loading wire:target="submit">Submitting…</span>
                    </button>
                </div>
            </form>
        </main>
    @endif
</div>
