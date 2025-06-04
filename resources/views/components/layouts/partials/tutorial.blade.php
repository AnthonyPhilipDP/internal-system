<!-- Enhanced Tutorial Dialogue Modal with Emerald Theme and Cat Narrator (Retained Position Feature) -->
<style>
  /* Custom Animations */
  @keyframes fadeInScale {
    0% {
      opacity: 0;
      transform: scale(0.9);
    }

    100% {
      opacity: 1;
      transform: scale(1);
    }
  }

  .animate-fadeInScale {
    animation: fadeInScale 0.4s ease-out forwards;
  }

  @keyframes pulseArrow {

    0%,
    100% {
      transform: translateX(0);
    }

    50% {
      transform: translateX(4px);
    }
  }

  .animate-pulseArrow {
    animation: pulseArrow 1s infinite;
  }
</style>

<div id="tutorial-modal" class="fixed inset-0 z-50 hidden">
  <!-- Transparent overlay -->
  <div class="absolute inset-0 bg-transparent"></div>

  <!-- Modal wrapper -->
  <div id="modal-content-wrapper" class="relative w-full h-full flex items-center justify-center">
    <!-- Using a minimized width -->
    <div id="tutorial-container"
      class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-4 w-11/12 md:w-1/4 transition-all duration-300 animate-fadeInScale relative border border-transparent hover:border-emerald-300">

      <!-- Header Layout: Left (Close Button), Center (Cat Image and Title), Right (Prev/Next Buttons) -->
      <div class="flex items-center justify-between mb-3">
        <!-- Left placeholder with fixed width -->
        <div class="w-24 flex justify-start">
          <button id="tutorial-close"
            class="px-3 py-1 bg-emerald-600 text-white rounded-sm hover:bg-emerald-700 transition-colors">
            Close
          </button>
        </div>

        <!-- Center: Cat Image and Title - Always centered if not repositioned -->
        <div class="flex items-center justify-center flex-grow">
          <img src="/images/default avatar.png" alt="Cat Narrator"
            class="w-12 h-12 rounded-full border border-emerald-300 mr-2">
          <h2 class="text-xl font-semibold text-emerald-600">Guide Cathy</h2>
        </div>

        <!-- Right placeholder with fixed width -->
        <div class="w-24 flex justify-end">
          <div class="flex space-x-2">
            <button id="tutorial-prev"
              class="px-3 py-1 bg-emerald-200 text-emerald-800 rounded-sm hover:bg-emerald-300 transition-colors hidden">
              Prev
            </button>
            <!-- Prevent text wrap for button -->
            <button id="tutorial-next"
              class="px-3 py-1 bg-emerald-600 text-white rounded-sm hover:bg-emerald-700 transition-colors whitespace-nowrap">
              Next
            </button>
          </div>
        </div>
      </div>

      <!-- Tutorial Content Container -->
      <div id="tutorial-content">
        <!-- Centralized text for the tutorial content -->
        <p class="text-lg font-medium text-gray-800 dark:text-gray-100 leading-snug text-center" id="tutorial-text"></p>
      </div>

      <!-- Animated Arrow Pointer for Admin Step -->
      <div id="tutorial-arrow" class="hidden absolute w-0 h-0 border-t-6 border-b-6 border-l-6 border-transparent"
        style="border-left-color: #10B981;"></div>
    </div>
  </div>
</div>

