<!-- Custom Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm rounded">
  <div class="container-fluid">
    <!-- Logo -->
    <a 
    class="navbar-brand fw-bold fs-3 d-flex align-items-center" 
    href="homepage"
    style="font-family: 'Poppins', sans-serif; color: #2C3E50;"
    >
    <span 
        class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center me-3" 
        style="width: 50px; height: 50px; font-size: 1.5rem;"
    >
        WS
    </span>
    <span>
        Write<span class="text-primary">Sphere</span>
    </span>
    </a>

  <!-- Search Form -->
  <div class="nav-item d-flex align-items-center w-100">
    <form action="search-users" method="GET" class="d-flex w-100">
      <div class="input-group w-100">
        <input 
          type="text" 
          name="query" 
          id="search-query" 
          class="form-control border-0 rounded-pill shadow-sm px-3" 
          placeholder="Search Authors..." 
          aria-label="Search Authors"
          style="transition: all 0.3s ease; background-color: #f8f9fa;"
          onfocus="this.style.backgroundColor='#ffffff'; this.style.boxShadow='0 0 10px rgba(0, 123, 255, 0.5)';" 
          onblur="this.style.backgroundColor='#f8f9fa'; this.style.boxShadow='none';"
        >
        <button 
          type="submit" 
          class="btn btn-primary rounded-pill ms-2 px-4 py-2 shadow-sm" 
          style="transition: all 0.3s ease; background-color: #007bff; border: none; font-size: 14px;"
          onmouseover="this.style.backgroundColor='#0056b3';" 
          onmouseout="this.style.backgroundColor='#007bff';"
        >
          <i class="fas fa-search"></i> Search
        </button>
      </div>
    </form>
  </div>


    <!-- Toggle button for mobile view -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navbar Links -->
    <div class="collapse navbar-collapse col-4" id="navbarNav">
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

        <!-- User Account Dropdown -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle fs-5" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Account
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="view-profile?id=<?=$_SESSION['id']?>">Profile</a></li>
            <li><a class="dropdown-item" href="my-posts">Your Posts</a></li>
            <li><a class="dropdown-item" href="settings">Settings</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="logout">Log out</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>


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
