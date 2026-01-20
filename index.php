<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Avilla Okada Hotel</title>
</head>
<body>
    <!--headers-->
 <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<header class="sticky top-0 z-50 w-full font-sans shadow-md" x-data="{ mobileMenuOpen: false }">
  
  <div class="bg-[#E6B49A] text-[#1E293B] py-2 px-4 sm:px-6 flex flex-col sm:flex-row justify-between items-center text-[10px] sm:text-xs md:text-sm gap-2">
    <div class="flex items-center gap-2 text-center sm:text-left">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
      </svg>
      <span>Okada Town, Edo State, Nigeria.</span>
    </div>
    
    <div class="flex items-center gap-3">
      <a href="#" class="bg-[#4267B2] text-white p-1 rounded-full hover:opacity-80"><svg class="w-3 h-3 fill-current" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg></a>
      <a href="#" class="bg-[#262626] text-white p-1 rounded-full hover:opacity-80"><svg class="w-3 h-3 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 1.366.062 2.633.332 3.608 1.308.975.975 1.245 2.242 1.308 3.608.058 1.266.07 1.646.07 4.85s-.012 3.584-.07 4.85c-.063 1.366-.333 2.633-1.308 3.608-.975.975-2.242 1.245-3.608 1.308-1.266.058-1.646.07-4.85.07s-3.584-.012-4.85-.07c-1.366-.063-2.633-.333-3.608-1.308-.975-.975-1.245-2.242-1.308-3.608-.058-1.266-.07-1.646-.07-4.85s.012-3.584.07-4.85c.062-1.366.332-2.633 1.308-3.608.975-.975 2.242-1.245 3.608-1.308 1.266-.058 1.646-.07 4.85-.07zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948s.014 3.667.072 4.947c.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072s3.667-.014 4.947-.072c4.358-.2 6.78-2.618 6.98-6.98.058-1.281.072-1.689.072-4.948s-.014-3.667-.072-4.947c-.2-4.358-2.618-6.78-6.98-6.98-1.28-.058-1.689-.072-4.948-.072zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.162 6.162 6.162 6.162-2.759 6.162-6.162-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.791-4-4s1.791-4 4-4 4 1.791 4 4-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg></a>
    </div>
  </div>

  <div class="hidden md:flex bg-[#D1E9DB] text-[#2C4E5E] py-3 px-6 justify-between items-center text-sm border-b border-gray-100">
    <div class="flex gap-8">
      <span>(+234) 707 198 4117</span>
      <a href="mailto:avillaokadahotel@gmail.com" class="hover:underline">avillaokadahotel@gmail.com</a>
    </div>
    <a href="#" class="hover:underline font-medium">Download our COVID 19 Guide Line</a>
  </div>

  <nav class="bg-white py-4 px-4 sm:px-6 flex justify-between items-center">
    <button @click="mobileMenuOpen = true" class="lg:hidden text-gray-600 p-2">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" /></svg>
    </button>

    <div class="flex items-center">
      <img src="asset/image/mylogo.jpg" alt="Logo" class="h-12 md:h-16">
    </div>

    <div class="hidden lg:flex items-center gap-8 font-bold text-[#4A4A4A] text-sm tracking-wide">
      <a href="#" class="hover:text-[#D48255]">HOME</a>
      <a href="#" class="text-[#D48255]">ABOUT US</a>
      <div class="group relative py-2 cursor-pointer">
        <div class="flex items-center gap-1 hover:text-[#D48255]">
          <span>ROOMS AND SUITES</span>
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" /></svg>
        </div>
        <div class="absolute top-full left-0 w-48 bg-white shadow-xl hidden group-hover:block border-t-2 border-[#D48255]">
          <a href="#" class="block px-4 py-3 hover:bg-gray-50 text-xs">Standard Room</a>
          <a href="#" class="block px-4 py-3 hover:bg-gray-50 text-xs">Deluxe Room</a>
          <a href="#" class="block px-4 py-3 hover:bg-gray-50 text-xs">Executive Suite</a>
        </div>
      </div>
      <div class="group relative py-2 cursor-pointer">
        <div class="flex items-center gap-1 hover:text-[#D48255]">
          <span>Food</span>
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" /></svg>
        </div>
        <div class="absolute top-full left-0 w-48 bg-white shadow-xl hidden group-hover:block border-t-2 border-[#D48255]">
          <a href="#" class="block px-4 py-3 hover:bg-gray-50 text-xs">Standard Room</a>
          <a href="#" class="block px-4 py-3 hover:bg-gray-50 text-xs">Deluxe Room</a>
          <a href="#" class="block px-4 py-3 hover:bg-gray-50 text-xs">Executive Suite</a>
        </div>
      </div>
      <div class="group relative py-2 cursor-pointer">
        <div class="flex items-center gap-1 hover:text-[#D48255]">
          <span>Drinks</span>
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" /></svg>
        </div>
        <div class="absolute top-full left-0 w-48 bg-white shadow-xl hidden group-hover:block border-t-2 border-[#D48255]">
          <a href="#" class="block px-4 py-3 hover:bg-gray-50 text-xs">Standard Room</a>
          <a href="#" class="block px-4 py-3 hover:bg-gray-50 text-xs">Deluxe Room</a>
          <a href="#" class="block px-4 py-3 hover:bg-gray-50 text-xs">Executive Suite</a>
        </div>
      </div>
      <a href="#" class="hover:text-[#D48255]">CONTACT US</a>
    </div>

    <div class="flex items-center gap-2 sm:gap-6">
      <button class="hidden sm:block text-gray-400 border p-2 rounded">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
      </button>
      <button class="bg-[#D48255] text-white px-4 sm:px-8 py-2 sm:py-3 rounded-full text-xs sm:text-sm font-semibold shadow-lg">
        Book Online
      </button>
    </div>
  </nav>

  <div x-show="mobileMenuOpen" 
       class="fixed inset-0 z-[60] lg:hidden" 
       x-transition:enter="transition ease-out duration-300" 
       x-transition:enter-start="opacity-0" 
       x-transition:enter-end="opacity-100">
    
    <div class="fixed inset-0 bg-black/50" @click="mobileMenuOpen = false"></div>

    <div class="fixed inset-y-0 left-0 w-3/4 max-w-xs bg-white shadow-xl p-6"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0">
      
      <div class="flex justify-between items-center mb-8">
        <span class="font-bold text-lg text-[#D48255]">MENU</span>
        <button @click="mobileMenuOpen = false" class="text-gray-500">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
      </div>

      <div class="flex flex-col gap-6 font-bold text-gray-700">
        <a href="#" class="hover:text-[#D48255] border-b pb-2">HOME</a>
        <a href="#" class="hover:text-[#D48255] border-b pb-2">ABOUT US</a>
        <a href="#" class="hover:text-[#D48255] border-b pb-2">ROOMS AND SUITES</a>
        <a href="#" class="hover:text-[#D48255] border-b pb-2">OUR SERVICE</a>
        <a href="#" class="hover:text-[#D48255] border-b pb-2">CONTACT US</a>
      </div>

      <div class="mt-10 text-xs text-gray-500 flex flex-col gap-2">
        <span>(+234) 707 198 4117</span>
        <span>avillaokadahotel@gmail.com</span>
      </div>
    </div>
  </div>
