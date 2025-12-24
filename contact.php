<?php
require_once 'config/database.php';
include 'header.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $subject = sanitize($_POST['subject']);
    $messageContent = sanitize($_POST['message']);

    // Basic validation
    if (empty($name) || empty($email) || empty($subject) || empty($messageContent)) {
        $message = 'All fields are required.';
        $messageType = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Invalid email format.';
        $messageType = 'error';
    } else {
        // In a real application, you would send an email or save to a database here.
        // For demonstration, we'll just show a success message.
        $message = 'Thank you for your message! We will get back to you soon.';
        $messageType = 'success';

        // Example: To send an email (requires mail server configuration)
        
        $to = "contact@avillaokada.com"; // Replace with your actual email
        $headers = "From: " . $email . "\r\n";
        $headers .= "Reply-To: " . $email . "\r\n";
        $emailBody = "Name: $name\nEmail: $email\nSubject: $subject\nMessage:\n$messageContent";

        if (mail($to, $subject, $emailBody, $headers)) {
            $message = 'Thank you for your message! We will get back to you soon.';
            $messageType = 'success';
        } else {
            $message = 'Failed to send your message. Please try again later.';
            $messageType = 'error';
        }
        
    }
}
?>

<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4 lg:px-8">
        <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-xl overflow-hidden md:flex">
            <!-- Left Side - Contact Info -->
            <div class="md:w-1/3 bg-teal-600 text-white p-8 space-y-6 flex flex-col justify-between">
                <div>
                    <h2 class="text-2xl font-bold mb-4">Contact Information</h2>
                    <p class="text-teal-100">Feel free to reach out to us with any questions or concerns.</p>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center space-x-3">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.828 0L6.343 16.657a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>123 Hotel St, City, Country</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <span>+1 234 567 8900</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span>info@avillaokada.com</span>
                    </div>
                    <div class="flex items-center space-x-3 bg-green-500 p-3 rounded-lg text-white">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2C6.477 2 2 6.477 2 12c0 3.189 1.385 6.068 3.582 8.01L4 22l2.001-.524A9.95 9.95 0 0012 22c5.523 0 10-4.477 10-10S17.523 2 12 2z"/>
                            <path fill="#fff" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.828 0L6.343 16.657a8 8 0 1111.314 0z"/>
                            <path fill="#fff" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <a href="https://wa.me/2347071984117" target="_blank" class="text-teal-100 hover:text-white">WhatsApp Us</a>
                    </div>
                </div>
            </div>

            <!-- Right Side - Contact Form -->
            <div class="md:w-2/3 p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Send Us a Message</h2>

                <?php if ($message): ?>
                    <div class="mb-6 p-4 rounded-lg <?php echo $messageType === 'error' ? 'bg-red-100 text-red-800 border border-red-200' : 'bg-green-100 text-green-800 border border-green-200'; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <form action="contact.php" method="POST" class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Your Name</label>
                        <input type="text" id="name" name="name" required
                               class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Your Email</label>
                        <input type="email" id="email" name="email" required
                               class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                        <input type="text" id="subject" name="subject" required
                               class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700">Your Message</label>
                        <textarea id="message" name="message" rows="5" required
                                  class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500"></textarea>
                    </div>
                    <div class="text-right">
                        <button type="submit"
                                class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                            Send Message
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
