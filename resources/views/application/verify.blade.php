<x-layout>
    <x-slot:title>
        Verify Application 
    </x-slot:title>
    <x-navbar />
    <x-admin.sidebar />
    <div class="main-content" style="min-height: 635px;">
        <section class="section">
            <div class="section-body">
                <div class="container mt-5">

                   
                  <livewire:applications.application-verify :application="$application" />
                </div>
            </div>
        </section>
    </div>
    <script src="{{asset('assets/bundles/prism/prism.js')}}"></script>

    <!-- Summernote JS -->
    <script src="{{ asset('assets/bundles/summernote/summernote-bs4.js') }}"></script>

    <!-- CodeMirror JS -->
    <script src="{{ asset('assets/bundles/codemirror/lib/codemirror.js') }}"></script>
    <script src="{{ asset('assets/bundles/codemirror/mode/javascript/javascript.js') }}"></script>

</x-layout>