<?php include "header-one.php"; ?>
<section class="w-full bg-white py-16 px-6 text-center font-sans">
  <div class="max-w-4xl mx-auto">
    <h1 class="font-serif text-5xl md:text-6xl text-gray-700 italic mb-2">Book Your Stay</h1>
    <p class="text-[10px] uppercase tracking-[0.5em] text-gray-400 mb-4 font-bold">PRECIOUS PALM ROYAL HOTEL</p>
    <div class="w-48 h-[2px] bg-[#D48255] mx-auto mb-8"></div>
    <p class="font-serif text-xl md:text-2xl text-gray-800 italic">Experience Nature's Paradise at its Finest</p>
  </div>
</section>

<section class="relative w-full bg-[#f9f9f9] py-20 px-6 font-sans">
  <div class="max-w-6xl mx-auto flex flex-col lg:flex-row gap-12">
    
    <div class="w-full lg:w-2/3 bg-white p-8 md:p-12 shadow-xl border-t-4 border-[#D48255]">
      <h2 class="text-2xl font-bold text-gray-800 mb-8 uppercase tracking-widest border-b pb-4">Reservation Details</h2>
      
      <form class="space-y-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-[10px] uppercase font-bold text-gray-500 mb-2">Check-In Date <span class="text-red-500">*</span></label>
            <input type="date" class="w-full border border-gray-200 p-3 focus:border-[#D48255] outline-none transition-colors">
          </div>
          <div>
            <label class="block text-[10px] uppercase font-bold text-gray-500 mb-2">Check-Out Date <span class="text-red-500">*</span></label>
            <input type="date" class="w-full border border-gray-200 p-3 focus:border-[#D48255] outline-none transition-colors">
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="md:col-span-1">
            <label class="block text-[10px] uppercase font-bold text-gray-500 mb-2">Room Type</label>
            <select class="w-full border border-gray-200 p-3 focus:border-[#D48255] outline-none appearance-none bg-white">
              <option>Standard Room</option>
              <option>Deluxe Room</option>
              <option>Executive Room</option>
            </select>
          </div>
          <div>
            <label class="block text-[10px] uppercase font-bold text-gray-500 mb-2">Adults</label>
            <select class="w-full border border-gray-200 p-3 focus:border-[#D48255] outline-none bg-white">
              <option>1 Adult</option>
              <option selected>2 Adults</option>
              <option>3 Adults</option>
            </select>
          </div>
          <div>
            <label class="block text-[10px] uppercase font-bold text-gray-500 mb-2">Children</label>
            <select class="w-full border border-gray-200 p-3 focus:border-[#D48255] outline-none bg-white">
              <option>0 Children</option>
              <option>1 Child</option>
              <option>2 Children</option>
            </select>
          </div>
        </div>

        <div class="pt-6 border-t">
          <h3 class="text-xs uppercase font-bold tracking-widest text-gray-400 mb-6">Guest Information</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <input type="text" placeholder="Full Name *" class="w-full border border-gray-200 p-3 focus:border-[#D48255] outline-none">
            <input type="email" placeholder="Email Address *" class="w-full border border-gray-200 p-3 focus:border-[#D48255] outline-none">
            <input type="tel" placeholder="Phone Number *" class="w-full border border-gray-200 p-3 focus:border-[#D48255] outline-none">
            <input type="text" placeholder="Special Requests" class="w-full border border-gray-200 p-3 focus:border-[#D48255] outline-none">
          </div>
        </div>

        <button type="submit" class="w-full bg-[#D48255] text-white py-4 font-bold uppercase tracking-[0.2em] hover:bg-[#b36d46] transition-all shadow-lg">
          Check Availability & Book
        </button>
      </form>
    </div>

    <div class="w-full lg:w-1/3 space-y-8">
      <div class="bg-[#333333] p-8 text-white">
        <h3 class="font-serif text-2xl italic mb-6">Why Book Directly?</h3>
        <ul class="text-xs space-y-4 font-bold tracking-wider">
          <li class="flex items-center gap-3"><span class="text-[#D48255]">✓</span> BEST RATE GUARANTEED</li>
          <li class="flex items-center gap-3"><span class="text-[#D48255]">✓</span> NO HIDDEN BOOKING FEES</li>
          <li class="flex items-center gap-3"><span class="text-[#D48255]">✓</span> FREE HI-SPEED WIFI</li>
          <li class="flex items-center gap-3"><span class="text-[#D48255]">✓</span> INDIVIDUAL CLIMATE CONTROL</li>
        </ul>
      </div>

      <div class="bg-white p-8 border border-gray-200 shadow-sm text-center">
        <div class="text-[#D48255] mb-4">
          <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
        </div>
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Need Help Booking?</p>
        <p class="text-xl font-bold text-gray-800">(+234) 707 198 4117</p>
      </div>
    </div>

  </div>
</section>





<?php include 'footer-one.php'; ?>