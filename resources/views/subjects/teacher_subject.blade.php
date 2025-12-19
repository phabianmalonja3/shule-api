
    <x-layout>
        <x-slot:title>
            Subject List Page
        </x-slot:title>
    
        <x-navbar />
        <x-admin.sidebar />
    
        <div class="main-content" style="min-height: 635px;">
            <section class="section">
                <div class="section-body">
                    <div class="row">
                        <div class="col-12">
                            <div class=""> <!-- Shadow overlay -->
                <div class="container mt-5">
                    <h1 class="mb-4 text-center ">Subjects Available</h1>
                    <div class="subject-grid">
                        <!-- First card (larger card) -->
                        
                        <!-- Remaining cards -->
                        @foreach ($subjects as $key => $subject)
                          
                                <div class="subject-card" style="background-color: {{ $subject['color'] }};">
                                    <div class="subject-card-inner">
                                        <!-- Front side -->
                                        <div class="subject-card-front">
                                            <h5>{{ $subject['name'] }}</h5>
                                        </div>
                                        <!-- Back side -->
                                        <div class="subject-card-back">
                                            <div>
                                                @if ($subject->schoolClasses->isNotEmpty())
                                                    @foreach ($subject->schoolClasses as $class)
                                                        <p>{{ $class->name }}</p> <!-- Assuming 'name' contains the class name -->
                                                    @endforeach
                                                @else
                                                    <p>No classes assigned</p>
                                                @endif
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                           
                        @endforeach
                    </div>
                </div>
                                </div></div></div></div></div>
                </div>
            </section>
    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add bounce animation to all cards
        document.addEventListener("DOMContentLoaded", () => {
            const cards = document.querySelectorAll(".subject-card");
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.classList.add("animate-bounce");
                }, index * 100); // Add a slight delay between each card
            });
        });
    </script>
  </section>
</div>
</x-layout>

