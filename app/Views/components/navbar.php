<!-- Custom Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm rounded">
  <div class="container-fluid">
    <!-- Logo -->
    <a class="navbar-brand fw-bold fs-3 ps-3" href="#">Blog App</a>

    <!-- Toggle button for mobile view -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navbar Links -->
    <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav ms-auto">
      <!-- Home Link -->
      <li class="nav-item">
        <a class="nav-link fs-5" id="home-link" href="homepage">Home</a>
      </li>
      <!-- Create Post Link -->
      <li class="nav-item">
        <a class="nav-link fs-5" id="create-post-link" href="create-post">Create Post</a>
      </li>
      <!-- See other posts -->
      <li class="nav-item">
        <a class="nav-link fs-5" id="view-posts-link" href="posts-lists">View Posts</a>
      </li>
      <!-- My Posts Link -->
      <li class="nav-item">
        <a class="nav-link fs-5" id="my-posts-link" href="my-posts">My Posts</a>
      </li>
      <!-- About Link -->
      <li class="nav-item">
        <a class="nav-link fs-5" id="about-link" href="#">About</a>
      </li>
      <!-- Contact Link -->
      <li class="nav-item">
        <a class="nav-link fs-5" id="contact-link" href="#">Contact</a>
      </li>
      <!-- User Account Dropdown -->
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle fs-5" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          Account
        </a>
        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
          <li><a class="dropdown-item" href="#">Profile</a></li>
          <li><a class="dropdown-item" href="settings">Settings</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item" href="logout">Log out</a></li>
        </ul>
      </li>
    </ul>
  </div>

<script>
  // Get the current URL
  const currentURL = window.location.pathname;

  // Get all nav links
  const navLinks = document.querySelectorAll('.navbar-nav .nav-link');

  // Loop through each link
  navLinks.forEach(link => {
    // Check if the link's href matches the current URL
    if (currentURL.includes(link.getAttribute('href'))) {
      // If it matches, add the 'active' class to the link
      link.classList.add('active');
    }
  });
</script>

  </div>
</nav>

<!-- Add custom styles -->
<style>
  /* Custom Navbar Styling */
  .navbar-light .navbar-nav .nav-link {
    color: #333;
    transition: color 0.3s ease;
  }

  .navbar-light .navbar-nav .nav-link:hover {
    color: #007bff; /* Add hover color */
    text-decoration: underline;
  }

  .navbar-light .navbar-nav .nav-link.active {
    color: #0056b3; /* Active link color */
    font-weight: bold;
  }

  .navbar-light .navbar-toggler-icon {
    background-color: #007bff;
  }

  .dropdown-menu {
    border-radius: 0.375rem; /* Smooth corners for dropdown */
  }

  .dropdown-item:hover {
    background-color: #f1f1f1; /* Hover effect for dropdown items */
  }

  .navbar-light .navbar-brand {
    color: #007bff;
    font-size: 1.75rem;
  }

  .navbar-light .navbar-brand:hover {
    color: #0056b3;
  }

</style>
