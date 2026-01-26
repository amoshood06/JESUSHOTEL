<?php include 'header-one.php'; ?>
  <!--herosection-->
  <section class="relative min-h-screen w-full flex items-center justify-center overflow-hidden font-sans">
  
  <div class="absolute inset-0 z-0">
    <div class="absolute inset-0 bg-[url('./asset/gallary/IMG-20251129-WA0045.jpg')] bg-cover bg-center bg-no-repeat"></div>
    <div class="absolute inset-0 bg-black/50"></div>
  </div>

  <div class="relative z-10 w-full max-w-6xl px-6 py-20 flex flex-col items-center text-center text-white">
    
    <p class="text-xs md:text-sm tracking-[0.4em] uppercase font-medium mb-4 opacity-90">
      Welcome To
    </p>

    <h1 class="font-serif text-3xl sm:text-5xl md:text-6xl lg:text-7xl font-bold uppercase tracking-tight leading-tight mb-6">
     Avilla Okada <br class="hidden md:block">Hotel
    </h1>

    <p class="text-lg sm:text-xl md:text-2xl italic font-light mb-10 opacity-95">
      Enjoyable Staying Since 1999
    </p>

    <div class="flex flex-col sm:flex-row items-center gap-4 sm:gap-6 mb-12 w-full sm:w-auto">
      <a href="bookings.php" class="group w-full sm:w-auto flex items-center justify-center gap-2 border-2 border-white rounded-full px-8 py-3.5 text-xs md:text-sm font-bold uppercase tracking-widest transition-all hover:bg-white hover:text-black">
        Book A Room
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
        </svg>
      </a>

      <a href="about.php" class="group w-full sm:w-auto flex items-center justify-center gap-2 border-2 border-white rounded-full px-8 py-3.5 text-xs md:text-sm font-bold uppercase tracking-widest transition-all hover:bg-white hover:text-black">
        More About Us
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:translate-y-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
      </a>
    </div>

    <div class="h-16 w-px bg-white/60 mb-8"></div>

    <div class="max-w-2xl px-4">
      <p class="text-xs sm:text-sm md:text-base italic leading-relaxed text-gray-200">
        <span class="font-bold underline decoration-1 underline-offset-4">Avilla Okada Hotel</span> is part of a collection of the finest independent luxury hotels in the Edo region. Learn more about our offering!
      </p>
    </div>
  </div>
</section>
<!--about us section-->
<section class="relative bg-[#111111] text-white py-20 px-6 sm:px-12 lg:px-24">
  
  <div class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-full">
    <svg width="60" height="30" viewBox="0 0 60 30" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M30 0L60 30H0L30 0Z" fill="#111111"/>
    </svg>
  </div>

  <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-12 lg:gap-24">
    
    <div class="space-y-8">
      <p class="text-gray-300 leading-relaxed text-sm sm:text-base italic md:not-italic">
        <span class="float-left text-6xl font-serif text-[#D48255] mr-3 mt-1 leading-[0.8]">D</span>
        esign and comfort are perfectly combined here, because we care about your well-being – all the materials used for room decoration are environment friendly, and your individual climate control can be adjusted accurately by degree.
      </p>
      
      <p class="text-gray-300 leading-relaxed text-sm sm:text-base">
        Our friendly and professional staff will efficiently be at your service in the best discretionary manner to provide your comfortable staying.
      </p>
    </div>

    <div class="space-y-8">
      <p class="text-gray-300 leading-relaxed text-sm sm:text-base">
        Book your holiday or event with us – and check our special offers – to experience the very best our beautiful coast has to offer.
      </p>

      <p class="text-gray-300 leading-relaxed text-sm sm:text-base">
        Our suites of great conference halls are both impressive and flexible. Superior interior design, ambiance of natural sunlight and great looking shades of palm trees make them just as popular a choice for weddings and other tailor-made events as for business. So come let's give you a royal treat in nature's paradise
      </p>
    </div>

  </div>
</section>