</header>

<div class="h-screen bg-gray-50">
  <!--herosection-->
  <section class="relative min-h-screen w-full flex items-center justify-center overflow-hidden font-sans">
  
  <div class="absolute inset-0 z-0">
    <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&q=80&w=2070')] bg-cover bg-center bg-no-repeat"></div>
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
      <a href="#" class="group w-full sm:w-auto flex items-center justify-center gap-2 border-2 border-white rounded-full px-8 py-3.5 text-xs md:text-sm font-bold uppercase tracking-widest transition-all hover:bg-white hover:text-black">
        Book A Room
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
        </svg>
      </a>

      <a href="#" class="group w-full sm:w-auto flex items-center justify-center gap-2 border-2 border-white rounded-full px-8 py-3.5 text-xs md:text-sm font-bold uppercase tracking-widest transition-all hover:bg-white hover:text-black">
        More About Us
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:translate-y-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
      </a>
    </div>

    <div class="h-16 w-px bg-white/60 mb-8"></div>

    <div class="max-w-2xl px-4">
      <p class="text-xs sm:text-sm md:text-base italic leading-relaxed text-gray-200">
        <span class="font-bold underline decoration-1 underline-offset-4">Precious palm royal hotel</span> is part of a collection of the finest independent luxury hotels in the Edo region. Learn more about our offering!
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
        <img src="https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&q=80" alt="Suite De Lodge" class="w-full h-80 object-cover transition-all duration-500 group-hover:scale-110">
        <div class="absolute inset-0 bg-black/20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
          <button class="bg-[#D48255] text-white px-6 py-2 rounded-full text-xs font-bold uppercase tracking-widest hover:bg-white hover:text-[#D48255] transition-colors">View Details</button>
        </div>
      </div>

      <div class="flex flex-col gap-4">
        <div class="relative group overflow-hidden shadow-lg">
          <div class="absolute top-0 left-0 right-0 bg-black/40 text-white py-2 px-4 text-center italic font-serif z-10">
            Deluxe Room
          </div>
          <img src="https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&q=80" alt="Standard Room" class="w-full h-[380px] object-cover transition-all duration-500 group-hover:scale-110">
          <div class="absolute inset-0 bg-black/20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
            <button class="bg-[#D48255] text-white px-6 py-2 rounded-full text-xs font-bold uppercase tracking-widest hover:bg-white hover:text-[#D48255] transition-colors">View Details</button>
          </div>
        </div>
      </div>

      <div class="relative group overflow-hidden md:mb-24 shadow-lg">
        <div class="absolute top-0 left-0 right-0 bg-black/40 text-white py-2 px-4 text-center italic font-serif z-10">
          Executive Suite
        </div>
        <img src="https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&q=80" alt="Ambassador Suite" class="w-full h-80 object-cover transition-all duration-500 group-hover:scale-110">
        <div class="absolute inset-0 bg-black/20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
          <button class="bg-[#D48255] text-white px-6 py-2 rounded-full text-xs font-bold uppercase tracking-widest hover:bg-white hover:text-[#D48255] transition-colors">View Details</button>
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
          <h3 class="font-serif text-5xl text-[#D48255] italic">Kanel</h3>
          <p class="text-xs uppercase tracking-[0.3em] text-gray-500 font-bold">Bar</p>
        </div>

        <div class="mb-10">
          <h3 class="font-serif text-5xl text-[#D48255] italic">Papaya</h3>
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
        
        <button class="group flex items-center gap-3 border-2 border-white rounded-full px-10 py-3 text-sm font-bold uppercase tracking-widest hover:bg-white hover:text-black transition-all">
          Our Menu
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
          </svg>
        </button>
      </div>

    </div>
  </div>
