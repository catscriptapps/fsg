<?php
// /resources/views/components/scroll-top.php
// This component adds a "Scroll to Top" button that appears when the user scrolls down the page.
?>

<button
  id="scroll-top"
  aria-label="Scroll to top"
  style="
  position: fixed;
  /* Bottom-right corner. While it shows, the live-chat bubble (also 44px,
     h-11, components/chat-widget.php) lifts to sit directly above it
     (app.css) — same size, same right edge (right-6 = 24px). */
  bottom: 24px;
  right: 24px;
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0.75rem;
  border-radius: 9999px;
  box-shadow: 0 10px 24px -8px rgba(93,67,39,0.55);
  background-color: #ca9c6a; /* brand gold */
  color: #0b0a08;
  opacity: 0;
  pointer-events: none;
  transition: background-color 0.3s ease, transform 0.3s ease;
 "
  onmouseover="this.style.backgroundColor='#b2804f'; this.style.transform='scale(1.1)'"
  onmouseout="this.style.backgroundColor='#ca9c6a'; this.style.transform='scale(1)'">
  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
    <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
  </svg>
</button>