<!--room section-->
<section class="bg-white py-20 px-6 sm:px-12 lg:px-24 max-w-7xl mx-auto overflow-hidden">
  <div class="flex flex-col lg:flex-row items-start gap-12 lg:gap-4">
    
    <div class="w-full lg:w-1/4 pt-10">
      <div class="relative mb-6">
        <h2 class="text-[#D48255] font-serif text-5xl md:text-6xl italic">Suites</h2>
        <div class="absolute top-1/2 left-full ml-4 w-32 h-[1px] bg-[#D48255] hidden lg:block"></div>
      </div>
      <p class="text-gray-500 text-sm leading-relaxed max-w-xs">
        Comfort, serenity and natural environment are perfectly combined here, because we care about your well-being.
      </p>
    </div>

    <div class="w-full lg:w-3/4 grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
      
      <div class="relative group overflow-hidden md:mt-24 shadow-lg">
        <div class="absolute top-0 left-0 right-0 bg-black/40 text-white py-2 px-4 text-center italic font-serif z-10">
          Standard Room
        </div>
        <img src="asset/gallary/IMG-20251129-WA0019.jpg" alt="Suite De Lodge" class="w-full h-80 object-cover transition-all duration-500 group-hover:scale-110">
        <div class="absolute inset-0 bg-black/20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
          <a href="standard-rooms.php">
            <button class="bg-[#D48255] text-white px-6 py-2 rounded-full text-xs font-bold uppercase tracking-widest hover:bg-white hover:text-[#D48255] transition-colors">View Details</button>
            </a>
        </div>
        <div class="bg-black/40 text-white py-2 px-4 text-center italic font-serif z-10">
          ₦16,000.00 Per Night
        </div>
      </div>

      <div class="flex flex-col gap-4">
        <div class="relative group overflow-hidden shadow-lg">
          <div class="absolute top-0 left-0 right-0 bg-black/40 text-white py-2 px-4 text-center italic font-serif z-10">
            Deluxe Room
          </div v>
          <img src="asset/gallary/IMG-20251129-WA0017.jpg" alt="Standard Room" class="w-full h-[380px] object-cover transition-all duration-500 group-hover:scale-110">
          <div class="absolute inset-0 bg-black/20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
            <a href="deluxe-rooms.php">
            <button class="bg-[#D48255] text-white px-6 py-2 rounded-full text-xs font-bold uppercase tracking-widest hover:bg-white hover:text-[#D48255] transition-colors">View Details</button>
            </a>
          </div>
          <div class="bg-black/40 text-white py-2 px-4 text-center italic font-serif z-10">
          ₦24,000.00 Per Night
        </div>
        </div>
      </div>

      <div class="relative group overflow-hidden md:mb-24 shadow-lg">
        <div class="absolute top-0 left-0 right-0 bg-black/40 text-white py-2 px-4 text-center italic font-serif z-10">
          Executive Suite
        </div>
        <img src="asset/gallary/IMG-20251129-WA0023.jpg" alt="Ambassador Suite" class="w-full h-80 object-cover transition-all duration-500 group-hover:scale-110">
        <div class="absolute inset-0 bg-black/20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
          <a href="executive-rooms.php">
          <button class="bg-[#D48255] text-white px-6 py-2 rounded-full text-xs font-bold uppercase tracking-widest hover:bg-white hover:text-[#D48255] transition-colors">View Details</button>
          </a>
        </div>
        <div class="bg-black/40 text-white py-2 px-4 text-center italic font-serif z-10">
          ₦20,000.00 Per Night
        </div>
      </div>

    </div>
  </div>
</section>