<script>
  // Define the index for the admin guide step (or any step that forces repositioning)
  const adminStepIndex = 2;

  // Array of tutorial steps (including admin step)
  const tutorialSteps = [
    "Hi there! I'm Guide Cathy The Cat, your tutorial assistant for this system. Meow!",
    "Welcome to the PMSi Internal System! This guide will walk you through the main features.",
    "Click the 'Administration' button to access the admin panel 🐱", // Admin step forces repositioning
    "Enjoy exploring the system! Click 'Got It' to close this guide. Meow!",
  ];

  // Track the current step
  let currentStep = 0;

  // Flag to note if the container is in absolute positioned mode (retaining last position)
  let isAbsolutePosition = false;

  // Get DOM elements
  const modal = document.getElementById('tutorial-modal');
  const modalWrapper = document.getElementById('modal-content-wrapper');
  const container = document.getElementById('tutorial-container');
  const tutorialText = document.getElementById('tutorial-text');
  const nextBtn = document.getElementById('tutorial-next');
  const prevBtn = document.getElementById('tutorial-prev');
  const closeBtn = document.getElementById('tutorial-close');
  const arrowEl = document.getElementById('tutorial-arrow');

  // Function to update the tutorial content and reposition only if needed
  function updateTutorial() {
    // Update the tutorial text
    tutorialText.textContent = tutorialSteps[currentStep];

    // For steps that require repositioning, recalc position; otherwise, retain last computed position
    if (currentStep === adminStepIndex) {
      // Remove centering classes from the wrapper so absolute positioning applies
      modalWrapper.classList.remove('flex', 'items-center', 'justify-center');

      const adminBtn = document.getElementById('admin-btn');
      if (adminBtn) {
        const rect = adminBtn.getBoundingClientRect();
        // Set container to absolute positioning relative to viewport
        container.classList.add('absolute');
        // Compute new position relative to the admin button
        container.style.top = (rect.top - 10) + 'px';

        const containerWidth = container.offsetWidth;
        if ((rect.left + rect.width + containerWidth + 10) > window.innerWidth) {
          container.style.left = (rect.left - containerWidth - 10) + 'px';
          // Position arrow on the right edge, pointing to the admin button
          arrowEl.style.left = containerWidth + 'px';
        } else {
          container.style.left = (rect.left + rect.width + 10) + 'px';
          // Position arrow on the left side of the container
          arrowEl.style.left = "-12px";
        }
        arrowEl.style.top = "24px";
        arrowEl.classList.remove('hidden');
        arrowEl.classList.add('animate-pulseArrow');
        // Mark that an absolute position has been set
        isAbsolutePosition = true;
      }
    } else {
      // For non-repositioning steps:
      // If container is not already absolute, ensure it is centered
      if (!isAbsolutePosition) {
        modalWrapper.classList.add('flex', 'items-center', 'justify-center');
        container.classList.remove('absolute');
        container.style.top = "";
        container.style.left = "";
      }
      // Hide the arrow pointer if visible
      arrowEl.classList.add('hidden');
      arrowEl.classList.remove('animate-pulseArrow');
    }

    // Update navigation buttons visibility and text
    if (currentStep === 0) {
      prevBtn.classList.add('hidden');
    } else {
      prevBtn.classList.remove('hidden');
    }
    nextBtn.textContent = (currentStep === tutorialSteps.length - 1) ? 'Got It' : 'Next';
  }

  // Function to open the tutorial modal
  function openTutorial() {
    modal.classList.remove('hidden');
    currentStep = 0;
    // Initially center the container if not already positioned absolutely
    if (!isAbsolutePosition) {
      modalWrapper.classList.add('flex', 'items-center', 'justify-center');
      container.classList.remove('absolute');
      container.style.top = "";
      container.style.left = "";
    }
    updateTutorial();
  }

  // Function to close the tutorial modal
  function closeTutorial() {
    modal.classList.add('hidden');
    // Optionally, you may choose to reset absolute positioning if desired on next open
    // isAbsolutePosition = false; // Uncomment to reset position on close
  }

  // Event listeners for tutorial navigation buttons
  nextBtn.addEventListener('click', function() {
    if (currentStep < tutorialSteps.length - 1) {
      currentStep++;
      updateTutorial();
    } else {
      closeTutorial();
    }
  });

  prevBtn.addEventListener('click', function() {
    if (currentStep > 0) {
      currentStep--;
      updateTutorial();
    }
  });

  closeBtn.addEventListener('click', function() {
    closeTutorial();
  });

  // Auto-open the tutorial on page load after a short delay
  window.addEventListener('load', function() {
    setTimeout(openTutorial, 500);
  });
</script>