</section>

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
        <a href="#" class="flex items-center gap-3 text-white text-xs font-bold uppercase tracking-[0.2em] mb-2 hover:text-[#D48255] transition-colors">
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
          Our big conference <span class="font-semibold text-[#D48255]">Stonehenge Hall</span> is outfitted with state-of-the-art technical facilities. It is perfect for top level negotiations or board of directors meetings.
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
      
      <a href="#" class="group flex items-center gap-3 border-2 border-white rounded-full px-8 py-3 text-[10px] md:text-xs font-bold uppercase tracking-[0.2em] text-white hover:bg-white hover:text-black transition-all">
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
<footer class="bg-black text-white pt-20 pb-10 px-6 sm:px-12 lg:px-24 font-sans">
  <div class="max-w-7xl mx-auto">
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
      
      <div class="space-y-6">
        <h4 class="text-lg font-bold uppercase tracking-widest">Our Location</h4>
        <div class="space-y-4 text-sm text-gray-400">
          <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-[#D48255] mt-1 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
            <p>Okada Town, Edo State, Nigeria.</p>
          </div>
          <div class="flex items-center gap-3">
            <svg class="w-5 h-5 text-[#D48255] shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
            <p>(+234) 707 198 4117</p>
          </div>
          <div class="flex items-center gap-3">
            <svg class="w-5 h-5 text-[#D48255] shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
            <p>avillaokadahotel@gmail.com</p>
          </div>
        </div>

        <div class="pt-4">
           <audio controls class="w-full h-10 rounded-full opacity-80 scale-90 origin-left">
            <source src="#" type="audio/mpeg">
          </audio>
        </div>
      </div>

      <div class="space-y-6">
        <h4 class="text-lg font-bold uppercase tracking-widest">Links</h4>
        <ul class="space-y-4 text-sm text-gray-400">
          <li><a href="#" class="hover:text-[#D48255] transition-colors flex items-center gap-2"><span class="text-[#D48255]">&rsaquo;</span> HOME</a></li>
          <li><a href="#" class="hover:text-[#D48255] transition-colors flex items-center gap-2"><span class="text-[#D48255]">&rsaquo;</span> ABOUT US</a></li>
          <li><a href="#" class="hover:text-[#D48255] transition-colors flex items-center gap-2"><span class="text-[#D48255]">&rsaquo;</span> POOL AND WELLNESS CENTER</a></li>
          <li><a href="#" class="hover:text-[#D48255] transition-colors flex items-center gap-2"><span class="text-[#D48255]">&rsaquo;</span> CONTACT US</a></li>
        </ul>
      </div>

      <div class="space-y-6">
        <h4 class="text-lg font-bold uppercase tracking-widest">Rooms and Suites</h4>
        <ul class="space-y-4 text-sm text-gray-400">
          <li><a href="#" class="hover:text-[#D48255] transition-colors">Standard Rooms</a></li>
          <li><a href="#" class="hover:text-[#D48255] transition-colors">Suite De Lodge Rooms</a></li>
          <li><a href="#" class="hover:text-[#D48255] transition-colors">Executive Double Rooms</a></li>
          <li><a href="#" class="hover:text-[#D48255] transition-colors">Diplomatic Suit</a></li>
          <li><a href="#" class="hover:text-[#D48255] transition-colors">Ambassodrial Suit</a></li>
        </ul>
      </div>

      <div class="space-y-6">
        <h4 class="text-lg font-bold uppercase tracking-widest">Thumb Gallery</h4>
        <div class="grid grid-cols-3 gap-2">
          <img src="asset/gallary/IMG-20251129-WA0022.jpg" alt="Gallery 1" class="w-full h-20 object-cover rounded shadow-sm hover:opacity-75 transition-opacity">
          <img src="https://via.placeholder.com/80" alt="Gallery 2" class="w-full h-20 object-cover rounded shadow-sm hover:opacity-75 transition-opacity">
          <img src="https://via.placeholder.com/80" alt="Gallery 3" class="w-full h-20 object-cover rounded shadow-sm hover:opacity-75 transition-opacity">
          <img src="https://via.placeholder.com/80" alt="Gallery 4" class="w-full h-20 object-cover rounded shadow-sm hover:opacity-75 transition-opacity">
          <img src="https://via.placeholder.com/80" alt="Gallery 5" class="w-full h-20 object-cover rounded shadow-sm hover:opacity-75 transition-opacity">
          <img src="https://via.placeholder.com/80" alt="Gallery 6" class="w-full h-20 object-cover rounded shadow-sm hover:opacity-75 transition-opacity">
        </div>
      </div>

    </div>

    <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-6">
      <div class="flex items-center gap-4">
        <img src="asset/image/mylogo.jpg" alt="Hotel Logo" class="h-12 w-auto">
        <div class="h-10 w-px bg-gray-700 hidden md:block"></div>
        <p class="text-xs text-gray-400">
          Copy Right &copy; 2025, Avilla Okada Hotel. All rights reserved.
        </p>
      </div>

      <div class="flex gap-6 text-gray-400">
        <a href="https://web.facebook.com/people/Avilla-Okada/61585139773672/?_rdc=1&_rdr#" class="hover:text-white transition-colors">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
        </a>
        <a href="https://web.facebook.com/people/Avilla-Okada/61585139773672/?_rdc=1&_rdr#" class="hover:text-white transition-colors">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.761 0 5-2.239 5-5v-14c0-2.761-2.239-5-5-5zm-3 7h-1.924c-.615 0-1.076.252-1.076.889v1.111h3l-.238 3h-2.762v8h-3v-8h-2v-3h2v-1.923c0-2.022 1.064-3.077 3.461-3.077h2.539v3z"/></svg>
        </a>
      </div>
    </div>
  </div>
</footer>

</div>

</body>
</html>