<!--bar-section-->
<section class="relative bg-fixed bg-cover bg-center py-24" style="background-image: url('https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?q=80&w=2070&auto=format&fit=crop');">
  <div class="absolute inset-0 bg-black/40"></div>

  <div class="relative z-10 max-w-6xl mx-auto px-6">
    <div class="flex flex-col md:flex-row shadow-2xl overflow-hidden min-h-[500px]">
      
      <div class="md:w-1/2 bg-[#F4F1EE]/95 p-10 flex flex-col items-center justify-center text-center relative">
        <div class="absolute top-10 left-10 w-20 h-px bg-[#D48255]"></div>
        <div class="absolute top-16 left-20 w-20 h-px bg-[#D48255]"></div>

        <div class="mb-8">
          <h3 class="font-serif text-5xl text-[#D48255] italic">Avilla</h3>
          <p class="text-xs uppercase tracking-[0.3em] text-gray-500 font-bold">Bar</p>
        </div>

        <div class="mb-10">
          <h3 class="font-serif text-5xl text-[#D48255] italic">Avilla</h3>
          <p class="text-xs uppercase tracking-[0.3em] text-gray-500 font-bold">Restaurant</p>
        </div>

        <div class="flex gap-10 border-t border-b border-gray-300 py-6 mb-8 w-full justify-center">
          <div>
            <p class="text-3xl font-bold text-[#D48255]">450 +</p>
            <p class="text-[10px] uppercase tracking-widest text-gray-600">Beautiful Dishes</p>
          </div>
          <div class="w-px h-12 bg-gray-300"></div>
          <div>
            <p class="text-3xl font-bold text-[#D48255]">30 +</p>
            <p class="text-[10px] uppercase tracking-widest text-gray-600">Caring Staff</p>
          </div>
        </div>

        <p class="text-gray-600 text-sm italic leading-relaxed">
          We pride ourselves to have one of the best bar & Restaurants in Benin City. We welcome you to come enjoy our services.
        </p>
      </div>

      <div class="md:w-1/2 bg-black/80 backdrop-blur-sm p-12 flex flex-col items-center justify-center text-center text-white">
        <p class="text-lg leading-relaxed mb-10 font-light">
          All that matters to us, is that our guests feel comfortable in our bar. We want our customers to enjoy their drinks and their conversations, we are happy to invite everybody to relax in our comfy lounge sofas, with great meals and to end the evening with a signature drink.
        </p>
        
        <button id="menuModalBtn" class="group flex items-center gap-3 border-2 border-white rounded-full px-10 py-3 text-sm font-bold uppercase tracking-widest hover:bg-white hover:text-black transition-all cursor-pointer">
          Our Menu
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
          </svg>
        </button>
      </div>

    </div>
  </div>
</section>



