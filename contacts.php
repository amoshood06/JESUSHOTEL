<?php 
// Start session first
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/database.php';

$emailSent = false;
$emailError = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Validate form inputs
    if (!empty($firstName) && !empty($lastName) && !empty($email) && !empty($message)) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            try {
                // Save contact message to database
                $stmt = $pdo->prepare("INSERT INTO contact_messages (first_name, last_name, email, message, created_at) VALUES (?, ?, ?, ?, NOW())");
                $stmt->execute([$firstName, $lastName, $email, $message]);
                
                $_SESSION['emailSent'] = true;
                
                // Send confirmation email to user (if mail is available)
                $userSubject = "We received your message - Avilla Okada Hotel";
                $userBody = "Dear $firstName,\n\n";
                $userBody .= "Thank you for contacting Avilla Okada Hotel. We have received your message and will get back to you soon.\n\n";
                $userBody .= "Best regards,\nAvilla Okada Hotel Team";
                $userHeaders = "From: avillaokadahotel@gmail.com\r\n";
                $userHeaders .= "Content-Type: text/plain; charset=UTF-8\r\n";
                
                @mail($email, $userSubject, $userBody, $userHeaders);
                
            } catch (Exception $e) {
                $_SESSION['emailError'] = true;
            }
            
            // Redirect after processing
            header('Location: contacts.php#contact-form');
            exit();
        } else {
            $_SESSION['emailError'] = true;
            header('Location: contacts.php#contact-form');
            exit();
        }
    } else {
        $_SESSION['emailError'] = true;
        header('Location: contacts.php#contact-form');
        exit();
    }
}

// Check for session messages
$emailSent = isset($_SESSION['emailSent']) && $_SESSION['emailSent'];
$emailError = isset($_SESSION['emailError']) && $_SESSION['emailError'];

// Clear session messages
if ($emailSent || $emailError) {
    unset($_SESSION['emailSent']);
    unset($_SESSION['emailError']);
}

include 'header-one.php';
?>
<!-- Contact Us Section -->
<section class="w-full bg-white py-16 px-6 text-center font-sans">
  <div class="max-w-4xl mx-auto">
    <h1 class="font-serif text-5xl md:text-6xl text-gray-700 italic mb-2">Contact Us</h1>
    <p class="text-[10px] uppercase tracking-[0.5em] text-gray-400 mb-4 font-bold">AVILLA OKADA HOTEL</p>
    <div class="w-48 h-[2px] bg-[#D48255] mx-auto mb-8"></div>
    <p class="font-serif text-xl md:text-2xl text-gray-800 italic">Come Let's Give You A Royal Treat In Nature's Paradise!</p>
  </div>
</section>

<section class="relative w-full h-[350px] flex items-center justify-center overflow-hidden">
  <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?q=80&w=2070&auto=format&fit=crop" 
       class="absolute inset-0 w-full h-full object-cover" alt="Hotel Interior">
  <div class="absolute inset-0 bg-black/40"></div>
  
  <div class="relative z-10 bg-[#222222]/90 p-10 md:p-16 text-center max-w-3xl border border-white/10">
    <h2 class="font-serif text-2xl md:text-3xl text-white italic mb-2">Inner peace is achieved with balance.</h2>
    <p class="text-[10px] uppercase tracking-[0.3em] text-gray-300 mb-8">- THIS IS HOW IT FEEL WHEN STAYING HERE -</p>
    <button onclick="document.getElementById('contact-form').scrollIntoView({ behavior: 'smooth' })" class="border border-white rounded-full px-8 py-2 text-[10px] font-bold text-white uppercase tracking-widest hover:bg-white hover:text-black transition-all">
      EMAIL US <i class="ml-2 far fa-envelope"></i>
    </button>
  </div>
</section>