<!-- Menu Modal -->
<div id="menuModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm hidden">
  <div id="menuModalContent" class="relative max-w-4xl w-full max-h-[90vh] overflow-y-auto bg-white rounded-lg shadow-2xl">
    <!-- Close button in top right corner -->
    <button id="closeMenuModal" class="absolute top-4 right-4 z-10 text-gray-400 hover:text-gray-600 transition-colors bg-white rounded-full p-2 shadow-lg">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>

    <!-- Modal Content -->
    <div class="p-6">
      <div class="text-center mb-8">
        <h2 class="text-3xl font-serif italic text-[#D48255]">Our Menu</h2>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Food Section -->
        <div>
          <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-[#D48255]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Food Categories
          </h3>
          <div class="space-y-2">
            <?php foreach ($foodCategories as $category): ?>
              <a href="food.php?category=<?= urlencode($category) ?>"
                 class="block p-3 rounded-lg border border-gray-200 hover:border-[#D48255] hover:bg-[#D48255]/5 transition-all duration-200 group">
                <div class="flex items-center justify-between">
                  <span class="font-medium text-gray-700 group-hover:text-[#D48255]"><?= htmlspecialchars($category) ?></span>
                  <svg class="w-4 h-4 text-gray-400 group-hover:text-[#D48255] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </div>
              </a>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Drink Section -->
        <div>
          <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-[#D48255]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            Drink Categories
          </h3>
          <div class="space-y-2">
            <?php foreach ($drinkCategories as $category): ?>
              <a href="drink.php?drink_category=<?= urlencode($category) ?>"
                 class="block p-3 rounded-lg border border-gray-200 hover:border-[#D48255] hover:bg-[#D48255]/5 transition-all duration-200 group">
                <div class="flex items-center justify-between">
                  <span class="font-medium text-gray-700 group-hover:text-[#D48255]"><?= htmlspecialchars($category) ?></span>
                  <svg class="w-4 h-4 text-gray-400 group-hover:text-[#D48255] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </div>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <!-- View All Menu Button -->
      <div class="mt-8 text-center">
        <a href="restaurant-and-bar.php" class="inline-flex items-center gap-2 bg-[#D48255] text-white px-6 py-3 rounded-full font-bold uppercase tracking-widest hover:bg-[#B86A3A] transition-colors">
          View Full Menu
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </a>
      </div>
    </div>
  </div>
</div>

<!-- services section-->
<section class="bg-white py-20 px-6 sm:px-12 lg:px-24 max-w-7xl mx-auto">
  <div class="w-48 h-[2px] bg-[#D48255] mb-12 hidden md:block"></div>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-12 text-center">
    <div class="space-y-4">
      <h3 class="font-serif text-3xl text-gray-800">
        <span class="italic text-[#D48255]">Luxury</span> Rooms.
      </h3>
      <p class="text-gray-500 text-sm leading-relaxed">
        We believe in functioning in harmony with nature. When that's achieved, everything comes to its place – this is the least you'll experience by staying here.
      </p>
    </div>

    <div class="space-y-4">
      <h3 class="font-serif text-3xl text-gray-800">
        <span class="italic text-[#D48255]">Best</span> Food.
      </h3>
      <p class="text-gray-500 text-sm leading-relaxed">
        Everything needed for creating special and unique experience is available here. Accept peace of mind and body rejuvenation directly from nature.
      </p>
    </div>

    <div class="space-y-4">
      <h3 class="font-serif text-3xl text-gray-800">
        <span class="italic text-[#D48255]">Quality</span> Service.
      </h3>
      <p class="text-gray-500 text-sm leading-relaxed">
        One of the basic philosophy for any kind of holiday and pleasure is quality – every thing must be set for your comfort, satisfaction and memorable experience.
      </p>
    </div>
  </div>

  <div class="flex justify-end mt-12">
    <div class="w-48 h-[2px] bg-[#D48255] hidden md:block"></div>
  </div>
</section>
<!--entertainment section-->
<section class="relative w-full min-h-[500px] flex items-center justify-center overflow-hidden font-sans">
  
  <div class="absolute inset-0 z-0">
    <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1505373877841-8d25f7d46678?q=80&w=2012&auto=format&fit=crop')] bg-cover bg-center"></div>
    <div class="absolute inset-0 bg-pink-900/20 backdrop-brightness-75"></div>
  </div>

  <div class="relative z-10 w-full max-w-6xl px-6 flex flex-col md:flex-row items-stretch">
    
    <div class="md:w-5/12 p-8 md:p-16 flex flex-col justify-center items-center md:items-start text-center md:text-left">
      <h2 class="font-serif text-4xl md:text-5xl text-white italic mb-8 drop-shadow-md">
        Entertainment <span class="not-italic">&</span> Events
      </h2>
      
      <div class="group">
        <a href="event-and-conference-halls.php" class="flex items-center gap-3 text-white text-xs font-bold uppercase tracking-[0.2em] mb-2 hover:text-[#D48255] transition-colors">
          Upcoming Events
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
          </svg>
        </a>
        <div class="w-full h-px bg-white"></div>
      </div>
    </div>

    <div class="md:w-7/12 bg-black/80 backdrop-blur-sm p-10 md:p-16 text-white relative border-t md:border-t-0 md:border-l border-white/10">
      
      <div class="absolute top-10 right-0 w-32 h-px bg-[#D48255] hidden md:block"></div>

      <div class="space-y-8">
        <p class="text-sm md:text-base leading-relaxed font-light">
          We specialize in arranging business-events. We can help you to dive into your work without a care. Your meeting should be successful, no matter what you aim for.
        </p>

        <p class="text-sm md:text-base leading-relaxed font-light">
          Our big conference <span class="font-semibold text-[#D48255]">Avilla Hall</span> is outfitted with state-of-the-art technical facilities. It is perfect for top level negotiations or board of directors meetings.
        </p>
      </div>
    </div>

  </div>
</section>
<!--gym section-->
<section class="relative w-full min-h-[550px] flex items-center justify-center overflow-hidden font-sans">
  
  <div class="absolute inset-0 z-0">
    <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?q=80&w=2070&auto=format&fit=crop')] bg-cover bg-center"></div>
    <div class="absolute inset-0 bg-black/30 backdrop-brightness-90"></div>
  </div>

  <div class="relative z-10 w-full max-w-6xl px-6 flex flex-col md:flex-row-reverse items-stretch">
    
    <div class="md:w-5/12 p-8 md:p-16 flex flex-col justify-center items-center md:items-end text-center md:text-right">
      <h2 class="font-serif text-3xl md:text-4xl text-white italic mb-8 drop-shadow-md">
        Spa / Wellness Center
      </h2>
      
      <a href="event-and-conference-halls.php" class="group flex items-center gap-3 border-2 border-white rounded-full px-8 py-3 text-[10px] md:text-xs font-bold uppercase tracking-[0.2em] text-white hover:bg-white hover:text-black transition-all">
        See Our Wellness Center
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7-7" />
        </svg>
      </a>
    </div>

    <div class="md:w-7/12 bg-black/85 backdrop-blur-sm p-10 md:p-16 text-white relative border-b md:border-b-0 md:border-r border-white/10">
      
      <div class="absolute top-12 left-0 w-32 h-px bg-[#D48255] hidden md:block"></div>

      <div class="space-y-6">
        <p class="text-xs md:text-sm leading-relaxed font-light">
          We have a sizable clean state of the earth swimming pool with a life guide and recreation centre having three table tennis boards, three snooker boards free music, a bar for both drinks and snacks. Our gym has the state of the earth equipment for body building and body fitness, we also have a sooner room in the gym for body fitness.
        </p>

        <p class="text-xs md:text-sm leading-relaxed font-light">
          Our ultramodern saloon has the latest equipment's for barbing and hear dressing, we also have a spar in the saloon and highly strained professionals that would give you any type of hear do you desire, we also offer pedicure and manicure services.
        </p>
      </div>
    </div>

  </div>
</section>
<!--celebration section-->
<section class="relative w-full min-h-[500px] flex items-center justify-center overflow-hidden font-sans">
  
  <div class="absolute inset-0 z-0">
    <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1519225421980-715cb0215aed?q=80&w=2070&auto=format&fit=crop')] bg-cover bg-center"></div>
    <div class="absolute inset-0 bg-black/20 backdrop-brightness-90"></div>
  </div>

  <div class="relative z-10 w-full max-w-6xl px-6 flex flex-col md:flex-row items-stretch">
    
    <div class="md:w-5/12 p-8 md:p-16 flex flex-col justify-center items-center md:items-start text-center md:text-left">
      <h2 class="font-serif text-4xl md:text-5xl text-white italic mb-8 drop-shadow-md">
        Weddings / <span class="not-italic">Celebrations</span>
      </h2>
      
      <a href="#" class="group flex items-center gap-3 border-2 border-white rounded-full px-10 py-3 text-[10px] md:text-xs font-bold uppercase tracking-[0.2em] text-white hover:bg-white hover:text-black transition-all">
        See The Venues
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7-7" />
        </svg>
      </a>
    </div>

    <div class="md:w-7/12 bg-black/80 backdrop-blur-sm p-10 md:p-16 text-white relative border-t md:border-t-0 md:border-l border-white/10">
      
      <div class="absolute top-12 right-0 w-32 h-px bg-[#D48255] hidden md:block"></div>

      <div class="space-y-8">
        <p class="text-sm md:text-base leading-relaxed font-light">
          On this most special of all days, may you be blessed in countless ways – our wedding organisation will help you to make your wedding being beautiful and stress free. With faith and love and lots of hope, and all the things which help you cope!
        </p>

        <p class="text-sm md:text-base leading-relaxed font-light">
          Our magnificent venue is also very much fit for hosting any kind of celebrations and events.
        </p>
      </div>
    </div>

  </div>
</section>
<!--footer section-->
<script>
// Menu Modal JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const menuModalBtn = document.getElementById('menuModalBtn');
    const menuModal = document.getElementById('menuModal');
    const closeMenuModal = document.getElementById('closeMenuModal');
    const menuModalContent = document.getElementById('menuModalContent');

    // Open modal
    menuModalBtn.addEventListener('click', function() {
        menuModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
    });

    // Close modal functions
    function closeModal() {
        menuModal.classList.add('hidden');
        document.body.style.overflow = 'auto'; // Restore scrolling
    }

    // Close button click
    closeMenuModal.addEventListener('click', closeModal);

    // Click outside modal content to close
    menuModal.addEventListener('click', function(e) {
        if (e.target === menuModal) {
            closeModal();
        }
    });

    // ESC key to close
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !menuModal.classList.contains('hidden')) {
            closeModal();
        }
    });
});
</script>
<?php include 'footer-one.php';?>