<section class="w-full bg-[#3d231a] py-20 px-6 font-sans">
  <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-16 text-white">
    
    <div>
      <p class="text-[9px] uppercase tracking-[0.3em] text-[#D48255] font-bold mb-4">WE'D LOVE TO HEAR FROM YOU</p>
      <h3 class="text-3xl font-bold mb-6">Contact Us</h3>
      <p class="text-sm leading-relaxed text-gray-300 mb-8 italic">
        For Centuries, Benin City, a historical town has been the cradle of African arts and culture, churning out works of art which belie the imagination. We will like you to visit us.
      </p>
      <div class="w-12 h-[2px] bg-[#D48255] mb-8"></div>
      
      <div class="space-y-6 text-xs font-bold tracking-wider">
        <p class="flex items-start gap-4">
          <span class="text-[#D48255]">●</span>
          Okada Town, Edo State, Nigeria.
        </p>
        <p class="flex items-center gap-4">
          <span class="text-[#D48255]">●</span>
          (+234) <span class="text-cyan-400">707 198 4117</span>
        </p>
        <p class="flex items-center gap-4">
          <span class="text-[#D48255]">●</span>
          avillaokadahotel@gmail.com
        </p>
      </div>
    </div>

    <div class="bg-transparent" id="contact-form">
      <h4 class="text-xs uppercase tracking-widest font-bold mb-2">Send Us A Message</h4>
      <div class="w-10 h-[2px] bg-[#D48255] mb-8"></div>
      
      <form class="space-y-6" method="POST">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-[10px] uppercase font-bold text-gray-400 mb-2">Name <span class="text-red-500">*</span></label>
            <input type="text" name="first_name" required class="w-full bg-white p-3 text-black focus:outline-none">
            <span class="text-[9px] text-gray-500">First</span>
          </div>
          <div class="pt-6 sm:pt-0">
            <label class="hidden sm:block text-[10px] uppercase font-bold text-gray-400 mb-2">&nbsp;</label>
            <input type="text" name="last_name" required class="w-full bg-white p-3 text-black focus:outline-none">
            <span class="text-[9px] text-gray-500">Last</span>
          </div>
        </div>
        
        <div>
          <label class="block text-[10px] uppercase font-bold text-gray-400 mb-2">Email <span class="text-red-500">*</span></label>
          <input type="email" name="email" required class="w-full bg-white p-3 text-black focus:outline-none">
        </div>
        
        <div>
          <label class="block text-[10px] uppercase font-bold text-gray-400 mb-2">Comment or Message <span class="text-red-500">*</span></label>
          <textarea rows="5" name="message" required class="w-full bg-white p-3 text-black focus:outline-none"></textarea>
        </div>
        
        <button type="submit" class="bg-[#D48255] text-white px-10 py-3 text-[10px] font-bold uppercase tracking-widest hover:bg-orange-600 transition-colors">
          SUBMIT
        </button>
      </form>
    </div>
  </div>
</section>
<!-- Toast Notification -->
<div id="toast" class="fixed top-4 right-4 z-[10000] px-6 py-3 bg-green-500 text-white rounded-lg shadow-lg opacity-0 transition-opacity duration-500 pointer-events-none">
  Email sent successfully!
</div>

<script>
function showToast(message = 'Email sent successfully!', isError = false) {
  const toast = document.getElementById('toast');
  toast.textContent = message;
  
  if (isError) {
    toast.classList.remove('bg-green-500');
    toast.classList.add('bg-red-500');
  } else {
    toast.classList.remove('bg-red-500');
    toast.classList.add('bg-green-500');
  }
  
  // Show toast
  setTimeout(() => {
    toast.classList.remove('opacity-0');
    toast.classList.add('opacity-100');
  }, 100);
  
  // Hide toast after 3 seconds
  setTimeout(() => {
    toast.classList.remove('opacity-100');
    toast.classList.add('opacity-0');
  }, 3100);
}

// Check if email was sent
<?php if ($emailSent): ?>
showToast('Email sent successfully!', false);
<?php elseif ($emailError): ?>
showToast('Error sending email. Please try again.', true);
<?php endif; ?>
</script>

<?php include 'footer-one.php'; ?>